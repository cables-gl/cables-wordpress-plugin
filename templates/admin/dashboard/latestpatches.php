<div class="wrap">
    <h3><span><?php echo i18n("page_dashboard_latestpatches_headline"); ?></span></h3>
    <div id="col-container">
        <p><?php echo i18n("page_dashboard_latestpatches_text"); ?></p>
        <?php foreach($context['notIntegratedStyles'] as $id => $notIntegratedStyle): ?>
            <div class="ps-screenshot-container">
                <img alt="<?php echo  $notIntegratedStyle->name; ?>" src="<?php echo cables_patch_screenshot_url($id) ?>"/>
            </div>
            <div>
                <?php echo i18n("page_dashboard_currentpatches_style"); ?> <a href="c"><?php echo $notIntegratedStyle->name; ?></a><br/>
                <?php echo i18n("page_dashboard_currentpatches_style_created"); ?> <?php echo $notIntegratedStyle->createdAt; ?><br/>
                <?php echo i18n("page_dashboard_currentpatches_original_patch"); ?> <?php echo $notIntegratedStyle->shapeName; ?>
            </div>
        <?php endforeach; ?>
        <br/>
        <button class="button-primary"><?php echo i18n("page_dashboard_latestpatches_button"); ?></button>
    </div>
    <div style="clear:both;"></div>
</div>
