function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
       alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    var lat =  position.coords.latitude;
    var lng = position.coords.longitude;
    $('#loc_latitude').val(lat);
    $('#loc_longitude').val(lng);
}

function initMap() {

        var map = new google.maps.Map(document.getElementById('mapid'), {
            center: {lat: 11.723699, lng: 124.69451},
            zoom: 4
        });
        // console.log(map);
        var card = document.getElementById('pac-card');
        var input = document.getElementById('pac-input');
        var types = document.getElementById('type-selector');
        var strictBounds = document.getElementById('strict-bounds-selector');



        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);
        // var options = {
        //     componentRestrictions: {country: "ph"}
        // };
        // console.log(options.componentRestrictions);
        var autocomplete = new google.maps.places.Autocomplete(input);
        // autocomplete.setComponentRestrictions({'country': ['ph']});

        // Bind the map's bounds (viewport) property to the autocomplete object,
        // so that the autocomplete requests use the current map bounds for the
        // bounds option in the request.
        var infowindow = new google.maps.InfoWindow();
        autocomplete.bindTo('bounds', map);

            var infowindowContent = document.getElementById('infowindow-content');
            infowindow.setContent(infowindowContent);
            var marker = new google.maps.Marker({
                  map: map,
                  anchorPoint: new google.maps.Point(0, -29),
                  draggable: true
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                infowindow.setPosition(pos);
                map.setCenter(pos);
                map.setZoom(13);
                marker.setPosition(pos);
                marker.setVisible(true);
            }, function() {
                handleLocationError(true, infoWindow, map.getCenter());
            });
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
        }
        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            alert(browserHasGeolocation ?
                                  'Error: The Geolocation service failed.' :
                                  'Error: Your browser doesn\'t support geolocation.');
        }

        google.maps.event.addListener(marker, 'dragend', function(event){
            document.getElementById('loc_latitude').value = event.latLng.lat();
            document.getElementById('loc_longitude').value = event.latLng.lng();
        });

        autocomplete.addListener('place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            // console.log(place);
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();

            if (!place.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);  // Why 17? Because it looks good.
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            var address = '';
            if (place.address_components) {
                address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }

            infowindowContent.children['place-name'].textContent = place.name;
            infowindowContent.children['place-address'].textContent = address;
            infowindow.open(map, marker);

            document.getElementById('loc_latitude').value = latitude;
            document.getElementById('loc_longitude').value = longitude;
        });
    }

function initMap2() {
  var lat = parseFloat(document.getElementById('edit_loc_latitude').value);
  var lng = parseFloat(document.getElementById('edit_loc_longitude').value);
  var workSiteLatLng = {lat: lat, lng: lng};
  // console.log(lat+"-"+lng);

  var map = new google.maps.Map(document.getElementById('edit_map'), {
      center: {lat: lat, lng: lng},
      zoom: 13
  });
  // console.log(map);
  var card = document.getElementById('pac-card');
  var input = document.getElementById('edit_pac-input');
  var types = document.getElementById('type-selector');
  var strictBounds = document.getElementById('strict-bounds-selector');



  map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);
  // var options = {
  //     componentRestrictions: {country: "ph"}
  // };
  // console.log(options.componentRestrictions);
  var autocomplete = new google.maps.places.Autocomplete(input);
  // autocomplete.setComponentRestrictions({'country': ['ph']});

  // Bind the map's bounds (viewport) property to the autocomplete object,
  // so that the autocomplete requests use the current map bounds for the
  // bounds option in the request.
  var infowindow = new google.maps.InfoWindow();
  autocomplete.bindTo('bounds', map);

      var infowindowContent = document.getElementById('edit_infowindow-content');
      infowindow.setContent(infowindowContent);
      var marker = new google.maps.Marker({
            map: map,
            position: workSiteLatLng,
            anchorPoint: new google.maps.Point(0, -29),
            draggable: true
  });

  function handleLocationError(browserHasGeolocation, infoWindow, pos) {
      infoWindow.setPosition(pos);
      alert(browserHasGeolocation ?
                            'Error: The Geolocation service failed.' :
                            'Error: Your browser doesn\'t support geolocation.');
  }

  google.maps.event.addListener(marker, 'dragend', function(event){
      document.getElementById('edit_loc_latitude').value = event.latLng.lat();
      document.getElementById('edit_loc_longitude').value = event.latLng.lng();
  });

  autocomplete.addListener('place_changed', function() {
      infowindow.close();
      marker.setVisible(false);
      var place = autocomplete.getPlace();
      // console.log(place);
      var latitude = place.geometry.location.lat();
      var longitude = place.geometry.location.lng();

      if (!place.geometry) {
          // User entered the name of a Place that was not suggested and
          // pressed the Enter key, or the Place Details request failed.
          window.alert("No details available for input: '" + place.name + "'");
          return;
      }

      // If the place has a geometry, then present it on a map.
      if (place.geometry.viewport) {
          map.fitBounds(place.geometry.viewport);
      } else {
          map.setCenter(place.geometry.location);
          map.setZoom(17);  // Why 17? Because it looks good.
      }
      marker.setPosition(place.geometry.location);
      marker.setVisible(true);

      var address = '';
      if (place.address_components) {
          address = [
          (place.address_components[0] && place.address_components[0].short_name || ''),
          (place.address_components[1] && place.address_components[1].short_name || ''),
          (place.address_components[2] && place.address_components[2].short_name || '')
          ].join(' ');
      }

      infowindowContent.children['edit_place-name'].textContent = place.name;
      infowindowContent.children['edit_place-address'].textContent = address;
      infowindow.open(map, marker);

      document.getElementById('edit_loc_latitude').value = latitude;
      document.getElementById('edit_loc_longitude').value = longitude;
  });
}

function initializeMaps(){
  initMap();
  initMap2();
}
