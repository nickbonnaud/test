<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;

class PayTipsResource extends Resource {

	public function toArray($request) {
		return [
			'date' => $this->updated_at->toDayDateTimeString(),
			'transaction_id' => $this->pos_transaction_id,
			'employee_id' => $this->employee_id,
			'total' => $this->net_sales + $this->tax,
			'tip' => $this->tips,
		];
	}
}