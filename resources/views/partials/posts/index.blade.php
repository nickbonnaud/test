@if(count($posts) > 0)
  @foreach($posts as $post)
    <div class="box box-default">
      <div class="box-header with-border">
        <a href="{{ route('posts.show', ['profiles' => $profile->slug, 'posts' => $post->id]) }}">
          <h3 class="box-title">{{ str_limit($post->message, 85) }}</h3>
        </a>
      </div>
      <div class="box-body">
        @if(!is_null($post->photo))
          <div class="text-center">
              <img src="{{ $post->photo->url }}">
          </div>
          <hr>
        @endif
        {{ $post->published_at->diffForHumans() }}
        by
        <a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}">
          <strong>{{ $profile->business_name }}</strong>
        </a>
          @include('partials.posts.delete')
      </div>
    </div>
  @endforeach
@endif