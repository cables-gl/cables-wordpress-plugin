<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 08:18
 */

namespace Cables\Plugin;

use Cables\Plugin\Api;
use Cables\Plugin\TemplateEngine\TemplateEngine;

class Backend {

    /**
     * @var TemplateEngine
     */
    private $template;

  /**
   * Backend constructor.
   * @param TemplateEngine $template
   */
    public function __construct(TemplateEngine $template) {
        $this->template = $template;
    }

    public function display() {
        static::enqueue_scripts();
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_cables_login', array($this, 'cables_login'));
        add_action('admin_post_cables_logout', array($this, 'cables_logout'));
        add_action('admin_post_cables_set_style_options', array($this, 'cables_set_style_options'));

    }

    public function enqueue_scripts() {
        wp_enqueue_script('cables_backend', plugins_url() . '/cables-wpplugin/public/js/backend.js');
        wp_enqueue_style('cables_backend', plugins_url() . '/cables-wpplugin/public/css/backend.css');
    }

    public function register_settings() {
        register_setting('cables_backend', 'cables_backend', array($this, 'validate_setting'));
        add_settings_section('cables_backend_theme', 'Polyshapes Settings', array($this, 'section_cb'), __FILE__);
        add_settings_field('api_key', 'Api-Key:', array($this, 'api_key_setting'), __FILE__, 'cables_backend_theme');
    }

    public function api_key_setting() {
        $options = Plugin::getPluginOptions();
        echo $options['api_key'];
    }

    public function validate_setting($plugin_options) {
        return $plugin_options;
    }

    public function section_cb() {
        echo "Settings to configure the integration of cables.io into your Wordpress installation.";
    }

    public function admin_menu() {
        add_menu_page(
          Plugin::getTranslatedString('page_backend_menu_main'),
          Plugin::getTranslatedString('page_backend_menu_main'),
          'manage_options',
          'cables_backend',
          array($this, 'cables_dashboard'),
          'dashicons-admin-appearance'
        );
        add_submenu_page(
          'cables_backend',
          Plugin::getTranslatedString('page_backend_menu_dashboard'),
          'Dashboard',
          'manage_options',
          'cables_backend',
          array($this, 'cables_dashboard')
        );

        if (Plugin::getPluginOption(Plugin::OPTIONS_API_KEY)) {
            add_submenu_page('cables_backend', Plugin::getTranslatedString('page_backend_menu_imports'), Plugin::getTranslatedString('page_backend_menu_imports'), 'manage_options', 'cables_backend_imports', array($this, 'imports_page'));
            add_submenu_page('cables_backend', Plugin::getTranslatedString('page_backend_menu_settings'), Plugin::getTranslatedString('page_backend_menu_settings'), 'administrator', 'cables_backend_settings', array($this, 'settings_page'));
            add_submenu_page('cables_backend', Plugin::getTranslatedString('page_backend_menu_import'), null, 'manage_options', 'cables_backend_import', array($this, 'import_page'));
            add_submenu_page('cables_backend', Plugin::getTranslatedString('page_backend_menu_style'), null, 'manage_options', 'cables_backend_style', array($this, 'style_page'));
        }
    }

    public function cables_dashboard() {
        $options = Plugin::getPluginOptions();
        $api = new Api\Cables();
        $params = array();
        if (!$options['api_key']) {
            $template = $this->template->loadTemplate('admin/auth');
            $params['action_url'] = esc_url(admin_url('admin-post.php'));
        } else {
            $styles = Plugin::getPluginOption(Plugin::OPTIONS_STYLES);
            $integratedStyles = array();
            $notIntgratedStyles = array();
            foreach ($styles as $styleId => $styleConfig) {
                $integrated = false;
                if (array_key_exists('integrations', $styleConfig) && !empty($styleConfig['integrations'])) {
                    foreach ($styleConfig['integrations'] as $key => $value) {
                        if ($value === 'on') {
                            $integrated = true;
                        }
                    }
                }
                if ($integrated) {
                    $integratedStyles[$styleId] = $styleConfig;
                } else {
                    $notIntgratedStyles[$styleId] = $styleConfig;
                }
            }
            $params['integratedStyles'] = $integratedStyles;
            $params['notIntegratedStyles'] = $notIntgratedStyles;
            $params['account'] = $api->getAccountInfo();
            $template = $this->template->loadTemplate('admin/dashboard/dashboard');
        }
        echo $this->template->render($template, $params);
    }

    public function import_page() {
        $styleId = $_GET['style'];
        $api = new Api\Cables();
        $style = $api->getStyle($styleId);
        $api->importPatch($style);
        $admin_url = admin_url('admin.php?page=cables_backend_style&style=' . $styleId);
        wp_redirect($admin_url);
        print "<a href='" . $admin_url . "'>go to style</a>";
        exit;
    }

    public function imports_page() {
        $params = array();
        $api = new Api\Cables();

        $active_tab = 'tab1';
        if (isset($_GET['tab']) && (isset($_GET['style']) && !empty($_GET['style']))) {
            $active_tab = $_GET['tab'];
            $styleId = $_GET['style'];
        }

        switch ($active_tab) {
            case 'tab2':
                $style = $api->getStyle($styleId);
                $styleConfig = array();
                if (!$style->isImported()) {
                    $api->importPatch($style);
                }
                $options = Plugin::getPluginOptions();
                if (is_array($options['styles'])) {
                    if (is_array($options['styles'][$styleId])) {
                        $styleConfig = $options['styles'][$styleId];
                    }
                }
                $pageTemplates = wp_get_theme()->get_page_templates();
                $params = array(
                    'style' => $styleId,
                    'isImported' => $style->isImported(),
                    'styleConfig' => $styleConfig,
                    'styleDir' => $api->getStyleDirUrl($style),
                    'action_url' => esc_url(admin_url('admin-post.php')),
                    'cssSelectors' => $this->getPossibleCssSelectors(),
                    'pageTemplates' => $pageTemplates
                );
                break;
            case 'tab3':
                $style = $api->getStyle($styleId);
                $styleConfig = array();
                if (!$style->isImported()) {
                    $api->importPatch($style);
                }
                $options = Plugin::getPluginOptions();
                if (is_array($options['styles'])) {
                    if (is_array($options['styles'][$styleId])) {
                        $styleConfig = $options['styles'][$styleId];
                    }
                }
                $pageTemplates = wp_get_theme()->get_page_templates();
                $params = array(
                    'style' => $styleId,
                    'isImported' => $style->isImported(),
                    'styleConfig' => $styleConfig,
                    'styleDir' => $api->getStyleDirUrl($style),
                    'action_url' => esc_url(admin_url('admin-post.php')),
                    'cssSelectors' => $this->getPossibleCssSelectors(),
                    'pageTemplates' => $pageTemplates
                );
                break;
            default:
            case 'tab1':
                $params['patches'] = $api->getMyPatches();
                break;

        }
        $template = $this->template->loadTemplate('admin/integration/integration');
        $params['style'] = $style;
        $params['active_tab'] = $active_tab;
        echo $this->template->render($template, $params);
    }

    public function cables_login() {
        status_header(200);
        $apikey = $_REQUEST['apikey'];
        if ($apikey) {
            Plugin::setPluginOption(Plugin::OPTIONS_API_KEY, $apikey);
            Plugin::setPluginOption(Plugin::OPTIONS_ACCOUNT_ID, 5711);
        }
        $admin_url = admin_url('admin.php?page=cables_backend');
        wp_redirect($admin_url);
        exit;
    }

    public function cables_logout() {
        status_header(200);
        $email = $_REQUEST['email'];
        Plugin::setPluginOption(Plugin::OPTIONS_API_KEY, null);
        Plugin::setPluginOption(Plugin::OPTIONS_ACCOUNT_ID, null);
        $admin_url = admin_url('admin.php?page=cables_backend');
        wp_redirect($admin_url);
        exit;
    }

    public function cables_set_style_options() {
        status_header(200);

        $redirect = $_REQUEST['redirect'];

        $integrations = $_REQUEST['cables_integration'] ? $_REQUEST['cables_integration'] : array();
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

        $pageTypes = $_REQUEST['cables_integration_pagetype'];
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

        if (!$redirect) {
            $redirect = admin_url('admin.php?page=cables_backend_style&style=' . $styleId);
        } else {
            $redirect = admin_url($redirect . '&style=' . $styleId);
        }
        wp_redirect($redirect);
        exit;
    }

    public function style_page() {

        $template = $this->template->loadTemplate('admin/style');
        $api = new Api\Cables();
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
        echo $this->template->render($template, $context);
    }

    public function settings_page() {

        $api = new Api\Cables();
        $vars = array();

        ob_start();
        settings_fields('cables_backend');
        $vars['fields'] = ob_get_contents();
        ob_end_clean();

        ob_start();
        do_settings_sections(__FILE__);
        $vars['sections'] = ob_get_contents();
        ob_end_clean();

        $vars['save'] = esc_attr('Save Changes');
        $vars['action_url'] = esc_url(admin_url('admin-post.php'));
        $vars['account'] = $api->getAccountInfo();
        $vars['apikey'] = Plugin::getPluginOption(Plugin::OPTIONS_API_KEY);

        $template = $this->template->loadTemplate('admin/settings');
        echo $this->template->render($template, $vars);

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
