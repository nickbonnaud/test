<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Account;
use App\Events\AccountReadyForProcessorReview;
use Illuminate\Http\Request;

class BusinessReviewController extends Controller
{
	public function __construct() {
		$this->middleware('admin');
    $this->middleware('auth');
  }

  public function show() {
    $profiles = Profile::where('approved', '=', false)->get();
    $accounts = Account::where('status', '=', 'review')->get();

    return view('admin.review', compact('profiles', 'accounts'));
  }

  public function updateProfile(Profile $profile, Request $request) {
    $profile->approved = $request->input('approved');
    $profile->save();
    return redirect()->back();
  }

  public function updateAccount(Account $account, Request $request) {
    $account->status = $request->input('status');
    event(new AccountReadyForProcessorReview($account));
    return redirect()->back();
  }
}
