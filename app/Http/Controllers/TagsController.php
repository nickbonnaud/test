<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Profile;
use App\Http\Requests\UpdateTagsRequest;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Profile $profile, UpdateTagsRequest $request) {
        $this->authorize('update', $profile);
        $profile->tags()->sync($request->input('tags'));
        return redirect()->route('profiles.edit', ['profiles' => $profile->slug]);
    }
}
