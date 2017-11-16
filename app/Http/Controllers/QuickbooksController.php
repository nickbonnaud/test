<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Profile;
use App\User;
use App\Account;
use App\Transaction;
use Carbon\Carbon;
use DateTimeZone;
use Crypt;

class QuickbooksController extends Controller
{

  private $IntuitAnywhere;
  private $context;
  private $realm;

  public function __construct(){
    if (!\QuickBooks_Utilities::initialized(env('QBO_DSN'))) {
      // Initialize creates the neccessary database schema for queueing up requests and logging
      \QuickBooks_Utilities::initialize(env('QBO_DSN'));
    }
    $this->IntuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));

  }
    
  public function  qboConnect(){
  	$the_tenant = auth()->user()->profile->id;

    if ($this->IntuitAnywhere->check(env('QBO_USERNAME'), $the_tenant) && $this->IntuitAnywhere->test(env('QBO_USERNAME'), $the_tenant)) {
      // Set up the IPP instance
      $IPP = new \QuickBooks_IPP(env('QBO_DSN'));
      // Get our OAuth credentials from the database
      $creds = $this->IntuitAnywhere->load(env('QBO_USERNAME'), $the_tenant);
      // Tell the framework to load some data from the OAuth store
      $IPP->authMode(
        \QuickBooks_IPP::AUTHMODE_OAUTH,
        env('QBO_USERNAME'),
        $creds);

      if (env('QBO_SANDBOX')) {
        // Turn on sandbox mode/URLs
        $IPP->sandbox(true);
      }
      // This is our current realm
      $this->realm = $creds['qb_realm'];
      // Load the OAuth information from the database
      $this->context = $IPP->context();
      return true;
    } else {
      return false;
    }
  }

  public function redirectOauth() {
  	$the_tenant = auth()->user()->profile->id;
    if ($this->IntuitAnywhere->handle(env('QBO_USERNAME'), $the_tenant))
    {
      ; // The user has been connected, and will be redirected to QBO_SUCCESS_URL automatically.
    }
    else
    {
      // If this happens, something went wrong with the OAuth handshake
      die('Oh no, something bad happened: ' . $this->IntuitAnywhere->errorNumber() . ': ' . $this->IntuitAnywhere->errorMessage());
    }
  }

  public function qboDisconnect() {
    $profile = auth()->user()->profile;
  	$the_tenant = $profile->id;
    $account = $profile->account;
    $this->IntuitAnywhere->disconnect(env('QBO_USERNAME'), $the_tenant, true);
    $profile->connected_qb = false;
    $profile->save();
    return redirect()->route('accounts.edit', ['accounts' => Crypt::encrypt($account->id)]);
  }

  public function qboDisconnectPublic() {
    return view('app.qbDisconnect');
  }

  public function qboLearnMore() {
    return view('app.learnMore');
  }

  public function qboTax() {
    return view('qbo.tax');
  }

  public function setTaxRate() {
    $this->qboConnect();
    
    $taxRateService = new \QuickBooks_IPP_Service_TaxRate();
    $taxRates = $taxRateService->query($this->context, $this->realm, "SELECT * FROM TaxRate");
    $TaxCodeService = new \QuickBooks_IPP_Service_TaxCode();
    $taxCodes = $TaxCodeService->query($this->context, $this->realm, "SELECT * FROM TaxCode");
    if (count($taxCodes) == 0 || count($taxRates) == 0) {
      flash()->overlay('Tax Rate Not Set', 'Your Tax Rate in QuickBooks is not set!', 'error');
      return redirect()->back();
    }
    $businessTaxRate = round(auth()->user()->profile->tax->total / 100, 2);
    foreach ($taxCodes as $taxCode) {
      $taxRateList = $taxCode->getSalesTaxRateList();
      if ($taxRateList !== null) {
        $qbTaxRate = 0;

        $taxRateDetailLine = $taxRateList->countTaxRateDetail();
        for ($i = 0; $i < $taxRateDetailLine; $i++) {
          $taxRateDetail = $taxRateList->getTaxRateDetail($i);
          $taxRateRef = $taxRateDetail->getTaxRateRef();

          foreach ($taxRates as $taxRate) {
            $taxId = $taxRate->getId();
            if ($taxId == $taxRateRef) {
              $componentRate = floatval($taxRate->getRateValue());
              $qbTaxRate = $qbTaxRate + $componentRate;
            }
          }
        }
        if ($qbTaxRate == $businessTaxRate) {
          $taxCodeId = $taxCode->getId();
          $taxCodeId = str_replace('{','',$taxCodeId);
          $taxCodeId = str_replace('}','',$taxCodeId);
          $taxCodeId = abs($taxCodeId);
          $this->setPockeytTaxCode($taxCodeId);
          flash()->success('Success', 'Pockeyt Sync now active!');
          return redirect()->back();
        }
      }
    }
    if (!auth()->user()->profile->account->pockeyt_qb_taxcode) {
      flash()->overlay('Sales Tax Rates do not match', 'Your Sales Tax Rate in Pockeyt is ' . $businessTaxRate . '%. Pockeyt cannot sync with QuickBooks if your Sales Tax in Pockeyt and QuickBooks do not match', 'error');
          return redirect()->back();
    }
  }
}

