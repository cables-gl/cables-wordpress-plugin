<div class="wrap">
    <div class="icon32"></div>
    <h2><?php echo i18n("page_settings_mainheadline"); ?></h2>
    <h3><?php echo i18n("page_settings_headline"); ?> "<?php echo $context['account']->email; ?>"</h3>
    <div id="cables_auth">
        <div class="metabox-holder columns-1">
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                    <h2 class="hndle"><span><?php echo i18n("page_settings_apikey_headline"); ?></span>
                    </h2>
                    <div class="inside">
                        <p>
                            <?php echo i18n("page_settings_apikey_login"); ?> <?php echo $context['account']->email; ?><br/>
                            <?php echo i18n("page_settings_apikey_key"); ?> <?php echo $context['apikey']; ?>
                        </p>
                    </div>
                </div>
                <div class="postbox">
                    <h2 class="hndle"><span><?php echo i18n("page_settings_block1_headline"); ?></span>
                    </h2>
                    <div class="inside">
                        <p>
                            <?php echo i18n("page_settings_block1_text"); ?>
                        </p>
                    </div>
                </div>
                <div class="postbox">
                    <h2 class="hndle"><span><?php echo i18n("page_settings_block2_headline"); ?></span>
                    </h2>
                    <div class="inside">
                        <?php echo i18n("page_settings_block2_text"); ?><br/><br/>
                        <form action="<?php echo  $context['action_url']; ?>" method="POST">
                            <input type="hidden" name="action" value="cables_logout">
                            <input class="button-primary" type="submit" name="submit"
                                   value="<?php echo i18n("page_settings_block2_button"); ?>"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
