@extends('layouts.layoutBasic')
@section('content')

  <div class="row">
    <div class="col-md-12">
      <h1>Create Business Profile</h1>
      <hr>
      @include ('partials.profiles.form_create')
      @include ('errors.form')
    </div>
  </div>
@stop
@section('scripts.footer')
  <script>
    function initMap() {
      var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: 35.7796, lng: -78.6382},
        zoom: 13
      });

      var input = document.getElementById('pac-input');

      var autocomplete = new google.maps.places.Autocomplete(input);
      autocomplete.bindTo('bounds', map);

      map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

      var infowindow = new google.maps.InfoWindow();
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

        if (place.geometry.viewport) {
          map.fitBounds(place.geometry.viewport);
        } else {
          map.setCenter(place.geometry.location);
          map.setZoom(17);
        }

        // Set the position of the marker using the place ID and location.
        marker.setPlace({
          placeId: place.place_id,
          location: place.geometry.location
        });
        marker.setVisible(true);

        var latitude = place.geometry.location.lat();
        var longitude = place.geometry.location.lng();
        var streetAddress;
        console.log(place);
        place.address_components.forEach(function(e) {
          if (e.types.includes('street_number')) {
            streetAddress = e.short_name;
          }
          if (e.types.includes('route')) {
            streetAddress = streetAddress + ' ' + e.short_name;
          }
          if (e.types.includes("administrative_area_level_1")) {
            $('#biz_state').val(e.short_name);
          }
          if (e.types.includes("administrative_area_level_2")) {
            $('#biz_county').val(e.short_name);
          }
          if (e.types.includes("locality")) {
            $('#biz_city').val(e.short_name);
          }
          if (e.types.includes('postal_code')) {
            $('#biz_zip').val(e.short_name);
          }
        });
        
        $('#latitude').val(latitude);
        $('#longitude').val(longitude);
        $('#phone').val(place.formatted_phone_number);
        $('#rating').val(place.rating);
        $('#google_id').val(place.place_id);
        $('#business_name').val(place.name);
        $('#website').val(place.website);
        $('#biz_street_address').val(streetAddress);

        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' +
            place.formatted_address);
        infowindow.open(map, marker);
      });
    }
    $('#tags').select2({
        placeholder: 'Type 3 or less tags that describe your business',
        maximumSelectionLength: 3
    });
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB5bWVb25GSXY-fhI5EFNJ8JualZcSluXE&libraries=places&callback=initMap"
        async defer></script>
@stop
