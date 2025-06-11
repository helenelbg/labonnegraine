<?php
    $id_order = Tools::getValue('id_order');

    // get order status
    $orderStatusPS = OrderState::getOrderStates($sc_agent->id_lang);
    $user_lang_iso = Language::getIsoById($sc_agent->id_lang);

    // SETTINGS, FILTERS AND COLONNES
    $sourceGridFormat = SCI::getGridViews('order_product');
    $sql_gridFormat = $sourceGridFormat;
    sc_ext::readCustomOrderPropProductsGridsConfigXML('gridConfig');
    $gridFormat = $sourceGridFormat;
    $cols = explode(',', $gridFormat);
    $all_cols = explode(',', $gridFormat);

    $colSettings = array();
    $colSettings = SCI::getGridFields('order_product');
    sc_ext::readCustomOrderPropProductsGridsConfigXML('colSettings');

    $cols = explode(',', $sourceGridFormat);

    $orderStatus = array(); // TODO $orderStatus toujours utilisé ?
    foreach ($orderStatusPS as $status)
    {
        $orderStatus[$status['id_order_state']] = $status;
    }

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $shop_id = SCI::getSelectedShop();
        if (empty($shop_id))
        {
            $shop_id = Configuration::get('PS_SHOP_DEFAULT');
        }
        $shop = new Shop($shop_id);
        $shop_group = $shop->getGroup();
    }
    $id_lang = $sc_agent->id_lang;
    $lang_setting = _s('ORD_LANG_PRODUCT_NAME');
    if (!empty($lang_setting))
    {
        if ($lang_setting == '2')
        {
            $id_lang_shop = Configuration::get('PS_LANG_DEFAULT', null, $shop_id, $shop->id_shop_group);
            if (!empty($id_lang_shop))
            {
                $id_lang = $id_lang_shop;
            }
        }
        elseif ($lang_setting != '1')
        {
            $id_lang_wanted = Language::getIdByIso(strtolower($lang_setting));
            if (!empty($id_lang_wanted))
            {
                $id_lang = $id_lang_wanted;
            }
        }
    }

function getFooterColSettings()
{
    global $cols,$colSettings;

    $footer = '';
    foreach ($cols as $id => $col)
    {
        if (sc_array_key_exists($col, $colSettings) && sc_array_key_exists('footer', $colSettings[$col]))
        {
            $footer .= $colSettings[$col]['footer'].',';
        }
        else
        {
            $footer .= ',';
        }
    }

    return $footer;
}

function getFilterColSettings()
{
    global $cols,$colSettings;

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
    global $cols,$colSettings;

    $uiset = uisettings::getSetting('ord_product');
    $hidden = $sizes = array();
    if (!empty($uiset))
    {
        $tmp = explode('|', $uiset);
        $tmp = explode('-', $tmp[2]);
        foreach ($tmp as $v)
        {
            $s = explode(':', $v);
            $sizes[$s[0]] = $s[1];
        }
        $tmp = explode('|', $uiset);
        $tmp = explode('-', $tmp[0]);
        foreach ($tmp as $v)
        {
            $s = explode(':', $v);
            $hidden[$s[0]] = $s[1];
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
        if (is_array($colSettings[$col]) && sc_array_key_exists('options', $colSettings[$col]) && is_array($colSettings[$col]['options']) && !empty($colSettings[$col]['options']))
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

function getProducts()
{
    global $id_order,$cols,$colSettings,$id_lang,$orderStatus,$user_lang_iso;
    $yesno = array(0 => _l('No'), 1 => _l('Yes'));

    ## image product
    $defaultimg = 'lib/img/i.gif';
    if (file_exists(SC_PS_PATH_DIR.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'_default.jpg'))
    {
        $defaultimg = SC_PS_PATH_REL.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'_default.jpg';
    }
    elseif (file_exists(SC_PS_PATH_DIR.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'.jpg'))
    {
        $defaultimg = SC_PS_PATH_REL.'img/p/'.$user_lang_iso.'-default-'._s('CAT_PROD_GRID_IMAGE_SIZE').'.jpg';
    }

    $sql = '
            SELECT od.*, o.id_currency, pl.name as p_name, '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.id_category_default, od.unit_price_tax_excl AS product_price ' : 'p.id_category_default').', img.id_image
            FROM '._DB_PREFIX_.'order_detail od
                INNER JOIN '._DB_PREFIX_.'orders o ON (o.id_order=od.id_order)
                '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'LEFT JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = od.product_id AND ps.id_shop = od.id_shop)' : 'LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = od.product_id)').'
                LEFT JOIN '._DB_PREFIX_.'product_lang pl ON ('.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.id_product' : 'p.id_product').' = pl.id_product AND pl.id_lang='.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ' AND pl.id_shop=o.id_shop' : '').')';
    if (version_compare(_PS_VERSION_, '1.6.1.0', '>='))
    {
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'image_shop img ON (img.id_product = od.product_id AND img.id_shop = od.id_shop AND img.cover = 1)';
    }
    elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'image_shop img
                    ON (img.id_image IN (SELECT GROUP_CONCAT(id_image) FROM '._DB_PREFIX_.'image WHERE id_product = od.product_id) AND img.id_shop = od.id_shop AND img.cover = 1)';
    }
    else
    {
        $sql .= ' LEFT JOIN '._DB_PREFIX_.'image img ON (img.id_product = od.product_id AND img.cover = 1)';
    }
    $sql .= ' WHERE od.id_order IN ('.pInSQL($id_order).')
            ORDER BY od.id_order_detail';
    $res = Db::getInstance()->ExecuteS($sql);
    $xml = '';
    if (!empty($res))
    {
        $imageManager = null;
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
        {
            $imageManager = new PrestaShop\PrestaShop\Adapter\ImageManager(new PrestaShop\PrestaShop\Adapter\LegacyContext());
        }
        foreach ($res as $history)
        {
            $history['actual_quantity_in_stock'] = SCI::getProductQty($history['product_id'], $history['product_attribute_id'], $history['id_warehouse']);

            // IN STOCK
            $history['instock'] = 0;
            $color_instock = '';
            $order_in_stock = ($history['product_quantity_in_stock'] >= $history['product_quantity'] ? 1 : 0);
            if ($order_in_stock == 1)
            {
                $history['instock'] = 1;
            }
            else
            {
                $total_qty_wanted = 0;
                if (!empty($history['product_id']))
                {
                    $sql_details = 'SELECT product_quantity FROM '._DB_PREFIX_.'order_detail WHERE product_id='.(int) $history['product_id'].' AND product_attribute_id='.(int) $history['product_attribute_id'];
                    $res_details = Db::getInstance()->ExecuteS($sql_details);
                    foreach ($res_details as $res_detail)
                    {
                        $total_qty_wanted += $res_detail['product_quantity'];
                    }

                    if ($history['actual_quantity_in_stock'] >= $history['product_quantity'])
                    {
                        $history['instock'] = 1;
                    }
                    if ($history['actual_quantity_in_stock'] < $total_qty_wanted && $history['actual_quantity_in_stock'] > 0)
                    {
                        $history['instock'] = 3;
                        $color_instock = '#FF9900';
                    }
                }
            }
            if ($history['instock'] == 0 && empty($color_instock))
            {
                $color_instock = '#FF0000';
            }

            if ($history['instock'] == 2)
            {
                $instock = _l('Insufficient current total stock');
            }
            elseif ($history['instock'] == 3)
            {
                $instock = _l('Partial');
            }
            else
            {
                $instock = $yesno[$history['instock']];
            }

            $combination_detail = null;
            if (!empty($history['product_attribute_id']))
            {
                $prod = new Product($history['product_id']);
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    if (version_compare(_PS_VERSION_, '1.7.7.6', '>='))
                    {
                        $_POST['setShopContext'] = 's-'.(int) $history['id_shop'];
                        $context = Context::getContext();
                        $context->currency = Currency::getCurrencyInstance((int) $history['id_currency']);
                    }
                    $attributes = $prod->getAttributesResume($id_lang);
                    if (!empty($attributes))
                    {
                        foreach ($attributes as $attr)
                        {
                            if ($attr['id_product_attribute'] == $history['product_attribute_id'])
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
                            if ($attr['id_product_attribute'] == $history['product_attribute_id'])
                            {
                                $detail[] = $attr['group_name'].' : '.$attr['attribute_name'];
                            }
                        }
                        $combination_detail = implode(', ', $detail);
                    }
                }
            }

            $xml .= "<row id='".$history['id_order_detail']."'>";
            $xml .= '<userdata name="open_cat_grid">'.$history['id_category_default'].'-'.$history['product_id'].'</userdata>';
            foreach ($cols as $field)
            {
                if (!empty($field) && !empty($colSettings[$field]))
                {
                    $xml .= generateValue($field, $history, $imageManager, $instock, $color_instock, $combination_detail);
                }
            }
            $xml .= '</row>';
        }
    }

    return $xml;
}

function generateValue($col, $row, $imageManager, $instock, $color_instock, $combination_detail)
{
    $return = '';
    switch ($col){
        case 'id_order_detail':
            $return .= '<cell style="color:#999999">'.$row['id_order_detail'].'</cell>';
            break;
        case 'product_name':
            $name = ((int) _s('ORD_ORDER_DETAIL_PRODUCT_NAME')) ? $row['product_name'] : $row['p_name'].(!empty($combination_detail) ? ' '.$combination_detail : '');
            $return .= '<cell><![CDATA['.$name.']]></cell>';
            break;
        case 'in_stock':
            $return .= '<cell'.(!empty($color_instock) ? ' bgColor="'.$color_instock.'"  style="color:#FFFFFF"' : '').'>'.$instock.'</cell>';
            break;
        case 'product_price':
            $return .= '<cell>'.number_format($row['product_price'], 6, '.', '').'</cell>';
            break;
        case 'product_weight':
            $return .= '<cell>'.number_format($row['product_weight'], 6, '.', '').'</cell>';
            break;
        case 'image':
            $img = '<i class="fad fa-file-image" ></i>';
            if (!empty($row['id_image']))
            {
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                {
                    $img = $imageManager->getThumbnailForListing((int) $row['id_image']);
                }
                elseif (version_compare(_PS_VERSION_, '1.5.0.5', '>='))
                {
                    $ord_cover = new Image((int) $row['id_image'], null, $row['id_shop']);
                    $ord_cover_path = _PS_IMG_DIR_.'p/'.$ord_cover->getExistingImgPath().'.jpg';
                    $img = ImageManager::thumbnail($ord_cover_path, 'product_mini_'.(int) $row['product_id'].'.jpg', 45);
                }
            }elseif(isset($row['product_id'])){
                $image_name = 'product_mini_'.(int) $row['product_id'].(isset($row['product_attribute_id']) ? '_'.(int) $row['product_attribute_id'] : '').'.jpg';
                if (file_exists(SC_PS_PATH_REL.'img/tmp/'.$image_name))
                {
                    $img = '<img src="'.SC_PS_PATH_REL.'img/tmp/'.$image_name.'"/>';
                }
            }
            $return .= '<cell><![CDATA['.$img.']]></cell>';
            break;
        default:
            $return .= '<cell><![CDATA['.$row[$col].']]></cell>';
            break;
    }

    return $return;
}
$xml = getProducts();
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
    <rows><head>
    <?php echo getColSettingsAsXML(); ?>
    <afterInit><call command="attachHeader"><param><?php echo getFilterColSettings(); ?></param></call>
        <call command="attachFooter"><param><![CDATA[<?php echo getFooterColSettings(); ?>]]></param></call></afterInit>
    </head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('ord_product').'</userdata>'."\n";
    echo $xml;
?>
</rows>
