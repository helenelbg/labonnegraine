<?php

$id_lang = Tools::getValue('id_lang', 0);
$statusMsg = array();
$sql = '
    SELECT os.id_order_state, os.name
    FROM `' . _DB_PREFIX_ . 'order_state_lang` os
    WHERE id_lang=' . (int)$id_lang;
$res = Db::getInstance()->ExecuteS($sql);
foreach ($res as $row) {
    $statusMsg[$row['id_order_state']] = $row['name'];
}

// SETTINGS, FILTERS AND COLONNES
$sourceGridFormat = SCI::getGridViews('propcustomers');
$sql_gridFormat = $sourceGridFormat;
sc_ext::readCustomPropCustomersGridConfigXML('gridConfig');
$gridFormat = $sourceGridFormat;
$cols = explode(',', $gridFormat);
$all_cols = explode(',', $gridFormat);

$colSettings = array();
$colSettings = SCI::getGridFields('propcustomers');
sc_ext::readCustomPropCustomersGridConfigXML('colSettings');

$date_start = '';
$temp_date = _s('CAT_PROPERTIES_CUSTOMERS_START_DATE');
if (!empty($temp_date)) {
    list($temp_year, $temp_month, $temp_day) = explode('-', trim($temp_date));
    if (checkdate($temp_month, $temp_day, $temp_year)) {
        $date_start = trim($temp_date);
    }
}

function getFooterColSettings()
{
    global $cols, $colSettings;

    $footer = array();
    foreach ($cols as $id => $col) {
        if (sc_array_key_exists($col, $colSettings) && sc_array_key_exists('footer', $colSettings[$col])) {
            $footer[] = $colSettings[$col]['footer'];
        } else {
            $footer[] = '';
        }
    }
    return implode(',',$footer);
}

function getFilterColSettings()
{
    global $cols, $colSettings;

    $filters = array();
    foreach ($cols as $id => $col) {
        if(!isset($colSettings[$col])){
            continue;
        }
        if ($colSettings[$col]['filter'] == 'na') {
            $colSettings[$col]['filter'] = '';
        }
        $filters[] = $colSettings[$col]['filter'];
    }
    return implode(',',$filters);
}

function getColSettingsAsXML()
{
    global $cols, $colSettings;
    $uiset = uisettings::getSetting('cat_customerbyproduct');
    $tmp = explode('|', $uiset);
    if (isset($tmp[2])) {
        $tmp = explode('-', $tmp[2]);
        $sizes = array();
        foreach ($tmp as $v) {
            if(!isset($colSettings[$v])){
                continue;
            }
            $s = explode(':', $v);
            $sizes[$s[0]] = $s[1];
        }
    }
    $tmp = explode('|', $uiset);
    if (isset($tmp[0])) {
        $tmp = explode('-', $tmp[0]);
        $hidden = array();
        foreach ($tmp as $v) {
            $s = explode(':', $v);
            if (isset($s[0]) && isset($s[1])) {
                $hidden[$s[0]] = $s[1];
            }
        }
    }

    $xml = array();
    foreach ($cols as $id => $col) {
        $xmlCol = '';
        if(!isset($colSettings[$col])){
            continue;
        }
        $xmlCol .= '<column id="' . $col . '"' . (sc_array_key_exists('format', $colSettings[$col]) ?
                ' format="' . $colSettings[$col]['format'] . '"' : '') .
            ' width="' . (sc_array_key_exists($col, $sizes) ? $sizes[$col] : $colSettings[$col]['width']) . '"' .
            ' hidden="' . (sc_array_key_exists($col, $hidden) ? $hidden[$col] : 0). '"' .
            ' align="' . $colSettings[$col]['align'] . '"' .
            ' type="' . $colSettings[$col]['type'] . '"' .
            ($colSettings[$col]['type'] == 'combo' ? ' source="index.php?ajax=1&amp;act=cat_specificprice_customer_get&amp;ajaxCall=1" auto="true" cache="false"' : '') . '
            sort="' . $colSettings[$col]['sort'] . '"
            color="' . $colSettings[$col]['color'] . '">' . $colSettings[$col]['text'];
        if (is_array($colSettings[$col]) && sc_array_key_exists('options', $colSettings[$col]) && is_array($colSettings[$col]['options']) && !empty($colSettings[$col]['options'])) {
            foreach ($colSettings[$col]['options'] as $k => $v) {
                $xmlCol .= '<option value="' . str_replace('"', '\'', $k) . '"><![CDATA[' . $v . ']]></option>';
            }
        }
        $xmlCol .= '</column>' . "\n";
        $xml[] = $xmlCol;
    }
    return implode('',$xml);
}

function generateValue($col, $row, $language_arr)
{
    global $colSettings, $id_lang, $tax, $defaultimg, $manus, $suppliers, $statusMsg;


    $return = '';
    switch ($col) {
        case 'id_customer':
        case 'id_order':
        case 'id_shop':
        case 'product_id':
        case 'product_attribute_id':
        case 'product_quantity':
            $return .= '<cell class="'.$col.'">' . $row[$col] . '</cell>';
            break;
        case 'id_order_state':
            $status = isset($statusMsg[$row[$col]])?$statusMsg[$row[$col]]:'';
            $return .= '<cell class="'.$col.'"><![CDATA[' . $status . ']]></cell>';
            break;
        case 'firstname':
        case 'lastname':
            $return .= '<cell style="color:#999999" class="'.$col.'"><![CDATA[' . $row[$col]. ']]></cell>';
            break;
        case 'cus_lang':
            $return .= '<cell class="'.$col.'"><![CDATA[' . $language_arr[$row[$col] ]. ']]></cell>';
            break;
        case 'payment':
            $return .= '<cell class="'.$col.'"><![CDATA[' . str_replace('&', '-', $row[$col]). ']]></cell>';
            break;
        case 'newsletter':
            $newsletter = ($row[$col] == 0)? _l('No'):_l('Yes');
            $return .= '<cell class="'.$col.'"><![CDATA[' . $newsletter. ']]></cell>';
            break;

        default:
            $return .= '<cell class="'.$col.'"><![CDATA[' . $row[$col] . ']]></cell>';
    }

    return $return;
}

function getRowsFromDB()
{
    global $id_lang, $id_product, $cols, $colSettings, $sql;
    $xml = '';
    $ids = Tools::getValue('ids', 0);

    $sql = 'SELECT o.id_order,od.id_order_detail,  od.product_reference, gl.name as group_name, c.id_default_group as id_group, o.date_add, c.firstname, c.lastname, c.id_customer, c.email, '.(SCMS ? 'psh.name AS shop_name, psh.id_shop as id_shop,' : '').(version_compare(_PS_VERSION_, '1.5.4.0', '>=') ? ' c.id_lang as cus_lang,' : '').' o.payment, od.product_name, od.product_id, od.product_attribute_id, od.product_quantity';
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql .= ',o.current_state AS id_order_state, c.newsletter, COALESCE(c.company, "") as company';
    }
    else
    {
        $sql .= ',(SELECT oh.id_order_state FROM '._DB_PREFIX_.'order_history oh WHERE oh.id_order=o.id_order ORDER BY oh.id_order_history DESC LIMIT 1)  AS id_order_state ';
    }
    $sql .= '
    FROM `'._DB_PREFIX_.'orders` o
    LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON (o.id_order=od.id_order)
    LEFT JOIN `'._DB_PREFIX_.'customer` c ON (o.id_customer=c.id_customer)
    LEFT JOIN `'._DB_PREFIX_.'customer_group` cg ON (cg.id_customer=c.id_customer AND cg.id_group=c.id_default_group)
    LEFT JOIN `'._DB_PREFIX_.'group` g ON (c.id_default_group=g.id_group)
    LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (g.id_group=gl.id_group AND gl.id_lang = '.$id_lang.')
    '.((SCMS && SCI::getSelectedShop()) && (!empty($sc_agent->id_employee)) ? ' INNER JOIN '._DB_PREFIX_."employee_shop es ON (es.id_shop = o.id_shop AND es.id_employee = '".(int) $sc_agent->id_employee."') " : '')
        .(SCMS ? ' LEFT JOIN '._DB_PREFIX_.'shop psh ON (psh.id_shop = c.id_shop) ' : '').'
    WHERE od.product_id IN ('.pInSQL($ids).')
        '.(!empty($date_start) ? ' AND o.date_add >= "'.pSQL($date_start).' 00:00:00" ' : '').'
    ORDER BY o.id_order DESC';
    $res = Db::getInstance()->executeS($sql);
    $customers = array_column($res,'id_customer');
    $xml .= '<userdata name="customer-count">'.count(array_unique($customers)).'</userdata>';
    // languages
    $languages = Language::getLanguages(true);
    $language_arr = array();
    foreach ($languages as $language)
    {
        $language_arr[$language['id_lang']] = $language['name'];
    }
    foreach ($res as $customer) {
        $row_color = '';
        $xml .= "<row id='" . $customer['id_customer'].'-'.$customer['id_order_detail'] . "' style=\"" . $row_color . '">';
        sc_ext::readCustomPropCustomersGridConfigXML('rowUserData', (array)$customer);
        foreach ($cols as $field) {
            if (!empty($field) && isset($colSettings[$field])) {
                $xml .= generateValue($field, $customer,$language_arr);
            }
        }
        $xml .= '</row>';
    }


    return $xml;
}


//XML HEADER
if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml')) {
    header('Content-type: application/xhtml+xml');
} else {
    header('Content-type: text/xml');
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo '<rows><head>';
echo getColSettingsAsXML();
echo '<afterInit><call command="attachHeader"><param>' . getFilterColSettings() . '</param></call>
            <call command="attachFooter"><param><![CDATA[' . getFooterColSettings() . ']]></param></call></afterInit>';
echo '</head>' . "\n";
if($uiSettings = uisettings::getSetting('cat_customerbyproduct')){
    echo '<userdata name="uisettings">' . $uiSettings . '</userdata>' . "\n";
}
sc_ext::readCustomPropSpePriceGridConfigXML('gridUserData');
echo getRowsFromDB();
?>
</rows>
