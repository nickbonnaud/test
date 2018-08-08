<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class PayEmployeeResource extends Resource {

	public function toArray($request) {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'employee_id' => $this->pos_employee_id,
			'role' => $this->role
		];
	}
}