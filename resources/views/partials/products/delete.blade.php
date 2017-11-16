<form action="{{ route('products.destroy', ['products' => $product->slug]) }}" method="post">
  <input type="hidden" name="_method" value="DELETE">
  {{ csrf_field() }}
  <input type="submit" value="Delete" class="btn btn-block btn-danger btn-sm">
</form>