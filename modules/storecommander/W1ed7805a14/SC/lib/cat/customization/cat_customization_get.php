<?php

    $id_lang = Tools::getValue('id_lang');
    $product_list = Tools::getValue('product_list');
    $id_customization_field = Tools::getValue('id_customization_field');
    $cols = '';
    $filters = '';
    foreach ($languages as $lang)
    {
        $cols .= '<column id="name¤'.$lang['iso_code'].'" width="150" type="edtxt" align="left" sort="str">'._l('Name').' '.strtoupper($lang['iso_code']).'</column>';
        $filters .= '#text_filter,';
    }

    function getCustomizationFields()
    {
        global $product_list,$languages;
        $sql = 'SELECT cf.*, cfl.name, cfl.id_lang
                FROM '._DB_PREFIX_.'customization_field cf
                LEFT JOIN '._DB_PREFIX_.'customization_field_lang cfl ON (cf.id_customization_field=cfl.id_customization_field '.(version_compare(_PS_VERSION_, '1.6.0.12', '>=') ? " AND cfl.id_shop = '".(int) SCI::getSelectedShop()."' " : '').')
                WHERE cf.id_product IN ('.pInSQL($product_list).')
                '.(version_compare(_PS_VERSION_, '1.7.3.0', '>=') ? " AND cf.is_deleted='0' " : '').'
                ORDER BY cfl.name';
        $res = Db::getInstance()->ExecuteS($sql);
        $fields = array();
        if (!empty($res))
        {
            foreach ($res as $row)
            {
                $fields[$row['id_customization_field']][$row['id_lang']]['name'] = $row['name'];
                $fields[$row['id_customization_field']]['type'] = $row['type'];
                $fields[$row['id_customization_field']]['required'] = $row['required'];
                $fields[$row['id_customization_field']]['id_product'] = $row['id_product'];
            }
        }
        if (!empty($fields))
        {
            foreach ($fields as $k => $val)
            {
                echo '<row id="'.$k.'">';
                echo '<cell>'.$k.'</cell>';
                echo '<cell>'.(int) $val['id_product'].'</cell>';
                echo '<cell>'.$val['type'].'</cell>';
                echo '<cell>'.$val['required'].'</cell>';
                foreach ($languages as $lang)
                {
                    echo '<cell><![CDATA['.(sc_array_key_exists($lang['id_lang'], $fields[$k]) ? $fields[$k][$lang['id_lang']]['name'] : '').']]></cell>';
                }
                echo '</row>';
            }
        }
    }

    //XML HEADER
    //include XML Header (as response will be in xml format)

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo '<rows parent="0">';
    echo '<head>';
    echo '<beforeInit>';
    echo '<call command="attachHeader"><param><![CDATA[#numeric_filter,#numeric_filter,#select_filter,#select_filter,'.$filters.']]></param></call>';
    echo '</beforeInit>';
    echo '<column id="id_customization_field" width="50" type="ro" align="right" sort="int">'._l('ID').'</column>';
    echo '<column id="id_product" width="50" type="ro" align="right" sort="int">'._l('id_product').'</column>';
    echo '<column id="type" width="50" type="coro" align="center" sort="str">'._l('Type').'<option value="0">'._l('File').'</option><option value="1">'._l('Text').'</option></column>';
    echo '<column id="required" width="50" type="coro" align="center" sort="str">'._l('Required').'<option value="0">'._l('No').'</option><option value="1">'._l('Yes').'</option></column>';
    echo $cols;
    echo '</head>';
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_customization').'</userdata>'."\n";
    getCustomizationFields();

    echo '</rows>';
