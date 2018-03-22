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
	 * Name of class under test
	 *
	 * @var string
	 */
	private $class_name = 'WPEngine\GeoIp';

	/**
	 * Test if class can be instantiated
	 */
	public function test_can_instantiate() {
		$this->assertTrue( class_exists( $this->class_name ) );

		$geo = GeoIp::instance();
		$this->assertEquals( $this->class_name, get_class( $geo ) );
	}

}
