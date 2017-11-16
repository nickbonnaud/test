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
      'accountUserFirst' => 'required',
      'accountUserLast' => 'required',
      'dateOfBirth' => 'required|date_format:Y-m-d',
      'ownership' => 'required',
      'indivStreetAddress' => 'required',
      'indivCity' => 'required',
      'indivState' => 'required',
      'indivZip' => 'required',
      'ownerEmail' => 'required',
      'ssn' => 'required',
    ];
  }

  public function validateBusiness() {
    return [
      'legalBizName' => 'required',
      'businessType' => 'required',
      'bizTaxId' => 'required',
      'established' => 'required|date_format:Y-m-d',
      'annualCCSales' => 'required',
      'bizStreetAddress' => 'required',
      'bizCity' => 'required',
      'bizState' => 'required',
      'bizZip' => 'required',
      'phone' => 'required',
      'accountEmail' => 'required'
    ];
  }

  public function validatePay() {
    return [
      'routing' => 'required',
      'accountNumber' => 'required',
      'method' => 'required'
    ];
  }
}
