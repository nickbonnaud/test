<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\CreateProfileRequest;
use App\Http\Requests\UpdateProfileRequest;

use App\Transaction;
use App\UserLocation;

class ProfilesController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tags = Tag::all();
        return view('profiles.create')->with('tags', $tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProfileRequest $request) {
        $user = auth()->user();
        $profile = $user->publish(new Profile($request->all()), $request->biz_county, $request->biz_state);
        $profile->addlocationData([
            'identifier' => $request->business_name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ],
        [
            'name' => $request->biz_city,
            'county' => $request->biz_county,
            'state' => $request->biz_state
        ],
        [
            'legal_biz_name' => $request->business_name,
            'biz_street_address' => $request->biz_street_address,
            'biz_city' => $request->biz_city,
            'biz_state' => $request->biz_state,
            'biz_zip' => $request->biz_zip,
            'phone' => $request->phone

        ])->tags()->sync($request->input('tags'));

        return redirect()->route('profiles.show', ['profiles' => $profile->slug]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function show(Profile $profile) {
        $this->authorize('view', $profile);
        return view('profiles.show', compact('profile', 'accountRoute'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function edit(Profile $profile) {
        $this->authorize('view', $profile);
        $tags = Tag::all();
        return view('profiles.edit', compact('profile', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Profile  $profile
     * @return \Illuminate\Http\Response
     */
    public function update(Profile $profile, UpdateProfileRequest $request) {
        $this->authorize('update', $profile);
        $profile->update($request->all());
        return redirect()->route('profiles.edit', ['profiles' => $profile->slug]);
    }

    public function test() {
        // UserLocation::create([
        //     'profile_id' => 1,
        //     'user_id' => 288
        // ]);

        $userLocation = UserLocation::where('profile_id', 1)->where('user_id', 288)->first();
        // $userLocation->removeLocation();

        // $transaction = Transaction::where('id', 482)->first();
        // $connectedPos = $transaction->profile->connectedPos;
        // $connectedPos->test();
        // $transaction->updateCloverFinalizedTransaction($connectedPos);
    }
}
