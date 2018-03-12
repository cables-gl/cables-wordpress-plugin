<?php
/*
Plugin Name: cables.gl Wordpress Integration
Plugin URI: https://cables.gl
Description: Integration of cables.gl into Wordpress
Version: 0.0.1
Author: undefined development
Author URI: http://undev.de
License: MIT
*/

namespace Cables\Plugin\Wordpress;

use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

require __DIR__ . '/vendor/autoload.php';

$loader = new Twig_Loader_Filesystem(dirname(__FILE__) . '/templates/');
$twig = new Twig_Environment($loader, array('debug' => false));
$twig->addGlobal('polyshapes_url', "https://dev.polyshapes.io");
$twig->addExtension(new Twig_Extension_Debug());

$shortcodes = new Shortcodes($twig);
$shortcodes->register();

if (is_admin()) {
    if( ! class_exists( 'WP_List_Table' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }
    $backend = new Backend($twig);
    $backend->display();
}else{
    $frontend = new Frontend($twig);
    $frontend->display();
}
