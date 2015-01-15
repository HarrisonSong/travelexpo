<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// Your own constructor code	
		$this->load->library('session');		
		$this->load->helper('url');
		$this->load->database();
		$this->load->library('facebook/facebook');
		$this->load->helper('facebook');
		$this -> load -> model('Facebook_model');
		
		define('ASSEST_URL', base_url().'assets/');						
	}


	/**
	 * Index Page for this controller.
	 */
	public function index() {
		$facebook = fb_object(site_url("general/login"));
		if (is_string($facebook) && $this->session->userdata('logged_in')) {
			redirect(site_url("general/logout"));
			return;
		}
		
		if($this->session->userdata('logged_in') == FALSE) {
			$output["title"] = 'Explore travel destinations from your friends\' photos!';
			$this->load->view('index.php', $output);	
		} else {
			redirect(site_url("member/map"));
		}
	}
 
	/**
	 * Login Page for this controller.
	 */
	public function login() {
		$facebook = fb_object(site_url("general/login"));
		
		if (is_string($facebook) && $this->session->userdata('logged_in')) {
			log_message('level', 'in A');
      		redirect(site_url("member/logout"));
			return;
		}
		
		if($this->session->userdata('logged_in') == FALSE) {
      		log_message('level', 'in B');
			/* Redirect to the link before logging */
			if (!is_string($facebook)) {
        		log_message('level', 'in C');
				$fbuser = $facebook->api('/me', array('fields' => 'id, name'));
				$login_query = $this->db->get_where('members', array('fb_id' => $fbuser['id']), 1);
				if ($login_query->num_rows() == 0) {
					$this->db->insert('members', array('fb_id' => $fbuser['id'], 'name' => $fbuser['name']));
					$frnd = $facebook -> api('/me/friends', array('fields' => 'id, name'));
					
					$this -> Facebook_model -> update_friend_list($fbuser['id'], $frnd['data']);
				}
				$new_session = array(
					'fb_id' => $fbuser['id'],
				 	'logged_in' => TRUE,
					'username' => $fbuser['name']
               );			   
				$this->session->set_userdata($new_session);
				redirect(site_url("member/map"));
			} else {
				redirect($facebook);	
			}
		} else {
			redirect(site_url("member/map"));	
		}
	}
	
	/**
	 * Logout Page for this controller.
	 */
	public function logout() {
		if($this->session->userdata('logged_in') == TRUE) {
			$this->session->set_userdata("logged_in", FALSE);
			
			/*
			$facebook = fb_object(site_url("general/index"));
			if (!is_string($facebook)) {
				$logoutUrl = $facebook->getLogoutUrl(array( 'next' => site_url("general/index")));			
				$_SESSION = array();
				if (ini_get("session.use_cookies")) {
					$params = session_get_cookie_params();
					setcookie(session_name(), '', time() - 42000,
						$params["path"], $params["domain"],
						$params["secure"], $params["httponly"]
					);
				}
			}
			*/
		}
		redirect(site_url("general/index"));
	}
	
	/**
	 * FriendPlace  Open Graph Objects
	 */
	public function OpenGraph_FriendPlace() {
		$output['friend_id']  = $this -> uri -> segment(3);
		$output['place_id']  = $this -> uri -> segment(4);
		
		$output['redirect_link'] = site_url();
		$facebook = fb_object(site_url(""));
		if (!is_string($facebook)) {
			$uid = $facebook->getUser();
			$friend_query = $this->db->get_where('friend', array('fb_id' => $uid, 'fid' =>$output['friend_id'] ));
			if ($friend_query ->num_rows() > 0) {
				$first_row = $friend_query -> first_row();
				$output['redirect_link'] = site_url("member/gallery/".$output['friend_id']."/".rawurlencode($first_row->fname)."/".$output['place_id'] );
			}
		}
		
		$place_info_query = $this->db->get_where('place', array('place_id' => $output['place_id']));
		if ($place_info_query ->num_rows() > 0) {
			$first_row = $place_info_query -> first_row();
			$output['info']['description'] = $first_row->description;
			$output['info']['name'] =  $first_row->name;
			
			if (!empty($first_row->city)) {
				$output['info']['name'] .=" ( ".$first_row->city."  , ".$first_row->country.")";
			};
			
			$output['info']['latitude'] =  $first_row->latitude;
			$output['info']['longitude'] =  $first_row->longitude;
		}
		
		$this->db->select('src');
		$location_post_query = $this ->db-> get_where('location_post', array('fb_id' => $output['friend_id'], 'place_id' => $output['place_id']));
		if ($location_post_query ->num_rows() > 0) {
			$first_row = $location_post_query -> first_row();
			$output['src'] = $first_row->src;
		} else {
			$output['src'] = ASSEST_URL."img/logo.png";
		}
		$this -> load -> view('FB_FriendPlace.php', $output);
	}

	/**
	 * callback url which are pinged by facebook when someone deauthorized the application. 
	 */
	public function removeapp() {
		$config['secret_key'] = "bb82496d1b09afa476c103228572e449" ;
		$data         =   parse_signed_request($_REQUEST['signed_request'], $config['secret_key']);
		$fbUserId   =   $data['user_id'];
		
		// remove data
		$this->db->delete('members', array('fb_id' => $fbUserId)); 
		$this->db->delete('friend', array('fb_id' => $fbUserId)); 
		$this->db->delete('prefer_place', array('fb_id' => $fbUserId)); 
		$this->db->delete('friend_place', array('fb_id' => $fbUserId));
	}
}
