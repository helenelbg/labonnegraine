<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $id_product = (int) Tools::getValue('id_product', 0);
    $id_products = Tools::getValue('id_products', 0);
    if (empty($id_products))
    {
        $id_products = array($id_product);
    }
    else
    {
        $id_products = explode(',', $id_products);
    }
    $list_id_image = Tools::getValue('list_id_image', 0);
    $action = Tools::getValue('action', 0);
    $id_image_array = explode(',', $list_id_image);
    $cache_product_name = array();

    $multiple = false;
    if (count($id_products) > 1)
    {
        $multiple = true;
    }

    $need_json_response = false;
    $json_reponse = array();

    foreach ($id_image_array as $id_image)
    {
        switch ($action){
            case 'update':
                $image = new Image((int) ($id_image));
                $col = Tools::getValue('col', 0);
                $val = Tools::getValue('val', 0);
                $fields = array('cover');
                $fields_lang = array('legend');
                sc_ext::readCustomImageGridConfigXML('updateSettings');
                $idlangByISO = array();
                $todo = array();
                $todo_lang = array();
                foreach ($languages as $lang)
                {
                    $fields_lang[] = 'name¤'.$lang['iso_code'];
                    $idlangByISO[$lang['iso_code']] = $lang['id_lang'];
                }
                SC_Ext::readCustomImageGridConfigXML('update_inArrayFields');
                foreach ($fields as $field)
                {
                    if ($col == $field)
                    {
                        if ($col == 'cover')
                        {
                            $sql = '';
                            if (version_compare(_PS_VERSION_, '1.6.1', '>='))
                            {
                                $sql = ('UPDATE '._DB_PREFIX_.'image SET cover=NULL WHERE id_product='.(int) $id_product);
                            }
                            else
                            {
                                $sql = ('UPDATE '._DB_PREFIX_.'image SET cover=0 WHERE id_product='.(int) $id_product);
                            }
                            if (!empty($sql))
                            {
                                Db::getInstance()->Execute($sql);
                            }
                            if (version_compare(_PS_VERSION_, '1.6.1', '>='))
                            {
                                $sql = ('UPDATE '._DB_PREFIX_.'image_shop SET cover=NULL WHERE id_product='.(int) $id_product.' AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
                            }
                            elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'image_shop SET cover=0 WHERE id_image IN (SELECT i.id_image FROM '._DB_PREFIX_.'image i WHERE i.id_product='.(int) $id_product.') AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
                            }
                            if (!empty($sql))
                            {
                                Db::getInstance()->Execute($sql);
                            }
                            $tmp_img_id = $id_product;
                            if(version_compare(_PS_VERSION_, '1.7.0.0', '>=')){
                                $tmp_img_id = $id_image;
                            }
                            @unlink(_PS_TMP_IMG_DIR_.'product_'.(int) $tmp_img_id.'.jpg');
                            @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int) $tmp_img_id.'.jpg');

                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                $shops = SCI::getSelectedShopActionList(false, (int) $id_product);
                                foreach ($shops as $shop_id)
                                {
                                    @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int) $tmp_img_id.'_'.(int) $shop_id.'.jpg');
                                }
                            }

                            if (version_compare(_PS_VERSION_, '1.6.1', '>='))
                            {
                                if (empty($val))
                                {
                                    $val = 'NULL';
                                }
                                else
                                {
                                    $val = (int) $val;
                                }
                                $todo[] = $field.'='.$val;
                            }
                            else
                            {
                                $todo[] = $field."='".psql(html_entity_decode($val))."'";
                            }
                        }
                        else
                        {
                            $todo[] = $field."='".psql(html_entity_decode($val))."'";
                        }
                        addToHistory('image', 'modification', $field, $id_image, $id_lang, _DB_PREFIX_.'image', psql(Tools::getValue($field)));
                    }
                }
                foreach ($fields_lang as $field)
                {
                    if ($col == $field)
                    {
                        $todo_lang[] = '`'.bqSQL($field)."`='".psql($val)."'";
                        addToHistory('image', 'modification', $field, $id_image, $id_lang, _DB_PREFIX_.'image_lang', $val);
                    }
                }
                if (count($todo))
                {
                    $sql = 'UPDATE '._DB_PREFIX_.'image SET '.join(' , ', $todo).' WHERE id_image='.(int) $id_image;
                    Db::getInstance()->Execute($sql);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $col == 'cover')
                    {
                        $sql = 'UPDATE '._DB_PREFIX_.'image_shop SET '.join(' , ', $todo).' WHERE id_image='.(int) $id_image.' AND id_shop IN ('.SCI::getSelectedShopActionList(true).')';
                        Db::getInstance()->Execute($sql);
                    }
                }
                if (count($todo_lang))
                {
                    $sql2 = 'UPDATE '._DB_PREFIX_.'image_lang SET '.join(' , ', $todo_lang).' WHERE id_image='.(int) $id_image.' AND id_lang='.(int) $id_lang;
                    Db::getInstance()->Execute($sql2);
                }
                sc_ext::readCustomImageGridConfigXML('onAfterUpdateSQL');
                break;
            case 'image_fill_legend_current_lang':
                $image = new Image((int) ($id_image));
                $sql = 'SELECT id_product FROM '._DB_PREFIX_."image WHERE id_image='".(int) $id_image."'";
                $id_product = Db::getInstance()->getValue($sql);
                if (!(empty($id_product)))
                {
                    if (empty($cache_product_name[$id_product]))
                    {
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $pduct = new Product((int) $id_product, false, (int) $id_lang,
                                (int) SCI::getSelectedShop());
                        }
                        else
                        {
                            $pduct = new Product((int) $id_product, (int) $id_lang);
                        }
                        if (is_array($pduct->name))
                        {
                            $pduct->name = $pduct->name[$id_lang];
                        }

                        if(_s('CAT_PROD_IMG_DEFAULT_LEGEND')
                            && _s('CAT_PROD_IMG_DEFAULT_LEGEND') == 1
                            && $pduct->id_manufacturer){
                            $manufacturer = new Manufacturer($pduct->id_manufacturer, $id_lang);
                            $pduct->name .= ' '.$manufacturer->name;
                        }
                        $cache_product_name[$id_product] = $pduct->name;
                    }
                    if (!empty($cache_product_name[$id_product]))
                    {
                        $sql2 = 'UPDATE '._DB_PREFIX_."image_lang SET legend='".pSQL($cache_product_name[$id_product])."' WHERE id_image=".(int) $id_image.' AND id_lang='.(int) $id_lang;
                        Db::getInstance()->Execute($sql2);
                    }
                }
                break;
            case 'image_fill_legend_all_lang':
                $image = new Image((int) ($id_image));
                $sql = 'SELECT id_product FROM '._DB_PREFIX_."image WHERE id_image='".(int) $id_image."'";
                $id_product = Db::getInstance()->getValue($sql);
                if (!(empty($id_product)))
                {
                    if (empty($cache_product_name[$id_product]))
                    {
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $pduct = new Product((int) $id_product, false, null,
                                (int) SCI::getSelectedShop());
                        }
                        else
                        {
                            $pduct = new Product((int) $id_product);
                        }
                        $cache_product_name[$id_product] = $pduct->name;
                        if(_s('CAT_PROD_IMG_DEFAULT_LEGEND')
                            && _s('CAT_PROD_IMG_DEFAULT_LEGEND') == 1
                            && $pduct->id_manufacturer){
                            $manufacturer = new Manufacturer($pduct->id_manufacturer, $id_lang);
                            $pduct->name .= ' '.$manufacturer->name;
                        }
                    }
                    if (!empty($cache_product_name[$id_product]))
                    {
                        $sql = 'SELECT DISTINCT id_lang FROM '._DB_PREFIX_.'image_lang';
                        $res = Db::getInstance()->ExecuteS($sql);
                        foreach ($res as $language)
                        {
                            if (!empty($cache_product_name[$id_product][$language['id_lang']]))
                            {
                                $sql2 = 'UPDATE '._DB_PREFIX_."image_lang SET legend='".pSQL($cache_product_name[$id_product][$language['id_lang']])."' WHERE id_image=".(int) $id_image.' AND id_lang='.(int) $language['id_lang'];
                                Db::getInstance()->Execute($sql2);
                            }
                        }
                    }
                }
                break;
            case 'shop':
                $id_shop = Tools::getValue('shop', 0);
                $value = Tools::getValue('val', 0);
                $is_cover = Tools::getValue('is_cover', 0);
                if (!empty($id_shop))
                {
                    $image = new Image((int) ($id_image));
                    if (!$image->isAssociatedToShop($id_shop) && $value == '1')
                    {
                        if (version_compare(_PS_VERSION_, '1.6.1', '>='))
                        {
                            if (version_compare(_PS_VERSION_, '1.6.1.0', '>=') && empty($is_cover))
                            {
                                $is_cover = 'NULL';
                            }
                            else
                            {
                                $is_cover = "'".(int) $is_cover."'";
                            }
                            $id_prd = Db::getInstance()->getValue('SELECT `id_product` FROM `'._DB_PREFIX_.'image` WHERE `id_image` = '.(int) $id_image);
                            $sql = 'INSERT INTO '._DB_PREFIX_.'image_shop (id_shop,id_image, id_product, cover) VALUES ('.(int) $id_shop.','.(int) $id_image.','.(int) $id_prd.','.$is_cover.')';
                        }
                        else
                        {
                            $sql = 'INSERT INTO '._DB_PREFIX_.'image_shop (id_shop,id_image) VALUES ('.(int) $id_shop.','.(int) $id_image.')';
                        }
                        Db::getInstance()->Execute($sql);
                    }
                    elseif ($image->isAssociatedToShop($id_shop) && empty($value))
                    {
                        $sql = 'DELETE FROM `'._DB_PREFIX_.'image_shop` WHERE `id_image` = '.(int) $id_image.' AND id_shop = '.(int) $id_shop;
                        Db::getInstance()->Execute($sql);
                    }
                }
                sc_ext::readCustomImageGridConfigXML('onAfterUpdateSQL');
                break;
            case 'delete':
                    $image = new Image((int) $id_image);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $image->deleteProductAttributeImage();
                        $image->deleteImage();

                        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'image_lang` WHERE `id_image` = '.(int) $id_image);
                        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'image_shop` WHERE `id_image` = '.(int) $id_image);
                        Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'image` WHERE `id_image` = '.(int) $id_image);
                    }
                    else
                    {
                        $image->delete();
                        @unlink(_PS_TMP_IMG_DIR_.'product_'.(int) $id_product.'.jpg');
                        @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int) $id_product.'.jpg');
                    }

                    ## Vérification que le produit ait une image par défaut
                    $id_image_cover = Db::getInstance()->getValue('SELECT id_image
                                                                            FROM `'._DB_PREFIX_.'image`
                                                                            WHERE `id_product` = '.(int) $id_product.'
                                                                            AND `cover`= 1');
                    if (empty($id_image_cover))
                    {
                        $id_image_fist = Db::getInstance()->getValue('SELECT `id_image`
                                                                        FROM `'._DB_PREFIX_.'image`
                                                                        WHERE `id_product` = '.(int) $id_product.'
                                                                        ORDER BY position ASC');
                        Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'image`
                                                        SET `cover` = 1
                                                        WHERE `id_image` = '.(int) $id_image_fist);
                    }

                    ## Vérification que le produit ait une image par défaut pour chaque shop
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $shops = SCI::getSelectedShopActionList(false, (int) $id_product);
                        foreach ($shops as $shop_id)
                        {
                            $id_image_cover = Db::getInstance()->getValue('SELECT id_image
                                                                            FROM `'._DB_PREFIX_.'image_shop`
                                                                            WHERE `id_product` = '.(int) $id_product.'
                                                                            AND `cover`= 1
                                                                            AND id_shop = '.(int) $shop_id);
                            if (empty($id_image_cover))
                            {
                                $id_image_fist = Db::getInstance()->getValue('SELECT ishop.`id_image`
                                                                                FROM `'._DB_PREFIX_.'image_shop` ishop
                                                                                LEFT JOIN `'._DB_PREFIX_.'image` i
                                                                                ON i.`id_image` = ishop.`id_image`
                                                                                WHERE ishop.`id_product` = '.(int) $id_product.'
                                                                                AND ishop.`id_shop` = '.(int) $shop_id.'
                                                                                ORDER BY i.position ASC');
                                Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'image_shop`
                                                                SET `cover` = 1
                                                                WHERE `id_image` = '.(int) $id_image_fist.'
                                                                AND id_shop = '.(int) $shop_id);
                            }
                            $tmp_img_id = $id_product;
                            if(version_compare(_PS_VERSION_, '1.7.0.0', '>=')){
                                $tmp_img_id = $id_image;
                            }
                            @unlink(_PS_TMP_IMG_DIR_.'product_mini_'.(int) $tmp_img_id.'_'.(int) $shop_id.'.jpg');
                        }
                    }
                break;
            case 'position':
                if (!$multiple)
                {
                    $todo = array();
                    $row = explode(';', Tools::getValue('positions'));
                    $high_position = Image::getHighestPosition($id_product) + 1;
                    $todo[] = 'UPDATE '._DB_PREFIX_.'image SET position=(position+'.$high_position.') WHERE id_product='.(int) $id_product;
                    foreach ($row as $v)
                    {
                        if ($v != '')
                        {
                            $pos = explode(',', $v);
                            $todo[] = 'UPDATE '._DB_PREFIX_.'image SET position='.((int) $pos[1] + 1).' WHERE id_product='.(int) $id_product.' AND id_image='.(int) $pos[0];
                        }
                    }
                    foreach ($todo as $task)
                    {
                        Db::getInstance()->execute($task);
                    }
                }
                sc_ext::readCustomImageGridConfigXML('onAfterUpdateSQL');
                break;
            case 'thumbnail_regeneration':
                // Getting format generation
                $need_json_response = true;
                $formats = ImageType::getImagesTypes('products');
                $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');
                $image_obj = new Image($id_image);
                $existing_img = _PS_PROD_IMG_DIR_.$image_obj->getExistingImgPath().'.jpg';
                if (file_exists($existing_img) && filesize($existing_img))
                {
                    foreach ($formats as $imageType)
                    {
                        $image_format_path = _PS_PROD_IMG_DIR_.$image_obj->getExistingImgPath().'-'.stripslashes($imageType['name']).'.jpg';
                        $image_hdpi_format_path = _PS_PROD_IMG_DIR_.$image_obj->getExistingImgPath().'-'.stripslashes($imageType['name']).'2x.jpg';
                        if (!file_exists($image_format_path))
                        {
                            if (!ImageManager::resize($existing_img, $image_format_path, (int) $imageType['width'], (int) $imageType['height']))
                            {
                                $json_reponse[$id_image]['error'][] = _l('Original image is corrupt (%s) for product ID %s or bad permission on folder.', null, array($existing_img, (int) $image_obj->id_product));
                            }
                        }
                        if ($generate_hight_dpi_images && !file_exists($image_hdpi_format_path))
                        {
                            if (!ImageManager::resize($existing_img, $image_hdpi_format_path, (int) $imageType['width'] * 2, (int) $imageType['height'] * 2))
                            {
                                $json_reponse[$id_image]['error'][] = _l('Original image is corrupt (%s) for product ID %s or bad permission on folder.', null, array($existing_img, (int) $image_obj->id_product));
                            }
                        }
                    }
                }
                else
                {
                    $json_reponse[$id_image]['error'][] = _l('Original image is missing or empty (%s) for product ID %s', null, array($existing_img, (int) $image_obj->id_product));
                }
                $json_reponse[$id_image]['error'] = implode("\n", $json_reponse[$id_image]['error']);
                if (empty($json_reponse[$id_image]['error']))
                {
                    $json_reponse[$id_image]['success'] = 'OK';
                }
                // no break
            default:
                break;
        }
    }

    if ($need_json_response)
    {
        exit(json_encode($json_reponse));
    }

if (!empty($id_products))
{
    //update date_upd
    Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET date_upd = NOW() WHERE id_product IN ('.pInSQL(implode(',', $id_products)).')');
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_shop SET date_upd = NOW() WHERE id_product IN ('.pInSQL(implode(',', $id_products)).') AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
    }
    // PM Cache
    ExtensionPMCM::clearFromIdsProduct(implode(',', $id_products));
}
