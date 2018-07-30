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
    
	/**
	 * Test display of admin notice
	 */
	public function test_action_admin_notices() {
		ob_start();
		$geo = new $this->class_name();
		$geo->setup();
		$geo->action_admin_init_check_plugin_dependencies();
		$geo->action_admin_notices();
		$expected_output = array(
			'<div class="notice notice-warning wpengine-geoip is-dismissible" data-key="dependency">',
			'<p>',
			'WP Engine GeoTarget requires a <a href="http://wpengine.com/plans/?utm_source=wpengine-geoip">WP Engine account</a> with GeoTarget enabled for full functionality. Only testing queries will work on this site.',
			'</p>',
			'</div>',
		);
		$actual_output = $this->getActualOutput();
		ob_end_clean();
		foreach ($expected_output as $line) {
			$this->assertContains( $line, $actual_output );
		}
	}
}
