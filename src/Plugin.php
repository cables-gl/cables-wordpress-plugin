<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 05.06.18
 * Time: 12:40
 */

namespace Polyshapes\Plugin;

use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

class Plugin {

    public static $baseUrl;
    public static $basePath;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Plugin constructor.
     */
    public function __construct(string $baseUrl, string $basePath) {
        static::$baseUrl = $baseUrl;
        static::$basePath = $basePath;

        $loader = new Twig_Loader_Filesystem($basePath . '/templates/');
        $twig = new Twig_Environment($loader, array('debug' => false));
        $twig->addGlobal('polyshapes_url', "https://dev.polyshapes.io");
        $twig->addExtension(new Twig_Extension_Debug());
        $this->twig = $twig;

        $shortcodes = new Shortcodes($twig);
        $shortcodes->register();
    }


    public function display() {
        if (is_admin()) {
            $backend = new Backend($this->twig);
            $backend->display();
        } else {
            $frontend = new Frontend($this->twig);
            $frontend->display();
        }

    }
}
