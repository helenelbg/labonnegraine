<?php

    function getSettings()
    {
        global $default_settings,$local_settings;
        $default_settings_temp = $default_settings;

        if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
        {
            // PS ADD A CONFIGURATION FOR THIS ACTION
            // SO CUSTOMER PARAMS IT IN PS BACKOFFICE
            unset($default_settings_temp['CAT_SEO_NAME_TO_URL']);
        }
        if (!_r('MEN_TOO_CUSTOM_LINKS'))
        {
            unset($default_settings_temp['TOOLS_LINK_1']);
            unset($default_settings_temp['TOOLS_LINK_2']);
            unset($default_settings_temp['TOOLS_LINK_3']);
            unset($default_settings_temp['TOOLS_LINK_4']);
            unset($default_settings_temp['TOOLS_LINK_5']);
        }
        $tiny = _s('CAT_PROD_IMG_TINYPNG');
        if (empty($tiny))
        {
            unset($default_settings_temp['CAT_PROD_IMG_TINYPNG']);
        }

        foreach ($default_settings_temp as $k => $v)
        {
            if ($v['id'] != 'CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE')
            {
                $val = $local_settings[$k]['value'];
                if (in_array($k, array('CAT_PROD_GRID_DEFAULT', 'CAT_PRODPROP_GRID_DEFAULT', 'CMS_PAGEPROP_GRID_DEFAULT', 'CMS_PAGE_GRID_DEFAULT', 'MAN_MANUF_PROP_GRID_DEFAULT', 'MAN_MANUF_GRID_DEFAULT', 'ORD_ORDER_GRID_DEFAULT', 'ORD_ORDPROP_GRID_DEFAULT', 'CUS_CUSTOMER_GRID_DEFAULT', 'CUS_CUSPROP_GRID_DEFAULT')))
                {
                    $uiset = UISettings::getSetting($k);
                    if (!empty($uiset))
                    {
                        $val = $uiset;
                    }
                }

                echo '<row id="'.$v['id'].'">';
                echo '<cell>'._l($v['section1']).'</cell>';
                echo '<cell>'._l($v['section2']).'</cell>';
                echo '<cell><![CDATA['._l($v['name']).']]></cell>';
                echo '<cell><![CDATA['.$val.']]></cell>';
                echo '<cell><![CDATA['._l($v['description']).']]></cell>';
                echo "<cell><![CDATA[<span style='color:#888888'>".$v['default_value'].'</span>]]></cell>';
                echo '<cell>'.$v['id'].'</cell>';
                echo '</row>';
            }
        }
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
    echo '<rows>';
    getSettings();
    echo '</rows>';
