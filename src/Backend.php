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
        add_action('admin_post_cables_set_patch_options', array($this, 'cables_set_patch_options'));

    }

    public function enqueue_scripts() {
        wp_enqueue_script('cables_backend', plugins_url() . '/cables-wpplugin/public/js/backend.js');
        wp_enqueue_style('cables_backend', plugins_url() . '/cables-wpplugin/public/css/backend.css');
    }

    public function register_settings() {
        register_setting('cables_backend', 'cables_backend', array($this, 'validate_setting'));
        add_settings_section('cables_backend_theme', 'Cables Settings', array($this, 'section_cb'), __FILE__);
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
            add_submenu_page('cables_backend', Plugin::getTranslatedString('page_backend_menu_patch'), null, 'manage_options', 'cables_backend_patch', array($this, 'patch_page'));
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
            $patches = Plugin::getPluginOption(Plugin::OPTIONS_PATCHES);
            $integratedPatches = array();
            $notIntgratedPatches = array();
            if($patches) {
              foreach ($patches as $patchId => $patchConfig) {
                $integrated = false;
                if (array_key_exists('integrations', $patchConfig) && !empty($patchConfig['integrations'])) {
                  foreach ($patchConfig['integrations'] as $key => $value) {
                    if ($value === 'on') {
                      $integrated = true;
                    }
                  }
                }
                if ($integrated) {
                  $integratedPatches[$patchId] = $patchConfig;
                } else {
                  $notIntgratedPatches[$patchId] = $patchConfig;
                }
              }
            }
            $params['integratedPatches'] = $integratedPatches;
            $params['notIntgratedPatches'] = $notIntgratedPatches;
            $params['account'] = $api->getAccountInfo();
            $template = $this->template->loadTemplate('admin/dashboard/dashboard');
        }
        echo $this->template->render($template, $params);
    }

    public function import_page() {
        $patchId = $_GET['patch'];
        $api = new Api\Cables();
        $patch = $api->getPatch($patchId);
        $api->importPatch($patch);
        $admin_url = admin_url('admin.php?page=cables_backend_patch&patch=' . $patchId);
        wp_redirect($admin_url);
        print "<a href='" . $admin_url . "'>go to patch</a>";
        exit;
    }

    public function imports_page() {
        $params = array();
        $api = new Api\Cables();

        $active_tab = 'tab1';
        if (isset($_GET['tab']) && (isset($_GET['patch']) && !empty($_GET['patch']))) {
            $active_tab = $_GET['tab'];
            $patchId = $_GET['patch'];
        }

        switch ($active_tab) {
            case 'tab2':
                $patch = $api->getPatch($patchId);
                $patchConfig = array();
                if (!$patch->isImported()) {
                    $api->importPatch($patch);
                }
                $options = Plugin::getPluginOptions();
                if (is_array($options['patches'])) {
                    if (is_array($options['patches'][$patchId])) {
                        $patchConfig = $options['patches'][$patchId];
                    }
                }
                $params = array(
                    'patch' => $patchId,
                    'isImported' => $patch->isImported(),
                    'patchConfig' => $patchConfig,
                    'patchDir' => $api->getPatchDirUrl($patch),
                    'action_url' => esc_url(admin_url('admin-post.php')),
                    'cssSelectors' => $this->getPossibleCssSelectors()
                );
                break;
            case 'tab3':
                $patch = $api->getPatch($patchId);
                $patchConfig = array();
                if (!$patch->isImported()) {
                    $api->importPatch($patch);
                }
                $options = Plugin::getPluginOptions();
                if (is_array($options['patches'])) {
                    if (is_array($options['patches'][$patchId])) {
                        $patchConfig = $options['patches'][$patchId];
                    }
                }
                $params = array(
                    'patch' => $patchId,
                    'isImported' => $patch->isImported(),
                    'patchConfig' => $patchConfig,
                    'patchDir' => $api->getPatchDirUrl($patch),
                    'action_url' => esc_url(admin_url('admin-post.php')),
                    'cssSelectors' => $this->getPossibleCssSelectors()
                );
                break;
            default:
            case 'tab1':
                $params['patches'] = $api->getMyPatches();
                break;

        }
        $template = $this->template->loadTemplate('admin/integration/integration');
        $params['patch'] = $patch;
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

    public function cables_set_patch_options() {
        status_header(200);

        $redirect = $_REQUEST['redirect'];

        $delete = $_REQUEST['delete'];
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

        $patchId = $_REQUEST['patch'];

        $pageTypes = $_REQUEST['cables_integration_pagetype'];
        $mobile = $_REQUEST['mobile'];

        $patches = Plugin::getPluginOption(Plugin::OPTIONS_PATCHES);
        if (!is_array($patches)) {
            $patches = array();
        }
        if($delete) {
          unset($patches[$patchId]);
          $api = new Api\Cables();
          $api->deletePatchFiles($patchId);
          Plugin::setPluginOption(Plugin::OPTIONS_PATCHES, $patches);
          wp_redirect(admin_url('/admin.php?page=cables_backend'));
          exit;
        }else{
          $patches[$patchId] = array(
            'background' => !!$integrateBackground,
            'integrations' => $integrations,
            'cssSelector' => $cssSelector,
            'page_types' => $pageTypes,
            'mobile' => !!$mobile
          );
        }
        Plugin::setPluginOption(Plugin::OPTIONS_PATCHES, $patches);

        if (!$redirect) {
            $redirect = admin_url('admin.php?page=cables_backend_patch&patch=' . $patchId);
        } else {
            $redirect = admin_url($redirect . '&patch=' . $patchId);
        }
        wp_redirect($redirect);
        exit;
    }

    public function patch_page() {

        $template = $this->template->loadTemplate('admin/patch');
        $api = new Api\Cables();
        $patchId = $_GET['patch'];
        $patch = $api->getPatch($patchId);
        $imported = $api->isImported($patch);
        $options = Plugin::getPluginOptions();
        if (is_array($options['patches'])) {
            if (is_array($options['patches'][$patchId])) {
                $patchConfig = $options['patches'][$patchId];
            }
        }
        $context = array(
            'patch' => $patch,
            'isImported' => $imported,
            'patchConfig' => $patchConfig,
            'patchDir' => $api->getPatchDirUrl($patch),
            'action_url' => esc_url(admin_url('admin-post.php')),
            'cssSelectors' => $this->getPossibleCssSelectors()
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
