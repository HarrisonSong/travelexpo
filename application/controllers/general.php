<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// Your own constructor code			
		$this->load->helper('url');
		$this->load->database();
		$this->load->library('facebook/facebook');
		$this->load->helper('facebook');
		
		define('ASSEST_URL', base_url().'assets/');						
	}


	/**
	 * Index Page for this controller.
	 */
	public function index() {
		$output["title"] = 'Explore travel destinations from your friends\' photos!';
		$this->load->view('index.php', $output);	
	}


	/**
	 * Map Page for this controller.
	 */
	public function map() {
		$output["title"] = 'Map';
		$this->load->view('map.php', $output);	
	}
	
	/**
	 * Map Page for this controller.
	 */
	public function map_iframe() {
		$this->load->view('map_iframe.php');	
	}
	
	/**
	 * Stats Page for this controller.
	 */
	public function stats() {
		$output["title"] = 'Stats';
		$this->load->view('stats.php', $output);	
	}


	/**
	 * Friends Page for this controller.
	 */
	public function friends() {
		$output["title"] = 'Friends';
		$this->load->view('friends.php', $output);	
	}
	
	
	/**
	 * Trip Page for this controller.
	 */
	public function trip() {
		$output["title"] = 'Trips';
		$this->load->view('trip.php', $output);	
	}
	
	

	/**
	 * Index Page 2 for this controller.
	 */
	public function index2() {
		
		
		$facebook = fb_object(site_url("general/index"));	
		if (!is_string($facebook)) {
			
			//SELECT name,description,geometry,latitude,longitude,checkin_count,display_subtext FROM place WHERE page_id IN( SELECT page_id FROM location_post WHERE author_uid IN (SELECT uid2 FROM friend WHERE uid1=me()) AND timestamp > 1331143200)
			
			// Friend location & hometown
			$params = array(
				'method' => 'fql.query',
				'query' => " SELECT uid, name, hometown_location, current_location FROM user WHERE uid IN ( SELECT uid2 FROM friend WHERE uid1 = me() )"
			);
			$result = $facebook->api($params);
			$i = 1;
			foreach($result as $row) {
				echo "<p>".$i."/ ".$row['uid'].": ".$row['name']."<br/>Home town: ".$row['hometown_location']['city']." - ".$row['hometown_location']['country']."</br>Current location: ".$row['current_location']."</p>";
				$i++;
			}
			
			
			/*
			// multiple query
			try {
			$fql_friends_fan_pages = array(
				'query1' => "SELECT page_id, type FROM location_post WHERE author_uid IN (SELECT uid2 FROM friend WHERE uid1=me())", 
				'query2' => "SELECT name,description,geometry,latitude,longitude,checkin_count,display_subtext FROM place WHERE page_id IN (SELECT page_id FROM #query1)"
			);
			
			$params_friends_fan_pages = array(
				'method' => 'fql.multiquery',
				'queries' => $fql_friends_fan_pages
			);
			
			$friends_fan_pages = $facebook->api($params_friends_fan_pages);
				
				print_r($friends_fan_pages);
			} catch ( FacebookApiException $e ) {
				print_r( $e);
				
			}
			*/
		
		} else {
			redirect($facebook);	
		}
		
	}
	
	public function location() {
		$facebook = fb_object(site_url("general/index"));	
		if (!is_string($facebook)) {
			
			try {
				$fql_location = array(
					'query1' => "SELECT id, author_uid, page_id, type FROM location_post WHERE author_uid IN (SELECT uid2 FROM friend WHERE uid1=me()) AND type ='photo' LIMIT 400, 1000", 
					'query2' => "SELECT name,description,geometry,latitude,longitude,checkin_count,display_subtext,page_id FROM place WHERE page_id IN (SELECT page_id FROM #query1)",
					'query3' => "SELECT object_id, src_small, src_big, created, place_id FROM photo WHERE object_id IN (SELECT id FROM #query1 where type='photo')"
				);
				
				$params_location = array(
					'method' => 'fql.multiquery',
					'queries' => $fql_location
				);
				
				$result = $facebook->api($params_location);
				
				
				
				$location_data = array();		
				$post = $result[1]['fql_result_set'];
				foreach($post as $row) {
					$location_data[$row['page_id']] = $row;
				}
				
				$photo_data = array();		
				$post = $result[2]['fql_result_set'];
				$i = 1;
				foreach($post as $row) {
					//$photo_data[$row['object_id']] = $row;
					echo $i.'/ '.$location_data[$row['place_id']]['name'].' - '.$row['created'].'<br/><img src="'.$row['src_small'].'"/>';
					
					$i++;
				}
				
			} catch ( FacebookApiException $e ) {
				print_r($e);		
			}
		} else {
			redirect($facebook);	
		}
	}
	
	public function friend_info() {
		$facebook = fb_object(site_url("general/friend_info"));
		if (!is_string($facebook)) {
			// Friend location & hometown
			$params = array(
				'method' => 'fql.query',
				'query' => "SELECT uid, name, hometown_location, current_location FROM user WHERE uid IN ( SELECT uid2 FROM friend WHERE uid1 = me() )"
			);
			$result = $facebook->api($params);
			$i = 1;
			foreach($result as $row) {
				echo "<p>".$i."/ <a href='".site_url("general/friend_details/".$row['uid'])."' target='_details'>".$row['uid']."</a>: ".$row['name']."</p>";
				$i++;
			}
		} else {
			redirect($facebook);	
		}
	}
	
	public function friend_details() {
		
		$uid = $this->uri->segment(3);
		$facebook = fb_object(site_url("general/friend_details/".$uid));
		
		 try {
			$fql_location = array(
				'query1' => "SELECT uid, name, hometown_location, current_location FROM user WHERE uid = '".$uid."'", 
				'query2' => "SELECT id, page_id, type, coords FROM location_post WHERE (author_uid = '".$uid."' OR '".$uid."' IN tagged_uids) LIMIT 0, 400", 
				'query3' => "SELECT object_id, src_small, src_big, created, place_id FROM photo WHERE object_id IN (SELECT id FROM #query2 where type='photo')",
				'query4' => "SELECT page_id,name,description,geometry,latitude,longitude,checkin_count,display_subtext,page_id FROM place WHERE page_id IN (SELECT page_id FROM #query2)",
			);
				
			$params_location = array(
				'method' => 'fql.multiquery',
				'queries' => $fql_location
			);
				
			$result = $facebook->api($params_location);
				
			echo "<p><b>User information details:</b><br/>";
			echo "Home town: ".$result[0]['fql_result_set'][0]['hometown_location']['name']."(id: )".$result[0]['fql_result_set'][0]['hometown_location']['id']."<br/>";
			echo "Current Location: ".$result[0]['fql_result_set'][0]['current_location']['name']."(id: )".$result[0]['fql_result_set'][0]['current_location']['id']."<p>";
				
			echo "<p>Total place visit: ".count($result[3]['fql_result_set'])."<br/>";
			echo "<b>Details</b><br/>";
			$i = 1;
			foreach($result[3]['fql_result_set'] as $row) {
				echo $i."/ id: <a href='".site_url("general/expert_place/".$row['page_id'])."' target='_location'>".$row['page_id']."<a/><br/>"; 
				echo " name: ".$row['name']."<br/>";
				echo " location: ".$row['latitude'].", ".$row['longitude']."<br/>";
				echo " checkin_count: ".$row['checkin_count']."<br/>";
				$i++;
			}
			echo "</p>";
			
			// object data
			$photos = array();
			foreach($result[2]['fql_result_set'] as $row) {
				$photos[$row['object_id']] = $row;
			}
				
			/*
			echo "<p>Details of post<br/>";
			$i = 1;
			foreach($result[1]['fql_result_set'] as $row) {
				echo $i."/ id: ".$row['id']."<br/>";
				echo " type: ".$row['type']."<br/>";
				echo " coords: ".$row['coords']['latitude'].",".$row['coords']['longitude']."<br/>";
				
				if ($row['type'] == 'photo') {
					echo "<img src='".$photos[$row['id']]['src_small']."'/>";
				}
				$i++;
			}
			echo "</p>";
			*/
		 }  catch ( FacebookApiException $e ) {
			print_r($e);		
		}
	}
	
	public function expert_place() {
		$place_id = $this->uri->segment(3);
		$facebook = fb_object(site_url("general/expert_place/".$place_id));
		 try {
			$fql_location = array(
				'query1' => "SELECT name,description,geometry,latitude,longitude,checkin_count,display_subtext,page_id FROM place WHERE page_id = '".$place_id."'", 
				'query2' => "SELECT uid2 FROM friend WHERE uid1 = me()",
				'query3' => "SELECT id, page_id, type, coords, tagged_uids, author_uid FROM location_post WHERE page_id = '".$place_id."' AND type = 'photo' AND (tagged_uids IN (SELECT uid2 FROM #query2) OR author_uid IN (SELECT uid2 FROM #query2)) LIMIT 0, 400",
			);
			$params_location = array(
				'method' => 'fql.multiquery',
				'queries' => $fql_location
			);
			$result = $facebook->api($params_location);
			
			$data = array();
			foreach($result[2]['fql_result_set'] as $row) {
				$data[$row['author_uid']][] = $row;
				foreach($row['tagged_uids'] as $tag) {
					$data[$tag][] = $row;
				}
			}
			
			$friend = array();
			foreach($result[1]['fql_result_set'] as $row) {
				$friend[$row['uid2']] = count($data[$row['uid2']]);
			}
			arsort($friend);
			
			print_r($friend);
		 } catch ( FacebookApiException $e ) {
			print_r($e);		
		}
	}
	
	public function most_place() {
		$facebook = fb_object(site_url("general/most_place"));
		 try {
			$fql_location = array(
				'query1' => "SELECT uid2 FROM friend WHERE uid1 = me()",
				'query2' => "SELECT page_id, name FROM place WHERE page_id IN (SELECT page_id FROM location_post WHERE tagged_uids IN (SELECT uid2 FROM #query1) OR author_uid IN (SELECT uid2 FROM #query1))"
			);
			$params_location = array(
				'method' => 'fql.multiquery',
				'queries' => $fql_location
			);
			$result = $facebook->api($params_location);
			
			print_r($result[1]);
		 } catch ( FacebookApiException $e ) {
			print_r($e);		
		}
	}
	
	
	public function logout() {
		$facebook = fb_object(site_url("general/index"));
		$logoutUrl = $facebook->getLogoutUrl(array( 'next' => site_url("general/index")));
		setcookie('fbs_'.$facebook->getAppId(), '', time()-100, '/', 'domain.com');
		session_destroy();
		redirect($logoutUrl);
	}
	
}
