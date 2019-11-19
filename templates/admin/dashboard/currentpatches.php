<div class="wrap">
    <h3><span><?php echo i18n("page_dashboard_currentpatches_headline"); ?></span></h3>
    <p><?php echo i18n("page_dashboard_currentpatches_text"); ?></p>

    <?php foreach($context['integratedPatches'] as $id => $integratedPatch): ?>
        <div id="col-container">
            <div class="ps-col-left">
                <div class="ps-screenshot-container">
                    <img alt="<?php echo $integratedPatch->name; ?>" src="<?php echo cables_patch_screenshot_url($id); ?>"/>
                </div>
                <div>
                    <?php echo i18n("page_dashboard_currentpatches_patch"); ?> <a href="c"><?php echo $integratedPatch->name; ?></a><br/>
                </div>
            </div>
            <div class="ps-col-right">
                <?php echo i18n("page_dashboard_currentpatches_current_pages_integration"); ?>
                <ul>
                    <?php if($integratedPatch->page_type): foreach($integratedPatch->page_type as $pagetype => $value): ?>
                        <li><?php echo $pagetype; ?></li>
                    <?php endforeach; endif; ?>
                </ul>
                <?php echo i18n("page_dashboard_currentpatches_current_website_integration"); ?>
                <ul>
                    <?php if($integratedPatch->integrations): foreach($integratedPatch->integrations as $integration => $value):; ?>
                        <li><?php echo $integration; ?></li>
                    <?php endforeach; endif; ?>
                </ul>
                <a href="?page=cables_backend_patch&patch=<?php echo $id; ?>" class="button-primary"><?php echo i18n("page_dashboard_currentpatches_change_website_integration"); ?></a>
            </div>
        </div>
        <div style="clear:both;"></div>
    <?php endforeach; ?>
</div>
