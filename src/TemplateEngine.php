<?php
namespace Cables\Plugin\Wordpress;

class TemplateEngine {

    public static function render($templateName, $options) {
        include(plugin_dir_path( __FILE__ ) . '../templates/' . $templateName . '.php');
    }

}