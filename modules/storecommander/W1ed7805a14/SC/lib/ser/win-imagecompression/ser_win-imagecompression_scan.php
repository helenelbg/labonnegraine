<?php

$start = Tools::getValue('start', 0);
$time_start = $time_end = microtime(true);
$error = array();
require_once dirname(__FILE__).'/ser_win-imagecompression_tools.php';

if ($start)
{
    Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'storecom_imagefile_TEMP');
    $temp_table_creaded = Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'storecom_imagefile_TEMP (`size_origin` INT(11) DEFAULT 0,`path` VARCHAR(255) NOT NULL,`name` VARCHAR(255) NOT NULL,`priority` INT(11) DEFAULT 0,`chmod` INT(11) DEFAULT 0,INDEX(`path`))');
    if ($temp_table_creaded)
    {
        ## analyse des données
        ## products
        insertTempImageFileInfos('p', $analysis_params['path']['p']);
        ## categories
        insertTempImageFileInfos('c', $analysis_params['path']['c']);
        ## cms
        insertTempImageFileInfos('cms', $analysis_params['path']['cms']);
        ## manufacturer
        insertTempImageFileInfos('m', $analysis_params['path']['m']);
        ## supplier
        insertTempImageFileInfos('su', $analysis_params['path']['su']);
        ## scenes
        insertTempImageFileInfos('scenes', $analysis_params['path']['scenes']);
        ## scenes thumb
        insertTempImageFileInfos('scenes_thumbs', $analysis_params['path']['scenes_thumbs']);
        ## themes
        insertTempImageFileInfos('themes', $analysis_params['path']['themes']);

        ## Maj si image modifie la renvoyer
        ## selon tmp.size_origin != size compressed de la ligne
        $sql = 'UPDATE '._DB_PREFIX_.'storecom_imagefile sc_img, '._DB_PREFIX_.'storecom_imagefile_TEMP sitmp
                SET sc_img.`status` = '.(int) $compression_status['untreated'].',
                    sc_img.`size_origin` = sitmp.`size_origin`,
                    sc_img.`count_compression_request` = 0,
                    sc_img.`size_compressed` = 0,
                    sc_img.`size_saved` = 0
                WHERE sc_img.`path` = sitmp.`path`
                AND sitmp.`size_origin` != sc_img.`size_compressed`
                AND sc_img.`status` != '.(int) $compression_status['ignored'];
        $update_updated_files = Db::getInstance()->Execute($sql);
        if (!$update_updated_files)
        {
            $error[] = _l('Unable to update data to final table. Please contact our support.');
            $error[] = 'MysqlError#001 '.Db::getInstance()->getMsgError();
        }

        ## copie des données table temp => table finale (quand path n'existe pas dans final)
        $sql = 'INSERT IGNORE INTO '._DB_PREFIX_.'storecom_imagefile (`size_origin`,`path`,`name`,`priority`)
                SELECT `size_origin`,`path`,`name`,`priority`
                FROM '._DB_PREFIX_.'storecom_imagefile_TEMP sitmp';
        $final_values_added = Db::getInstance()->Execute($sql);
        if (!$final_values_added)
        {
            $error[] = _l('Unable to add data to final table. Please contact our support.');
            $error[] = 'MysqlError#002 '.Db::getInstance()->getMsgError();
        }

        ## update des données depuis table temp.
        $sql = 'UPDATE '._DB_PREFIX_.'storecom_imagefile sc_img, '._DB_PREFIX_.'storecom_imagefile_TEMP sitmp
                SET sc_img.`date_last_scan` = "'.pSQL(date('Y-m-d H:i:s')).'", sc_img.`priority` = sitmp.`priority`, sc_img.`chmod` = sitmp.`chmod`
                WHERE sc_img.path = sitmp.path';
        $update_date = Db::getInstance()->Execute($sql);
        if (!$update_date)
        {
            $error[] = _l('Unable to update data to final table. Please contact our support.');
            $error[] = 'MysqlError#003 '.Db::getInstance()->getMsgError();
        }
        Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'storecom_imagefile_TEMP');

        ## suppression des lignes dont le fichier n'existe plus
        ## date_last_scan écrasé par les données du jour. Donc si date_last_scan plus ancien que le précédent scan = on supprime
        $stats = SCI::getConfigurationValue('SC_IMAGECOMPRESSION_STATS');
        if (!empty($stats))
        {
            $stats = json_decode($stats, true);
            $dtime = new DateTime($stats['last_scan']);
            $date_last_scan_minus_five_days = $dtime->modify('-1 hour');
            $date_delete_row_from = $date_last_scan_minus_five_days->format('Y-m-d H:i:s');
            if (!empty($stats))
            {
                Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'storecom_imagefile
                                                    WHERE `date_last_scan` < "'.pSQL($date_delete_row_from).'"');
            }
        }

        ## test des chmod pour éviter d'envoyer des images non writable
        $chmod_files = Db::getInstance()->executeS('SELECT chmod,path 
                                                        FROM `'._DB_PREFIX_.'storecom_imagefile` 
                                                        WHERE status != '.(int)$compression_status['not_writable'].' 
                                                        GROUP BY chmod');
        if (!empty($chmod_files))
        {
            ## on remet status à 0 dans le cas où les chmods auraient été modifiés
            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'storecom_imagefile
                                            SET status=0
                                            WHERE status='.(int) $compression_status['not_writable']);
            foreach ($chmod_files as $row)
            {
                $tmp_path = rtrim(_PS_ROOT_DIR_, '\/').$row['path'];
                $tmp = explode('/', $row['path']);
                array_pop($tmp);
                $tmp = implode('/', $tmp);
                ## si on peut pas écrire sur le fichier, on l'enlève du compression_init
                if (!is_writable($tmp_path))
                {
                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'storecom_imagefile
                                                    SET status='.(int) $compression_status['not_writable'].'
                                                    WHERE chmod='.(int) $row['chmod']);
                    $error[] = _l('Folder %s contain some file not writable (%s). Please fix them before scan again.', false, array($tmp, $row['chmod']));
                }
                else
                {
                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'storecom_imagefile
                                                    SET status='.(int) $compression_status['untreated'].'
                                                    WHERE chmod='.(int) $row['chmod'].'
                                                    AND status = '.(int) $compression_status['not_writable']);
                }
            }
        }

        ## gestion des priorités sur images catalogue p,c etc...
        ## images de base
        setImageFilePriority('image_base');
        ## images officielles
        setImageFilePriority('official');
        ## autre format
        setImageFilePriority('other');
        ## images obsoletes
        setImageFilePriority('obsolete');
        ## news
        setImageFilePriority('news');
        ## bestseller
        setImageFilePriority('bestseller');
        ## homepage
        setImageFilePriority('homepage');
    }
    else
    {
        $error[] = _l('Unable to create temporary table. You need to contact your administrator.');
    }
    ## recalcul gain pour les images compressées
    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'storecom_imagefile
                                    SET size_saved = IF(size_compressed > 0,size_origin-size_compressed,0)
                                    WHERE status='.(int) $compression_status['compressed']);
    $total = Db::getInstance()->getRow('SELECT COUNT(id_storecom_imagefile) as total_count,
                                                SUM(`size_origin`) as total_size,
                                                SUM(`size_compressed`) as total_size_compressed,
                                                SUM(`size_saved`) as total_size_gain
                                            FROM '._DB_PREFIX_.'storecom_imagefile
                                            WHERE `image_base` = 0
                                            AND `priority` NOT IN ('.(int) $analysis_params['priorities']['other'].','.
        (int) $analysis_params['priorities']['obsolete'].')');
    $compressed = Db::getInstance()->getRow('SELECT COUNT(id_storecom_imagefile) as total_count_compressed, SUM(`size_origin`) as total_origin_size_from_compressed_image, SUM(`size_saved`) as total_size_gain_from_compressed_image
                                                                    FROM '._DB_PREFIX_.'storecom_imagefile
                                                                    WHERE status='.(int) $compression_status['compressed'].'
                                                                    AND size_saved > 0');
    $total_count_compressed = (int) $compressed['total_count_compressed'];
    $total_origin_size_from_compressed_image = (int) $compressed['total_origin_size_from_compressed_image'];
    $total_size_base = Db::getInstance()->getValue('SELECT SUM(`size_origin`)
                                                                    FROM '._DB_PREFIX_.'storecom_imagefile
                                                                    WHERE image_base = 1');
    $total_size_ps_format = Db::getInstance()->getValue('SELECT SUM(`size_origin`)
                                                                    FROM '._DB_PREFIX_.'storecom_imagefile
                                                                    WHERE priority ='.(int) $analysis_params['priorities']['official']);
    $obsolete_images = Db::getInstance()->getRow('SELECT COUNT(id_storecom_imagefile) as total_count_obsolete,
                                                            SUM(`size_origin`) as total_size_obsolete
                                                            FROM '._DB_PREFIX_.'storecom_imagefile
                                                            WHERE priority ='.(int) $analysis_params['priorities']['obsolete']);

    ## si ce total n'est pas bon,
    ## ça fausse les calculs on exclue
    $total_real_size_gain = 0;
    if ($total_origin_size_from_compressed_image > 0)
    {
        $total_real_size_gain = (float) ($compressed['total_size_gain_from_compressed_image'] / $total_origin_size_from_compressed_image) * 100;
    }

    ## si ce total n'est pas cohérent (potentiellement une boutique qui supprime ses images au fur et à mesure...),
    ## ça fausse les calculs alors on exclue
    if ($total_real_size_gain > 100)
    {
        $total_real_size_gain = 0;
    }

    $new_stats = array(
        'last_scan' => date('Y-m-d H:i:s'),
        'total_count' => (int) $total['total_count'],
        'total_count_compressed' => (int) $total_count_compressed,
        'total_percentage_compressed' => ($total_count_compressed > 0 ? (float) round($total_count_compressed / $total['total_count'] * 100, 2) : 0),
        'total_size' => (int) $total['total_size'],
        'total_size_compressed' => (int) $total['total_size_compressed'],
        'total_origin_size_from_compressed_image' => (int) $total_origin_size_from_compressed_image,
        'total_after_compression' => (int) ($total['total_size'] - $total['total_size_gain']),
        'total_size_gain' => (int) $total['total_size_gain'],
        'total_real_size_gain' => (float) round($total_real_size_gain, 2),
        'total_percentage_gain' => ($total['total_size_gain'] > 0 ? (float) round($total['total_size_gain'] / $total['total_size'] * 100, 2) : 0),
        'total_size_base' => (int) $total_size_base,
        'total_size_ps_format' => (int) $total_size_ps_format,
        'total_count_obsolete' => (int) $obsolete_images['total_count_obsolete'],
        'total_size_obsolete' => (int) $obsolete_images['total_size_obsolete'],
    );
    SCI::updateConfigurationValue('SC_IMAGECOMPRESSION_STATS', json_encode($new_stats));

    $error_server = error_get_last(); ## en cas d'erreur serveur
    if (!empty($error_server))
    {
        if (!in_array($error_server['type'], array(E_NOTICE, E_USER_NOTICE, E_USER_DEPRECATED)))
        {
            $error[] = $error_server['type'].'# '.$error_server['message'];
        }
    }

    ## SC
    $access_details = access_details();
    $ext = get_loaded_extensions();
    $need = array(
        'LICENSE' => '#',
        'DOMAIN' => getShopProtocol().$access_details['domain'].__PS_BASE_URI__,
        'SC_UNIQUE_ID' => SCI::getConfigurationValue('SC_UNIQUE_ID'),
        'errors' => (!empty($error) ? implode("\n", $error) : ''),
        'images_type' => json_encode($analysis_params['images_type']),
        'sc_install_folder' => SC_MODULE_FOLDER_NAME,
    );
    $data = array(
        'php_cwebp' => 0,
        'php_gmagick' => 0,
        'php_imagick' => 0,
        'php_gd' => 0,
    );
    exec('cwebp', $output, $return);
    if ($return !== 127)
    {
        $data['php_cwebp'] = 1;
    }
    if (in_array('gmagick', $ext))
    {
        $tmp = new Gmagick();
        $data['php_gmagick'] = (int) in_array('WEBP', $tmp->queryFormats('*'));
    }
    if (in_array('imagick', $ext))
    {
        $tmp = new Imagick();
        $data['php_imagick'] = (int) in_array('WEBP', $tmp->queryFormats());
    }
    if (in_array('gd', $ext)
        && function_exists('imagewebp')
        && function_exists('imagecreatefrompng')
        && function_exists('imagecreatefromjpeg'))
    {
        $tmp = gd_info();
        $data['php_gd'] = (int) $tmp['WebP Support'];
    }
    $data += $need;
    $data += $new_stats;
    makeCallToOurApi('Analysis/Img', array(), $data);
    $time_end = microtime(true);
}
foreach ($error as &$r)
{
    $r = '<li>'.$r.'</li>';
}
$return = array(
    'valid' => (empty($error) ? 1 : 0),
    'error' => (empty($error) ? 0 : 1),
    'detail' => implode("\n", $error),
    'process_duration' => $time_end - $time_start,
);
echo json_encode($return);

function insertTempImageFileInfos($item, $folder_path)
{
    global $error, $analysis_params;
    $sql_file_path = _PS_TMP_IMG_DIR_.$item.'.sql';
    switch ($item) {
        case 'themes':
            $enabled_themes = array();
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
            {
                $res = Db::getInstance()->executeS('SELECT theme_name FROM '._DB_PREFIX_.'shop WHERE active = 1 AND deleted = 0');
                if (!empty($res))
                {
                    foreach ($res as $row)
                    {
                        $enabled_themes[] = $row['theme_name'];
                    }
                }
            }
            elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $res = Db::getInstance()->executeS('SELECT DISTINCT(t.directory)
                                                                FROM '._DB_PREFIX_.'shop s
                                                                LEFT JOIN '._DB_PREFIX_.'theme t ON t.id_theme = s.id_theme
                                                                WHERE s.active = 1
                                                                AND s.deleted = 0');
                if (!empty($res))
                {
                    foreach ($res as $row)
                    {
                        $enabled_themes[] = $row['directory'];
                    }
                }
            }
            else
            {
                $enabled_themes[] = _THEME_NAME_;
            }
            if (!empty($enabled_themes))
            {
                foreach ($enabled_themes as $theme_dir)
                {
                    $command = 'find '.$folder_path.$theme_dir.' -type f -regex ".*\.\(jpg\|png\|jpeg\)" -size +'.(int) $analysis_params['minimal_size'].'c -printf "(%s,\"%p\",\"%f\",'.(int) $analysis_params['priorities'][$item].',%m),\n" >> '.$sql_file_path;
                    exec($command);
                }
                if (!file_exists($sql_file_path))
                {
                    $error[] = $item.': '._l('SQL file not found');
                }
                else
                {
                    $res = insertToTemp($sql_file_path);
                    if (!$res)
                    {
                        $error[] = $item.': '._l('Unable to insert data to temporary table. Please contact our support.');
                    }
                }
            }
            break;
        default:
            ## export dans un fichier sql
            ## %s: size octet
            ## %p: path
            ## %f: filename
            ## %m: chmod
            $command = 'find '.$folder_path.' -type f -regex ".*\.\(jpg\|png\|jpeg\)" -size +'.(int) $analysis_params['minimal_size'].'c -printf "(%s,\"%p\",\"%f\",'.(int) $analysis_params['priorities'][$item].',%m),\n" > '.$sql_file_path;
            exec($command);
            if (!file_exists($sql_file_path))
            {
                $error[] = $item.': '._l('SQL file not found');
            }
            else
            {
                $res = insertToTemp($sql_file_path);
                if (!$res)
                {
                    $error[] = $item.': '._l('Unable to insert data to temporary table. Please contact our support.');
                }
            }
    }
    if (file_exists($sql_file_path))
    {
        unlink($sql_file_path);
    }
}

function insertToTemp($sql_file_path)
{
    global $error;
    ## création requete sql
    $values = file_get_contents($sql_file_path);
    if (!empty($values))
    {
        $values = str_replace(rtrim(_PS_ROOT_DIR_, '\/'), '', $values);
        $data_array = explode("\n", $values);
        $data_array_chunked = array_chunk($data_array, 10000);
        foreach ($data_array_chunked as $chunck)
        {
            $sql = 'INSERT INTO '._DB_PREFIX_.'storecom_imagefile_TEMP VALUES '."\n";
            $sql .= implode("\n", $chunck);
            $sql = trim(trim($sql), ',');
            $res = Db::getInstance()->Execute($sql);
            if (!$res)
            {
                $error[] = 'MysqlError# '.Db::getInstance()->getMsgError();

                return false;
            }
        }
    }

    return true;
}

function setImageFilePriority($item)
{
    global $error, $analysis_params;
    switch ($item) {
        case 'homepage':
            $id_home_category = array();
            $homefeatured = Db::getInstance()->executeS('SELECT DISTINCT(value) FROM '._DB_PREFIX_.'configuration WHERE name ="HOME_FEATURED_CAT"');
            if (!empty($homefeatured))
            {
                foreach ($homefeatured as $row)
                {
                    $id_home_category[] = $row['value'];
                }
            }
            else
            {
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $cats = Db::getInstance()->Executes('SELECT DISTINCT(id_category) FROM '._DB_PREFIX_.'shop WHERE active = 1 AND deleted = 0');
                    foreach ($cats as $row)
                    {
                        $id_home_category[] = $row['id_category'];
                    }
                }
                else
                {
                    $id_home_category = array(1);
                }
            }
            $home_featured_nb = Configuration::get('HOME_FEATURED_NBR');
            $limit = (int) (!empty($home_featured_nb) ? $home_featured_nb : 10);
            $sql = 'SELECT DISTINCT(i.id_image)
                    FROM '._DB_PREFIX_.'category_product cp
                    RIGHT JOIN '._DB_PREFIX_.'image i ON i.id_product = cp.id_product
                    WHERE cp.id_category IN ('.pInSQL(implode(',', $id_home_category)).')
                    LIMIT '.(int) $limit;
            $images = Db::getInstance()->executeS($sql);
            $res = setProductPriorityByIdImages($images, $item);
            if (!$res)
            {
                $error[] = $item.': '._l('Unable to update priorities to final table. Please contact our support.');
            }
            break;
        case 'bestseller':
            $sql = 'SELECT id_image
                    FROM '._DB_PREFIX_.'image
                    WHERE id_product IN (SELECT ps.id_product
                        FROM '._DB_PREFIX_.'product_sale ps
                        WHERE ps.date_upd >= (NOW()-INTERVAL 6 MONTH)
                        ORDER BY ps.quantity DESC)';
            $images = Db::getInstance()->executeS($sql);
            $res = setProductPriorityByIdImages($images, $item);
            if (!$res)
            {
                $error[] = $item.': '._l('Unable to update priorities to final table. Please contact our support.');
            }
            break;
        case 'news':
            $sql = 'SELECT i.id_image
                        FROM '._DB_PREFIX_.'product p
                        RIGHT JOIN '._DB_PREFIX_.'image i ON i.id_product = p.id_product
                        WHERE p.date_add >= (NOW()-INTERVAL 6 MONTH)';
            $images = Db::getInstance()->executeS($sql);
            $res = setProductPriorityByIdImages($images, $item);
            if (!$res)
            {
                $error[] = $item.': '._l('Unable to update priorities to final table. Please contact our support.');
            }
            break;
        case 'official':
            $like_sql = array();
            foreach ($analysis_params['images_type'] as $type)
            {
                $like_sql[] = '`path` LIKE "%'.$type['name'].$analysis_params['ps_img_extension'].'"';
            }
            ## miniature categorie
            $like_sql[] = '`path` LIKE "%_thumb'.$analysis_params['ps_img_extension'].'"';
            $sql = 'UPDATE '._DB_PREFIX_.'storecom_imagefile
                        SET `priority` = '.(int) $analysis_params['priorities'][$item].'
                        WHERE `image_base` = 0
                        AND `priority` IN ('.pInSQL(implode(',', $analysis_params['item_catalog_priority'])).')
                        AND ('.implode(' OR ', $like_sql).')';
            $res = Db::getInstance()->execute($sql);
            if (!$res)
            {
                $error[] = $item.': '._l('Unable to update priorities to final table. Please contact our support.');
            }
            break;
        case 'image_base':
            $sql = 'UPDATE '._DB_PREFIX_.'storecom_imagefile
                        SET `image_base` = 1
                        WHERE `priority` IN ('.pInSQL(implode(',', $analysis_params['item_catalog_priority'])).")
                        AND name REGEXP '^([0-9]*\\".$analysis_params['ps_img_extension'].")'";
            $res = Db::getInstance()->execute($sql);
            if (!$res)
            {
                $error[] = $item.': '._l('Unable to update priorities to final table. Please contact our support.');
            }
            break;
        case 'other':
            $sql = 'UPDATE '._DB_PREFIX_.'storecom_imagefile
                        SET `priority` = '.(int) $analysis_params['priorities'][$item].'
                        WHERE `priority` IN ('.pInSQL(implode(',', $analysis_params['item_catalog_priority'])).')
                        AND image_base = 0';
            $res = Db::getInstance()->execute($sql);
            if (!$res)
            {
                $error[] = $item.': '._l('Unable to update priorities to final table. Please contact our support.');
            }
            break;
        case 'obsolete':
            $sql = 'UPDATE '._DB_PREFIX_.'storecom_imagefile
                        SET `priority` = '.(int) $analysis_params['priorities'][$item].'
                        WHERE priority = '.(int) $analysis_params['priorities']['other']."
                        AND name REGEXP '^[0-9]*-'";
            $res = Db::getInstance()->execute($sql);
            if (!$res)
            {
                $error[] = $item.': '._l('Unable to update priorities to final table. Please contact our support.');
            }
            break;
    }
}

function setProductPriorityByIdImages($images, $item)
{
    global $analysis_params;
    if (!empty($images))
    {
        $in_clause = array();
        foreach ($images as $row)
        {
            $in_clause[] = $row['id_image'].$analysis_params['ps_img_extension'];
        }
        $sql = 'UPDATE '._DB_PREFIX_.'storecom_imagefile
                       SET `priority` = '.(int) $analysis_params['priorities'][$item].'
                       WHERE `image_base` = 1
                       AND `path` LIKE "/img/p/%"
                       AND `name` IN ("'.implode('","', $in_clause).'")';

        return Db::getInstance()->execute($sql);
    }

    return true;
}
