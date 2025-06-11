<?php

$filter_view_name = Tools::getValue('filter_view_name', '');
$filter_view_encoded = Tools::getValue('filter_view_encoded', '');
$period_selection = Tools::getValue('periodselection', '');
$action = Tools::getValue('action', '');

switch ($action) {
    case 'add':
        if (!empty($filter_view_name) && !empty($filter_view_encoded) && !empty($period_selection))
        {
            $data = array(
                'name' => $filter_view_name,
                'value' => $filter_view_encoded,
                'periodselection' => $period_selection,
            );
            if ($return = CustomSettings::addCustomSetting('ord', 'filters', $data))
            {
                exit($filter_view_name);
            }
        }
    break;
    case 'delete':
        $filter_used = Tools::getValue('filter_used', '');
        if (!empty($filter_used))
        {
            if ($return = CustomSettings::deleteCustomSetting('ord', 'filters', $filter_used))
            {
                exit($filter_used);
            }
        }
    break;
}
exit('KO');
