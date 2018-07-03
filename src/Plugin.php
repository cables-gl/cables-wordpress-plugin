<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 05.06.18
 * Time: 12:40
 */

namespace Polyshapes\Plugin;

use Symfony\Component\Yaml\Yaml;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

class Plugin {

    private static $baseUrl;
    private static $basePath;
    private static $config;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Plugin constructor.
     */
    public function __construct(string $baseUrl, string $basePath) {

        static::$baseUrl = $baseUrl;
        static::$config = Yaml::parseFile($basePath . 'config/config.yml');

        $loader = new Twig_Loader_Filesystem($basePath . '/templates/');
        $twig = new Twig_Environment($loader, array('debug' => false));
        $polyshapes_url = self::getConfig()['polyshapes_url'];
        $twig->addGlobal('polyshapes_url', $polyshapes_url);
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

    /**
     * @return string
     */
    public static function getBaseUrl() {
        return static::$baseUrl;
    }

    /**
     * @return string
     */
    public static function getBasePath() {
        return static::$basePath;
    }

    /**
     * @return string
     */
    public static function getApiUrl() {
        return static::$baseUrl . '/api';
    }

    /**
     * @return array
     */
    public static function getConfig() {
        $globals = static::$config['global'];
        $environment = static::$config['environment'];
        if(array_key_exists($environment, static::$config)) {
            $envConf = static::$config[$environment];
            if(!is_array($envConf)) {
                $envConf = array();
            }
        }else{
            $envConf = array();
        }
        return array_merge($globals, $envConf);
    }

}
