<?php

if ($_GET['ajaxCall'] == 1)
{
    $search = Tools::getValue('mask');

    $sql = 'SELECT id_customer, firstname, lastname
                FROM '._DB_PREFIX_.'customer
                WHERE CONCAT_WS(" ", firstname, lastname) LIKE "%'.pSQL($search).'%"
                 OR id_customer =  "'.(int) $search.'"';
    $res = Db::getInstance()->ExecuteS($sql);

    $return = array(
        'id_customer' => 0,
        'name' => _l('All'),
    );
    if (Tools::getValue('getIdCus') == 1)
    {
        if ($res[0])
        {
            $return = array(
                'id_customer' => $res[0]['id_customer'],
                'name' => $res[0]['firstname'].' '.$res[0]['lastname'],
            );
        }
        exit(json_encode($return));
    }
    else
    {
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
            foreach ($res as $customer)
            {
                $xml .= '<option value="'.$customer['id_customer'].'"><![CDATA['.$customer['firstname'].' '.$customer['lastname'].']]></option>';
            }
        }
        else
        {
            $xml .= '<option value="0">'._l('All (No result found)').'</option>';
        }
        $xml .= '</complete>';
        print_r($xml);
    }
}

exit;
