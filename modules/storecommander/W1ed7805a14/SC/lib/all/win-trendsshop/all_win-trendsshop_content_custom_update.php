<?php

$filter_view_name = Tools::getValue('filter_view_name', '');
$filter_view_encoded = Tools::getValue('filter_view_encoded', '');
$action = Tools::getValue('action', '');

switch ($action) {
    case 'add':
        if (!empty($filter_view_name) && !empty($filter_view_encoded))
        {
            $data = array(
                'name' => $filter_view_name,
                'value' => $filter_view_encoded,
            );
            if ($return = CustomSettings::addCustomSetting('all', 'ts_content_filters', $data))
            {
                exit($filter_view_name);
            }
        }
        break;
    case 'delete':
        $filter_used = Tools::getValue('filter_used', '');
        if (!empty($filter_used))
        {
            $return = CustomSettings::deleteCustomSetting('all', 'ts_content_filters', $filter_used);
            if (empty($return))
            {
                exit($filter_used);
            }
        }
        break;
}
exit('KO');
