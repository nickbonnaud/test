<?php

namespace App\Listeners;

use Crypt;
use App\Events\AccountReadyForProcessorReview;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendAccountDataToProcessor
{
  
  public $account; 

  public function __construct()
  {
      //
  }

  /**
   * Handle the event.
   *
   * @param  AccountReadyForProcessorReview  $event
   * @return void
   */
  public function handle(AccountReadyForProcessorReview $event)
  {
    $this->account = $event->account;

    $formattedAccountData = $this->formatAccountData();
    $this->account->sendAccountDataToProcessor($formattedAccountData);
  }

  public function formatAccountData() {
    $data = array (
      'new' => 0,
      'established' => date_format(date_create($this->account->established), 'Ymd'),
      'annualCCSales' => $this->account->annualCCSales * 100,
      'status' => 1,
      'tcVersion' => 1,
      'entity' => array(
        'type' => $this->account->businessType,
        'name' => $this->account->legalBizName,
        'address1' => $this->account->bizStreetAddress,
        'city' => $this->account->bizCity,
        'state' => $this->account->bizState,
        'zip' => $this->account->bizZip,
        'country' => "USA",
        'phone' => preg_replace("/[^0-9]/","", $this->account->phone),
        'email' => $this->account->accountEmail,
        'ein' => preg_replace("/[^0-9]/","", $this->account->bizTaxId),
        'website' => $this->account->profile->website,
        
      ),
      'members' => array(
        array(
          'first' => $this->account->accountUserFirst,
          'last' => $this->account->accountUserLast,
          'dob' => date_format(date_create($this->account->dateOfBirth), 'Ymd'),
          'ownership' => $this->account->ownership,
          'email' => $this->account->ownerEmail,
          'ssn' => preg_replace("/[^0-9]/","", Crypt::decrypt($this->account->getOriginal('ssn'))),
          'primary' => 1
        )
      )
    );
    return $data;
  }
}
