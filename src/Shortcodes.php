<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 14:02
 */

namespace Cables\Plugin\Wordpress;

use Cables\Api\CablesApi;
use Twig_Environment;

class Shortcodes {

    public static $SHORTCODE_SINGLE_PATCH = 'patch';

    private $twig;

    /**
     * Shortcodes constructor.
     */
    public function __construct(Twig_Environment $twig) {
        $this->twig = $twig;
        $this->config = new WordpressCablesConfig();
        $this->api = new CablesApi($this->config);
    }

    public function register() {
        add_shortcode(static::$SHORTCODE_SINGLE_PATCH, array($this, 'singlePatchCode'));
    }

    public function singlePatchCode($atts, $content, $tag) {
        $template = $this->twig->loadTemplate('frontend/shape.shortcode.twig');
        $shape = $this->api->getShape($atts['id']);
        $js = $this->api->getJavscriptForShape($shape);
        echo $this->twig->render($template, array(
            'shape' => $shape,
            'javascript' => $js,
            'targetSelector' => '#polypatch'
        ));
    }

}
