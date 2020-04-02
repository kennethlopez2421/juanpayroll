    		var mymap = L.map('mapid');
 			// var mymap = L.map('mapid');
  			L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    		maxZoom: 18,
    		id: 'mapbox.streets',
    		accessToken: 'pk.eyJ1IjoicmVucmVucGVkcmFqZXRhIiwiYSI6ImNqcmJ3OXl2NDAwdHAzeXJzbGV5N2N2ZHMifQ.1ORAeNT5FUwJOuTRnFdY_g'
			}).addTo(mymap);
    		var geocodeService = L.esri.Geocoding.geocodeService();
  			//Get the location of the map
  			var current_position;
  			var get_location = "";
  			function locate() {
    			mymap.locate({setView: true, maxZoom: 18});
  			}
  			// // call locate every 3 seconds... forever
  			// // setInterval(locate, 3000);
  			locate();
  			mymap.options.minZoom = 16;

			  function onLocationFound(e) {
			    // if position defined, then remove the existing position marker and accuracy circle from the map
			    if (current_position) {
			        mymap.removeLayer(current_position);
			    }
			      geocodeService.reverse().latlng(e.latlng).run(function(error, result) {
			       L.marker(result.latlng).addTo(mymap).bindPopup("Your Location").openPopup();
			       // L.marker(result.latlng).addTo(mymap).bindPopup(result.address.Match_addr).openPopup();
			        $("#getaddress").val(result.address.Match_addr);
			        $("#mapdetails").html("Location: "+result.address.Match_addr);
			   });
			  }
			    mymap.on('click', function(e) {
    				geocodeService.reverse().latlng(e.latlng).run(function(error, result) {
     				L.marker(result.latlng).addTo(mymap).bindPopup(result.address.Match_addr).openPopup();
    				});
  				});
			  function onLocationError(e) {
			    console.log(e.message);
			  }
			  $('#submitbtn').click(function(){
			  	alert($("#getaddress").val());
			  });
			  mymap.on('locationfound', onLocationFound);
			  mymap.on('locationerror', onLocationError);