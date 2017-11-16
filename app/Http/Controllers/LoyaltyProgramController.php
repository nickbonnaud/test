<?php

namespace App\Http\Controllers;

use App\Profile;
use App\LoyaltyProgram;
use App\Http\Requests\CreateLoyaltyProgramRequest;
use Illuminate\Http\Request;

class LoyaltyProgramController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Profile $profile) {
    $this->authorize('view', $profile);
    return view('loyalty_programs.create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Profile $profile, CreateLoyaltyProgramRequest $request) {
    $this->authorize('update', $profile);
    $profile->loyaltyProgram()->save(new loyaltyProgram($request->all()));
    return redirect()->route('loyaltyProgram.show', ['profiles' => $profile->slug]);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\LoyaltyProgram  $loyaltyProgram
   * @return \Illuminate\Http\Response
   */
  public function show(Profile $profile) {
    $this->authorize('view', $profile);
    $loyaltyProgram = $profile->loyaltyProgram;
    return view('loyalty_programs.show', compact('loyaltyProgram'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\LoyaltyProgram  $loyaltyProgram
   * @return \Illuminate\Http\Response
   */
  public function destroy(Profile $profile) {
    $this->authorize('delete', $profile);
    $profile->loyaltyProgram->delete();
    return redirect()->route('loyaltyProgram.create', ['profiles' => $profile->slug]);
  }
}
