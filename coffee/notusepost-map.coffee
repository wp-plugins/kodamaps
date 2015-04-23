initialize = ->
  jQuery(document).find('[id^=map_canvas]').each ->
    postInfo = window['kodamaps_posts_'+jQuery(@).attr('id').replace('map_canvas_','')]
    initLat = if postInfo.centerLat? and postInfo.centerLat isnt '' then parseFloat postInfo.centerLat,10 else 0
    initLng = if postInfo.centerLng? and postInfo.centerLng isnt '' then parseFloat postInfo.centerLng,10 else 0
    initZoom = if postInfo.zoom? and postInfo.zoom isnt '' then parseInt postInfo.zoom,10 else 10
    address = if postInfo.centerAddr? and postInfo.centerAddr isnt '' then postInfo.centerAddr else ''

    if address isnt ''
      _this = @
      geocoder = new google.maps.Geocoder()
      geocoder.geocode 'address': address,
        (results,status) ->
          if status is google.maps.GeocoderStatus.OK
            initLat = results[0].geometry.location.k
            initLng = results[0].geometry.location.D
            opt =
              zoom: initZoom
              center: new google.maps.LatLng initLat,initLng
              mapTypeid: google.maps.MapTypeId.ROADMAP
            map = new google.maps.Map _this,opt
            marker = new google.maps.Marker
              position: new google.maps.LatLng initLat,initLng
              map: map
            map.panTo new google.maps.LatLng initLat,initLng
    else
      opt =
        zoom: initZoom
        center: new google.maps.LatLng initLat,initLng
        mapTypeid: google.maps.MapTypeId.ROADMAP
      map = new google.maps.Map @,opt
      marker = new google.maps.Marker
        position: new google.maps.LatLng initLat,initLng
        map: map
      map.panTo new google.maps.LatLng initLat,initLng

    return #each
  return #initialize

window.onload = initialize
