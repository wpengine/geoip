<?php
/*
Plugin Name: WP Engine GeoIP
Version: 1.1.2
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

	// The path to the plugin. Let's just make that function call once.
	private $geoip_path;

	// The geographical data loaded from the environment
	public $geos;

	// A list of countries and their continents
	public $countries;

	// WP-Admin errors notices
	private $admin_notices = array();

	// Shortcodes
	const SHORTCODE_CONTINENT	= 'geoip-continent';
	const SHORTCODE_COUNTRY     = 'geoip-country';
	const SHORTCODE_REGION      = 'geoip-region';
	const SHORTCODE_CITY        = 'geoip-city';
	const SHORTCODE_POSTAL_CODE = 'geoip-postalcode';
	const SHORTCODE_LATITUDE    = 'geoip-latitude';
	const SHORTCODE_LONGITUDE   = 'geoip-longitude';
	const SHORTCODE_LOCATION    = 'geoip-location';
	const SHORTCODE_CONTENT 	= 'geoip-content';

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

		$this->geoip_path = plugin_dir_path( __FILE__ );

		// Get our array of countries and continents
		require_once( $this->geoip_path . '/inc/country-list.php' );

		$this->countries = apply_filters( 'geoip_country_list', geoip_country_list() );

		$this->geos = $this->get_actuals();

		$this->geos = $this->get_test_parameters( $this->geos );

		$this->geos = apply_filters( 'geoip_location_values', $this->geos );
	}

	/**
	 * Here we extract the data from headers set by nginx -- lets only send them if they are part of the cache key
	 *
	 * @since 0.1.0
	 * @return array All of the GeoIP related environment variables available on the current server instance
	 */
	public function get_actuals() {

		$geos = array(
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

		$geos['active'] = ( isset( $geos['countrycode'] ) && false !== $geos['countrycode'] )  ? true : false;

		$geos['continent'] = $this->continent( $geos['countrycode'] );

		return $geos;
	}

	/**
	 * We want people to be able to test the plugin, so we'll include some url parameters that will spoof a location
	 *
	 * @since 1.1.0
	 * @return array modified version of the GeoIP location array based on url parameters
	 */
	public function get_test_parameters( $geos ) {

		$params = $_GET;

		if( !isset( $params['geoip'] ) ) {
			return $geos;
		}

		foreach( $params as $key => $value ) {

			$key = $this->match_label_synonyms( $key );

			if( isset( $geos[ $key ] ) ) {
				$geos[ $key ] = $value;
			}
		}

		return $geos;
	}

	/**
	 * Get Continent
	 *
	 * @since 1.1.0
	 * @return string Two-letter continent code, e.g. EU for Europe
	 */
	public function continent( $country = '' ) {

		$continent = '';

		if( empty( $country ) ) {
			$country = $this->geos[ 'countrycode' ];
		}

		if( isset( $this->countries[ $country ] ) ) {
			$continent = $this->countries[ $country ]['continent'];
		}

		return $continent;
	}

	/**
	 * Get Country
	 *
	 * @since 0.5.0
	 * @return string Two-letter country code, e.g.) US for the United States of America
	 */
	public function country() {
		return $this->geos[ 'countrycode' ];
	}

	/**
	 * Get Region
	 *
	 * @since 0.5.0
	 * @return string Two-letter region code. e.g.) CA for California
	 */
	public function region() {
		return $this->geos[ 'region' ];
	}

	/**
	 * Get City
	 *
	 * @since 0.5.0
	 * @return mixed Description
	 */
	public function city() {
		return $this->geos[ 'city' ];
	}

	/**
	 * Get Postal Code
	 *
	 * @since 0.6.0
	 * @return mixed Description
	 */
	public function postal_code() {
		return $this->geos[ 'postalcode' ];
	}

	/**
	 * Get Latitude
	 *
	 * @since 0.6.0
	 * @return mixed Description
	 */
	public function latitude() {
		return $this->geos[ 'latitude' ];
	}

	/**
	 * Get Longitude
	 *
	 * @since 0.6.0
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

		// Continent Shortcode
		if ( ! shortcode_exists( self::SHORTCODE_CONTINENT ) ) {
			add_shortcode( self::SHORTCODE_CONTINENT, array( $this, 'do_shortcode_continent' ) );
		}

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

		// Smart Location Shortcode
		if ( ! shortcode_exists( self::SHORTCODE_CONTENT ) ) {
			add_shortcode( self::SHORTCODE_CONTENT, array( $this, 'do_shortcode_content' ) );
		}
	}

	/**
	 * Output the current continent
	 *
	 * @since 1.1.0
	 * @return string Two-letter continent code
	 */
	function do_shortcode_continent( $atts ) {
		$continent = '[' . self::SHORTCODE_CONTINENT . ']';

		$country = $this->geos[ 'countrycode' ];

		if( isset( $this->countries[ $country ] ) ) {
			$continent = $this->countries[ $country ]['continent'];
		}
		return $continent;
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
	 * @since  0.6.0
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
	 * @since  0.6.0
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
	 * @since  0.6.0
	 * @return string longitude
	 */
	function do_shortcode_longitude( $atts ) {
		if( isset( $this->geos[ 'longitude' ] ) ) {
			return $this->longitude();
		}
		return '[' . self::SHORTCODE_LONGITUDE . ']';
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
	 * Output the content filtered by region
	 *
	 * @since 1.1.0
	 * @return string HTML
	 */
	function do_shortcode_content( $atts, $content = null ) {

		$keep = true;

		$test_parameters = array();

		// Process and organzie the test parameters
		foreach( $atts as $label => $value ) {

			// Intialize our negation parameters
			$negate = 0;
			$inline_negate = 0;

			// Check to see if the attribute has "not" in it
			$negate = preg_match( '/not?[-_]?(.*)/', $label, $matches );

			// WordPress doesn't like a dash in shortcode parameter labels
			// Just in case, check to see if the value has "not-" in it
			if( ! $negate ) {
				$inline_negate = $negate = preg_match( '/not?\-([^=]+)\=\"?([^"]+)\"?/', $value, $matches );
			}

			// Label after the negation match
			$label = $negate ? $matches[1] : $label;

			// Value after the negation match
			$value = $inline_negate ? $matches[2] : $value;

			// Replace common synonyms with our values
			$label = $this->match_label_synonyms( $label );

			// Abort if the label doesn't match
			if( !isset( $this->geos[ $label ] ) ) {
				continue;
			}

			// Find out if the value is comma delimited
			$test_values = (array) explode( ',',  $value );

			// Add the value to the test parameters
			$test_parameters[ $label ] = array(
				'test_values' => $test_values,
				'negate' => $negate,
				);
		}

		// Sort the test parameters by region type – largest to smallest
		uksort( $test_parameters, array( $this, 'compare_location_type' ) );

		$test_parameters = apply_filters( 'geoip_test_parameters', $test_parameters, $atts );

		// Process through parameters, testing to see if we have a match
		foreach( $test_parameters as $label => $parameter ) {

			$test_values = $parameter['test_values'];

			$negate = $parameter['negate'];

			// Sanitize the match value
			$match_value = strtolower( $this->geos[ $label ] );

			// Sanitize the test values
			foreach( $test_values as &$test_value ) {
				$test_value = strtolower( trim( $test_value, " \t\"." ) );
			}

			$is_match = in_array( $match_value, $test_values );

			$is_match = ! $negate ? $is_match : ! $is_match;

			if( ! $is_match ) {
				$keep = false;
			}
		}

		if( ! $keep ) {
			return '';
		}

		// Process any shortcodes in the content
		$content = do_shortcode( $content );

		return apply_filters( 'geoip_content', $content, $atts );
	}

	/**
	 * Compare the location types
	 *
	 * Used for sorting location types from largest area to smallest area
	 *
	 * @since 1.1.2
	 */
	public function compare_location_type( $a, $b ) {
		$location_types = array(
			'continent'    => 1,
			'countrycode'  => 2,
			'countrycode3' => 2,
			'countryname'  => 2,
			'region'       => 3,
			'areacode'     => 4,
			'city'         => 5,
			'postalcode'   => 6,
			);

		if( isset( $location_types[ $a ] ) && isset( $location_types[ $b ] ) ) {
			return $location_types[ $a ] - $location_types[ $b ];
		} else {
			return 0;
		}
	}

	/**
	 * Checks if environment variable depencies are available on the server
	 *
	 * @todo Include link to query documentation when available on the Plugin Directory
	 * @since  0.5.0
	 */
	public function action_admin_init_check_plugin_dependencies() {

		if( ! $this->geos['active'] ) {
			$this->admin_notices[] = __( 'WP Engine GeoIP requires a <a href="http://wpengine.com/plans/?utm_source=' . self::TEXT_DOMAIN . '">WP Engine account</a> for full functionality. Only testing queries will work on this site.', self::TEXT_DOMAIN );
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

			// Display the notices
			echo '<div class="error notice is-dismissible">';

			foreach( $this->admin_notices as $notice ) {
				echo "<p>$notice</p>";
			}

			echo '</div>';
		}
	}

	/**
	 * As a favor to users, let's match some common synonyms
	 *
	 * @since 1.1.0
	 * @return string label
	 */
	private function match_label_synonyms( $label ) {

		if( 'country' == $label ) {
			$label = 'countrycode';
		}

		if( 'state' == $label ) {
			$label = 'region';
		}

		if( 'zipcode' == $label || 'zip' == $label ) {
			$label = 'postalcode';
		}

		return $label;
	}
}

// Register the GeoIP instance
GeoIp::init();
