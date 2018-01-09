<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'first_name' => $this->first_name,
      'last_name' => $this->last_name,
      'email' => $this->email,
      'card_type' => $this->card_type,
      'last_four_card' => $this->last_four_card,
      'photo_url' => $this->photo_path,
      'default_tip' => $this->default_tip_rate,
      'token' => $this->token
    ];
  }
}
