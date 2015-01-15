<?php $this->load->view('header.php');?>
<body class="map">
    <div class="wrapper map">
<?php $this->load->view('navbar.php');?>
<script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=XXXXXXXXXXX">
</script>
<script type="text/javascript" src = "<?php echo ASSEST_URL?>js/map.js"></script>
<div class="main-content map">
<div class="map-wrap">
<div class="profile-wrap">
	<div class="map-google">
		<div id="map_canvas" class="map-content"></div>
		<div id="profile-pic"></div>
		<div id="location-name"></div>
	</div>
	<div class="slide-button">
	</div>
	<div class="zoom-button">
		<p>Back</p>
	</div>
	<div class="friends-profile-wrap ">
	<ul>
		<?php for($i = 0; $i < 50 && $i <$no_friends; $i++) { 
			echo '
			<li>
			<a href="#"  title="' . $friend_data[$i]["name"] . '" fb_id="'.$friend_data[$i]["id"].'" class="face-child-img map" style="background-image:url(\'https://graph.facebook.com/'.$friend_data[$i]['id'].'/picture?type=square\');">
			</a>
			</li>';
		} ?>
	</ul>

</div>
</div>
</div>
<script type="text/javascript">
var friend_list = new Array();

<?php for($i = 0; $i <$no_friends; $i++) { 
			echo 'friend_list['. $i .'] = new Array("'.$friend_data[$i]["name"] . '","'.$friend_data[$i]["id"] .'"); ';
} ?>

$("#location-name").hide();
map_initialize();
var markerList = new Array();
var cityList = new Array();
$('.face-child-img.map').click(function(){
	$('#location-name').fadeOut();
	//alert($(this).attr('fb_id'));
	//console.log(cityList.length);
	//console.log($(this).attr('data-original-title'));
	load_map($(this).attr('fb_id'),$(this).attr('data-original-title'), true);

});

$(document).ready(function() {
	$('.zoom-button').hide();
	$('#profile-pic').html('<img src="https://graph.facebook.com/<?php echo $this->session->userdata["fb_id"] ?>/picture?type=normal" alt=""><h3><?php echo $this->session->userdata("username") ?></h3>');
	<?php 
		echo 'load_map("'.$this->session->userdata["fb_id"].'","'.$this->session->userdata["username"].'","true");';
	?>

	var current=50;

	$('.friends-profile-wrap').scroll(function(){
		console.log(friend_list.length);
		if($('.friends-profile-wrap li').last().positionAncestor('.friends-profile-wrap').top<=350){
			var i;
			for(i=current;(i-current)<40 && i<friend_list.length;i++){
				$('.friends-profile-wrap ul').append('<li><a href="#"  title="'+ friend_list[i][0] + '" fb_id="'+ friend_list[i][1] +'" class="face-child-img map active" style="background-image:url(\'https://graph.facebook.com/'+ friend_list[i][1] +'/picture?type=square\');"></a></li>');
				
			}
			current=i;

			$('.face-child-img.map').tooltip({
						animation: false
				});
		}

		$('.face-child-img.map').unbind("click");
		$('.face-child-img.map').click(function(){
			$('#location-name').fadeOut();
			//alert($(this).attr('fb_id'));
			//console.log(cityList.length);
			//console.log($(this).attr('data-original-title'));
			load_map($(this).attr('fb_id'),$(this).attr('data-original-title'), true);

});

	});

});
function length(table){
	var count = 0;
	for(var item in table){
		count++;
	}
	return count;
}

function show(table){
	for(var item in table){
		console.log(table[item]);
	}
}

function load_map(fb_id,name,need_clear_map) {
	//console.log(fb_id);
	if (need_clear_map) {
		if (markerList) {
   			 for (var i = 0; i < markerList.length; i++ ) {
      			markerList[i].setMap(null);
    		 }
  		}
		markerList = new Array();
		if(cityList){
			for(var city in cityList){
				delete cityList[city];
			}
		}
		cityList = new Array();
	}
				$('#profile-pic').fadeOut(500,function(){
					$('#profile-pic').html('<img src="https://graph.facebook.com/'+fb_id+'/picture?type=normal"></img><h3>'+ name +'</h3>');
					$(this).fadeIn(500);
				});

				$.ajax({
				url: "<?php echo site_url("member/ajax_map")?>"+"/"+fb_id,
				dataType: "json",
				success: function(output){
					if (output.error != '') {
						alert(output.data.error);
						return 0;
					}

					if(output.data.no_places == 0) {
						//console.log("nodata");
						$('#location-name').fadeOut(500, function(){
							$(this).html('<a href="<?php echo site_url("member/person")?>'+ '/'+fb_id+'/'+ unescape(encodeURIComponent(name))+'" title=""><h2>'+0+' place worldwide >></h2></a>').fadeIn(500);
						});
						return 0;
					}

					
					console.log("city clear already" + length(cityList));
					console.log("marker clear already" + length(markerList));
					console.log(cityList["Singapore"]);

					$.each(output.data.list,function(key,info){
						if(info.city != 0){
						
							if(!cityList[info.city]){
								//console.log("first be added in" + info.city);
								cityList[info.city] = [1,info.city,info.country,info.latitude,info.longitude];
								//console.log(length(cityList));
								//show(cityList);
								//console.log(cityList[info.city]);
							}else{
								//console.log("repeat");
								cityList[info.city][0] += 1;
								//console.log(length(cityList));
								//show(cityList);
								//console.log(cityList[info.city]);
							}
							//console.log(cityList[info.city]);
						}else{
							console.log("one more 0 city");
						}
					});
					var count = 0;
					for(var city in cityList){
						count += cityList[city][0];
					}
					$('#location-name').fadeOut(500, function(){
						$(this).html('<a href="<?php echo site_url("member/person")?>'+ '/'+fb_id+'/'+ unescape(encodeURIComponent(name))+'" title=""><h2>'+count+' places worldwide >></h2></a>').fadeIn(500);
					});
					for (var city in cityList){
						var latLng = new google.maps.LatLng(cityList[city][3],cityList[city][4]);
							var marker = new google.maps.Marker({
								map:map,
								draggable:false,
								animation: google.maps.Animation.DROP,
								icon:"http://chart.apis.google.com/chart?chst=d_simple_text_icon_left&chld="+cityList[city][0]+"|20|000000|airport|24|0033FF|FFFFFF",
								//icon:"http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld="+cityList[city][0]+"|357EC7|FFFFFF",
								position:latLng,
								title:cityList[city][1] + " , " + cityList[city][2]
							});	
							google.maps.event.addListener(marker, 'click', (function(marker){
									return function(){
								map.setCenter(marker.getPosition());
								map.setZoom(13);
								$('.zoom-button').show();
								$('#location-name').fadeOut(500, function(){
										$(this).html('<a href="<?php echo site_url("member/person")?>'+ '/'+fb_id+'/'+ unescape(encodeURIComponent(name))+'#'+escape(marker.title.substr(marker.title.indexOf(",")+2))+'" title=""><h2>'+marker.title+' >></h2></a>').fadeIn(500);
								});
								$('.zoom-button').click(function(){
									var centerglobal=new google.maps.LatLng(0, 183.75);
									map.setZoom(2);
									map.setCenter(centerglobal);
									$(this).hide();
								});

								}
							})(marker));
							markerList.push(marker);
					}


					
					/*$.each(output.data.list,function(key,info){
						if(!cityList[info.city]&&info.city != 0){
							//console.log(info.city);
							var city = [info.latitude,info.longitude];
							cityList[info.city] = city;
							//console.log(++count);
							var latLng = new google.maps.LatLng(cityList[info.city][0], cityList[info.city][1]);
							var marker = new google.maps.Marker({
								map:map,
								draggable:false,
								animation: google.maps.Animation.DROP,
								icon:"http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=1|357EC7|FFFFFF",
								position:latLng,
								title:name + ' , ' + info.city + " , " + info.country
							});

							google.maps.event.addListener(marker, 'click', function(){
								map.setCenter(marker.getPosition());
								$('#location-name').fadeOut(500, function(){
										$(this).html('<h1>'+info.city+', '+info.country+'</h1>').fadeIn(500);
								});
								});
							markerList.push(marker);
						}
					});*/
				}
			});
		}
		
		/*
		var friends = new Array();
		var no_friend = <?php echo $no_friends?>;
		var places_data = new Object();
		places_data.no = 0;
		places_data.items = new Object();
		
		var markers = new Array();
		$(document).ready(function() {
			for(i = 0; i < 10; i++) {
				load_friend_info(i);
			} 
		});
		
		function load_friend_info(index) {
			$.ajax({
				url: "<?php echo site_url("member/ajax_friend_info")?>"+"/"+friends[index][0]+"/1",
				dataType: "json",
				success: function(friend_info){
					if (friend_info.error != 0 || friend_info.data.no_place == 0)
						return 0;
						
					$.each(friend_info.data.places, function(key, info) {
						if(typeof places_data.items[key] == "undefined") {
							places_data.no = places_data.no + 1;
							places_data.items[key] = {no_friend:1,no_photos:info};
							load_place(key);
						} else {
							places_data.items[key].no_friend = places_data.items[key].no_friend + 1;
							places_data.items[key].no_photos = places_data.items[key].no_photos + info;
						}
					});
				}
			});
		 }
		 
		function load_place(place_id) {
	 	$.ajax({
			url: "<?php echo site_url("member/place_position")?>"+"/"+place_id,
			dataType: "json",
			success: function(place){
				if (place.error != 0)
					return 0;
				
				markers[place_id] = new google.maps.Marker({
													map:map,
													draggable:true,
													animation: google.maps.Animation.DROP,
													icon:"http://ec2-175-41-163-41.ap-southeast-1.compute.amazonaws.com/assets/img/pin.png",
													position: new google.maps.LatLng(place.data.latitude, place.data.longitude),
												  });
			}
		});
		
		}
		*/


		/**
 * Get the current coordinates of the first element in the set of matched
 * elements, relative to the closest positioned ancestor element that
 * matches the selector.
 * @param {Object} selector
 */
jQuery.fn.positionAncestor = function(selector) {
    var left = 0;
    var top = 0;
    this.each(function(index, element) {
        // check if current element has an ancestor matching a selector
        // and that ancestor is positioned
        var $ancestor = $(this).closest(selector);
        if ($ancestor.length && $ancestor.css("position") !== "static") {
            var $child = $(this);
            var childMarginEdgeLeft = $child.offset().left - parseInt($child.css("marginLeft"), 10);
            var childMarginEdgeTop = $child.offset().top - parseInt($child.css("marginTop"), 10);
            var ancestorPaddingEdgeLeft = $ancestor.offset().left + parseInt($ancestor.css("borderLeftWidth"), 10);
            var ancestorPaddingEdgeTop = $ancestor.offset().top + parseInt($ancestor.css("borderTopWidth"), 10);
            left = childMarginEdgeLeft - ancestorPaddingEdgeLeft;
            top = childMarginEdgeTop - ancestorPaddingEdgeTop;
            // we have found the ancestor and computed the position
            // stop iterating
            return false;
        }
    });
    return {
        left:    left,
        top:    top
    }
};

		</script>	
		<?php $this->load->view('footer.php');?>