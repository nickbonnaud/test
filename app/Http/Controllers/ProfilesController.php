<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\CreateProfileRequest;
use App\Http\Requests\UpdateProfileRequest;

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
        $profile = $user->publish(new Profile($request->except(['latitude', 'longitude', 'county', 'state', 'city'])), $request->county, $request->state);
        $profile->addlocationData([
            'identifier' => $request->business_name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ],
        [
            'name' => $request->city,
            'county' => $request->county,
            'state' => $request->state
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
        return view('profiles.show', compact('profile'));
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
}
