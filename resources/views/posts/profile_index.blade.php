@extends('layouts.layoutDashboard')
@section('content')
<div class="content-wrapper-scroll">
  <div class="scroll-main">
    <div class="scroll-main-contents">
    	<section class="content-header">
        <h1>
          Add | Recent Posts
        </h1>
        <ol class="breadcrumb">
          <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Posts</li>
        </ol>
      </section>
    	<section class="content">
      	<div class="col-md-6 col-sm-6 col-xs-12">
      		<div class="box box-primary">
      			<div class="box-header with-border">
      				<h3 class="box-title">Create a New Post</h3>
      			</div>
            @include ('errors.form')
    				@include('partials.posts.form_create_post')
      		</div>
      	</div>
        <div class="scroll-container-analytics col-md-6 col-sm-6 col-xs-12">
            <div class="scroll-contents">
              @include('partials.posts.index', ['posts' => $posts, 'no_icons' => true])
            </div>
        </div>
    	</section>
    </div>
  </div>
</div>
@stop