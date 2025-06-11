<?php

class terminatorTools
{
    private $defaultfiles = array();
    private $dbtables = array();

    public function __construct()
    {
        $this->defaultfiles = array('index.php', '.htaccess');
        if (count($this->dbtables) == 0)
        {
            $res = array_values(Db::getInstance()->ExecuteS('SHOW TABLES'));
            foreach ($res as $row)
            {
                foreach ($row as $v)
                {
                    $this->dbtables[] = $v;
                }
            }
        }
    }

    // get the number of active elements of a table
    public function g($table, $customSQL = '')
    {
        $query = array();
        if ($customSQL != '')
        {
            $query[] = $customSQL;
        }
        $res = Db::getInstance()->ExecuteS('SHOW TABLES LIKE "'._DB_PREFIX_.$table.'"');
        if (count($res))
        {
            $checkdeleted = Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'.bqSQL(_DB_PREFIX_.$table).'` LIKE \'deleted\'');
            if (count($checkdeleted))
            {
                $query[] = 'deleted=0';
            }
            $res = Db::getInstance()->ExecuteS('SELECT COUNT(*) AS nb 
                                                FROM `'.bqSQL(_DB_PREFIX_.$table).'`
                                                '.(count($query) > 0 ? ' WHERE '.join(' AND ', $query) : ''));

            return $res[0]['nb'];
        }
        else
        {
            return '0';
        }
    }

    public function deleteFilesInDirectory($dirname, $mask = '', $recursive = false)
    {
        $files = scandir($dirname);

        foreach ($files as $file)
        {
            if ($file != '.' && $file != '..')
            {
                if (!is_dir($dirname.$file) && (file_exists($dirname.$file)))
                {
                    if ($mask != '')
                    {
                        if ((strpos($file, $mask) !== false
                                || ($mask == '[numeric]' && is_numeric(Tools::substr($file, 0, 1))))
                            && !in_array($file, $this->defaultfiles)
                        ) {
                            unlink($dirname.$file);
                        }
                    }
                    else
                    {
                        if (!in_array($file, $this->defaultfiles))
                        {
                            unlink($dirname.$file);
                        }
                    }
                }
                if ($recursive && is_dir($dirname.$file))
                {
                    $this->deleteFilesInDirectory($dirname.$file, $mask, $recursive);
                }
            }
        }
    }

    // functions used several times in the code

    public function dirEmpty($dir, $dirOrigin, $exceptions = array())
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);
            foreach ($objects as $object)
            {
                if ($object != '.' && $object != '..' && !in_array($object, $exceptions))
                {
                    if (filetype($dir.'/'.$object) == 'dir')
                    {
                        $this->dirEmpty($dir.'/'.$object, $dirOrigin, $exceptions);
                    }
                    else
                    {
                        @unlink($dir.'/'.$object);
                    }
                }
            }
            reset($objects);
            if ($dir != $dirOrigin)
            {
                rmdir($dir);
            }
        }
    }

    public function tr($tables)
    {
        $tables = explode(',', $tables);
        foreach ($tables as $table)
        {
            if (in_array(_DB_PREFIX_.trim($table), $this->dbtables))
            {
                Db::getInstance()->Execute('TRUNCATE `'.bqSQL(_DB_PREFIX_.trim($table)).'`');
            }
        }
    }

    public function trKeepLast($tables, $numberToKeep)
    {
        $tables = explode(',', $tables);
        foreach ($tables as $table)
        {
            if (in_array(_DB_PREFIX_.trim($table), $this->dbtables))
            {
                // subselect avec LIMIT non pris en charge par certaines versions de mysql
                $ids_to_keep = Db::getInstance()->ExecuteS('SELECT id_'.trim($table).'
                 FROM `'.bqSQL(_DB_PREFIX_.trim($table)).'`
                 ORDER BY id_'.trim($table).' DESC
                 LIMIT '.$numberToKeep.'
                 ');
                $ids_to_keep = array_map(function ($e)
                {
                    return $e['id_log'];
                }, $ids_to_keep);
                $ids_to_keep = implode(',', array_values($ids_to_keep));
                $sql = 'DELETE FROM `'.bqSQL(_DB_PREFIX_.trim($table)).'` WHERE `id_'.bqSQL(trim($table)).'` NOT IN ('.pInSQL($ids_to_keep).')';
                Db::getInstance()->Execute($sql);
            }
        }
    }

    public function trEx($tables, $key_field, $values_to_keep)
    {
        $tables = explode(',', $tables);
        foreach ($tables as $table)
        {
            if (in_array(_DB_PREFIX_.trim($table), $this->dbtables))
            {
                Db::getInstance()->Execute('DELETE FROM `'.bqSQL(_DB_PREFIX_.trim($table)).'`
                                            WHERE `'.bqSQL($key_field).'` NOT IN ('.pInSQL($values_to_keep).')');
            }
        }
    }

    public function getRootCategories()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT GROUP_CONCAT(c.`id_category`)
                                                                FROM `'._DB_PREFIX_.'category` c
                                                                WHERE `is_root_category` = 1', false);
    }

    // truncate tables

    public function getNextID($table, $col)
    {
        $row = Db::getInstance()->getRow('SELECT MAX('.pSQL($col).')+1 AS AUTO_INCREMENT 
                                            FROM `'.bqSQL(_DB_PREFIX_.$table).'`');

        return $row['AUTO_INCREMENT'];
    }

    // truncate tables

    public function getAutoIncrement($table)
    {
        $row = Db::getInstance()->Executes('SHOW TABLE STATUS FROM `'._DB_NAME_.'` WHERE `name` LIKE "'._DB_PREFIX_.$table.'"');

        return $row[0]['Auto_increment'];
    }

    // truncate tables with exceptions

    public function getDirectorySize($path, $data = null)
    {
        if (function_exists('exec'))
        {
            $command = 'du -s '.$path;
            $output = exec($command);
            if (!empty($output))
            {
                $output = explode($path, $output);

                return trim($output[0] * 1024);
            }
        }
        $totalsize = 0;
        $totalcount = 0;
        $dircount = 0;
        if ($handle = opendir($path))
        {
            while (false !== ($file = readdir($handle)))
            {
                $nextpath = rtrim($path, '/').'/'.$file;
                if ($file != '.' && $file != '..' && !is_link($nextpath))
                {
                    if (is_dir($nextpath))
                    {
                        ++$dircount;
                        $result = $this->getDirectorySize($nextpath);
                        $totalsize += $result['size'];
                        $totalcount += $result['count'];
                        $dircount += $result['dircount'];
                    }
                    elseif (is_file($nextpath) && $file != 'index.php')
                    {
                        $totalsize += filesize($nextpath);
                        ++$totalcount;
                    }
                }
            }
        }
        @closedir($handle);
        $total = array();
        $total['size'] = $totalsize;
        $total['count'] = $totalcount;
        $total['dircount'] = $dircount;
        if ($data == null)
        {
            return $total;
        }

        return $total['size'];
    }

    public function sizeFormat($size)
    {
        if ($size < -1024)
        {
            return $size.' octets';
        }
        else
        {
            if ($size < (1024 * 1024))
            {
                $size = round($size / 1024, 1);

                return $size.' Ko';
            }
            else
            {
                if ($size < (1024 * 1024 * 1024))
                {
                    $size = round($size / (1024 * 1024), 1);

                    return $size.' Mo';
                }
                else
                {
                    $size = round($size / (1024 * 1024 * 1024), 1);

                    return $size.' Go';
                }
            }
        }
    }

    public function mainFunctions($task = '')
    {
        switch ($task) {
            case 'deleteProducts_info':
                return 'product ; accessory ; '.(version_compare(_PS_VERSION_, '8.0.0', '<') ? 'attribute_impact ; ' : '').'cart_product ; feature_product ; image ;'.
                    'customization ; customization_field ; product_supplier ; product_tag ; scene_products ; '.
                    'search_index ; specific_price ; specific_price_priority ; category_product ; compare_product ; pack ;'.
                    'product_attachement ; product_attribute ; product_carrier ; product_country_tax ; product_download ; '.
                    'product_group_reduction_cache ; product_sale ; product_shop ; stock ; stock_available ; '.
                    'supply_order_detail ; wharehouse_product_location ; product_lang ; attachement ; attachement_lang ; '.
                    'image_lang ; image_shop ; product_attribute_combination ; product_attribute_image ; stock_mvt ;'.
                    'customer_thread';
            case 'deleteProducts':
                // main references
                $this->tr('product,accessory,attribute_impact,cart_product,feature_product,image,customization,'.
                    'customization_field,product_supplier,product_tag,scene_products,search_index,specific_price,'.
                    'specific_price_priority,category_product,compare_product,pack,product_attachement,'.
                    'product_attribute,product_attribute_shop,product_carrier,product_country_tax,product_download,'.
                    'product_group_reduction_cache,product_sale,product_shop,stock,stock_available,'.
                    'supply_order_detail,wharehouse_product_location');
                // reference objects
                $this->tr('product_lang,attachement,attachement_lang,image_lang,image_shop,'.
                    'product_attribute_combination,product_attribute_image,stock_mvt');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customer_thread` 
                                            WHERE `id_product` NOT IN(SELECT DISTINCT `id_product` 
                                                                        FROM `'._DB_PREFIX_.'product`)');

                if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
                {
                    Image::deleteAllImages(_PS_PROD_IMG_DIR_);
                    if (!file_exists(_PS_PROD_IMG_DIR_))
                    {
                        mkdir(_PS_PROD_IMG_DIR_);
                    }
                }
                else
                {
                    $this->deleteFilesInDirectory(_PS_PROD_IMG_DIR_, '[numeric]', true);
                }

                $this->deleteFilesInDirectory(_PS_TMP_IMG_DIR_, 'product');
                $this->deleteFilesInDirectory(_PS_DOWNLOAD_DIR_);
                break;
            case 'deleteCarts_info':
                return 'cart ; cart_product ; cart_cart_rule ; orders ; message ; message_readed ; specific_price ; '.
                    'customization ; customized_data';
            case 'deleteCarts':
                $this->tr('cart,cart_product,cart_cart_rule');
                Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'orders` SET id_cart = 0');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'message` WHERE id_cart > 0');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'message_readed` 
                                            WHERE `id_message` NOT IN(SELECT DISTINCT `id_message` 
                                                                        FROM `'._DB_PREFIX_.'message`)');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE id_cart > 0');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customization` WHERE id_cart > 0');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customized_data` 
                                            WHERE `id_customization` NOT IN(SELECT DISTINCT `id_customization` 
                                                                            FROM `'._DB_PREFIX_.'customization`)');
                break;
            case 'deleteProductComments_info':
                return 'product_comment ; product_comment_grade ; product_comment_report ; product_comment_usefulness';
            case 'dneleteProductComments':
                $this->tr('product_comment,product_comment_grade,product_comment_report,product_comment_usefulness');
                break;
            case 'deleteOrders_info':
                return 'orders ; order_carrier ; order_cart_rule ; order_history ; order_invoice ; order_return ; '.
                    'order_slip ; order_detail ; order_detail_tax ; '.
                    'order_invoice_payment ; order_invoice_tax ; order_return_detail ; order_slip_detail ; message ; '.
                    'customer_thread ; employee ; stock_mvt';
            case 'deleteOrders':
                $this->tr('orders,order_carrier,order_cart_rule,order_history,order_invoice,order_payment,'.
                    'order_return,order_slip');
                $this->tr('order_detail,order_detail_tax,order_invoice_payment,order_invoice_tax,order_return_detail,'.
                    'order_slip_detail,order_slip_detail_tax');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'message` WHERE `id_order` > 0');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customer_thread` 
                                            WHERE `id_order` NOT IN(SELECT DISTINCT `id_order` 
                                                                    FROM `'._DB_PREFIX_.'orders`)');
                Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'employee` SET id_last_order = 0');
                Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'stock_mvt` WHERE `id_order` > 0');
                $this->mainFunctions('deleteCarts');
                break;
        }
    }
}
