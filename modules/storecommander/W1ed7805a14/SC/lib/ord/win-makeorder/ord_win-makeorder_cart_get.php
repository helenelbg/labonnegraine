<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_customer = (int) Tools::getValue('id_customer');
$id_shop = (int) Tools::getValue('id_shop');
$local_currency_instance = null;

$id_cart = 0;

function getRowsFromDB()
{
    global $id_lang,$id_customer,$id_shop,$id_cart,$local_currency_instance;

    $decimal = (_s('CAT_PROD_PRICEWITHOUTTAX4DEC') == '1' ? 4 : 2);

    $sql = '
            SELECT c.id_cart, o.id_order, o.valid
            FROM '._DB_PREFIX_.'cart c
                LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_cart=c.id_cart)
            WHERE c.id_customer = "'.(int) $id_customer.'"
                '.(SCMS && !empty($id_shop) ? ' AND c.id_shop = "'.(int) $id_shop.'" ' : '').'
            ORDER BY c.date_add DESC';
    $cart = Db::getInstance()->getRow($sql);
    $xml = '';
    if (!empty($cart) && (!empty($cart['id_cart']) && (empty($cart['id_order']) || (!empty($cart['id_order']) && empty($cart['valid'])))))
    {
        $cart = new Cart((int) $cart['id_cart']);
        $id_cart = $cart->id;
        $local_currency_instance = Currency::getCurrencyInstance((int) $cart->id_currency);
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
        {
            $context = Context::getContext();
            $context->currency = $local_currency_instance;
        }
        $products_detail = $cart->getProducts();
        $cart->update(); ## update la date automatiquement

        if (!empty($products_detail))
        {
            foreach ($products_detail as $product)
            {
                $price_tax_excl = (array_key_exists('price_with_reduction_without_tax', $product) ? $product['price_with_reduction_without_tax'] : $product['price']);
                $price_tax_incl = (array_key_exists('price_with_reduction', $product) ? $product['price_with_reduction'] : $product['price_wt']);
                $xml .= '<row id="'.$product['id_product'].'_'.$product['id_product_attribute'].'">';
                $xml .= '    <userdata name="path_pdt">'.$product['id_category_default'].'-'.$product['id_product'].(!empty($product['id_product_attribute']) ? '-'.$product['id_product_attribute'] : '').'</userdata>';
                $xml .= '    <cell>'.$product['id_product'].'</cell>';
                $xml .= '    <cell>'.$product['id_product_attribute'].'</cell>';
                $xml .= '    <cell><![CDATA['.$product['reference'].']]></cell>';
                $xml .= '    <cell><![CDATA['.$product['name'].(array_key_exists('attributes', $product) ? ' '.$product['attributes'] : '').']]></cell>';
                $xml .= '    <cell></cell>';
                $xml .= '    <cell>'.number_format($product['wholesale_price'], $decimal, '.', '').'</cell>';
                $xml .= '    <cell>'.number_format($price_tax_excl, $decimal, '.', '').'</cell>';
                $xml .= '    <cell>'.number_format($price_tax_incl, $decimal, '.', '').'</cell>';
                $xml .= '    <cell>'.$product['quantity'].'</cell>';
                $xml .= '    <cell>'.$product['total_wt'].'</cell>';
                $xml .= '</row>';
            }
        }
    }
    else
    {
        $customer = new Customer((int) $id_customer);

        $cart = new Cart();
        $cart->id_customer = (int) $id_customer;
        $cart->id_lang = (int) $id_lang;
        $cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $cart->secure_key = $customer->secure_key;
        if (SCMS && !empty($id_shop))
        {
            $cart->id_shop = (int) $id_shop;
        }
        $cart->add();
        $local_currency_instance = Currency::getCurrencyInstance((int) $cart->id_currency);
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
        {
            $context = Context::getContext();
            $context->currency = $local_currency_instance;
        }

        if (!empty($cart->id))
        {
            $id_cart = $cart->id;
        }
    }

    return $xml;
}

/*
0: coef = PV HT - PV HT
1: coef = (PV HT - PA HT)*100 / PA HT
2: coef = PV HT / PA HT
3: coef = PV TTC / PA HT
4: coef = (PV TTC - PA HT)*100 / PA HT
5: coef = (PV HT - PA HT)*100 / PV HT
*/
$marginMatrix_form = array(
    0 => '{price}-{wholesale_price}',
    1 => '({price}-{wholesale_price})*100/{wholesale_price}',
    2 => '{price}/{wholesale_price}',
    3 => '{price_inc_tax}/{wholesale_price}',
    4 => '({price_inc_tax}-{wholesale_price})*100/{wholesale_price}',
    5 => '({price}-{wholesale_price})*100/{price}',
);

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
            <call command="attachHeader"><param><![CDATA[#numeric_filter,#numeric_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter]]></param></call>
        </beforeInit>
        <column id="id_product" width="60" type="ro" align="right" sort="int"><?php echo _l('id prod.'); ?></column>
        <column id="id_product_attribute" width="60" type="ro" align="right" sort="int"><?php echo _l('id prod. attr.'); ?></column>
        <column id="reference" width="100" type="ro" align="left" sort="str"><?php echo _l('Reference'); ?></column>
        <column id="product" width="360" type="ro" align="left" sort="str"><?php echo _l('Product'); ?></column>
        <column id="margin" width="80" type="ro" align="right" sort="int" format="0.00"><?php echo _l('Margin/Coef'); ?></column>
        <column id="wholesale_price" width="80" type="ro" align="right" sort="int"><?php echo _l('Wholesale price'); ?></column>
        <column id="price" width="80" type="edn" align="right" sort="int"><?php echo _l('Price excl. Tax'); ?></column>
        <column id="price_it" width="80" type="ro" align="right" sort="int"><?php echo _l('Price incl. Tax'); ?></column>
        <column id="quantity" width="80" type="edn" align="right" sort="int"><?php echo _l('Quantity'); ?></column>
        <column id="total_product" width="80" type="ro" align="right" sort="int"><?php echo _l('Total produit'); ?></column>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('makeOrder_cart_grid').'</userdata>'."\n";
    echo '<userdata name="id_cart">'.$id_cart.'</userdata>'."\n";
    echo $xml;

    $cart = new Cart((int) $id_cart);
    $free_shipping = 0;
    $cart_rules = $cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING);
    if (count($cart_rules))
    {
        foreach ($cart_rules as $cart_rule)
        {
            if ($cart_rule['id_cart_rule'] == CartRule::getIdByCode(CartRule::BO_ORDER_CODE_PREFIX.(int) $cart->id))
            {
                $free_shipping = 1;
                break;
            }
        }
    }

    if (empty($local_currency_instance))
    {
        $local_currency_instance = Currency::getCurrencyInstance((int) $cart->id_currency);
    }
    echo '<userdata name="total_et">'.Tools::displayPrice($cart->getOrderTotal(false, Cart::BOTH), $local_currency_instance, false).'</userdata>'."\n";
    echo '<userdata name="total_it">'.Tools::displayPrice($cart->getOrderTotal(true, Cart::BOTH), $local_currency_instance, false).'</userdata>'."\n";
    echo '<userdata name="free_shipping">'.(int) $free_shipping.'</userdata>'."\n";
    echo '<userdata name="marginMatrix_form">'.$marginMatrix_form[_s('CAT_PROD_GRID_MARGIN_OPERATION')].'</userdata>'."\n";
    ?>
</rows>
