<?php
/**
 * Unit tests for WP Engine GeoTarget Plugin
 *
 * @package wpengine-geoip
 */

namespace WPEngine;

/**
 * Unit tests for GeoIP class
 */
class GeoIp_Test extends \WP_UnitTestCase {

	/**
	 * Test if class can be instantiated
	 */
	public function test_can_instantiate() {
		$class_name = 'WPEngine\GeoIp';
		$this->assertTrue( class_exists( $class_name ) );

		$geo = GeoIp::instance();
		$this->assertEquals( $class_name, get_class( $geo ) );
	}

}
