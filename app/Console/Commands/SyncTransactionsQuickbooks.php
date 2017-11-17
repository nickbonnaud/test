<?php

namespace App\Console\Commands;

use App\Profile;
use App\Transaction;
use Illuminate\Console\Command;

class SyncTransactionsQuickbooks extends Command
{

  private static $intuitAnywhere;
  private static $context;
  private static $realm;
  private static $invoiceService;
  private static $paymentService;


  protected $signature = 'app:sync-transactions-quickbooks';
  protected $description = 'Sync purchases with QuickBooks';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    self::init();
    $profiles = self::getProfiles();
    foreach ($profiles as $profile) {
      if (self::$intuitAnywhere->check(env('QBO_USERNAME'), $profile->id) && self::$intuitAnywhere->test(env('QBO_USERNAME'), $profile->id)) {
        self::setCredentials($profile);
        $unSyncedTransactions = self::getUnSyncedTransactions($profile);
        foreach ($unSyncedTransactions as $transaction) {
          $invoice = $transaction->syncInvoiceWithQuickbooks();
          if($response = self::sendInvoice($invoice)) {
            $payment = $transaction->syncPaymentDetailsWithQuickbooks($response);
            if ($resp = self::sendPayment($payment)) {
              $transaction->qb_synced = true;
              $transaction->save();
            } else {
              $transaction->qb_synced = false;
              $transaction->save();
              dd(self::$paymentService->lastError());
            }
          } else {
            $transaction->qb_synced = false;
            $transaction->save();
            dd(self::$invoiceService->lastError());
          }
        }
      }
    }
  }

  public static function getProfiles() {
    return Profile::where('connected_qb', '=', true)->whereHas('account', function($query) {
      $query->whereNotNull('pockeyt_qb_taxcode');
    })->get();
  }

  public static function getUnSyncedTransactions($profile) {
    return Transaction::where(function($query) use ($profile) {
      $query->where('qb_synced', '=', false)
            ->where('profile_id', '=', $profile->id)
            ->where('created_at', '>', $profile->account->qb_connected_date);
    })->get();
  }

  public static function setCredentials($profile) {
    $IPP = new \QuickBooks_IPP(env('QBO_DSN'));
    $creds = self::$intuitAnywhere->load(env('QBO_USERNAME'), $profile->id);
    $IPP->authMode(\QuickBooks_IPP::AUTHMODE_OAUTH, env('QBO_USERNAME'), $creds);

    if (env('QBO_SANDBOX')) {
      $IPP->sandbox(true);
    }
    self::$realm = $creds['qb_realm'];
    self::$context = $IPP->context();
  }

  public static function sendInvoice($invoice) {
    self::$invoiceService = new \QuickBooks_IPP_Service_Invoice();
    return $invoiceService->add(self::$context, self::$realm, $invoice);
  }

  public static function sendPayment($payment) {
    self::$paymentService = new \QuickBooks_IPP_Service_Payment();
    return $paymentService->add(self::$context, self::$realm, $payment);
  }

  public static function init() {
    if (!\QuickBooks_Utilities::initialized(env('QBO_DSN'))) {
      \QuickBooks_Utilities::initialize(env('QBO_DSN'));
    }
    static::$intuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));
  }
}
