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
class GeoIp_SmokeTest extends \WP_UnitTestCase {
    public function test_shortcode_continent() {
        $this->assertEquals('NA', do_shortcode('[geoip-continent]'));
    }

    public function test_shortcode_country() {
        $this->assertEquals('US', do_shortcode('[geoip-country]'));
    }

    public function test_shortcode_region() {
        $this->assertEquals('TX', do_shortcode('[geoip-region]'));
    }

    public function test_shortcode_city() {
        $this->assertEquals('Austin', do_shortcode('[geoip-city]'));
    }

    public function test_shortcode_postalcode() {
        $this->assertEquals('78759', do_shortcode('[geoip-postalcode]'));
    }

    public function test_shortcode_latitude() {
        $this->assertEquals('30.40000', do_shortcode('[geoip-latitude]'));
    }

    public function test_shortcode_longitude() {
        $this->assertEquals('-97.75280', do_shortcode('[geoip-longitude]'));
    }

    public function test_shortcode_location() {
        $this->assertEquals('Austin, TX US', do_shortcode('[geoip-location]'));
    }

    public function test_shortcode_content() {
        // Test in a location where content is expected
        $expected = 'Hello, world!';
        $actual = do_shortcode('[geoip-content country="US"]Hello, world![/geoip-content]');
        $this->assertEquals($expected, $actual);

        // Test by negating a location
        $expected = '';
        $actual = do_shortcode('[geoip-content not_country="US"]Hello, world![/geoip-content]');
        $this->assertEquals($expected, $actual);

        // Test with a dashed negation
        $expected = '';
        $actual = do_shortcode('[geoip-content not-country="US"]Hello, world![/geoip-content]');
        $this->assertEquals($expected, $actual);

        // Test in a location where content is not expected
        $expected = '';
        $actual = do_shortcode('[geoip-content country="FR"]Hello, world![/geoip-content]');
        $this->assertEquals($expected, $actual);

        // Test with a label that we don't understand
        $expected = 'Hello, world!';
        $actual = do_shortcode('[geoip-content cooontry="US"]Hello, world![/geoip-content]');
        $this->assertEquals($expected, $actual);

        // Test with a list of locations
        $expected = 'Hello, world!';
        $actual = do_shortcode('[geoip-content country="FR,IE,AU,US"]Hello, world![/geoip-content]');
        $this->assertEquals($expected, $actual);
    }
}
