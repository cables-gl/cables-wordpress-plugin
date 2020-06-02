<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 17:54
 */

namespace Cables\Plugin\Wordpress;

use Cables\Api\CablesApi;

class Frontend {

    /**
     * Frontend constructor.
     */
    public function __construct() {
        $this->config = new WordpressCablesConfig();
        $this->api = new CablesApi($this->config);
    }

    public function display() {
    }
    
}
