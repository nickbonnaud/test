@extends('layouts.layoutDashboard')
@section('content')
<div class="content-wrapper-scroll" id="product">
  <div class="scroll-main">
    <div class="scroll-main-contents">
    	<section class="content-header">
        <h1>
          {{ $product->name }}
        </h1>
        <ol class="breadcrumb">
          <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
          <li><a href="{{ route('products.profile', ['profiles' => $profile->slug]) }}">Inventory</a></li>
          <li class="active">Change Product</li>
        </ol>
      </section>
    	<section class="content">
      	<div class="scroll-container-analytics col-md-6">
          <div class="scroll-contents">
        		<div class="box box-primary">
        			<div class="box-header with-border">
        				<h3 class="box-title">Edit {{ $product->name }} info</h3>
        			</div>
        			<div class="box-body">
              	@include ('errors.form')
  			      	@include ('partials.products.form_edit')
  			      </div>
        		</div>
          </div>
      	</div>
      	@if($product->photo)
          <div class="col-md-6">
        		<div class="box box-primary">
        			<div class="box-header with-border">
        				<h3 class="box-title">{{ $product->name }}</h3>
        			</div>
        			<div class="box-body">
      					<img src="{{ $product->photo->url }}" class="business-hero-img img-responsive" alt="Product Image">
  			      </div>
        		</div>
        	</div>
        @endif
    	</section>
    </div>
  </div>
</div>
@stop

@section('scripts.footer')
<script>

  getCategories = function() {
    var categoryObjects = {!! $categories !!};
    var categories = [];
    categoryObjects.forEach(function(categoryObject) {
      if (categoryObject.category) {
        if (categories.indexOf(categoryObject.category) == -1) {
          categories.push(categoryObject.category);
        }
      }
    });
    return categories;
  }
  $("#category").select2({
    tags: true,
    data: getCategories(),
    maximumSelectionLength: 1
  }).val('{{ $product->category }}').trigger('change');

</script>
@stop