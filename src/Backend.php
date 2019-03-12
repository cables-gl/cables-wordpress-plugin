<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 08:18
 */

namespace Polyshapes\Plugin;

use Polyshapes\Plugin\Api;
use Twig_Environment;

class Backend {

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Backend constructor.
     */
    public function __construct(Twig_Environment $twig) {
        $this->twig = $twig;
    }

    public function display() {
        static::enqueue_scripts();
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_polyshapes_login', array($this, 'polyshapes_login'));
        add_action('admin_post_polyshapes_logout', array($this, 'polyshapes_logout'));
        add_action('admin_post_polyshapes_set_style_options', array($this, 'polyshapes_set_style_options'));

    }

    public function enqueue_scripts() {
        wp_enqueue_script('polyshapes_backend', plugins_url() . '/polyshapes-wpplugin/public/js/backend.js');
        wp_enqueue_style('polyshapes_backend', plugins_url() . '/polyshapes-wpplugin/public/css/backend.css');
    }

    public function register_settings() {
        register_setting('polyshapes_backend', 'polyshapes_backend', array($this, 'validate_setting'));
        add_settings_section('polyshapes_backend_theme', 'Polyshapes Settings', array($this, 'section_cb'), __FILE__);
        add_settings_field('api_key', 'Api-Key:', array($this, 'api_key_setting'), __FILE__, 'polyshapes_backend_theme');
    }

    public function api_key_setting() {
        $options = Plugin::getPluginOptions();
        echo $options['api_key'];
    }

    public function validate_setting($plugin_options) {
        return $plugin_options;
    }

    public function section_cb() {
        echo "Settings to configure the integration of polyshapes.io into your Wordpress installation.";
    }

    public function admin_menu() {
        add_menu_page(Plugin::getTranslatedString('page_backend_menu_main'), Plugin::getTranslatedString('page_backend_menu_main'), 'manage_options', 'polyshapes_backend', array($this, 'polyshapes_dashboard'), 'dashicons-admin-appearance');
        add_submenu_page('polyshapes_backend', Plugin::getTranslatedString('page_backend_menu_dashboard'), 'Dashboard', 'manage_options', 'polyshapes_backend', array($this, 'polyshapes_dashboard'));

        if (Plugin::getPluginOption(Plugin::OPTIONS_API_KEY)) {
            add_submenu_page('polyshapes_backend', Plugin::getTranslatedString('page_backend_menu_imports'), Plugin::getTranslatedString('page_backend_menu_imports'), 'manage_options', 'polyshapes_backend_imports', array($this, 'imports_page'));
            add_submenu_page('polyshapes_backend', Plugin::getTranslatedString('page_backend_menu_settings'), Plugin::getTranslatedString('page_backend_menu_settings'), 'administrator', 'polyshapes_backend_settings', array($this, 'settings_page'));
            add_submenu_page('polyshapes_backend', Plugin::getTranslatedString('page_backend_menu_import'), null, 'manage_options', 'polyshapes_backend_import', array($this, 'import_page'));
            add_submenu_page('polyshapes_backend', Plugin::getTranslatedString('page_backend_menu_style'), null, 'manage_options', 'polyshapes_backend_style', array($this, 'style_page'));
        }
    }

    public function polyshapes_dashboard() {
        $options = Plugin::getPluginOptions();
        $params = array();
        if (!$options['api_key']) {
            $template = $this->twig->loadTemplate('admin/auth.twig');
            $params['action_url'] = esc_url(admin_url('admin-post.php'));
        } else {
            $styles = Plugin::getPluginOption(Plugin::OPTIONS_STYLES);
            $integratedStyles = array();
            foreach ($styles as $styleId => $styleConfig) {
                if (array_key_exists('integrations', $styleConfig) && !empty($styleConfig['integrations'])) {
                    foreach ($styleConfig['integrations'] as $key => $value) {
                        if ($value === 'on') {
                            $integratedStyles[$styleId] = $styleConfig;
                        }
                    }
                }
            }
            $params['integratedStyles'] = $integratedStyles;
            $template = $this->twig->loadTemplate('admin/dashboard/dashboard.twig');
        }
        echo $this->twig->render($template, $params);
    }

    public function import_page() {
        $styleId = $_GET['style'];
        $api = new Api\Polyshapes();
        $style = $api->getStyle($styleId);
        $api->importStyle($style);
        $admin_url = admin_url('admin.php?page=polyshapes_backend_style&style=' . $styleId);
        wp_redirect($admin_url);
        print "<a href='" . $admin_url . "'>go to style</a>";
        exit;
    }

    public function imports_page() {
        $active_tab = 'tab1';
        if (isset($_GET['tab'])) {
            $active_tab = $_GET['tab'];
        }
        $template = $this->twig->loadTemplate('admin/integration/integration.twig');
        $params = array();
        $api = new Api\Polyshapes();
        $params['styles'] = $api->getMyStyles();
        $params['active_tab'] = $active_tab;
        echo $this->twig->render($template, $params);
    }

    public function polyshapes_login() {
        status_header(200);
        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        $api = new Api\Polyshapes();
        $response = $api->login($email, $password);
        if ($response && isset($response->key)) {
            Plugin::setPluginOption(Plugin::OPTIONS_API_KEY, $response->key);
            Plugin::setPluginOption(Plugin::OPTIONS_ACCOUNT_ID, $response->accountId);
        }
        $admin_url = admin_url('admin.php?page=polyshapes_backend');
        wp_redirect($admin_url);
        exit;
    }

    public function polyshapes_logout() {
        status_header(200);
        $email = $_REQUEST['email'];
        Plugin::setPluginOption(Plugin::OPTIONS_API_KEY, null);
        Plugin::setPluginOption(Plugin::OPTIONS_ACCOUNT_ID, null);
        $admin_url = admin_url('admin.php?page=polyshapes_backend');
        wp_redirect($admin_url);
        exit;
    }

    public function polyshapes_set_style_options() {
        status_header(200);

        $integrations = $_REQUEST['polyshapes_integration'] ? $_REQUEST['polyshapes_integration'] : array();
        $integrateHeader = array_key_exists('header', $integrations);
        $integrateHero = array_key_exists('hero', $integrations);
        $integrateBackground = array_key_exists('background', $integrations);
        $integrateFooter = array_key_exists('footer', $integrations);
        $integrateCustom = array_key_exists('custom', $integrations);
        $customSelector = null;
        if (array_key_exists('customSelector', $integrations)) {
            $customSelector = $integrations['customSelector'];
        }
        $cssSelector = '';

        if ($integrateHeader) {
            $cssSelector .= 'header.site-header, ';
        }

        if ($integrateHero) {
            $cssSelector .= '#wp-custom-header, ';
        }

        if ($integrateFooter) {
            $cssSelector .= 'footer.site-footer, ';
        }

        if ($integrateCustom) {
            $cssSelector .= $customSelector . ', ';
        }

        $cssSelector = preg_replace('/,\s$/', '', $cssSelector);

        $styleId = $_REQUEST['style'];

        $pageTypes = $_REQUEST['polyshapes_integration_pagetype'];
        $mobile = $_REQUEST['mobile'];

        $styles = Plugin::getPluginOption(Plugin::OPTIONS_STYLES);
        if (!is_array($styles)) {
            $styles = array();
        }
        $styles[$styleId] = array(
            'background' => !!$integrateBackground,
            'integrations' => $integrations,
            'cssSelector' => $cssSelector,
            'page_types' => $pageTypes,
            'mobile' => !!$mobile
        );
        Plugin::setPluginOption(Plugin::OPTIONS_STYLES, $styles);

        $admin_url = admin_url('admin.php?page=polyshapes_backend_style&style=' . $styleId);
        wp_redirect($admin_url);
        exit;
    }

    public function style_page() {

        $template = $this->twig->loadTemplate('admin/style.twig');
        $api = new Api\Polyshapes();
        $styleId = $_GET['style'];
        $style = $api->getStyle($styleId);
        $imported = $api->isImported($style);
        $options = Plugin::getPluginOptions();
        if (is_array($options['styles'])) {
            if (is_array($options['styles'][$styleId])) {
                $styleConfig = $options['styles'][$styleId];
            }
        }
        $pageTemplates = wp_get_theme()->get_page_templates();
        $context = array(
            'style' => $style,
            'isImported' => $imported,
            'styleConfig' => $styleConfig,
            'styleDir' => $api->getStyleDirUrl($style),
            'action_url' => esc_url(admin_url('admin-post.php')),
            'cssSelectors' => $this->getPossibleCssSelectors(),
            'pageTemplates' => $pageTemplates
        );
        echo $this->twig->render($template, $context);
    }

    public function settings_page() {

        $vars = array();

        ob_start();
        settings_fields('polyshapes_backend');
        $vars['fields'] = ob_get_contents();
        ob_end_clean();

        ob_start();
        do_settings_sections(__FILE__);
        $vars['sections'] = ob_get_contents();
        ob_end_clean();

        $vars['save'] = esc_attr('Save Changes');
        $vars['action_url'] = esc_url(admin_url('admin-post.php'));

        $template = $this->twig->loadTemplate('admin/settings.twig');
        echo $this->twig->render($template, $vars);

    }

    private function getPossibleCssSelectors() {
        $selectors = array();
        $selectors['image'] = json_encode(get_header_image_tag());
        $selectors['header'] = json_encode(get_custom_header());
        $selectors['video'] = json_encode(get_header_video_url());
        $selectors['css'] = json_encode(wp_custom_css_cb());
        $selectors['css_post'] = json_encode(wp_get_custom_css_post());
        $selectors['custom_css'] = json_encode(wp_get_custom_css());
        $selectors['starter_content'] = json_encode(get_theme_starter_content());
        return $selectors;
    }

}
