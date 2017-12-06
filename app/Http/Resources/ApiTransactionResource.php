<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiTransactionResource extends Resource
{
  
  public function toArray($request) {
    if ($request->has('deals')) {
      return [
        'id' => $this->id,
        'business_name' => $this->profile->business_name,
        'logo' => $this->profile->logo->apiUrl,
        'tax' => $this->tax,
        'net_sales' => $this->net_sales,
        'total' => $this->total,
        'redeemed' => $this->redeemed,
        'purchased_on' => $this->created_at,
        'post' => [
          'message' => $this->deal->messge,
          'deal_item' => $this->deal->deal_item,
          'photo' => $this->deal->photo->apiUrl,
          
        ]
      ];
    } else {
      return [
        'id' => $this->id,
        'business_name' => $this->profile->business_name,
        'logo' => $this->profile->logo->apiUrl,
        'products' => $this->products,
        'tax' => $this->tax,
        'tips' => $this->tips,
        'net_sales' => $this->net_sales,
        'total' => $this->total,
        'purchased_on' => $this->created_at
      ];
    }
  }
}
