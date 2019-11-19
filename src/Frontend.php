<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 17:54
 */

namespace Cables\Plugin;


use Cables\Plugin\TemplateEngine\TemplateEngine;

class Frontend {

    /**
     * @var TemplateEngine
     */
    private $template;

  /**
   * Frontend constructor.
   * @param TemplateEngine $template
   */
    public function __construct(TemplateEngine $template) {
        $this->template = $template;
    }

    public function display() {
        add_action('wp_footer', array($this, 'polyshape_footer'));
    }

    public function polyshape_footer() {

        $options = Plugin::getPluginOptions();
        $styles = array();
        foreach ($this->getElementReplacingStyleIds() as $styleId) {
            $api = new Api\Cables();
            $style = $api->getStyle($styleId);
            $styleOptions = $options['styles'][$styleId];
            if (!$this->isActiveByPageType($styleOptions)) {
                continue;
            }
            if (!$this->isActiveByMobileSetting($styleOptions)) {
                continue;
            }

            $styleOptions = array(
                'style' => $style,
                'styleConfig' => $styleOptions,
                'background' => !!$styleOptions['background'],
                'cssSelector' => $styleOptions['cssSelector'],
                'styleDir' => $api->getStyleDirUrl($style)
            );

            $styles[] = $styleOptions;
        }

        $content = "";
        if (!empty($styles)) {
            $template = $this->template->loadTemplate('frontend/style.footer');
            $content = $this->template->render($template, array('styles' => $styles));
        }

        echo $content;

    }

    private function getElementReplacingStyleIds() {
        $ids = array();
        $options = Plugin::getPluginOptions();
        if (is_array($options['styles'])) {
            foreach ($options['styles'] as $id => $style) {
                foreach ($style['integrations'] as $key => $value) {
                    if ($value === 'on') {
                        $ids[] = $id;
                    }
                }
            }
        }
        return $ids;
    }

    private function isActiveByPageType($styleOptions) {
        $pageOptions = $styleOptions['page_types'];
        $isHome = (is_home() || is_front_page());
        if (!is_array($pageOptions)) {
            return false;
        } else if ($isHome && $pageOptions['home'] == 'on') {
            return true;
        } else if ((!$isHome && is_page()) && $pageOptions['page'] == 'on') {
            return true;
        } else if (is_single() && $pageOptions['post'] == 'on') {
            return true;
        } else if ($pageOptions['all'] == 'on') {
            return true;
        } else {
            return false;
        }
    }

    private function isActiveByMobileSetting($styleOptions) {
        if (wp_is_mobile()) {
            if (array_key_exists('mobile', $styleOptions)) {
                return ($styleOptions['mobile'] == "on");
            }
        }
        return true;
    }

}
