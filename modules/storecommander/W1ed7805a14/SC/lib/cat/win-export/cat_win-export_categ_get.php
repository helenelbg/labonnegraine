<?php

    $id_lang = (int) Tools::getValue('id_lang');

    function getLevelFromDB($parent_id)
    {
        global $id_lang;
        $sql = 'SELECT c.active,c.id_category,name FROM '._DB_PREFIX_.'category c
                        LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $id_lang.(SCMS && SCI::getSelectedShop() > 0 ? ' AND cl.id_shop='.(int) SCI::getSelectedShop() : '').")
                        WHERE c.id_parent=$parent_id
                        ORDER BY cl.name";
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $k => $row)
        {
            $style = '';
            if ($row['name'] == '')
            {
                $sql2 = 'SELECT c.active,c.id_category,name FROM '._DB_PREFIX_.'category c
                                LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=2)
                                WHERE c.id_category='.$row['id_category'];
                $res2 = Db::getInstance()->getRow($sql2);
                $style = 'style="background:lightblue" ';
            }
            $icon = ($row['active'] ? 'catalog.png' : 'catalog_edit.png');
            if (!sc_in_array($row['name'], array('SC Recycle Bin', 'SC Corbeille'), 'catWinExportCategGet_corbeille'))
            {
                echo '<row '.($style != '' ? $style : '').
                                    ' id="'.$row['id_category'].'"'.($parent_id == 0 ? ' open="1"' : '').'>'.
                                    '<cell>'.$row['id_category'].'</cell>'.
                                    '<cell>0</cell>'.
                                    '<cell image="'.$icon.'"><![CDATA['.($style == '' ? $row['name'] : _l('To Translate:').' '.$res2['name']).']]></cell>';
                getLevelFromDB($row['id_category']);
                echo '</row>'."\n";
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

?>
<rows parent="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter,,#text_filter]]></param></call>
</beforeInit>
<column id="id_category" width="40" type="ro" align="right" sort="na"><?php echo _l('ID'); ?></column>
<column id="used" width="50" type="ch" align="center" sort="na"><?php echo _l('Used'); ?></column>
<column id="name" width="250" type="tree" align="left" sort="na"><?php echo _l('Name'); ?></column>
</head>
<?php
    echo '<row '.
                    ' id="1">'.
                    '<cell>1</cell>'.
                    '<cell>0</cell>'.
                    '<cell image="catalog.png"><![CDATA['._l('Home').']]></cell>'.
                    '</row>';
    getLevelFromDB(1);
?>
</rows>
