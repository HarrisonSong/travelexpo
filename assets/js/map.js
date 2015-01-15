var Singapore = new google.maps.LatLng(0, 183.75);
var map;
var styleArray = [
{
    "featureType": "poi.park",
    "stylers": [
      { "visibility": "off" }
    ]
  }

]
function map_initialize() {
  var panOption = {
	position:google.maps.ControlPosition.LEFT_CENTER,
	//style:google.maps.ZoomControlStyle.DEFAULT
  };
  var mapOptions = {
    zoom: 2,
	//scaleControl:true,
	//panControl:true,
	//zoomControl:true,
	//panControlOptions:panOption,
    draggable:true,
    center: Singapore,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
	disableDefaultUI:true,
	styles: styleArray
  };

 map = new google.maps.Map(document.getElementById("map_canvas"),
      mapOptions);
}