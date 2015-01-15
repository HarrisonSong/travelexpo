<?php $this->load->view('header.php');?>
<body>
 <div class="wrapper">
<?php $this->load->view('navbar.php');?>
<div class="main-content">
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span4">
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
 		<div id="city-nav">
 			
 		</div>
		</div>
		<div class="span8">
			<div id="list_places">

			</div>
		</div>
	</div>
</div>


<script type="text/javascript" src="<?php echo ASSEST_URL?>js/jquery.fittext.js"></script>
<script type="text/javascript">
var total_country = 0;
var total_city = 0;
var total_place = 0;

function postToFeed(place_id) {
      $.ajax({
			url: "<?php echo site_url("member/ajax_post_activity_wantToGo/".$uid)?>"+"/"+place_id,
			dataType: "json",
			success: function(data){
			}
		});
}

function load_info() {
	$.ajax({
		url: "<?php echo site_url("member/ajax_person_place")?>"+"/"+<?php echo $uid?>,
		dataType: "json",
		success: function(output){
			if (output.error != 0) {
				alert(output.error);
				return 0;
			}
			
			if (output.data.no_place == 0) {
				
			}
			
			total_place = output.data.no_place;
			var places_data = output.data.place_list;
			var countries_data = output.data.country_list;
			
			var string_output = "";
			var mis_output="";
			$.each(countries_data, function(country_name, country_info) {
				if (country_name != 0) {
					total_country++;
					string_output += "<h3 id="+  escape(country_name) +">"+country_name+"</h3>";
					string_output += '<ul class="thumbnails container-fluid" id="main_area">';
					$.each(country_info, function(city_name, places) {
						total_city++;
						$.each(places, function(place_id, key) {
							string_output += gen_place_info_html(key, places_data[key], city_name+", ");	
						});
						
					});
					string_output += "</ul>";
					$('#city-nav').append('<a class="side-nav" href="#'+ escape(country_name) +'" title="">'+country_name+'</a>');

				} else {
					mis_output += "<h3 id='others'>Other Places</h3>";
					mis_output += '<ul class="thumbnails container-fluid" id="main_area">';
					$.each(country_info, function(city_name, places) {
						$.each(places, function(place_id, key) {
							mis_output += gen_place_info_html(key, places_data[key],"");	
						});
					});
					mis_output += "</ul>";
					
				}
			});
			
			string_output=string_output+mis_output;
			if(mis_output!=''){
				$('#city-nav').append('<a class="side-nav" href="#others" title="">Other Places</a>');
			}

			$('#subtitle').html('in ' + total_country + ' countries, ' + total_city +' cities and ' +total_place+' places.');
			$("#list_places").html(string_output);


			$('.like-button').tooltip({'placement':'bottom'});
			photoEvent();
			
			$('.thumbnail .photo-preview').each(function(){
				var location_id = $(this).attr('data-location');
				console.log(location_id);
				$.ajax({
				url: "<?php echo site_url('jjp/like') . '?action=islike&place='?>" + location_id,
				dataType: "json",
				context: this,
				success:function(output){
					if(output.success==false){
						return 0;
					}

					if(output.is_liked==true){
						$(this).parent().find('.like-button').addClass('liked');
					}

				if(output.like_count==0){
                    $(this).parent().find('.like-button').attr('data-original-title', 'I want to go there!');
                }else{
                    $(this).parent().find('.like-button').attr('data-original-title', output.like_count+" friends want to go there");
                }
				}

				});
			});

			$('.thumbnail .like-button').click(function(){
				var location_id = $(this).parent().find('.photo-preview').attr('data-location');
				if($(this).hasClass('liked')){
					$(this).removeClass('liked');
					$.ajax({
						url: "<?php echo site_url('jjp/like') . '?action=dislike&place='?>" + location_id,
						dataType:'json',
						context:this,
						success:function(output){
							if(output.success==false){
								return 0;
							}

							if(output.like_count==0){
                    $(this).parent().find('.like-button').attr('data-original-title', 'I want to go there!');
                }else{
                    $(this).parent().find('.like-button').attr('data-original-title', output.like_count+" friends want to go there");
                }
						}
					});
				}else{
					$(this).addClass('liked');
					$.ajax({
						url: "<?php echo site_url('jjp/like') . '?action=like&place='?>" + location_id,
						dataType:'json',
						context:this,
						success:function(output){
							if(output.success==false){
								return 0;
							}

							if(output.like_count==0){
                    $(this).parent().find('.like-button').attr('data-original-title', 'I want to go there!');
                }else{
                    $(this).parent().find('.like-button').attr('data-original-title', output.like_count+" friends want to go there");
                }
						}
					});
					
					postToFeed($(this).attr('place_id'));
				}
			});
		}
	});
 }

function gen_place_info_html(key, info, city) {
	return "<li class='span6'><div class='thumbnail fixed-height'><a href='javascript:void(0);' title='I want to be there!' class='like-button' place_id='"+key+"'></a><div data-location="+key+" class='photo-preview'><a href='<?php echo site_url('member/gallery')."/".$uid."/".rawurlencode($title);?>/"+key+"' style='background-image:url("+info['src']+ ");' title=''></a></div><h3 class='location-name'>" +info['name']+ "</h3><p>"+city+info['no_photos']+" Photos</p></div></li>";
}

$(document).ready(function() {
	load_info();
	$('.responsive-heading').fitText();
});
	
</script>
<?php $this->load->view('footer.php');?>