@extends('layouts.layoutBasic')
@section('content')
    <div class="jumbotron">
        <div class="row">
            <div class="col-sm-8">
                <h1>Pockeyt Business Dashboard</h1>
        
                <p>An easier way to connect with your customers</p>
            </div>
            <div class="col-sm-4 loginbuttons">
                @auth
                    @if($profile)
                        <a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}" class="btn btn-primary">View Profile</a>
                    @else
                        <a href="{{ route('profiles.create') }}" class="btn btn-primary">Create Profile</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Sign Up</a>
                @endauth
            </div>
        </div>
    </div>
@stop