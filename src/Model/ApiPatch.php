<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:18
 */

namespace Cables\Plugin\Model;


use Cables\Plugin\Api\Cables;
use Cables\Plugin\Plugin;

class ApiPatch {

    public $id;
    public $name;
    public $shapeId;
    public $accountId;
    public $downloadUrl;
    public $isPreset;
    public $imported;

    /**
     * ApiPatch constructor. Private, use factory-method
     */
    private function __construct() {
    }

    /**
     * @param $patchJSON
     * @return ApiPatch
     */
    public static function fromJson($patchJSON): ApiPatch {
        $patch = new ApiPatch();
        $patch->name = $patchJSON->name;
        $patch->id = $patchJSON->_id;
        return $patch;
    }


    public function isImported() {
        if ($this->imported === null) {
            $api = new Cables();
            $this->imported = $api->isImported($this);
        }
        return $this->imported;
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
