<?php

namespace WPEngine;

class wpengineGeoipTest extends \WP_UnitTestCase
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
}
