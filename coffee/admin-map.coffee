initialize = ->
  initLat = jQuery('#kodamaps-txt-input-lat').val() || 0
  initLng = jQuery('#kodamaps-txt-input-lng').val() || 0
  initZoom = if initLat || initLng then 16 else 0
  mapOptions =
    zoom: initZoom
    center: new google.maps.LatLng initLat,initLng
  map = new google.maps.Map document.getElementById("map_canvas"),mapOptions
  latLng = new google.maps.LatLng initLat,initLng
  marker = new google.maps.Marker
    position: latLng
    map: map

  jQuery('#kodamaps-txt-input-address, #kodamaps-txt-input-lat, #kodamaps-txt-input-lng').change ->
    geocoder = new google.maps.Geocoder()
    if @.id is 'kodamaps-txt-input-address'
      address = jQuery('#kodamaps-txt-input-address').val()
      geocoder.geocode 'address': address,
        (results, status) ->
          if status is google.maps.GeocoderStatus.OK
            latLng = new google.maps.LatLng results[0].geometry.location.k,results[0].geometry.location.D
            marker.position = latLng
            marker.setMap map
            map.panTo new google.maps.LatLng(marker.getPosition().k,marker.getPosition().D)
            map.setZoom 16
            jQuery('#kodamaps-txt-input-lat').val marker.getPosition().k
            jQuery('#kodamaps-txt-input-lng').val marker.getPosition().D
            jQuery('.kodamaps-postdata-address').val address
            jQuery('.kodamaps-postdata-lat').val marker.getPosition().k
            jQuery('.kodamaps-postdata-lng').val marker.getPosition().D
          return
    else
      latLng = new google.maps.LatLng jQuery('#kodamaps-txt-input-lat').val(),jQuery('#kodamaps-txt-input-lng').val()
      geocoder.geocode 'latLng': latLng,
        (results, status) ->
          if status is google.maps.GeocoderStatus.OK
            address = results[0].formatted_address
            marker.position = latLng
            marker.setMap map
            map.panTo new google.maps.LatLng(marker.getPosition().k, marker.getPosition().D)
            map.setZoom 16
            jQuery('#kodamaps-txt-input-address').val address
            jQuery('#kodamaps-txt-input-lat').val marker.getPosition().k
            jQuery('#kodamaps-txt-input-lng').val marker.getPosition().D
            jQuery('.kodamaps-postdata-address').val address
            jQuery('.kodamaps-postdata-lat').val marker.getPosition().k
            jQuery('.kodamaps-postdata-lng').val marker.getPosition().D
          return
    return # $().change ->
  return # initialize
window.onload = initialize
