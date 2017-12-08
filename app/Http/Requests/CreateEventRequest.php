<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
      'title' => 'required',
      'body' => 'required',
      'photo' => 'required|mimes:jpg,jpeg,png,bmp',
      'event_date' => 'required|date_format:Y-m-d',
      'event_time' => 'required'
    ];
  }
}
