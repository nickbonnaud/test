<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class TransactionResource extends Resource
{
  
  public function toArray($request) {
    return [
      'id' => $this->id,
      'status' => $this->status,
      'customer_name' => $this->user->first_name . ' ' . $this->user->last_name
    ];
  }
}
