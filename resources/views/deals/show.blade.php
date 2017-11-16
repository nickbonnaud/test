@extends('layouts.layoutPost')
<head>
    <meta property="og:type" content="article" />
    <meta property="og:url" content="https:www.pockeytbiz.com/deals/{{ $profile->slug }}/{{ $deal->id }}" /> 
    <meta property="og:title" content="{{ $deal->message }}" />
    @if($deal->photo)
        <meta property="og:image"  content="{{ $deal->photo->url }}" />
    @else
        <meta property="og:image"  content="{{ $profile->logo->url }}" />
    @endif
</head>

@section('content')
    @if(is_null($deal))
        <h2 class="text-center">Sorry! Looks like this deal was deleted! :(</h2>
    @endif
    <div class="row">
        <div class="col-md-12">
            <img class="photoLogo" src="{{ $profile->logo->url }}">
            <span class="partnername">{{ $profile->business_name }}</span>
            <p class="postTitle">Purchase this deal for ${{ $deal->price }} on Pockeyt to get a {{ $deal->deal_item }}.</p>

            <img class="postPhoto" src="{{ $deal->photo->url }}">
            <hr>
            <article class="postText">
                {!! $deal->formatted_message !!}
            </article>
            <hr>
            <div class="footer-date">{{ $deal->published_at->diffForHumans() }}</div>
            <p class="signature">- Brought to you by <a href="http://www.pockeyt.com/">Pockeyt</a>

        </div>
    </div>

@stop