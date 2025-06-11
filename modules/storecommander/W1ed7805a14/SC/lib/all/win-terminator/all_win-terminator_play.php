<?php

$id_lang = (int) Tools::getValue('id_lang');
$actions = (Tools::getValue('actions'));
$params = (Tools::getValue('params'));

require dirname(__FILE__).'/tools.php';
$terminatorTools = new terminatorTools();

if (!empty($actions))
{
    $actions = explode(',', $actions);

    foreach ($actions as $action)
    {
        if ($action == 'deloldcart')
        {
            $nbDays = (int) $params[$action];
            if (!empty($nbDays))
            {
                if (is_int($nbDays))
                {
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart` 
                                                        WHERE TO_DAYS(NOW()) - TO_DAYS(`date_upd`) > '.pSQL($nbDays).' 
                                                        AND `id_cart` NOT IN(SELECT DISTINCT `id_cart` 
                                                                                FROM `'._DB_PREFIX_.'orders`)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_product` 
                                                        WHERE `id_cart` NOT IN(SELECT DISTINCT `id_cart` 
                                                                                FROM `'._DB_PREFIX_.'cart`)');
                    Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'orders` o 
                                                        SET o.id_cart = 0 
                                                        WHERE o.id_cart NOT IN(SELECT DISTINCT c.id_cart 
                                                                                FROM '._DB_PREFIX_.'cart c)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'message` 
                                                        WHERE id_cart NOT IN(SELECT DISTINCT c.id_cart 
                                                                            FROM '._DB_PREFIX_.'cart c)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'message_readed` 
                                                        WHERE `id_message` NOT IN(SELECT DISTINCT `id_message` 
                                                                                    FROM `'._DB_PREFIX_.'message`)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price` 
                                                        WHERE id_cart > 0 AND id_cart NOT IN(SELECT DISTINCT c.id_cart 
                                                                                                FROM '._DB_PREFIX_.'cart c)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customization` 
                                                        WHERE id_cart NOT IN(SELECT DISTINCT c.id_cart 
                                                                                FROM '._DB_PREFIX_.'cart c)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customized_data` 
                                                        WHERE `id_customization` NOT IN(SELECT DISTINCT `id_customization` 
                                                                                        FROM `'._DB_PREFIX_.'customization`)');
                }
            }
        }
        if ($action == 'deloldupdatedproducts')
        {
            $var = $params[$action];
            if ($var != '')
            {
                $d = (int) Tools::substr($var, 8, 2);
                $m = (int) Tools::substr($var, 5, 2);
                $y = (int) Tools::substr($var, 0, 4);
                if (checkdate($m, $d, $y))
                {
                    $res = Db::getInstance()->ExecuteS('SELECT `id_product` 
                                                                FROM `'._DB_PREFIX_.'product` 
                                                                WHERE `date_upd`<\''.pSQL($var).'\'');
                    foreach ($res as $productId)
                    {
                        $p = new product((int) ($productId), true, (int) $id_lang);
                        $p->delete();
                    }
                }
            }
        }
        if ($action == 'deldiscountdate')
        {
            $nbDays = (int) $params[$action];
            if (!empty($nbDays))
            {
                if (is_int($nbDays))
                {
                    ## Specific prices
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price` 
                                                        WHERE `to` < (NOW() - INTERVAL '.pSQL($nbDays).' DAY)');

                    ## Cart Rules
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule` 
                                                            WHERE `date_to` < (NOW() - INTERVAL '.(int) $nbDays.' DAY)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_carrier` 
                                                            WHERE `id_cart_rule` NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'cart_rule`)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_country` 
                                                            WHERE `id_cart_rule` NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'cart_rule`)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` 
                                                            WHERE `id_cart_rule` NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'cart_rule`)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_lang` 
                                                            WHERE `id_cart_rule` NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'cart_rule`)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_shop` 
                                                            WHERE `id_cart_rule` NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'cart_rule`)');

                    ## Catalog Rules
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price_rule` 
                                                            WHERE `to` < (NOW() - INTERVAL '.(int) $nbDays.' DAY)
                                                            AND `to` > "0000-00-00 00:00:00" ');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price_rule_condition_group` 
                                                            WHERE `id_specific_price_rule` NOT IN (SELECT id_specific_price_rule FROM `'._DB_PREFIX_.'specific_price_rule`)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price_rule_condition` 
                                                            WHERE `id_specific_price_rule_condition_group` NOT IN (SELECT id_specific_price_rule_condition_group FROM `'._DB_PREFIX_.'specific_price_rule_condition_group`)');
                }
            }
        }
        if ($action == 'delupload')
        {
            $nbDays = (int) $params[$action];
            if (!empty($nbDays))
            {
                if (is_int($nbDays))
                {
                    $customFiles = Db::getInstance()->ExecuteS('SELECT pc.`id_cart`, pcd.`id_customization`, cusd.*
                                                            FROM `'._DB_PREFIX_.'cart` pc
                                                            RIGHT JOIN `'._DB_PREFIX_.'cart_product` pcd 
                                                            ON pcd.`id_cart` = pc.`id_cart` AND pcd.`id_customization` > 0
                                                            LEFT JOIN `'._DB_PREFIX_.'customized_data` cusd 
                                                            ON cusd.`id_customization` = pcd.`id_customization`
                                                            WHERE pc.`date_add` < (NOW() - INTERVAL '.pSQL($nbDays).' DAY)');

                    foreach ($customFiles as $cust_data)
                    {
                        if (isset($cust_data['type']) && $cust_data['type'] == 0)
                        {
                            unlink(_PS_UPLOAD_DIR_.$cust_data['value']);
                            unlink(_PS_UPLOAD_DIR_.$cust_data['value'].'_small');
                        }
                    }

                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customized_data`
                                                        WHERE `id_customization` IN (SELECT DISTINCT pcd.`id_customization`
                                                                                        FROM `'._DB_PREFIX_.'cart` pc
                                                                                        RIGHT JOIN `'._DB_PREFIX_.'cart_product` pcd 
                                                                                        ON pcd.`id_cart` = pc.`id_cart` 
                                                                                        AND pcd.`id_customization` > 0
                                                                                        WHERE pc.`date_add` < (NOW() - INTERVAL '.pSQL($nbDays).' DAY))');
                    Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cart_product` pcd 
                                                            JOIN `'._DB_PREFIX_.'cart` pc ON pc.`id_cart` = pcd.`id_cart`
                                                        SET pcd.`id_customization` = 0
                                                        WHERE pc.`date_add` < (NOW() - INTERVAL '.pSQL($nbDays).' DAY)');
                }
            }
        }
        if ($action == 'delsearch')
        {
            $terminatorTools->tr('search_index,search_word,sekeyword,statssearch');
        }
        if ($action == 'delpagenotfound')
        {
            $terminatorTools->tr('pagenotfound');
        }
        if ($action == 'dellog')
        {
            $numberToKeep = (int) $params[$action];
            $terminatorTools->trKeepLast('log', $numberToKeep);
        }

        // Database
        if ($action == 'delcart')
        {
            $terminatorTools->mainFunctions('deleteCarts');
        }
        if ($action == 'delcategoryandproduct')
        {
            $terminatorTools->trEx(
                'category,category_lang,category_group,category_shop',
                'id_category',
                '1,'.$terminatorTools->getRootCategories()
            );
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE `id_category` NOT IN(SELECT DISTINCT `id_category` FROM `'._DB_PREFIX_.'category`)');
            if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
            {
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'scene_category` WHERE `id_category` NOT IN(SELECT DISTINCT `id_category` FROM `'._DB_PREFIX_.'category`)');
            }
            $terminatorTools->deleteFilesInDirectory(_PS_CAT_IMG_DIR_, '[numeric]');
            $terminatorTools->mainFunctions('deleteProducts');
            $terminatorTools->mainFunctions('deleteCarts');
            $terminatorTools->mainFunctions('deleteProductComments');
        }
        if ($action == 'delorder')
        {
            $terminatorTools->mainFunctions('deleteOrders');
        }
        if ($action == 'delslip')
        {
            $terminatorTools->tr('order_slip,order_slip_detail,order_slip_detail_tax');
        }
        if ($action == 'delcustomer')
        {
            $terminatorTools->tr('customer,address,customer_group,customer_thread,customer_message,'.
                'customer_message_sync_imap');
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'employee` SET id_last_customer = 0');
            $terminatorTools->mainFunctions('deleteOrders');
            $terminatorTools->mainFunctions('deleteCarts');
        }
        if ($action == 'resetgroup')
        {
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'carrier_group` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'group` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'group_lang` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'group_reduction` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'group_shop` WHERE id_group > 3');
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'customer` 
                                                SET id_default_group = 1 
                                                WHERE id_default_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price_rule` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_group` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'module_group` WHERE id_group > 3');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_group_reduction_cache` 
                                                WHERE id_group > 3');
        }
        if ($action == 'delmessage')
        {
            $terminatorTools->tr('order_message,order_message_lang');
        }
        if ($action == 'delmanufacturer')
        {
            $terminatorTools->tr('manufacturer,manufacturer_lang,manufacturer_shop');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'address` WHERE id_manufacturer!=0');
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product` SET id_manufacturer=0');
            $terminatorTools->deleteFilesInDirectory(_PS_MANU_IMG_DIR_, '[numeric]');
        }
        if ($action == 'delsupplier')
        {
            $terminatorTools->tr('supplier,product_supplier,supplier_lang,supplier_shop,supply_order,supply_order_detail,'.
                'supply_order_history,supply_order_receipt_history');
            Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'address` WHERE id_supplier!=0');
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product` SET id_supplier=0');
            $terminatorTools->deleteFilesInDirectory(_PS_SUPP_IMG_DIR_, '[numeric]');
        }
        if ($action == 'delcarrier')
        {
            $terminatorTools->tr('carrier,carrier_lang,carrier_group,carrier_shop,carrier_tax_rules_group_shop,carrier_zone,'.
                'delivery,range_price,range_weight,cart_rule_carrier,product_carrier,warehouse_carrier');
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
            {
                $terminatorTools->tr('module_carrier');
            }
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cart` SET id_carrier=0');
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'orders` SET id_carrier=0');
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'order_carrier` SET id_carrier=0');
        }
        if ($action == 'delproduct')
        {
            $terminatorTools->mainFunctions('deleteProducts');
            $terminatorTools->mainFunctions('deleteProductComments');
            $terminatorTools->mainFunctions('deleteCarts');
        }
        if ($action == 'delcustomization')
        {
            $terminatorTools->tr('customization,customized_data,customization_field,customization_field_lang');
            Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product` 
                                                SET customizable=0,uploadable_files=0,text_fields=0');
            $terminatorTools->deleteFilesInDirectory(_PS_UPLOAD_DIR_);
        }
        if ($action == 'deldisabledproducts')
        {
            $res = Db::getInstance()->ExecuteS('SELECT `id_product` 
                                                        FROM `'._DB_PREFIX_.'product` 
                                                        WHERE `active`=0');
            foreach ($res as $line)
            {
                $p = new Product((int) ($line['id_product']), true, (int) ($id_lang));
                $p->delete();
            }
        }
        if ($action == 'delfeature')
        {
            $terminatorTools->tr('feature,feature_lang,feature_product,feature_shop,feature_value,feature_value_lang');
        }
        if ($action == 'delstockmvt')
        {
            $terminatorTools->tr('stock_mvt');
        }
        if ($action == 'delattributes')
        {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
            {
                Db::getInstance()->execute('SET FOREIGN_KEY_CHECKS = 0;');
                $terminatorTools->tr('attribute,attribute_lang,attribute_group,attribute_group_lang,attribute_group_shop,'.
                    'attribute_shop,'.(version_compare(_PS_VERSION_, '8.0.0', '<') ? 'attribute_impact,' : '').'product_attribute,product_attribute_shop,'.
                    'product_attribute_combination,product_attribute_image');
                Db::getInstance()->execute('SET FOREIGN_KEY_CHECKS = 1;');
            }
            else
            {
                $terminatorTools->tr('attribute,attribute_lang,attribute_group,attribute_group_lang,attribute_group_shop,'.
                    'attribute_shop,attribute_impact,product_attribute,product_attribute_shop,'.
                    'product_attribute_combination,product_attribute_image');
            }
        }
        if ($action == 'deltag')
        {
            $terminatorTools->tr('tag,product_tag,tag_count');
        }
        if ($action == 'deltextalias')
        {
            $terminatorTools->tr('alias');
        }
        if ($action == 'delscene')
        {
            $terminatorTools->tr('scene,scene_category,scene_lang,scene_products,scene_shop');
            $terminatorTools->deleteFilesInDirectory(_PS_SCENE_IMG_DIR_, '[numeric]');
            $terminatorTools->deleteFilesInDirectory(_PS_SCENE_THUMB_IMG_DIR_, '[numeric]');
        }
        if ($action == 'deldiscount')
        {
            $terminatorTools->tr('specific_price');
        }
        if ($action == 'delpageviewed')
        {
            $terminatorTools->tr('page_viewed,date_range');
        }
        if ($action == 'delconnections')
        {
            $nbDays = (int) $params[$action];
            if (!empty($nbDays))
            {
                if (is_int($nbDays))
                {
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'connections` 
                                                                WHERE `date_add` < (NOW() - INTERVAL '.pSQL($nbDays).' DAY)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'connections_page` 
                                                                WHERE `id_connections` NOT IN (SELECT DISTINCT `id_connections` 
                                                                                                FROM `'._DB_PREFIX_.'connections`)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'connections_source` 
                                                                WHERE `id_connections` NOT IN (SELECT DISTINCT `id_connections` 
                                                                                                FROM `'._DB_PREFIX_.'connections`)');
                    Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'guest` 
                                                                WHERE `id_guest` NOT IN (SELECT DISTINCT `id_guest` 
                                                                                            FROM `'._DB_PREFIX_.'connections`)');
                    Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cart`
                                                        SET `id_guest` = 0
                                                         WHERE `id_guest` NOT IN (SELECT DISTINCT `id_guest` 
                                                                                FROM `'._DB_PREFIX_.'guest`)');
                }
            }
        }
        if ($action == 'delreferrer' && version_compare(_PS_VERSION_, '8.0.0', '<'))
        {
            $terminatorTools->tr('referrer,referrer_cache,referrer_shop');
        }
        if ($action == 'resetrangeprice')
        {
            $terminatorTools->tr('range_price');
        }
        if ($action == 'resetrangeweight')
        {
            $terminatorTools->tr('range_weight');
        }
        if ($action == 'resetstore')
        {
            if (version_compare(_PS_VERSION_, '1.7.3.0', '>='))
            {
                $terminatorTools->tr('store,store_shop,store_lang');
            }
            else
            {
                $terminatorTools->tr('store,store_shop');
            }
            $terminatorTools->deleteFilesInDirectory(_PS_STORE_IMG_DIR_);
        }

        // Modules
        if ($action == 'delcomment')
        {
            $terminatorTools->mainFunctions('deleteProductComments');
        }
        if ($action == 'delwishlist')
        {
            $terminatorTools->tr('wishlist,wishlist_email,wishlist_product,wishlist_product_cart');
        }
        if ($action == 'delloyalty')
        {
            $terminatorTools->tr('loyalty,loyalty_history');
        }
        if ($action == 'delemailsubscription')
        {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
            {
                $terminatorTools->tr('emailsubscription');
            }
            else
            {
                $terminatorTools->tr('newsletter');
            }
        }
        if ($action == 'delbestsellers')
        {
            $terminatorTools->tr('product_sale');
        }

        // Other
        if ($action == 'invoicenum')
        {
            $invoicenum = $params[$action];
            if (!empty($invoicenum) && is_numeric($invoicenum))
            {
                Configuration::updateValue('PS_INVOICE_START_NUMBER', $invoicenum);
            }
        }
        if ($action == 'ordernum')
        {
            $ordernum = $params[$action];
            echo $ordernum;
            if (!empty($ordernum) && is_numeric($ordernum) && $ordernum >= $terminatorTools->getNextID('orders', 'id_order'))
            {
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'orders` 
                                                    AUTO_INCREMENT ='.(int) $ordernum);
            }
        }
        if ($action == 'clientnum')
        {
            $clientnum = $params[$action];
            if (!empty($clientnum) && is_numeric($clientnum) && $clientnum >= $terminatorTools->getNextID('customer', 'id_customer'))
            {
                Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'customer` 
                                                    AUTO_INCREMENT ='.(int) $clientnum);
            }
        }
        if ($action == 'optimize')
        {
            $tabs = Db::getInstance()->ExecuteS('SHOW TABLES');
            $query = array();
            foreach ($tabs as $val)
            {
                $query[] = '`'.bqSQL($val['Tables_in_'._DB_NAME_]).'`';
            }
            Db::getInstance()->Execute('OPTIMIZE TABLE '.join(',', $query));
        }
        // Files
        if ($action == 'delsmartycache')
        {
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
            {
                $terminatorTools->dirEmpty(
                    _PS_CACHE_DIR_.'smarty/cache/',
                    _PS_CACHE_DIR_.'smarty/cache/',
                    array('index.php')
                );
                $terminatorTools->dirEmpty(
                    _PS_CACHE_DIR_.'smarty/compile/',
                    _PS_CACHE_DIR_.'smarty/compile/',
                    array('index.php')
                );

                $future_cache_dir = _PS_ROOT_DIR_.'/var/cache_new';
                $current_cache_dir = _PS_ROOT_DIR_.'/var/cache';
                $old_cache_dir = _PS_ROOT_DIR_.'/var/cache_old';

                ## 1 - Création futur dossier de cache
                $terminatorTools->dirEmpty($future_cache_dir, '');
                mkdir($future_cache_dir);
                if (file_exists($future_cache_dir))
                {
                    ## 2 - création dossier old + renommage du dossier de cache actuel en old
                    $terminatorTools->dirEmpty($old_cache_dir, '');
                    mkdir($old_cache_dir);
                    if (file_exists($old_cache_dir))
                    {
                        rename($current_cache_dir, $old_cache_dir);
                        ## 3 - futur dossier de cache devient dossier de cache actuel
                        $terminatorTools->dirEmpty($current_cache_dir, $current_cache_dir);
                        rename($future_cache_dir, $current_cache_dir);
                        if (function_exists('exec'))
                        {
                            exec('rm -rf '.$old_cache_dir);
                        }
                    }
                }
            }
            elseif (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
            {
                $terminatorTools->dirEmpty(
                    _PS_CACHE_DIR_.'smarty/cache/',
                    _PS_CACHE_DIR_.'smarty/cache/',
                    array('index.php')
                );
                $terminatorTools->dirEmpty(
                    _PS_CACHE_DIR_.'smarty/compile/',
                    _PS_CACHE_DIR_.'smarty/compile/',
                    array('index.php')
                );
            }
            else
            {
                $terminatorTools->deleteFilesInDirectory(_PS_CACHE_DIR_.'smarty/cache/');
                $terminatorTools->deleteFilesInDirectory(_PS_CACHE_DIR_.'smarty/compile/');
            }
        }
        if ($action == 'deltmpimg')
        {
            if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
            {
                Image::clearTmpDir();
            }
            else
            {
                $terminatorTools->deleteFilesInDirectory(_PS_TMP_IMG_DIR_);
            }
        }
        if ($action == 'delcachefs')
        {
            $terminatorTools->deleteFilesInDirectory(_PS_CACHE_DIR_.'cachefs/');
        }
        if ($action == 'delcachetcpdf')
        {
            $terminatorTools->deleteFilesInDirectory(_PS_CACHE_DIR_.'tcpdf/');
        }
        if ($action == 'emptypsimportfolder')
        {
            $terminatorTools->deleteFilesInDirectory(SC_PS_PATH_ADMIN_DIR.'import/');
        }
        if ($action == 'resetcustomerspwd')
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'customer SET passwd = ""');
        }
        if ($action == 'resetemployeespwd')
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'employee SET passwd = ""');
        }
    }
}
