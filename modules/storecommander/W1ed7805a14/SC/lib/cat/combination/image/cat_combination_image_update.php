<?php

$ids = Tools::getValue('ids');
$images = explode(',', $ids);
$selection = Tools::getValue('selection', '');
$selectionArr = preg_split('/,/', $selection);
$state = Tools::getValue('state', '');
$action = Tools::getValue('action', null);

switch ($action) {
    case 'legend':
        $id_lang = (int) Tools::getValue('id_lang');
        $legend = (string) Tools::getValue('legend', '');
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'image_lang 
                                                SET legend = "'.pSQL($legend).'" 
                                                WHERE id_image IN ('.pInSQL($ids).')');
        break;
    case 'state':
        if ($state == 'true')
        {
            foreach ($images as $id_image)
            {
                foreach ($selectionArr as $id_combi)
                {
                    $sql = '
                    SELECT COUNT(*) as nb FROM '._DB_PREFIX_.'product_attribute_image
                    WHERE id_image = '.(int) $id_image.' AND id_product_attribute='.(int) $id_combi.' GROUP BY id_image';
                    $res = Db::getInstance()->getRow($sql);
                    if (empty($res['nb']))
                    {
                        $sql = '
                        INSERT INTO '._DB_PREFIX_.'product_attribute_image (id_product_attribute,id_image)
                        VALUES ('.(int) $id_combi.','.(int) $id_image.')';
                        Db::getInstance()->Execute($sql);
                    }
                }
            }
        }
        elseif ($state == 'false')
        {
            if ($selection != '')
            {
                foreach ($images as $id_image)
                {
                    $sql = '
                            DELETE FROM '._DB_PREFIX_.'product_attribute_image
                            WHERE id_image = '.(int)$id_image.' AND id_product_attribute IN ('.pInSQL($selection).')';
                    Db::getInstance()->Execute($sql);
                }
            }
        }
        break;
}
