<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 17:54
 */

namespace Polyshapes\Plugin;

use Twig_Environment;

class Frontend {

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * Frontend constructor.
     */
    public function __construct(Twig_Environment $twig) {
        $this->twig = $twig;
    }

    public function display() {
        add_action('wp_footer', array($this, 'shape_footer'));
    }

    public function shape_footer() {

        $options = Plugin::getPluginOptions();
        $template = $this->twig->loadTemplate('frontend/shape.footer.twig');
        $shapes = array();
        foreach ($this->getElementReplacingShapeIds() as $shapeId) {
            $api = new Api\Polyshapes();
            $shape = $api->getShape($shapeId);
            $shapeOptions = $options['shapes'][$shapeId];
            if (!$this->isActiveByPageType($shapeOptions)) {
                continue;
            }
            if (!$this->isActiveByMobileSetting($shapeOptions)) {
                continue;
            }
            $selectorsSetting = explode(',', $shapeOptions['target_selectors']);
            foreach ($selectorsSetting as $selector) {
                $splittedSelectors[] = trim($selector);
            }
            $shapeConfig = array(
                'shape' => $shape,
                'background' => ($shapeOptions['background'] == "on"),
                'element_replacement' => $this->isThemeHookActivated($shapeOptions),
                'patchDir' => $api->getShapeDirUrl($shape),
                'targetSelectors' => $splittedSelectors
            );

            $shapes[] = $shapeConfig;
        }

        echo $this->twig->render($template, array('shapes' => $shapes));
    }

    private function getElementReplacingShapeIds() {
        $ids = array();
        $options = Plugin::getPluginOptions();
        if (is_array($options['shapes'])) {
            foreach ($options['shapes'] as $id => $shape) {
                if ($shape['element_replacement'] === "on") {
                    $ids[] = $id;
                } else if ($shape['background'] === "on") {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

    private function isActiveByPageType($shapeOptions) {
        $pageOptions = $shapeOptions['page_types'];
        $isHome = (is_home() || is_front_page());
        if (!is_array($pageOptions)) {
            return false;
        } else if ($isHome && $pageOptions['home'] == 'on') {
            return true;
        } else if ((!$isHome && is_page()) && $pageOptions['page'] == 'on') {
            return true;
        } else if (is_single() && $pageOptions['post'] == 'on') {
            return true;
        } else {
            return false;
        }
    }

    private function isActiveByMobileSetting($shapeOptions) {
        if (wp_is_mobile()) {
            if (array_key_exists('mobile', $shapeOptions)) {
                return ($shapeOptions['mobile'] == "on");
            }
        }
        return true;
    }

    private function isThemeHookActivated($shapeOptions) {
        if (is_array($shapeOptions)) {
            if ($shapeOptions['element_replacement'] === "on") {
                return true;
            }
        }
        return false;
    }

}
