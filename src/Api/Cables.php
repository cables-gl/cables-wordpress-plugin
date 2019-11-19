<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:17
 */

namespace Cables\Plugin\Api;

use Cables\Plugin\Model\ApiPatch;
use Cables\Plugin\Plugin;

class Cables {

    /**
     * @return ApiPatch
     */
    public function getStyle(string $id): ApiPatch {
        $response = $this->getRemote('/mypatches/');
        $patchesJSON = json_decode($response['body'])->patches;
        $patchJSON = "{}";
        foreach ($patchesJSON as $patch) {
          if($patch->_id == $id) {
            $patchJSON = $patch;
            break;
          }
        }
        return ApiPatch::fromJson($patchJSON);
    }

    private function getRemote(string $method, array $params = array(), $methodIsUrl = false) {
        $options = Plugin::getPluginOptions();
        $args = array(
            'headers' => array(
                'x-api-key' => $options['api_key'],
                'x-client-id' => $this->getClientId()
            )
        );
        $args = array_merge_recursive($args, $params);
        $url = Plugin::getApiUrl() . $method;
        if($methodIsUrl) {
          $url = $method;
        }
        $response = wp_remote_get($url, $args);
        return $response;
    }

    private function getClientId() {
        $url = parse_url(get_site_url());
        return "wordpress_" . $url['host'] . $url['path'];
    }

    private function getAccountId() {
        return Plugin::getPluginOption(Plugin::OPTIONS_ACCOUNT_ID);
    }

    /**
     * @return ApiPatch[]
     */
    public function getMyPatches(): array {
        $url = '/mypatches';
        $response = $this->getRemote($url);
        $patchesJSON = json_decode($response['body'])->patches;
        $patches = array();
        foreach ($patchesJSON as $patchJSON) {
            $patches[] = ApiPatch::fromJson($patchJSON);
        }
        return $patches;
    }

    public function login($email, $password) {
        $clientId = $this->getClientId();
        $params = array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($email . ':' . $password)
            )
        );
        $response = $this->getRemote('/auth?createIfMissing=true&clientId=' . $clientId, $params);
        return json_decode($response['body']);
    }

    public function getAccountInfo() {
        $url = '/accounts/' . $this->getAccountId();
        $response = $this->getRemote($url);
        $accountJSON = json_decode($response['body']);
        return $accountJSON;
    }

    public function getStyleScreenshot($styleId) {
        $url = '/styles/' . $styleId . '/screenshot';
        $response = $this->getRemote($url);
        return $response['body'];
    }

    private function postRemote(string $method, $params = array()) {
        return wp_safe_remote_post(Plugin::getApiUrl() . $method, array('body' => $params));
    }

    public function isImported(ApiPatch $style): bool {
        $filename = Plugin::getBasePath() . 'public/patches/' . $style->getId() . '/cables.txt';
        return file_exists($filename);
    }

    public function getStyleDirUrl(ApiPatch $style) {
        $filename = Plugin::getBaseUrl() . 'public/patches/' . $style->getId() . '/';
        return $filename;
    }

    public function importPatch(ApiPatch $patch) {

        $method = '/project/' . $patch->getId() . '/export?removeIndexHtml=1&compatibility=old&jsonName=patch';
        $response = $this->getRemote($method, array('timeout' => 120));

        $body = $response['body'];
        $export = json_decode($body);
        if($export->path) {

          $patchUrl = Plugin::getConfig()['cables_url'] . $export->path;

          $response = $this->getRemote($patchUrl, array('timeout' => 120), true);

          WP_Filesystem();
          $upload_dir = wp_upload_dir();
          $filename = trailingslashit($upload_dir['path']) . $patch->getId() . '.zip';

          // by this point, the $wp_filesystem global should be working, so let's use it to create a file
          global $wp_filesystem;
          $chmod_file = (0644 & ~ umask());
          if ( defined( 'FS_CHMOD_FILE' ) ) {
            $chmod_file = FS_CHMOD_FILE;
          }
          if (!$wp_filesystem->put_contents($filename, $response['body'], $chmod_file)) {
            echo '<div class="error">error saving file!</div>';
          }

          $destination_path = Plugin::getBasePath() . 'public/patches/' . $patch->getId() . '/';
          $unzipfile = unzip_file($filename, $destination_path);
          if (is_wp_error($unzipfile)) {
            var_dump($unzipfile);
            echo '<div class="error">There was an error unzipping the file.</div>';
          }
        }else{
          echo '<div class="error">error getting information for patch</div>';
        }
    }


}
