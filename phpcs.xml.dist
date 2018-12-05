<?xml version="1.0"?>
<ruleset name="WP Engine GeoTarget" namespace="WPEngine\GeoIp\Standard">
    <!-- Check WP Standards -->
    <rule ref="WordPress"/>

    <!-- Check PHP version compatibility for 5.6 and higher -->
    <rule ref="PHPCompatibility"/>
    <config name="testVersion" value="5.6-"/>

    <!-- Excluded these dirs -->
    <exclude-pattern>./build</exclude-pattern>
    <exclude-pattern>./test</exclude-pattern>
    <exclude-pattern>./vendor</exclude-pattern>

    <!-- Show sniff codes in all reports -->
	<arg value="s"/>
	<!-- Up the Memory limit for large plugins -->
	<ini name="memory_limit" value="128M"/>
</ruleset>
