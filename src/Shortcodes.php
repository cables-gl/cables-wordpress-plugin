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
   * @param TemplateEngine $template
   */
    public function __construct(TemplateEngine $template) {
        $this->template = $template;
    }

    public function register() {
        add_shortcode(static::$SHORTCODE_SINGLE_PATCH, array($this, 'singlePatchCode'));
    }

    public function singlePatchCode($atts, $content, $tag) {
        $template = $this->template->loadTemplate('frontend/patch.shortcode');
        $api = new Api\Cables();
        $patch = $api->getPatch($atts['id']);
        if(!$atts['width']) {
          $atts['width'] = '100%';
          $atts['containerstyle'] .= 'position:relative; padding-bottom: 75%';
          $atts['patchstyle'] .= 'position:absolute; height: 75%';
        }

        return $this->template->render($template, array(
            'patch' => $patch,
            'isImported' => $api->isImported($patch),
            'patchDir' => $api->getPatchDirUrl($patch),
            'style' => [
              'width' => $atts['width'],
              'height' => $atts['height'],
              'containerStyle' => $atts['containerstyle'],
              'patchStyle' => $atts['patchstyle']
            ]
        ));
    }

}
