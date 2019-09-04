<?php
/**
 * This file helps to prevent any issues from file name change
 * wpengine-geoip.php -> class-geoip.php to adhere to WPCS
 *
 * @package wpengine-geoip
 */

namespace WPEngine;

// Exit if this file is directly accessed.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Replace plugin filename in options table
 */
function replace_previous_plugin_filename() {
	$active_plugins = get_option( 'active_plugins', array() );
	foreach ( $active_plugins as $key => $active_plugin ) {
		if ( strstr( $active_plugin, '/wpengine-geoip.php' ) ) {
			$active_plugins[ $key ] = str_replace( '/wpengine-geoip.php', '/class-geoip.php', $active_plugin );
			break;
		}
	}
	update_option( 'active_plugins', $active_plugins );
}
replace_previous_plugin_filename();
