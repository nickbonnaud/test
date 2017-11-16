<form method="POST" action="{{ route('products.update', ['products' => $product->slug]) }}" class="form-horizontal" enctype="multipart/form-data">
	{{ method_field('PATCH') }}
  {{ csrf_field() }}
	<div class="box-body">
    <div class="form-group">
      <label for="name">Product name:</label>
      <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}" required>
    </div>

    <div class="form-group">
      <label for="price">Price:</label>
      <input-money v-bind:value="{{ $product->price }}" type="price"></input-money>
    </div>

    <div class="form-group">
      <label for="description">Product description:</label>
      <input type="text" name="description" id="description" class="form-control" value="{{ $product->description }}">
      <p class="help-block">Optional</p>
    </div>

    <div class="form-group">
      <label for="category">Category</label>
      <select id="category" class="js-example-tags form-control" multiple="multiple" name="category"></select>
      <p class="help-block">Optional</p>
    </div>

    <div class="form-group">
      <label for="sku">SKU:</label>
      <input type="text" name="sku" id="sku" class="form-control" value="{{ $product->sku }}">
      <p class="help-block">Optional</p>
    </div>

    <div class="photo-input">
      <label for="photo">Add Photo</label>
      <input type="file" name="photo" id="photo" value="$product->photo->url">
      <p class="help-block">Optional</p>
    </div>
	</div>

	<div class="box-footer">
	  <button type="submit" class="btn btn-success pull-right">Update</button>
	</div>
</form>