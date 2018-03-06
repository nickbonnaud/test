<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ApiTransactionResource extends Resource
{
  
  public function toArray($request) {
    if ($request->has('deals')) {
      return [
        'id' => $this->id,
        'redeemed' => $this->redeemed,
        'purchased_on' => $this->created_at,
        'post' => [
          'id' => $this->deal->id,
          'message' => $this->deal->message,
          'deal_item' => $this->deal->deal_item,
          'photo_thumb_url' => $this->deal->photo->apiThumbnailUrl,
          'price' => $this->deal->price,
          'logo' => $this->profile->logo->apiUrl,
          'business_name' => $this->profile->business_name,
          'published_at' => $this->deal->published_at,
          'end_date' => $this->deal->end_date,
          'is_redeemable' => $this->deal->is_redeemable,
        ]
      ];
    } else {
      return [
        'id' => $this->id,
        'business_name' => $this->profile->business_name,
        'business_slug' => $this->profile->slug,
        'logo' => $this->profile->logo->apiUrl,
        'products' => $this->products,
        'tax' => $this->tax,
        'tips' => $this->tips,
        'net_sales' => $this->net_sales,
        'total' => $this->total,
        'purchased_on' => $this->created_at,
        'status' => $this->status,
        'paid' => $this->paid,
        'bill_closed' => $this->bill_closed
      ];
    }
  }
}
