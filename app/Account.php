<?php

namespace App;

use Crypt;
use SplashPayments;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

  protected $fillable = ['legalBizName', 'businessType', 'bizTaxId', 'established', 'annualCCSales', 'bizStreetAddress', 'bizCity', 'bizZip', 'bizState', 'phone', 'accountEmail', 'accountUserFirst', 'accountUserLast', 'dateOfBirth', 'ownership', 'indivStreetAddress', 'indivCity', 'indivZip', 'indivState', 'ownerEmail', 'ssn', 'method', 'accountNumber', 'routing', 'status'];


  public function getRouteKeyName() {
    return 'slug';
  }

  public function profile() {
    return $this->belongsTo(Profile::class);
 	}

 	public function setSsnAttribute($ssn) {
 		$this->attributes['ssn'] = Crypt::encrypt($ssn);
 	}

 	public function getSsnAttribute($ssn) {
    if ($ssn) {
      return substr(Crypt::decrypt($ssn), -4);
    }
 	}

 	public function setAccountNumberAttribute($accountNumber) {
 		$this->attributes['accountNumber'] = Crypt::encrypt($accountNumber);
 	}

 	public function getAccountNumberAttribute($accountNumber) {
    if ($accountNumber) {
      return substr(Crypt::decrypt($accountNumber), -4);
    }
 	}

 	public function setRoutingAttribute($routing) {
 		$this->attributes['routing'] = Crypt::encrypt($routing);
 	}

 	public function getRoutingAttribute($routing) {
 		if ($routing) {
      return substr(Crypt::decrypt($routing), -4);
    }
 	}

 	public function setAnnualCCSalesAttribute($sales) {
 		$this->attributes['annualCCSales'] = round(preg_replace("/[^0-9\.]/","",$sales));
 	}

 	public function setOwnershipAttribute($ownership) {
 		$this->attributes['ownership'] = $ownership * 100;
 	}

 	public function getOwnershipAttribute($ownership) {
    if ($ownership) {
      return round($ownership / 100);
    }
 	}

 	public function updateAccount($account) {
 		$account = $this->checkForMaskedData($account);
 		$account['status'] = "review";
    return $this->update($account);
 	}

 	private function checkForMaskedData($account) {
 		foreach ($account as $key => $attribute) {
 			if (starts_with($attribute, 'XX') || starts_with($attribute, 'xx')) {
 				unset($account[$key]);
 			}
 		}
 		unset($account['type']);
 		return $account;
 	}

 	public function setLegalBizNameAttribute($businessName) {
    if ($this->legalBizName != $businessName) {
      $slug = str_slug($businessName, '-');
      $count = Account::raw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
      $this->attributes['slug'] = $count ? "{$slug}-{$count}" : $slug;
    }
    $this->attributes['legalBizName'] = $businessName;
  }

  public function route() {
  	if (!$this->ownerEmail || !$this->method) {
  		return "/accounts/{$this->slug}/edit";
  	} else {
  		return "/accounts/{$this->slug}";
  	}
  }

  public function getAccountFormStage() {
  	if (!$this->ownerEmail) {
  		return "owner";
  	} else {
  		return "bank";
  	}
  }

  public function methodName() {
    switch ($this->method) {
      case 8:
        return "Checking Account";
        break;
      case 9:
        return "Savings Account";
        break;
      case 10:
        return "Corporate Checking Account";
        break;
      case 11:
        return "Corporate Savings Account";
        break;
    }
  }

  public function businessTypeName() {
    switch ($this->businessType) {
      case 0:
        return "Sole Proprietor";
        break;
      case 1:
        return "Corporation";
        break;
      case 2:
        return "LLC";
        break;
      case 3:
        return "Partnership";
        break;
      case 4:
        return "Association";
        break;
    }
  }

  public function squareLocationSet() {
    if ($this->square_location_id) {
      return true;
    } else {
      return false;
    }
  }

  public function sendAccountDataToProcessor($formattedAccountData) {
    SplashPayments\Utilities\Config::setTestMode(true);
    SplashPayments\Utilities\Config::setApiKey(env('SPLASH_KEY'));
    $object = new SplashPayments\merchants($formattedAccountData);

    try {
      $object->create();
    } catch (SplashPayments\Exceptions\Base $e) {
      dd($e->getResponse());
    }
    $response = $object->getResponse();
    $this->splashId = $response[0]->id;
    $this->save();
  }
}
