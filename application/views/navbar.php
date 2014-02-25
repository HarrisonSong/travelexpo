<div class='navbar'>
	<div class='navbar-inner'>
		<div class='container'>
			<!-- .btn-navbar is used as the toggle for collapsed navbar content -->
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>

			<!-- Be sure to leave the brand out there if you want it shown -->
			<a class="brand newfont insetType" href="#">TravelExpo</a>
			<!-- Everything you want hidden at 940px or less, place within here -->
			<div class="nav-collapse">
				<ul class="nav">
					<li class="divider-vertical nomargin"></li>
					<li <?php if($title=='Map') echo 'class="active"';?>>
						<a href="<?php echo site_url("general/map")?>" class="nav-icon-button map" data-placement="bottom" title="Map">Map</a>
					</li>
					<li class="divider-vertical nomargin"></li>
					<li <?php if($title=='Friends') echo 'class="active"';?>><a href="<?php echo site_url("general/friends")?>" data-placement="bottom" class="nav-icon-button friends" title="Friends">Friends</a></li>
					<li class="divider-vertical nomargin"></li>
					<li <?php if($title=='Trips') echo 'class="active"';?>><a href="<?php echo site_url("general/trip")?>" data-placement="bottom" class="nav-icon-button trips" title="Trips">Trips</a></li>
					<li class="divider-vertical nomargin"></li>
					<li <?php if($title=='Stats') echo 'class="active"';?>><a href="<?php echo site_url("general/stats")?>" data-placement="bottom" class="nav-icon-button stats" title="Statistics">Stats</a></li>
					<li class="divider-vertical nomargin"></li>
				</ul>

				<ul class="nav pull-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Username <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="#">Settings</a></li>
							<li class="divider"></li>
							<li><a href="#">Logout</a></li>
						</ul>
					</li>
				</ul>
				<form class="navbar-form pull-right">
					<input type="text" class="search-query span2" placeholder="Search Friends">
				</form>
			</div>
		</div>
	</div>
</div>
<div class="main-content">