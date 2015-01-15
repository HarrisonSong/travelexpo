<?php $this->load->view('header.php');?>
 <div class="wrapper">
<?php $this->load->view('navbar.php');?>
<div class="main-content">

<script language="javascript">
	var need_cache_friends = new Array();
	<?php 
		$i = 0;
		foreach($need_cache_friends as $friend) {
			echo 'need_cache_friends['.$i.'] = new Array("'.$friend['id'].'","'.$friend['name'].'");';
			$i++;
		}
	?>
	var no_friend = <?php echo count($need_cache_friends)?>;
	var current = 0;
	var limit = 10;
	var total_cache = 0;
	function update_friend_list() {
		$("#update_friend_list_button").html("Loading...");
		$.ajax({
			url: "<?php echo site_url("member/ajax_update_friend_list")?>",
			dataType: "json",
			success: function(output){
				$("#update_friend_list_button").hide();
				if (output.error != 0) {
					alert(output.error);
				} else {
					$("#total_friends").html("Total friends: "+output.no_friends);
				}
			}
		});
	}
	
	function remove_cache() {
		$("#remove_cache_button").html("Loading...");
		$.ajax({
			url: "<?php echo site_url("member/ajax_remove_cache")?>",
			dataType: "json",
			success: function(output){
				$("#remove_cache_button").hide();
			}
		});
	}
	
	function cache() {
		
		if (current >= no_friend) {
			$("#cache_message").html("Finish caching!");
			return;
		}
		
		$("#cache_message").show();
		$("#cache_more_button").hide();
		var sub_array = new Array();
		for(var i = current; i < current + limit & i < no_friend; i++) {
			sub_array[i - current] = need_cache_friends[i][0];
		}
		load_friend_info(sub_array);
		current = i;
	}
	
	function load_friend_info(sub_array_friends) {
		 $.ajax({
			url: "<?php echo site_url("member/ajax_cache_friend")?>",
			dataType: "json",
			type: "POST",
			data: "list=" + sub_array_friends,
			success: function(output){
				if (output.error != 0) {
					$("#cache_message").html("Error... Please reload the page");
				} else {
					total_cache += output.no_friends;
					$("#cache_message").html("Caching process ... Finish "+total_cache+" friends!");
					cache();
				}
			}
		});
	 }
	
	
	function load_need_tag_photo() {
		 $.ajax({
			url: "<?php echo site_url("member/ajax_need_tag_photo")?>",
			dataType: "json",
			success: function(output){
				
				if (output.error != 0) {
					return 0;
				}
				
				if (output.total_photo == 0) {
					return 0;
				}
				
				$("#photo_tag_message").html("You have "+output.total_photo+" photos need to add location. Please add location for them and click Clear Cached Photos to update them on the application");
					
				$.each(output.data, function(key, info) {
					$("#main_area").append('<li class="span4"><div class="thumbnail fixed-height"><div class="photo-preview"><a href="'+info.link+'" style="background-image:url('+info.src_big+');" title="" target="tag_photo"></a><div><p><a href="'+info.link+'" title="" target="tag_photo">ADD LOCATION</a></p></div></li>');
				});
				
			}
		});
	 }
</script>

<div class="well settings">
	<button class="btn" onclick="update_friend_list()" id="update_friend_list_button">Refresh Friend List</button><span id="total_friends"></span>
<button class="btn" onclick="remove_cache()" id="remove_cache_button">Clear Cached Photos</button>
<span id="total_friends"></span>
<p>Number of Friend already cached: <?php echo $no_cached_friends;?></p>
<p>You still have <?php echo count($need_cache_friends);?> friends need to cache</p>
<button class="btn" onclick="cache()" id="cache_more_button">Cache them now!</button>
<p id="cache_message" style="display:none">Caching....</p>
</div>

<p align="center" id="photo_tag_message"></p>
<ul class="thumbnails container-fluid" id="main_area">
</ul>

<script type="text/javascript">
	jQuery(document).ready(function() {
		if(no_friend==0){
			$('#cache_more_button').hide();
		}
		load_need_tag_photo();
	});
</script>

<?php $this->load->view('footer.php');?>