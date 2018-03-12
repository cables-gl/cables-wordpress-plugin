<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 12.03.18
 * Time: 16:46
 */

namespace Cables\Plugin\Wordpress;


use Cables\Api\Interfaces\Storage;
use Cables\Api\Model\Patch;
use Cables\Api\Model\PatchExport;

class WordpressCablesStorage implements Storage {

    /**
     * WordpressCablesStorage constructor.
     */
    public function __construct() {
    }

    public function storePatch(PatchExport $export, string $content) {

        WP_Filesystem();
        $upload_dir = wp_upload_dir();
        $filename = trailingslashit($upload_dir['path']).$export->getId().'.zip';

        // by this point, the $wp_filesystem global should be working, so let's use it to create a file
        global $wp_filesystem;
        if ( ! $wp_filesystem->put_contents( $filename, $content, FS_CHMOD_FILE) ) {
            echo 'error saving file!';
        }

        $destination_path = $this->getPatchDir($export->getId());
        $unzipfile = unzip_file( $filename, $destination_path);
        if ( is_wp_error( $unzipfile ) ) {
            print_r($unzipfile);
            echo 'There was an error unzipping the file.';
        }
    }

    public function isImported(Patch $patch): bool {
        $filename = plugin_dir_path(__FILE__) . '../public/patches/' . $patch->getId() . '/cables.txt';
        return file_exists($filename);
    }

    public function getPatchDirUrl(Patch $patch): string {
        $filename = plugin_dir_url(__FILE__) . '../public/patches/' . $patch->getId() . '/';
        return $filename;
    }

    public function deletePatch(Patch $patch) {
        $dir = $this->getPatchDir($patch->getId());
        $this->rrmdir($dir);
    }

    private function getPatchDir($patchId): string {
        return plugin_dir_path(__FILE__) . '../public/patches/' . $patchId . '/';
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir"){
                        $this->rrmdir($dir."/".$object);
                    }else{
                        unlink($dir."/".$object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}
