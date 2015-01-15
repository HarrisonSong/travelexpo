<?php $this->load->view('header.php');?>
 <div class="wrapper">
<?php $this->load->view('navbar.php');?>
<div class="main-content">
<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
  FB.init({
    appId  : '<?php echo $fb_appId;?>',
    status : true, // check login status
    cookie : true, // enable cookies to allow the server to access the session
    xfbml  : true  // parse XFBML
  });
  function invite()
  {
	 FB.ui({method: 'apprequests', message: 'TRAVEL EXPO - Finding your next travel destination through your friends travel photos on Facebook..', data: 'tracking information for the user'}); 
  }
</script>
<div><span>Friends who have used </span><span style = "font-family: 'Lobster', cursive"> TravelExpo:</span></div><br/>
<?php 
	$count = 0;
	$Url= site_url("member/person");
	echo "<div class ='row-fluid'>"; 
	foreach($friends as $friend){
		$count++;
		echo "<a class = 'invite_list span1' href = '".$Url."/".$friend['fb_id']."/'><img src='https://graph.facebook.com/".$friend['fb_id']."/picture?type=square'/></a><p class = 'invite_list span1'>".$friend['name']."</p>";
		if($count%6 == 0){
		echo "</div><br/><div class ='row-fluid'>";
		}
	} 
	echo "</div>";?>
<button class = "btn btn-primary invite_friends" onclick="invite()">INVITE your Friends</button>
<?php $this->load->view('footer.php');?>