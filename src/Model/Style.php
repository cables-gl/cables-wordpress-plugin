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

class Style {

    private $title;
    private $id;
    private $customizerData;
    private $presets;
    private $hasScreenshotSeries;
    private $info;
    private $json;
    private $downloadUrl;
    private $patchName;
    private $imported;

    /**
     * Shape constructor. Private, use factory-method
     */
    private function __construct() {
    }

    /**
     * @param $jsonshape
     * @return Style
     */
    public static function fromJson($jsonshape): Style {
        $shape = new Style();
        $shape->setTitle($jsonshape->name);
        $shape->setId($jsonshape->id);
        $shape->setJson(json_encode($jsonshape));
        $shape->setPatchName('polyshapes');
        $downloadUrl = Plugin::getApiUrl() . '/styles/' . $jsonshape->id . '/archive';
        $shape->setDownloadUrl($downloadUrl);
        return $shape;
    }

    /**
     * @return mixed
     */
    public function getPatchName() {
        return $this->patchName;
    }

    /**
     * @param mixed $patchName
     */
    public function setPatchName($patchName) {
        $this->patchName = $patchName;
    }

    /**
     * @return mixed
     */
    public function getDownloadUrl() {
        return $this->downloadUrl;
    }

    /**
     * @param mixed $downloadUrl
     */
    public function setDownloadUrl($downloadUrl) {
        $this->downloadUrl = $downloadUrl;
    }

    /**
     * @param mixed $title
     */
    private function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @param mixed $id
     */
    private function setId($id) {
        $this->id = $id;
    }

    /**
     * @param CustomizerData $customizerData
     */
    private function setCustomizerData(CustomizerData $customizerData) {
        $this->customizerData = $customizerData;
    }

    /**
     * @param mixed $presets
     */
    private function setPresets($presets) {
        $this->presets = $presets;
    }

    /**
     * @param mixed $hasScreenshotSeries
     */
    private function setHasScreenshotSeries($hasScreenshotSeries) {
        $this->hasScreenshotSeries = $hasScreenshotSeries;
    }

    /**
     * @param mixed $info
     */
    private function setInfo($info) {
        $this->info = $info;
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return CustomizerData
     */
    public function getCustomizerData(): CustomizerData {
        return $this->customizerData;
    }

    /**
     * @return mixed
     */
    public function getPresets() {
        return $this->presets;
    }

    /**
     * @return mixed
     */
    public function getHasScreenshotSeries() {
        return $this->hasScreenshotSeries;
    }

    /**
     * @return mixed
     */
    public function getInfo() {
        return $this->info;
    }

    /**
     * @return mixed
     */
    public function getJson() {
        return $this->json;
    }

    /**
     * @param mixed $json
     */
    private function setJson($json) {
        $this->json = $json;
    }

    public function isImported() {
        if($this->imported === null) {
            $api = new Polyshapes();
            $this->imported = $api->isImported($this);
        }
        return $this->imported;
    }

}
