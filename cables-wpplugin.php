<?php
/*
Plugin Name: cables.gl Wordpress
Plugin URI: https://cables.gl
Description: Integration of cables.gl patches into the Wordpress CMS
Version: 0.8.1
Author: undefined development
Author URI: http://undev.de
License: MIT
*/


use Cables\Plugin\Plugin;

require __DIR__ . '/vendor/autoload.php';

$baseUrl = plugin_dir_url(__FILE__);
$basePath = plugin_dir_path(__FILE__);

$plugin = new Plugin($baseUrl, $basePath);

add_action(
    'plugins_loaded',
    array($plugin, 'setup')
);

$plugin->display();

function i18n($text) {
  return Plugin::getTranslatedString($text);
}

function cables_patch_screenshot_url($patchId) {
  return Plugin::getPatchScreenshotUrl($patchId);
}
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://ci.undev.de/releases/undev-studio/polyshapes_api_wpclient/master/release.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'undev/polyshapes-wpplugin'
);
