initialize = ->
  initLat = if kodamaps_post? then kodamaps_post.lat else 31.773827
  initLng = if kodamaps_post? then kodamaps_post.lng else 130.7518837
  initZoom = if kodamaps_post? and kodamaps_post.zoom isnt '' then parseInt kodamaps_post.zoom,10 else 10

  mapOptions =
    zoom: initZoom
    center: new google.maps.LatLng initLat,initLng
    mapTypeid: google.maps.MapTypeId.ROADMAP

  if document.getElementById("map_canvas")
    map = new google.maps.Map document.getElementById("map_canvas"),mapOptions
    marker = new google.maps.Marker
        position: new google.maps.LatLng kodamaps_post.lat,kodamaps_post.lng
        map: map
        icon: kodamaps_post.marker
    map.panTo new google.maps.LatLng initLat,initLng

  return

window.onload = initialize
