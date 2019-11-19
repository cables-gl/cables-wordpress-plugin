<?php
/**
 * Created by IntelliJ IDEA.
 * User: stephan
 * Date: 07.02.18
 * Time: 17:54
 */

namespace Cables\Plugin;


use Cables\Plugin\TemplateEngine\TemplateEngine;

class Frontend {

    /**
     * @var TemplateEngine
     */
    private $template;

  /**
   * Frontend constructor.
   * @param TemplateEngine $template
   */
    public function __construct(TemplateEngine $template) {
        $this->template = $template;
    }

    public function display() {
        add_action('wp_footer', array($this, 'cables_patch_footer'));
    }

    public function cables_patch_footer() {

        $options = Plugin::getPluginOptions();
        $patches = array();
        foreach ($this->getElementReplacingPatchIds() as $patchId) {
            $api = new Api\Cables();
            $patch = $api->getPatch($patchId);
            $patchOptions = $options['patches'][$patchId];
            if (!$this->isActiveByPageType($patchOptions)) {
                continue;
            }
            if (!$this->isActiveByMobileSetting($patchOptions)) {
                continue;
            }

            $patchOptions = array(
                'patch' => $patch,
                'patchConfig' => $patchOptions,
                'background' => !!$patchOptions['background'],
                'cssSelector' => $patchOptions['cssSelector'],
                'patchDir' => $api->getPatchDirUrl($patch)
            );

            $patches[] = $patchOptions;
        }

        $content = "";
        if (!empty($patches)) {
            $template = $this->template->loadTemplate('frontend/patch.footer');
            $content = $this->template->render($template, array('patches' => $patches));
        }

        echo $content;

    }

    private function getElementReplacingPatchIds() {
        $ids = array();
        $options = Plugin::getPluginOptions();
        if (is_array($options['patches'])) {
            foreach ($options['patches'] as $id => $patch) {
                foreach ($patch['integrations'] as $key => $value) {
                    if ($value === 'on') {
                        $ids[] = $id;
                    }
                }
            }
        }
        return $ids;
    }

    private function isActiveByPageType($patchOptions) {
        $pageOptions = $patchOptions['page_types'];
        $isHome = (is_home() || is_front_page());
        if (!is_array($pageOptions)) {
            return false;
        } else if ($isHome && $pageOptions['home'] == 'on') {
            return true;
        } else if ((!$isHome && is_page()) && $pageOptions['page'] == 'on') {
            return true;
        } else if (is_single() && $pageOptions['post'] == 'on') {
            return true;
        } else if ($pageOptions['all'] == 'on') {
            return true;
        } else {
            return false;
        }
    }

    private function isActiveByMobileSetting($patchOptions) {
        if (wp_is_mobile()) {
            if (array_key_exists('mobile', $patchOptions)) {
                return ($patchOptions['mobile'] == "on");
            }
        }
        return true;
    }

}
