<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:17
 */

namespace Polyshapes\Plugin\Api;

use Polyshapes\Plugin\Model\Shape;

class Polyshapes {

    private static $API_USERNAME = 'polyshapes';
    private static $API_PASSWORD = 'undefined';
    private static $BASE_URL = 'https://dev.polyshapes.io/api';

    /**
     * @return Shape
     */
    public function getShape(string $id) : Shape {
        $response = $this->callRemote('/shape/' . $id);
        $jsonshape = json_decode($response['body']);
        return Shape::fromJson($jsonshape);
    }

    /**
     * @return Shape[]
     */
    public function getAllShapes(): array {
        $response = $this->callRemote('/list');
        $jsonshapes = json_decode($response['body']);
        $shapes = array();
        foreach ($jsonshapes as $jsonshape) {
            $shapes[] = Shape::fromJson($jsonshape);
        }
        return $shapes;
    }

    private function callRemote(string $method): array {
        $args = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode(static::$API_USERNAME . ':' . static::$API_PASSWORD)
            )
        );
        return wp_remote_get(static::$BASE_URL . $method, $args);
    }

    public function getJavscriptForShape(Shape $shape) : string {
        $response = $this->callRemote('/p/' . $shape->getId());
        return $response['body'];
    }

}
