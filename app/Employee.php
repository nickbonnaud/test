<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model {

	protected $fillable = [
		'profile_id',
		'name',
		'pos_employee_id',
		'role'
	];

	public function profile() {
		return $this->belongsTo('App\Profile');
	}
}