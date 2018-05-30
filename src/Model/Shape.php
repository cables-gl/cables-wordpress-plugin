<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:18
 */

namespace Polyshapes\Plugin\Model;


class Shape {

    private $title;
    private $id;
    private $customizerData;
    private $presets;
    private $hasScreenshotSeries;
    private $info;
    private $json;
    private $fileName;

    /**
     * Shape constructor. Private, use factory-method
     */
    private function __construct() {
    }

    /**
     * @param $jsonshape
     * @return Shape
     */
    public static function fromJson($jsonshape): Shape {
        $shape = new Shape();
        $shape->setTitle($jsonshape->title);
        $shape->setId($jsonshape->_id);
        $shape->setJson(json_encode($jsonshape));
        $shape->setFileName($jsonshape->file_shape);
        return $shape;
    }

    /**
     * @return mixed
     */
    public function getFileName() {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName) {
        $this->fileName = $fileName;
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

}
