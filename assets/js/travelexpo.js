jQuery(document).ready(function() {

	$('.slide-button').click(function(){
		if($(this).hasClass('active')){
			$('.friends-profile-wrap, .slide-button, .face-child-img').removeClass('active');
		}else{
			$('.friends-profile-wrap, .slide-button, .face-child-img').addClass('active');
		}
	});

			$('.face-child-img.map').tooltip({
						animation: false
				});

});


function photoEvent(){
	$('.photo-preview > a').mouseover(function(){
		var move_x, move_y;
		move_x=Math.floor(Math.random()*60)+20;
		move_y=Math.floor(Math.random()*60)+20;
   		$(this).css("background-position-x", move_x+'%').css("background-position-y",move_y+'%');

	}).mouseout(function(){
		var move_x, move_y;
		move_x=Math.floor(Math.random()*60)+20;
		move_y=Math.floor(Math.random()*60)+20;
   		$(this).css("background-position-x", move_x+'%').css("background-position-y",move_y+'%');
	});
}