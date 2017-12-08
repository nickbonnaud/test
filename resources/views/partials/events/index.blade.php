@if(count($events) > 0)
  @foreach($events as $event)
    <div class="box box-default">
      <div class="box-header with-border">
        <h3 class="box-title"><a href="{{ route('events.show', ['profiles' => $profile->slug, 'posts' => $event->id]) }}">{{ str_limit($event->title, 85) }}</a></h3>
        <p class="event-date pull-right">Date: {{ $event->formatedEventTime() }}</p>
      </div>
      <div class="box-body">
        @if(!is_null($event->photo))
          <div class="text-center">
            <img src="{{ $event->photo->url}}">
          </div>
          <hr>
        @elseif(!is_null($event->social_photo_url))
          <div class="text-center">
              <img src="{{ $event->social_photo_url }}">
          </div>
          <hr>
        @endif
        {{ $event->published_at->diffForHumans() }}
        by
        <a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}">
          <strong>{{ $profile->business_name }}</strong>
        </a>
          @include('partials.events.delete')
      </div>
    </div>
  @endforeach
@endif