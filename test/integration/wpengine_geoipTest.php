<?php

namespace WPEngine;

require_once 'src/wpengine-geoip.php';

class wpengine_geoipTest extends \WP_UnitTestCase
{
    /**
     * Verify that wpengine-geoip correctly replaces the old plugin filename
     */
    public function test_wpengine_geoip() {
        $active_plugins = array(
            '/wpengine-geoip.php',
        );
        update_option( 'active_plugins', $active_plugins );

        replace_previous_plugin_filename();
        $expected = array(
            '/class-geoip.php',
        );
        $actual = get_option( 'active_plugins', array() );
        $this->assertEquals($expected, $actual);
    }

    public function test_activate_when_multiple_active_plugins() {
        $active_plugins = array(
            '/foo.php',
            '/wpengine-geoip.php',
            '/bar.php'
        );
        update_option( 'active_plugins', $active_plugins );

        replace_previous_plugin_filename();
        $expected = array(
            '/foo.php',
            '/class-geoip.php',
            '/bar.php'
        );
        $actual = get_option( 'active_plugins', array() );
        $this->assertEquals($expected, $actual);
    }
}
