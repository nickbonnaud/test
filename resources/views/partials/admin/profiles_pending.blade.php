@if(count($profiles) > 0)
  @foreach($profiles as $profile)
  	<tr class="product-row">
  		<td class="product-row-data">{{ $profile->business_name }}</td>
  		<td class="product-row-data"><a href="{{ $profile->website }}" target="_blank">{{ $profile->website }}</a></td>
      <td class="product-row-data">{{ $profile->description }}</td>
  		@if($profile->logo)
  			<td><img src="{{ $profile->logo->thumbnail_url }}" class="product-image"></td>
      @else
        <td>No Logo</td>
  		@endif
      @if($profile->hero)
        <td><img src="{{ $profile->hero->thumbnail_url }}" class="product-image"></td>
      @else
        <td>No Hero</td>
      @endif
  		<td>@include('partials.admin.approve_profile')</td>
  	</tr>
  @endforeach
@endif