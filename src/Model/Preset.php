<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 11:43
 */

namespace Polyshapes\Plugin\Model;


class Preset {

    private $parameters;
    private $iconColors;

    /**
     * Preset constructor. Private, use factory-method
     */
    private function __construct() {
    }

    /**
     * @param $jsonshape
     * @return Preset
     */
    public static function fromJson($jsonshape): Preset {
        $preset = new Preset();
        $preset->setParameters($jsonshape->parameters);
        $preset->setIconColors($jsonshape->iconColors);
        return $preset;
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
    public function getIconColors() {
        return $this->iconColors;
    }

    /**
     * @param mixed $iconColors
     */
    private function setIconColors($iconColors) {
        $this->iconColors = $iconColors;
    }



}
