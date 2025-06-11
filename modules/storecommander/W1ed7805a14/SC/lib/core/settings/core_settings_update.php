<?php

    $id_setting = Tools::getValue('gr_id');
    $value = Tools::getValue('value');

    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        $action = 'update';
        if (sc_array_key_exists($id_setting, $local_settings))
        {
            if (in_array($id_setting, array('CAT_PROD_GRID_DEFAULT', 'CAT_PRODPROP_GRID_DEFAULT', 'CMS_PAGEPROP_GRID_DEFAULT', 'CMS_PAGE_GRID_DEFAULT', 'MAN_MANUF_PROP_GRID_DEFAULT', 'MAN_MANUF_GRID_DEFAULT', 'ORD_ORDER_GRID_DEFAULT', 'ORD_ORDPROP_GRID_DEFAULT', 'CUS_CUSTOMER_GRID_DEFAULT', 'CUS_CUSPROP_GRID_DEFAULT')))
            {
                $employee_settings = UISettings::load_ini_file();
                $employee_settings[$id_setting] = $value;
                UISettings::write_ini_file($employee_settings, false);
            }
            else
            {
                switch ($id_setting){
                    case 'CAT_PROD_IMG_UPLOAD_MAX_FILESIZE':
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            SCI::updateConfigurationValue('PS_LIMIT_UPLOAD_IMAGE_VALUE', $value);
                        }
                        else
                        {
                            SCI::updateConfigurationValue('PS_PRODUCT_PICTURE_MAX_SIZE', ($value * 1024 * 1024));
                        }
                        break;
                }
                $local_settings[$id_setting]['value'] = $value;
                saveSettings();
            }
            if (sc_array_key_exists('needRefresh', $default_settings[$id_setting]))
            {
                if ($default_settings[$id_setting]['needRefresh'])
                {
                    $action = 'updateAndRefresh';
                }
            }
        }
        $newId = $id_setting;
    }

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo '<data>';
    echo "<action type='".$action."' sid='".Tools::getValue('gr_id')."' tid='".$newId."'/>";

    echo '</data>';
