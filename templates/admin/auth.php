<div class="wrap">
    <h2><?php echo i18n("page_auth_mainheadline"); ?></h2>
    <div id="poststuff">
        <div class="metabox-holder columns-1">
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox">
                    <h2 class="hndle"><span><?php echo i18n("page_auth_block1_headline"); ?></span>
                    </h2>
                    <div class="inside">
                        <p>
                            <?php echo i18n("page_auth_block1_text"); ?>
                            <a class="button-primary" name="Login" href="echo $context['cables_url']/settings"><?php echo i18n("page_auth_block1_button"); ?></a>
                        </p>
                        <form action="<?php echo $context['action_url']; ?>" method="POST">
                            <input type="hidden" name="action" value="cables_login">

                            <br/><br/>
                            <label for="cables_login_apikeyy"><?php echo i18n("page_auth_block1_apikey_label"); ?></label>
                            <input required type="text" id="cables_login_apikey" name="apikey" value=""/>
                            <br/><br/>
                            <input class="button-primary" type="submit" name="submit"
                                   value="<?php echo i18n("page_auth_block1_submit_label"); ?>"/>

                        </form>
                    </div>
                </div>
                <div class="postbox">
                    <h2 class="hndle"><span><?php echo i18n("page_auth_block2_headline"); ?></span>
                    </h2>
                    <div class="inside">
                        <p>
                            <img src="<?php echo i18n("page_auth_block2_image1"); ?>"><br/>
                            <?php echo i18n("page_auth_block2_imagetext1"); ?>
                            <br/>
                            <img src="<?php echo i18n("page_auth_block2_image2"); ?>"><br/>
                            <?php echo i18n("page_auth_block2_imagetext2"); ?>
                            <button class="button-primary" name="Login"><?php echo i18n("page_auth_block2_button"); ?></button>

                        </p>
                    </div>
                </div>
                <div class="postbox">
                    <h2 class="hndle"><span><?php echo i18n("page_auth_block3_headline"); ?></span>
                    </h2>
                    <div class="inside">
                        <p>
                            <?php echo i18n("page_auth_block3_text"); ?>
                            <br/>
                            <button class="button-primary" name="Login"><?php echo i18n("page_auth_block3_button"); ?></button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>


