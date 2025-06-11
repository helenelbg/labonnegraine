<?php

$id_customer = (string) Tools::getValue('id_customer', null);
$id_lang = (int) Tools::getValue('id_lang', Configuration::get('PS_LANG_DEFAULT'));

$ui_setting_name = 'cus_cart';
$colSettings = array();
$grids = array();
// SETTINGS, FILTERS AND COLONNES
include $ui_setting_name.'_data_fields.php';
include $ui_setting_name.'_data_views.php';
$cols = explode(',', $grids);

function getFooterColSettings()
{
    global $cols, $colSettings;

    $footer = array();
    foreach ($cols as $id => $col)
    {
        if (sc_array_key_exists($col, $colSettings) && sc_array_key_exists('footer', $colSettings[$col]))
        {
            $footer['param1'][]= $colSettings[$col]['footer'];
            $footer['param2'][]= 'text-align:right;';
        }
        else
        {
            $footer['param1'][] = '';
            $footer['param2'][] = '';
        }
    }
    $footer['param1'] = implode(',',$footer['param1']);
    $footer['param2'] = implode(',',$footer['param2']);
    return $footer;
}

function getFilterColSettings()
{
    global $cols, $colSettings;

    $filters = '';
    foreach ($cols as $id => $col)
    {
        if ($colSettings[$col]['filter'] == 'na')
        {
            $colSettings[$col]['filter'] = '';
        }
        $filters .= $colSettings[$col]['filter'].',';
    }
    $filters = trim($filters, ',');

    return $filters;
}

function getColSettingsAsXML()
{
    global $cols, $colSettings, $ui_setting_name;

    $uiset = uisettings::getSetting($ui_setting_name);
    $tmp = explode('|', $uiset);
    if (isset($tmp[2]))
    {
        $tmp = explode('-', $tmp[2]);
        $sizes = array();
        foreach ($tmp as $v)
        {
            $s = explode(':', $v);
            $sizes[$s[0]] = $s[1];
        }
    }
    $tmp = explode('|', $uiset);
    if (isset($tmp[0]))
    {
        $tmp = explode('-', $tmp[0]);
        $hidden = array();
        foreach ($tmp as $v)
        {
            $s = explode(':', $v);
            if (isset($s[0]) && isset($s[1]))
            {
                $hidden[$s[0]] = $s[1];
            }
        }
    }
    $xml = '';
    foreach ($cols as $id => $col)
    {
        $xml .= '<column id="'.$col.'"'.(sc_array_key_exists('format', $colSettings[$col]) ?
                ' format="'.$colSettings[$col]['format'].'"' : '').
            ' width="'.(sc_array_key_exists($col, $sizes) ? $sizes[$col] : $colSettings[$col]['width']).'"'.
            ' hidden="'.(sc_array_key_exists($col, $hidden) ? $hidden[$col] : 0).'"'.
            ' align="'.$colSettings[$col]['align'].'" 
                        type="'.$colSettings[$col]['type'].'" 
                        sort="'.$colSettings[$col]['sort'].'" 
                        color="'.$colSettings[$col]['color'].'">'.$colSettings[$col]['text'];
        if (!empty($colSettings[$col]['options']))
        {
            foreach ($colSettings[$col]['options'] as $k => $v)
            {
                $xml .= '<option value="'.str_replace('"', '\'', $k).'"><![CDATA['.$v.']]></option>';
            }
        }
        $xml .= '</column>'."\n";
    }

    return $xml;
}

function getData()
{
    global $id_customer, $cols, $id_lang;

    $xml = '';

    $sql = 'SELECT c.`id_cart`
            FROM '._DB_PREFIX_.'cart c
            WHERE NOT EXISTS (SELECT 1 FROM '._DB_PREFIX_.'orders o 
                                WHERE o.`id_cart` = c.`id_cart`
                                AND o.`id_customer` IN ('.pInSQL($id_customer).'))
            AND c.`id_customer` IN ('.pInSQL($id_customer).')';
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql .= ' AND c.id_shop = '.(int) SCI::getSelectedShop();
    }
    $sql .= ' ORDER BY c.`date_upd` DESC';
    $id_carts = Db::getInstance()->ExecuteS($sql);

    if (!empty($id_carts))
    {
        $carts = array();
        $cart_all_products = array();
        $info_cart_all_products = array();

        foreach ($id_carts as $one_cart)
        {
            $id_cart = (int) $one_cart['id_cart'];

            $sql = 'SELECT id_product,id_product_attribute,date_add
                FROM '._DB_PREFIX_.'cart_product 
                WHERE id_cart = '.(int) $id_cart;
            $info_cart_product = Db::getInstance()->executeS($sql);


            $tmp = array();

            if (!empty($info_cart_product))
            {
                foreach ($info_cart_product as $row)
                {
                    $tmp[$row['id_product'].'_'.$row['id_product_attribute']] = $row['date_add'];
                }
                $info_cart_all_products[$id_cart] = $tmp;
            }

            $cart_obj = new Cart($id_cart);

            if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
            {
                $context = Context::getContext();
                $context->currency = Currency::getCurrencyInstance((int) $cart_obj->id_currency);
            }
            $cart_products = $cart_obj->getProducts();

            // get customer email
            $customerEmail = Db::getInstance()->getValue('SELECT email
                FROM '._DB_PREFIX_.'customer 
                WHERE id_customer = '.(int)$cart_obj->id_customer);


            // XML Generation
            if (!empty($cart_products))
            {
                foreach ($cart_products as $product)
                {
                    $xml .= '<row id="'.$product['id_product'].'_'.$product['id_product_attribute'].'">';
                    foreach ($cols as $col)
                    {
                        switch ($col) {
                            case 'id_cart':
                                $xml .= '<cell>'.$id_cart.'</cell>';
                                break;
                            case 'id_customer':
                                $xml .= '<cell>'.$cart_obj->id_customer.'</cell>';
                                break;
                            case 'email':
                                $xml .= '<cell><![CDATA['.$customerEmail.']]></cell>';
                                break;
                            case 'product_date_add':
                                $date = '';
                                if (array_key_exists($product['id_product'].'_'.$product['id_product_attribute'], $info_cart_all_products[$id_cart]))
                                {
                                    $date = $info_cart_all_products[$id_cart][$product['id_product'].'_'.$product['id_product_attribute']];
                                }
                                $xml .= '<cell><![CDATA['.$date.']]></cell>';
                                break;
                            case 'product_name':
                                $xml .= '<cell><![CDATA['.$product['name'].': '.$product['attributes_small'].']]></cell>';
                                break;
                            case 'stock_available':
                                $xml .= '<cell><![CDATA['.SCI::getProductQty($product['id_product'], $product['id_product_attribute'], null, (!empty($cart->id_shop) ? $cart->id_shop : null)).']]></cell>';
                                break;
                            default:
                                $xml .= '<cell><![CDATA['.$product[$col].']]></cell>';
                        }
                    }
                    $xml .=  '<userdata name="open_cat_grid">'.(!empty($product['id_category_default']) ? (int) $product['id_category_default'].'-'.(int) $product['id_product'] : '').'</userdata>';
                    $xml .= '</row>';
                }
            }
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
echo '<rows><head>';
echo getColSettingsAsXML();
$footer = getFooterColSettings();
echo '<afterInit>
        <call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
        <call command="attachFooter"><param><![CDATA['.$footer['param1'].']]></param><param><![CDATA['.$footer['param2'].']]></param></call>
    </afterInit>';
echo '</head>'."\n";

echo '<userdata name="uisettings">'.uisettings::getSetting($ui_setting_name).'</userdata>'."\n";
sc_ext::readCustomPropSpePriceGridConfigXML('gridUserData');

echo getData();
?>
</rows>