<?php
namespace Cables\Plugin\TemplateEngine;

use Cables\Plugin\Plugin;

class Php implements TemplateEngine {

  public function loadTemplate($templateName){
    return plugin_dir_path( __FILE__ ) . '../../templates/' . $templateName . '.php';
  }

  public function render($template, $context) {

    $cables_url = Plugin::getConfig()['cables_url'];
    $cables_api_url = Plugin::getConfig()['cables_api_url'];
    $context['cables_api_url'] = $cables_api_url;
    $context['cables_url'] = $cables_url;
    $context['cables_screenshot_baseurl'] = Plugin::getScreenshotBaseUrl();
    include($template);
  }
}