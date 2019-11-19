<div class="wrap">
    <h2><?php echo i18n("page_integration_mainheadline"); ?></h2>
    <h2 class="nav-tab-wrapper">
        <a href="?page=cables_backend_imports&tab=tab1" class="nav-tab<?php if($context['active_tab'] == 'tab1'): ?> nav-tab-active<?php endif ?>"><?php echo i18n("page_integration_tab1_tabtitle"); ?></a>
        <a href="?page=cables_backend_imports&tab=tab2&style=<?php echo $context['style']->id; ?>" class="nav-tab<?php if($context['active_tab'] == 'tab2'): ?> nav-tab-active<?php endif; ?>"><?php echo i18n("page_integration_tab2_tabtitle"); ?></a>
        <a href="?page=cables_backend_imports&tab=tab3&style=<?php echo $context['style']->id; ?>" class="nav-tab<?php if($context['active_tab'] == 'tab3'): ?> nav-tab-active<?php endif; ?>"><?php echo i18n("page_integration_tab3_tabtitle"); ?></a>
    </h2>
    <?php include($context['active_tab'] . '.php'); ?>
</div>
