<?php

namespace App\Mail;

use App\Profile;
use App\Transaction;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransactionErrorEmail extends Mailable
{
  use Queueable, SerializesModels;

  public $transaction;
  protected $error;


  public function __construct(Transaction $transaction, $error)
  {
    $this->transaction = $transaction;
    $this->error = $error;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->from('error@pockeyt.com')
      ->subject('Pockeyt Transaction Error')
      ->view('emails.transactions.error')
      ->with(['error' => $this->error]);
  }
}
