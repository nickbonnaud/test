<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class PayProfileResource extends Resource
{

  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'slug' => $this->slug,
      'business_name' => $this->business_name,
      'logo' =>  $this->logo->apiUrl,
      'connected_pos' => $this->connectedPos() ? ($this->connectedPos()->first())->account_type : null,
      'token' => [
      	'value' => $this->token ? $this->token : null,
      	'expiry' => $this->token ? Carbon::now()->addMinutes(env('JWT_TTL'))->timestamp : null
      ]
    ];
  }
}
