<?php
namespace Cables\Plugin\TemplateEngine;

interface TemplateEngine {

  public function loadTemplate($templateName);

  public function render($template, $context);

}