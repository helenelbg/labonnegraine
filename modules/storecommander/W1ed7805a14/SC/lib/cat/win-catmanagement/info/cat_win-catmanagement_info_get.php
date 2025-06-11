<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $idlist = (Tools::getValue('idlist', 0));

    function getRowsFromDB()
    {
        global $id_lang,$idlist;
        $xml = '';

        if (!empty($idlist))
        {
            $array_langs = array();
            $langs = Language::getLanguages(false);
            foreach ($langs as $lang)
            {
                $array_langs[$lang['id_lang']] = strtoupper($lang['iso_code']);
            }

            $array_shops = array();
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $shops = Shop::getShops(false);
                foreach ($shops as $shop)
                {
                    $shop['name'] = str_replace('&', _l('and'), $shop['name']);
                    $array_shops[$shop['id_shop']] = $shop['name'];
                }
            }

            $sql = '
            SELECT cl.*
            FROM '._DB_PREFIX_.'category_lang cl
                '.((!_s('CAT_PROD_LANGUAGE_ALL')) ? ' INNER JOIN '._DB_PREFIX_.'lang l ON (cl.id_lang = l.id_lang AND l.active = 1)' : '').'
            WHERE cl.id_category IN ('.pInSQL($idlist).')
                AND cl.name != "SC Recycle Bin"
            ORDER BY cl.id_category, cl.id_lang';
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql = 'SELECT cl.*
                    FROM '._DB_PREFIX_.'category_lang cl
                        '.((!_s('CAT_PROD_LANGUAGE_ALL')) ? ' INNER JOIN '._DB_PREFIX_.'lang l ON (cl.id_lang = l.id_lang AND l.active = 1)' : '').'
                    WHERE cl.id_category IN ('.pInSQL($idlist).')
                        AND cl.name != "SC Recycle Bin"
                    ORDER BY cl.id_category, cl.id_lang';
                if (SCMS)
                {
                    $sql .= ',id_shop';
                }
            }
            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row)
            {
                $description = strip_tags($row['description']);
                $additional_description = strip_tags($row['additional_description']);

                $xml .= "<row id='".$row['id_category'].'_'.$row['id_lang'].(SCMS ? '_'.$row['id_shop'] : '')."'>";
                $xml .= ' <cell>'.$row['id_category'].'</cell>';
                if (SCMS)
                {
                    $xml .= ' <cell>'.$array_shops[$row['id_shop']].'</cell>';
                }
                $xml .= ' <cell>'.$array_langs[$row['id_lang']].'</cell>';
                $xml .= ' <cell><![CDATA['.$row['name'].']]></cell>';
                $xml .= ' <cell><![CDATA['.$description.']]></cell>';
                $xml .= ' <cell><![CDATA['.strlen($description).']]></cell>';
                if (version_compare(_PS_VERSION_, '8.0.0', '>='))
                {
                    $xml .= ' <cell><![CDATA['.$additional_description.']]></cell>';
                    $xml .= ' <cell><![CDATA['.strlen($additional_description).']]></cell>';
                }
                $xml .= '</row>';
            }
        }

        return $xml;
    }

    //XML HEADER
    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

    $xml = '';
    if (!empty($idlist))
    {
        $xml = getRowsFromDB();
    }
?>
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter<?php if (SCMS){ ?>,#select_filter<?php } ?>,#select_filter,#text_filter,#text_filter,#numeric_filter<?php if (version_compare(_PS_VERSION_, '8.0.0', '>=')) { ?>,#text_filter,#numeric_filter<?php }?>]]></param></call>
</beforeInit>
<column id="id_category" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<?php if (SCMS){ ?>
<column id="shop" width="100" type="ro" align="left" sort="str"><?php echo _l('Shop'); ?></column>
<?php } ?>
<column id="lang" width="60" type="ro" align="center" sort="str"><?php echo _l('Lang'); ?></column>
<column id="name" width="120" type="ed" align="left" sort="str"><?php echo _l('Name'); ?></column>
<column id="description" width="200" type="ro" align="left" sort="str"><?php echo _l('Description'); ?></column>
<column id="description_width" width="40" type="ro" align="right" sort="str"><?php echo _l('Description length'); ?></column>
<?php if (version_compare(_PS_VERSION_, '8.0.0', '>=')) { ?>
<column id="additional_description" width="200" type="ro" align="left" sort="str"><?php echo _l('Additional description'); ?></column>
<column id="additional_description_width" width="40" type="ro" align="right" sort="str"><?php echo _l('Additional description length'); ?></column>
<?php } ?>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_prop_info_grid').'</userdata>'."\n";
    echo $xml;
?>
</rows>
