<?php

$id_lang = (int) Tools::getValue('id_lang');
$action = Tools::getValue('action', '');
$ids = Tools::getValue('ids', 0);
$segment_id = trim(Tools::getValue('segmentId', null), 'seg_');

/*
 * ACTION
*/
switch ($action) {
    case 'delete':
        $sql = 'DELETE FROM '._DB_PREFIX_.'sc_segment_element
                WHERE id_segment_element IN ('.pInSQL($ids).')';
        Db::getInstance()->Execute($sql);
        break;
    case 'delete_from_id_element':
        $sql = 'DELETE FROM '._DB_PREFIX_.'sc_segment_element
            WHERE id_element IN ('.pInSQL($ids).') AND id_segment = '.(int) $segment_id;
        Db::getInstance()->Execute($sql);
        break;
    case 'empty':
        $sql = 'DELETE FROM '._DB_PREFIX_.'sc_segment_element
                WHERE id_segment IN ('.pInSQL($ids).')';
        Db::getInstance()->Execute($sql);
        break;
    default:
}
