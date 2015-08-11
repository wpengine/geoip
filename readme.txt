=== WP Engine GeoIP ===
Contributors: wpengine, markkelnar, stevenkword, stephenlin, ryanshoover, taylor4484
Tags: wpe, wpengine, geoip, localization, geolocation
Requires at least: 3.0.1
Tested up to: 4.3
Stable tag: 1.1.2
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

= * Please Note * =

This plugin will only function on your [WP Engine](http://wpengine.com/plans/?utm_source=wpengine-geoip) Business, Premium or Enterprise level account. This will not function outside of the WP Engine environment.

== Installation ==

1. Upload `wpengine-geoip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Please view the 'Other Notes' tab to see all of the available GeoIP shortcodes


== Location Variable Shortcodes ==
You can use any of the following location variable shortcodes to return the various geographic location the user is visiting your site from:

1) Continent: `[geoip-continent]`

2) Country: `[geoip-country]`

3) Region: `[geoip-region]`

* In the US region will return States
* In Canada region will return Providences
* Outside the US/CA this will return a Region number. Please note region numbers are not unique between countries

4) City: `[geoip-city]`

5) Postal Code: `[geoip-postalcode]`

* This variable is only available in the US due to limitations with the location data GeoIP uses

6) Latitude: `[geoip-latitude]`

7) Longitutde: `[geoip-longitude]`

8) Location: `[geoip-location]`

= Example =
`Hi, and welcome to [geoip-city]! The place to be in [geoip-region],[geoip-country].`
A visitor from Austin, Texas would see the following:
`Hi, and welcome to Austin! The place to be in TX, US.`

== Localized Content ==

9) Content: `[geoip-content country="US"]Your US specific content goes here[/geoip-content]`
The content shortcode allows you to hide or show specific content based on visitor geographies:

Below are all the supported geography options, this allows to you SHOW content for only specific locations:

* continent
* country
* areacode
* region
* city
* postalcode

Below are all the supported negative geography options, this allows to you HIDE content for only specific locations:

* not_continent
* not_country
* not_areacode
* not_region
* not_city
* not_postalcode

= Examples of the Content Shortcode =
This will display “Content just for US visitors” strictly for visitors viewing from the United States. 
`[geoip-content country="US"] Content just for US visitors [/geoip-content]`


This will display “Content just for everyone in Texas and California” strictly for visitors from Texas and California.
`[geoip-content region="TX, CA."] Content just for everyone in Texas and California [/geoip-content]`


You can mix and match geography and negative geography options to create verbose logic in a single shortcode:
`[geoip-content country="US" not-city="Austin"]Content for US visitors but not for visitors in Austin[/geoip-content]`

= Limitations =

There is a single limitation in the logic that lets you filter content for multiple geographic areas.

You can progressively limit the area that content is shown in. But once your content is hidden from an area, a subset of that area can't be added back in.

For example,
If I limit my content to Europe, then limit my content from Great Britain, I can't go back and show it to London.

= Creative Work Arounds =

== Limit content to some regions of a country (or some cities of a state) ==

You want to show an offer for free shipping to every state in the US *but* Alaska and Hawaii. You may be inclined to write something like

**BAD**

```
[geoip_content country="US" not_state="AK, HI"]Lorem ipsum dolor sit amet[/geoip_content]
```

Instead, show it to all other 48 states

**GOOD**

```
[geoip_content state="AL, AZ, AR, CA, CO, CT, DE, FL, GA, ID, IL, IN, IA, KS, KY, LA, ME, MD, MA, MI, MN, MS, MO, MT, NE, NV, NH, NJ, NM, NY, NC, ND, OH, OK, OR, PA, RI, SC, SD, TN, TX, UT, VT, VA, WA, WV, WI, WY"]Free shipping on all orders over $50![/geoip_content]
```

== Duplicate location names ==

You want to show discount airfare on a flight to Paris, France. The content should show to all of the US and France, but not Paris itself. 

**BAD**

```
[geoip_content country="US, FR" not_city="Paris"]Fly to Paris for only $199![/geoip_content]
```

The problem here is that Paris, Texas will be hidden. The solution? Just have two geoip_content shortcodes.

**GOOD**

```
[geoip_content country="FR" not_city="Paris"]Fly to Paris for only $199![/geoip_content][geoip_content country="US"]Fly to Paris for only $199![/geoip_content]
```
== Adding an area into an omitted region ==

You want to show an ad written in Spanish to all of South America except for Brazil. Brasilia, however, has enough Spanish speakers that you want to include Brasilia.

**BAD**

```
[geoip_content continent="SA" not_country="BR" city="Brasilia"]Lorem ipsum dolor sit amet[/geoip_content]
```

**GOOD**

```
[geoip_content continent="SA" not_country="BR"]Venta de la Navidad en los adaptadores USB[/geoip_content]
[geoip_content city="Brasilia"]Venta de la Navidad en los adaptadores USB[/geoip_content]
```

== Testing Parameters ==
You can use the following URL parameters to test how your localized content will appear to visitors from various geographic locations. You can add any of the parameters below to any URL of a page using the GeoIP shortcodes or API calls:

Spoof visitor from the state of Texas:
`yourdomain.com/?geoip&region=TX`

Spoof visitor from the United States:
`yourdomain.com/?geoip&country=US`

Spoof visitor from Austin, Texas
`yourdomain.com/?geoip&city=Austin`

Spoof visitor from the U.S. zip code 78701:
`yourdomain.com/?geoip&zip=78701`


Please note: full page redirects and TLD redirects still need to be implemented with the necessary API calls.

== Frequently Asked Questions ==

1) Will this work outside of the WP Engine hosting account?

No, this will only work within the WP Engine environment. This will not work for sites hosted on other web hosts.

2) Are there any other restrictions to using this plugin?

Yes. Even though the GeoIP variables on the server are available to Business, Premium and Enterprise customers, you will still need to reach out to the [Support Team](https://my.wpengine.com/support#general-issue) to fully enable GeoIP for your site.

For Personal and Professional customers who are interested in GeoIP, please contact the [Support Team](https://my.wpengine.com/support#general-issue) as well.

3) What variables do I have access to?

Continent, country, state, city, zip, latitude, longitude.

4) How do I sign up for a WP Engine Account?:

That’s easy! [Signup here](http://wpengine.com/plans/?utm_source=wpengine-geoip).

5) I installed the plugin and used a shortcode or API call and it isn’t working.

Please contact the WP Engine [Support Team](https://my.wpengine.com/support#general-issue).

== Screenshots ==

1. Authoring a new post with GeoIP shortcodes
2. An example post using GeoIP shortcodes

== Changelog ==

= 1.1.2 =
- Fixes logic for nested parameter selectors in content shortcode
- Now supports nested shortcodes. You can use shortcodes inside the [geoip-content] shortcode
- Updated Documentation
- Bumps version number for WP 4.3 compatibility

= 1.1.1 =
- Fixes logic for negated parameters in content shortcode
- Allows the plugin to run on development sites

= 1.1.0 =
- Adds continent shortcode
- Adds content shortcode for localized geographic content
- Adds testing parameters to spoof visitor location
- Bumps version number for WP 4.2.2 compatibility

= 1.0.2 =
- Renames longitude environment variable
- Bumps version number for WP 4.2 compatibility

= 1.0.1 =
- Changes to readme.txt

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

== Upgrade Notice ==

= 1.1.2 =
- Fixes logic for nested parameter selectors in content shortcode
- Now supports nested shortcodes. You can use shortcodes inside the [geoip-content] shortcode
- Updated Documentation
