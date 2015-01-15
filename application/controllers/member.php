<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * Member class: contains the pages which are only accessed by members.
 */
class Member extends CI_Controller {
	/**
	 * Object constructor
	 */
	public function __construct() {
		parent::__construct();

		$this -> load -> library('session');
		$this -> load -> library('email');
		$this -> load -> helper('file');
		$this -> load -> helper('url');
		$this -> load -> database();
		$this -> load -> library('facebook/facebook');
		$this -> load -> helper('facebook');
		$this -> load -> model('Facebook_model');
		$this -> load -> model('Place_model');

		define('ASSEST_URL', base_url() . 'assets/');

		// check permission
		if ($this -> session -> userdata('logged_in') == FALSE) {
			$this -> session -> set_userdata('redirect_link', current_url());
			redirect(site_url("general/login"));
			return;
		} else {
			$facebook = fb_object(current_url());
			if (is_string($facebook)) {
				redirect($facebook);
				return;
			}
		}

	}

	/**
	 * Map Page for this controller.
	 */
	public function map() {
		$output["title"] = 'Map';

		$friend_sql = "SELECT fid AS id, fname AS name FROM friend WHERE fb_id = '" . $this -> session -> userdata('fb_id') . "' ORDER BY RAND()";
		$friend_query = $this -> db -> query($friend_sql);
		$output['friend_data'] = $friend_query -> result_array();
		$output['no_friends'] = count($output['friend_data']);
		$this -> load -> view('map.php', $output);
	}

	/**
	 * Return the places user visited in JSON format.
	 */
	public function ajax_map() {
		$output = array("error" => 0, "fb_id" => "", "data" => "");
		$output['fb_id'] = $this -> uri -> segment(3);
		if (empty($output['fb_id'])) {
			$output['fb_id'] = "me()";
		}
		$facebook = fb_object(site_url(""));
		if (!is_string($facebook)) {
			$this -> load -> library('geo/geohelper');
			$geo = new GeoHelper();
			$places_update = array();

			// check cache
			$location_post_sql = "SELECT location_post.place_id AS place_id, latitude, longitude, city, country, no_photos FROM location_post LEFT JOIN place ON place.place_id=location_post.place_id WHERE fb_id ='" . $output['fb_id'] . "' AND date > SUBTIME(NOW(),'20 0:0:0')";
			$location_post_query = $this -> db -> query($location_post_sql);
			if ($location_post_query -> num_rows() > 0) {
				$first_row = $location_post_query -> first_row();
				if ($first_row -> place_id != '') {
					$output['data']['no_places'] = $location_post_query -> num_rows();
					$output['data']['list'] = array();
					foreach ($location_post_query->result() as $location_post_row) {

						$output['data']['list'][$location_post_row -> place_id] = array('place_id' => $location_post_row -> place_id, 'latitude' => $location_post_row -> latitude, 'longitude' => $location_post_row -> longitude, 'no_photos' => $location_post_row -> no_photos, );
						if ($location_post_row -> city == "") {
							$local_info = $geo -> reverseGeocode($location_post_row -> latitude, $location_post_row -> longitude);
							if (empty($local_info['city'])) {
								$local_info['city'] = 0;
								$local_info['country'] = 0;
							}

							$output['data']['list'][$location_post_row -> place_id]['city'] = $local_info['city'];
							$output['data']['list'][$location_post_row -> place_id]['country'] = $local_info['country'];
							$places_update[] = array('id' => $location_post_row -> place_id, 'city' => $local_info['city'], 'country' => $local_info['country']);
						} else {
							$output['data']['list'][$location_post_row -> place_id]['city'] = $location_post_row -> city;
							$output['data']['list'][$location_post_row -> place_id]['country'] = $location_post_row -> country;
						}

					}
				} else {
					$output['data']['no_places'] = 0;
					$output['data']['list'] = array();
				}
			} else {
				$result = $this -> Facebook_model -> location_post($facebook, $output['fb_id']);

				if (!empty($result['error'])) {
					$output['error'] = $result['error'];
				} else {
					$output['data']['no_places'] = count($result['data']['places']);
					$output['data']['list'] = array();
					if ($output['data']['no_places'] > 0) {
						foreach ($result['data']['places'] as $key => $place) {
							$output['data']['list'][$key] = array('place_id' => $key, 'latitude' => $place['info']['latitude'], 'longitude' => $place['info']['longitude'], 'no_photos' => count($place['photos']), );
						}

						$local_info = $geo -> reverseGeocode($place['info']['latitude'], $place['info']['longitude']);
						if (empty($local_info['city'])) {
							$local_info['city'] = 0;
							$local_info['country'] = 0;
						}
						$output['data']['list'][$key]['city'] = $local_info['city'];
						$output['data']['list'][$key]['country'] = $local_info['country'];
						$places_update[] = array('id' => $key, 'city' => $local_info['city'], 'country' => $local_info['country']);
					}
				}
			}

			$this -> Place_model -> update_city($places_update);
		} else {
			$output['error'] = "need login to facebook";
		}
		$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
	}

	/**
	 * Stats Page for this controller.
	 */
	public function stats() {
		$output["title"] = 'Stats';

		$facebook = fb_object(site_url("member/stats"));
		$user_id = $this -> session -> userdata('fb_id');
		// get friends from database
		$friend_data = array();
		$this -> db -> select('fid AS id, fname AS name');
		$friend_query = $this -> db -> get_where('friend', array('fb_id' => $this -> session -> userdata('fb_id')));
		foreach ($friend_query->result_array() as $friend) {
			$friend_data[$friend['id']] = $friend['name'];
		};
		$total_friend = count($friend_data);
		if ($total_friend == 0) {
			redirect(site_url("member/map"));
			return;
		}

		//get top 5 friends who have been to most places
		$top_friend_sql = "SELECT location_post.fb_id, count(location_post.place_id), count(DISTINCT city), count(DISTINCT country), count(DISTINCT city)*1 + count(DISTINCT country)*2 FROM location_post LEFT JOIN place ON location_post.place_id = place.place_id WHERE EXISTS (SELECT * FROM friend WHERE fb_id = '" . $user_id . "' AND fid =location_post.fb_id ) GROUP BY location_post.fb_id ORDER BY count(DISTINCT city)*1 + count(DISTINCT country)*3 DESC LIMIT 0, 5";
		$top_friend_query = $this -> db -> query($top_friend_sql);
		$top_friend_data = array();
		foreach ($top_friend_query->result_array() as $friend) {
			$no_places = $friend['count(location_post.place_id)'];
			$no_cities = $friend['count(DISTINCT city)'];
			$no_countries = $friend['count(DISTINCT country)'];
			$points = $friend['count(DISTINCT city)*1 + count(DISTINCT country)*3'];
			$top_friend_data[] = array('fid' => $friend['fb_id'], 'name' => $friend_data[$friend['fb_id']], 'no_places' => $no_places, 'no_countries' => $no_countries, 'no_cities' => $no_cities, 'points' => $points);
		}

		//get top 3 popular places

		$top_place_sql = "SELECT COUNT(DISTINCT fb_id), place.name, place.description, place.city, place.country FROM location_post LEFT JOIN place ON place.place_id=location_post.place_id WHERE fb_id in (" . implode(',', array_keys($friend_data)) . ")  and location_post.place_id<>'' and place.city<>'' and place.city<>'0' and place.country<>'' and place.country<>'0'  Group BY place.city, place.country Order by COUNT(DISTINCT fb_id) DESC LIMIT 0, 3";
		$top_place_query = $this -> db -> query($top_place_sql);
		$top_place_data = array();

		foreach ($top_place_query->result_array() as $place) {
			$percent = round($place['COUNT(DISTINCT fb_id)'] * 100 / $total_friend, 2);
			$top_place_data[] = array('country' => $place['country'], 'city' => $place['city'], 'no_friends' => $place['COUNT(DISTINCT fb_id)'], 'percent' => $percent, 'name' => $place['name'], 'description' => $place['description']);
		}

		$output['top_friends'] = json_encode($top_friend_data);
		$output['top_places'] = json_encode($top_place_data);

		// calculate the accuracy of data
		$need_cache_friend_sql = "SELECT fid AS id, fname AS name FROM friend WHERE fb_id = '" . $user_id . "' AND NOT EXISTS (SELECT * FROM location_post WHERE fb_id = friend.fid)";
		$need_cache_friend_query = $this -> db -> query($need_cache_friend_sql);
		$output['total_friends'] = $total_friend;
		$output['need_cache_friends'] = $need_cache_friend_query -> result_array();
		$output['accuracy'] = 100 - round(count($output['need_cache_friends']) * 100 / $total_friend, 2);
		$output['tag_id']=$top_friend_data[0]['fid'];
		$this -> load -> view('stats.php', $output);
	}

	/**
	 * Friends Page for this controller.
	 */
	public function friends() {
		$output["title"] = 'Friends';

		$friend_sql = "SELECT fid AS id, fname AS name FROM friend WHERE fb_id = '" . $this -> session -> userdata('fb_id') . "' ORDER BY fname ASC";
		$friend_query = $this -> db -> query($friend_sql);
		$output['friend_data'] = $friend_query -> result_array();
		$output['no_friends'] = count($output['friend_data']);
		$this -> load -> view('friends.php', $output);
	}

	/**
	 * Return the number of places the friend visited, number of photos he took as well as the url to his random photo
	 */
	public function ajax_friend() {
		$output = array("error" => 0, "fb_id" => "", "data" => "");
		$output['fb_id'] = $this -> uri -> segment(3);
		if (empty($output['fb_id'])) {
			$output['fb_id'] = "me()";
		}
		$facebook = fb_object(site_url("member/friends"));
		if (!is_string($facebook)) {
			// check cache
			$location_post_sql = "SELECT count(place_id) AS no_places, sum(no_photos) AS no_photos FROM location_post WHERE fb_id ='" . $output['fb_id'] . "' AND date > SUBTIME(NOW(),'20 0:0:0')";
			$location_post_query = $this -> db -> query($location_post_sql);
			$location_post_row = $location_post_query -> first_row();
			if ($location_post_row -> no_places > 0) {
				$output['data']['no_places'] = $location_post_row -> no_places;
				$output['data']['no_photos'] = $location_post_row -> no_photos;

				if ($location_post_row -> no_photos > 0) {
					$photo_sql = "SELECT src FROM location_post WHERE fb_id ='" . $output['fb_id'] . "' AND src !='' LIMIT 0,1";
					$photo_query = $this -> db -> query($photo_sql);
					$row = $photo_query -> first_row();
					$output['data']['a_photo_src'] = $row -> src;
				}
			} else {
				$result = $this -> Facebook_model -> location_post($facebook, $output['fb_id']);
				if (!empty($result['error'])) {
					$output['error'] = $result['error'];
				} else {
					$output['data']['no_places'] = count($result['data']['places']);
					$output['data']['no_photos'] = $result['data']['total_photos'];
					$output['data']['a_photo_src'] = $result['data']['a_photo_src'];
				}
			}
		} else {
			$output['error'] = "need login to facebook";
		}

		$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
	}

	/**
	 * Gallery Page for this controller.
	 */
	public function gallery() {
		$output['uid'] = $this -> uri -> segment(3);
		$output['title'] = rawurldecode($this -> uri -> segment(4));
		$output['pid'] = $this -> uri -> segment(5);
		$output['place'] = rawurldecode($this -> uri -> segment(6));

		if (empty($output['uid'])) {
			redirect(site_url("member/friends"));
			return;
		}

		$facebook = fb_object(site_url());

		$this -> db -> select('object_ids');
		$location_post_query = $this -> db -> get_where('location_post', array('fb_id' => $output['uid'], 'place_id' => $output['pid']));

		if ($location_post_query -> num_rows() > 0) {
			$first_row = $location_post_query -> first_row();
			try {
				$params = array('method' => 'fql.query', 'query' => "SELECT link, src_big, caption, created FROM photo WHERE object_id IN (" . $first_row -> object_ids . ")", );
				$output['data'] = $facebook -> api($params);
				$output['data'][0]['created'] = gmdate("Y-m-d\TH:i:s\Z", $output['data'][0]['created']);
			} catch(Exception $e) {
				echo $e -> getMessage();
			}
			$this -> load -> view('gallery.php', $output);
		} else {
			refirect(site_url("member/friends"));
		}

	}

	/**
	 * post activity explore on faceboook.
	 */
	public function ajax_post_activity_friendplace() {
		$output['error'] = 0;
		$friend_id = $this -> uri -> segment(3);
		$place_id = $this -> uri -> segment(4);
		$facebook = fb_object(site_url());
		if (!is_string($facebook)) {
			try {
				$params = array('friendplace' => site_url("general/OpenGraph_FriendPlace/" . $friend_id . "/" . $place_id));
				$out = $facebook -> api('/me/travel_expo:explore', 'post', $params);
			} catch(Exception $e) {
				$output['error'] = $e -> getMessage();
			}
		} else {
			$output['error'] = "need login to facebook";
		}
		$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
	}

	/**
	 * post activity want_to_go on faceboook.
	 */
	public function ajax_post_activity_wantToGo() {
		$output['error'] = 0;
		$friend_id = $this -> uri -> segment(3);
		$place_id = $this -> uri -> segment(4);
		$facebook = fb_object(site_url());
		if (!is_string($facebook)) {
			try {
				$params = array('friendplace' => site_url("general/OpenGraph_FriendPlace/" . $friend_id . "/" . $place_id));
				$out = $facebook -> api('/me/travel_expo:want_to_go', 'post', $params);
			} catch(Exception $e) {
				$output['error'] = $e -> getMessage();
			}
		} else {
			$output['error'] = "need login to facebook";
		}
		$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
	}

	/**
	 * Person Page for this controller.
	 */
	public function person() {
		$output = array();
		$output['uid'] = $this -> uri -> segment(3);
		$user_info = $this -> db -> get_where('friend', array('fid' => $output['uid']), 1);
		$user_info = $user_info -> first_row();
		$output["title"] = $user_info -> fname;

		$facebook = fb_object(site_url());
		if (!is_string($facebook)) {
			$output["fb_appId"] = $facebook -> getAppId();
		} else {
			$output["fb_appId"] = "";
		}
		$this -> load -> view('person.php', $output);
	}

	/**
	 * Return details information of user in JSON format
	 */
	public function ajax_person_place() {
		$output = array("error" => 0, "uid" => "", "data" => "");
		$output['uid'] = $this -> uri -> segment(3);

		if (empty($output['uid'])) {
			$output['error'] = "uid is empty";
			$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
			return;
		}

		$facebook = fb_object(site_url());
		if (!is_string($facebook)) {

			$check_cache_location_post_query = $this -> db -> get_where('location_post', array('fb_id' => $output['uid']), 1);
			if ($check_cache_location_post_query -> num_rows() == 0) {
				$this -> Facebook_model -> location_post($facebook, $output['uid']);
			}

			$location_post_sql = "SELECT location_post.place_id AS place_id, latitude, longitude, name, city, country, object_ids, src FROM location_post LEFT JOIN place ON place.place_id=location_post.place_id WHERE fb_id ='" . $output['uid'] . "'";
			$location_post_query = $this -> db -> query($location_post_sql);
			if ($location_post_query -> num_rows() > 0) {
				$first_row = $location_post_query -> first_row();
				if ($first_row -> place_id != '') {
					$places = array();
					$photo_places = array();
					$output['data'] = array('no_place' => $location_post_query -> num_rows(), 'place_list' => array(), 'country_list' => array());

					$this -> load -> library('geo/geohelper');
					$geo = new GeoHelper();
					$places_update = array();

					foreach ($location_post_query->result() as $row) {
						$arr = explode(",", $row -> object_ids);
						$output['data']['place_list'][$row -> place_id]['no_photos'] = count($arr);

						// check whether we need to find a photo src
						if (empty($row -> src)) {
							$photo_places[$arr[0]] = $row -> place_id;
						} else {
							$output['data']['place_list'][$row -> place_id]['src'] = $row -> src;
						}

						// check whether we need to get the information of place
						if (empty($row -> name)) {
							$places[$row -> place_id] = $row -> place_id;
						} else {
							$output['data']['place_list'][$row -> place_id]['name'] = $row -> name;
						}

						// check whether we need to get city
						if ($row -> city == "") {
							$local_info = $geo -> reverseGeocode($row -> latitude, $row -> longitude);
							if (empty($local_info['city'])) {
								$local_info['city'] = 0;
								$local_info['country'] = 0;
							}
							$output['data']['country_list'][$local_info['country']][$local_info['city']][$row -> place_id] = $row -> place_id;
							$places_update[] = array('id' => $row -> place_id, 'city' => $local_info['city'], 'country' => $local_info['country']);
						} else {
							$output['data']['country_list'][$row -> country][$row -> city][$row -> place_id] = $row -> place_id;
						}
					}

					$fql_location = array('photo_query' => "SELECT object_id, src_big FROM photo WHERE object_id IN (" . implode(",", array_keys($photo_places)) . ")", 'place_query' => "SELECT page_id, name FROM place WHERE page_id IN (" . implode(",", $places) . ")", );
					$params_location = array('method' => 'fql.multiquery', 'queries' => $fql_location);
					$result = $facebook -> api($params_location);

					foreach ($result[0]['fql_result_set'] as $row) {
						$output['data']['place_list'][$photo_places[$row['object_id']]]['src'] = $row['src_big'];
						// cache database
						$this -> db -> where('place_id', $photo_places[$row['object_id']]);
						$this -> db -> where('fb_id', $output['uid']);
						$this -> db -> update('location_post', array('src' => $row['src_big']));
					}
					foreach ($result[1]['fql_result_set'] as $row) {
						$output['data']['place_list'][$row['page_id']]['name'] = $row['name'];
						$this -> db -> where('place_id', $row['page_id']);
						$this -> db -> update('place', array('name' => $row['name']));
					}

					$this -> Place_model -> update_city($places_update);

				} else {
					$output['data'] = array('no_place' => '0', 'place_list' => '', 'country_list' => '');
				}
			} else {
				$output['error'] = "need to load the places";
			}

		} else {
			$output['error'] = "need login to facebook";
		}

		$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
	}

	/**
	 * Return details information of place in JSON format
	 */
	public function place_info() {
		$output = array("error" => 0, "place_id" => "", "data" => "");
		$output['place_id'] = $this -> uri -> segment(3);
		$facebook = fb_object(site_url("member/friends"));
		if (!is_string($facebook)) {

			$fb_id = $this -> session -> userdata('fb_id');
			$friend_place_query = $this -> db -> get_where('friend_place', array('fb_id' => $fb_id, 'place_id' => $output['place_id']), 1);
			if ($friend_place_query -> num_rows() > 0) {
				$friend_place_first_row = $friend_place_query -> first_row();
				$output['data']['no_friends'] = $friend_place_first_row -> no_friends;
				$output['data']['list_friends'] = explode(',', $friend_place_first_row -> friend_list);
			} else {
				$friend_place_data = $this -> Facebook_model -> friend_place($facebook, $fb_id, $output['place_id']);
				$output['data']['no_friends'] = $friend_place_data['data']['no_friends'];
				$output['data']['list_friends'] = $friend_place_data['data']['list_friends'];
			}

			$place_info_query = $this -> db -> get_where('place', array('place_id' => $output['place_id']));
			if ($place_info_query -> num_rows() > 0) {
				$place_info = $place_info_query -> first_row();
				$output['info']['latitude'] = $place_info -> latitude;
				$output['info']['longitude'] = $place_info -> longitude;
				if (!empty($place_info -> name)) {
					$output['info']['name'] = $place_info -> name;
					$output['info']['description'] = $place_info -> description;
				} else {
					$place_info_data = $this -> Facebook_model -> update_place_info($facebook, $output['place_id']);
					$output['info']['name'] = $place_info_data['data']['name'];
					$output['info']['description'] = $place_info_data['data']['description'];
				}

				if ($place_info -> city != "") {
					$output['info']['city'] = $place_info -> city;
					$output['info']['country'] = $place_info -> country;
				} else {
					$this -> load -> library('geo/geohelper');
					$geo = new GeoHelper();
					$place_info = $geo -> reverseGeocode($place_info -> latitude, $place_info -> longitude);
					$output['info']['city'] = $place_info['city'];
					$output['info']['country'] = $place_info['country'];
					$this -> Place_model -> update_city(array('id' => $output['place_id'], 'city' => $place_info['city'], 'country' => $place_info['country']));
				}
			} else {
				$output['error'] = "no such place";
			}
		} else {
			$output['error'] = "need login to facebook";
		}

		$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
	}

	/**
	 * Invite Page for this controller.
	 */
	public function invite() {
		$facebook = fb_object(site_url());
		if (!is_string($facebook)) {
			$output["fb_appId"] = $facebook -> getAppId();
		} else {
			$output["fb_appId"] = "";
		}

		$friend_used_app_sql = "SELECT * FROM members WHERE members.fb_id IN (SELECT fid FROM friend WHERE fb_id = '" . $this -> session -> userdata('fb_id') . "')";
		$result = $this -> db -> query($friend_used_app_sql);
		$output['friends'] = $result -> result_array();

		$this -> load -> view('invite.php', $output);
	}

	/**
	 * Setting Page for this controller.
	 */
	public function setting() {
		$cache_friend_sql = "SELECT count(fid) AS no FROM friend WHERE fb_id = '" . $this -> session -> userdata('fb_id') . "' AND EXISTS (SELECT * FROM location_post WHERE fb_id = friend.fid)";
		$cache_friend_query = $this -> db -> query($cache_friend_sql);
		$first_row = $cache_friend_query -> first_row();
		$output['no_cached_friends'] = $first_row -> no;

		$need_cache_friend_sql = "SELECT fid AS id, fname AS name FROM friend WHERE fb_id = '" . $this -> session -> userdata('fb_id') . "' AND NOT EXISTS (SELECT * FROM location_post WHERE fb_id = friend.fid)";
		$need_cache_friend_query = $this -> db -> query($need_cache_friend_sql);
		$output['need_cache_friends'] = $need_cache_friend_query -> result_array();

		$this -> load -> view('setting.php', $output);
	}

	/**
	 * Remove the old records and insert the friend list of the user into database
	 */
	public function ajax_update_friend_list() {
		$output = array("error" => 0, "no_friends" => "");
		$facebook = fb_object(site_url());
		if (!is_string($facebook)) {
			$frnd = $facebook -> api('/me/friends', array('fields' => 'id, name'));
			$this -> Facebook_model -> update_friend_list($this -> session -> userdata('fb_id'), $frnd['data']);
			$output['no_friends'] = count($frnd['data']);
		} else {
			$output['error'] = "need login to facebook";
		}

		$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
	}

	/**
	 * Remove the cache data of users
	 */
	public function ajax_remove_cache() {
		$this -> db -> delete('location_post', array('fb_id' => $this -> session -> userdata('fb_id')));
		$this -> db -> delete('friend_place', array('fb_id' => $this -> session -> userdata('fb_id')));
		$this -> output -> set_content_type('application/json') -> set_output(json_encode("success"));
	}

	/**
	 * Load the friends' information
	 */
	public function ajax_cache_friend() {
		$output = array("error" => 0, "no_friends" => "");
		$facebook = fb_object(site_url(""));
		if (!is_string($facebook)) {
			$friends_list = $this -> input -> post('list');
			$friends_list = explode(",", $friends_list);
			$count = 0;
			foreach ($friends_list as $friend) {
				$result = $this -> Facebook_model -> location_post($facebook, $friend);
				if (!empty($result['error'])) {
					$output['error'] = $result['error'];
					break;
				}
				$count++;
			}
			$output["no_friends"] = $count;
		} else {
			$output['error'] = "need login to facebook";
		}
		$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
	}

	/**
	 * Return the list of photo which the user has not added the location.
	 */
	public function ajax_need_tag_photo() {
		$output = array("error" => 0, "total_photo" => "", "data" => "");
		$facebook = fb_object(site_url());
		if (!is_string($facebook)) {
			$params = array('method' => 'fql.query', 'query' => "SELECT link, src_big FROM photo WHERE owner = (" . $this -> session -> userdata('fb_id') . ") AND place_id = ''", );
			$data = $facebook -> api($params);
			$output["total_photo"] = count($data);
			$output["data"] = $data;
		} else {
			$output['error'] = "need login to facebook";
		}
		$this -> output -> set_content_type('application/json') -> set_output(json_encode($output));
	}

	/**
	 * AJAX for search
	 */
	public function search() {
		//param
		$q = $_GET["query"];
		$facebook = fb_object(site_url("member/map"));
		$uid = $this -> session -> userdata('fb_id');
		//lookup all hints from array if length of q>0
		if (strlen($q) > 0) {
			$data = array('query' => $q, 'suggestions' => array(), 'data' => array());
			$sql_name = "SELECT fid, fname FROM friend WHERE fb_id=" . $uid . " AND fname LIKE '%" . $this -> db -> escape_like_str($q) . "%'";
			$result = $this -> db -> query($sql_name);

			foreach ($result->result_array() as $row) {
				array_push($data['suggestions'], $row['fname']);
				array_push($data['data'], $row['fid']);
			}
			$response = json_encode($data);
		}
		//output the response
		echo $response;
	}

	public function like() {
		$result = array('success' => false, 'is_liked' => false, 'like_count' => 0, 'action' => '');
		$facebook = fb_object(site_url("member/map"));

		if ($_GET['action'] == "like" && $_GET['place']) {//if action is 'like'
			$result['success'] = TRUE;
			$result['action'] = 'like';
			$place_id = $_GET['place'];
			$fb_id = $this -> session -> userdata('fb_id');
			$sql_check_exist = "SELECT * FROM prefer_place WHERE fb_id=$fb_id AND place_id=$place_id";
			$sql_add_want = "INSERT INTO prefer_place VALUES ($fb_id , $place_id)";
			$sql_get_like_num = "SELECT COUNT(fb_id) FROM prefer_place WHERE place_id=$place_id";
			$sql_delete_want = "DELETE FROM prefer_place WHERE fb_id=$fb_id AND place_id=$place_id";
			//check has the data already recorded
			$query_check = $this -> db -> query($sql_check_exist);
			//	echo "Number of raws in result :" . mysql_num_rows($result)."\n";
			if (count($query_check -> result_array()) == 0) {
				$query_add = $this -> db -> query($sql_add_want);
				$query_get_like_num = $this -> db -> query($sql_get_like_num);
				$rows = $query_get_like_num -> result_array();
				$row = $rows[0];
				$result['like_count'] = $row['COUNT(fb_id)'];
				$result['is_liked'] = true;
			} else {
				$query_get_like_num = $this -> db -> query($sql_get_like_num);
				$rows = $query_get_like_num -> result_array();
				$row = $rows[0];
				$result['like_count'] = $row['COUNT(fb_id)'];
				$result['is_liked'] = true;
			}
		} else if ($_GET['action'] == "dislike" && $_GET['place']) {

			$result['success'] = TRUE;
			$result['action'] = 'dislike';
			$place_id = $_GET['place'];
			$fb_id = $this -> session -> userdata('fb_id');
			$sql_check_exist = "SELECT * FROM prefer_place WHERE fb_id=$fb_id AND place_id=$place_id";
			$sql_get_like_num = "SELECT COUNT(fb_id) FROM prefer_place WHERE place_id=$place_id";
			$sql_delete_want = "DELETE FROM prefer_place WHERE fb_id=$fb_id AND place_id=$place_id";
			//check has the data already recorded
			$query_check = $this -> db -> query($sql_check_exist);
			if (count($query_check -> result_array()) != 0) {
				$query_delete = $this -> db -> query($sql_delete_want);
				$query_get_like_num = $this -> db -> query($sql_get_like_num);
				$rows = $query_get_like_num -> result_array();
				$row = $rows[0];
				$result['like_count'] = $row['COUNT(fb_id)'];
			}
		} else if ($_GET['action'] == "islike" && $_GET['place']) {
			$result['success'] = TRUE;
			$result['action'] = 'islike';
			$place_id = $_GET['place'];
			$fb_id = $this -> session -> userdata('fb_id');
			$sql_check_exist = "SELECT * FROM prefer_place WHERE fb_id=$fb_id AND place_id=$place_id";
			$sql_get_like_num = "SELECT COUNT(fb_id) FROM prefer_place WHERE place_id=$place_id";
			$query_check = $this -> db -> query($sql_check_exist);
			if (count($query_check -> result_array()) != 0) {
				$result['is_liked'] = TRUE;
				$query_get_like_num = $this -> db -> query($sql_get_like_num);
				$rows = $query_get_like_num -> result_array();
				$row = $rows[0];
				$result['like_count'] = $row['COUNT(fb_id)'];
			} else {
				$result['is_liked'] = FALSE;
				$query_get_like_num = $this -> db -> query($sql_get_like_num);
				$rows = $query_get_like_num -> result_array();
				$row = $rows[0];
				$result['like_count'] = $row['COUNT(fb_id)'];
			}
		}
		$response = json_encode($result);
		echo $response;
	}
	
	//Ajax for posting photo to facebook
	public function ajax_post_photo_people() {
		$result = array("result" => false, "error" => '', "data" => '');
		$facebook = fb_object(site_url("member/stats"));
		$user_id = $facebook -> getUser();
		if ($user_id) {
			if (isset($_POST['dataURL'])) {
				//process data
				$image_data = $_POST['dataURL'];
				$image_data = str_replace('data:image/png;base64,', '', $image_data);
				$image_data = str_replace(' ', '+', $image_data);
				$data = base64_decode($image_data);
				$tags = array();

				if (isset($_POST['tag_id']) && isset($_POST['x']) && isset($_POST['y'])) {
				
					$tags = array( 
						array("tag_uid" => $_POST['tag_id'], 
							'x' => $_POST['x'], 
							'y' => $_POST['y']), 
					);
				}

				$filename = $user_id . '.png';
				if (write_file('./assets/img/stats/user_img/' . $filename, $data)) {
					$tempfileName = ASSEST_URL . 'img/stats/user_img/' . $filename;
					//get current time
					$time = date(DATE_ISO8601);
					try {
						$facebook -> setFileUploadSupport(true);
						$post_data = array('name' => 'Statistics  From TravelExpo', 'url' => $tempfileName, "created_time" => $time,"tags"=>$tags
						);
						//$data_photo;
						
						$data_photo = $facebook -> api("/me/photos", 'post', $post_data);

						//clear cache img
						delete_files('./assets/img/stats/user_img/' . $filename);
				
						$result['data'] = $data_photo;
						$result['result'] = true;

						echo json_encode($result);

					} catch(FacebookApiException $e) {
						// if error from facebook
						$result['error'] = $e -> getType() . $e -> getMessage();
						echo json_encode($result);
					}
				} else {
					$result['error'] = "fail to save content:" . $filename;
					echo json_encode($result);
				}
			} else {
				$result['error'] = "dataURL not set";
				echo json_encode($result);
			}
		} else {
			$login_url = $facebook -> getLoginUrl(array('redirect_uri' => site_url(), 'scope' => 'photo_upload'));
			$result['data'] = 'Please <a href="' . $login_url . '">login.</a>';
			$result['result'] = true;
			echo json_encode($result);
		}
	}
	//Ajax for posting photo to facebook
	public function ajax_post_photo_place() {
		$result = array("result" => false, "error" => '', "data" => '');
		$facebook = fb_object(site_url("member/stats"));
		$user_id = $facebook -> getUser();
		if ($user_id) {
			if (isset($_POST['dataURL'])) {
				//process data
				$image_data = $_POST['dataURL'];
				$image_data = str_replace('data:image/png;base64,', '', $image_data);
				$image_data = str_replace(' ', '+', $image_data);
				$data = base64_decode($image_data);
				

				$filename = $user_id . '.png';
				if (write_file('./assets/img/stats/user_img/' . $filename, $data)) {
					$tempfileName = ASSEST_URL . 'img/stats/user_img/' . $filename;
					//get current time
					$time = date(DATE_ISO8601);
					try {
						$facebook -> setFileUploadSupport(true);
						$post_data = array('name' => 'Statistics  From TravelExpo', 'url' => $tempfileName, "created_time" => $time,
						);
						
						$data_photo = $facebook -> api("/me/photos", 'post', $post_data);

						//clear cache img
						delete_files('./assets/img/stats/user_img/' . $filename);

						$result['data'] = $data_photo;
						$result['result'] = true;

						echo json_encode($result);

					} catch(FacebookApiException $e) {
						// if error from facebook
						$result['error'] = $e -> getType() . $e -> getMessage();
						echo json_encode($result);
					}
				} else {
					$result['error'] = "fail to save content:" . $filename;
					echo json_encode($result);
				}
			} else {
				$result['error'] = "dataURL not set";
				echo json_encode($result);
			}
		} else {
			$login_url = $facebook -> getLoginUrl(array('redirect_uri' => site_url(), 'scope' => 'photo_upload'));
			$result['data'] = 'Please <a href="' . $login_url . '">login.</a>';
			$result['result'] = true;
			echo json_encode($result);
		}
	}
}
