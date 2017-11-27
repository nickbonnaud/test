<?php

namespace App;

use App\LoyaltyCard;
use App\PostAnalytics;
use SplashPayments;
use Carbon\Carbon;
use App\Notifications\TransactionBillWasClosed;
use App\Notifications\PayOrKeepOpenNotification;
use App\Notifications\CustomerRedeemDeal;
use App\Notifications\FixTransactionNotification;
use App\Events\TransactionError;
use App\Events\TransactionSuccess;
use App\Events\TransactionsChange;
use App\Mail\TransactionReceipt;
use App\Mail\TransactionErrorEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	protected static function boot() {
    static::created(function($transaction) {
      if (($transaction->bill_closed == true) && !$transaction->is_refund) {
        $transaction->sendBillClosedNotification();
      }
    });

    static::updated(function($transaction) {
      if ($transaction->notification_id && !$transaction->is_refund && $transaction->paid && ($transaction->status == 20)) {
        $transaction->removeNotifications();
      }
    });
  }

  protected $fillable = [
  	'profile_id',
    'user_id',
  	'paid',
  	'products',
    'tax',
    'tips',
    'net_sales',
  	'total',
    'employee_id',
    'redeemed',
    'bill_closed',
    'status'
  ];

  public function profile() {
    return $this->belongsTo('App\Profile');
  }

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function deal() {
    return $this->belongsTo('App\Post');
  }

  public function setRedeemedAttribute($redeemed) {
    $this->attributes['redeemed'] = filter_var($redeemed, FILTER_VALIDATE_BOOLEAN);
  }

  public function setBillClosedAttribute($billClosed) {
    if (($this->bill_closed == false && !is_null($this->bill_closed)) && $billClosed && !$this->is_refund) {
      $this->sendBillClosedNotification();
    }
    $this->attributes['bill_closed'] = $billClosed;
  }

  public function setStatusAttribute($status) {
    if (!$this->paid && ($status == 2)) {
      $this->markNotificationAsRead();
    }
    $this->attributes['status'] = $status;
  }

  public function removeNotifications() {
    $notifications = $this->user->notifications()
      ->where('data->data->custom->transactionId', $this->id)
      ->get();

    // Testing Remove Before Production
    if (count($notifications) == 0) {
      $notifications = $this->user->notifications()->get();
      $index = 0;
      foreach ($notifications as $notification) {
        $index = $index++;
        $test = $notification->data['data']['custom']['transactionId'];
        if ($test != $this->id) {
          array_slice($notifications, $index);
        }
      }
    }




    foreach ($notifications as $notification) {
      $notification->delete();
    }
  }

  public function markNotificationAsRead() {
    $notifications = $this->user->unreadNotifications()
      ->where('data->data->custom->transactionId', $this->id)
      ->get();


    // Testing Remove Before Production
    if (count($notifications) == 0) {
      $notifications = $this->user->notifications()->get();
      $index = 0;
      foreach ($notifications as $notification) {
        $index = $index++;
        $test = $notification->data['data']['custom']['transactionId'];
        if ($test != $this->id) {
          array_slice($notifications, $index);
        }
      }
    }




    $notifications->markAsRead();
  }

  public function transactionErrorEvent() {
    event(new TransactionError($this->user, $this, $this->profile->slug));
  }

  public function transactionSuccessEvent() {
    event(new TransactionSuccess($this->user, $this->profile->slug));
  }

  public function transactionChangeEvent() {
    event(new TransactionsChange($this, $this->profile->slug));
  }

  public function processCharge($tip = null) {
    if ($tip) {
      $this->addTipToTransaction($tip);
    } else {
      $this->addUserDefaultTip();
    }
    $success = $this->sendToProcessor();
    $this->processTransactionResults($success);
    return $success;
  }

  public function processTransactionResults($success) {
    if ($success) {
      $this->paid = true;
      $this->status = 20;
      $this->save();
      $this->updateUserTransactionData();
      $this->transactionSuccessEvent();
    } else {
      $this->paid = false;
      $this->status = 1;
      $this->save();
      $this->transactionErrorEvent();
    }
    $this->transactionChangeEvent();
  }

  public function updateUserTransactionData() {
    PostAnalytics::checkRecentlyViewed($this->user, $this->profile, $this);
    LoyaltyCard::updateWithTransactions($this->user, $this->profile, $this);
    $this->sendEmailReceipt($this->user, $this->profile);
  }

  public function addTipToTransaction($tip) {
    $this->tips = $tip;
    $this->total = $this->total + $tip;
  }

  public function addUserDefaultTip() {
    $this->tips = ($this->user->default_tip_rate / 100) * $this->total;
    $this->total = $this->total + $this->tips;
  }

  public function processDeal($dealId) {
    $post = Post::findOrFail($dealId);
    $this->calculateTransactionDetails($post);
    $success = $this->sendToProcessor();
    $this->processDealResults($success);
    return $success;
  }

  public function processDealResults($success) {
    if ($success) {
      $this->paid = true;
      $this->status = 20;
      $this->redeemed = false;
      $this->save();
    } else {
      $this->paid = false;
      $this->status = 1;
      $this->redeemed = false;
      $this->save();
    }
  }

  public function calculateTransactionDetails($post) {
    $this->net_sales = $post->price;
    $this->tax = round(($this->profile->tax->total / 10000) * $post->price);
    $this->total = $post->price + $this->tax;
    $this->deal_id = $post->id;
    $this->products = json_encode(['name' => $post->deal_item, 'price' => $post->price, 'quantity' => 1]);
    $this->save();
  }

  public function scopeFilter($query, $filters, $profile) {
    return $filters->apply($query)->where('profile_id', '=', $profile->id);
  }

  public function scopeApiFilter($query, $filters, $user) {
    return $filters->apply($query)->where('user_id', '=', $user->id);
  }

  public function syncInvoiceWithQuickbooks() {
    $invoice = $this->createQuickbooksInvoice();
    $this->addSalesLine($invoice);
    if ($this->tips) {
      $this->addTipsLine($invoice);
    }
    $this->addTaxDetail($invoice);
    $invoice->setCustomerRef($this->profile->account->pockeyt_qb_id);
    return $invoice;
  }

  public function createQuickbooksInvoice() {
    $invoice = new \QuickBooks_IPP_Object_Invoice();
    $invoice->setTxnDate($this->created_at->toDateString());
    $invoice->setDueDate($this->created_at->toDateString());
    $invoice->setPrivateNote('Pockeyt Sale Transaction ID # ' . $this->id);
    return $invoice;
  }

  public function addSalesLine($invoice) {
    $line = new \QuickBooks_IPP_Object_Line();
    $line->setDetailType('SalesItemLineDetail');
    $line->setAmount(($this->net_sales / 100));
    $line->setDescription('Total Amount');

    $salesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
    $salesItemLineDetail->setUnitPrice(($this->net_sales / 100));
    $salesItemLineDetail->setQty(1);
    $salesItemLineDetail->setItemRef($this->profile->account->pockeyt_item);
    $salesItemLineDetail->setTaxCodeRef('TAX');

    $line->addSalesItemLineDetail($salesItemLineDetail);
    $invoice->addLine($line);
    return $invoice;
  }

  public function addTipsLine($invoice) {
    $line = new \QuickBooks_IPP_Object_Line();
    $line->setDetailType('SalesItemLineDetail');
    $line->setAmount(($this->tips / 100));
    $line->setDescription('Pockeyt Tips Money');

    $salesItemLineDetail = new \QuickBooks_IPP_Object_SalesItemLineDetail();
    $salesItemLineDetail->setUnitPrice(($this->tips / 100));
    $salesItemLineDetail->setQty(1);
    $salesItemLineDetail->setItemRef($this->profile->account->pockeyt_tips_item);

    $line->addSalesItemLineDetail($salesItemLineDetail);
    $invoice->addLine($line);
    return $invoice;
  }

  public function addTaxDetail($invoice) {
    $taxDetail = new \QuickBooks_IPP_Object_TxnTaxDetail();
    $taxDetail->setTxnTaxCodeRef($this->profile->account->pockeyt_qb_taxcode);
    $taxDetail->setTotalTax($this->tax / 100);
    $invoice->addTxnTaxDetail($taxDetail);
    return $invoice;
  }

  public function syncPaymentDetailsWithQuickbooks($response) {
    $quickbooksPayment = $this->createQuickbooksPayment();
    $this->linkInvoice($response, $quickbooksPayment);
    $quickbooksPayment->setCustomerRef($this->profile->account->pockeyt_qb_id);
    return $quickbooksPayment;
  }

  public function createQuickbooksPayment() {
    $quickbooksPayment = new \QuickBooks_IPP_Object_Payment();
    $quickbooksPayment->setTotalAmt(($this->total / 100));
    $quickbooksPayment->setTxnDate($this->created_at->toDateString());
    $quickbooksPayment->setPrivateNote('Pockeyt Credit Card Payment. Pockeyt Transaction ID # ' . $this->id);
    $quickbooksPayment->setPaymentRefNum($this->id);
    $quickbooksPayment->setPaymentMethodRef($this->profile->account->pockeyt_payment_method);
    return $quickbooksPayment;
  }

  public function linkInvoice($response, $quickbooksPayment) {
    $line = new \QuickBooks_IPP_Object_Line();
    $line->setAmount(($this->total / 100));

    $linkedTxn = new \QuickBooks_IPP_Object_LinkedTxn();
    $linkedTxn->setTxnId($response);
    $linkedTxn->setTxnType('Invoice');

    $line->setLinkedTxn($linkedTxn);
    $quickbooksPayment->addLine($line);
    return $quickbooksPayment;
  }

  public function sendToProcessor() {
    SplashPayments\Utilities\Config::setTestMode(true);
    SplashPayments\Utilities\Config::setApiKey(env('SPLASH_KEY'));

    $response = new SplashPayments\txns(
      [
        'merchant' => $this->profile->account->splashId,
        'type' => 1,
        'origin' => 2,
        'token' => $this->user->customer_id,
        'first' => $this->user->first_name,
        'last' => $this->user->last_name,
        'total' => $this->total
      ]
    );
    try {
      $response->create();
    }
    catch(SplashPayments\Exceptions\Base $e) {}
    if ($response->hasErrors()) {
      $error = $response->getErrors();
      $this->sendTransactionErrorsEmail($error[0]['msg'], $error[0]['code']);
      $success = false;
    } else {
      $result = $response->getResponse();
      $success = $this->processSplashResults($result[0]);
    }
    return $success;
  }

  public function processSplashResults($processedTransaction) {
    if ($processedTransaction->status == '0' || $processedTransaction->status == '1') {
      $success =  true;
    } else {
      $msg = 'Processed and Failed';
      $code = $processedTransaction->status;
      $splashId = $processedTransaction->id;
      $this->sendTransactionErrorsEmail($msg, $code, $splashId);
      $success =  false;
    }
    $this->splash_id = $processedTransaction->id;
    return $success;
  }

  public function sendTransactionErrorsEmail($msg, $code, $splashId = null) {
    $transactionSplashId = isset($splashId) ? $splashId : 'Not Processed';
    Mail::to(env('DEFAULT_EMAIL'))->send(new TransactionErrorEmail($this->profile, $this->user, $this, $msg, $code, $transactionSplashId));
  }

  public function sendEmailReceipt($user, $profile) {
    Mail::to($user->email)->send(new TransactionReceipt($profile, $this));
  }

  public function sendRedeemRequestToCustomer() {
    $this->user->notify(new CustomerRedeemDeal($this));
  }

  public function sendBillClosedNotification() {
    $this->user->notify(new TransactionBillWasClosed($this));
  }

  public function sendFixTransactionNotification($previousNotifCount = null) {
    if (!$previousNotifCount) {
      $previousNotifCount = $this->user->notifications()->where('data->data->custom->transactionId', $this->id)->count();
    }
    $this->user->notify(new FixTransactionNotification($this, $previousNotifCount));
  }

  public function sendPayOrKeepOpenNotification() {
    $this->user->notify(new PayOrKeepOpenNotification($this));
  }

  public function checkRecentSentNotification($classType = null) {
    if ($classType) {
      $type = "App\\Notifications\\" . $classType;
      return $this->user->notifications()->where('type', $type)
        ->where('data->data->custom->transactionId', $this->id)
        ->where('created_at', '>=', Carbon::now()->subMinutes(5))->count();
    } else {
      return $this->user->notifications()
        ->where('data->data->custom->transactionId', $this->id)
        ->where('created_at', '>=', Carbon::now()->subMinutes(5))->count();
    }
  }
}
