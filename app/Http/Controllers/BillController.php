<?php

namespace App\Http\Controllers;

use App\User;
use App\Profile;
use App\Transaction;
use Illuminate\Http\Request;

class BillController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
	}

	public function show(Profile $profile, User $user, Request $request) {
  	$this->authorize('view', $profile);
  	if ($request->input('employee')) {
  		$employee = User::find($request->input('employee'));
  		$employeeId = $employee->id;
  	} else {
  		$employeeId = '';
  	}
  	if ($request->input('bill')) {
  		$bill = Transaction::find($request->input('bill'));
  	} else {
  		$bill = '[]';
  	}
  	$customer = $user;
		return view('bill.show', compact('bill', 'customer', 'employeeId'));
  }
}
