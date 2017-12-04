<?php

namespace App\Http\Controllers;

use App\Account;
use App\Profile;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
	}

	public function create(Profile $profile)
	{
		$this->authorize('update', $profile);
		return view('accounts.create');
	}

	public function store(Profile $profile, CreateAccountRequest $request)
	{
		$this->authorize('update', $profile);
		$account = $profile->createAccount(new Account($request->all()));
		return redirect()->route('accounts.edit', ['accounts' => $account->slug]);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Account  $account
	 * @return \Illuminate\Http\Response
	 */
	public function show(Account $account)
	{
		$this->authorize('view', $account);
		return view('accounts.show', compact('account'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  \App\Account  $account
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Account $account)
	{
		$this->authorize('view', $account);
		return view('accounts.create_'. $account->getAccountFormStage(), compact('account'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Account  $account
	 * @return \Illuminate\Http\Response
	 */
	public function update(Account $account, UpdateAccountRequest $request)
	{
		$this->authorize('update', $account);
		$account->updateAccount($request->all());
		if (!$account->owner_email) {
			return redirect()->route('accounts.edit', ['accounts' => $account->slug]);
		} elseif (!$account->method) {
			return redirect()->route('accounts.edit', ['accounts' => $account->slug]);
		} else {
			return redirect()->route('accounts.show', ['accounts' => $account->slug]);
		}
	}
}
