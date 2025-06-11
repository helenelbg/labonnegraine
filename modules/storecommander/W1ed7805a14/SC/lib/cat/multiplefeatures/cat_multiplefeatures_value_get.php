<?php

    $feature_valuegroups = array();
    $id_lang = (int) Tools::getValue('id_lang');
    $id_feature = (int) Tools::getValue('id_feature');
    $filter = (int) Tools::getValue('filter', 0);
    $id_category = (int) Tools::getValue('id_category');

    $idlist = Tools::getValue('product_list');
    $used = array();
    $cntProducts = count(explode(',', $idlist));

    $hasPosition = false;
    if (isField('position', 'feature_product'))
    {
        $hasPosition = true;
    }

    if ($filter)
    {
        $sql = '
        SELECT id_product
        FROM `'._DB_PREFIX_.'category_product`
        WHERE id_category='.(int) $id_category;
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $val)
        {
            $productList[] = (int) $val['id_product'];
        }
        $sql = '
        SELECT DISTINCT(id_feature_value)
        FROM `'._DB_PREFIX_.'feature_product`
        WHERE id_product IN ('.join(',', $productList).')';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $val)
        {
            $featuresSelection[] = (int) $val['id_feature_value'];
        }
    }

    $xml = '';

    $sql = '
        SELECT *, "o" as position
        FROM `'._DB_PREFIX_.'feature_value` v
        LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` vl ON (v.`id_feature_value` = vl.`id_feature_value` AND vl.`id_lang` = '.(int) $id_lang.')
        WHERE v.`id_feature` = '.(int) $id_feature.' AND (v.`custom` IS NULL OR v.`custom` = 0)
            '.($filter && count($featuresSelection) ? ' AND v.id_feature_value IN ('.join(',', $featuresSelection).')' : '');
    if (empty($hasPosition))
    {
        $sql .= ' ORDER BY vl.`value` ASC';
    }
    $feature_values = Db::getInstance()->ExecuteS($sql);

    function getValues()
    {
        global $idlist,$used, $cntProducts, $feature_values,$hasPosition;

        $multiple = false;
        if (strpos($idlist, ',') !== false)
        {
            $multiple = true;
        }

        if (!$multiple)
        {
            foreach ($feature_values as $key => $feature_value)
            {
                $used[$feature_value['id_feature_value']] = array(0, ''); //used,color

                $sql2 = '    SELECT id_feature_value '.($hasPosition ? ' ,position ' : '').'
                    FROM '._DB_PREFIX_.'feature_product
                    WHERE id_product IN ('.pInSQL($idlist).")
                        AND id_feature_value = '".(int) $feature_value['id_feature_value']."'";
                $res2 = Db::getInstance()->getRow($sql2);
                if (!empty($res2['id_feature_value']))
                {
                    $used[$feature_value['id_feature_value']][0] = 1;
                    $used[$feature_value['id_feature_value']][1] = '';
                    if ($hasPosition)
                    {
                        $feature_values[$key]['position'] = $res2['position'];
                    }
                }
            }
        }
        else
        {
            if ($hasPosition)
            {
                $first_product = 0;
                $exp = explode(',', $idlist);
                if (!empty($exp[0]))
                {
                    $first_product = $exp[0];
                }
            }
            foreach ($feature_values as $key => $feature_value)
            {
                $used[$feature_value['id_feature_value']] = array(0, 'DDDDDD'); //used,color
                $nb_present = 0;

                if (!empty($first_product) && $hasPosition)
                {
                    $sql2 = 'SELECT position
                                FROM '._DB_PREFIX_.'feature_product
                                WHERE id_product = '.(int) $first_product.'
                                AND id_feature = '.(int) $feature_value['id_feature'].'
                                AND id_feature_value = '.(int) $feature_value['id_feature_value'];
                    $res2 = Db::getInstance()->ExecuteS($sql2);
                    if (!empty($res2) && isset($res2[0]['position']))
                    {
                        $feature_values[$key]['position'] = $res2[0]['position'];
                    }
                }

                $sql2 = 'SELECT id_feature_value
                            FROM '._DB_PREFIX_.'feature_product
                            WHERE id_product IN ('.pInSQL($idlist).')
                            AND id_feature = '.(int) $feature_value['id_feature'].'
                            AND id_feature_value = '.(int) $feature_value['id_feature_value'];
                $res2 = Db::getInstance()->ExecuteS($sql2);
                if (!empty($res2))
                {
                    foreach ($res2 as $temp2)
                    {
                        if (!empty($temp2['id_feature_value']))
                        {
                            ++$nb_present;
                        }
                    }
                }

                if ($nb_present == $cntProducts)
                {
                    $used[$feature_value['id_feature_value']][0] = 1;
                    $used[$feature_value['id_feature_value']][1] = '7777AA';
                }
                elseif ($nb_present < $cntProducts && $nb_present > 0)
                {
                    $used[$feature_value['id_feature_value']][1] = '777777';
                }
            }
        }

        foreach ($feature_values as $row)
        {
            echo '<row id="'.$row['id_feature_value'].'">';
            echo '<cell><![CDATA['.$row['id_feature_value'].']]></cell>';
            echo '<cell style="background-color:'.((!empty($used[$row['id_feature_value']][1])) ? '#'.$used[$row['id_feature_value']][1] : '').'">'.$used[$row['id_feature_value']][0].'</cell>';
            echo '<cell><![CDATA['.$row['value'].']]></cell>';
            if (!empty($hasPosition))
            {
                echo '<cell '.($row['position'] == 'o' ? 'style="color:#AAAAAA"' : '').'><![CDATA['.$row['position'].']]></cell>';
            }
            echo '</row>';
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
?>
<rows>
<head>
<beforeInit>
</beforeInit>
<column id="id" width="40" type="ro" align="left" sort="int"><?php echo _l('ID'); ?></column>
<column id="used" width="80" type="ch" align="center" sort="int"><?php echo _l('Used'); ?></column>
<column id="name" width="200" type="ro" align="left" sort="str"><?php echo _l('Name'); ?></column>
<?php if (!empty($hasPosition)) { ?>
<column id="position" width="40" type="ro" align="left" sort="str"><?php echo _l('Pos'); ?></column>
<?php } ?>
<afterInit>
<call command="enableHeaderMenu"></call>
<call command="attachHeader"><param><![CDATA[#text_filter,#select_filter,#text_filter<?php if (!empty($hasPosition))
{
    echo ',';
} ?>]]></param></call>
</afterInit>
</head>
<?php
    getValues();
?>
</rows>
