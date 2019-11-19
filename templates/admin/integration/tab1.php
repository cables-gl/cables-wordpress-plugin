<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-1">
        <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                    <h2><span><?php echo i18n("page_integration_tab1_headline"); ?></span></h2>
                    <div class="inside">
                        <div id="col-container">
                            <?php foreach ($context['patches'] as $patch): ?>
                                <a href="?page=cables_backend_imports&tab=tab2&style=<?php echo $patch->id; ?>">
                                    <div class="ps-col-left">
                                        <div class="ps-screenshot-container">
                                            <img alt="<?php echo $patch->name; ?>" src="<?php echo cables_patch_screenshot_url($patch->id) ?>"/>
                                        </div>
                                    </div>
                                    <div class="ps-col-right">
                                        <div class="text">
                                            <span class="title"><?php echo $patch->name; ?></span>
                                        </div>
                                    </div>
                                </a>
                                <div style="clear: both"></div>
                                <hr/>
                            <?php endforeach; ?>
                            <button class="button-primary" name="Login"><?php echo i18n("page_integration_tab1_button"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br class="clear">
</div>