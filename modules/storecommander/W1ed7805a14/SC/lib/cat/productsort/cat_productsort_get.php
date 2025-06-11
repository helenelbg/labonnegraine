<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_category = (int) Tools::getValue('id_category', 0);

$sourceGridFormat = SCI::getGridViews('productsort');
$sql_gridFormat = $sourceGridFormat;
sc_ext::readCustomProductsortGridConfigXML('gridConfig');
$gridFormat = $sourceGridFormat;
$cols = explode(',', $gridFormat);
$all_cols = explode(',', $gridFormat);

$colSettings = array();
$colSettings = SCI::getGridFields('productsort');
sc_ext::readCustomProductsortGridConfigXML('colSettings');

$xml = '';
if (!empty($id_category))
{
    $defaultimg = 'lib/img/i.gif';
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if (file_exists(SC_PS_PATH_DIR.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'_default.jpg'))
        {
            $defaultimg = SC_PS_PATH_REL.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'_default.jpg';
        }
    }
    else
    {
        if (file_exists(SC_PS_PATH_DIR.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'.jpg'))
        {
            $defaultimg = SC_PS_PATH_REL.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'.jpg';
        }
    }

    $sqlJoinImage = ' LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product= p.id_product AND i.cover=1)';
    $sqlSelectImage = ' ,(SELECT i.id_image FROM '._DB_PREFIX_.'image i WHERE i.id_product= p.id_product AND i.cover=1 LIMIT 1) AS id_image';

    if (SCI::getSelectedShop() > 0)
    {
        if (version_compare(_PS_VERSION_, '1.6.1.0', '>='))
        {
            $sqlJoinImage = ' LEFT JOIN '._DB_PREFIX_.'image_shop i ON (i.id_product= p.id_product AND i.id_shop = '.(int) SCI::getSelectedShop().' AND i.cover=1)';
            $sqlSelectImage = ' ,(SELECT i.id_image FROM '._DB_PREFIX_.'image_shop i WHERE i.id_product= p.id_product AND i.id_shop = '.(int) SCI::getSelectedShop().' AND i.cover=1 LIMIT 1) AS id_image';
        }
        elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $sqlJoinImage = ' LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product= p.id_product) 
                                LEFT JOIN '._DB_PREFIX_.'image_shop imgs ON (imgs.id_image = i.id_image  AND imgs.id_shop = '.(int) SCI::getSelectedShop().' AND imgs.cover=1)';
            $sqlSelectImage = ' ,(SELECT i.id_image FROM '._DB_PREFIX_.'image i INNER JOIN '._DB_PREFIX_.'image_shop imgs ON (imgs.id_image = i.id_image  AND imgs.id_shop = '.(int) SCI::getSelectedShop().' AND imgs.cover=1) WHERE i.id_product= p.id_product LIMIT 1) AS id_image';
        }
    }

    $sqlSelect = 'SELECT p.*, pl.* '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' , prs.* ' : '').', cp.position';
    sc_ext::readCustomProductsortGridConfigXML('SQLSelectDataSelect');
    $sqlSelect = $sqlSelect.' '.$sqlSelectImage;

    $sqlFrom = ' FROM '._DB_PREFIX_.'product p';
    $sqlJoin = 'INNER JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product= p.id_product AND cp.id_category='.(int) $id_category.') '.
                (SCMS ? ' INNER JOIN '._DB_PREFIX_.'product_shop prs ON (prs.id_product=p.id_product AND prs.id_shop = ('.(SCI::getSelectedShop() > 0 ? (int) SCI::getSelectedShop() : 'p.id_shop_default').')) ' : '').
                ((!SCMS && version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? ' INNER JOIN '._DB_PREFIX_.'product_shop prs ON (prs.id_product=p.id_product AND prs.id_shop = p.id_shop_default) ' : '').'
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product= p.id_product AND pl.id_lang='.(int) $id_lang.' '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop=prs.id_shop ' : '').')';
    $sqlJoin = $sqlJoin.' '.$sqlJoinImage;

    sc_ext::readCustomProductsortGridConfigXML('SQLSelectDataLeftJoin');
    $sqlGroup = 'GROUP BY p.id_product ORDER BY cp.position ASC';
    $sql = $sqlSelect.' '.$sqlFrom.' '.$sqlJoin.' '.$sqlGroup;

    $res = Db::getInstance()->ExecuteS($sql);

    $pos = 0;
    foreach ($res as $row)
    {
        $xml .= "<row id='".$row['id_product']."'>";
        foreach ($cols as $id => $col)
        {
            if ($col == 'position')
            {
                $xml .= '<cell>'.$pos.'</cell>';
            }
            elseif ($col == 'image')
            {
                if ($row['id_image'] == '')
                {
                    $xml .= '<cell><![CDATA[<i class="fad fa-file-image" ></i>--]]></cell>';
                }
                else
                {
                    $xml .= "<cell><![CDATA[<img src='".SC_PS_PATH_REL.'img/p/'.getImgPath((int) $row['id_product'], (int) $row['id_image'], _s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>]]></cell>";
                }
            }
            else
            {
                sc_ext::readCustomProductsortGridConfigXML('rowData');
                $xml .= '<cell><![CDATA['.$row[$col].']]></cell>';
            }
        }
        $xml .= "</row>\n";
        ++$pos;
    }
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
    ?>
<rows id="0">
<head>
<?php foreach ($cols as $id => $col)
    {
        echo '<column id="'.$col.'"'.(is_array($colSettings[$col]) && sc_array_key_exists('format', $colSettings[$col]) ? ' format="'.$colSettings[$col]['format'].'"' : '').' width="'.$colSettings[$col]['width'].'" align="'.$colSettings[$col]['align'].'" type="'.$colSettings[$col]['type'].'" sort="'.$colSettings[$col]['sort'].'" color="'.$colSettings[$col]['color'].'"><![CDATA['.$colSettings[$col]['text'].']]>';
        if (is_array($colSettings[$col]) && sc_array_key_exists('options', $colSettings[$col]) && is_array($colSettings[$col]['options']) && !empty($colSettings[$col]['options']))
        {
            foreach ($colSettings[$col]['options'] as $k => $v)
            {
                echo '<option value="'.str_replace('"', '\'', $k).'"><![CDATA['.$v.']]></option>';
            }
        }
        echo '</column>'."\n";
    }
?>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_productsort').'</userdata>'."\n";
    sc_ext::readCustomProductsortGridConfigXML('gridUserData');
    echo $xml;
?>
</rows>