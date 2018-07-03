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

      $data = $this->checkForPockeytTransaction($orderId);
      $cloverTransaction = $this->getTransactionData($orderId);
      if ($data) {
        switch ($action) {
          case 'CREATE':
            $this->createCloverTransaction($cloverTransaction, $data);
            break;
          case 'UPDATE':
            $this->updateCloverTransaction($cloverTransaction, $data);
            break;
          case 'DELETE':
            
            break;
        }
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
    return $this->parseLineItems($lineItems);
  }

  private function parseLineItems($lineItems) {
    $pockeytCustomer = null;
    $purchasedProducts = [];
    dd($lineItems);
    foreach ($lineItems as $lineItem) {
      if ($lineItem->alternateName == 'pockeyt') {
        $customerId = substr($lineItem->itemCode, 8);
        $pockeytCustomer = User::where('id', $customerId)->first();
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
            $item = (object) [
              'id' => 'clover:' . $lineItem->item->id,
              'name' => $lineItem->name,
              'price' => $lineItem->price,
              'quantity' => 1
            ];
            array_push($purchasedProducts, $item);
          }
        } else {
          $item = (object) [
            'id' => 'clover:' . $lineItem->item->id,
            'name' => $lineItem->name,
            'price' => $lineItem->price,
            'quantity' => 1
          ];
          array_push($purchasedProducts, $item);
        }
      }
    }
    if ($pockeytCustomer) {
      $data = ['customer' => $pockeytCustomer, 'products' => $purchasedProducts];
      return $data;
    } else {
      return null;
    }
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
      'pos_transaction_id' => $cloverTransaction->id,
    ]);
    $transaction->save();

    if ($cloverTransaction->state == 'open') {
      $this->addNoteToTransaction($cloverTransaction->id, $customer);
    } else {
      $userLocation = UserLocation::where('user_id', $customer->id)->where('profile_id', $this->profile_id)->first();
      $posCustomerId = $userLocation->pos_customer_id;
      $this->removePockeytCustomerFromTransaction($cloverTransaction->id, $posCustomerId);
    }
  }

  private function updateCloverTransaction($cloverTransaction, $data) {
    $customer = $data['customer'];
    $products = $data['products'];
    $total = $cloverTransaction->total;
    $subTotalAndTax = $this->getCloverTransactionSubtotalAndTax($products, $total);
    $subTotal = $subTotalAndTax['subTotal'];
    $tax = $subTotalAndTax['tax'];

    $transaction = Transaction::where('pos_transaction_id', $cloverTransaction->id)->first();
    $transaction->update([
      'bill_closed' => $cloverTransaction->state != 'open',
      'products' => json_encode($products),
      'tax' => $tax,
      'net_sales' => $subTotal,
      'total' => $total,
    ]);
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

  private function removePockeytCustomerFromTransaction($cloverTransactionId, $posCustomerId) {
    $client = new Client(['base_uri' => env('CLOVER_BASE_URL')]);
    try {
      $response = $client->request('DELETE', 'v3/merchants/' . $this->merchant_id . '/orders/' . $cloverTransactionId . '/line_items/' . $posCustomerId, [
        'headers' => [
          'Authorization' => 'Bearer ' . $this->token,
          'Accept' => 'application/json'
        ]
      ]);
    } catch (ClientErrorResponseException $exception) {
      dd($exception->getResponse()->getBody(true));
    }
  }

  private function getCloverTransactionSubtotalAndTax($products, $total) {
    $subTotal = 0;
    foreach ($products as $product) {
      $subTotal = $subTotal + ($product->quantity * $product->price);
    }
    $tax = $total - $subTotal;
    return ['subTotal' => $subTotal, 'tax' => $tax];
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
    return json_decode($response->getBody()->getContents());
  }
}
