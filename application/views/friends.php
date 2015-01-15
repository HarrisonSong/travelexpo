<?php $this->load->view('header.php');?>
<body>
	<a name="top"></a>
 <div class="wrapper">
<?php $this->load->view('navbar.php');?>
<div class="main-content">
<ul class="thumbnails container-fluid" id="main_area">
 	<li class="span4">
 		<div class="thumbnail fixed-height overview">
 			<div class="facetile">
 				<ul>
				<?php for($i = 0; $i < 20 && $i < $no_friends; $i++) { 
 					echo '
					<li>
 						<div class="face-child-img" style="background-image:url(\'https://graph.facebook.com/'.$friend_data[$i]['id'].'/picture?type=square\');">
 						</div>
 					</li>';
				} ?>
 				</ul>
 			</div>
 			<div class="black-overlay">
 				
 			</div>
 			<div class="text-overlay">
 				<h2>
 				<?php echo $no_friends?> friends
 				</h2>
 				<p>
 				with travel photos
 				</p>
 			</div>
 		</div>
 	</li>
</ul>
<a href = "#top" id = "back-top"><img src = "<?php echo ASSEST_URL?>img/up-arrow.png"/></a>
<button onclick="show()" class="btn btn-primary show_more" data-loading-text="Loading..." id="show_more_button">Show More</button>
</div>
<script language="javascript">
	var friends = new Array();
	<?php 
		$i = 0;
		foreach($friend_data as $friend) {
			echo 'friends['.$i.'] = new Array("'.$friend['id'].'","'.$friend['name'].'");';
			$i++;
		}
	?>
	var no_friend = <?php echo $no_friends?>;
	var current = 0;
	var limit = 10;
	var loading =0;
	$(document).ready(function(){
		show();
	});
	
	function show() {
		$("#show_more_button").button('loading');
		for(var i = current; i < current + limit & i < no_friend; i++) {
			loading++;
			load_friend_info(i);
		}
		current = i;
		
		// hide show more button if there is no more firends need to load
		if (current == no_friend) {
			$("#show_more_button").hide();
		}
	}
	 
	function load_friend_info(index) {
		 $.ajax({
			url: "<?php echo site_url("member/ajax_friend")?>"+"/"+friends[index][0],
			dataType: "json",
			success: function(friend_info){
				loading--;
				if (loading <= 0)
					$("#show_more_button").button('reset')
					
				if (friend_info.error != 0 || friend_info.data.no_photos == 0)
					return 0;
				$("#main_area").append('<li class="span4"><div class="thumbnail fixed-height"><div class="photo-preview"><a href="<?php echo site_url("member/person")?>'+ '/'+friends[index][0]+'/" style="background-image:url('+friend_info.data.a_photo_src+');" title=""></a></div><h3>'+friends[index][1]+'</h3><p>'+friend_info.data.no_places+' Places, '+friend_info.data.no_photos+' Photos</p></div></li>');
				photoEvent();
			}
		});
	 }
</script>
<?php $this->load->view('footer.php');?>