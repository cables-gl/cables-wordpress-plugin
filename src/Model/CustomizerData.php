<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 11:40
 */

namespace Polyshapes\Plugin\Model;


class CustomizerData {

    private $parameters;
    private $tabs;

    /**
     * CustomizerData constructor. Private, use factory-method
     */
    private function __construct() {
    }

    /**
     * @param $jsonshape
     * @return CustomizerData
     */
    public static function fromJson($jsonshape): CustomizerData {
        $data = new CustomizerData();
        $data->setParameters($jsonshape->parameters);
        $data->setTabs($jsonshape->tabs);
        return $data;
    }

    /**
     * @return mixed
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * @param mixed $parameters
     */
    private function setParameters($parameters) {
        $this->parameters = $parameters;
    }

    /**
     * @return mixed
     */
    public function getTabs() {
        return $this->tabs;
    }

    /**
     * @param mixed $tabs
     */
    private function setTabs($tabs) {
        $this->tabs = $tabs;
    }



}
