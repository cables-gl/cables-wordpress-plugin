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
        if(!$this->isThemeHookActivated()) return;

        $options = get_option('polyshapes_backend');
        $template = $this->twig->loadTemplate('frontend/shape.footer.twig');
        $shapes = array();
        foreach ($this->getElementReplacingShapeIds() as $shapeId) {
            $api = new Api\Polyshapes();
            $shape = $api->getShape($shapeId);
            $js = $api->getJavscriptForShape($shape);
            $bla = explode(',', $options['shapes'][$shapeId]['target_selector']);
            foreach ($bla as $selector) {
                $splittedSelectors[] = trim($selector);
            }
            $shapeConfig = array(
                'shape' => $shape,
                'patchDir' => $api->getShapeDirUrl($shape),
                'javascript' => $js,
                'targetSelectors' => $splittedSelectors
            );
            $shapes[] = $shapeConfig;
        }

        echo $this->twig->render($template, array('shapes' => $shapes));
    }

    private function isThemeHookActivated() {
        $options = get_option('polyshapes_backend');
        if(is_array($options['shapes'])) {
            foreach ($options['shapes'] as $shape) {
                if($shape['element_replacement'] === "on") {
                    return true;
                }
            }
        }
        return false;
    }

    private function getElementReplacingShapeIds() {
        $ids = array();
        $options = get_option('polyshapes_backend');
        if(is_array($options['shapes'])) {
            foreach ($options['shapes'] as $id => $shape) {
                if ($shape['element_replacement'] === "on") {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

}
