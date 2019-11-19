<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-1">
        <div id="post-body-content">
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                    <div class="inside">
                        <form method="POST" action="<?php echo $context['action_url']; ?>">

                            <input type="hidden" name="action" value="cables_set_patch_options"/>
                            <input type="hidden" name="patch" value="<?php echo $context['patch']->id; ?>"/>
                            <input type="hidden" name="redirect" value="admin.php?page=cables_backend_imports&tab=tab3"/>

                            <?php include(__DIR__ . '/../_partials/integration_formfields.php'); ?>
                            <input name="mobile" id="mobile" type="checkbox"<?php if($context['patchConfig']->mobile): ?> checked="checked"<?php endif; ?>>
                            <label for="mobile"><?php echo i18n("page_patch_block2_text"); ?></label><br/><br/>

                            <span><a href="?page=cables_backend_imports&tab=tab1" class="button-primary" name="Login"><?php echo i18n("page_integration_tab2_button_back"); ?></a></span>
                            <span><input name="Submit" type="submit" class="button-primary" value="<?php echo i18n("page_integration_tab2_button"); ?>"/></span>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br class="clear">
</div>