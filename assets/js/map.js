var stockholm = new google.maps.LatLng(59.32522, 18.07002);
var parliament = new google.maps.LatLng(59.327383, 18.06747);
var styleArray = [
  {
    featureType: "all",
    stylers: [
      { saturation: 80 }
    ]
  },{
    featureType: "road.arterial",
    elementType: "geometry",
    stylers: [
      { hue: "#00ffee" },
      { saturation: 50 }
    ]
  },{
    featureType: "poi.business",
    elementType: "labels",
    stylers: [
      { visibility: "off" }
    ]
  }
]
function initialize() {
  var mapOptions = {
    zoom: 5,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    center: stockholm,
	disableDefaultUI:true,
	styles: styleArray
  };

var  map = new google.maps.Map(document.getElementById("map_canvas"),
      mapOptions);

var  marker = new google.maps.Marker({
    map:map,
    draggable:true,
    animation: google.maps.Animation.DROP,
    position: parliament,
	icon:'travel.png',
	title:"test"
  });
 
var infowin = new google.maps.InfoWindow({
	content:"that is very fun!"
});

google.maps.event.addListener(marker, 'click', function(){
	map.setZoom(13);
    map.setCenter(marker.getPosition());
	infowin.open(map,marker);
}); 

  google.maps.event.addListener(map, 'center_changed', function() {
    // 3 seconds after the center of the map has changed, pan back to the
    // marker.
    window.setTimeout(function() {
      map.panTo(marker.getPosition());
    }, 3000);
  }); 
}