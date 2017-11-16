<?php

namespace App\Http\Controllers;

use App\Profile;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  public function show(Profile $profile) {
  	$this->authorize('view', $profile);
  	$employees = $profile->getEmployees();
  	return view('team.show', compact('employees'));
  }
}
