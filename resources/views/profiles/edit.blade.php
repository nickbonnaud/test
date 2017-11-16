@extends('layouts.layoutDashboard')

@section('content')
<div class="content-wrapper-scroll">
  <div class="scroll-main">
    <div class="scroll-main-contents">
      <section class="content-header">
        <h1>
          Your Business Profile
        </h1>
        <ol class="breadcrumb">
          <li><a href="{{ route('profiles.show', ['profiles' => $profile->slug]) }}"><i class="fa fa-dashboard"></i> Home</a></li>
          <li class="active">Business Profile</li>
        </ol>
      </section>

        <!-- Main content -->
      <section class="content">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-body box-profile">
              @if(is_null($profile->hero))
                <a href="#" data-toggle="modal" data-target="#businessHeroModal">
                <img src="{{ asset('/images/defaultBackground.png') }}" class="business-hero-img img-responsive" alt="Business Hero Image">
                </a>
              @else
                <img src="{{$profile->hero->url }}" class="business-hero-img img-responsive" alt="Business Hero Image">
                <div class="title-space text-right">
                  @include('partials.photos.form_delete_hero')
                </div>
              @endif
              @if(is_null($profile->logo))
                <a href="#" data-toggle="modal" data-target="#businessLogoModal">
                  <img src="{{ asset('/images/defaultLogo.png') }}" class="business-logo-img img-responsive" alt="Business Logo Image">
                </a>
              @else
                <img src="{{ asset($profile->logo->url) }}" class="business-logo-img img-responsive" alt="Business Logo Image">
                <div class="title-space-logo">
                    @include('partials.photos.form_delete_logo')
                </div>
              @endif
              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Business Name</b>
                  <p class="pull-right">{{ $profile->business_name }}</p>
                </li>
                <li class="list-group-item">
                  <b>Website</b>
                  <p class="pull-right">{{ $profile->website }}</p>
                </li>
              </ul>
              <strong>Description</strong>
              @if( strlen($profile->description) > 225 )
                <p>{{ substr($profile->description, 0, 225) }}...</p>
              @else
                <p>{{ $profile->description }}</p>
              @endif
              <hr>
              <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#businessInfoModal">
                <b>Edit</b>
              </a>
            </div>
          </div>
        @include ('errors.form')
        </div>
          <!-- Business Location -->
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Location used for payments</h3>
              <div class="box-body">
                <input id="pac-input" class="controls" type="text" placeholder="Enter a location">
                <div id="map"></div>
                <div id="infowindow-content">
                  <span id="place-name"  class="title"></span>
                </div>
              </div>
              <div class="box-footer">
                <a href="#" class="btn btn-danger btn-block" data-toggle="modal" data-target="#businessLocationModal">
                	<b>Set Business to THIS Location</b>
                </a>
              </div>
            </div>
          </div>
          <div class="box box-primary collapsed-box last-box">
            <div class="box-header with-border">
              <h3 class="box-title">Tags</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
              </div>
              <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            	<ul class="list-group list-group-unbordered"">
                @foreach ($profile->tags as $tag)
                  <li class="list-group-item business-tags">{{ $tag->name }}</li>
                @endforeach
              </ul>
              <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#businessTagsModal">
                <b>Change</b>
              </a>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

 <!--  Modals -->
<div class="modal fade" id="businessHeroModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header-timeline">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="userPhotoModalLabel">Change Background Image</h4>
      </div>
      <div class="modal-body-customer-info">
        <div class="box-body photo-modal">
          @include('partials.photos.form_create_hero')
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="businessLogoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header-timeline">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="userPhotoModalLabel">Change Logo Image</h4>
      </div>
      <div class="modal-body-customer-info">
        <div class="box-body photo-modal">
          @include('partials.photos.form_create_logo')
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="businessInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header-timeline">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="userInfoModalLabel">Edit Info</h4>
      </div>
      <div class="modal-body-customer-info">
      	@include('partials.profiles.form_edit')
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="businessTagsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header-timeline">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="businessTagsModalLabel">Change tags by typing and selecting new ones or deleting old ones</h4>
      </div>
        <div class="modal-body-customer-info">
          @include('partials.tags.form_edit')
        </div>
    </div>
  </div>
</div>
<div class="modal fade" id="businessLocationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header-timeline">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="businessTagsModalLabel"><b>Warning</b> this changes the location of your business, which impacts your ability to collect payments</h4>
      </div>
      <div class="modal-body-customer-info">
        @include('partials.geoLocations.form_edit')
      </div>
    </div>
  </div>
</div>
@stop

@section('scripts.footer')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/dropzone.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5bWVb25GSXY-fhI5EFNJ8JualZcSluXE&libraries=places&callback=initMap"></script>

<script>
  function initMap() {
    @if(isset($profile->geoLocation))
      var latitude = {!! $profile->geoLocation->latitude !!};
      var longitude = {!! $profile->geoLocation->longitude !!}
      var zoomSet = 17;
    @else
      var latitude = 35.7796;
      var longitude = -78.6382;
      var zoomSet = 13;
    @endif
    
    var bizLatlng = new google.maps.LatLng(latitude,longitude);
    var map = new google.maps.Map(document.getElementById('map'), {
    center: {lat: latitude, lng: longitude},
    zoom: zoomSet,
    gestureHandling: 'cooperative'
    });

    var defaultMarker = new google.maps.Marker({
      position: bizLatlng,
    });

    defaultMarker.setMap(map);

    var input = document.getElementById('pac-input');

    var autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

    map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

    var infowindow = new google.maps.InfoWindow();
    var infowindowContent = document.getElementById('infowindow-content');
    infowindow.setContent(infowindowContent);
    var geocoder = new google.maps.Geocoder;
    var marker = new google.maps.Marker({
    map: map
    });
    marker.addListener('click', function() {
    infowindow.open(map, marker);
    });

    autocomplete.addListener('place_changed', function() {
      infowindow.close();
      var place = autocomplete.getPlace();

      if (!place.geometry) {
        return;
      }
      geocoder.geocode({'placeId': place.place_id}, function(results, status) {
        if (status !== 'OK') {
          window.alert('Geocoder failed due to: ' + status);
          return;
        }
        map.setZoom(17);
        map.setCenter(place.geometry.location);

        marker.setPlace({
            placeId: place.place_id,
            location: place.geometry.location
        });
        marker.setVisible(true);
        infowindowContent.children['place-name'].textContent = place.name;
        infowindow.open(map, marker);
      });

      var latitude = place.geometry.location.lat();
      var longitude = place.geometry.location.lng();

      place.address_components.forEach(function(e) {
        if (e.types.includes("administrative_area_level_1")) {
            $('#state').val(e.short_name);
        } 
        if (e.types.includes("administrative_area_level_2")) {
            $('#county').val(e.short_name);
        }
        if (e.types.includes("postal_code")) {
            $('#zip').val(e.short_name);
        }
      });
      $('#latitude').val(latitude);
      $('#longitude').val(longitude);
    });
  };

  Dropzone.options.uploadHero = {
    paramName: 'photo',
    maxFilesize: 3,
    acceptedFiles: '.jpg, .jpeg, .png, .bmp',
    init: function() {
      this.on('success', function() {
        window.location.reload();
      });
    }
  };

  Dropzone.options.uploadLogo = {
    paramName: 'photo',
    maxFilesize: 3,
    acceptedFiles: '.jpg, .jpeg, .png, .bmp',
    init: function() {
      this.on('success', function() {
        window.location.reload();
      });
    }
  };

  $('#tags').select2({
    placeholder: 'Type 3 or less tags that describe your business',
    maximumSelectionLength: 3
  });

</script>
@stop