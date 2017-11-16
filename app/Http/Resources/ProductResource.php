<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProductResource extends Resource
{
  public function toArray($request) {
    return [
      'id' => $this->id,
      'name' => $this->name,
      'price' => $this->price,
      'description' => $this->description,
      'category' => $this->category,
      'sku' => $this->sku,
      'photo' => optional($this->photo)->url,
      'thumbnail' => optional($this->photo)->thumbnail_url
    ];
  }
}
