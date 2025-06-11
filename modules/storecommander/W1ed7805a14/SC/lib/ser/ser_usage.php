<?php

$snapshot = Tools::getValue('snapshot', null);
$act = Tools::getValue('act', null);

try
{
    $snapshotDecoded = json_decode($snapshot, true);
}
catch (Exception $e)
{
    exit;
}

if ($act == 'ser_usage' && !empty($snapshotDecoded))
{
    $interface_data = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'storecom_usage WHERE id_storecom_usage = 1');
    unset($interface_data['id_storecom_usage']);

    foreach ($interface_data as $interface => $data)
    {
        $interface_data[$interface] = json_decode($data, true);
        if ($interface_data[$interface] === null)
        {
            $interface_data[$interface] = array();
        }
    }

    foreach ($snapshotDecoded as $interface => $actions)
    {
        foreach ($actions as $action => $value)
        {
            if (array_key_exists($action, $interface_data[$interface]))
            {
                $interface_data[$interface][$action] += $value;
            }
            else
            {
                $interface_data[$interface][$action] = $value;
            }
        }
    }

    foreach ($interface_data as $interface => $data)
    {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'storecom_usage SET `'.bqSQL($interface).'`="'.pSQL(json_encode($data)).'" WHERE id_storecom_usage = 1');
    }
}
