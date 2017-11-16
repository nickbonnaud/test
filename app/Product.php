<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

	protected $fillable = [ 'name', 'price', 'description', 'category', 'sku', 'end_date', 'price'];

	public function getRouteKeyName() {
    return 'slug';
  }

	public function getPriceAttribute($price) {
    return round($price / 100, 2);
  }

	public function setPriceAttribute($price) {
    $this->attributes['price'] =  preg_replace("/[^0-9\.]/","", $price) * 100;
  }

  public function setNameAttribute($name) {
    if ($this->name != $name) {
      $slug = str_slug($name, '-');
      $count = Product::raw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
      $this->attributes['slug'] = $count ? "{$slug}-{$count}" : $slug;
    }
    $this->attributes['name'] = $name;
  }

	public function photo() {
    return $this->belongsTo('App\Photo');
  }

	public function profile() {
    return $this->belongsTo('App\Profile');
  }

  public function addPhoto($file) {
    if ($file) {
      $this->associatePhoto(Photo::fromForm($file));
    }
    return $this;
  }

  public function updatePhoto($file) {
    if ($file) {
      $this->destroyPhoto();
      $this->associatePhoto(Photo::fromForm($file));
    }
    return $this;
  }

	public function associatePhoto($photo) {
    return $this->photo()->associate($photo);
  }

  public function destroyPhoto() {
  	$photo = $this->photo;
  	$this->photo()->dissociate()->save();
    $photo->delete();
  }

  public function scopeFilter($query, $filters, $profile, $type) {
    return $filters->apply($query, $type)->where('profile_id', '=', $profile->id)->with('photo');
  }

  public static function syncSquareInventory($profile) {
    $squareInventory = SquareAccount::getInventory($profile);
    if ($squareInventory == []) return 'no_inventory';
    foreach ($squareInventory as $item) {
      $name = $item->name;
      if ($name != 'Pockeyt Customer') {
        foreach ($item->variations as $variation) {
          if (! $product = Product::where('square_id', '=', $variation->id)->first()) {
            $product = new Product;
          }
          self::updateProduct($variation, $name, $product, $profile);
        }
      }
    }
    return 'success';
  }

  public static function updateProduct($variation, $name, $product, $profile) {
    $product->name = $name . ' ' . $variation->name;
    $product->price = $variation->price_money->amount / 100;
    $product->sku = isset($variation->sku) ? $variation->sku : null;
    $product->square_id = $variation->id;
    $profile->products()->save($product);
  }
}
