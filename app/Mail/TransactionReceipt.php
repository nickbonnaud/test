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

  public $transaction;

  public function __construct(Transaction $transaction)
  {
    $transaction->products = json_decode($transaction->products);
    $this->transaction = $transaction;
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
