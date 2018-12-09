<?php
/**
 * Test bootstrap
 *
 * @package wpengine-geoip
 */

/**
 * Set some GeoIP environment vars
 */
putenv('HTTP_GEOIP_COUNTRY_CODE=US');
putenv('HTTP_GEOIP_COUNTRY_NAME=United States');
putenv('HTTP_GEOIP_LATITUDE=30.40000');
putenv('HTTP_GEOIP_LONGITUDE=-97.75280');
putenv('HTTP_GEOIP_REGION=TX');
putenv('HTTP_GEOIP_CITY=Austin');
putenv('HTTP_GEOIP_POSTAL_CODE=78759');


/**
 * Set WordPress test environment location
 */
$_tests_dir = '/wordpress/tests/phpunit/includes';

/**
 * The WordPress tests functions.
 *
 * We are loading this so that we can add our tests filter
 * to load the plugin, using tests_add_filter().
 */
require_once $_tests_dir . '/functions.php';

/**
 * Manually load the plugin main file.
 *
 * The plugin won't be activated within the test WP environment,
 * that's why we need to load it manually.
 *
 * You will also need to perform any installation necessary after
 * loading your plugin, since it won't be installed.
 */
function _manually_load_plugin() {
	require __DIR__ . '/../src/class-geoip.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

/**
 * Sets up the WordPress test environment.
 *
 * We've got our action set up, so we can load this now,
 * and viola, the tests begin.
 */
require $_tests_dir . '/bootstrap.php';
