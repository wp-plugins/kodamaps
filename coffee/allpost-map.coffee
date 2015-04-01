initialize = ->
  initLat = 0
  initLng = 0
  initLat = if kodamaps_posts? and kodamaps_posts.centerLat isnt '' then parseFloat(kodamaps_posts.centerLat, 10) else initLat
  initLng = if kodamaps_posts? and kodamaps_posts.centerLng isnt '' then parseFloat(kodamaps_posts.centerLng, 10) else initLng
  initZoom = if kodamaps_posts? and kodamaps_posts.zoom isnt '' then parseInt(kodamaps_posts.zoom,10) else 10
  address = if kodamaps_posts? and kodamaps_posts.centerAddr isnt '' then kodamaps_posts.centerAddr else ''

  if address isnt ''
    geocoder = new google.maps.Geocoder()

    geocoder.geocode 'address': address,
      (results,status) ->
        if status is google.maps.GeocoderStatus.OK
          initLat = results[0].geometry.location.k
          initLng = results[0].geometry.location.D
          mapOptions =
            zoom: initZoom
            center: new google.maps.LatLng initLat,initLng
            mapTypeid: google.maps.MapTypeId.ROADMAP
          if document.getElementById("map_canvas")
            map = new google.maps.Map document.getElementById("map_canvas"),mapOptions
          for post in kodamaps_posts.postInfo
            markerOption =
              position: new google.maps.LatLng post.lat,post.lng
              map: map
            if post.marker
              markerOption.icon = post.marker
            marker = new google.maps.Marker markerOption
        return
  else
    mapOptions =
      zoom: initZoom,
      center: new google.maps.LatLng(initLat, initLng),
      mapTypeid: google.maps.MapTypeId.ROADMAP

    if document.getElementById("map_canvas")
      map = new google.maps.Map document.getElementById("map_canvas"),mapOptions
    for post in kodamaps_posts.postInfo
      markerOption =
        position: new google.maps.LatLng post.lat,post.lng
        map: map
      if post.marker
        markerOption.icon = post.marker
      marker = new google.maps.Marker markerOption

  return

window.onload = initialize
