<?php

$id_lang = (int) Tools::getValue('id_lang');

$id_shop = (int) Tools::getValue('id_shop', SCI::getSelectedShop());

$combi = (int) Tools::getValue('combi', 0);
$withSubCateg = (int) Tools::getValue('withSubCateg', 0);
$dateT = Tools::getValue('dateT', date('d/m/Y'));
$dateT = dateFrtoUS($dateT);
$yearY = getYearforUs($dateT);
$id_category = (int) Tools::getValue('category', '');
$filters = Tools::getValue('filters', '');
$filters = explode(',', $filters);

if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($combi))
{
    $uisetting = '_with_combi';
}
else
{
    $uisetting = '_without_combi';
}

/*
 * OPTIONS
*/
if (SCMS)
{
    $sql = 'SELECT s.*
                FROM '._DB_PREFIX_.'shop s
                '.((!empty($sc_agent->id_employee)) ? ' INNER JOIN '._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int) $sc_agent->id_employee."') " : '').'
                ORDER BY s.name';
    $res = Db::getInstance()->ExecuteS($sql);
    $shops = array();
    $shops[0] = _l('All');
    foreach ($res as $shop)
    {
        $shops[$shop['id_shop']] = $shop['name'];
    }

    $has_shops_restrictions = false;
    $all_shops = Db::getInstance()->ExecuteS('SELECT id_shop FROM '._DB_PREFIX_.'shop');
    if (count($all_shops) != count($res))
    {
        $has_shops_restrictions = true;
    }

    $group_shops = array();
    $group_shops[0] = _l('All');
    if (!$has_shops_restrictions)
    {
        $sql = 'SELECT *
                            FROM '._DB_PREFIX_.'shop_group
                            ORDER BY name';
        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $group)
        {
            $group_shops[$group['id_shop_group']] = $group['name'];
        }
    }
}

$sql = 'SELECT *
                    FROM '._DB_PREFIX_.'group_lang
                    WHERE id_lang='.(int) $id_lang.'
                    ORDER BY id_group';
$res = Db::getInstance()->ExecuteS($sql);
$groups = array();
$groups[0] = _l('All');
foreach ($res as $group)
{
    $groups[$group['id_group']] = $group['name'];
}

$sql = 'SELECT cl.id_country,cl.name
                    FROM '._DB_PREFIX_.'country_lang cl
                    LEFT JOIN '._DB_PREFIX_.'country c ON (c.id_country=cl.id_country)
                    WHERE cl.id_lang='.(int) $id_lang.' AND c.active=1
                    ORDER BY cl.name';
$res = Db::getInstance()->ExecuteS($sql);
$countries = array();
$countries[0] = _l('All');
foreach ($res as $country)
{
    $countries[$country['id_country']] = $country['name'];
}

$sql = 'SELECT id_currency,iso_code
                    FROM '._DB_PREFIX_.'currency
                    WHERE active=1
                    ORDER BY iso_code';
$res = Db::getInstance()->ExecuteS($sql);
$currencies = array();
foreach ($res as $currency)
{
    $currencies[$currency['id_currency']] = $currency['iso_code'];
}

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

// Manufacturers
$arrManufacturers = array();
$where = '';
if (SCMS && $id_shop > 0)
{
    $where = ' INNER JOIN '._DB_PREFIX_."manufacturer_shop ms ON ms.id_manufacturer = m.id_manufacturer WHERE ms.id_shop = '".(int) $id_shop."'";
}

$sql = 'SELECT m.id_manufacturer,m.name FROM '._DB_PREFIX_.'manufacturer m '.$where.' ORDER BY m.name';
$res = Db::getInstance()->ExecuteS($sql);
foreach ($res as $row)
{
    if ($row['name'] == '')
    {
        $row['name'] = ' ';
    }
    $arrManufacturers[$row['id_manufacturer']] = $row['name'];
}
$arrManufacturers[0] = '-';

// Suppliers
$arrSuppliers = array();
$where = '';
if (SCMS && $id_shop > 0)
{
    $where = ' INNER JOIN '._DB_PREFIX_."supplier_shop ss ON ss.id_supplier = s.id_supplier WHERE ss.id_shop = '".(int) $id_shop."'";
}

$sql = 'SELECT s.id_supplier,s.name FROM '._DB_PREFIX_.'supplier s '.$where.' ORDER BY s.name';
$res = Db::getInstance()->ExecuteS($sql);
foreach ($res as $row)
{
    if ($row['name'] == '')
    {
        $row['name'] = ' ';
    }
    $arrSuppliers[$row['id_supplier']] = $row['name'];
}
$arrSuppliers[0] = '-';

// Tax
$arrTax = array(0 => '-');
$tax = array(0 => 0);

$inner = '';

if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCMS && $id_shop > 0)
{
    $inner = ' INNER JOIN '._DB_PREFIX_."tax_rules_group_shop trgs ON (trgs.id_tax_rules_group = trg.id_tax_rules_group AND trgs.id_shop = '".(int) $id_shop."')";
}

$sql = 'SELECT trg.name, trg.id_tax_rules_group,t.rate
        FROM `'._DB_PREFIX_.'tax_rules_group` trg
        LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int) SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
            LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
        '.$inner.'
        WHERE trg.active=1';
$res = Db::getInstance()->ExecuteS($sql);
foreach ($res as $row)
{
    if ($row['name'] == '')
    {
        $row['name'] = ' ';
    }
    $arrTax[$row['id_tax_rules_group']] = $row['name'];
    $tax[$row['id_tax_rules_group']] = $row['rate'];
}

$marginMatrix_form = array(
        0 => '{price}-{wholesale_price}',
        1 => '({price}-{wholesale_price})*100/{wholesale_price}',
        2 => '{price}/{wholesale_price}',
        3 => '{price_inc_tax}/{wholesale_price}',
        4 => '({price_inc_tax}-{wholesale_price})*100/{wholesale_price}',
        5 => '({price}-{wholesale_price})*100/{price}',
);

/*
 * FUNCTIONS
 */
function formatDateNum($date)
{
    $date = explode(' ', $date);

    return date('Ymd', strtotime($date[0]));
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
    global $cols,$colSettings,$uisetting;

    $uiset = uisettings::getSetting('specificprice_grid'.$uisetting);
    $sizes = $hidden = array();
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

function getSubCategoriesXml($parent_id)
{
    global $xml_sub;
    $sql = 'SELECT c.id_category FROM '._DB_PREFIX_.'category c WHERE c.id_parent='.(int) $parent_id;
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $row)
    {
        $xml_sub .= getXml($row['id_category']);
        getSubCategoriesXml($row['id_category']);
    }
}

/*
 * XML
 */
$sourceGridFormat = SCI::getGridViews('winspeprice');
$sql_gridFormat = $sourceGridFormat;
sc_ext::readCustomWinSpePriceGridConfigXML('gridConfig');
$gridFormat = $sourceGridFormat;
if (version_compare(_PS_VERSION_, '1.5.0.0', '<') || empty($combi))
{
    $gridFormat = str_replace(',id_product_attribute,', ',', $gridFormat);
}
$gridFormat = str_replace(',,', ',', $gridFormat);
$cols = explode(',', $gridFormat);
$all_cols = explode(',', $gridFormat);

$colSettings = array();
$colSettings = SCI::getGridFields('winspeprice');
sc_ext::readCustomWinSpePriceGridConfigXML('colSettings');

function generateValue($col, $row, $p, $pa = null, $params = array(), $row_color = null)
{
    global $colSettings,$id_lang,$tax,$arrManufacturers,$arrSuppliers,$defaultimg;
    $return = '';
    switch ($col){
        case 'id_specific_price':
            $return .= ('<cell style="color:#999999">'.$row['id_specific_price'].'</cell>');
            break;
        case 'name':
            $name = $p->name;
            if (is_array($name))
            {
                $name = $name[$id_lang];
            }
            $return .= '<cell'.(!empty($row_color) ? ' style="'.$row_color.'"' : '').'><![CDATA['.$name.']]></cell>';
            break;
        case 'from_quantity':
            if (_s('APP_COMPAT_MODULE_PPE'))
            {
                $row['from_quantity'] = number_format($row['from_quantity'], 6, '.', '');
            }
            $return .= ('<cell><![CDATA['.$row['from_quantity'].']]></cell>');
            break;
        case 'manufacturer':
            $return .= ('<cell><![CDATA['.$arrManufacturers[$p->id_manufacturer].']]></cell>');
            break;
        case 'supplier':
            $return .= ('<cell><![CDATA['.$arrSuppliers[$p->id_supplier].']]></cell>');
            break;
        case 'price':
            $return .= '<cell>'.($row['price'] != -1 || version_compare(_PS_VERSION_, '1.5.0.0', '<') ? number_format($row['price'], 2) : '-1').'</cell>';
            break;
        case 'reduction_price':
            $return .= '<cell'.(!empty($row_color) ? ' style="'.$row_color.'"' : '').'><![CDATA['.number_format($params['reduction_price'], 2, '.', '').']]></cell>';
            break;
        case 'reduction_percent':
            $return .= '<cell'.(!empty($row_color) ? ' style="'.$row_color.'"' : '').'><![CDATA['.number_format($params['reduction_percent'], 2, '.', '').']]></cell>';
            break;
        case 'margin_wt_amount_after_reduction':
            $return .= '<cell><![CDATA['.$params['margin_wt_amount_after_reduction'].']]></cell>';
            break;
        case 'margin_wt_percent_after_reduction':
            $return .= '<cell><![CDATA['.$params['margin_wt_percent_after_reduction'].']]></cell>';
            break;
        case 'margin_after_reduction':
            $return .= '<cell'.(!empty($row_color) ? ' style="'.$row_color.'"' : '').'><![CDATA['.$params['margin_after_reduction'].']]></cell>';
            break;
        case 'price_with_reduction_tax_excl':
            $return .= '<cell'.(!empty($row_color) ? ' style="'.$row_color.'"' : '').'><![CDATA['.number_format($params['price_with_reduction_tax_excl'], 2, '.', '').']]></cell>';
            break;
        case 'price_with_reduction_tax_incl':
            $return .= '<cell'.(!empty($row_color) ? ' style="'.$row_color.'"' : '').'><![CDATA['.number_format($params['price_with_reduction_tax_incl'], 2, '.', '').']]></cell>';
            break;
        case 'shop_id':
            $return .= ('<cell><![CDATA['.$row['id_shop'].']]></cell>');
            break;
        case 'on_sale':
            $return .= ('<cell><![CDATA['.$p->on_sale.']]></cell>');
            break;
        case 'from':case 'to':
            $return .= '<cell'.(!empty($row_color) ? ' style="'.$row_color.'"' : '').'><![CDATA['.$row[$col].']]></cell>';
            break;
        case 'image':
            $image = Image::getCover((int) $p->id);
            if (empty($image['id_image']))
            {
                $f = '<i class="fad fa-file-image" ></i>';
            }
            else
            {
                $f = "<img src='".SC_PS_PATH_REL.'img/p/'.getImgPath((int) $p->id, (int) $image['id_image'], _s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>";
            }
            $return .= '<cell><![CDATA['.$f.']]></cell>';
            break;
        case 'active':
            if (!empty($p->active))
            {
                $f = _l('Yes');
            }
            else
            {
                $f = _l('No');
            }
            $return .= '<cell><![CDATA['.$f.']]></cell>';
            break;
        case 'id_customer':
            if ($row['id_customer'] > 0)
            {
                $sql = 'SELECT CONCAT_WS(" ",firstname,lastname) as customer FROM '._DB_PREFIX_.'customer WHERE id_customer = '.(int) $row['id_customer'];
                $customer = Db::getInstance()->getRow($sql);
                $return .= '<cell><![CDATA['.$customer['customer'].']]></cell>';
            }
            else
            {
                $return .= '<cell><![CDATA['._l('All').']]></cell>';
            }
            break;
        case 'reference':case 'supplier_reference':case 'ean13':case 'upc':
            if (empty($row['id_product_attribute']))
            {
                $return .= '<cell><![CDATA['.$p->{$col}.']]></cell>';
            }
            else
            {
                $return .= '<cell><![CDATA['.$pa->{$col}.']]></cell>';
            }
            break;
        case 'reduction_tax':
            if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
            {
                $return .= '<cell>'.$row[$col].'</cell>';
            }
            else
            {
                $return .= '<cell type="ro"><![CDATA['._l('Incl. tax').']]></cell>';
            }
            break;
        default:
            $return .= '<cell>'.$row[$col].'</cell>';
    }

    return $return;
}

function getXml($id_category)
{
    global $id_lang,$id_shop,$dateT,$filters,$arrManufacturers,$arrSuppliers,$tax,$combi,$yearY,$withSubCateg,$cols,$colSettings,$sql;
    $xml = '';
    if (!empty($id_category))
    {
        $cat_where = '';
        if ($withSubCateg)
        {
            $category_selected = new Category((int) $id_category);
            $cat_where .= ' INNER JOIN '._DB_PREFIX_.'category_product cp ON (sp.id_product = cp.id_product)
                    INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND c.nleft>="'.$category_selected->nleft.'" AND c.nright<="'.$category_selected->nright.'" ) ';
        }
        else
        {
            $cat_where .= ' INNER JOIN '._DB_PREFIX_.'category_product cp ON (sp.id_product = cp.id_product AND cp.id_category="'.(int) $id_category.'") ';
        }

        $where = '';
        if (SCMS && !empty($id_shop))
        {
            $where .= ' AND (sp.id_shop=0 OR sp.id_shop='.($id_shop > 0 ? (int) $id_shop : 'p.id_shop_default').')';
        }

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && empty($combi))
        {
            $where .= ' AND (sp.id_product_attribute=0)';
        }

        // FILTERS
        // DATE
        $where_temp = '';
        if (sc_in_array('dat_present', $filters, 'catWinSpecPriceGet_filters') !== false)
        {
            $where_temp .= ' (sp.`from` >= "'.$dateT.' 00:00:00'.'" OR sp.`to` <= "'.$dateT.' 23:59:59'.'" OR ("'.$dateT.'" between sp.`from` and sp.`to`))';
        }
        if (sc_in_array('dat_futur', $filters, 'catWinSpecPriceGet_filters') !== false)
        {
            if (!empty($where_temp))
            {
                $where_temp .= ' OR ';
            }
            $where_temp .= ' ( sp.`from` > "'.$dateT.' 23:59:59'.'" ) ';
        }
        if (sc_in_array('dat_past', $filters, 'catWinSpecPriceGet_filters') !== false)
        {
            if (!empty($where_temp))
            {
                $where_temp .= ' OR ';
            }
            $where_temp .= ' ( sp.`to` < "'.$dateT.' 00:00:00'.'" AND sp.`to`!="0000-00-00 00:00:00") ';
        }
        if (!empty($where_temp))
        {
            $where .= ' AND ('.$where_temp.') ';
        }

        if (!sc_in_array('dat_unlimited', $filters, 'catWinSpecPriceGet_filters') !== false && !empty($filters) && !empty($filters[0]))
        {
            $where .= ' AND ( sp.`to`!="0000-00-00 00:00:00" ) ';
        }
        elseif (sc_in_array('dat_unlimited', $filters, 'catWinSpecPriceGet_filters') !== false && count($filters) == 1)
        {
            $where .= ' AND ( sp.`to`="0000-00-00 00:00:00" ) ';
        }

        // FOURNISSEURS
        $where_temp = '';
        foreach ($filters as $filter)
        {
            if (strpos($filter, 'sup_') !== false)
            {
                $id_f = (int) str_replace('sup_', '', $filter);
                if (!empty($id_f))
                {
                    if (!empty($where_temp))
                    {
                        $where_temp .= ' OR ';
                    }
                    $where_temp .= ' ( p.id_supplier = "'.(int) $id_f.'" ) ';
                }
            }
        }
        if (!empty($where_temp))
        {
            $where .= ' AND ('.$where_temp.') ';
        }

        // MARQUES
        $where_temp = '';
        foreach ($filters as $filter)
        {
            if (strpos($filter, 'man_') !== false)
            {
                $id_m = (int) str_replace('man_', '', $filter);
                if (!empty($id_m))
                {
                    if (!empty($where_temp))
                    {
                        $where_temp .= ' OR ';
                    }
                    $where_temp .= ' ( p.id_manufacturer = "'.(int) $id_m.'" ) ';
                }
            }
        }
        if (!empty($where_temp))
        {
            $where .= ' AND ('.$where_temp.') ';
        }

        // MARQUES
        $where_temp = '';
        foreach ($filters as $filter)
        {
            if (strpos($filter, 'cou_') !== false)
            {
                $id_c = (int) str_replace('cou_', '', $filter);
                if (!empty($id_c))
                {
                    if (!empty($where_temp))
                    {
                        $where_temp .= ' OR ';
                    }
                    $where_temp .= ' ( sp.id_country = "'.(int) $id_c.'" ) ';
                }
            }
        }
        if (!empty($where_temp))
        {
            $where .= ' AND ('.$where_temp.') ';
        }

        //echo $where;die();

        $sql = '
            SELECT sp.*, p.reference, p.on_sale '.(SCMS ? ',ps.on_sale' : '').(SCMS ? ' ,ps.id_shop as product_id_shop' : '').' ';
        sc_ext::readCustomWinSpePriceGridConfigXML('SQLSelectDataSelect');
        $sql .= ' FROM '._DB_PREFIX_.'specific_price sp
                '.$cat_where.'
                INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product)
                    '.(SCMS ? ' INNER JOIN '._DB_PREFIX_.'product_shop ps ON (ps.id_product = sp.id_product AND ps.id_shop='.($id_shop > 0 ? (int) $id_shop : 'p.id_shop_default').')' : '').' ';
        sc_ext::readCustomWinSpePriceGridConfigXML('SQLSelectDataLeftJoin');
        $sql .= ' WHERE 1 AND sp.id_specific_price_rule = 0
            '.$where.(version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? ' AND sp.id_cart = 0 ' : '').'
            GROUP BY sp.id_specific_price
            ORDER BY sp.`id_product` ASC, sp.`from` DESC';
        $res = Db::getInstance()->ExecuteS($sql);

        $ecotax_tax_rate = SCI::getEcotaxTaxRate();
        $disabled_fields_by_id_speprice = array();
        $xml = '';
        foreach ($res as $specific_price)
        {
            $id_shop = (int) $specific_price['product_id_shop'];
            $row_color = '';
            $id_product = $specific_price['id_product'];
            $pa = null;

            if ($specific_price['price'] > -1)
            {
                $disabled_fields_by_id_speprice[] = $specific_price['id_specific_price'];
            }

            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $product = new Product((int) $id_product, false, (int) $id_lang, (int) $id_shop);
            }
            else
            {
                $product = new Product((int) $id_product, (int) $id_lang);
            }

            // DECLINAISON
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($specific_price['id_product_attribute']))
            {
                $id_product .= '_'.$specific_price['id_product_attribute'];
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $pa = new Combination((int) $specific_price['id_product_attribute'], (int) $id_lang, (int) $id_shop);
                }
                else
                {
                    $pa = new Combination((int) $specific_price['id_product_attribute'], (int) $id_lang);
                }
            }

            // REDUCTION ILLIMITEE
            $from_base = $specific_price['from'];
            $to_base = $specific_price['to'];
            if ($specific_price['from'] == $specific_price['to'])
            {
                $specific_price['from'] = date($yearY.'-01-01 00:00:00');
                $specific_price['to'] = (date('Y') + 1).date('-m-d 00:00:00');
            }
            if ($specific_price['from'] == '0000-00-00 00:00:00')
            {
                $specific_price['from'] = date($yearY.'-01-01 00:00:00');
            }
            if ($specific_price['to'] == '0000-00-00 00:00:00')
            {
                $specific_price['to'] = (date('Y') + 1).date('-m-d 00:00:00');
            }

            // DATES NUMERIQUES
            if ($from_base != '0000-00-00 00:00:00')
            {
                $specific_price['from_num'] = formatDateNum($specific_price['from']);
            }
            else
            {
                $specific_price['from_num'] = _l('Unlimited');
            }
            if ($to_base != '0000-00-00 00:00:00')
            {
                $specific_price['to_num'] = formatDateNum($specific_price['to']);
            }
            else
            {
                $specific_price['to_num'] = _l('Unlimited');
            }

            if ((float) $specific_price['price'] > -1)
            {
                $item_price = $specific_price['price'];
            }
            else
            {
                $item_price = ($pa ? $product->price + $pa->price : $product->price);
            }

            // PRIX ET MARGES
            $params = array();
            $params['reduction_price'] = 0;
            $params['reduction_percent'] = 0;
            $params['margin_wt_amount_after_reduction'] = 0;
            $params['margin_wt_percent_after_reduction'] = 0;
            $params['margin_after_reduction'] = 0;
            $params['price_with_reduction_tax_excl'] = 0;
            $params['price_with_reduction_tax_incl'] = 0;

            if ($specific_price['reduction_type'] == 'percentage')
            {
                $params['reduction_percent'] = $specific_price['reduction'] * 100;
                $params['reduction_price'] = 0;
            }
            else
            {
                $params['reduction_percent'] = 0;
                $params['reduction_price'] = $specific_price['reduction'];
            }

            if ($params['reduction_price'] > 0)
            {
                if (version_compare(_PS_VERSION_, '1.6.0.11', '>=') && empty($specific_price['reduction_tax']))
                {
                    $params['price_with_reduction_tax_incl'] = ($item_price - $params['reduction_price']) * ($tax[(int) $product->id_tax_rules_group] / 100 + 1) + ((_s('CAT_PROD_ECOTAXINCLUDED') ? $product->ecotax * $ecotax_tax_rate : 0));
                    $params['price_with_reduction_tax_excl'] = ($item_price - $params['reduction_price']);
                }
                else
                {
                    $params['price_with_reduction_tax_incl'] = (($item_price * ($tax[(int) $product->id_tax_rules_group] / 100 + 1))) - $params['reduction_price'] + ((_s('CAT_PROD_ECOTAXINCLUDED') ? $product->ecotax * $ecotax_tax_rate : 0));
                    $params['price_with_reduction_tax_excl'] = (($item_price)) - ($params['reduction_price'] / ($tax[(int) $product->id_tax_rules_group] / 100 + 1));
                }
            }
            if ($params['reduction_percent'] > 0)
            {
                $params['price_with_reduction_tax_incl'] = (($item_price * ($tax[(int) $product->id_tax_rules_group] / 100 + 1))) * (1 - $specific_price['reduction']) + ((_s('CAT_PROD_ECOTAXINCLUDED') ? $product->ecotax * $ecotax_tax_rate : 0));
                $params['price_with_reduction_tax_excl'] = (($item_price)) * (1 - $specific_price['reduction']);
            }

            // PROMOTION ACTIVE
            if (
                ($from_base <= $dateT.' 00:00:00')
                &&
                ($to_base >= $dateT.' 23:59:59' || $to_base == '0000-00-00 00:00:00')
            ) {
                $row_color = '#FFAAFF';
            }
            // PROMOTION A VENIR
            elseif ($from_base > $dateT.' 23:59:59')
            {
                $row_color = '#eed9ee';
            }

            // PROMOTION ILLIMITE
            if (
                ($from_base <= $dateT.' 00:00:00' || $from_base == '0000-00-00 00:00:00')
                &&
                ($to_base == '0000-00-00 00:00:00')
            ) {
                $row_color = '#B389C5';
            }

            // XML
            if (!empty($row_color))
            {
                $row_color = 'background-color: '.$row_color.';';
            }
            $xml .= "<row id='".$specific_price['id_specific_price']."'>";

            $taxes = ($tax[(int) $product->id_tax_rules_group] / 100 + 1);
            $xml .= '<userdata name="price">'.$item_price.'</userdata>';
            $xml .= '<userdata name="taxes">'.$taxes.'</userdata>';
            $xml .= '<userdata name="ecotaxe">'.((_s('CAT_PROD_ECOTAXINCLUDED') ? $product->ecotax * SCI::getEcotaxTaxRate() : 0)).'</userdata>';
            $xml .= '<userdata name="wholesale_price">'.$product->wholesale_price.'</userdata>';
            $xml .= '<userdata name="id_category_default">'.$product->id_category_default.'</userdata>';

            sc_ext::readCustomWinSpePriceGridConfigXML('rowUserData', (array) $specific_price);
            foreach ($cols as $field)
            {
                if (!empty($field) && !empty($colSettings[$field]))
                {
                    $xml .= generateValue($field, $specific_price, $product, $pa, $params, $row_color);
                }
            }

            $xml .= '</row>';
        }
        $userdata_supp = '<userdata name="disabled_fields_by_id_speprice">'.implode(',', $disabled_fields_by_id_speprice).'</userdata>'."\n";

        $xml = $userdata_supp.$xml;
    }

    return $xml;
}

    $xml = getXml((int) $id_category);

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
    echo '<afterInit><call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
            <call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call></afterInit>';
    echo '</head>'."\n";

    echo '<userdata name="uisettings">'.uisettings::getSetting('specificprice_grid'.$uisetting).'</userdata>'."\n";
    echo '<userdata name="marginMatrix_form">'.$marginMatrix_form[_s('CAT_PROD_GRID_MARGIN_OPERATION')].'</userdata>'."\n";
    sc_ext::readCustomWinSpePriceGridConfigXML('gridUserData');

    echo $xml;
?>
</rows>
