<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{

	/**
	 * Fillable fields for a tag
	 * @var array
	 */
	protected $fillable = [
		'county',
		'state',
		'county_tax',
		'state_tax'
	];

	public function profiles() {
		return $this->hasMany('App\Profile');
	}
}
