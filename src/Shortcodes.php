<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 14:02
 */

namespace Cables\Plugin\Wordpress;

use Cables\Api\CablesApi;
use TemplateEngine;

class Shortcodes {

    public static $SHORTCODE_SINGLE_PATCH = 'patch';

    /**
     * Shortcodes constructor.
     */
    public function __construct() {
        $this->config = new WordpressCablesConfig();
        $this->api = new CablesApi($this->config);
    }

    public function register() {
        add_shortcode(static::$SHORTCODE_SINGLE_PATCH, array($this, 'singlePatchCode'));
    }

    public function singlePatchCode($atts, $content, $tag) {
        $shape = $this->api->getShape($atts['id']);
        $js = $this->api->getJavscriptForShape($shape);
        echo TemplateEngine::render('frontend/shape.shortcode.twig', array(
            'shape' => $shape,
            'javascript' => $js,
            'targetSelector' => '#polypatch'
        ));
    }

}
