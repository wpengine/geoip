<?php
/*
 * Plugin Name: WP Engine Geo
 * Version: 0.1
 * Author: WP Engine Labs
*/

namespace WPEngine;

class GeoIp {

	public $geos;

	public function __construct() {
		$actuals = $this->get_actuals();
		$overrides = $this->get_overrides();
		$this->geos = array_merge($actuals, $overrides);
	}

	/**
 	* here we extract the data from headers set by nginx -- lets only send them if they are part of the cache key
 	*/
	public function get_actuals() {
		return array(
			'countrycode' => getenv('HTTP_GEOIP_COUNTRY_CODE'),
			'countrycode3' => getenv('HTTP_GEOIP_COUNTRY_CODE3'),
			'countryname' => getenv('HTTP_GEOIP_COUNTRY_NAME'),
			'latitude' => getenv('HTTP_GEOIP_LATITUDE'),
			'longitude' => getenv('HTTP_GEOIP_LONGITUDE'),
			'areacode' => getenv('HTTP_GEOIP_AREA_CODE'),
			'region' => getenv('HTTP_GEOIP_REGION'),
			'city' => getenv('HTTP_GEOIP_CITY'),
			'postalcode' => getenv('HTTP_GEOIP_POSTAL_CODE'),
		);
	}

	public function get_overrides() {
		return array(
		);
	}

	/**
 	* examples of easy to use utility functions that we should have for each geo that is part of the cache key
 	*/
	public function country() {
		return $this->geos['countrycode'];
	}
	public function region() {
		return $this->geos['region'];
	}
	public function city() {
		return $this->geos['city'];
	}

}

/**
 * Register to do the stuff
 */
function append_content($content) {
	print date('r')."<br>";
	$geo = new GeoIp();
	print "Country: ".$geo->country()."<br>";
	print "Region: ".$geo->region()."<br>";
	print "City: ".$geo->city()."<br>";
}
add_filter('the_content', 'WPEngine\append_content' );

