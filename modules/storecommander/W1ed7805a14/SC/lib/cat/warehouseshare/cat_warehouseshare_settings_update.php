<?php

$value = Tools::getValue('setting_value', '0');

$local_settings['CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE']['value'] = $value;

saveSettings();
