<?php
function fb_object($link_back) {
	$facebook = new Facebook(array(
		'appId'  => '452885334756918',
		'secret' => 'bb82496d1b09afa476c103228572e449',
	));
	
	
	// Get User ID
	$user = $facebook->getUser();
	
	// We may or may not have this data based on whether the user is logged in.
	//
	// If we have a $user id here, it means we know the user is logged into
	// Facebook, but we don't know if the access token is valid. An access
	// token is invalid if the user logged out of Facebook.
	
	if ($user) {
	  try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
	  } catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	  }
	}
	
	// Login or logout url will be needed depending on current user state.
	if ($user) {
	  return $facebook;		
	} else {
	  return $facebook->getLoginUrl(array('scope' => 'publish_actions, user_photos, friends_photos, photo_upload', 'redirect_uri' => $link_back));	
	}
		
			
}

function parse_signed_request($signed_request, $secret) {
	list($encoded_sig, $payload) = explode('.', $signed_request, 2);
 
	// decode the data
	$sig = base64_url_decode($encoded_sig);
	$data = json_decode(base64_url_decode($payload), true);
 	
	if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
    	error_log('Unknown algorithm. Expected HMAC-SHA256');
    	return null;
	}
 
	// check sig
	$expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	if ($sig !== $expected_sig) {
    	error_log('Bad Signed JSON signature!');
    	return null;
	}
 
	return $data;
}
 
function base64_url_decode($input) {
	return base64_decode(strtr($input, '-_', '+/'));
}

?>