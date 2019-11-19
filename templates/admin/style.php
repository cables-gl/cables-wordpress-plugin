<div class="wrap">
    <div class="icon32"></div>
    <h2><?php echo i18n("page_style_mainheadline"); ?></h2>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="POST" action="<?php echo $context['action_url']; ?>">
                        <div class="postbox">
                            <h2 class="handle"><span><?php echo i18n("page_style_block1_headline"); ?></span>
                            </h2>
                            <div class="inside">

                                <input type="hidden" name="action" value="cables_set_style_options"/>
                                <input type="hidden" name="style" value="<?php echo $context['style']->id; ?>"/>

                                <?php include(__DIR__ . '/_partials/integration_formfields.php'); ?>

                            </div>
                        </div>
                        <div class="postbox">
                            <h2 class="handle"><span><?php echo i18n("page_style_block2_headline"); ?></span>
                            </h2>
                            <div class="inside">

                                <p>
                                    <input name="mobile" id="mobile"
                                           type="checkbox"<?php if($context['styleConfig']['mobile']): ?> checked="checked"<?php endif; ?>>
                                    <label for="element_replacement"><?php echo i18n("page_style_block2_text"); ?></label>
                                </p>
                                <p class="submit">
                                    <input name="Submit" type="submit" class="button-primary" value="Save Changes">
                                </p>
                            </div>
                        </div>
                    </form>
                    <div class="postbox">
                        <h2 class="handle"><span><?php echo i18n("page_style_block3_headline"); ?></span>
                        </h2>
                        <div class="inside">
                            <p>
                            <div class="shortcode">
                                <?php echo i18n("page_style_block3_text"); ?>
                                <pre>[cables_patch id='<?php echo $context['style']->id; ?>']</pre>
                            </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h2 class="handle"><span><?php echo $context['style.title']; ?></span></h2>
                        <div class="inside">
                            <img src="<?php echo cables_patch_screenshot_url($context['style']->id); ?>" style="width:100%;"/>
                            <p><?php echo i18n("page_style_sidebar1_text"); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





