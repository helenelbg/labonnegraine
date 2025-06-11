<?php
$id_lang = (int) Tools::getValue('id_lang');

$xml = '';

if (SCMS)
{
    $shops = array();
    $shops_array = Shop::getShops(false);
    foreach ($shops_array as $shop)
    {
        $shops[$shop['id_shop']] = $shop['name'];
    }
}

$sqlLang = 'SELECT `iso_code`,`id_lang` FROM `'._DB_PREFIX_.'lang`';
$listLang = Db::getInstance()->ExecuteS($sqlLang);
$langOption = array();
foreach ($listLang as $list)
{
    $langOption[$list['id_lang']] = $list['iso_code'];
}

// SETTINGS, FILTERS AND COLONNES
$sourceGridFormat = SCI::getGridViews('gmapartner');
$sql_gridFormat = $sourceGridFormat;
sc_ext::readCustomGMAPartnerGridConfigXML('gridConfig');
$gridFormat = $sourceGridFormat;
$cols = explode(',', $gridFormat);
$all_cols = explode(',', $gridFormat);

$colSettings = array();
$colSettings = SCI::getGridFields('gmapartner');
sc_ext::readCustomGMAPartnerGridConfigXML('colSettings');

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

    $uiset = uisettings::getSetting('gmapartner');
    $hidden = $sizes = array();
    if (!empty($uiset))
    {
        $tmp = explode('|', $uiset);
        $tmp = explode('-', $tmp[2]);
        foreach ($tmp as $v)
        {
            $s = explode(':', $v);
            if (isset($s[0]) && isset($s[1]))
            {
                $sizes[$s[0]] = $s[1];
            }
        }
        $tmp = explode('|', $uiset);
        $tmp = explode('-', $tmp[0]);
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

function generateValue($param_col, $param_row, $param_partnerCustomer, $param_color_coupon, $param_total, $param_total_payments, $param_total_to_pay, $param_total_invoiced, $nb_aff)
{
    global $colSettings,$id_lang,$shops,$col, $row, $partnerCustomer, $color_coupon, $total, $total_payments,$total_to_pay,$total_invoiced;
    $col = $param_col;
    $row = $param_row;
    $partnerCustomer = $param_partnerCustomer;
    $color_coupon = $param_color_coupon;
    $total_payments = $param_total_payments;
    $total_to_pay = $param_total_to_pay;
    $total_invoiced = $param_total_invoiced;
    $total = $param_total;
    $return = '';
    switch ($col){
        case 'ppa_date':
            if ($row['ppa_date'] == '0000-00-00')
            {
                $row['ppa_date'] = '';
            }
            $return .= '<cell><![CDATA['.$row[$col].']]></cell>';
            break;
        case 'total_payments':
            $return .= '<cell><![CDATA['.$total_payments.']]></cell>';
            break;
        case 'total_to_pay':
            $return .= '<cell><![CDATA['.$total_to_pay.']]></cell>';
            break;
        case 'total_invoiced':
            $return .= '<cell><![CDATA['.$total_invoiced.']]></cell>';
            break;
        case 'total_gained':
            $return .= '<cell><![CDATA['.$total.']]></cell>';
            break;
        case 'quantity':
            $return .= '<cell>'.$nb_aff.'</cell>';
            break;
        case 'coupon_code':
            $return .= '<cell'.(!empty($color_coupon) ? ' bgColor="'.$color_coupon.'"  style="color:#FFFFFF"' : '').'><![CDATA['.$row['coupon_code'].']]></cell>';
            break;
        case 'id_shop':
            $return .= '<cell><![CDATA['.$shops[(int) $partnerCustomer->id_shop].']]></cell>';
            break;
        case 'name':
            $name = '';
            if (!empty($partnerCustomer->firstname) || !empty($partnerCustomer->lastname))
            {
                $name = $partnerCustomer->firstname.' '.$partnerCustomer->lastname;
            }
            $return .= '<cell><![CDATA['.$name.']]></cell>';
            break;
        default:
            sc_ext::readCustomGMAPartnerGridConfigXML('rowData');
            $return .= '<cell><![CDATA['.$row[$col].']]></cell>';
            break;
    }

    return $return;
}

function getPartners()
{
    global $id_lang,$cols,$colSettings,$shops,$sql;

    $sql = 'SELECT af.*, c.id_lang, c.email, c.company ';
    sc_ext::readCustomGMAPartnerGridConfigXML('SQLSelectDataSelect');
    $sql .= ' FROM '._DB_PREFIX_.'scaff_partner af
                LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = af.customer_id)
        WHERE 1=1 ';
    sc_ext::readCustomGMAPartnerGridConfigXML('SQLSelectDataLeftJoin');
    $sql .= ' ORDER BY af.id_partner';
    $res = Db::getInstance()->ExecuteS($sql);

    $xml = '';
    if (!empty($res))
    {
        $nb_aff_by_partner = Db::getInstance()->executeS('SELECT scaff_partner_id, COUNT(id_customer) AS nb_aff
                                                                FROM '._DB_PREFIX_.'customer
                                                                WHERE scaff_partner_id != ""
                                                                GROUP BY scaff_partner_id');
        $nb_aff_by_partner = array_column($nb_aff_by_partner, 'nb_aff', 'scaff_partner_id');
        foreach ($res as $row)
        {
            $nb_aff_by_current_partner = (int) (array_key_exists($row['id_partner'], $nb_aff_by_partner) ? $nb_aff_by_partner[$row['id_partner']] : 0);

            $partnerCustomer = new Customer($row['customer_id']);

            $color_coupon = '';
            $id_coupon = 0;

            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql = 'SELECT *
                FROM '._DB_PREFIX_."cart_rule
                WHERE code='".pSQL($row['coupon_code'])."'";
                $res_coupon = Db::getInstance()->ExecuteS($sql);
                if (!empty($res_coupon[0]['id_cart_rule']))
                {
                    $id_coupon = $res_coupon[0]['id_cart_rule'];
                    if ($res_coupon[0]['reduction_product'] != 0 || $res_coupon[0]['reduction_amount'] != 0 || $res_coupon[0]['product_restriction'] != 0)
                    {
                        $color_coupon = '#D87B3C';
                    }
                }
            }
            else
            {
                $sql = 'SELECT *
                FROM '._DB_PREFIX_."discount
                WHERE name='".pSQL($row['coupon_code'])."'";
                $res_coupon = Db::getInstance()->ExecuteS($sql);
                if (!empty($res_coupon[0]['id_discount']))
                {
                    $id_coupon = $res_coupon[0]['id_discount'];
                    if ($res_coupon[0]['id_discount_type'] == 2)
                    {
                        $color_coupon = '#D87B3C';
                    }
                }
            }

            $total = 0;
            $total_payments = 0;
            $total_to_pay = 0;
            $total_invoiced = 0;

            $sql = 'SELECT *
                    FROM `'._DB_PREFIX_.'scaff_commission`
                    WHERE `id_partner` = '.(int) $row['id_partner'].'
                    AND status != "cancelled"
                    AND hidden = "0"
                    AND active = 1
                    ORDER BY id_commission_paying DESC';
            $coms = Db::getInstance()->ExecuteS($sql);

            $liste_paying_id = $prices = array();
            if (!empty($coms))
            {
                foreach ($coms as $com)
                {
                    // If it is linked with an order
                    if (!empty($com['order_id']) || empty($liste_paying_id[$com['id_commission']]))
                    {
                        $prices[$com['id_commission']] = $com['price'].'_'.$com['status'];
                    }

                    if (!empty($com['id_commission_paying']))
                    {
                        $liste_paying_id[$com['id_commission_paying']] = $com['id_commission_paying'];
                    }
                }
            }

            if (!empty($prices))
            {
                foreach ($prices as $price_row)
                {
                    list($r_price, $r_status) = explode('_', $price_row);
                    $total += $r_price;
                    switch ($r_status) {
                        case 'paid':
                        case 'paid_order':
                            $total_payments += $r_price;
                            break;
                        case 'waiting':
                            $total_to_pay += $r_price;
                            break;
                        case 'invoiced':
                            $total_to_pay += $r_price;
                            $total_invoiced += $r_price;
                            break;
                    }
                }
            }

            $xml .= '<row id="'.$row['id_partner'].'">';
            $xml .= '<userdata name="id_coupon">'.(int) $id_coupon.'</userdata>';
            if (SCMS)
            {
                $xml .= '<userdata name="id_shop">'.(int) $partnerCustomer->id_shop.'</userdata>';
            }
            sc_ext::readCustomGMAPartnerGridConfigXML('rowUserData');

            foreach ($cols as $field)
            {
                if (!empty($field) && !empty($colSettings[$field]))
                {
                    $xml .= generateValue($field, $row, $partnerCustomer, $color_coupon, $total, $total_payments, $total_to_pay, $total_invoiced, $nb_aff_by_current_partner);
                }
            }
            $xml .= '</row>';
        }
    }

    return $xml;
}

$xml = getPartners();

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

    echo '<userdata name="uisettings">'.uisettings::getSetting('gmapartner').'</userdata>'."\n";
    sc_ext::readCustomGMAPartnerGridConfigXML('gridUserData');

    echo $xml;
?>
</rows>
