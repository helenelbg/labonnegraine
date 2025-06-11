<?php

$ajaxCall = Tools::getValue('ajaxCall');
$id_lang = (int) Tools::getValue('id_lang');
$id_filter = Tools::getValue('id_filter');
$action = Tools::getValue('action');

if (!empty($ajaxCall))
{
    $search = Tools::getValue('mask');

    $return = array(
        'id_criterion' => 0,
        'name' => _l('All'),
    );
    switch ($action) {
        case 'get_criterion':
            $id_filter = str_replace('filter_', '', $id_filter);
            $id_compat = $search;
                $sql = 'SELECT ct.id_ukoocompat_criterion, ctl.value
                FROM '._DB_PREFIX_.'ukoocompat_criterion_lang ctl
                LEFT JOIN '._DB_PREFIX_.'ukoocompat_compat_criterion ct ON ct.id_ukoocompat_criterion = ctl.id_ukoocompat_criterion
                WHERE ct.id_ukoocompat_compat = '.(int) $id_compat.' 
                AND ct.id_ukoocompat_filter = '.(int) $id_filter.' 
                AND ctl.id_lang = '.(int) $id_lang;
            $res = Db::getInstance()->ExecuteS($sql);
            if (!empty($res[0]))
            {
                $return = array(
                    'criterion_id' => $res[0]['id_ukoocompat_criterion'],
                    'criterion_name' => $res[0]['value'],
                );
            }
            exit(json_encode($return));
            break;
        default:
            $sql = 'SELECT ct.*, ctl.value
                FROM '._DB_PREFIX_.'ukoocompat_criterion_lang ctl
                LEFT JOIN '._DB_PREFIX_."ukoocompat_criterion ct ON ct.id_ukoocompat_criterion = ctl.id_ukoocompat_criterion
                WHERE ctl.value LIKE '%".pSQL($search)."%' 
                AND ctl.id_lang = ".(int) $id_lang.' 
                AND ct.id_ukoocompat_filter = '.(int) $id_filter.' 
                ORDER BY ABS(ctl.value), ctl.value, ct.position';
            $res = Db::getInstance()->ExecuteS($sql);
            if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
            {
                header('Content-type: application/xhtml+xml');
            }
            else
            {
                header('Content-type: text/xml');
            }
            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $xml .= '<complete>';
            if ($res)
            {
                foreach ($res as $criterion)
                {
                    $xml .= '<option value="'.$criterion['value'].'"><![CDATA['.$criterion['value'].']]></option>';
                }
            }
            else
            {
                $xml .= '<option value="0">'._l('All (No result found)').'</option>';
            }
            $xml .= '</complete>';
            exit($xml);
            break;
    }
}
exit;
