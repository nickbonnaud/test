<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PayBeaconResource extends Resource
{
  public function toArray($request)
  {

    return [
      'uuid' => $this->uuid,
      'identifier' => $this->identifier,
    ];
  }
}
