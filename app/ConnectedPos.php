<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\GuzzleException;
use Guzzle\Http\Exception\ClientErrorResponseException;
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
        ]
      ]);
    } catch (GuzzleException $e) {
      if ($e->hasResponse()) {
        dd("error: " . $e->getResponse());
      }
    }
    $body = json_decode($response->getBody());
    $this->clover_category_id = $body->id;
    $this->save();
  }

  public function createDeleteCustomer($eventType, $userLocation) {
    if ($eventType == 'enter') {
      $this->createPockeytCustomer($userLocation);
    } else {
      $this->deletePockeytCustomer($userLocation);
    }
  }

  public function createPockeytCustomer($userLocation) {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('POST', 'v3/merchants/' . $this->merchant_id . '/items', [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ],
        'json' => [
          'name' => $userLocation->user->first_name . ' ' . $userLocation->user->last_name,
          'alternateName' => 'pockeyt',
          'price' => 0,
          'priceType' => 'FIXED',
          'isRevenue' => false,
          'defaultTaxRates' => false,
          'categories' => [
            (object) ['id' => $this->clover_category_id]
          ]
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
    $body = json_decode($response->getBody());
    $posCustomerId = $body->id;

    $userLocation->pos_customer_id = $posCustomerId;
    $userLocation->save();
    $this->linkCustomerItemToCategory($userLocation);
  }

  public function deletePockeytCustomer($userLocation) {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('DELETE', 'v3/merchants/' . $this->merchant_id . '/items/' . $userLocation->pos_customer_id, [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
  }

  private function linkCustomerItemToCategory($userLocation) {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('POST', 'v3/merchants/' . $this->merchant_id . '/category_items', [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ],
        'json' => [
          'elements' => [
            (object) ['category' => (object) ['id' => $this->clover_category_id], 'item' => (object) ['id' => $userLocation->pos_customer_id]]
          ]
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
  }

  public function parseWebHookData($orderData) {
    foreach ($orderData as $order) {
      $action = $order['type'];
      $orderId = substr($order['objectId'], 2);

      $products = $this->checkForPockeytTransaction($orderId);

      switch ($action) {
        case 'CREATE':
          $this->createCloverTransaction($orderId);
          break;
        case 'UPDATE':
          
          break;
        case 'DELETE':
          
          break;
      }
    }
  }

  private function checkForPockeytTransaction($orderId) {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('GET', 'v3/merchants/' . $this->merchant_id . '/orders/' . $orderId . '/line_items', [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
    $lineItems = (json_decode($response->getBody()->getContents()))->elements;
    $products = $this->parseLineItems($lineItems);
    dd($products);
  }

  private function parseLineItems($lineItems) {
    $isPockeytCustomer = false;
    $purchasedProducts = [];
    foreach ($lineItems as $lineItem) {
      if ($lineItem->alternateName == 'pockeyt') {
        $isPockeytCustomer = true;
      } else {
        if (count($purchasedProducts) > 0) {
          foreach ($purchasedProducts as $purchasedProduct) {
            $itemAlreadyStored = false;
            if ($purchasedProduct->name == $lineItem->name) {
              $itemAlreadyStored = true;
              $purchasedProduct->quantity++;
              break;
            }
          }
          if (!$itemAlreadyStored) {
            $this->createFormattedItem($lineItem, $purchasedProducts); 
          }
        } else {
          $this->createFormattedItem($lineItem, $purchasedProducts);
        }
      }
    }
    if ($isPockeytCustomer) {
      return $purchasedProducts;
    } else {
      return null;
    }
  }

  private function createFormattedItem($lineItem, $purchasedProducts) {
    $item = (object) [
      'id' => 'clover:' . $lineItem->item->id,
      'name' => $lineItem->name,
      'price' => $lineItem->price,
      'quantity' => 1
    ];
    array_push($purchasedProducts, $item);
  }

  private function createCloverTransaction($orderId) {
    $cloverTransaction = $this->getTransactionData($orderId);
  }

  private function getTransactionData($orderId) {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('GET', 'v3/merchants/' . $this->merchant_id . '/orders/' . $orderId, [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
    dd($response->getBody());
  }

  public function modifyOrder() {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('POST', 'v3/merchants/' . $this->merchant_id . '/orders/72WGF62NTRY1E', [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ],
        'json' => [
          'note' => 'Pockeyt Pay Customer: Test User'
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
    dd($response->getBody());
  }
}
