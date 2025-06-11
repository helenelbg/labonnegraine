<?php

$limit = 25;
$shop_condition = ((version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? ' AND pl.id_shop = ' . (int)SCI::getSelectedShop() : '');

if (is_numeric($_GET['q']))
{
    $sql = 'SELECT p.id_product,pl.name as pname,pa.id_product_attribute
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product=pl.id_product AND pl.id_lang='.(int) $sc_agent->id_lang.$shop_condition.')
            LEFT JOIN `'._DB_PREFIX_."product_attribute` pa ON (p.id_product=pa.id_product)
            WHERE (
                 p.id_product = '".(int) Tools::getValue('q')."'
                OR pa.id_product_attribute = '".(int) Tools::getValue('q')."'
                OR p.ean13 = '".(int) Tools::getValue('q')."'
                OR pa.ean13 = '".(int) Tools::getValue('q')."'
                OR p.reference LIKE '%".psql(Tools::getValue('q'))."%'
                OR pa.reference LIKE '%".psql(Tools::getValue('q'))."%'
                )
                AND
                p.cache_is_pack = 0
            GROUP BY p.id_product
            ORDER BY pl.name ASC,pa.default_on DESC
            LIMIT ".(int) $limit;
    $res = Db::getInstance()->ExecuteS($sql);
}
else
{
    $sql = 'SELECT p.id_product,pl.name as pname,pa.id_product_attribute
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.id_product=pl.id_product AND pl.id_lang='.(int) $sc_agent->id_lang.$shop_condition.')
            LEFT JOIN `'._DB_PREFIX_."product_attribute` pa ON (p.id_product=pa.id_product)
            WHERE (
                p.reference LIKE '%".psql(Tools::getValue('q'))."%'
                OR pl.name LIKE '%".psql(Tools::getValue('q'))."%'
                OR pa.reference LIKE '%".psql(Tools::getValue('q'))."%'
                )
                AND
                p.cache_is_pack = 0
            GROUP BY p.id_product
            ORDER BY pl.name ASC,pa.default_on DESC
            LIMIT ".(int) $limit;
    $res = Db::getInstance()->ExecuteS($sql);
}

if ($res != '')
{
    $content = '';
    $plist = array();
    echo '[';
    foreach ($res as $row)
    {
        if (!in_array($row['id_product'], $plist))
        {
            $name = str_replace("\'", '', addslashes($row['pname']));
            if (!empty($row['id_product_attribute']))
            {
                $sql_attr = 'SELECT agl.name as gp, al.name
                            FROM '._DB_PREFIX_.'product_attribute_combination pac
                            INNER JOIN '._DB_PREFIX_.'attribute a ON pac.id_attribute = a.id_attribute
                            INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl ON a.id_attribute_group = agl.id_attribute_group
                            INNER JOIN '._DB_PREFIX_.'attribute_lang al ON pac.id_attribute = al.id_attribute';
                if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')){
                    $sql_attr .= ' INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas ON pac.id_product_attribute = pas.id_product_attribute AND pas.id_shop ='.(int)SCI::getSelectedShop().' ';
                }
                $sql_attr.= " WHERE pac.id_product_attribute = '".$row['id_product_attribute']."'
                                AND agl.id_lang = '".$sc_agent->id_lang."'
                                AND al.id_lang = '".$sc_agent->id_lang."'
                            GROUP BY a.id_attribute
                            ORDER BY agl.name";
                $res_attr = Db::getInstance()->executeS($sql_attr);
                foreach ($res_attr as $attr)
                {
                    if (!empty($attr['gp']) && !empty($attr['name']))
                    {
                        if (!empty($name))
                        {
                            $name .= ', ';
                        }
                        $name .= $attr['gp'].' : '.$attr['name'];
                    }
                }
            }

            $content .= '{"id_product":"'.$row['id_product'].'","id_product_attribute":"'.(int) $row['id_product_attribute'].'","pname":"'.$name.'"},';
            $plist[] = $row['id_product'];
        }
        if (count($plist) > 25)
        {
            break;
        }
    }
    $content = trim($content, ',');
    echo $content;
    echo ']';
}

