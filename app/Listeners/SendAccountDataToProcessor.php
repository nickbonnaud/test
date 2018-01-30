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
      'annualCCSales' => $this->account->annual_cc_sales * 100,
      'mcc' => '8111',
      'status' => 1,
      'entity' => array(
        'type' => $this->account->business_type,
        'name' => $this->account->legal_biz_name,
        'address1' => $this->account->biz_street_address,
        'city' => $this->account->biz_city,
        'state' => $this->account->biz_state,
        'zip' => $this->account->biz_zip,
        'country' => "USA",
        'phone' => preg_replace("/[^0-9]/","", $this->account->phone),
        'email' => $this->account->account_email,
        'ein' => preg_replace("/[^0-9]/","", $this->account->biz_tax_id),
        'website' => $this->account->profile->website,
        'accounts' => array(
          array(
            'primary' => 1,
            'account' => array(
              'method' => $this->account->method,
              'number' => Crypt::decrypt($this->account->getOriginal('account_number')),
              'routing' => Crypt::decrypt($this->account->getOriginal('routing'))
            )
          )
        )
      ),
      'members' => array(
        array(
          'first' => $this->account->account_user_first,
          'last' => $this->account->account_user_last,
          'dob' => date_format(date_create($this->account->date_of_birth), 'Ymd'),
          'ownership' => $this->account->ownership,
          'email' => $this->account->owner_email,
          'ssn' => preg_replace("/[^0-9]/","", Crypt::decrypt($this->account->getOriginal('ssn'))),
          'address1' => $this->account->indiv_street_address,
          'city' => $this->account->indiv_city,
          'state' => $this->account->indiv_state,
          'zip' => $this->account->indiv_zip,
          'country' => "USA",
          'primary' => 1
        )
      )
    );
    return $data;
  }
}
