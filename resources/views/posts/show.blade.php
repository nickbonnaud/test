@extends('layouts.layoutPost')
<head>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="https:www.pockeytbiz.com/posts/{{ $profile->slug }}/{{ $post->id }}" /> 
    <meta property="og:title" content="{{ $post->message }}" />
    @if($post->photo)
        <meta property="og:image"  content="{{ $post->photo->url }}" />
    @else
        <meta property="og:image"  content="{{ $profile->logo->url }}" />
    @endif
</head>

@section('content')
    @if(is_null($post))
        <h2 class="text-center">Sorry! Looks like this post was deleted! :(</h2>
    @endif
    <div class="row">
        <div class="col-md-12">
            <img class="photoLogo" src="{{ $profile->logo->url }}">
            <span class="partnername">{{ $profile->business_name }}</span>
            @if($post->photo)
                <img class="postPhoto" src="{{ $post->photo->url }}">
            @endif
            <hr>
            <article class="postText">
                {!! $post->formatted_message !!}
            </article>
            <hr>
            <div class="footer-date">{{ $post->published_at->diffForHumans() }}</div>
            <p class="signature">- Brought to you by <a href="http://www.pockeyt.com/">Pockeyt</a>

        </div>
    </div>

@stop