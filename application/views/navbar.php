<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=404341769625504";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class='navbar navbar-inverse'>
	<div class='navbar-inner'>
		<div class='container'>
			<!-- .btn-navbar is used as the toggle for collapsed navbar content -->
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>

			<!-- Be sure to leave the brand out there if you want it shown -->
			<a class="brand newfont insetType" href="<?php echo site_url("member/map")?>">TravelExpo</a>
			<!-- Everything you want hidden at 940px or less, place within here -->
			<div class="nav-collapse">
				<ul class="nav">
					<li class="divider-vertical nomargin"></li>
					<li <?php if($title=='Map') echo 'class="active"';?>>
						<a href="<?php echo site_url("member/map")?>" class="nav-icon-button map">Map</a>
					</li>
					<li class="divider-vertical nomargin"></li>
					<li <?php if($title=='Friends') echo 'class="active"';?>><a href="<?php echo site_url("member/friends")?>" class="nav-icon-button friends" >Friends</a></li>
					<li class="divider-vertical nomargin"></li>
					<li <?php if($title=='Stats') echo 'class="active"';?>><a href="<?php echo site_url("member/stats")?>" class="nav-icon-button stats">Statistics</a></li>
					<li class="divider-vertical nomargin"></li>
					<li <?php if($title=='Invite') echo 'class="active"';?>><a href="<?php echo site_url("member/invite")?>" class="nav-icon-button invite" title="">Invite</a></li>
					<li class="divider-vertical nomargin"></li>
					<li><div class="fb-like" data-href="http://drekee.com/" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false" data-font="segoe ui" ></div></li>
					<li><div class="google-plus"><div class="g-plusone" data-size="medium" data-href="http://drekee.com/"></div>
</div><!-- Place this tag where you want the +1 button to render. -->


<!-- Place this tag after the last +1 button tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script></li>
				</ul>

				<ul class="nav pull-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $this->session->userdata('username') ?><b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo site_url("member/person") . '/' . $this->session->userdata['fb_id']?>">My Trips</a></li>
							<li><a href="<?php echo site_url("member/setting")?>">Settings</a></li>
							<li class="divider"></li>
							<li><a href="<?php echo site_url("general/logout")?>">Logout</a></li>
						</ul>
					</li>
				</ul>
				<form class="navbar-form pull-right">
					<input type="text" id="search_box" class="search-query span2" placeholder="Search Friends" >
				</form>
			</div>
		</div>
	</div>
</div>
