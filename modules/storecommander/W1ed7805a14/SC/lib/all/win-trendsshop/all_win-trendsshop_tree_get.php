<?php

$id_lang = (int) Tools::getValue('id_lang', Configuration::get('PS_LANG_DEFAULT'));
$icon = 'catalog.png';
$sub_icon = 'plus_ar.gif';
$icon_code = 'im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'"';
$sub_icon_code = 'im0="'.$sub_icon.'" im1="'.$sub_icon.'" im2="'.$sub_icon.'"';

    function getTrendsShopTree()
    {
        global $id_lang,$icon_code;

        $filters = array(
            'date_start' => _l('Starting').' [date]',
            'date_end' => _l('Ending').' [date]',
            'country' => _l('Country'),
            'lang' => _l('Language'),
            'payment_method' => _l('Payment method'),
        );

        $filters['customer_group'] = _l('Customer group');

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $filters['carrier'] = _l('Carrier');
        }
        if (SCMS)
        {
            $filters['shop'] = _l('Shop');
        }

        $return = '';
        foreach ($filters as $filter_key => $filter_name)
        {
            if ($filter_key == 'date_start' || $filter_key == 'date_end')
            {
                $return .= '<item text="'.$filter_name.'" id="'.$filter_key.'" open="0" '.$icon_code.'>';
            }
            else
            {
                $return .= '<item text="'.$filter_name.'" id="none_'.$filter_key.'" open="0" '.$icon_code.'>';
                switch ($filter_key) {
                    case 'shop':
                        $sql = 'SELECT DISTINCT id_shop FROM '._DB_PREFIX_.'orders';
                        $found = Db::getInstance()->executeS($sql);
                        if (!empty($found))
                        {
                            $shops = array();
                            foreach ($found as $res)
                            {
                                $shops[] = Shop::getShop((int) $res['id_shop']);
                            }
                            $return .= returnData($shops, $filter_key);
                        }
                        break;
                    case 'country':
                        $sql = 'SELECT DISTINCT addr.id_country
                                FROM '._DB_PREFIX_.'orders o
                                LEFT JOIN '._DB_PREFIX_.'address addr ON addr.id_address = o.id_address_delivery';
                        $found = Db::getInstance()->executeS($sql);
                        if (!empty($found))
                        {
                            $countries = array();
                            foreach ($found as $k => $res)
                            {
                                $countries[$k]['id_country'] = (int) $res['id_country'];
                                $countries[$k]['name'] = Country::getNameById($id_lang, (int) $res['id_country']);
                            }
                            $return .= returnData($countries, $filter_key);
                        }
                        break;
                    case 'lang':
                        $sql = 'SELECT DISTINCT o.id_lang, ll.name
                                    FROM '._DB_PREFIX_.'orders o
                                    LEFT JOIN '._DB_PREFIX_.'lang ll ON ll.id_lang = o.id_lang';
                        $languages = Db::getInstance()->executeS($sql);
                        if (!empty($languages))
                        {
                            $return .= returnData($languages, $filter_key);
                        }
                        break;
                    case 'payment_method':
                        $sql = 'SELECT DISTINCT module as name FROM '._DB_PREFIX_.'orders';
                        $found = Db::getInstance()->executeS($sql);
                        if (!empty($found))
                        {
                            foreach ($found as $res)
                            {
                                $name = $res['name'];
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $module_name = Module::getModuleName($name);
                                }
                                else
                                {
                                    $module = Module::getInstanceByName($name);
                                    $module_name = $module->displayName;
                                }
                                $return .= createRow($name, $module_name, $filter_key);
                            }
                        }
                        break;
                    case 'carrier':
                        $sql = 'SELECT DISTINCT carr.id_reference, carr.name
                                FROM '._DB_PREFIX_.'orders o 
                                LEFT JOIN '._DB_PREFIX_.'carrier carr ON carr.id_carrier = o.id_carrier';
                        $carriers = Db::getInstance()->executeS($sql);
                        $final_arr = array();
                        if (!empty($carriers))
                        {
                            foreach ($carriers as $carrier)
                            {
                                $final_arr[$carrier['id_reference']]['id_carrier'] = $carrier['id_reference'];
                                $final_arr[$carrier['id_reference']]['name'] = $carrier['name'];
                            }
                            $return .= returnData($final_arr, $filter_key);
                        }
                        break;
                    case 'customer_group':
                        $sql = 'SELECT DISTINCT(c.id_default_group) as id_customer_group, gl.name
                                FROM '._DB_PREFIX_.'orders o
                                RIGHT JOIN '._DB_PREFIX_.'customer c ON c.id_customer = o.id_customer
                                LEFT JOIN '._DB_PREFIX_.'group_lang gl ON gl.id_group = c.id_default_group AND gl.id_lang= '.(int) $id_lang.' 
                                WHERE c.id_default_group > 0';
                        $customer_group = Db::getInstance()->executeS($sql);
                        $return .= returnData($customer_group, $filter_key);
                        break;
                }
            }
            $return .= '</item>';
        }

        echo $return;
    }

    function returnData($data_list, $field)
    {
        $id_field = 'id_'.$field;
        $rows = '';
        foreach ($data_list as $data)
        {
            $rows .= createRow($data[$id_field], $data['name'], $field);
        }

        return $rows;
    }

    function createRow($id, $name, $key)
    {
        global $sub_icon_code;

        return '<item text="'.htmlspecialchars($name).'" id="'.$key.'#'.$id.'" '.$sub_icon_code.'/>';
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
    echo '<tree id="0">';
    getTrendsShopTree();
    echo '</tree>';
