<?php

$idlist = Tools::getValue('idlist');
$id_lang = (int) Tools::getValue('id_lang');
$cntGroups = count(explode(',', $idlist));
$used = array();

$sql = 'SELECT distinct cg.id_category 
        FROM '._DB_PREFIX_.'category_group cg
        WHERE cg.id_group IN ('.pInSQL($idlist).')';
$res = Db::getInstance()->ExecuteS($sql);

foreach ($res as $row)
{
    $used[] = $row['id_category'];
}

echo join(',', $used);
