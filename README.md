# cables.gl Wordpress Plugin

Plugin to access patches of your cables.gl account in Wordpress

## Description

Plugin provides shortcodes, theme integration and the display patches that you
have created in your cables.gl account

## Installation

* [Download](https://github.com/cables-gl/cables-wordpress-plugin/releases) latest release .zip file
* Login as an Adminuser on your wordpress site
* Upload the zipped Plugin to your Wordpress installation
* Click on "Cables" in the main menu on the left
* Insert API-Key (create a new one in your account settings in cables.gl)
* fiddle around

## Requirements

* needs `define('FS_METHOD','direct');` in `wp-config.php`
* webserver user needs write permission to the plugin dir (should be ok if installing through Wordpress)
