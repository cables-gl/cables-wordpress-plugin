<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 17:54
 */

namespace Cables\Plugin\Wordpress;

use Cables\Api\CablesApi;
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
        $this->config = new WordpressCablesConfig();
        $this->api = new CablesApi($this->config);
    }

    public function display() {
    }
    
}
