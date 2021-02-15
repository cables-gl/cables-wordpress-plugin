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

class Cables
{

  /**
   * @return ApiPatch
   */
  public function getPatch(string $patchId): ApiPatch
  {
    $patchFile = Plugin::getBasePath() . 'public/patches/' . $patchId . '/patch.json';
    if(file_exists($patchFile)) {
        $patchJSON = json_decode(file_get_contents($patchFile));
    }else{
        $response = $this->getRemote('/mypatches/');
        $patchesJSON = json_decode($response['body'])->patches;
        $patchJSON = "{}";
        foreach ($patchesJSON as $patch) {
            if ($patch->_id == $patchId) {
                $patchJSON = $patch;
                break;
            }
        }
        file_put_contents($patchFile, json_encode($patchJSON, JSON_PRETTY_PRINT));
    }
    $apiPatch = ApiPatch::fromJson($patchJSON);
    return $apiPatch;
  }

  private function getRemote(string $method, array $params = array(), $methodIsUrl = false)
  {
    $options = Plugin::getPluginOptions();
    $args = array(
      'headers' => array(
        'x-api-key' => $options['api_key'],
        'x-client-id' => $this->getClientId()
      )
    );
    $args = array_merge_recursive($args, $params);
    $url = Plugin::getApiUrl() . $method;
    if ($methodIsUrl) {
      $url = $method;
    }
    $response = wp_remote_get($url, $args);
    return $response;
  }

  private function getClientId()
  {
    $client_id = "wordpress";
    $url = parse_url(get_site_url());
    if(isset($url['host'])) {
      $client_id = $client_id . "_" . $url['host'];
    }
    if(isset($url['host'])) {
      $client_id = $client_id . "_" . $url['path'];
    }
    return $client_id;
  }

  private function getAccountId()
  {
    return Plugin::getPluginOption(Plugin::OPTIONS_ACCOUNT_ID);
  }

  /**
   * @return ApiPatch[]
   */
  public function getMyPatches(): array
  {
    $url = '/mypatches';
    $response = $this->getRemote($url);
    $patchesJSON = json_decode($response['body'])->patches;
    $patches = array();
    foreach ($patchesJSON as $patchJSON) {
      $patches[] = ApiPatch::fromJson($patchJSON);
    }
    return $patches;
  }

  public function login($email, $password)
  {
    $clientId = $this->getClientId();
    $params = array(
      'headers' => array(
        'Authorization' => 'Basic ' . base64_encode($email . ':' . $password)
      )
    );
    $response = $this->getRemote('/auth?createIfMissing=true&clientId=' . $clientId, $params);
    return json_decode($response['body']);
  }

  public function getAccountInfo()
  {
    $url = '/accounts/' . $this->getAccountId();
    $response = $this->getRemote($url);
    $accountJSON = json_decode($response['body']);
    return $accountJSON;
  }

  public function getPatchScreenshot($patchId)
  {
    $url = '/patches/' . $patchId . '/screenshot';
    $response = $this->getRemote($url);
    return $response['body'];
  }

  private function postRemote(string $method, $params = array())
  {
    return wp_safe_remote_post(Plugin::getApiUrl() . $method, array('body' => $params));
  }

  public function isImported(ApiPatch $patch): bool
  {
    $filename = Plugin::getBasePath() . 'public/patches/' . $patch->getId() . '/cables.txt';
    return file_exists($filename);
  }

  public function getPatchDirUrl(ApiPatch $patch)
  {
    $filename = Plugin::getBaseUrl() . 'public/patches/' . $patch->getId() . '/';
    return $filename;
  }

  public function importPatch(ApiPatch $patch)
  {

    $method = '/project/' . $patch->getId() . '/export?a=1&assets=auto&combineJS=true&compatibility=modern&removeIndexHtml=1&jsonName=patch';
    $response = $this->getRemote($method, array('timeout' => 120));

    $body = $response['body'];
    $export = json_decode($body);
    if ($export->path) {

      $patchUrl = Plugin::getConfig()['cables_url'] . $export->path;

      $response = $this->getRemote($patchUrl, array('timeout' => 120), true);

      WP_Filesystem();
      $upload_dir = wp_upload_dir();
      $filename = trailingslashit($upload_dir['path']) . $patch->getId() . '.zip';

      // by this point, the $wp_filesystem global should be working, so let's use it to create a file
      global $wp_filesystem;
      $chmod_file = (0644 & ~umask());
      if (defined('FS_CHMOD_FILE')) {
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
    } else {
      echo '<div class="error">error getting information for patch</div>';
    }
  }

  public function deletePatchFiles($patchId)
  {
    WP_Filesystem();
    global $wp_filesystem;
    $wp_filesystem->rmdir(dirname(Plugin::getBasePath() . 'public/patches/' . $patchId . '/'), true);
  }


}
