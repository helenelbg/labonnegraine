<?php

$type = Tools::getValue('type');
$id_lang = (int) Tools::getValue('id_lang');
$idlist = Tools::getValue('idlist');
$cntElements = count(explode(',', $idlist));
$used = array();

$sql = 'SELECT DISTINCT(id_segment) FROM '._DB_PREFIX_.'sc_segment_element
                WHERE id_element IN ('.pInSQL($idlist).")
                    AND type_element = '".pSQL($type)."'";
$res = Db::getInstance()->ExecuteS($sql);

foreach ($res as $row)
{
    $used[] = $row['id_segment'];
}

echo join(',', $used);
