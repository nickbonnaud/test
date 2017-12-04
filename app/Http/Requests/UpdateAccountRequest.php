<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize() {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules() {
    $type = $this->request->get('type');
    switch ($type) {
      case 'owner':
        return $this->validateOwner();
        break;
      case 'business':
        return $this->validateBusiness();
        break;
      case 'bank':
        return $this->validatePay();
        break;
      default:
        return [
        'type' => 'required|in:owner,business,bank'
      ];
    }
  }

  public function validateOwner() {
    return [
      'account_user_first' => 'required',
      'account_user_last' => 'required',
      'date_of_birth' => 'required|date_format:Y-m-d',
      'ownership' => 'required',
      'indiv_street_address' => 'required',
      'indiv_city' => 'required',
      'indiv_state' => 'required',
      'indiv_zip' => 'required',
      'owner_email' => 'required',
      'ssn' => 'required',
    ];
  }

  public function validateBusiness() {
    return [
      'legal_biz_name' => 'required',
      'business_type' => 'required',
      'biz_tax_id' => 'required',
      'established' => 'required|date_format:Y-m-d',
      'annual_cc_sales' => 'required',
      'biz_street_address' => 'required',
      'biz_city' => 'required',
      'biz_state' => 'required',
      'biz_zip' => 'required',
      'phone' => 'required',
      'account_email' => 'required'
    ];
  }

  public function validatePay() {
    return [
      'routing' => 'required',
      'account_number' => 'required',
      'method' => 'required'
    ];
  }
}
