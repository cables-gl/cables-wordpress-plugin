<div class="wrap">
    <h1><?php echo i18n("page_dashboard_mainheadline"); ?></h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
                            <?php include('currentpatches.php'); ?>
                            <hr/>
                            <?php include('latestpatches.php'); ?>
                        </div>
                    </div>

                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <div class="meta-box-sortables">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo i18n("page_dashboard_loginbox_headline"); ?></span></h2>
                        <div class="inside">
                            <p><?php echo $context['account.email'] ?></p>
                        </div>
                    </div>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo i18n("page_dashboard_sidebar1_headline"); ?></span></h2>
                        <div class="inside">
                            <img src="<?php echo i18n("page_dashboard_sidebar1_image"); ?>"/>
                            <p><?php echo i18n("page_dashboard_sidebar1_text"); ?></p>
                        </div>
                    </div>
                    <div class="postbox">
                        <div class="inside">
                            <img src="<?php echo i18n("page_dashboard_sidebar2_image"); ?>"/>
                            <p><?php echo i18n("page_dashboard_sidebar2_text"); ?></p>
                            <button class="button-primary" name="Login"><?php echo i18n("page_dashboard_sidebar2_button"); ?></button>
                        </div>
                    </div>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo i18n("page_dashboard_sidebar3_headline"); ?></span></h2>
                        <div class="inside">
                            <img src="<?php echo i18n("page_dashboard_sidebar3_image"); ?>"/>
                            <p><?php echo i18n("page_dashboard_sidebar3_text"); ?></p>
                            <button class="button-primary" name="Login"><?php echo i18n("page_dashboard_sidebar2_button"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>