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

  public $profile;
  public $user;
  public $transaction;
  public $msg;
  public $code;
  public $transactionSplashId;

  public function __construct(Profile $profile, User $user, Transaction $transaction, $msg, $code, $transactionSplashId)
  {
    $this->profile = $profile;
    $this->user = $user;
    $this->transaction = $transaction;
    $this->msg = $msg;
    $this->code = $code;
    $this->transactionSplashId = $transactionSplashId;
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
      ->view('emails.transactions.error');
  }
}
