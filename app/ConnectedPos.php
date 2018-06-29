<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class ConnectedPos extends Model
{
	protected $fillable = ['account_type', 'token', 'merchant_id'];

  public function profile() {
    return $this->belongsTo('App\Profile');
  }

  public function createPockeytCustomersCategory() {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('POST', 'v3/merchants/' . $this->merchant_id . '/categories', [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ],
        'json' => [
          'name' => 'Pockeyt Customers',
          'id' => 'pockeyt_customers_category'
        ]
      ]);
    } catch (GuzzleException $e) {
      if ($e->hasResponse()) {
        dd("error: " . $e->getResponse());
      }
    }
    dd("success " . json_decode($response->getBody()));
  }
}
