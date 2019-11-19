<div class="wrap">
    <h3><span><?php echo i18n("page_dashboard_currentpatches_headline"); ?></span></h3>
    <p><?php echo i18n("page_dashboard_currentpatches_text"); ?></p>

    <?php foreach($context['integratedStyles'] as $id => $integratedStyle): ?>
        <div id="col-container">
            <div class="ps-col-left">
                <div class="ps-screenshot-container">
                    <img alt="<?php echo $integratedStyle->name; ?>" src="<?php echo cables_patch_screenshot_url($id); ?>"/>
                </div>
                <div>
                    <?php echo i18n("page_dashboard_currentpatches_style"); ?> <a href="c"><?php echo $integratedStyle->name; ?></a><br/>
                    <?php echo i18n("page_dashboard_currentpatches_style_created"); ?> <?php echo $integratedStyle->createdAt; ?><br/>
                    <?php echo i18n("page_dashboard_currentpatches_original_patch"); ?> <?php echo $integratedStyle->shapeName; ?>
                </div>
            </div>
            <div class="ps-col-right">
                <?php echo i18n("page_dashboard_currentpatches_current_pages_integration"); ?>
                <ul>
                    <?php if($integratedStyle->page_type): foreach($integratedStyle->page_type as $pagetype => $value): ?>
                        <li><?php echo $pagetype; ?></li>
                    <?php endforeach; endif; ?>
                </ul>
                <?php echo i18n("page_dashboard_currentpatches_current_website_integration"); ?>
                <ul>
                    <?php if($integratedStyle->integrations): foreach($integratedStyle->integrations as $integration => $value):; ?>
                        <li><?php echo $integration; ?></li>
                    <?php endforeach; endif; ?>
                </ul>
                <a href="?page=cables_backend_style&style=<?php echo $id; ?>" class="button-primary"><?php echo i18n("page_dashboard_currentpatches_change_website_integration"); ?></a>
            </div>
        </div>
        <div style="clear:both;"></div>
    <?php endforeach; ?>
</div>
