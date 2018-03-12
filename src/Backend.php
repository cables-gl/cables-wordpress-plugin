<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 08:18
 */

namespace Cables\Plugin\Wordpress;

use Cables\Api\CablesApi;
use Cables\Api\Interfaces\Config;
use Twig_Environment;

class Backend {
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CablesApi
     */
    private $api;

    /**
     * Backend constructor.
     */
    public function __construct(Twig_Environment $twig) {
        $this->twig = $twig;
        $this->config = new WordpressCablesConfig();
        $this->api = new CablesApi($this->config);
    }

    public function display() {
        self::enqueue_scripts();
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script('cables_backend', plugins_url() . '/cables_wordpress_plugin/public/js/backend.js');
        wp_enqueue_style('cables_backend', plugins_url() . '/cables_wordpress_plugin/public/css/backend.css');
    }

    public function register_settings() {

        register_setting('cables_backend', 'cables_backend', array($this, 'validate_setting'));
        add_settings_section('polyshapes_cables_integration', 'Cables Integration', array($this, 'section_cables'), __FILE__);

        add_settings_field('cables_api_key', 'Api-Key:', array($this, 'cables_api_key_setting'), __FILE__, 'polyshapes_cables_integration');


    }

    public function cables_api_key_setting() {
        $options = get_option('cables_backend');
        echo "<input name='cables_backend[cables_api_key]' type='text' width='96' value='{$options['cables_api_key']}' />";
    }

    public function validate_setting($plugin_options) {
        return $plugin_options;
    }

    public function section_cables() {
        echo "Enter your API-Key that you find in the settings of your cables.gl useraccount";
    }

    public function admin_menu() {
        add_menu_page('cables.gl', 'cables.gl', 'manage_options', 'cables_backend', array($this, 'plugin_page'), 'dashicons-admin-appearance');
        add_submenu_page('cables_backend', 'My Patches', 'My Patches', 'manage_options', 'cables_backend_my_patches', array($this, 'my_patches_page'));
        add_submenu_page('cables_backend', 'Settings', 'Settings', 'manage_options', 'cables_backend_settings', array($this, 'settings_page'));
        add_submenu_page('cables_backend', 'Patch', null, 'manage_options', 'cables_backend_edit_patch', array($this, 'patch_page'));
        add_submenu_page('cables_backend', 'Import Patch', null, 'manage_options', 'cables_backend_import_patch', array($this, 'patch_import_page'));
    }

    public function plugin_page() {
        $imported = array();
        $cables_api_key = $this->config->getApiKey();
        if ($cables_api_key) {
            $patches = $this->api->getMyPatches();
            foreach ($patches as $patch) {
                if ($this->api->isImported($patch)) {
                    $imported[] = $patch;
                }
            }
        }
        if (!$cables_api_key || empty($imported)) {
            $template = $this->twig->loadTemplate('admin/home.twig');
            $options = array(
                'api_key' => $cables_api_key,
                'importedPatches' => $imported
            );
            echo $this->twig->render($template, $options);
        } else {
            $myListTable = new Patches_Table($imported, $this->api);
            echo '<div class="wrap"><h2>Recently Imported Patches</h2>';
            $myListTable->prepare_items();
            $myListTable->display();
            echo '</div>';
        }
    }

    public function my_patches_page() {
        $myListTable = new Patches_Table($this->api->getMyPatches(), $this->api);
        echo '<div class="wrap"><h2>My Patches</h2>';
        $myListTable->prepare_items();
        $myListTable->display();
        echo '</div>';
    }

    public function patch_import_page() {
        $patches = $this->api->getMyPatches();
        $patchId = $_GET['patch'];
        foreach ($patches as $patch) {
            if ($patch->getId() == $patchId) {
                $importPatch = $patch;
            }
        }
        $template = $this->twig->loadTemplate('admin/import.twig');
        $this->api->importPatch($importPatch);
        echo $this->twig->render($template, array(
            'id' => $patchId
        ));
    }

    public function patch_page() {
        $template = $this->twig->loadTemplate('admin/patch.twig');
        $patchId = $_GET['patch'];
        $myPatch = null;
        foreach ($this->api->getMyPatches() as $patch) {
            if ($patch->getId() == $patchId) {
                $myPatch = $patch;
            }
        }
        echo $this->twig->render($template, array(
            'id' => $patchId,
            'patch' => $myPatch,
            'patchDir' => $this->api->getPatchDirUrl($myPatch),
            'isImported' => $this->api->isImported($myPatch)
        ));
    }

    public function settings_page() {

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

        $template = $this->twig->loadTemplate('admin/settings.twig');
        echo $this->twig->render($template, $vars);

    }

    public function isDevEnv() {
        return true;
    }
}
