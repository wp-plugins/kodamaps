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
    draggable: true
    map: map

  # 緯度経度入力後の処理
  inputLatLng = ->
    address = jQuery('#kodamaps-txt-input-address').val()
    geocoder = new google.maps.Geocoder()
    geocoder.geocode 'address': address,
      (results, status) ->
        if status is google.maps.GeocoderStatus.OK
          latLng = new google.maps.LatLng results[0].geometry.location.k,results[0].geometry.location.D
          marker.position = latLng
          marker.setMap map
          map.panTo new google.maps.LatLng(marker.getPosition().k,marker.getPosition().D)
          # map.setZoom 16
          jQuery('#kodamaps-txt-input-lat').val marker.getPosition().k
          jQuery('#kodamaps-txt-input-lng').val marker.getPosition().D
          jQuery('.kodamaps-postdata-address').val address
          jQuery('.kodamaps-postdata-lat').val marker.getPosition().k
          jQuery('.kodamaps-postdata-lng').val marker.getPosition().D
        return
    return

  # 住所入力後の処理
  inputAddress = ->
    latLng = new google.maps.LatLng jQuery('#kodamaps-txt-input-lat').val(),jQuery('#kodamaps-txt-input-lng').val()
    geocoder = new google.maps.Geocoder()
    geocoder.geocode 'latLng': latLng,
      (results, status) ->
        if status is google.maps.GeocoderStatus.OK
          address = results[0].formatted_address
          marker.position = latLng
          marker.setMap map
          map.panTo new google.maps.LatLng(marker.getPosition().k, marker.getPosition().D)
          # map.setZoom 16
          jQuery('#kodamaps-txt-input-address').val address
          jQuery('#kodamaps-txt-input-lat').val marker.getPosition().k
          jQuery('#kodamaps-txt-input-lng').val marker.getPosition().D
          jQuery('.kodamaps-postdata-address').val address
          jQuery('.kodamaps-postdata-lat').val marker.getPosition().k
          jQuery('.kodamaps-postdata-lng').val marker.getPosition().D
        return
    return

  # マップをクリックした時に移動して位置情報を取得
  google.maps.event.addListener map, 'click', (event) ->
    marker.position = event.latLng
    marker.setMap map
    console.log marker.getPosition().lat()
    console.log marker.getPosition().lng()
    jQuery('#kodamaps-txt-input-lat').val marker.getPosition().lat()
    jQuery('#kodamaps-txt-input-lng').val marker.getPosition().lng()
    inputAddress()
    return

  # マーカーをドラッグした後のイベントをハンドリング
  google.maps.event.addListener marker, 'dragend', ->
    console.log marker.getPosition().lat()
    console.log marker.getPosition().lng()
    jQuery('#kodamaps-txt-input-lat').val marker.getPosition().lat()
    jQuery('#kodamaps-txt-input-lng').val marker.getPosition().lng()
    inputAddress()
    return

  # 入力エリアの値に変化があった際のイベントをハンドリング
  jQuery('#kodamaps-txt-input-address, #kodamaps-txt-input-lat, #kodamaps-txt-input-lng').change ->
    # 住所が変更された場合
    if @.id is 'kodamaps-txt-input-address'
      inputLatLng()
    # 緯度経度が変更された場合
    else
      inputAddress()
    return

  return

window.onload = initialize
