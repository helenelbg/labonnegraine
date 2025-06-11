<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $id_product = (int) Tools::getValue('id_product');
    $selection = Tools::getValue('selection', '');
    $selectionArr = explode(',', $selection);
    foreach ($selectionArr as $k => $sel)
    {
        if (substr($sel, 0, 3) == 'NEW')
        {
            unset($selectionArr[$k]);
        }
    }
    $selection = implode(',', $selectionArr);

    function getRowsFromDB()
    {
        global $id_lang,$id_product,$selection,$selectionArr;

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $sql = '
            SELECT i.*, il.legend
            FROM `'._DB_PREFIX_.'image` i
            INNER JOIN `'._DB_PREFIX_.'product` p ON (i.`id_product`=p.`id_product`)
            '.(SCMS ? 'INNER JOIN `'._DB_PREFIX_.'image_shop` ims ON (ims.id_image=i.id_image AND ims.id_shop='.(SCI::getSelectedShop() > 0 ? (int) SCI::getSelectedShop() : 'p.id_shop_default').')' : '').'
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (il.id_image=i.id_image AND il.id_lang='.$id_lang.')
            WHERE i.`id_product` = '.(int) $id_product.'
            ORDER BY i.position';
        }
        else
        {
            $sql = '
            SELECT i.*, il.legend
            FROM `'._DB_PREFIX_.'image` i
            LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (il.id_image=i.id_image AND il.id_lang='.$id_lang.')
            WHERE i.`id_product` = '.(int) $id_product.'
            ORDER BY i.position';
        }
        $res = Db::getInstance()->ExecuteS($sql);
        $xml = '';
        foreach ($res as $image)
        {
            $sql = '
            SELECT COUNT(*) AS nb
            FROM `'._DB_PREFIX_.'product_attribute_image` pai
            WHERE pai.`id_image` = '.(int) $image['id_image'].
            ($selection != '' ? ' AND pai.id_product_attribute IN ('.pInSQL($selection).')' : '').
            ' GROUP BY pai.`id_image`';
            $used = Db::getInstance()->getRow($sql);
            if (!$used)
            {
                $used = array(
                    'nb' => 0,
                );
            }
            $xml .= "<row id='".$image['id_image']."'>";
            $xml .= '<cell style="background-color:'.($used['nb'] == count($selectionArr) ? '#7777AA' : ($used['nb'] < count($selectionArr) && $used['nb'] > 0 ? '#777777' : '#DDDDDD')).'">'.($used['nb'] == count($selectionArr) ? 1 : 0).'</cell>';
            $xml .= '<cell'.($image['cover'] ? ' style="background-color:#7777AA"' : '')."><![CDATA[<img src='".SC_PS_PATH_REL.'img/p/'.getImgPath((int) $id_product, (int) $image['id_image'], _s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>".(_s('CAT_PROD_IMG_SAVE_FILENAME') && _s('CAT_PROD_IMG_DISPLAY_FILENAME') ? '<br/>'.$image['sc_path'] : '').']]></cell>';
            $xml .= '<cell><![CDATA['.$image['legend'].']]></cell>';
            $xml .= '</row>';
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

    $xml = getRowsFromDB();
?>
<rows id="0">
<head>
<column id="used" width="50" type="ch" align="center" sort="na"><?php echo _l('Used'); ?></column>
<column id="image" width="120" type="ro" align="center" sort="na"><?php echo _l('Image'); ?></column>
<column id="legend" width="*" type="ed" align="left" sort="na"><?php echo _l('Legend'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_combi_image').'</userdata>'."\n";
    echo $xml;
?>
</rows>
