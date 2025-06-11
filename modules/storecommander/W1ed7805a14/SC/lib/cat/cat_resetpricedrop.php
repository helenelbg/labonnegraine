<?php

$action = Tools::getValue('action', '');
if ($action == 'dates')
{
    $date = (date('m') - 1 > 0 ? date('Y') : date('Y') - 1).'-'.(date('m') - 1 > 0 ? date('m') - 1 : 12).'-01';
    $date2 = (date('m') - 1 > 0 ? date('Y') : date('Y') - 1).'-'.(date('m') - 1 > 0 ? date('m') - 1 : 12).'-02';
    $sql = 'UPDATE '._DB_PREFIX_."specific_price SET `from`='".$date."',`to`='".$date2."' WHERE id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
    Db::getInstance()->Execute($sql);
}
if ($action == 'reductions')
{
    $sql = 'UPDATE '._DB_PREFIX_.'specific_price SET reduction=0
                WHERE id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1';
    Db::getInstance()->Execute($sql);
}
if ($action == 'delsales')
{
    $sql = 'DELETE FROM '._DB_PREFIX_.'specific_price
                WHERE id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1';
    Db::getInstance()->Execute($sql);
}
