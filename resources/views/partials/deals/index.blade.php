@if(count($deals) > 0)
  @foreach($deals as $deal)
    <div class="box box-default">
      <div class="box-header with-border">
       <p class="pull-right">Expires on: <b>{{ $deal->end_date }}</b></p>
        <h3 class="box-title" v-on:click="getPurchasedDeals({{ $deal->id }})"><a href="#" data-toggle="modal" data-target="#dealModal">{{ str_limit($deal->message, 85) }}</a></h3>
      <i class="fa fa-calendar pull-right"></i>
      </div>
      <div class="box-body">
        @if($deal->photo)
          <div class="text-center">
            <img src="{{ $deal->photo->url}}">
          </div>
          <hr>
        @endif
        <i>This post is redeemable for ${{ $deal->price }}</i>
          @include('partials.deals.delete')
      </div>
    </div>
  @endforeach
@endif