@if(count($products) > 0)
  @foreach($products as $product)
  	<tr class="product-row">
  		<td class="product-row-data"><a href="{{ route('products.edit', ['product' => $product->slug]) }}">{{ $product->name }}</a></td>
  		<td class="product-row-data">${{ $product->price }}</td>
  		@if($product->photo)
  			<td><img src="{{ $product->photo->thumbnail_url }}" class="product-image"></td>
      @else
        <td><img src="{{ asset('/images/noImage.png') }}" class="product-image"></td>
  		@endif
  		<td>@include('partials.products.delete')</td>
  	</tr>
  @endforeach
@endif