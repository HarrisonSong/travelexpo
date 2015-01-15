<?php $this->load->view('header.php');?>
<?php $this->load->view('navbar.php');?>

<ul class="thumbnails container-fluid" id="main_area">
 	<li class="span4">
 		<div class="thumbnail fixed-height overview">
 			<div class="friend_profile_pic">
				<?php 
 					echo '<div class="face-child-img full" style="background-image:url(\'https://graph.facebook.com/' . $uid . '/picture?type=large\');"></div>';
				?>
 			</div>
 			<div class="black-overlay">
 				
 			</div>
 			<div class="text-overlay friend_name">
 				<h2 class="responsive-heading">
 				<?php echo $title?>
 				</h2>
 				<p id="subtitle">
 				</p>
 			</div>
 		</div>
 	</li>
 	<?php foreach($data as $key=>$location){
 			echo "<li class='span4'><div class='thumbnail fixed-height'><a href='javascript:void(0);'' title='I want to be there!' class='like-button'></a><div data-location=". $key ." class='photo-preview'><a href='" . site_url('member/gallery') . "/" . $uid . "/" . rawurlencode($title) . "/" . $key . "/" . rawurlencode($location['name']) . "' style='background-image:url(" . $location['src'] . ");' title=''></a></div><h3 class='location-name'>" .  $location['name'] . "</h3><p>". $location['no_photos']." Photos</p></div></li>";
 	} ?>
</ul>

<script type="text/javascript" src="<?php echo ASSEST_URL?>js/jquery.fittext.js"></script>
<script type="text/javascript">
var locations = new Array();
<?php 

foreach($country as $key=>$city) {
	$country_name = $key;
	foreach($city as $key=>$location){
		$city_name = $key;
		foreach($location as $key=>$id){
			echo 'locations['. $key .'] = new Array("'.$city_name .'","'.$country_name.'");';
		}
	}
}
?>
$(document).ready(function() {
	$('.responsive-heading').fitText();
	$('.like-button').tooltip({'placement':'bottom'});
	photoEvent();
	var num_albums = $('.thumbnails > li').size()-1;
	$('#subtitle').html('in ' + num_albums +' travel destinations');

	$('.photo-preview').each(function(index){
		var location_id = $(this).attr('data-location');
		if(locations[location_id]!=null && locations[location_id][0]!='' && locations[location_id][1]!=''){
			var origin = $(this).parent().children('p').html();
			$(this).parent().find('p').html(locations[location_id][0]+", "+locations[location_id][1]+".  "+origin);
		}
	});
});
</script>
<?php $this->load->view('footer.php');?>