<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:17
 */

namespace Polyshapes\Plugin\Api;

use Polyshapes\Plugin\Frontend;
use Polyshapes\Plugin\Model\Shape;
use Polyshapes\Plugin\Plugin;

class Polyshapes {

    private static $BASE_URL = 'https://dev.polyshapes.io/api';

    /**
     * @return Shape
     */
    public function getShape(string $id): Shape {
        $response = $this->getRemote('/shapes/shape/' . $id);
        $jsonshape = json_decode($response['body']);
        return Shape::fromJson($jsonshape->shape);
    }

    private function getRemote(string $method): array {
        $options = get_option('polyshapes_backend');
        $args = array(
            'headers' => array(
                'X-Api-Key' => $options['api_key'],
                'X-Client-Id' => $this->getClientId()
            )
        );
        return wp_remote_get(static::$BASE_URL . $method, $args);
    }

    private function getClientId() {
        $url = parse_url(get_site_url());
        $clientId = $url['host'];
        return $clientId;
    }

    /**
     * @return Shape[]
     */
    public function getAllShapes(): array {
        $response = $this->getRemote('/shapes');
        $jsonshapes = json_decode($response['body']);
        $shapes = array();
        foreach ($jsonshapes->shapes as $jsonshape) {
            $shapes[] = Shape::fromJson($jsonshape);
        }
        return $shapes;
    }

    public function login($email, $password) {
        $clientId = $this->getClientId();
        $params = array(
            'email' => $email,
            'password' => $password,
            'clientid' => $clientId
        );
        $response = $this->postRemote('/user/login', $params);
        return json_decode($response['body']);
    }

    private function postRemote(string $method, $params = array()) {
        return wp_safe_remote_post(static::$BASE_URL . $method, array('body' => $params));
    }

    public function isImported(Shape $shape): bool {
        $filename = Plugin::$basePath . 'public/patches/' . $shape->getId() . '/cables.txt';
        return file_exists($filename);
    }

    public function getShapeDirUrl(Shape $shape) {
        $filename = Plugin::$baseUrl . 'public/patches/' . $shape->getId() . '/';
        return $filename;
    }

    public function importShape(Shape $shape) {

        $method = '/shapes/shape/' . $shape->getId() . '/package';
        $response = $this->getRemote($method);

        WP_Filesystem();
        $upload_dir = wp_upload_dir();
        $filename = trailingslashit($upload_dir['path']) . $shape->getId() . '.zip';

        // by this point, the $wp_filesystem global should be working, so let's use it to create a file
        global $wp_filesystem;
        if (!$wp_filesystem->put_contents($filename, $response['body'], FS_CHMOD_FILE)) {
            echo 'error saving file!';
        }

        $destination_path = Plugin::$basePath . 'public/patches/' . $shape->getId() . '/';
        $unzipfile = unzip_file($filename, $destination_path);
        if (is_wp_error($unzipfile)) {
            print_r($unzipfile);
            echo 'There was an error unzipping the file.';
        }
    }


}
