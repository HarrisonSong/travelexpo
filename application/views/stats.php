<?php $this->load->view('header.php'); ?>
<body>
 <div class="wrapper">
<?php $this->load->view('navbar.php'); ?>
<script type="text/javascript" src="<?php echo ASSEST_URL?>js/graph_people.js"></script>
<script type="text/javascript" src="<?php echo ASSEST_URL?>js/graph_place.js"></script>
<script type="text/javascript" src="<?php echo ASSEST_URL?>js/spin.min.js"></script>

<div class="main-content">
<script>
var opts = {
		  lines: 13, // The number of lines to draw
		  length: 15, // The length of each line
		  width: 4, // The line thickness
		  radius: 10, // The radius of the inner circle
		  corners: 1, // Corner roundness (0..1)
		  rotate: 0, // The rotation offset
		  color: '#FFF', // #rgb or #rrggbb
		  speed: 1, // Rounds per second
		  trail: 60, // Afterglow percentage
		  shadow: true, // Whether to render a shadow
		  hwaccel: false, // Whether to use hardware acceleration
		  className: 'spinner', // The CSS class to assign to the spinner
		  zIndex: 2e9, // The z-index (defaults to 2000000000)
		  top: 'auto', // Top position relative to parent in px
		  left: 'auto' // Left position relative to parent in px
		};
var spinner ;
function post(button) {
		//create spin for submission 
		var target = document.getElementById('test');
		spinner = new Spinner(opts).spin(target);
		var errorMsg = "Sorry, we encounter some problem please have a try later :p";
		var successMsg = "Successfully uploaded";
		var uploadingMsg="Uploading...";
		
		//uploading
		button.setAttribute("class", "btn btn-block btn-large btn-primary");
		button.innerHTML = uploadingMsg;
		
		var callback= function(data) {
			var result = jQuery.parseJSON(data);
			console.log(result);
			spinner.stop();
			if (result['result'] == true) {
			
				button.setAttribute("class", "btn btn-block btn-large btn-success");
				button.innerHTML = successMsg;
			} else {
				button.innerHTML = errorMsg;
			}
		};
		
		if($('#canvasPeople').css("display")!="none"){//if uploading people stats
			var x=80,y=20;//top profile position:hardcode!
			var tag_id=<?php echo "'".$tag_id."'"?>;
			var dataURL = document.getElementById("canvasPeople").toDataURL('image/png');
			var jqxhr = $.post("../member/ajax_post_photo_people", {
			'dataURL' : dataURL,
			'x':x,
			'y':y,
			'tag_id':tag_id
		}, callback);
		}
		else{//if uploading place stats
			var dataURL = document.getElementById("canvasPlace").toDataURL('image/png');
			var jqxhr = $.post("../member/ajax_post_photo_place", {
			'dataURL' : dataURL
		}, callback);
		}
		
		jqxhr.error(function() {
			button.innerHTML = errorMsg;
		});
	}
</script>     


<script type='text/javascript'>
		// Grayscale w canvas method
	function grayscale(src) {
		var canvas = document.createElement('canvas');
		var ctx = canvas.getContext('2d');
		var imgObj = new Image();
		imgObj.src = src;
		canvas.width = imgObj.width;
		canvas.height = imgObj.height;
		ctx.drawImage(imgObj, 0, 0);
		var imgPixels = ctx.getImageData(0, 0, canvas.width, canvas.height);
		for (var y = 0; y < imgPixels.height; y++) {
			for (var x = 0; x < imgPixels.width; x++) {
				var i = (y * 4) * imgPixels.width + x * 4;
				var avg = (imgPixels.data[i] + imgPixels.data[i + 1] + imgPixels.data[i + 2]) / 3;
				imgPixels.data[i] = avg;
				imgPixels.data[i + 1] = avg;
				imgPixels.data[i + 2] = avg;
			}
		}
		ctx.putImageData(imgPixels, 0, 0, 0, 0, imgPixels.width, imgPixels.height);
		return canvas.toDataURL();
	}

	var canvasId='canvasPeople';
	var count = 0;
	var img_srcs = [];
	var top_friends = jQuery.parseJSON(<?php echo "'".$top_friends."'"?>);
	var background_src = "<?php echo ASSEST_URL?>img/stats/stat_people_background2.png";
	var crown_src = "<?php echo ASSEST_URL?>img/stats/crown.png";
	var stat_friend_data=prepareData(top_friends);
	
	if(stat_friend_data[0].length<5){// check if their are enough data for drawing
		$(document).ready(function(){drawEmptyCanvas(background_src,canvasId);});
	}else{
		var preparedLinks=preparePicURL(stat_friend_data[0]);
		convertLinks(preparedLinks,'drawGraph');
		var callback = function() {			
		};
	}

</script>


<script>	
$(window).load(function() {

	
		
	//==================
	var top_places = <?php echo $top_places ?>;
	var stat_place_data = preparePlaceData(top_places);
	var percent = stat_place_data[0];
	var hasBeen = stat_place_data[2];
	var cities = stat_place_data[1][0];
	var countries = stat_place_data[1][1];
	var crownSrc="<?php echo ASSEST_URL?>img/stats/crown.png";
    var starSrc="<?php echo ASSEST_URL?>img/stats/star.png";
	var halfStarSrc="<?php echo ASSEST_URL?>img/stats/star_half.png";
	var place_bg_src="<?php echo ASSEST_URL?>img/stats/place.png";
	
	show("canvasPlace",percent,hasBeen,cities,countries,crownSrc,starSrc,halfStarSrc,place_bg_src);
	crown = new Image();
	crown.onload = function() {
		var ctx = document.getElementById("canvasPlace").getContext("2d");
		ctx.drawImage(this, 25, 75,40,40);
	};
	crown.src = crownSrc;
	// Fade in images so there isn't a color "pop" document load and then on window load
	$(".icon ").fadeIn(500);

	// clone image
	$('.icon ').each(function() {
		var el = $(this);
		el.css({
			"position" : "absolute"
		}).wrap("<div class='img_wrapper' style='display: inline-block'>").clone().addClass('img_grayscale').css({
			"position" : "absolute",
			"z-index" : "998",
			"opacity" : "0"
		}).insertBefore(el).queue(function() {
			var el = $(this);
			el.parent().css({
				"width" : this.width,
				"height" : this.height
			});
			el.dequeue();
		});
		this.src = grayscale(this.src);
	});

	// Fade image
	$('.icon ').mouseover(function() {
		$(this).parent().find('img:first').stop().animate({
			opacity : 1
		}, 500);
	})
	$('.img_grayscale').mouseout(function() {
		$(this).stop().animate({
			opacity : 0
		}, 1000);
	});

	$(".icon").click(function() {
	
		$(".img_wrapper").css({
			"display" : "none"
		});
	
		$(this).fadeTo("normal", 0);
		$(".icon").fadeTo("slow", 0);
		$(".icon").css({
			"display" : "none"
		});
		/*$("#canvasId").css({
			"display" : "inline"
		});*/
		$("#arrow").css({
			"display" : "block"
		});
		$("#button").css({
			"display" : "inline"
		});
		button.setAttribute("class", "btn btn-block btn-large btn-primary");
		$("#button").text("Share with friends on Facebook");
		$("#button").fadeTo("slow", 1);
		//$("#canvasId").fadeTo("slow", 1);
		$("#arrow").fadeTo("slow", 0.7);
	});

	$("#people").click(function(){
		$("#canvasPeople").css({
			"display" : "inline"
		});
		$("#canvasPeople").fadeTo("slow", 1);
	});
	
	$("#place").click(function(){
		$("#canvasPlace").css({
			"display" : "inline"
		});
		$("#canvasPlace").fadeTo("slow", 1);
	});
	$("#arrow").mouseover(
		function () {
			$(this).fadeTo("fast", 1);
	}); 
	$("#arrow").mouseout(
		function () {
			if($('.icon').css("display")=="none"){
			$(this).fadeTo("fast", 0.7);}
	});
	$("#arrow").click(function() {
		$(".img_wrapper").css({
			"display" : "inline-block"
		});
		$("#canvasPeople").fadeTo("slow", 0);
		$("#canvasPlace").fadeTo("slow", 0);
		$("#button").fadeTo("slow", 0);
		$("#button").css({
			"display" : "none"
		});
		$("#canvasPeople").css({
			"display" : "none"
		});
		$("#canvasPlace").css({
			"display" : "none"
		});
		$("#arrow").css({
			"display" : "none"
		});
		$(".icon").css({
			"display" : "inline"
		});
		$(".icon").fadeTo("slow", 1);
	});
});
;
</script>
<div class="container" id="canvasdiv" >
	<div class="row">
		<div class="span6"><img class="icon" id ="people" src="<?php echo ASSEST_URL?>img/stats/people_icon.png"/>
		</div>
		<div class="span6"><img class="icon"  id = "place" src="<?php echo ASSEST_URL?>img/stats/place_icon.png"/>
		</div>
	</div>
			<div id="test" style="position:absolute; left: 45%; top: 40%;">
			</div>
	<div class="row ">
		<div class="span1">
				<img id="arrow" style="display:none" src="<?php echo ASSEST_URL?>img/stats/arrow.png"width="50px" height="50px" ></i>
		</div>

		<div class="span8 ">
			<canvas class="canvas" id="canvasPeople" style="display:none;opacity:0" width="700px" height="462px" >
				Sorry we could not display the content on IE
			</canvas>
			<canvas class="canvas" id="canvasPlace" style="display:none;opacity:0" width="707px" height="467px" >
				Sorry we could not display the content on IE
			</canvas>
			<div class="row">
				<div class="span2">
						<button class="btn btn-block btn-large btn-primary "  id="button" onClick="post(this)" style="display:none;width:700px">
							Share with friends on Facebook
						</button>
				</div>
			</div>
		</div>
	</div>
</div>


<?php if ($accuracy < 100) {?>
<p align="center" style="margin-top:40px">
The accuracy in statistics is <?php echo $accuracy;?>%.<br/>
If you want to have more precise results, you may need to wait for our system to collect your friend data. We don't have data of <?php echo count($need_cache_friends)?> friends.<br/>
<button class="btn" onClick="cache()" id="cache_more_button">Get them now!</button>
<p id="cache_message" style="display:none" align="center">Loading....</p>
</p>

<? } ?>

<script language="javascript">

	//cache
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

	function cache() {
		
		if (current >= no_friend) {
			$("#cache_message").html("Finish loading! Please refresh the page");
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
					$("#cache_message").html("Loading process ... Finish "+total_cache+" friends!");
					cache();
				}
			}
		});
	 }
</script>

<!--<img  id="stat-place" class="stat-img" height="200px" width="200px" src="<?php echo ASSEST_URL?>img/stats/top_place_before.png" alt="Your Next Travel Destination" />
<img  class="stat-img" height="200px" width="200px" src="<?php echo ASSEST_URL?>img/stats/top_people_before.png" alt="Travel Nuts" />

<?php $this->load->view('footer.php'); ?>