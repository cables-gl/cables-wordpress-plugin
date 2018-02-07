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
        $template = $this->twig->loadTemplate('frontend/shape.footer.twig');
        $api = new Api\Polyshapes();
        $shape = $api->getShape('SysXgGVDl');
        $js = $api->getJavscriptForShape($shape);
        echo $this->twig->render($template, array(
            'shape' => $shape,
            'javascript' => $js,
            'target' => 'masthead'
        ));
    }

}
