<?php

    $ids_image = Tools::getValue('ids');
    $images = explode(',', $ids_image);
    $state = Tools::getValue('state', '');
    $selection = Tools::getValue('selection', null);
    $selection_arr = explode(',', $selection);
    $list = array();
    foreach ($selection_arr as $k => $row)
    {
        list($id_product, $id_product_attribute) = explode('_', $row);
        $list[(int) $id_product_attribute] = (int) $id_product;
    }
    if (!empty($list))
    {
        if ($state == 'true')
        {
            $sql = 'SELECT DISTINCT(id_product)
                    FROM '._DB_PREFIX_.'image 
                    WHERE id_image IN ('.pInSQL($ids_image).')';
            $cache_prd = Db::getInstance()->executeS($sql);
            $cache_id_product = array();
            if (!empty($cache_prd))
            {
                foreach ($cache_prd as $row)
                {
                    $cache_id_product[] = (int) $row['id_product'];
                }
            }
            $need_to_copy_image = array();
            foreach ($images as $id_image)
            {
                foreach ($list as $id_product_attribute => $id_product)
                {
                    $sql = 'SELECT COUNT(*) AS nb 
                            FROM '._DB_PREFIX_.'product_attribute_image
                            WHERE id_image = '.(int) $id_image.' 
                            AND id_product_attribute='.(int) $id_product_attribute.' 
                            GROUP BY id_image';
                    $res = Db::getInstance()->getRow($sql);

                    if (empty($res['nb']))
                    {
                        if (!in_array($id_product, $cache_id_product))
                        {
                            $new_image_id = SCI::duplicateProductImages($cache_id_product[0], $id_product, $id_image);
                            if (!empty($new_image_id))
                            {
                                $sql = 'INSERT INTO '._DB_PREFIX_.'product_attribute_image (id_product_attribute,id_image)
                                VALUES ('.(int) $id_product_attribute.','.(int) $new_image_id.')';
                                Db::getInstance()->Execute($sql);
                            }
                        }
                        else
                        {
                            $sql = 'INSERT INTO '._DB_PREFIX_.'product_attribute_image (id_product_attribute,id_image)
                                VALUES ('.(int) $id_product_attribute.','.(int) $id_image.')';
                            Db::getInstance()->Execute($sql);
                        }
                    }
                }
            }
        }
        elseif ($state == 'false')
        {
            $id_product_attribute_list = array();
            foreach ($list as $id_produc_attribute => $void)
            {
                $id_product_attribute_list[] = $id_produc_attribute;
            }
            foreach ($images as $id_image)
            {
                $sql = 'DELETE FROM '._DB_PREFIX_.'product_attribute_image
                        WHERE id_image = '.(int) $id_image.' 
                        AND id_product_attribute IN ('.pInSQL(implode(',', $id_product_attribute_list)).')';
                Db::getInstance()->Execute($sql);
            }
        }
    }
