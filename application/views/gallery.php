<!DOCTYPE html>
<html lang="en">
<head>
    <link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
    <meta charset="utf-8">
    <title><?php echo $title . ' - TravelExpo'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
<meta property="og:title" content="Explore your friends&#039; trips on TravelExpo" />
<meta property="og:type" content="website" />
<meta property="og:url" content="http://ec2-175-41-163-41.ap-southeast-1.compute.amazonaws.com" />
<meta property="og:image" content="http://ec2-175-41-163-41.ap-southeast-1.compute.amazonaws.com/assets/img/logo128.png" />
<meta property="og:site_name" content="TravelExpo" />
<meta property="fb:app_id" content="404341769625504" />
    <!-- Le styles -->
    <link rel="stylesheet" href="<?php echo ASSEST_URL?>css/style.css">
    <link href="<?php echo ASSEST_URL?>css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSEST_URL?>css/jquery.mCustomScrollbar.css">
    <link href="<?php echo ASSEST_URL?>css/bootstrap-responsive.css" rel="stylesheet">
    <script type="text/javascript" src="<?php echo ASSEST_URL?>js/jquery-1.8.0.min.js"></script>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
      <![endif]-->

            <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-34501345-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
  </head>
  <body class="gallery" style="background: url(<?php echo $data[0]['src_big'] ?>) no-repeat center center;" >


      <div class="wrapper">
        <?php $this->load->view('navbar.php');?>
<div class="main-content">
        <div class="container-fluid">
            <div class="row-fluid" >
                <div class="span12">
                     <div class="span1">
                    <a class="back" href=" <?php echo site_url("member/person") . '/' . $uid .'/' . rawurlencode($title) ?> "><h1 class="gallery">&lt;</h1></a>
                </div>
                <div class="span11">
                    <a class="title" href=" <?php echo site_url("member/person") . '/' . $uid .'/' . rawurlencode($title) ?> "><h1 class="gallery"> <?php echo rawurldecode($title) ?> </h1></a>
                </div>
                </div>
               
            </div>
            <div class="row-fluid photos">
                <div class="image-control span7">
                  <!--Body content-->
                    <div class="image-holder">
                    <img src=" <?php echo $data[0]['src_big'] ?> " alt="">
                    <h4 id="caption"><?php echo $data[0]['caption'] ?><br><?php echo $data[0]['created'] ?> </h4>
                </div>
                  <div class="left button-holder">
                    <a href="#" title="Previous photo" class="previous-button button">Previous</a>
                    <a href="#" title="I want to go there!" class="want-to-go button">Want to go</a>
                    <a href="<?php echo $data[0]['link'] ?> " title="Leave him/her a message!" target="_blank" class="comments button">Comments</a>
                </div>
                <div class="right button-holder">
                    <a href="#" title="Next photo" class="next-button button">Next</a>
                </div>

            </div>
            
            <div class="span4 des clearfix">
                <p id="place"></p>
                <p id="location"></p>
                <p id="friends"></p>
                <span id="profiles"></span>
                <p id="des-title">Description</p>
                <p id="description"></p>
                <p id="map-title">Local Map</p>
                <div id="google-map"></div>
            </div>
            <div class="span1">
            </div>
        </div>
    </div>
    <div class="comment-box">

    </div>
    <script type="text/javascript">
    $('#caption').hide();
    if($('#caption').html()!='<br>'){
        $('#caption').show();
    }
    var photos = new Array();
    <?php 
    $i = 0;
    foreach($data as $photo) {
        echo 'photos['.$i.'] = new Array("'.$photo['src_big'] .'","'.$photo['link'].'","'.str_replace('<br />','<br />\\',nl2br(htmlentities($photo['caption']))).'","'. $photo['created'] .'");
         ';
        $i++;
    }
    ?>
    jQuery(document).ready(function() {
        
        $.ajax({
            url: "<?php echo site_url("member/ajax_post_activity_friendplace/".$uid."/".$pid)?>",
            dataType: "json",
            success: function(data){
            }
        });
        $.ajax({
            url: "<?php echo site_url('jjp/like') . '?action=islike&place='.$pid?>",
            dataType: "json",
            context: this,
            success:function(output){
                if(output.success==false){
                    return 0;
                }
                if(output.is_liked==true){
                    $(".want-to-go").addClass('liked');
                }
                if(output.like_count==0){
                    $(".want-to-go").attr('data-original-title', 'I want to go there!');
                }else{
                    $(".want-to-go").attr('data-original-title', output.like_count+" friends want to go there");
                }
                
            }
        });
        
        $('.want-to-go').click(function(){
                var location_id = $(this).parent().find('.photo-preview').attr('data-location');
                if($(this).hasClass('liked')){
                    $(this).removeClass('liked');
                    $.ajax({
                        url: "<?php echo site_url('jjp/like') . '?action=dislike&place='.$pid?>",
                        dataType:'json',
                        context:this,
                        success:function(output){
                            if(output.success==false){
                                return 0;
                            }

                            if(output.like_count==0){
                    $(".want-to-go").attr('data-original-title', 'I want to go there!');
                }else{
                    $(".want-to-go").attr('data-original-title', output.like_count+" friends want to go there");
                }
                        }
                    });
                }else{
                    $(this).addClass('liked');
                    $.ajax({
                        url: "<?php echo site_url('jjp/like') . '?action=like&place='.$pid?>",
                        dataType:'json',
                        context:this,
                        success:function(output){
                            if(output.success==false){
                                return 0;
                            }

                            if(output.like_count==0){
                    $(".want-to-go").attr('data-original-title', 'I want to go there!');
                }else{
                    $(".want-to-go").attr('data-original-title', output.like_count+" friends want to go there");
                }
                        }
                    });
                }
            });
        
        
        $('.des').children().hide();
        $('#place').show();
        $('.left>a').tooltip();
        $('.right>a').tooltip();
        var num_photo = <?php echo sizeof($data); ?>;
        var current = 0;
        console.log(num_photo);
        $('.previous-button').hide();
        if(num_photo==1){
            $('.next-button').hide();
        }

        var images = new Array()
        function preload() {
            for (i = 0; i < num_photo; i++) {
                images[i] = new Image();
                images[i].src = photos[i];
            }
        }

        preload();

        $('.previous-button').click(function(){
            current--;
            var current_img = photos[current];
            if(current==0)
                $('.previous-button').hide();

            $('.image-holder > img').replaceWith('<img src="'+ photos[current][0] +'" alt="">');
            if(photos[current][2]!=""){
                $('#caption').html(unescape(photos[current][2])+'<br>'+photos[current][3]).fadeIn(500);
            }
            $('.comments').attr("href", photos[current][1]);
            $('.next-button').show();

        });

        $('.next-button').click(function(){
            current++;
            var current_img = photos[current];
            if(photos[current]!=null){
                if(current==num_photo-1)
                    $('.next-button').hide();

                $('.image-holder > img').replaceWith('<img src="'+ photos[current] +'" alt="">');
            if(photos[current][2]!=""){
                $('#caption').html(photos[current][2]+'<br>'+photos[current][3]).fadeIn(500);
            }
                $('.comments').attr("href", photos[current][1]);
                $('.previous-button').show();

            }
        });



        $('.image-holder').click(function(){
            current++;
            if(current==num_photo)
                current=0;

            var current_img = photos[current];

            if(photos[current]!=null){
                $('.previous-button').show();
                 $('.next-button').show();
                if(current==num_photo-1)
                    $('.next-button').hide();
                if(current==0)
                    $('.previous-button').hide();

                $('.image-holder > img').replaceWith('<img src="'+ photos[current] +'" alt="">');
            if(photos[current][2]!=""){
                $('#caption').html(photos[current][2]+'<br>'+photos[current][3]).fadeIn(500);
            }
                $('.comments').attr("href", photos[current][1]);
            }
        });        

        jQuery.ajax({
            url: "<?php echo site_url('member/place_info') . '/' . $pid?>",
            dataType: "json",
            success: function(location_info){
                console.log(location_info);
                if(location_info.error==0){
                    $('#place').html(location_info.info.name);
                    if(location_info.data.no_friends!="0"){
                        $('#friends').html(location_info.data.no_friends + " friends have been here");
                        var count=0;
                        for(var id in location_info.data.list_friends){
                            if(count==20)
                                break;
                            $('#profiles').append('<a href="<?php echo site_url("member/person")?>/'+ location_info.data.list_friends[id] +'" target="_blank" title=""><img src="https://graph.facebook.com/'+ location_info.data.list_friends[id] +'/picture?type=square" title="">');
                            count++;
                        }
                        $('#friends , #profiles').fadeIn(500);
                    }else{
                        $('#friends').html('No other friends have been here');
                            $('#friends').fadeIn(500);
                    }
                       
                    if(location_info.info.city!="0"&&location_info.info.country!="0"&&(location_info.info.city+", "+location_info.info.country)!=location_info.info.name){
                        $('#location').html(location_info.info.city+", "+location_info.info.country);
                        $('#location').fadeIn(500);
                    }
                        
                    $('#des-title').fadeIn(500);
                    if(location_info.info.description!=""){
                        
                        $('#description').html(location_info.info.description).fadeIn(500);

                    }else{
                        $('#description').html("No description of the place yet... :(").fadeIn(500);
                    }

                    $('#google-map').html('<img style="width:100%" src="http://maps.googleapis.com/maps/api/staticmap?center='+ location_info.info.latitude+','+location_info.info.longitude+'&size=767x560&zoom=11&markers=size:mid|'+ location_info.info.latitude+','+location_info.info.longitude+'&sensor=false">');
                    $('#map-title').fadeIn(500);
                    $('#google-map').fadeIn('500');
                }else{
                    $('#google-map').hide();
                }
            }
        });
});

function I_want_to_go(place_id) {
    
}
</script>

<?php $this->load->view('footer.php');?>