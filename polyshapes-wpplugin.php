<?php
/*
Plugin Name: Polyshapes Wordpress
Plugin URI: https://polyshapes.io
Description: Integration of polyshapes.io into Wordpress
Version: 0.0.2
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
