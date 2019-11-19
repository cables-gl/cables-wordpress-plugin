<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 14:02
 */

namespace Cables\Plugin;


use Cables\Plugin\TemplateEngine\TemplateEngine;

class Shortcodes {

    public static $SHORTCODE_SINGLE_PATCH = 'cables_patch';

    /**
     * @var TemplateEngine
     */
    private $template;

  /**
   * Shortcodes constructor.
   * @param TemplateEngine $twig
   */
    public function __construct(TemplateEngine $twig) {
        $this->template = $twig;
    }

    public function register() {
        add_shortcode(static::$SHORTCODE_SINGLE_PATCH, array($this, 'singleStyleCode'));
    }

    public function singleStyleCode($atts, $content, $tag) {
        $template = $this->template->loadTemplate('frontend/style.shortcode');
        $api = new Api\Cables();
        $style = $api->getStyle($atts['id']);
        echo $this->template->render($template, array(
            'style' => $style,
            'targetSelector' => '#cablespatch',
            'isImported' => $api->isImported($style),
            'styleDir' => $api->getStyleDirUrl($style)
        ));
    }

}
