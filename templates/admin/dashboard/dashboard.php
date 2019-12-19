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
                        <h2 class="hndle"><span><?php echo i18n("page_dashboard_sidebar1_headline"); ?></span></h2>
                        <div class="inside">
                            <a href="https://www.youtube.com/channel/UC7IRYQBFbt1KX4YmhBuIbhA" target="_blank">
                                <img style="width: 100%;" src="<?php echo i18n("page_dashboard_sidebar1_image"); ?>"/>
                            </a>
                            <p><?php echo i18n("page_dashboard_sidebar1_text"); ?></p>
                            <p><?php echo i18n("page_dashboard_sidebar2_text"); ?></p>
                            <button class="button-primary" name="Login"><?php echo i18n("page_dashboard_sidebar2_button"); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>