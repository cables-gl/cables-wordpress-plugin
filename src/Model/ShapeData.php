<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 14.02.18
 * Time: 13:35
 */

namespace Polyshapes\Plugin\Model;


class ShapeData {

    private $size;
    private $path;
    private $log;

    /**
     * Shape constructor. Private, use factory-method
     */
    private function __construct() {
    }

    /**
     * @param $jsonshape
     * @return Patch
     */
    public static function fromJson($json): ShapeData {
        $export = new ShapeData();
        $export->setSize($json->size);
        $export->setPath($json->path);
        $export->setLog($json->log);
        return $export;
    }

    /**
     * @param mixed $size
     */
    private function setSize($size) {
        $this->size = $size;
    }

    /**
     * @param mixed $path
     */
    private function setPath($path) {
        $this->path = $path;
    }

    /**
     * @param mixed $log
     */
    private function setLog($log) {
        $this->log = $log;
    }

    /**
     * @return mixed
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * @return mixed
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getLog() {
        return $this->log;
    }


}
