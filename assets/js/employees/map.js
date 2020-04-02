  function initMap(){
    console.log("map connected");
    //checks if geolocation is available on browser
    if (navigator.geolocation) {
        // navigator.geolocation.watchPosition(function(position){
        navigator.geolocation.getCurrentPosition(function(position){
          console.log(position.coords.accuracy);
          var getlatitude = position.coords.latitude;
          var getlongitude = position.coords.longitude;
          var location = {lat:getlatitude ,lng:getlongitude};
          var map = new google.maps.Map(document.getElementById("mapid"),{
            zoom: 15,
            center: location,
            gestureHandling: 'cooperative'
          });

          var geocoder = new google.maps.Geocoder;
          var infowindow = new google.maps.InfoWindow;
          geocodelatlang(geocoder,map,infowindow,getlatitude,getlongitude);
            // console.log(map);
        });
    }
    else {
      alert("Geolocation is not supported by this browser.");
    }
  }

  //converts latlng to address
  function geocodelatlang(geocoder,map,infowindow,getlatitude,getlongitude){
    var latlng = {lat: getlatitude, lng: getlongitude};
    geocoder.geocode({'location': latlng}, function(results, status) {
          if (status === 'OK') {
            if (results[0]) {
              map.setZoom(18);
              var marker = new google.maps.Marker({
                position: latlng,
                map: map
              });
              infowindow.setContent(results[0].formatted_address);
              infowindow.open(map, marker);
              $("#mapdetails").html(results[0].formatted_address);
              var geoAddress = {
                lat : results[0].geometry.location.lat(),
                lng : results[0].geometry.location.lng()
              }

              // $("#getaddress").val(results[0].formatted_address);
              $('#getaddress').val(JSON.stringify(geoAddress));
              // console.log(results[0].geometry.location.lat());
              // console.log( $("#getaddress").val());
            } else {
              window.alert('No results found');
            }
          } else {
            window.alert('Geocoder failed due to: ' + status);
          }
        });
    }
    //this will confirm the location is accessible
  function check_location(){
    var confirmlocation = "location_accessible";
    $("#confirmlocation").val(confirmlocation);
  }
