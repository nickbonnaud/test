<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
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
}
