<?php

namespace App\Http\Controllers;

use App\GeoLocation;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateGeoLocationsRequest;

class GeoLocationController extends Controller
{
  public function __construct() {
    $this->middleware('auth');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(GeoLocation $geoLocation, UpdateGeoLocationsRequest $request) {
    $this->authorize('update', $geoLocation);
    $geoLocation->update([
      'latitude' => $request->latitude,
      'longitude' => $request->longitude
    ]);
    $geoLocation->updateTaxRate($request->county, $request->state);
    return redirect()->route('profiles.edit', ['profiles' => $geoLocation->profile->slug]);
  }
}
