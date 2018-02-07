<?php
/*
Plugin Name: Polyshapes Wordpress
Plugin URI: https://polyshapes.io
Description: Integration of polyshapes.io into Wordpress
Version: 0.1
Author: Stephan Maihoefer
Author URI: http://undev.de
License: MIT
*/


use Polyshapes\Plugin\Backend;

require __DIR__ . '/vendor/autoload.php';

if (is_admin()) {
    $loader = new Twig_Loader_Filesystem(dirname(__FILE__) . '/templates/');
    // $twig = new Twig_Environment($loader, array());
    $twig = new Twig_Environment($loader, array('debug' => false));
    $twig->addGlobal('polyshapes_url', "https://polyshapes:undefined@dev.polyshapes.io");
    $twig->addExtension(new Twig_Extension_Debug());
    $backend = new Backend($twig);
    $backend->display();
}
