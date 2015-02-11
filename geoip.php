<?php
/*
Plugin Name: WP Engine GeoIP
Version: 0.5.0
Description: Create a personalized user experienced based on location.
Author: WP Engine
Author URI: http://wpengine.com
Plugin URI: https://wordpress.org/plugins/wpe-geo-ip/
Text Domain: wpe-geo-ip
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

	// Dependency management
	private $plugin_dependencies   = array();
	private $depenencies_present   = false;
	private $admin_notices         = array();

	/* Shortcode */
	const SHORTCODE_COUNTRY = 'geoip-country';
	const SHORTCODE_REGION  = 'geoip-region';
	const SHORTCODE_CITY    = 'geoip-city';

	const TEXT_DOMAIN       = 'wpe-geo-ip';

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public static function init() {

		// Check for dependencies
		add_action( 'admin_init', array( self::instance(), 'action_admin_init_check_plugin_dependencies' ), 9999 ); // check late

		// Initialize
		add_action( 'init', array( self::instance(), 'setup' ) );
		add_action( 'init', array( self::instance(), 'action_init_register_shortcodes' ) );
	}

	/**
	 * [instance description]
	 * @return [type] [description]
	 */
	public static function instance() {
		// create a new object if it doesn't exist.
		is_null( self::$instance ) && self::$instance = new self;
		return self::$instance;
	}

	/**
	 * [setup description]
	 * @return [type] [description]
	 */
	public function setup() {
		$this->geos = $this->get_actuals();
	}

	/**
	 * Here we extract the data from headers set by nginx -- lets only send them if they are part of the cache key
	 *
	 * @return [type] [description]
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
	 * Examples of easy to use utility functions that we should have for each geo that is part of the cache key
	 *
	 * @return mixed Description
	 */
	public function country() {
		return $this->geos[ 'countrycode' ];
	}

	/**
	 * Region
	 *
	 * @return mixed Description
	 */
	public function region() {
		return $this->geos[ 'region' ];
	}

	/**
	 *
	 *
	 * @return mixed Description
	 */
	public function city() {
		return $this->geos[ 'city' ];
	}

	/**
	 * Register the shortcode(s)
	 *
	 * @since  1.0.0
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

	}

	/**
	 * Output the current country
	 *
	 * @since  1.0.0
	 * @return string $html
	 */
	function do_shortcode_country( $atts ) {
		if( isset( $this->geos[ 'country' ] ) ) {
			return $this->country();
		}
		return '[' . self::SHORTCODE_COUNTRY . ']';
	}

	/**
	 * Output the current region
	 *
	 * @since  1.0.0
	 * @return string $html
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
	 * @since  1.0.0
	 * @return string $html
	 */
	function do_shortcode_city( $atts ) {
		if( isset( $this->geos[ 'city' ] ) ) {
			return $this->city();
		}
		return '[' . self::SHORTCODE_CITY . ']';
	}


	/**
	 * [action_admin_init_check_plugin_dependencies description]
	 * @return [type] [description]
	 */
	public function action_admin_init_check_plugin_dependencies() {
		// Check to see if the environment variables are present
		if( empty( getenv( 'HTTP_GEOIP_COUNTRY_CODE' ) ) ) ) {
			$this->admin_notices[] = __( 'Please note - this plugin will only function on your <a href="http://wpengine.com/plans/?utm_source=' . self::TEXT_DOMAIN . '">WP Engine account</a>. This will not function outside of the WP Engine environment. Plugin <b>deactivated.</b>', self::TEXT_DOMAIN );
		}

		// If required plugins are not present, throw an error notice and bail
		if( ! $this->depenencies_present ) {
			add_action( 'admin_notices', array( self::instance(), 'action_admin_notices' ) );
			return;
		}
	}

	/**
	 * [action_admin_notices description]
	 * @return [type] [description]
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

// Register to do the stuff
GeoIp::init();
