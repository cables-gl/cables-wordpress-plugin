<?php if($context['notIntegratedPatches']): ?>
<div class="wrap">
    <h3><span><?php echo i18n("page_dashboard_latestpatches_headline"); ?></span></h3>
    <div id="col-container">
        <p><?php echo i18n("page_dashboard_latestpatches_text"); ?></p>
        <?php if($context['notIntegratedPatches']): foreach($context['notIntegratedPatches'] as $id => $notIntegratedPatch): ?>
            <div class="ps-screenshot-container">
                <img alt="<?php echo  $notIntegratedPatch->name; ?>" src="<?php echo cables_patch_screenshot_url($id) ?>"/>
            </div>
            <div>
                <?php echo i18n("page_dashboard_currentpatches_patch"); ?> <a href="c"><?php echo $notIntegratedPatch->name; ?></a><br/>
            </div>
        <?php endforeach; endif; ?>
        <br/>
        <button class="button-primary"><?php echo i18n("page_dashboard_latestpatches_button"); ?></button>
    </div>
    <div style="clear:both;"></div>
</div>
<?php endif; ?>