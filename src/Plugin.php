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

    const OPTIONS_API_KEY = 'api_key';
    const OPTIONS_STYLES = 'styles';
    const OPTIONS_ACCOUNT_ID = 'account_id';

    private static $baseUrl;
    private static $basePath;
    private static $config;

    private static $translations = array();

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
        static::$basePath = $basePath;

        $loader = new Twig_Loader_Filesystem($basePath . '/templates/');
        $twig = new Twig_Environment($loader, array('debug' => true));
        $polyshapes_url = static::getConfig()['polyshapes_url'];
        $polyshapes_api_url = static::getConfig()['polyshapes_api_url'];
        $twig->addGlobal('polyshapes_api_url', $polyshapes_api_url);
        $twig->addGlobal('polyshapes_url', $polyshapes_url);
        $twig->addGlobal('polyshapes_screenshot_baseurl', static::getScreenshotBaseUrl());
        $twig->addFilter(new \Twig\TwigFilter('i18n', array(static::class, 'getTranslatedString'), ['is_safe' => ['html']]));
        $twig->addFunction('polyshapes_style_screenshot_url', new \Twig\TwigFunction('polyshapes_style_screenshot_url', array($this, 'getStyleScreenshotUrl')));
        $twig->addExtension(new Twig_Extension_Debug());
        $this->twig = $twig;

        $shortcodes = new Shortcodes($twig);
        $shortcodes->register();

    }

    public function setup() {
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
    }

    public function query_vars($vars) {
        $vars[] = 'polyshapes_action';
        $vars[] = 'style_screenshot';
        $vars[] = 'style';
        return $vars;
    }

    public function parse_request($wp) {
        if (array_key_exists('polyshapes_action', $wp->query_vars)) {
            $action = $wp->query_vars['polyshapes_action'];
            switch ($action) {
                case 'style_screenshot':
                    header('Content-Type: image/png', true);
                    $styleId = $wp->query_vars['style'];
                    $api = new Api\Polyshapes();
                    echo $api->getStyleScreenshot($styleId);
                    break;
                default:
                    break;
            }
            exit();
        }
    }

    public function getStyleScreenshotUrl($styleId) {
        return static::getScreenshotBaseUrl() . $styleId;
    }

    public static function getScreenshotBaseUrl() {
        return get_site_url() . '?polyshapes_action=style_screenshot&style=';
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
        return static::getConfig()['polyshapes_api_url'];
    }

    /**
     * @return array
     */
    public static function getConfig() {
        $globals = static::$config['global'];
        $environment = static::$config['environment'];
        if (array_key_exists($environment, static::$config)) {
            $envConf = static::$config[$environment];
            if (!is_array($envConf)) {
                $envConf = array();
            }
        } else {
            $envConf = array();
        }
        $merge = array_merge($globals, $envConf);
        return $merge;
    }

    /**
     * @return array
     */
    public static function getPluginOptions() {
        $options = get_option('polyshapes_backend');
        if ($options && is_array($options)) {
            return $options;
        } else {
            return array();
        }
    }

    /**
     * @param $option
     * @return mixed|null
     */
    public static function getPluginOption($option) {
        $options = static::getPluginOptions();
        if (array_key_exists($option, $options)) {
            return $options[$option];
        } else {
            return null;
        }
    }

    /**
     * @param $option
     * @param $value
     * @return bool
     */
    public static function setPluginOption($option, $value) {
        $options = static::getPluginOptions();
        $options[$option] = $value;
        return update_option('polyshapes_backend', $options);
    }

    public static function getTranslatedString($string) {
        $locale = get_locale();
        $localeFields = explode('_', $locale);
        $lang = trim($localeFields[0]);
        $translation = array();
        $languageFile = static::$basePath . 'config/i18n/' . $lang . '.yml';
        if (array_key_exists($lang, static::$translations) & static::$translations[$lang]) {
            $translation = static::$translations[$lang];
        } else if (file_exists($languageFile)) {
            $translation = Yaml::parseFile($languageFile);
            static::$translations[$lang] = $translation;
        } else {
            $defaultLang = static::getConfig()['default_language'];
            $translation = Yaml::parseFile($languageFile);
            static::$translations[$defaultLang] = $translation;
        }

        $translations = static::flattenArray($translation);
        $key = 'polyshapes_' . $string;
        if (array_key_exists($key, $translations)) {
            return $translations[$key];
        } else {
            return "missing translation for " . $string . " and language " . $lang;
        }
    }

    public static function flattenArray($array, $prefix = '') {
        $result = array();

        foreach ($array as $key => $value) {
            $new_key = $prefix . (empty($prefix) ? '' : '_') . $key;

            if (is_array($value)) {
                $result = array_merge($result, static::flattenArray($value, $new_key));
            } else {
                $result[$new_key] = $value;
            }
        }
        return $result;
    }

}