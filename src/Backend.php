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
        self::enqueue_scripts();
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_polyshapes_login', array($this, 'polyshapes_login'));
        add_action('admin_post_polyshapes_set_shape_options', array($this, 'polyshapes_set_shape_options'));

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
        $options = get_option('polyshapes_backend');
        echo $options['api_key'];
    }

    public function validate_setting($plugin_options) {
        return $plugin_options;
    }

    public function section_cb() {
        echo "Settings to configure the integration of polyshapes.io into your Wordpress installation.";
    }

    public function admin_menu() {
        add_menu_page('Polyshapes', 'Polyshapes', 'manage_options', 'polyshapes_backend', array($this, 'plugin_page'), 'dashicons-admin-appearance');
        add_submenu_page('polyshapes_backend', 'Imports', 'Imports', 'manage_options', 'polyshapes_backend_imports', array($this, 'imports_page'));
        add_submenu_page('polyshapes_backend', 'Settings', 'Settings', 'administrator', 'polyshapes_backend_settings', array($this, 'settings_page'));

        add_submenu_page('polyshapes_backend', 'Import', null, 'manage_options', 'polyshapes_backend_import', array($this, 'import_page'));
        add_submenu_page('polyshapes_backend', 'Shape', null, 'manage_options', 'polyshapes_backend_shape', array($this, 'shape_page'));

    }

    public function plugin_page() {
        $options = get_option('polyshapes_backend');
        $params = array();
        if (!$options['api_key']) {
            $template = $this->twig->loadTemplate('admin/auth.twig');
            $params['action_url'] = esc_url(admin_url('admin-post.php'));
        } else {
            $template = $this->twig->loadTemplate('admin/home.twig');
        }
        echo $this->twig->render($template, $params);

    }

    public function import_page() {
        $shapeId = $_GET['shape'];
        $api = new Api\Polyshapes();
        $shape = $api->getShape($shapeId);
        $api->importShape($shape);
        $admin_url = admin_url('admin.php?page=polyshapes_backend_shape&shape=' . $shapeId);
        wp_redirect($admin_url);
        print "<a href='" . $admin_url . "'>go to shape</a>";
        exit;
    }

    public function imports_page() {
        $template = $this->twig->loadTemplate('admin/imports.twig');
        $params = array();
        $api = new Api\Polyshapes();
        $params['shapes'] = $api->getAllShapes();
        echo $this->twig->render($template, $params);
    }

    public function polyshapes_login() {
        status_header(200);
        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        $api = new Api\Polyshapes();
        $response = $api->login($email, $password);
        if ($response && isset($response->apikey)) {
            $options = get_option('polyshapes_backend');
            $options['api_key'] = $response->apikey->key;
            update_option('polyshapes_backend', $options);
        }
        $admin_url = admin_url('admin.php?page=polyshapes_backend');
        wp_redirect($admin_url);
        exit;
    }

    public function polyshapes_set_shape_options() {
        status_header(200);
        $shapeId = $_REQUEST['shape'];
        $elementReplacement = $_REQUEST['element_replacement'];
        $targetSelectors = $_REQUEST['target_selectors'];
        $pageTypes = $_REQUEST['page_types'];
        $background = $_REQUEST['background'];
        $mobile = $_REQUEST['mobile'];
        $options = get_option('polyshapes_backend');
        if (!is_array($options['shapes'])) {
            $options['shapes'] = array();
        }
        $options['shapes'][$shapeId] = array(
            "element_replacement" => $elementReplacement,
            "target_selectors" => $targetSelectors,
            'page_types' => $pageTypes,
            'background' => $background,
            'mobile' => $mobile
        );
        update_option('polyshapes_backend', $options);
        $admin_url = admin_url('admin.php?page=polyshapes_backend_shape&shape=' . $shapeId);
        wp_redirect($admin_url);
        exit;
    }

    public function shape_page() {

        $template = $this->twig->loadTemplate('admin/shape.twig');
        $api = new Api\Polyshapes();
        $shapeId = $_GET['shape'];
        $shape = $api->getShape($shapeId);
        $imported = $api->isImported($shape);
        $options = get_option('polyshapes_backend');
        $replacesElements = false;
        $targetSelectors = "";
        $setBackground = false;
        $mobile = true;
        $pageTypes = array('home' => 'on', 'post' => 'on', 'page' => 'on');
        if(is_array($options['shapes'])) {
            if(is_array($options['shapes'][$shapeId])) {
                $shapeConfig = $options['shapes'][$shapeId];
                $replacesElements = $shapeConfig['element_replacement'];
                $targetSelectors = $shapeConfig['target_selectors'];
                $pageTypes = $shapeConfig['page_types'];
                $setBackground = $shapeConfig['background'];
                $mobile = $shapeConfig['mobile'];
            }
        }



        echo $this->twig->render($template, array(
            'shape' => $shape,
            'isImported' => $imported,
            'pages' => $pageTypes,
            'background' => $setBackground,
            'mobile' => $mobile,
            'replacesElements' => $replacesElements,
            'targetSelectors' => $targetSelectors,
            'patchDir' => $api->getShapeDirUrl($shape),
            'action_url' => esc_url(admin_url('admin-post.php')),
            'cssSelectors' => $this->getPossibleCssSelectors()
        ));
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
