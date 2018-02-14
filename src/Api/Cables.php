<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 14.02.18
 * Time: 13:15
 */

namespace Polyshapes\Plugin\Api;


use Polyshapes\Plugin\Model\Patch;
use Polyshapes\Plugin\Model\PatchExport;

class Cables {

    private static $CABLES_URL = 'https://cables.gl';
    private static $API_URL = 'https://cables.gl/api';

    /**
     * @return string
     */
    public function getPatchExport(string $id) : PatchExport {
        $response = $this->callRemote(static::$API_URL, '/project/' . $id . '/export');
        $jsonshape = json_decode($response['body']);
        return PatchExport::fromJson($jsonshape);
    }

    /**
     * @return Patch[]
     */
    public function getMyPatches() : array {
        $response = $this->callRemote(static::$API_URL, '/myprojects');
        $jsonshapes = json_decode($response['body']);
        $shapes = array();
        foreach ($jsonshapes as $jsonshape) {
            $shapes[] = Patch::fromJson($jsonshape);
        }
        return $shapes;
    }

    public function importPatch(string $patchId, PatchExport $export) {

        $response = $this->callRemote(static::$CABLES_URL, $export->getPath());

        WP_Filesystem();
        $upload_dir = wp_upload_dir();
        $filename = trailingslashit($upload_dir['path']).$patchId.'.zip';

        // by this point, the $wp_filesystem global should be working, so let's use it to create a file
        global $wp_filesystem;
        if ( ! $wp_filesystem->put_contents( $filename, $response['body'], FS_CHMOD_FILE) ) {
            echo 'error saving file!';
        }

        $destination_path = plugin_dir_path(__FILE__) . '../../public/patches/' . $patchId . '/';
        $unzipfile = unzip_file( $filename, $destination_path);
        if ( is_wp_error( $unzipfile ) ) {
            echo 'There was an error unzipping the file.';
        }
    }

    private function callRemote(string $baseUrl, string $method): array {
        $options = get_option('polyshapes_backend');
        $args = array(
            'timeout' => 120,
            'headers' => array(
                'X-apikey' => $options['cables_api_key']
            )
        );
        $response = wp_remote_get($baseUrl . $method, $args);
        if($response instanceof \WP_Error) {
            print_r($response);
            die();
        }
        return $response;
    }

    public function isImported($patchId) : bool {
        $filename = plugin_dir_path(__FILE__) . '../../public/patches/' . $patchId . '/cables.txt';
        return file_exists($filename);
    }

    public function getPatchDirUrl($patchId) {
        $filename = plugin_dir_url(__FILE__) . '../../public/patches/' . $patchId . '/';
        return $filename;
    }

}
