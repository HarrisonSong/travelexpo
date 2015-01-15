<?php $this->load->view('header.php');?>
<?php $this->load->view('navbar.php');?>
<ul class="thumbnails container-fluid" id="main_area">
 	<li class="span4">
 		<div class="thumbnail fixed-height overview">
 			<div class="facetile">
 				<ul>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 					<li>
 						<div class="face-child-img" style="background-image:url(http://placehold.it/100x100);">
 						</div>
 					</li>
 				</ul>
 			</div>
 			<div class="black-overlay">
 				
 			</div>
 			<div class="text-overlay">
 				<h2>
 				<span id="total_place">0</span> Places
 				</h2>
 				<p>
 				for you to explore
 				</p>
 			</div>
 		</div>
 	</li>
</ul>
<script language="javascript">
/*
	var friends = new Array();
	<?php 
		$i = 0;
		foreach($friend_data as $friend) {
			echo 'friends['.$i.'] = new Array("'.$friend['id'].'","'.$friend['name'].'");';
			$i++;
		}
	?>
	var no_friend = <?php echo $no_friends?>;
	var places_data = new Object();
	places_data.no = 0;
	places_data.items = new Object();
	$(document).ready(function() {
		for(i = 0; i < no_friend; i++) {
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
						
						$("#total_place").html(places_data.no);
						
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
			url: "<?php echo site_url("member/place_info")?>"+"/"+place_id,
			dataType: "json",
			success: function(place){
				if (place.error != 0)
					return 0;
				
				$("#main_area").append('<li class="span4"><div class="thumbnail fixed-height"><div class="photo-preview"><a href="#" style="background-image:url('+place.data.pic+');" title=""></a></div><h3>'+place.data.name+'</h3><p>'+places_data.items[place_id].no_friend+' Friends, '+places_data.items[place_id].no_photos+' Photos</p></div></li>');
			}
		});
	 }
	 */
</script>
<?php $this->load->view('footer.php');?>