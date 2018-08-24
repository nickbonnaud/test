<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\GuzzleException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use GuzzleHttp\Client;
use Config;

class ConnectedPos extends Model {
	protected $fillable = ['account_type', 'token', 'merchant_id', 'clover_tender_id'];

  public function profile() {
    return $this->belongsTo('App\Profile');
  }

  public function addPockeytCustomersCategory() {
    if(!$categoryId = $this->checkIfCategoryExistsInClover()) {
      $categoryId = $this->createPockeytCustomersCategory();
    }
    $this->clover_category_id = $categoryId;
    $this->save();
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
          'name' => Config::get('constants.clover.category')
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
    $body = json_decode($response->getBody());
    return $body->id;
  }

  public function checkIfCategoryExistsInClover() {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);

    try {
      $response = $client->request("GET", "v3/merchants/{$this->merchant_id}/categories", [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
    $body = json_decode($response->getBody());
    $categories = $body->elements;
    foreach ($categories as $category) {
      if (strtolower($category->name) == strtolower(Config::get('constants.clover.category'))) {
        return $category->id;
      }
    }
    return null;
  }

  public function createDeleteCustomer($eventType, $userLocation) {
    if ($eventType == 'enter') {
      $this->addPockeytCustomer($userLocation);
    } else {
      $this->deletePockeytCustomer($userLocation);
    }
  }

  public function addPockeytCustomer($userLocation) {
    
  }

  public function checkIfCloverCustomerExists() {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request("GET", "v3/merchants/{$this->merchant_id}/categories/5EYQZRQB2WSA0/items", [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
    $body = json_decode($response->getBody());
    $customers = $body->elements;
    foreach ($customers as $customer) {
      dd("in foreach");
    }
    dd("after");
  }

  public function createPockeytCustomer() {
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
          'code' => 'pockeyt-' . $userLocation->user->id,
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

      if ($action == 'DELETE') {
        $this->deleteCloverTransaction($orderId);
      } else {
        $data = $this->getLineItems($orderId);
        if ($customer = $data['customer']) {
          $cloverTransaction = $this->getTransactionData($orderId);
          $transaction = Transaction::where('pos_transaction_id', $cloverTransaction->id)->first();
          $userLocation = UserLocation::where('profile_id', $this->profile_id)->where('user_id', $customer->id)->first();
          $userLocation->clover_line_item_id = $data['line_item_id'];
          $userLocation->exit_notification_sent = false;
          $userLocation->save();

          if ($transaction) {
            $this->updateCloverTransaction($cloverTransaction, $data, $transaction);
          } else {
            $this->createCloverTransaction($cloverTransaction, $data);
          }
        }
      }
    }
  }

  public function getLineItems($orderId) {
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
    return $this->parseLineItems($lineItems);
  }

  private function parseLineItems($lineItems) {
    $pockeytCustomer = null;
    $lineItemId = null;
    $purchasedProducts = [];
    foreach ($lineItems as $lineItem) {
      if ($lineItem->alternateName == 'pockeyt') {
        $customerId = substr($lineItem->itemCode, 8);
        $lineItemId = $lineItem->id;
        $pockeytCustomer = User::where('id', $customerId)->first();
      } else {
        if (count($purchasedProducts) > 0) {
          foreach ($purchasedProducts as $purchasedProduct) {
            $itemAlreadyStored = false;
            if (isset($lineItem->item) && ($purchasedProduct->id == ('clover:' . $lineItem->item->id))) {
              $itemAlreadyStored = true;
              $purchasedProduct->quantity++;
              break;
            }
          }
          if (!$itemAlreadyStored) {
            $item = (object) [
              'id' => 'clover:' . (isset($lineItem->item) ? $lineItem->item->id : 'custom'),
              'name' => $lineItem->name,
              'price' => $lineItem->price,
              'quantity' => 1
            ];
            array_push($purchasedProducts, $item);
          }
        } else {
          $item = (object) [
            'id' => 'clover:' . (isset($lineItem->item) ? $lineItem->item->id : 'custom'),
            'name' => $lineItem->name,
            'price' => $lineItem->price,
            'quantity' => 1
          ];
          array_push($purchasedProducts, $item);
        }
      }
    }
    $data = ['customer' => $pockeytCustomer, 'products' => $purchasedProducts, 'line_item_id' => $lineItemId];
    return $data;
  }

  private function createCloverTransaction($cloverTransaction, $data) {
    $customer = $data['customer'];
    $products = $data['products'];
    $total = $cloverTransaction->total;
    $subTotalAndTax = $this->getCloverTransactionSubtotalAndTax($products, $total);
    $subTotal = $subTotalAndTax['subTotal'];
    $tax = $subTotalAndTax['tax'];

    $transaction = new Transaction([
      'profile_id' => $this->profile_id,
      'user_id' => $customer->id,
      'paid' => false,
      'bill_closed' => $cloverTransaction->state != 'open',
      'status' => 10,
      'products' => json_encode($products),
      'tax' => $tax,
      'net_sales' => $subTotal,
      'total' => $total,
      'pos_transaction_id' => $cloverTransaction->id
    ]);
    $transaction->save();
    $this->addNoteToTransaction($cloverTransaction->id, $customer);
  }

  private function updateCloverTransaction($cloverTransaction, $data, $transaction) {
    $customer = $data['customer'];
    $products = $data['products'];
    $total = $cloverTransaction->total;
    $subTotalAndTax = $this->getCloverTransactionSubtotalAndTax($products, $total);
    $subTotal = $subTotalAndTax['subTotal'];
    $tax = $subTotalAndTax['tax'];

    $transaction->update([
      'bill_closed' => false,
      'status' => 10,
      'products' => json_encode($products),
      'tax' => $tax,
      'net_sales' => $subTotal,
      'total' => $total,
    ]);
  }

  private function deleteCloverTransaction($cloverTransactionId) {
    $transaction = Transaction::where('pos_transaction_id', $cloverTransactionId)->first();
    if ($transaction) {
      $transaction->delete();
    }
  }

  private function addNoteToTransaction($cloverTransactionId, $customer) {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('POST', 'v3/merchants/' . $this->merchant_id . '/orders/' . $cloverTransactionId, [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ],
        'json' => [
          'note' => 'Pockeyt Pay Customer: ' . $customer->first_name . ' ' . $customer->last_name 
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
  }

  public function closeCloverTransaction($transaction) {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    $total = $transaction->tax + $transaction->net_sales;
    try {
      $response = $client->request('POST', 'v3/merchants/' . $this->merchant_id . '/orders/' . $transaction->pos_transaction_id . '/payments', [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ],
        'json' => [
          'tender' => (object) [
            "id" => 'BPQN5844528BA'
          ],
          'amount' => $total
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
  }

  public function removePockeytCustomerFromTransaction($cloverTransactionId, $customer) {
    $userLocation = UserLocation::where('user_id', $customer->id)->where('profile_id', $this->profile_id)->first();
    $lineItemId = $userLocation->clover_line_item_id;
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('DELETE', 'v3/merchants/' . $this->merchant_id . '/orders/' . $cloverTransactionId . '/line_items/' . $lineItemId, [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
  }

  public function getCloverTransactionSubtotalAndTax($products, $total) {
    $subTotal = 0;
    foreach ($products as $product) {
      $subTotal = $subTotal + ($product->quantity * $product->price);
    }
    $tax = $total - $subTotal;
    return ['subTotal' => $subTotal, 'tax' => $tax];
  }

  public function getTransactionData($orderId) {
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
    return json_decode($response->getBody()->getContents());
  }














  public function test() {
     $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('GET', 'v3/merchants/' . $this->merchant_id . '/tenders', [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
    dd(json_decode($response->getBody()->getContents()));

  }


}
