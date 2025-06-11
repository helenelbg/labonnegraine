<?php

$id_category_target = (int) Tools::getValue('categoryTarget', 0);
$source_category_id_list = Tools::getValue('categorySource', null);
$dropped_pages_id_list = Tools::getValue('cmsPages', null);

if ($id_category_target && $dropped_pages_id_list)
{
    $sql = 'UPDATE '._DB_PREFIX_.'cms 
        SET id_cms_category = '.$id_category_target.' 
        WHERE id_cms IN ('.pInSQL($dropped_pages_id_list).')';
    Db::getInstance()->execute($sql);

    if (!empty($source_category_id_list))
    {
        $source_categories = explode(',', $source_category_id_list);
        $source_categories[] = $id_category_target;
        $source_categories = array_values($source_categories);
        foreach ($source_categories as $id_category)
        {
            CMS::cleanPositions($id_category);
        }
    }
}
