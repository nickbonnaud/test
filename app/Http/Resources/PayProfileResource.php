<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class PayProfileResource extends Resource
{

  public function toArray($request)
  {
    $connectedPos = $this->connectedPos()->first();
    return [
      'id' => $this->id,
      'slug' => $this->slug,
      'business_name' => $this->business_name,
      'logo' =>  $this->logo->apiUrl,
      'connected_pos' => isset($connectedPos) && $connectedPos->account_type == 'clover' && !is_null($connectedPos->clover_tender_id) ? $connectedPos->account_type : null,
      'token' => [
      	'value' => $this->token ? $this->token : null,
      	'expiry' => $this->token ? Carbon::now()->addMinutes(env('JWT_TTL'))->timestamp : null
      ]
    ];
  }
}
