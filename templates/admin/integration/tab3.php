<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-1">
        <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                    <h2><span><?php echo i18n("page_integration_tab3_headline"); ?></span></h2>
                    <div id="col-container">
                        <div class="ps-col-left">
                            <div class="ps-screenshot-container">
                                <img alt="<?php echo $context['style']->name; ?>" src="<?php echo cables_patch_screenshot_url($context['style']->id) ?>"/>
                            </div>
                            <div>
                                <?php echo i18n("page_dashboard_currentpatches_style"); ?> <a href="c"><?php echo $context['style']->name; ?></a><br/>
                                <?php echo i18n("page_dashboard_currentpatches_style_created"); ?> <?php echo $context['style']->createdAt; ?><br/>
                                <?php echo i18n("page_dashboard_currentpatches_original_patch"); ?> <?php echo $context['style']->shapeName; ?>
                            </div>
                        </div>
                        <div class="ps-col-right">
                            <?php echo i18n("page_dashboard_currentpatches_current_pages_integration"); ?>
                            <ul>
                                <?php if($context['styleConfig']['page_types']): foreach($context['styleConfig']['page_types'] as $pagetype => $value): ?>
                                    <li><?php echo $pagetype; ?></li>
                                <?php endforeach; endif;?>
                            </ul>
                            <?php echo i18n("page_dashboard_currentpatches_current_website_integration"); ?>
                            <ul>
                                <?php if($context['styleConfig']['integrations']): foreach($context['styleConfig']['integrations'] as $integration => $value): ?>
                                    <li><?php echo $integration; ?></li>
                                <?php endforeach; endif; ?>
                            </ul>
                            <br/>
                            <span><a href="?page=cables_backend_style&style=<?php echo $context['style']->id; ?>" class="button-primary" name="Login"><?php echo i18n("page_integration_tab3_preview"); ?></a></span>
                        </div>
                    </div>
                    <div style="clear:both;"><br/><br/></div>
                    <span><a href="?page=cables_backend_imports&tab=tab2&style=<?php echo $context['style']->id; ?>" class="button-primary" name="Login"><?php echo i18n("page_integration_tab3_button_back"); ?></a></span>
                    <span><a href="?page=cables_backend_style&style=<?php echo $context['style']->id; ?>" class="button-primary" name="Login"><?php echo i18n("page_integration_tab3_button"); ?></a></span>
                </div>
            </div>
        </div>
    </div>
    <br class="clear">
</div>