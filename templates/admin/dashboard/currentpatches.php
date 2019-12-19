<div class="wrap">
    <h3><span><?php echo i18n("page_dashboard_currentpatches_headline"); ?></span></h3>
    <p><?php echo i18n("page_dashboard_currentpatches_text"); ?></p>

    <?php foreach($context['integratedPatches'] as $id => $integratedPatch): ?>
        <div id="col-container">
            <div class="ps-col-left">
                <div class="ps-screenshot-container">
                    <img alt="<?php echo $integratedPatch->name; ?>" src="<?php echo cables_patch_screenshot_url($id); ?>"/>
                </div>
            </div>
            <div class="ps-col-right">
                <a href="?page=cables_backend_patch&patch=<?php echo $id; ?>" class="button-primary"><?php echo i18n("page_dashboard_currentpatches_change_website_integration"); ?></a>
            </div>
        </div>
        <div style="clear:both;"></div>
    <?php endforeach; ?>
</div>
