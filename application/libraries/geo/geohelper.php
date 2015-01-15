<?php

/**
 * This example shows
 *
 * - How to perform a reverse geocode
 * - How to determine the city of the location
 */

require ('sdk/bootstrap.php');
require ('sdk/communicator/CurlCommunicator.php');

class GeoHelper {
	
	public $service;
	public function __construct() {
		$service = new GoogleGeocodeServiceV3(new CurlCommunicator());
	}

	protected function getCityFromGeocode($latitude, $longitude) {
		$service = new GoogleGeocodeServiceV3(new CurlCommunicator());
		// Geographic center of US ZIP code 90210
		$response = $service -> reverseGeocode($latitude, $longitude);

		while ($response -> valid()) {
			// Address component type we're checking for
			$city = GoogleGeocodeResponseV3::ACT_LOCALITY;
			$country = GoogleGeocodeResponseV3::ACT_COUNTRY;
			// Is it a city-level result?
			if ($response -> assertType($city)) {
				// Get the city name
				return array('city' => $response -> getAddressComponentName($city), 'country' =>$response -> getAddressComponentName($country)) ;
			}
			$response -> next();
		}
	}
	
	public function reverseGeocode($latitude, $longitude) {
		return $this->getCityFromGeocode($latitude, $longitude);
	}

}
