<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 08:18
 */

namespace Polyshapes\Plugin;

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
    }

    public function enqueue_scripts() {
        wp_enqueue_script('polyshapes_backend', plugins_url() . '/polyshapes-wpplugin/public/js/backend.js');
        wp_enqueue_style('polyshapes_backend', plugins_url() . '/polyshapes-wpplugin/public/css/backend.css');
    }

    public function admin_menu() {
        add_menu_page('Polyshapes', 'Polyshapes', 'manage_options', 'polyshapes_backend', array($this, 'plugin_page'), 'dashicons-admin-appearance');
    }

    public function plugin_page() {
        $template = $this->twig->loadTemplate('admin/home.twig');
        echo $this->twig->render($template, array());
    }
}
