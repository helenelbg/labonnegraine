<?php
$id_shop = $initial_shop_selected = (int) SCI::getSelectedShop();
$shop_group = new ShopGroup((int) Shop::getGroupFromShop((int) $id_shop));
if ($shop_group->share_stock)
{
    $id_shop = 0;
    $id_shop_group = (int) $shop_group->id;
}
else
{
    $id_shop = (int) $id_shop;
    $id_shop_group = 0;
}

$params = array(
    'id_product_list' => (string) Tools::getValue('product_list', null),
    'id_shop' => (int) $id_shop,
    'id_shop_group' => (int) $id_shop_group,
    'id_lang' => (int) Tools::getValue('id_lang'),
    'initial_shop_selected' => (int) $initial_shop_selected,
);

function getStockMovementHistory($params)
{
    $sql = 'SELECT SQL_CALC_FOUND_ROWS sm.id_stock_mvt,
                           sm.id_stock,
                           sm.id_order,
                           sm.id_employee,
                           sm.employee_lastname,
                           sm.employee_firstname,
                           sm.physical_quantity,
                           sm.date_add,
                           sm.sign,
                           smrl.id_stock_mvt_reason,
                           smrl.name                            AS movement_reason,
                           p.id_product                         AS product_id,
                           COALESCE(pa.id_product_attribute, 0) AS combination_id,
                           IF(LENGTH(COALESCE(pa.reference, "")) = 0,
                                   IF(LENGTH(TRIM(p.reference)) > 0, p.reference, "N/A"),
                                   CONCAT(p.reference, " ", pa.reference)
                               )                                AS product_reference,
                           pl.name                              AS product_name,
                           p.id_supplier                        AS supplier_id,
                           COALESCE(s.name, "N/A")              AS supplier_name,
                           (SELECT GROUP_CONCAT(DISTINCT CONCAT(agl.name, " - ", al.name) SEPARATOR ", ")
                                 FROM '._DB_PREFIX_.'product_attribute pa2
                                     JOIN '._DB_PREFIX_.'product_attribute_combination pac 
                                         ON (pac.id_product_attribute = pa2.id_product_attribute)
                                     JOIN '._DB_PREFIX_.'attribute a 
                                         ON (a.id_attribute = pac.id_attribute)
                                     JOIN '._DB_PREFIX_.'attribute_lang al 
                                         ON (a.id_attribute = al.id_attribute AND al.id_lang = '.(int) $params['id_lang'].')
                                     JOIN '._DB_PREFIX_.'attribute_group ag 
                                         ON (ag.id_attribute_group = a.id_attribute_group)
                                     JOIN '._DB_PREFIX_.'attribute_group_lang agl 
                                         ON (ag.id_attribute_group = agl.id_attribute_group AND agl.id_lang = '.(int) $params['id_lang'].')
                                WHERE pa2.id_product = p.id_product 
                                AND pa2.id_product_attribute = pa.id_product_attribute
                           )                                    AS combination_name
            FROM '._DB_PREFIX_.'stock_mvt sm
                INNER JOIN '._DB_PREFIX_.'stock_mvt_reason_lang smrl 
                    ON (smrl.id_stock_mvt_reason = sm.id_stock_mvt_reason AND smrl.id_lang = '.(int) $params['id_lang'].')
                INNER JOIN '._DB_PREFIX_.'stock_available sa 
                    ON (sa.id_stock_available = sm.id_stock)
                LEFT JOIN '._DB_PREFIX_.'product p 
                    ON (p.id_product = sa.id_product)
                LEFT JOIN '._DB_PREFIX_.'product_attribute pa 
                    ON (pa.id_product_attribute = sa.id_product_attribute)
                LEFT JOIN '._DB_PREFIX_.'product_lang pl 
                    ON (p.id_product = pl.id_product AND pl.id_lang = '.(int) $params['id_lang'].')
                INNER JOIN '._DB_PREFIX_.'product_shop ps 
                    ON (p.id_product = ps.id_product AND ps.id_shop = '.(int) $params['initial_shop_selected'].')
                LEFT JOIN '._DB_PREFIX_.'supplier s 
                    ON (p.id_supplier = s.id_supplier)
                LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac 
                    ON (pac.id_product_attribute = pa.id_product_attribute)
                LEFT JOIN '._DB_PREFIX_.'product_attribute_shop pas 
                    ON (pas.id_product = pa.id_product AND pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop = '.(int) $params['id_shop'].')
            WHERE sa.id_shop = '.(int) $params['id_shop'].'
              AND sa.id_shop_group = '.(int) $params['id_shop_group'].'
              AND sa.id_product IN ('.pInSQL($params['id_product_list']).')
              AND sa.id_product_attribute = COALESCE(pa.id_product_attribute, 0)
            GROUP BY sm.id_stock_mvt
            HAVING 1
            ORDER BY date_add DESC';
    $movements = Db::getInstance()->ExecuteS($sql);
    $xml_data = array();
    if (!empty($movements))
    {
        foreach ($movements as $movement)
        {
            $qty = $movement['physical_quantity'] * $movement['sign'];
            $xml_data[] = implode("\n", array(
                '<row id="'.(int) $movement['id_stock_mvt'].'">',
                '    <cell>'.(int) $movement['id_stock_mvt'].'</cell>',
                '    <cell>'.(int) $movement['id_order'].'</cell>',
                '    <cell>'.(int) $movement['product_id'].'</cell>',
                '    <cell>'.(int) $movement['combination_id'].'</cell>',
                '    <cell><![CDATA['.(!empty($movement['combination_name']) ? $movement['combination_name'] : '-').']]></cell>',
                '    <cell><![CDATA['.$movement['movement_reason'].']]></cell>',
                '    <cell style="color:#fff" bgColor="'.($qty > 0 ? '#25b9d7' : '#363a41').'"><![CDATA['.$qty.']]></cell>',
                '    <cell><![CDATA['.$movement['date_add'].']]></cell>',
                '    <cell><![CDATA['.$movement['employee_firstname'].' '.$movement['employee_lastname'].']]></cell>',
                '</row>',
            ));
        }
    }

    return implode("\n", $xml_data);
}

$stock_movement_history = getStockMovementHistory($params);
if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
<rows parent="0">
    <head>
        <beforeInit>
            <call command="attachHeader">
                <param><![CDATA[#numeric_filter,#numeric_filter,#numeric_filter,#text_filter,#select_filter,#numeric_filter,#text_filter,#text_filter]]></param>
            </call>
        </beforeInit>
        <column id="id_stock_mvt" width="50" type="ro" align="left" sort="int"><?php echo _l('ID'); ?></column>
        <column id="id_order" width="50" type="ro" align="left" sort="int"><?php echo _l('Id order'); ?></column>
        <column id="product_id" width="50" type="ro" align="left" sort="int"><?php echo _l('Id product'); ?></column>
        <column id="combination_id" width="50" type="ro" align="left" sort="int"><?php echo _l('Combination ID'); ?></column>
        <column id="combination_name" width="120" type="ro" align="left" sort="str"><?php echo _l('Combination name'); ?></column>
        <column id="movement_reason" width="120" type="ro" align="left" sort="str"><?php echo _l('Reason'); ?></column>
        <column id="physical_quantity" width="70" type="ro" align="center" sort="str"><?php echo _l('Quantity'); ?></column>
        <column id="date_add" width="150" type="ro" align="left" sort="str"><?php echo _l('Date add'); ?></column>
        <column id="employee_firstname" width="120" type="ro" align="left" sort="str"><?php echo _l('Employee'); ?></column>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_StockMvtHistory').'</userdata>'."\n";
    echo $stock_movement_history;
    ?>
</rows>