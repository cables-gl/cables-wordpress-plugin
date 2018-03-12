<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 12.03.18
 * Time: 16:17
 */

namespace Cables\Plugin\Wordpress;


use Cables\Api\Interfaces\Config;
use Cables\Api\Interfaces\Storage;

class WordpressCablesConfig implements Config {

    /**
     * CablesWordpressConfig constructor.
     */
    public function __construct() {
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string {
        return 'https://cables.gl';
    }

    public function getApiUrl(): string {
        return $this->getBaseUrl() . '/api';
    }

    /**
     * @return string
     */
    public function getApiKey() {
        $options = get_option('cables_backend');
        return $options['cables_api_key'];
    }

    /**
     * @return Storage
     */
    public function getStorage() {
        return new WordpressCablesStorage();
    }
}
