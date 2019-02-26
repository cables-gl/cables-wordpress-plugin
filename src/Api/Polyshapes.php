<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:17
 */

namespace Polyshapes\Plugin\Api;

use Polyshapes\Plugin\Model\Shape;
use Polyshapes\Plugin\Plugin;

class Polyshapes {

    /**
     * @return Shape
     */
    public function getShape(string $id): Shape {
        $response = $this->getRemote('/shapes/' . $id);
        $jsonshape = json_decode($response['body']);
        return Shape::fromJson($jsonshape);
    }

    private function getRemote(string $method, array $params = array()): array {
        $options = Plugin::getPluginOptions();
        $args = array(
            'headers' => array(
                'X-Api-Key' => $options['api_key'],
                'X-Client-Id' => $this->getClientId()
            )
        );
        $args = array_merge_recursive($args, $params);
        return wp_remote_get(Plugin::getApiUrl() . $method, $args);
    }

    private function getClientId() {
        return "wordpress";
    }

    /**
     * @return Shape[]
     */
    public function getAllShapes(): array {
        $response = $this->getRemote('/shapes');
        $jsonshapes = json_decode($response['body']);
        $shapes = array();
        foreach ($jsonshapes as $jsonshape) {
            $shapes[] = Shape::fromJson($jsonshape);
        }
        return $shapes;
    }

    public function login($email, $password) {
        $clientId = $this->getClientId();
        $params = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($email . ':' . $password)
            )
        );
        $response = $this->getRemote('/auth?clientId=' . $clientId, $params);
        return json_decode($response['body']);
    }

    private function postRemote(string $method, $params = array()) {
        return wp_safe_remote_post(Plugin::getApiUrl() . $method, array('body' => $params));
    }

    public function isImported(Shape $shape): bool {
        $filename = Plugin::getBasePath() . 'public/patches/' . $shape->getId() . '/cables.txt';
        return file_exists($filename);
    }

    public function getShapeDirUrl(Shape $shape) {
        $filename = Plugin::getBaseUrl() . 'public/patches/' . $shape->getId() . '/';
        return $filename;
    }

    public function importShape(Shape $shape) {

        $method = '/shapes/' . $shape->getId() . '/archive';
        $response = $this->getRemote($method);

        WP_Filesystem();
        $upload_dir = wp_upload_dir();
        $filename = trailingslashit($upload_dir['path']) . $shape->getId() . '.zip';


        // by this point, the $wp_filesystem global should be working, so let's use it to create a file
        global $wp_filesystem;
        if (!$wp_filesystem->put_contents($filename, $response['body'], FS_CHMOD_FILE)) {
            echo 'error saving file!';
        }

        $destination_path = Plugin::getBasePath() . 'public/patches/' . $shape->getId() . '/';
        $unzipfile = unzip_file($filename, $destination_path);
        if (is_wp_error($unzipfile)) {
            print_r($unzipfile);
            echo 'There was an error unzipping the file.';
        }
    }


}
