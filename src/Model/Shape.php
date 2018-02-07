<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:18
 */

namespace Polyshapes\Plugin\Model;


class Shape {

    private $cablesId;
    private $title;
    private $created;
    private $updated;
    private $id;
    private $customizerData;
    private $presets;
    private $hasScreenshotSeries;
    private $info;

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
        $shape->setCablesId($jsonshape->cablesId);
        $shape->setTitle($jsonshape->title);
        $shape->setCreated($jsonshape->created);
        $shape->setUpdated($jsonshape->updated);
        $shape->setId($jsonshape->id);
        $shape->setCustomizerData(CustomizerData::fromJson($jsonshape->customizerData));
        $shape->setPresets(Preset::fromJson($jsonshape->presets));
        $shape->setHasScreenshotSeries($jsonshape->hasScreenshotSeries);
        $shape->setInfo($jsonshape->info);
        return $shape;
    }

    /**
     * @param mixed $cablesId
     */
    private function setCablesId($cablesId) {
        $this->cablesId = $cablesId;
    }

    /**
     * @param mixed $title
     */
    private function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @param mixed $created
     */
    private function setCreated($created) {
        $this->created = $created;
    }

    /**
     * @param mixed $updated
     */
    private function setUpdated($updated) {
        $this->updated = $updated;
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
    public function getCablesId() {
        return $this->cablesId;
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
    public function getCreated() {
        return $this->created;
    }

    /**
     * @return mixed
     */
    public function getUpdated() {
        return $this->updated;
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


}
