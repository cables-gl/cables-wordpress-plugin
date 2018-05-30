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

    public function target_selector_setting() {
        $options = get_option('polyshapes_backend');
        echo "<input name='polyshapes_backend[target_selector]' type='text' value='{$options['target_selector']}' />";
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
        wp_redirect(admin_url('admin.php?page=polyshapes_backend_shape&shape=' . $shapeId));
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
        wp_redirect(admin_url('admin.php?page=polyshapes_backend'));
        exit;
    }

    public function polyshapes_set_shape_options() {
        status_header(200);
        $shapeId = $_REQUEST['shape'];
        $elementReplacement = $_REQUEST['element_replacement'];
        $targetSelector = $_REQUEST['target_selector'];
        $options = get_option('polyshapes_backend');
        if (!is_array($options['shapes'])) {
            $options['shapes'] = array();
        }
        $options['shapes'][$shapeId] = array("element_replacement" => $elementReplacement, "target_selector" => $targetSelector);
        update_option('polyshapes_backend', $options);
        wp_redirect(admin_url('admin.php?page=polyshapes_backend_shape&shape=' . $shapeId));
        exit;
    }

    public function shape_page() {
        $template = $this->twig->loadTemplate('admin/shape.twig');
        $api = new Api\Polyshapes();
        $shapeId = $_GET['shape'];
        $shape = $api->getShape($shapeId);
        $shape->patchname = 'randompoints_example';
        $imported = $api->isImported($shape);
        $options = get_option('polyshapes_backend');
        $replacesElements = false;
        $targetSelector = "";
        if(is_array($options['shapes'])) {
            if(is_array($options['shapes'][$shapeId])) {
                $shapeConfig = $options['shapes'][$shapeId];
                $replacesElements = $shapeConfig['element_replacement'];
                $targetSelector = $shapeConfig['target_selector'];
            }
        }
        echo $this->twig->render($template, array(
            'shape' => $shape,
            'isImported' => $imported,
            'replacesElements' => $replacesElements,
            'targetSelector' => $targetSelector,
            'patchDir' => $api->getShapeDirUrl($shape),
            'action_url' => esc_url(admin_url('admin-post.php'))
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

}
