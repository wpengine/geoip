<?php
/*
Plugin Name: WP Engine GeoIP
Version: 1.0.0
Description: Create a personalized user experienced based on location.
Author: WP Engine
Author URI: http://wpengine.com
Plugin URI: https://wordpress.org/plugins/wpengine-geoip/
Text Domain: wpengine-geoip
Domain Path: /languages
*/

/* Examples use of how to add geoip information to post content:

function geoip_append_content( $content ) {
	$geo = WPEngine\GeoIp::instance();
	$content .= "How's the weather in {$geo->city()}, {$geo->region()} {$geo->country()}?<br /><br />";
	return $content;
}
add_filter( 'the_content', 'geoip_append_content' );

*/

namespace WPEngine;

// Exit if this file is directly accessed
if ( ! defined( 'ABSPATH' ) ) exit;

class GeoIp {

	// The single instance of this object.  No need to have more than one.
	private static $instance = null;

	// The geographical data loaded from the environment
	public $geos;

	// WP-Admin errors notices
	private $admin_notices = array();

	// Shortcodes
	const SHORTCODE_COUNTRY     = 'geoip-country';
	const SHORTCODE_REGION      = 'geoip-region';
	const SHORTCODE_CITY        = 'geoip-city';
	const SHORTCODE_POSTAL_CODE = 'geoip-postalcode';
	const SHORTCODE_LATITUDE    = 'geoip-latitude';
	const SHORTCODE_LONGITUDE   = 'geoip-longitude';
	const SHORTCODE_LOCATION    = 'geoip-location';

	// Text Domain
	const TEXT_DOMAIN           = 'wpengine-geoip';

	/**
	 * Initialize hooks and setup environment variables
	 *
	 * @since 0.1.0
	 */
	public static function init() {

		// Initialize
		add_action( 'init', array( self::instance(), 'setup' ) );
		add_action( 'init', array( self::instance(), 'action_init_register_shortcodes' ) );

		// Check for dependencies
		add_action( 'admin_init', array( self::instance(), 'action_admin_init_check_plugin_dependencies' ), 9999 ); // check late
		add_action( 'admin_notices', array( self::instance(), 'action_admin_notices' ) );

	}

	/**
	 * Register singleton
	 *
	 * @since 0.1.0
	 */
	public static function instance() {
		// create a new object if it doesn't exist.
		is_null( self::$instance ) && self::$instance = new self;
		return self::$instance;
	}

	/**
	 * Setup environment variables
	 *
	 * @since 0.1.0
	 */
	public function setup() {
		$this->geos = $this->get_actuals();
	}

	/**
	 * Here we extract the data from headers set by nginx -- lets only send them if they are part of the cache key
	 *
	 * @since 0.1.0
	 * @return array All of the GeoIP related environment variables available on the current server instance
	 */
	public function get_actuals() {
		return array(
			'countrycode'  => getenv( 'HTTP_GEOIP_COUNTRY_CODE' ),
			'countrycode3' => getenv( 'HTTP_GEOIP_COUNTRY_CODE3' ),
			'countryname'  => getenv( 'HTTP_GEOIP_COUNTRY_NAME' ),
			'latitude'     => getenv( 'HTTP_GEOIP_LATITUDE' ),
			'longitude'    => getenv( 'HTTP_GEOIP_LONGITUDE' ),
			'areacode'     => getenv( 'HTTP_GEOIP_AREA_CODE' ),
			'region'       => getenv( 'HTTP_GEOIP_REGION' ),
			'city'         => getenv( 'HTTP_GEOIP_CITY' ),
			'postalcode'   => getenv( 'HTTP_GEOIP_POSTAL_CODE' ),
		);
	}

	/**
	 * Get Country
	 *
	 * @return string Two-letter country code, e.g.) US for the United States of America
	 */
	public function country() {
		return $this->geos[ 'countrycode' ];
	}

	/**
	 * Get Region
	 *
	 * @return string Two-letter region code. e.g.) CA for California
	 */
	public function region() {
		return $this->geos[ 'region' ];
	}

	/**
	 * Get City
	 *
	 * @return mixed Description
	 */
	public function city() {
		return $this->geos[ 'city' ];
	}

	/**
	 * Get Postal Code
	 *
	 * @return mixed Description
	 */
	public function postal_code() {
		return $this->geos[ 'postalcode' ];
	}

	/**
	 * Get Latitude
	 *
	 * @return mixed Description
	 */
	public function latitude() {
		return $this->geos[ 'latitude' ];
	}

	/**
	 * Get Longitude
	 *
	 * @return mixed Description
	 */
	public function longitude() {
		return $this->geos[ 'longitude' ];
	}

	/**
	 * Register the shortcode(s)
	 *
	 * @since  0.5.0
	 * @uses add_shortcode()
	 * @return null
	 */
	public function action_init_register_shortcodes() {

		// Country Shortcode
		if ( ! shortcode_exists( self::SHORTCODE_COUNTRY ) ) {
			add_shortcode( self::SHORTCODE_COUNTRY, array( $this, 'do_shortcode_country' ) );
		}

		// Region Shortcode
		if ( ! shortcode_exists( self::SHORTCODE_REGION ) ) {
			add_shortcode( self::SHORTCODE_REGION, array( $this, 'do_shortcode_region' ) );
		}

		// City Shortcode
		if ( ! shortcode_exists( self::SHORTCODE_CITY ) ) {
			add_shortcode( self::SHORTCODE_CITY, array( $this, 'do_shortcode_city' ) );
		}

		// Postal Code Shortcode
		if ( ! shortcode_exists( self::SHORTCODE_POSTAL_CODE ) ) {
			add_shortcode( self::SHORTCODE_POSTAL_CODE, array( $this, 'do_shortcode_postal_code' ) );
		}

		// Latitude Shortcode
		if ( ! shortcode_exists( self::SHORTCODE_LATITUDE ) ) {
			add_shortcode( self::SHORTCODE_LATITUDE, array( $this, 'do_shortcode_latitude' ) );
		}

		// Longitude Shortcode
		if ( ! shortcode_exists( self::SHORTCODE_LONGITUDE ) ) {
			add_shortcode( self::SHORTCODE_LONGITUDE, array( $this, 'do_shortcode_longitude' ) );
		}

		// Smart Location Shortcode
		if ( ! shortcode_exists( self::SHORTCODE_LOCATION ) ) {
			add_shortcode( self::SHORTCODE_LOCATION, array( $this, 'do_shortcode_location' ) );
		}

	}

	/**
	 * Output the current country
	 *
	 * @since  0.5.0
	 * @return string Two-letter country code
	 */
	function do_shortcode_country( $atts ) {
		if( isset( $this->geos[ 'countrycode' ] ) ) {
			return $this->country();
		}
		return '[' . self::SHORTCODE_COUNTRY . ']';
	}

	/**
	 * Output the current region
	 *
	 * @since  0.5.0
	 * @return string Two-letter region code
	 */
	function do_shortcode_region( $atts ) {
		if( isset( $this->geos[ 'region' ] ) ) {
			return $this->region();
		}
		return '[' . self::SHORTCODE_REGION . ']';
	}

	/**
	 * Output the current city
	 *
	 * @since  0.5.0
	 * @return string City name
	 */
	function do_shortcode_city( $atts ) {
		if( isset( $this->geos[ 'city' ] ) ) {
			return $this->city();
		}
		return '[' . self::SHORTCODE_CITY . ']';
	}

	/**
	 * Output the current postal code
	 *
	 * @since  0.5.0
	 * @return string postal code
	 */
	function do_shortcode_postal_code( $atts ) {
		if( isset( $this->geos[ 'postalcode' ] ) ) {
			return $this->postal_code();
		}
		return '[' . self::SHORTCODE_POSTAL_CODE . ']';
	}

	/**
	 * Output the current latitude
	 *
	 * @since  0.5.0
	 * @return string latitude
	 */
	function do_shortcode_latitude( $atts ) {
		if( isset( $this->geos[ 'latitude' ] ) ) {
			return $this->latitude();
		}
		return '[' . self::SHORTCODE_LATITUDE . ']';
	}

	/**
	 * Output the current longitude
	 *
	 * @since  0.5.0
	 * @return string longitude
	 */
	function do_shortcode_longitude( $atts ) {
		if( isset( $this->geos[ 'longitude' ] ) ) {
			return $this->longitude();
		}
		return '[' . self::SHORTCODE_longitude . ']';
	}

	/**
	 * Output the current human readable location, in a smart way.
	 *
	 * @since  0.5.0
	 * @return string $html
	 */
	function do_shortcode_location( $atts ) {

		$city = $this->city();
		if( isset( $city ) && ! empty( $city ) ) {
			return trim( $this->city() . ', ' . $this->region() . ' ' . $this->country() );
		}
		//Fallback
		return trim( $this->region() . ' ' . $this->country() );
	}

	/**
	 * Checks if environment variable depencies are available on the server
	 *
	 * @since  0.5.0
	 */
	public function action_admin_init_check_plugin_dependencies() {
		// Check to see if the environment variables are present
		$is_wpe = getenv( 'HTTP_GEOIP_COUNTRY_CODE' );
		if( ! isset( $is_wpe ) || empty( $is_wpe ) ) {
			$this->admin_notices[] = __( 'Please note - this plugin will only function on your <a href="http://wpengine.com/plans/?utm_source=' . self::TEXT_DOMAIN . '">WP Engine account</a>. This will not function outside of the WP Engine environment. Plugin <b>deactivated.</b>', self::TEXT_DOMAIN );
		}
		unset( $is_wpe );
	}

	/**
	 * Displays notice in the admin area if the dependent environment variables are not present
	 *
	 * @since  0.5.0
	 */
	public function action_admin_notices() {
		if( 0 < count( $this->admin_notices ) ) {
			// Hide the activation message
			echo '<style>.wrap .updated{display:none;}</style>';

			// Display the errors
			echo '<div class="error">';
			foreach( $this->admin_notices as $notice ) {
				echo "<p>$notice</p>";
			}
			echo '</div>';

			// Disable this plugin
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}

}

// Register the GeoIP instance
GeoIp::init();