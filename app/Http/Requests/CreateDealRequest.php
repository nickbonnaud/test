<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDealRequest extends FormRequest
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
      'message' => 'required',
      'deal_item' => 'required',
      'photo' => 'required|mimes:jpg,jpeg,png,bmp',
      'price' => 'required',
      'end_date' => 'required|date_format:Y-m-d'
    ];
  }
}
