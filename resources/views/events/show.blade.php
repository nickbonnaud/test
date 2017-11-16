@extends('layouts.layoutPost')
<head>
  <meta property="og:type" content="article" />
  <meta property="og:url" content="https:www.pockeytbiz.com/events/{{ $profile->slug }}/{{ $event->id }}" /> 
  <meta property="og:title" content="{{ $event->title }}" />
  @if($event->photo)
      <meta property="og:image"  content="{{ $event->photo->url }}" />
  @else
      <meta property="og:image"  content="{{ $profile->logo->url }}" />
  @endif
</head>

@section('content')
	@if(is_null($event))
        <h2 class="text-center">Sorry! Looks like this event was deleted! :(</h2>
    @endif
    <div class="row">
        <div class="col-md-12">
            <img class="photoLogo" src="{{ $profile->logo->url }}">
            <span class="partnername">{{ $profile->business_name }}</span>
            <p class="postTitle">{{ $event->title }}</p>

            <img class="postPhoto" src="{{ $event->photo->url }}">
            <hr>
            <article class="postText">
                {!!  $event->formatted_body !!}
            </article>
            <hr>
            <div class="footer-date">{{ $event->published_at->diffForHumans() }}</div>
            <p class="signature">- Brought to you by <a href="http://www.pockeyt.com/">Pockeyt</a>

        </div>
    </div>
@stop