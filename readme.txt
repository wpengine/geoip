=== WP Engine GeoIP ===
Contributors: wpengine, markkelnar, stevenkword, stephenlin
Tags: wpe, wpengine, geoip, localization, geolocation
Requires at least: 3.0.1
Tested up to: 4.1.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create a personalized user experienced based on location.

== Description ==

WP Engine GeoIP integrates with the variables on your WP Engine site to display content catered to the visitor’s location. With the ability to access variables from as broad as country to as specific as latitude and longitude, your website can now display geographically relevant content.

= Geo-Marketing =

* Create marketing campaigns targeted only at certain locations.

= Localization =

* Redirect incoming traffic to content in the local language or currency.
* Businesses with local branches can direct customers to a relevant physical location or local microsite.

= Ecommerce =

* Filter out merchandise or services that are not available in a certain locale.
* Display country-specific shipping, tax, or sales information.

= Legal Requirements =

* Filter required legal notices from countries for whom those notices may not be relevant.

= ** Please Note =

This plugin will only function on your [WP Engine](http://wpengine.com/plans/?utm_source=wpengine-geoip) Business, Premium or Enterprise level account. This will not function outside of the WP Engine environment.

== Installation ==

1. Upload `geoip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== How To Use The Shortcode ==

1) Country: [geoip-country]

2) Region (State): [geoip-region]

3) City: [geoip-city]

4) Postal Code: [geoip-postalcode]

5) Latitude: [geoip-latitude]

6) Longitutde: [geoip-longitude]

7) Location: [geoip-location]

== Frequently Asked Questions ==

1) Will this work outside of a WP Engine account?

No, this will only work within a WP Engine environment.

2) Are there any other restrictions to using this plugin?

Yes, the GeoIP variables on the server are available to Business, Premium and Enterprise customers. Personal and Professional customers, please contact the [Support Team](https://my.wpengine.com/support#general-issue) if you are interested in GeoIP.

3) What variables do I have access to?

Country, state, city, zip, latitude, longitude.

4) How do I sign up for a WP Engine Account?:

That’s easy! [Click here](http://wpengine.com/plans/?utm_source=wpengine-geoip).

5) I installed the plugin and code and it isn’t working.

Please contact the [Support Team](https://my.wpengine.com/support#general-issue).

== Screenshots ==

1. Authoring a new post with GeoIP shortcodes
2. An example post using GeoIP shortcodes

== Changelog ==

= 1.0.0 =
- Initial release

= 0.7.0 =
- Removes plugin dependency management artifacts

= 0.6.0 =
- Add shortcodes for postal code, latitude and longitude.

= 0.5.0 =
- Adds shortcodes for city, region, and country.
- Displays admin notice when GEOIP environment variables are absent.
- Formatting updates to readme and file headers.

= 0.4.0 =
- Code cleanup for WordPress coding standards and white space.

= 0.3.0 =
- Change action to react at 'init'.

= 0.2.0 =
- Add static function and singleton construction.

= 0.1.0 =
- Initial version