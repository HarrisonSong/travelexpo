<?php
class Place_model extends CI_Model {
	function __construct()
    {
        parent::__construct();
    }
	
	function update_city($places) {
		foreach($places as $place) {
			$this->db->where('place_id', $place['id']);
			if (empty($place['city'])) {
				$this->db->update('place', array('city' => '0', 'country' => '0'));
			} else {
				$this->db->update('place', array('city' => $place['city'], 'country' => $place['country']));
			}
		}
	}
}

?>