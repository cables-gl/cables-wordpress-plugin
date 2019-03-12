<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 10:17
 */

namespace Polyshapes\Plugin\Api;

use Polyshapes\Plugin\Model\ApiStyle;
use Polyshapes\Plugin\Plugin;

class Polyshapes {

    /**
     * @return ApiStyle
     */
    public function getStyle(string $id): ApiStyle {
        $response = $this->getRemote('/styles/' . $id);
        $styleJSON = json_decode($response['body']);
        return ApiStyle::fromJson($styleJSON);
    }

    private function getRemote(string $method, array $params = array()) {
        $options = Plugin::getPluginOptions();
        $args = array(
            'headers' => array(
                'X-Api-Key' => $options['api_key'],
                'X-Client-Id' => $this->getClientId()
            )
        );
        $args = array_merge_recursive($args, $params);
        $url = Plugin::getApiUrl() . $method;
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
     * @return ApiStyle[]
     */
    public function getMyStyles(): array {
        $url = '/accounts/' . $this->getAccountId() . '/styles';
        $response = $this->getRemote($url);
        $stylesJSON = json_decode($response['body']);
        $styles = array();
        foreach ($stylesJSON as $styleJSON) {
            $styles[] = ApiStyle::fromJson($styleJSON);
        }
        return $styles;
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

    private function postRemote(string $method, $params = array()) {
        return wp_safe_remote_post(Plugin::getApiUrl() . $method, array('body' => $params));
    }

    public function isImported(ApiStyle $style): bool {
        $filename = Plugin::getBasePath() . 'public/styles/' . $style->getId() . '/cables.txt';
        return file_exists($filename);
    }

    public function getStyleDirUrl(ApiStyle $style) {
        $filename = Plugin::getBaseUrl() . 'public/styles/' . $style->getId() . '/';
        return $filename;
    }

    public function importStyle(ApiStyle $style) {

        $method = '/styles/' . $style->getId() . '/archive';
        $response = $this->getRemote($method);

        WP_Filesystem();
        $upload_dir = wp_upload_dir();
        $filename = trailingslashit($upload_dir['path']) . $style->getId() . '.zip';


        // by this point, the $wp_filesystem global should be working, so let's use it to create a file
        global $wp_filesystem;
        if (!$wp_filesystem->put_contents($filename, $response['body'], FS_CHMOD_FILE)) {
            echo 'error saving file!';
        }

        $destination_path = Plugin::getBasePath() . 'public/styles/' . $style->getId() . '/';
        $unzipfile = unzip_file($filename, $destination_path);
        if (is_wp_error($unzipfile)) {
            print_r($unzipfile);
            echo 'There was an error unzipping the file.';
        }
    }


}
