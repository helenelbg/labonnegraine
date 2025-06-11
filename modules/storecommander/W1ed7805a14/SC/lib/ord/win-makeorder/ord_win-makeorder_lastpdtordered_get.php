<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_customer = (int) Tools::getValue('id_customer');

function getRowsFromDB()
{
    global $id_lang,$id_customer;

    $sql = '
            SELECT od.*, pl.name as p_name, o.date_add as order_date '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ',ps.id_category_default' : ',p.id_category_default').'
            FROM '._DB_PREFIX_.'order_detail od
                INNER JOIN '._DB_PREFIX_.'orders o ON (o.id_order=od.id_order)
                '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = od.product_id AND ps.id_shop = od.id_shop)' : 'INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)').'
                INNER JOIN '._DB_PREFIX_.'product_lang pl ON ('.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.id_product' : 'p.id_product').' = pl.id_product AND pl.id_lang='.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop=o.id_shop' : '').')
            WHERE o.id_customer = "'.(int) $id_customer.'"
            GROUP BY od.product_id, od.product_attribute_id
            ORDER BY o.date_add DESC';
    $res = Db::getInstance()->ExecuteS($sql);
    $xml = '';
    if (!empty($res))
    {
        foreach ($res as $row)
        {
            $combination_detail = null;
            if (!empty($row['product_attribute_id']))
            {
                $prod = new Product($row['product_id']);
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    if (version_compare(_PS_VERSION_, '1.7.7.6', '>='))
                    {
                        $_POST['setShopContext'] = 's-'.(int) $row['id_shop'];
                        $context = Context::getContext();
                        $context->currency = Currency::getCurrencyInstance((array_key_exists('id_currenty', $row) ? (int) $row['id_currency'] : (int) SCI::getConfigurationValue('PS_CURRENCY_DEFAULT')));
                    }
                    $attributes = $prod->getAttributesResume($id_lang);
                    if (!empty($attributes))
                    {
                        foreach ($attributes as $attr)
                        {
                            if ($attr['id_product_attribute'] == $row['product_attribute_id'])
                            {
                                $combination_detail = $attr['attribute_designation'];
                                break;
                            }
                        }
                    }
                }
                else
                {
                    $detail = array();
                    $attributes = SCI::getAttributeCombinations($prod, (int) $id_lang);
                    if (!empty($attributes))
                    {
                        foreach ($attributes as $attr)
                        {
                            if ($attr['id_product_attribute'] == $row['product_attribute_id'])
                            {
                                $detail[] = $attr['group_name'].' : '.$attr['attribute_name'];
                            }
                        }
                        $combination_detail = implode(', ', $detail);
                    }
                }
            }

            list($date, $hour) = explode(' ', $row['order_date']);

            $xml .= "<row id='".$row['product_id'].'_'.$row['product_attribute_id']."'>";
            $xml .= '      <userdata name="path_pdt">'.$row['id_category_default'].'-'.$row['product_id'].(!empty($row['product_attribute_id']) ? '-'.$row['product_attribute_id'] : '').'</userdata>';
            $xml .= '<cell>'.$row['product_id'].'-'.$row['product_attribute_id'].'</cell>';
            $xml .= '<cell><![CDATA['.$row['p_name'].(!empty($combination_detail) ? ' '.$combination_detail : '').']]></cell>';
            $xml .= '<cell><![CDATA['.$date.']]></cell>';
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

$xml = getRowsFromDB();
?>
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#text_filter,#text_filter,#text_filter]]></param></call>
        </beforeInit>
        <column id="id" width="60" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
        <column id="product" width="360" type="ro" align="left" sort="str"><?php echo _l('Product'); ?></column>
        <column id="date" width="80" type="ro" align="left" sort="str"><?php echo _l('Date'); ?></column>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('makeOrder_lastpdtordered_grid').'</userdata>'."\n";
    echo $xml;
    ?>
</rows>
