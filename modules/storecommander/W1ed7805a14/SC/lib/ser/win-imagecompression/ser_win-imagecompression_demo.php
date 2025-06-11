<?php

$start = Tools::getValue('start', 0);
define('COMPRESS_K', 'ee819775b31f4290730dea62bcef06effc8b589df2a26549c0c7992915dc3406');
define('COMPARE_K', '7a4942fd7e39b8177eeb7ea132b9ad686239fc1ee0893cb8bbb619885e1a5fb2');
if ($start)
{
    $access_details = access_details();
    $current_domain = getShopProtocol().$access_details['domain'].__PS_BASE_URI__;
    $current_domain_img_tmp_url = rtrim($current_domain, '/').'/img/tmp/';

    ## si demo compression déjà faite, affiche html
    $data_already_saved = SCI::getConfigurationValue('SC_IMAGECOMPRESSION_DEMO');
    if (!empty($data_already_saved))
    {
        $saved_data = json_decode($data_already_saved, true);
        $one_compressed_file_missing = 0;
        foreach ($saved_data as $image)
        {
            $current_path = str_replace($current_domain_img_tmp_url, _PS_TMP_IMG_DIR_, $image['compressed_url']);
            if (!file_exists($current_path))
            {
                ++$one_compressed_file_missing;
            }
        }
        if (empty($one_compressed_file_missing))
        {
            echo returnHtmlForComparison($saved_data);
            exit;
        }
    }

    require_once dirname(__FILE__).'/ser_win-imagecompression_tools.php';

    $last_stats = json_decode(SCI::getConfigurationValue('SC_IMAGECOMPRESSION_STATS'), true);
    $last_analysis = date('Y-m-d', strtotime($last_stats['last_scan']));
    $today = date('Y-m-d');
    if (!empty($last_stats) && (!empty($last_stats['last_scan']) && $last_analysis == $today))
    {
        ## Récupérer le plus grand format d'image dispo
        $sql = 'SELECT  `name`,`width`,`height`,(width*height) as biggest_size FROM `'._DB_PREFIX_.'image_type`
                WHERE `products` = 1
                ORDER BY `biggest_size` DESC';
        $biggest_format = Db::getInstance()->getRow($sql);

        if (!empty($biggest_format))
        {
            ## Récupérer 5 images de base dans meilleurs ventes, si pas trouvé, news, si pas trouvé, p
            $sql = 'SELECT * 
                    FROM '._DB_PREFIX_.'storecom_imagefile 
                    WHERE `priority` IN (
                    '.$analysis_params['priorities']['bestseller'].',
                    '.$analysis_params['priorities']['news'].',
                    '.$analysis_params['priorities']['p'].'
                    ) 
                    AND `image_base` = 1
                    ORDER BY `priority` ASC, `size_origin` DESC
                    LIMIT 5';
            $five_best_seller = Db::getInstance()->executeS($sql);

            if (!empty($five_best_seller))
            {
                ## Appeler sc.net pour compression image
                require_once SC_DIR.'lib/php/Requests/Requests.php';
                Requests::register_autoloader();

                $urls = $names = $ids = array();
                foreach ($five_best_seller as $image)
                {
                    $name = str_replace($analysis_params['ps_img_extension'], '-'.$biggest_format['name'].$analysis_params['ps_img_extension'], $image['name']);
                    $path = str_replace($analysis_params['ps_img_extension'], '-'.$biggest_format['name'].$analysis_params['ps_img_extension'], $image['path']);
                    $url_path = rtrim($current_domain, '/').$path;
                    $names[$url_path] = $name;
                    $ids[$url_path] = str_replace($analysis_params['ps_img_extension'], '', $image['name']);
                    $urls[] = $url_path;
                }

                $data = array(
                    'urls' => implode(',', $urls),
                );
                $options = array(
                    'timeout' => 50,
                    'connect_timeout' => 50,
                    'verify' => false,
                );

                $response = Requests::post('https://www.storecommander.net/imgcompress/demo.php', array('k3y' => COMPRESS_K), $data, $options);
                if ($response->success)
                {
                    $body = json_decode($response->body, true);
                    if (!empty($body['errors']))
                    {
                        echo '<p>'._l('There are some errors:').'</p>
                        <ul>';
                        foreach ($body['errors'] as $error)
                        {
                            list($item, $img_url) = explode('#', $error);
                            switch ($item) {
                                case 'FAIL_1':
                                    echo '<li>'.$img_url.' '._l('Compression error.').'</li>';
                                    break;
                                case 'FAIL_2':
                                    echo '<li>'.$img_url.' '._l('Empty image url.').'</li>';
                                    break;
                                default:
                                    echo '<li>'._l('Compression params error.').'</li>';
                            }
                        }
                        echo '</ul>';
                        echo '<hr>';
                    }
                    if (!empty($body['success']))
                    {
                        ## Enregistrer final dans img/tmp
                        $distant_images = $body['success'];
                        $result_to_save = $img_not_ready = array();
                        foreach ($urls as $cle)
                        {
                            if (array_key_exists($cle, $distant_images))
                            {
                                $current_distant_image = $distant_images[$cle];

                                $file_name = (string) $names[$cle];
                                if ($current_distant_image['Status']['Code'] == '2')
                                {
                                    $compressed_image_data = sc_file_get_contents($current_distant_image['LossyURL']);
                                    if (!empty($compressed_image_data))
                                    {
                                        if ($compressed_image_data !== false && validImage($compressed_image_data))
                                        {
                                            $write_file = file_put_contents(_PS_TMP_IMG_DIR_.$file_name, $compressed_image_data);
                                            if ($write_file !== false)
                                            {
                                                $id_image = (int) $ids[$cle];
                                                $sql = 'SELECT pl.name
                                                    FROM `'._DB_PREFIX_.'image` i
                                                    RIGHT JOIN `'._DB_PREFIX_.'product_lang` pl 
                                                        ON pl.id_product = i.id_product 
                                                        AND pl.id_lang = '.(int) $sc_agent->id_lang.'
                                                    WHERE (pl.name IS NOT NULL AND pl.name != "") 
                                                    AND i.id_image = '.(int) $id_image;
                                                $product_name = Db::getInstance()->getValue($sql);
                                                $result_to_save[] = array(
                                                    'product_name' => $product_name,
                                                    'origin_size' => $current_distant_image['OriginalSize'],
                                                    'compressed_size' => $current_distant_image['LossySize'],
                                                    'gain_size' => $current_distant_image['OriginalSize'] - $current_distant_image['LossySize'],
                                                    'original_url' => $current_distant_image['OriginalURL'],
                                                    'compressed_url' => $current_domain_img_tmp_url.$file_name,
                                                );
                                            }
                                            else
                                            {
                                                echo '<p>'._l('Error').': '._l('Writing file:').' '._PS_TMP_IMG_DIR_.$file_name.'</p>';
                                            }
                                        }
                                        else
                                        {
                                            echo '<p>'._l('Error').': '._l('Saving content for file:').' '._PS_TMP_IMG_DIR_.$file_name.'</p>';
                                        }
                                    }
                                }
                                else
                                {
                                    $img_not_ready = true;
                                    break;
                                }
                            }
                        }

                        if (!$img_not_ready && !empty($result_to_save))
                        {
                            SCI::updateConfigurationValue('SC_IMAGECOMPRESSION_DEMO', json_encode($result_to_save));
                            echo returnHtmlForComparison($result_to_save);
                        }
                        if ($img_not_ready)
                        {
                            echo 'RELOAD';
                        }
                    }
                    else
                    {
                        echo '<p>'._l('Error').': '._l('No compressed images recovered.').'</p>';
                    }
                }
                else
                {
                    echo '<p>'._l('Error').': '._l('Error from compression script.').'</p>';
                }
            }
        }
        else
        {
            echo _l('Biggest image format not found.');
        }
    }
    else
    {
        echo _l('You need to do analysis first.');
    }
}

function returnHtmlForComparison($data_for_html)
{
    global $user_lang_iso;
    $html = '<ol style="margin:0;">';
    $total = array(
        'total_origin' => 0,
        'total_compressed' => 0,
        'total_gain' => 0,
    );
    foreach ($data_for_html as $row)
    {
        $total['total_origin'] += (int) $row['origin_size'];
        $total['total_compressed'] += (int) $row['compressed_size'];
        $total['total_gain'] += (int) $row['gain_size'];
        $path_origin = base64_encode($row['original_url']);
        $path_compressed = base64_encode($row['compressed_url']);
        $comparison_url_params = '?iso='.$user_lang_iso.'&k3y='.COMPARE_K.'&pathOrigin='.$path_origin.'&pathCompressed='.$path_compressed;
        $html .= '<li>'._l('Image from product:').' '.$row['product_name'].
            '<br/>'._l('Original file:').' '.sizeFormat((int) $row['origin_size'], 2).
            '<br/>'._l('Compressed file:').' '.sizeFormat((int) $row['compressed_size'], 2).
            '<br/>'._l('Gain:').' '.sizeFormat((int) $row['gain_size'], 2).
            '<br/><a  style="text-align:left;" href="https://storecommander.net/imgcompress/comparison.php'.$comparison_url_params.'" target="_blank">'._l('Check the difference visually').'</a>'.
            '</li>';
    }
    $html .= '</ol>';
    $html .= '<ul>
        <li>'._l('Total size before compression:').' '.sizeFormat((int) $total['total_origin'], 2).'</li>
        <li>'._l('Total size after compression:').' '.sizeFormat((int) $total['total_compressed'], 2).'</li>
        <li>'._l('Reduced total size:').' '.sizeFormat((int) $total['total_gain'], 2).' - '.($total['total_compressed'] > 0 ? (float) round($total['total_gain'] / $total['total_origin'] * 100, 2).'%' : 0).'</li>
    </ul>';

    return $html;
}

function validImage($file_content)
{
    $size = getimagesizefromstring($file_content);

    return strtolower(substr($size['mime'], 0, 5)) == 'image' ? true : false;
}
