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
	private static $class_name = 'WPEngine\GeoIp';

	/**
	 * Test if class can be instantiated
	 */
	public function test_can_instantiate() {
		$this->assertTrue( class_exists( self::$class_name ) );
		$geo = GeoIp::instance();
		$this->assertEquals( self::$class_name, get_class( $geo ) );
	}

    /**
     * Verify shortcodes are added to WordPress
     *
     * @param string $slug Name of shortcode
     *
     * @dataProvider data_shortcode_slugs
     */
    public function test_shortcodes_exist($slug) {
        $this->assertTrue( shortcode_exists( "geoip-${slug}" ) );
    }

    public function data_shortcode_slugs() {
        return array(
            ['continent'],
            ['country'],
            ['region'],
            ['city'],
            ['postalcode'],
            ['latitude'],
            ['longitude'],
            ['location'],
            ['content'],
        );
    }


    /**
     * Test init
     *
     * @param string $hook Name of WordPress hook
     * @param string $method_name Method being hooked
     *
     * @dataProvider data_test_init
     */
    public function test_init($hook, $method_name) {
        // remove existing action
        $geoip = GeoIp::instance();
        $priority = has_action( $hook, array( $geoip, $method_name ) );
        if ( $priority ) {
            remove_action( $hook, array( $geoip, $method_name ), $priority );
        }

        // readd action using init then test
        $geoip::init();
        $this->assertInternalType( 'int', has_action( $hook, array( $geoip, $method_name ) ) );
    }

    public function data_test_init()
    {
        return array(
            ['init', 'setup'],
            ['init', 'action_init_register_shortcodes'],
            ['admin_enqueue_scripts', 'enqueue_admin_js'],
            ['admin_init', 'action_admin_init_check_plugin_dependencies'],
            ['admin_notices', 'action_admin_notices'],
            ['wp_ajax_geoip_dismiss_notice', 'ajax_action_dismiss_notice'],
        );
    }

    /**
     * Test enqueue_admin_js
     */
    public function test_enqueue_admin_js() {
        global $wp_scripts;

        // The script shouldn't be enqueued yet but alas dequeue before test
        wp_dequeue_script( GeoIp::TEXT_DOMAIN . '-admin-js' );

        $geoip_mock = $this->getMockBuilder( self::$class_name )
            ->disableOriginalConstructor()
            ->setMethods( array( 'helper_should_notice_show' ) )
            ->getMock();

        // Test when null/false return from helper_should_notice_show
        $geoip_mock->enqueue_admin_js();
        $is_enqueued = isset($wp_scripts->registered[GeoIp::TEXT_DOMAIN . '-admin-js']);
        $this->assertFalse($is_enqueued);

        $geoip_mock->expects( $this->once() )
            ->method( 'helper_should_notice_show' )
            ->willReturn( true );

        // Test for enqueue
        $geoip_mock->enqueue_admin_js();
        $is_enqueued = isset($wp_scripts->registered[GeoIp::TEXT_DOMAIN . '-admin-js']);
        $this->assertTrue($is_enqueued);

        // Test for localized script with nonce
        $nonce_var_string = false;
        $wp_dependency = $wp_scripts->registered[GeoIp::TEXT_DOMAIN . '-admin-js'];
        if (isset($wp_dependency->extra['data'])) {
            $nonce_var_string = $wp_dependency->extra['data'];
        }
        $this->assertContains('nonce', $nonce_var_string);
    }

	/**
	 * Test display of admin notice
	 */
	public function test_action_admin_notices() {
		ob_start();
		$geo = new self::$class_name();
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
		foreach ( $expected_output as $line ) {
			$this->assertContains( $line, $actual_output );
		}
	}
}
