<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLoyaltyProgramRequest extends FormRequest
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
  public function rules() {
    $type = $this->request->get('optionsRadios');
    switch ($type) {
      case 'increment':
        return $this->validateIncrement();
        break;
      case 'amount':
        return $this->validateAmount();
        break;
      default:
        return [
        'optionsRadios' => 'required',
        'reward' => 'required'
      ];
    }
  }

  protected function validateIncrement() {
    return [
      'purchases_required' => 'required',
      'optionsRadios' => 'required',
      'reward' => 'required'
    ];
  }

  protected function validateAmount() {
    return [
      'amount_required' => 'required',
      'optionsRadios' => 'required',
      'reward' => 'required'
    ];
  }
}
