# WP Engine GeoTarget
[![Build Status](https://travis-ci.org/wpengine/geoip.svg?branch=master)](https://travis-ci.org/wpengine/geoip) [![codecov](https://codecov.io/gh/wpengine/geoip/branch/master/graph/badge.svg)](https://codecov.io/gh/wpengine/geoip)

WP Engine GeoTarget integrates with the variables on your WP Engine site to display content catered to the visitor’s location. With the ability to access variables from as broad as country to as specific as latitude and longitude, your website can now display geographically relevant content.

## Use cases

### Geo-Marketing

* Create marketing campaigns targeted only at certain locations.

### Localization

* Redirect incoming traffic to content in the local language or currency.
* Businesses with local branches can direct customers to a relevant physical location or local microsite.

### Ecommerce

* Filter out merchandise or services that are not available in a certain locale.
* Display country-specific shipping, tax, or sales information.

### Legal Requirements

* Filter required legal notices from countries for whom those notices may not be relevant.

### * Please Note *

If you are signed into a Premium or Enterprise plan, you can use this plugin at no additional cost. If you are on another plan type and would like to use GeoTarget on one of your sites, you can add it to your plan [here](http://wpengine.com/plans/?utm_source=wpengine-geoip). This will not function outside of the WP Engine environment.

## Installation

1. Upload `wpengine-geoip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## How to use

### Location Variable Shortcodes

You can use any of the following location variable shortcodes to return the various geographic location the user is visiting your site from:

1. Continent: `[geoip-continent]`

2. Country: `[geoip-country]`

3. Region: `[geoip-region]`
  * In the US region will return States
  * In Canada region will return Provinces
  * Outside the US/CA this will return a Region number. Please note region numbers are not unique between countries

4. City: `[geoip-city]`

5. Postal Code: `[geoip-postalcode]`
  * This variable is only available in the US due to limitations with the location data GeoTarget uses

6. Latitude: `[geoip-latitude]`

7. Longitude: `[geoip-longitude]`

8. Location: `[geoip-location]`

#### Example

**In your post editor:**

> Hi, and welcome to [geoip-city]! The place to be in [geoip-region],[geoip-country].

**A visitor from Austin, Texas would see:**

> Hi, and welcome to Austin! The place to be in TX, US.

### Localized Content

The content shortcode allows you to hide or show specific content based on visitor geographies:

> [geoip-content country="US"]Your US specific content goes here[/geoip-content]

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

#### Examples of the Content Shortcode

This will display “Content just for US visitors” strictly for visitors viewing from the United States.

> [geoip-content country="US"] Content just for US visitors [/geoip-content]

This will display “Content just for everyone in Texas and California” strictly for visitors from Texas and California.

> [geoip-content region="TX, CA."] Content just for everyone in Texas and California [/geoip-content]

You can mix and match geography and negative geography options to create verbose logic in a single shortcode:

> [geoip-content country="US" not_city="Austin"]Content for US visitors but not for visitors in Austin[/geoip-content]

#### Limitation

There is a limitation in the logic that lets you filter content for multiple geographic areas.

You can progressively limit the area that content is shown in. But once your content is hidden from an area, a subset of that area can't be added back in.

For example,
If I localize content so that it shows to all of Europe, then prevent my content from showing in Great Britain, I can't go back and show it to London.

#### Creative Work Arounds

Limit content to some regions of a country (or some cities of a state)

##### Example 1

You want to show an offer for free shipping to every state in the US *but* Alaska and Hawaii. You may be inclined to write something like

*Instead of:*

> [geoip-content country="US" not_state="AK, HI"]Lorem ipsum dolor sit amet[/geoip-content]

*Show it to all 48 states:*

> [geoip-content state="AL, AZ, AR, CA, CO, CT, DE, FL, GA, ID, IL, IN, IA, KS, KY, LA, ME, MD, MA, MI, MN, MS, MO, MT, NE, NV, NH, NJ, NM, NY, NC, ND, OH, OK, OR, PA, RI, SC, SD, TN, TX, UT, VT, VA, WA, WV, WI, WY"]Free shipping on all orders over $50![/geoip-content]

##### Example 2

You want to show discount airfare on a flight to Paris, France. The content should show to all of the US and France, but not Paris itself.

*Instead of:*

> [geoip-content country="US, FR" not_city="Paris"]Fly to Paris for only $199![/geoip-content]

The problem here is that Paris, Texas will be hidden. The solution?

*Just have two geoip-content shortcodes:*

> [geoip-content country="FR" not_city="Paris"]Fly to Paris for only $199![/geoip-content]
> [geoip-content country="US"]Fly to Paris for only $199![/geoip-content]

##### Example 3

You want to show an ad written in Spanish to all of South America except for Brazil. Brasilia, however, has enough Spanish speakers that you want to include Brasilia.

*Instead of:*

> [geoip-content continent="SA" not_country="BR" city="Brasilia"]Lorem ipsum dolor sit amet[/geoip-content]

*Just have two geoip-content shortcodes:*

> [geoip-content continent="SA" not_country="BR"]Venta de la Navidad en los adaptadores USB[/geoip-content]
> [geoip-content city="Brasilia"]Venta de la Navidad en los adaptadores USB[/geoip-content]

## Additional features

### Calculate distance between points

You have a utility function that will calculate the distance from your provided lat/long coordinate to the visitor's location in either miles or kilometers. This can be useful for determining approximate distances, as results may be cached at the state or country level, depending on your configuration.

#### Example

```php
$latitude  = 30.268246;
$longitude = -97.745992;
$geo = WPEngine\GeoIp::instance();
if ( false !== $geo->distance_to( $latitude, $longitude ) ) {
    $miles_to_wp_engine = $geo->distance_to( $latitude, $longitude );
}
```

### Test pages from other locations

You can use the following URL parameters to test how your localized content will appear to visitors from various geographic locations. You can add any of the parameters below to any URL of a page using the GeoTarget shortcodes or API calls:

#### Examples

Spoof visitor from the state of Texas:

```http
https://yourdomain.com/?geoip&region=TX
```

Spoof visitor from the United States:

```http
yourdomain.com/?geoip&country=US
```

Spoof visitor from Austin, Texas

```http
yourdomain.com/?geoip&city=Austin
```

Spoof visitor from the U.S. zip code 78701:

```http
yourdomain.com/?geoip&zip=78701
```

## Frequently Asked Questions

1. Will this work outside of the WP Engine hosting account?

    > No, this will only work within the WP Engine environment. This will not work for sites hosted on other web hosts.

2. Are there any other restrictions to using this plugin?

    > Yes. If you are signed into a Premium or Enterprise plan, you can use this plugin at no additional cost. If you are on another plan type and would like to use GeoTarget on one of your sites, you can add it to your plan [here](http://wpengine.com/plans/?utm_source=wpengine-geoip). For all plan types, just reach out to the [Support Team](https://my.wpengine.com/support) to enable GeoTarget for your site.
    > You can read our full GeoTarget activation guide [here](https://wpengine.com/support/geoip-personalizing-content-based-geography/).

3. What variables do I have access to?

    > Continent, country, state, city, zip, latitude, longitude.

4. How do I sign up for a WP Engine Account?:

    > That’s easy! [Signup here](http://wpengine.com/plans/?utm_source=wpengine-geoip).

5. I installed the plugin and used a shortcode or API call and it isn’t working.

    > Please contact the WP Engine [Support Team](https://my.wpengine.com/support).

## Contributing

Running `make` from the repository root will download dependencies, lint, and test the plugin. `make build` will package the plugin as a zip and place it in the `/build` directory.
