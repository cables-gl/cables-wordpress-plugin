<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 05.06.18
 * Time: 12:40
 */

namespace Cables\Plugin;

use Symfony\Component\Yaml\Yaml;
use Twig\Template;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;

class Plugin {

    const OPTIONS_API_KEY = 'api_key';
    const OPTIONS_PATCHES = 'patches';
    const OPTIONS_ACCOUNT_ID = 'account_id';

    private static $baseUrl;
    private static $basePath;
    private static $config;

    private static $translations = array();

    /**
     * @var \TemplateEngine
     */
    private $template;

    /**
     * Plugin constructor.
     */
    public function __construct($baseUrl, $basePath) {

        static::$baseUrl = $baseUrl;
        static::$config = Yaml::parseFile($basePath . 'config/config.yml');
        static::$basePath = $basePath;

        $this->template = new TemplateEngine\Php();

        $shortcodes = new Shortcodes($this->template);
        $shortcodes->register();

    }

    public function setup() {
        add_filter('query_vars', array($this, 'query_vars'), 10, 1);
        add_action('parse_request', array($this, 'parse_request'), 10, 1);
    }

    public function query_vars($vars) {
        $vars[] = 'cables_action';
        $vars[] = 'patch_screenshot';
        $vars[] = 'patch';
        return $vars;
    }

    public function parse_request($wp) {
        if (array_key_exists('cables_action', $wp->query_vars)) {
            $action = $wp->query_vars['cables_action'];
            switch ($action) {
                case 'patch_screenshot':
                    header('Content-Type: image/png', true);
                    $patchId = $wp->query_vars['patch'];
                    $api = new Api\Cables();
                    echo $api->getPatchScreenshot($patchId);
                    break;
                default:
                    break;
            }
            exit();
        }
    }

    public static function getPatchScreenshotUrl($patchId) {
        return static::getScreenshotBaseUrl() . $patchId . '/screenshot.png';
    }

    public static function getScreenshotBaseUrl() {
        return static::getConfig()['cables_url'] . '/project/';
    }

    public function display() {
        if (is_admin()) {
            $backend = new Backend($this->template);
            $backend->display();
        } else {
            $frontend = new Frontend($this->template);
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
        return static::getConfig()['cables_api_url'];
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
        $options = get_option('cables_backend');
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
        return update_option('cables_backend', $options);
    }

    public static function getTranslatedString($string) {
        $locale = get_locale();
        $localeFields = explode('_', $locale);
        $lang = trim($localeFields[0]);
        $translation = array();
        $languageFile = static::$basePath . 'config/i18n/' . $lang . '.yml';
        if (isset(static::$translations[$lang]) && static::$translations[$lang]) {
            $translation = static::$translations[$lang];
        } else if (file_exists($languageFile)) {
            $translation = Yaml::parseFile($languageFile);
            static::$translations[$lang] = $translation;
        } else {
            $defaultLang = static::getConfig()['default_language'];
            $languageFile = static::$basePath . 'config/i18n/' . $defaultLang . '.yml';
            $translation = Yaml::parseFile($languageFile);
            static::$translations[$defaultLang] = $translation;
        }

        $translations = static::flattenArray($translation);
        $key = 'cables_' . $string;
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
