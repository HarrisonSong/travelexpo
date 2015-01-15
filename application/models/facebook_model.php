<?php
class Facebook_model extends CI_Model {
	function __construct()
    {
        parent::__construct();
    }
	
	function location_post($facebook, $fb_id) {
		$output = array("error" => 0, "data" => ""); 
		try {
			$fql_location = array(
				'place_query' => "SELECT id, page_id, coords FROM location_post WHERE (author_uid = '".$fb_id."' OR '".$fb_id."' IN tagged_uids) AND type = 'photo' AND page_id !='' LIMIT 0, 2000", 
				'photo_query' => "SELECT place_id, object_id, src_big FROM photo WHERE object_id IN (SELECT id FROM #place_query) LIMIT 0, 1"
			);	
			$params_location = array(
				'method' => 'fql.multiquery',
				'queries' => $fql_location
			);
			$result = $facebook->api($params_location);
				
			$places = array();
					
			$place_query = $result[0]['fql_result_set'];
			$total_photos = count($place_query);
			foreach($place_query as $place_row) {
				$places[$place_row['page_id']]['photos'][$place_row['id']] = $place_row['id'];
				$places[$place_row['page_id']]['info']['latitude'] = $place_row['coords']['latitude'];
				$places[$place_row['page_id']]['info']['longitude'] = $place_row['coords']['longitude'];
			}
					
			if ($total_photos > 0) {
				$a_photo_src = $result[1]['fql_result_set'][0]['src_big'];
			} else {
				$a_photo_src = "";
			}
		}  catch ( FacebookApiException $e ) {
			$output['error'] = "FQL error";
		}
				
		//cache into database
		$this->db->delete('location_post', array('fb_id' => $fb_id));
		if ($total_photos > 0) {
			foreach($places as $key=>$place) {
				$this->db->insert('location_post', array('fb_id' => $fb_id, 'place_id' =>$key, 'object_ids' => implode(",", $place['photos']), 'no_photos' => count($place['photos'])));
						
				// insert place info
				$place_info_query = $this->db->get_where('place', array('place_id' => $key), 1);
				if ($place_info_query->num_rows() == 0) {
					$this->db->insert('place', array('place_id' => $key, 'latitude' =>$place['info']['latitude'], 'longitude' =>$place['info']['longitude']));
				}
			}
					
			//insert a photo info
			$this->db->where('place_id', $result[1]['fql_result_set'][0]['place_id']);
			$this->db->where('fb_id', $fb_id);
			$this->db->update('location_post', array('src' => $result[1]['fql_result_set'][0]['src_big'])); 
		} else {
			$this->db->insert('location_post', array('fb_id' => $fb_id, 'place_id' =>'', 'object_ids' =>'', 'no_photos' => 0));
		}
		
		$output['data'] = array('total_photos' => $total_photos, 'places' => $places, 'a_photo_src' => $a_photo_src);
		return $output;
	}
	
	function friend_place($facebook, $fb_id, $place_id) {
		$output = array("error" => 0, "data" => ""); 
		
		$friends_list = array();
		$this->db->select('fid AS id');
		$friend_query = $this->db->get_where('friend', array('fb_id' => $fb_id));
		foreach($friend_query->result_array() as $friend_row) {
			$friends_list[$friend_row['id']] = $friend_row['id'];
		};
		
		$params = array(
			'method' => 'fql.query',
			'query' => " SELECT tagged_uids, author_uid FROM location_post WHERE page_id = '" . $place_id. "' AND type = 'photo'  LIMIT 0, 2000"
		);
		$result_friends = $facebook->api($params);
		$friends_in_place = array();
		foreach ($result_friends as $photo_row) {
			if (isset($friends_list[$photo_row['author_uid']])) {
				$friends_in_place[$photo_row['author_uid']] = $photo_row['author_uid'];
			}
			foreach ($photo_row['tagged_uids'] as $tag) {
				if (isset($friends_list[$tag])) {
					$friends_in_place[$tag] = $tag;
				}
			}
		}
		
		//delete old records
		$this->db->delete('friend_place', array('fb_id' => $fb_id, 'place_id' => $place_id));
	
		//insert into database
		$this->db->insert('friend_place', array('fb_id' => $fb_id, 'place_id' => $place_id, 'friend_list' => implode(',', $friends_in_place), 'no_friends' => count($friends_in_place))); 	

		$output['data']['no_friends'] = count($friends_in_place);
		$output['data']['list_friends'] = $friends_in_place;
		return $output;
	}
	
	
	function update_place_info($facebook, $place_id) {
		$output = array("error" => 0, "data" => ""); 
		$params = array(
			'method' => 'fql.query',
			'query' => "SELECT name,description FROM place WHERE page_id='" . $output['place_id'] . "'"
		);
		$result = $facebook->api($params);
		
		$this->db->where('place_id', $place_id);
		$this->db->update('place', $result[0]);
		
		$output['data']['name'] = $result[0]['name'];
		$output['data']['description'] = $result[0]['description'];
		return $output;
	}
	
	function update_friend_list($fb_id, $friend_data) {
		//delete old records
		$this->db->delete('friend', array('fb_id' => $fb_id));
		
		$friend_sql = "INSERT INTO friend  (fb_id, fid, fname) VALUES";
		$friend_sql .= "('".$fb_id."', '".$friend_data[0]['id']."', '".mysql_real_escape_string($friend_data[0]['name'])."')";
		for($i = 1; $i < count($friend_data); $i++) {
			$friend_sql .= ", ('".$fb_id."', '".$friend_data[$i]['id']."', '".mysql_real_escape_string($friend_data[$i]['name'])."')";
		}
		$this->db->query($friend_sql);
	}
	
}

?>