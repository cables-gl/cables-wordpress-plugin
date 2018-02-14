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
    }

    public function register_settings() {

        register_setting('polyshapes_backend', 'polyshapes_backend', array($this, 'validate_setting'));

        add_settings_section('polyshapes_backend_theme', 'Theme-Hook', array($this, 'section_cb'), __FILE__);

        add_settings_field('theme_hook', 'Activate Element Replacement:', array($this, 'theme_hook_setting'), __FILE__, 'polyshapes_backend_theme');
        add_settings_field('shape_id', 'Shape-Id:', array($this, 'shape_id_setting'), __FILE__, 'polyshapes_backend_theme');
        add_settings_field('target_selector', 'Target Element (CSS-Selector):', array($this, 'target_selector_setting'), __FILE__, 'polyshapes_backend_theme');


        add_settings_section('polyshapes_cables_integration', 'Cables Integration', array($this, 'section_cables'), __FILE__);

        add_settings_field('cables_api_key', 'Api-Key:', array($this, 'cables_api_key_setting'), __FILE__, 'polyshapes_cables_integration');


    }

    public function theme_hook_setting() {
        $options = get_option('polyshapes_backend');
        if($options['theme_hook']) {
            echo "<input name='polyshapes_backend[theme_hook]' type='checkbox' checked='checked'/>";
        }else{
            echo "<input name='polyshapes_backend[theme_hook]' type='checkbox'/>";
        }
    }

    public function shape_id_setting() {
        $options = get_option('polyshapes_backend');
        echo "<input name='polyshapes_backend[shape_id]' type='text' value='{$options['shape_id']}' />";
    }

    public function target_selector_setting() {
        $options = get_option('polyshapes_backend');
        echo "<input name='polyshapes_backend[target_selector]' type='text' value='{$options['target_selector']}' />";
    }

    public function cables_api_key_setting() {
        $options = get_option('polyshapes_backend');
        echo "<input name='polyshapes_backend[cables_api_key]' type='text' width='96' value='{$options['cables_api_key']}' />";
    }

    public function validate_setting($plugin_options) {
        return $plugin_options;
    }

    public function section_cb() {
        echo "Activating the theme hook will replace the innerHtml of every element on the public page with the polyshape corresponding tho the given ID";
    }

    public function section_cables() {
        echo "Enter your API-Key that you find in the settings of your cables.gl useraccount";
    }

    public function enqueue_scripts() {
        wp_enqueue_script('polyshapes_backend', plugins_url() . '/polyshapes-wpplugin/public/js/backend.js');
        wp_enqueue_style('polyshapes_backend', plugins_url() . '/polyshapes-wpplugin/public/css/backend.css');
    }

    public function admin_menu() {
        add_menu_page('Polyshapes', 'Polyshapes', 'manage_options', 'polyshapes_backend', array($this, 'plugin_page'), 'dashicons-admin-appearance');
        add_submenu_page('polyshapes_backend', 'Shape', null, 'manage_options', 'polyshapes_backend_shape', array($this, 'shape_page'));
        $options = get_option('polyshapes_backend');
        if($options['cables_api_key']) {
            add_submenu_page('polyshapes_backend', 'cables.gl', 'cables.gl', 'manage_options', 'polyshapes_backend_cables', array($this, 'cables_page'));
            add_submenu_page('polyshapes_backend', 'Patch', null, 'manage_options', 'polyshapes_backend_patch', array($this, 'patch_page'));
            add_submenu_page('polyshapes_backend', 'Import Patch', null, 'manage_options', 'polyshapes_backend_patch_import', array($this, 'patch_import_page'));

        }
        add_submenu_page('polyshapes_backend', 'Settings', 'Settings', 'administrator', 'polyshapes_backend_settings', array($this, 'settings_page'));

    }

    public function plugin_page() {
        $template = $this->twig->loadTemplate('admin/home.twig');
        $api = new Api\Polyshapes();
        $shapes = $api->getAllShapes();
        echo $this->twig->render($template, array('shapes' => $shapes));
    }

    public function shape_page() {
        $template = $this->twig->loadTemplate('admin/shape.twig');
        $api = new Api\Polyshapes();
        $shape = $api->getShape($_GET['shape']);
        $js = $api->getJavscriptForShape($shape);
        echo $this->twig->render($template, array(
            'shape' => $shape,
            'javascript' => $js,
            'targetSelector' => '#polypatch'
        ));
    }

    public function cables_page() {
        $template = $this->twig->loadTemplate('admin/cables.twig');
        $api = new Api\Cables();
        $patches = $api->getMyPatches();
        echo $this->twig->render($template, array('patches' => $patches));
    }

    public function patch_page() {
        $template = $this->twig->loadTemplate('admin/patch.twig');
        $api = new Api\Cables();
        $patchId = $_GET['patch'];
        $imported = $api->isImported($patchId);
        $export = $api->getPatchExport($patchId);
        foreach ($api->getMyPatches() as $myPatch) {
            if($myPatch->getId() == $patchId) {
                $patchName = $myPatch->getName();
            }
        }
        echo $this->twig->render($template, array(
            'export' => $export,
            'id' => $patchId,
            'name' => str_replace(' ', '_', $patchName),
            'isImported' => $imported,
            'patchDir' => $api->getPatchDirUrl($patchId)
        ));
    }

    public function patch_import_page() {
        $template = $this->twig->loadTemplate('admin/import.twig');
        $api = new Api\Cables();
        $patchId = $_GET['patch'];
        $export = $api->getPatchExport($patchId);
        $api->importPatch($patchId, $export);
        echo $this->twig->render($template, array(
            'id' => $patchId
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

    public function isDevEnv() {
        return true;
    }
}
