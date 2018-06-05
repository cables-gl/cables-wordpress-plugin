<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 14:02
 */

namespace Polyshapes\Plugin;


use Twig_Environment;

class Shortcodes {

    public static $SHORTCODE_SINGLE_POLYSHAPE = 'polyshape';

    private $twig;

    /**
     * Shortcodes constructor.
     */
    public function __construct(Twig_Environment $twig) {
        $this->twig = $twig;
    }

    public function register() {
        add_shortcode(static::$SHORTCODE_SINGLE_POLYSHAPE, array($this, 'singlePolyshapeCode'));
    }

    public function singlePolyshapeCode($atts, $content, $tag) {
        $template = $this->twig->loadTemplate('frontend/shape.shortcode.twig');
        $api = new Api\Polyshapes();
        $shape = $api->getShape($atts['id']);
        echo $this->twig->render($template, array(
            'shape' => $shape,
            'targetSelector' => '#polypatch',
            'isImported' => $api->isImported($shape),
            'patchDir' => $api->getShapeDirUrl($shape)
        ));
    }

}
