<?php

$ids = (Tools::getValue('ids', 0));
$action = (Tools::getValue('action', 0));
$value = (Tools::getValue('value', null));
$return = 'KO';

if (isset($action) && $action == 'updated')
{
    if (!empty($value))
    {
        $ids = explode(',', $ids);
        foreach ($ids as $id)
        {
            $data = explode('_', $id);
            $sql = 'UPDATE '._DB_PREFIX_."group_lang 
                    SET name = '".pSQL($value)."'
                    WHERE id_group = ".(int) $data[0].' 
                    AND id_lang = '.(int) $data[1];
            if (Db::getInstance()->Execute($sql))
            {
                $return = 'OK';
            }
        }
    }
}

exit($return);
