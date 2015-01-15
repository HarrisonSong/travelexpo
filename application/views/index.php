<!DOCTYPE html>
<html lang="en">
<head>
    <link href='http://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300,700' rel='stylesheet' type='text/css'>
    <meta charset="utf-8">
    <title>TravelExpo - Explore travel destinations with your friends on Facebook</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="og:description" content="Finding your next travel destination through your friends travel photos on Facebook.">
    <meta name="author" content="">
<meta property="og:title" content="Explore your friends&#039; trips on TravelExpo" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo site_url();?>" />
<meta property="og:image" content="<?php echo ASSEST_URL?>img/logo128.png" />
<meta property="og:site_name" content="TravelExpo" />
<meta property="fb:app_id" content="404341769625504" />
    <!-- Le styles -->
    <script type="text/javascript" src="<?php echo ASSEST_URL?>js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="<?php echo ASSEST_URL?>js/jquery.flexslider-min.js"></script>
    <link rel="stylesheet" href="<?php echo ASSEST_URL?>css/style_index.css">
    <link rel="stylesheet" href="<?php echo ASSEST_URL?>css/bootstrap.css">
    <link rel="stylesheet" href="<?php echo ASSEST_URL?>css/flexslider.css">

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
  <body>
    <div class="wrapper homepage">
       <div class="content">

            <h1 class="logo">TravelExpo</h1>
            <h2 class="tagline">Explore your friends' trips</h2>
            <h3 class="tagline">Finding your next travel destination through your friends' travel photos on Facebook</h3>
            <p><a href="<?php echo site_url("general/login")?>" class="btn btn-primary btn-large">Login in using Facebook</a></p>            


        <div class="slide-wrap">
            <div class="flexslider">
            <ul class="slides">
                <li>
                  <img src="<?php echo ASSEST_URL?>img/screen/photo1.jpg" />
              </li>
              <li>
                  <img src="<?php echo ASSEST_URL?>img/screen/photo2.jpg" />
              </li>
              <li>
                  <img src="<?php echo ASSEST_URL?>img/screen/photo3.jpg" />
              </li>
          </ul>
      </div> 
        </div>

  </div>   
</div>
<div class="footer">
    <span class="at">© 2012 TravelExpo</span>
<ul class="footer-links floats">
    <li><a href="#">Privacy Policy</a></li>
    <li><a href="#">Terms of Service</a></li>
      <li><a href="#">User Support</a></li>
    <li><a href="#">About</a></li>
    <li><a href="#">Contact</a></li>
</ul>
</div>
<script type="text/javascript">
$(window).load(function() {
  $('.flexslider').flexslider({
    animation: "slide"
});
});

</script>

</body>
</html>