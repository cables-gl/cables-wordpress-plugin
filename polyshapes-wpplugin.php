<?php
/*
Plugin Name: Polyshapes Wordpress
Plugin URI: https://polyshapes.io
Description: Integration of polyshapes.io into Wordpress
Version: 0.0.6
Author: Stephan Maihoefer
Author URI: http://undev.de
License: MIT
*/


use Polyshapes\Plugin\Plugin;

require __DIR__ . '/vendor/autoload.php';

$baseUrl = plugin_dir_url(__FILE__);
$basePath = plugin_dir_path(__FILE__);

$plugin = new Plugin($baseUrl, $basePath);
$plugin->display();

$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://ci.undev.de/releases/undev/polyshapes-wpplugin/master/release.json',
    __FILE__, //Full path to the main plugin file or functions.php.
    'undev/polyshapes-wpplugin'
);
