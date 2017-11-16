<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use DateTimeZone;

class QuickbookAccount extends Model
{

	public static $context;
	public static $realm;
	public static $profile;

	public static function getData($profile) {
		self::init($profile);

		self::setQboId();
		self::createAccount();
		self::createTipsAccount();
		self::createItem();
		self::createTipsItem();
		self::createPaymentMethod();
		self::setQbActive();

		return self::createTaxAccount();
	}

	public static function init($profile) {
		$the_tenant = $profile->id;
		$intuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));
		$creds = $intuitAnywhere->load(env('QBO_USERNAME'), $the_tenant);
		$IPP = new \QuickBooks_IPP(env('QBO_DSN'));

		$IPP->authMode(
      \QuickBooks_IPP::AUTHMODE_OAUTH,
      env('QBO_USERNAME'),
      $creds
    );

    if (env('QBO_SANDBOX')) {
      // Turn on sandbox mode/URLs
      $IPP->sandbox(true);
    }
		static::$context = $IPP->context();
		static::$realm = $creds['qb_realm'];
		static::$profile = $profile;
	}

	public static function setQboId() {
		$quickBookCustomerId = self::$profile->account->pockeyt_qb_id;
		$customerService = new \QuickBooks_IPP_Service_Customer();

		if ($quickBookCustomerId) {
			$retrievedQuickBookCustomerId = $customerService->query(self::$context, self::$realm, "SELECT * FROM Customer WHERE Id = '{$quickBookCustomerId}'");
			if (count($retrievedQuickBookCustomerId) != 0) return;
		}
		$customer = new \QuickBooks_IPP_Object_Customer();
		$customer->setDisplayName('Pockeyt Customer');
  	$customer->setNotes('Created to track Pockeyt sales');
  	if ($resp = $customerService->add(self::$context, self::$realm, $customer)) {
      $resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return self::setPockeytQbId($resp);
  	} else {
  		dd($customerService->lastError(self::$context));
  	}
	}

	public static function createAccount() {
    $accountId = self::$profile->account->pockeyt_qb_account;
  	$accountService = new \QuickBooks_IPP_Service_Account();
    if ($accountId) {
      $qbAccountId = $accountService->query(self::$context, self::$realm, "SELECT * FROM Account WHERE Id = '{$accountId}'");
      if (count($qbAccountId) != 0) { return; }
    }

  	$account = new \QuickBooks_IPP_Object_Account();

  	$account->setName('Pockeyt Income');
  	$account->setDescription('Pockeyt Sales');
  	$account->setCashFlowClassification('Revenue');
  	$account->setAccountType('Income');
  	$account->setAccountSubType('SalesOfProductIncome');
  	
  	if ($resp = $accountService->add(self::$context, self::$realm, $account))
		{
			$resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
			return self::setPockeytQbAccount($resp);
		}
		else
		{
			dd($accountService->lastError(self::$context));
		}
	}

	public static function createTipsAccount() {
		$accountId = self::$profile->account->pockeyt_qb_tips_account;
    $accountService = new \QuickBooks_IPP_Service_Account();
     if ($accountId) {
      $qbAccountId = $accountService->query(self::$context, self::$realm, "SELECT * FROM Account WHERE Id = '{$accountId}'");
      if (count($qbAccountId) != 0) { return; }
    }
    $account = new \QuickBooks_IPP_Object_Account();

    $account->setName('Pockeyt Tips');
    $account->setCashFlowClassification('Liability');
    $account->setAccountType('OtherCurrentLiabilities');
    $account->setAccountSubType('OtherCurrentLiabilities');
    
    if ($resp = $accountService->add(self::$context, self::$realm, $account))
    {
      $resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return self::setPockeytQbTipsAccount($resp);
    }
    else
    {
      dd($accountService->lastError(self::$context));
    }
	}

	public static function createItem() {
		$itemId = self::$profile->account->pockeyt_item;
  	$itemService = new \QuickBooks_IPP_Service_Item();
    if ($itemId) {
      $qbItemId = $itemService->query(self::$context, self::$realm, "SELECT * FROM Item WHERE Id = '{$itemId}'");
      if (count($qbItemId) != 0) { return; }
    }
  	$item = new \QuickBooks_IPP_Object_Item();

  	$item->setName('Pockeyt Item');
		$item->setType('Service');
		$item->setIncomeAccountRef(self::$profile->account->pockeyt_qb_account);
		if ($resp = $itemService->add(self::$context, self::$realm, $item))
		{
			$resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return self::setPockeytItem($resp);
		}
		else
		{
			dd($itemService->lastError(self::$context));
		}
	}

	public static function createTipsItem() {
		$itemId = self::$profile->account->pockeyt_tips_item;
    $itemService = new \QuickBooks_IPP_Service_Item();
    if ($itemId) {
      $qbItemId = $itemService->query(self::$context, self::$realm, "SELECT * FROM Item WHERE Id = '{$itemId}'");
      if (count($qbItemId) != 0) { return; }
    }
    $itemService = new \QuickBooks_IPP_Service_Item();
    $item = new \QuickBooks_IPP_Object_Item();

    $item->setName('Pockeyt Tips');
    $item->setType('Service');
    $item->setIncomeAccountRef(self::$profile->account->pockeyt_qb_tips_account);
    if ($resp = $itemService->add(self::$context, self::$realm, $item))
    {
      $resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return self::setPockeytTipsItem($resp);
    }
    else
    {
      dd($itemService->lastError(self::$context));
    }
	}

	public static function createPaymentMethod() {
		$methodId = self::$profile->account->pockeyt_payment_method;
  	$paymentMethodService = new \QuickBooks_IPP_Service_PaymentMethod();
    if ($methodId) {
      $qbMethodId = $paymentMethodService->query(self::$context, self::$realm, "SELECT * FROM PaymentMethod WHERE Id = '{$methodId}'");
      if (count($qbMethodId) != 0) { return; }
    }
  	$paymentMethod = new \QuickBooks_IPP_Object_PaymentMethod();

  	$paymentMethod->setName('Pockeyt Payment');
  	if ($resp = $paymentMethodService->add(self::$context, self::$realm, $paymentMethod))
  	{
  		$resp = str_replace('{','',$resp);
      $resp = str_replace('}','',$resp);
      $resp = abs($resp);
      return self::setPockeytPaymentMethod($resp);
  	}
  	else
  	{
  		dd($paymentMethodService->lastError(self::$context));
  	}
	}

	public static function createTaxAccount() {
		$taxRateService = new \QuickBooks_IPP_Service_TaxRate();
    $taxRates = $taxRateService->query(self::$context, self::$realm, "SELECT * FROM TaxRate");
    $TaxCodeService = new \QuickBooks_IPP_Service_TaxCode();
    $taxCodes = $TaxCodeService->query(self::$context, self::$realm, "SELECT * FROM TaxCode");
    if (count($taxCodes) == 0 || count($taxRates) == 0) {
      return $qbTaxRate = 'not set';
    }
    $businessTaxRate = round(self::$profile->tax->total / 100, 2);
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
          return self::setPockeytTaxCode($taxCodeId);
        } 
      }
    }
    if (!self::$profile->account->pockeyt_qb_taxcode) {
      return $qbTaxRate = 'not matching';
    }
	}

	public static function setQbActive() {
		self::$profile->connected_qb = true;
		self::$profile->save();

		$account = self::$profile->account;
		$account->qb_connected_date = Carbon::now(new DateTimeZone(config('app.timezone')));
		return $account->save();
	}

	public static function setPockeytQbId($resp) {
		$account = self::$profile->account;
		$account->pockeyt_qb_id = $resp;
		return $account->save();
	}

	public static function setPockeytQbAccount($resp) {
		$account = self::$profile->account;
    $account->pockeyt_qb_account = $resp;
    return $account->save();
	}

	public static function setPockeytQbTipsAccount($resp) {
		$account = self::$profile->account;
    $account->pockeyt_qb_tips_account = $resp;
    return $account->save();
	}

	public static function setPockeytItem($resp) {
		$account = self::$profile->account;
    $account->pockeyt_item = $resp;
    return $account->save();
	}

	public static function setPockeytTipsItem($resp) {
		$account = self::$profile->account;
    $account->pockeyt_tips_item = $resp;
    return $account->save();
	}

	public static function setPockeytPaymentMethod($resp) {
		$account = self::$profile->account;
  	$account->pockeyt_payment_method = $resp;
  	return $account->save();
	}

	public static function setPockeytTaxCode($taxCodeId) {
		$account = self::$profile->account;
    $account->pockeyt_qb_taxcode = $taxCodeId;
    $account->save();
    return 'success';
	}

	public static function setTaxRate($profile) {
		self::init($profile);

		$taxRateService = new \QuickBooks_IPP_Service_TaxRate();
    $taxRates = $taxRateService->query(self::$context, self::$realm, "SELECT * FROM TaxRate");
    $TaxCodeService = new \QuickBooks_IPP_Service_TaxCode();
    $taxCodes = $TaxCodeService->query(self::$context, self::$realm, "SELECT * FROM TaxCode");
    if (count($taxCodes) == 0 || count($taxRates) == 0) {
    	return $result = 'qbo_tax_not_set';
    }
    $businessTaxRate = round($profile->tax->total / 100, 2);
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
          return $result = self::setPockeytTaxCode($taxCodeId);
        }
      }
    }
    if (!$profile->account->pockeyt_qb_taxcode) {
 			return $result = 'qbo_not_match';
    }
	}

	public static function disable($profile) {
		$intuitAnywhere = new \QuickBooks_IPP_IntuitAnywhere(env('QBO_DSN'), env('QBO_ENCRYPTION_KEY'), env('QBO_OAUTH_CONSUMER_KEY'), env('QBO_CONSUMER_SECRET'), env('QBO_OAUTH_URL'), env('QBO_SUCCESS_URL'));
    $intuitAnywhere->disconnect(env('QBO_USERNAME'), $profile->id, true);
    $profile->connected_qb = false;
    $profile->save();
    return 'disable_success';
	}
}
