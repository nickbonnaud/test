<?php

namespace App;

use App\Notifications\TransactionBillWasClosed;
use App\Events\TransactionError;
use App\Events\TransactionsChange;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	protected static function boot() {
    static::created(function ($transaction) {
      if (($transaction->bill_closed == true) && !$transaction->is_refund) {
        $transaction->user->notify(new TransactionBillWasClosed($transaction));
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
      $this->user->notify(new TransactionBillWasClosed($this));
    }
    $this->attributes['bill_closed'] = $billClosed;
  }

  public function transactionErrorEvent() {
    event(new TransactionError($this->user, $this, $this->profile->slug));
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
    $this->save();
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
    $this->save();
  }

  public function calculateTransactionDetails($post) {
    $this->net_sales = $post->price;
    $this->tax = round(($this->profile->tax->total / 10000) * $post->price);
    $this->total = $post->price + $this->tax;
    $this->deal_id = $post->id;
    $this->products = json_encode(['name' => $post->deal_item, 'price' => $post->price, 'quantity' => 1]);
  }

  public function scopeFilter($query, $filters, $profile) {
    return $filters->apply($query)->where('profile_id', '=', $profile->id);
  }

  public function scopeApiFilter($query, $filters, $user) {
    return $filters->apply($query)->where('user_id', '=', $user->id);
  }
}
