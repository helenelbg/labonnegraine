<?php
        include_once '../config/config.inc.php';
        include_once '../config/settings.inc.php';
        include_once '../init.php';

    if (($open = fopen("plants.csv", "r")) !== false) {
        while (($data = fgetcsv($open, 1000, ",")) !== false) {
            $array[] = $data;
        }
     
        fclose($open);
    }
    unset($array[0]);
    echo "<pre>";
    print_r($array);
    echo "</pre>";
    foreach($array as $prod)
    {
        $errors = array();

        if (Validate::isLoadedObject($product = new Product($prod[0]))) {
            $id_product_old = $product->id;
            if (empty($product->price) && Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shops = ShopGroup::getShopsFromGroup(Shop::getContextShopGroupID());
                foreach ($shops as $shop) {
                    if ($product->isAssociatedToShop($shop['id_shop'])) {
                        $product_price = new Product($id_product_old, false, null, $shop['id_shop']);
                        $product->price = $product_price->price;
                    }
                }
            }
            unset(
                $product->id,
                $product->id_product
            );

            $product->indexed = false;
            $product->active = false;
            $product->upc = '00'.$prod[1];
            $product->reference = $prod[2];
            
            $product->id_supplier = $prod[7];
            $product->wholesale_price = str_replace('.', ',', $prod[8]);
            $product->id_category_default = $prod[3];

            foreach ($product->name as $langKey => $oldName) {
                $product->name[$langKey] = $prod[4];
                $product->meta_title[$langKey] = $prod[12];
            }

            if ($product->add()
            //&& Category::duplicateProductCategories($id_product_old, $product->id)
            && Product::duplicateSuppliers($id_product_old, $product->id)
            //&& ($combination_images = Product::duplicateAttributes($id_product_old, $product->id)) !== false
            && GroupReduction::duplicateReduction($id_product_old, $product->id)
            && Product::duplicateAccessories($id_product_old, $product->id)
            && Product::duplicateFeatures($id_product_old, $product->id)
            && Product::duplicateSpecificPrices($id_product_old, $product->id)
            && Pack::duplicate($id_product_old, $product->id)
            && Product::duplicateCustomizationFields($id_product_old, $product->id)
            && Product::duplicateTags($id_product_old, $product->id)
            && Product::duplicateDownload($id_product_old, $product->id)) {

                $combination_images = array();

                $id_product_attribute = $product->addAttribute(
                    str_replace('.', ',', ($prod[9]/1.055)),
                    $prod[10],
                    0,
                    0,
                    0,
                    '',
                    $prod[11],
                    1,
                    '',
                    '',
                    1,
                    array(1),
                    '',
                    0,
                    '',
                    '',
                    '',
                    ''
                );

                $reqc = 'INSERT INTO `ps_product_attribute_combination` SET id_product_attribute = "'.$id_product_attribute.'", id_attribute = "'.$prod[5].'";';
                $rangeec = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqc);
                $reqc2 = 'INSERT INTO `ps_product_attribute_combination` SET id_product_attribute = "'.$id_product_attribute.'", id_attribute = "'.$prod[6].'";';
                $rangeec2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqc2);

                if ($product->hasAttributes()) {
                    Product::updateDefaultAttribute($product->id);
                }

                if (!Tools::getValue('noimage') && !Image::duplicateProductImages($id_product_old, $product->id, $combination_images)) {
                    $errors[] = $this->trans('An error occurred while copying the image.', [], 'Admin.Notifications.Error');
                } else {
                    Hook::exec('actionProductAdd', ['id_product_old' => $id_product_old, 'id_product' => (int) $product->id, 'product' => $product]);
                    if (in_array($product->visibility, ['both', 'search']) && Configuration::get('PS_SEARCH_INDEXATION')) {
                        Search::indexation(false, $product->id);
                    }
                }
            } else {
                $errors[] = $this->trans('An error occurred while creating an object.', [], 'Admin.Notifications.Error');
            }

            $req = 'INSERT INTO `ps_category_product` SET id_product = "'.$product->id.'", id_category = "'.$prod[3].'";';
            $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
            $req = 'INSERT INTO `ps_category_product` SET id_product = "'.$product->id.'", id_category = "344";';
            $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
            $req = 'INSERT INTO `ps_category_product` SET id_product = "'.$product->id.'", id_category = "345";';
            $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
            $req = 'INSERT INTO `ps_category_product` SET id_product = "'.$product->id.'", id_category = "5";';
            $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
            $req = 'INSERT INTO `ps_category_product` SET id_product = "'.$product->id.'", id_category = "9";';
            $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
            $req = 'INSERT INTO `ps_category_product` SET id_product = "'.$product->id.'", id_category = "10";';
            $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
            $req = 'INSERT INTO `ps_category_product` SET id_product = "'.$product->id.'", id_category = "11";';
            $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);

            $reqd = 'DELETE FROM `ps_product_supplier` WHERE id_product = "'.$product->id.'";';
            $rangeed = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqd);
            
            $reqd2 = 'DELETE FROM `ps_feature_product` WHERE id_product = "'.$product->id.'" AND id_feature = 17;';
            $rangeed2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqd2);

            $reqd3 = 'DELETE FROM `ps_feature_product` WHERE id_product = "'.$product->id.'" AND id_feature = 27;';
            $rangeed3 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqd3);

            $reqs1 = 'INSERT INTO `ps_feature_product` SET id_feature = 27, id_product = "'.$product->id.'", id_feature_value = "2428";';
            $rangees1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqs1);
            $reqs2 = 'INSERT INTO `ps_feature_product` SET id_feature = 27, id_product = "'.$product->id.'", id_feature_value = "2429";';
            $rangees2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqs2);
            $reqs3 = 'INSERT INTO `ps_feature_product` SET id_feature = 27, id_product = "'.$product->id.'", id_feature_value = "2430";';
            $rangees3 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqs3);

            $requp = 'UPDATE `ps_product_lang` SET meta_keywords = REPLACE(meta_keywords, "graines,", "") WHERE id_product = "'.$product->id.'";';
            $rangeeup = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($requp);

            $reqdt = 'DELETE FROM `ps_product_tag` WHERE id_product = "'.$product->id.'" AND id_tag = 32;';
            $rangeedt = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqdt);

            $reqi = 'INSERT INTO `ps_product_supplier` SET id_product = "'.$product->id.'", id_supplier = "'.$prod[7].'";';
            $rangeei = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqi);  
            
            $reqdt = 'DELETE FROM `ps_product_carrier` WHERE id_product = "'.$product->id.'";';
            $rangeedt = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqdt);

            $reqt = 'INSERT INTO `ps_product_carrier` SET id_product = "'.$product->id.'", id_carrier_reference = "155", id_shop = 1;';
            $rangeet = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqt); 
            $reqt = 'INSERT INTO `ps_product_carrier` SET id_product = "'.$product->id.'", id_carrier_reference = "189", id_shop = 1;';
            $rangeet = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqt); 
            $reqt = 'INSERT INTO `ps_product_carrier` SET id_product = "'.$product->id.'", id_carrier_reference = "192", id_shop = 1;';
            $rangeet = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqt); 
            $reqt = 'INSERT INTO `ps_product_carrier` SET id_product = "'.$product->id.'", id_carrier_reference = "193", id_shop = 1;';
            $rangeet = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqt); 
            $reqt = 'INSERT INTO `ps_product_carrier` SET id_product = "'.$product->id.'", id_carrier_reference = "348", id_shop = 1;';
            $rangeet = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqt); 
            $reqt = 'INSERT INTO `ps_product_carrier` SET id_product = "'.$product->id.'", id_carrier_reference = "390", id_shop = 1;';
            $rangeet = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($reqt); 
        }
        echo 'Error : <pre>';
        print_r($errors);
        echo '</pre>';

        die;
    }
?>