<p>
    <?php echo i18n("page_integration_tab2_location_headline"); ?><br/>
    <input type="checkbox" name="cables_integration[header]" id="cables_integration_header"<?php echo $context['patchConfig']['integrations']['header'] ? ' checked="checked"' : '' ?>/>
    <label for="cables_integration_header"><?php echo i18n("page_integration_tab2_location_header"); ?></label>
    <input type="checkbox" name="cables_integration[hero]" id="cables_integration_hero"<?php echo $context['patchConfig']['integrations']['hero'] ? ' checked="checked"' : '' ?>/>
    <label for="cables_integration_hero"><?php echo i18n("page_integration_tab2_location_hero"); ?></label>
    <input type="checkbox" name="cables_integration[background]" id="cables_integration_background"<?php echo $context['patchConfig']['integrations']['background'] ? ' checked="checked"' : '' ?>/>
    <label for="cables_integration_background"><?php echo i18n("page_integration_tab2_location_background"); ?></label>
    <input type="checkbox" name="cables_integration[footer]" id="cables_integration_footer"<?php echo $context['patchConfig']['integrations']['footer'] ? ' checked="checked"' : '' ?>/>
    <label for="cables_integration_footer"><?php echo i18n("page_integration_tab2_location_footer"); ?></label>
    <input type="checkbox" name="cables_integration[custom]" id="cables_integration_custom"<?php echo $context['patchConfig']['integrations']['custom'] ? ' checked="checked"' : '' ?>/>
    <label for="cables_integration_custom"><?php echo i18n("page_integration_tab2_location_custom_field"); ?></label>
</p>
<p>
    <?php echo i18n("page_integration_tab2_pagetype_headline"); ?><br/>
    <input type="checkbox" name="cables_integration_pagetype[all]" id="cables_integration_pagetype_all"<?php echo $context['patchConfig']['page_types']['all'] ? ' checked="checked"' : '' ?>/>
    <label for="cables_integration_pagetype_all"><?php echo i18n("page_integration_tab2_pagetype_all"); ?></label>

    <input type="checkbox" name="cables_integration_pagetype[home]" id="cables_integration_pagetype_home"<?php echo $context['patchConfig']['page_types']['home'] ? ' checked="checked"' : '' ?>/>
    <label for="cables_integration_pagetype_home"><?php echo i18n("page_integration_tab2_pagetype_home"); ?></label>

    <input type="checkbox" name="cables_integration_pagetype[post]" id="cables_integration_pagetype_post"<?php echo $context['patchConfig']['page_types']['post'] ? ' checked="checked"' : '' ?>/>
    <label for="cables_integration_pagetype_post"><?php echo i18n("page_integration_tab2_pagetype_post"); ?></label>

    <input type="checkbox" name="cables_integration_pagetype[page]" id="cables_integration_pagetype_page"<?php echo $context['patchConfig']['page_types']['page'] ? ' checked="checked"' : '' ?>/>
    <label for="cables_integration_pagetype_page"><?php echo i18n("page_integration_tab2_pagetype_page"); ?></label>

</p>
