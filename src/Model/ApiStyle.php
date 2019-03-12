<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:18
 */

namespace Polyshapes\Plugin\Model;


use Polyshapes\Plugin\Api\Polyshapes;
use Polyshapes\Plugin\Plugin;

class ApiStyle {

    private $id;
    private $name;
    private $ops;
    private $shapeId;
    private $accountId;
    private $downloadUrl;
    private $isPreset;
    private $imported;

    /**
     * ApiStyle constructor. Private, use factory-method
     */
    private function __construct() {
    }

    /**
     * @param $styleJSON
     * @return ApiStyle
     */
    public static function fromJson($styleJSON): ApiStyle {
        $style = new ApiStyle();
        $style->name = $styleJSON->name;
        $style->id = $styleJSON->id;
        $style->ops = $styleJSON->ops;
        $downloadUrl = Plugin::getApiUrl() . '/styles/' . $styleJSON->id . '/archive';
        $style->downloadUrl = $downloadUrl;
        return $style;
    }


    public function isImported() {
        if ($this->imported === null) {
            $api = new Polyshapes();
            $this->imported = $api->isImported($this);
        }
        return $this->imported;
    }


    /**
     * @return mixed
     */
    public function getOps() {
        return $this->ops;
    }


    /**
     * @return mixed
     */
    public function getShapeId() {
        return $this->shapeId;
    }

    /**
     * @return mixed
     */
    public function getAccountId() {
        return $this->accountId;
    }

    /**
     * @return mixed
     */
    public function getDownloadUrl() {
        return $this->downloadUrl;
    }


    /**
     * @return mixed
     */
    public function isPreset() {
        return $this->isPreset;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

}
