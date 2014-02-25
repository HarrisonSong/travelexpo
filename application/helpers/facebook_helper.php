<?php
function fb_object($link_back) {
	$facebook = new Facebook(array(
		'appId'  => '363647320372203',
		'secret' => '8368c6fdb4bacb0c7c2898cc49d98ae0',
	));
		
	$uid = $facebook->getUser();
		
	// login or logout url will be needed depending on current user state.		
	if(!$uid) {
		return $facebook->getLoginUrl(array('scope' => 'publish_stream, user_location, friends_location,user_hometown, friends_hometown, user_photos, friends_photos, user_status, friends_status, user_checkins, friends_checkins', 'redirect_uri' => $link_back));	
	}
		
	return $facebook;				
}

?>