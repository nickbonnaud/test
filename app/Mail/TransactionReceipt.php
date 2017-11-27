<?php

namespace App\Mail;

use App\Profile;
use App\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransactionReceipt extends Mailable
{
  use Queueable, SerializesModels;

  public $profile;
  public $transaction;
  public $items;

  public function __construct(Profile $profile, Transaction $transaction)
  {
    $this->profile = $profile;
    $this->transaction = $transaction;
    $this->items = json_decode($transaction->products);
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->from('receipts@pockeyt.com')
      ->subject('Recent transaction from Pockeyt')
      ->view('emails.transactions.receipt');
  }
}
