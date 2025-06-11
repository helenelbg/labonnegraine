<?php

$action = Tools::getValue('action', null);
$error = $detail = array();
if (!empty($action))
{
    $last_stats = json_decode(SCI::getConfigurationValue('SC_IMAGECOMPRESSION_STATS'), true);
    if (empty($last_stats))
    {
        $error[] = _l('You need to do analysis first.');
    }
    else
    {
        switch ($action) {
            case 'backup_confirmation':
                $value = Tools::getValue('value', 0);
                $res = SCI::updateConfigurationValue('SC_IMAGECOMPRESSION_BACKUP_CONFIRM', (int) $value);
                if (!$res)
                {
                    $error[] = _l('Error during process');
                }
                break;
            case 'compression_activation':
                ## 0: non activé
                ## 1: activé
                ## 2: preprod
                $compression_value = $compression_value_for_local_config = (int) Tools::getValue('value', 0);
                $access_details = access_details();
                if ($compression_value == 1)
                {
                    $preprod_keywords = array('127.0.0.1', 'demo.', 'dev.', 'preprod.', 'test.', 'beta.', '/demo', '/dev', '/preprod', '/test', '/beta');
                    $domain = $access_details['domain'].__PS_BASE_URI__;
                    foreach ($preprod_keywords as $k_word)
                    {
                        $pos = strpos(strtolower($domain), $k_word);
                        if ($pos !== false)
                        {
                            $compression_value = 2;
                            break;
                        }
                    }
                    ## si toujours 1
                    if ($compression_value == 1)
                    {
                        ## vérification que qu'une image (parmis les 5 aléatoires) ne soit pas en 401 sinon => site dev
                        $sql = 'SELECT r1.name,r1.path
                                FROM '._DB_PREFIX_.'storecom_imagefile AS r1 JOIN
                                   (SELECT CEIL(RAND() *
                                                 (SELECT MAX(id_storecom_imagefile)
                                                    FROM '._DB_PREFIX_.'storecom_imagefile)) AS id_storecom_imagefile)
                                    AS r2
                                WHERE r1.id_storecom_imagefile >= r2.id_storecom_imagefile
                                ORDER BY r1.id_storecom_imagefile ASC
                                LIMIT 5';
                        $res = Db::getInstance()->executeS($sql);
                        if (!empty($res))
                        {
                            foreach ($res as $row)
                            {
                                $headers = get_headers($domain.$row['path']);
                                if (strpos('401', $headers[0]) !== false)
                                {
                                    $compression_value = 2;
                                    break;
                                }
                            }
                        }
                    }
                }

                ## si preprod(2) on laisse à 1 pour pouvoir enregistrement local
                $res = SCI::updateConfigurationValue('SC_IMAGECOMPRESSION_ACTIVE', (int) $compression_value_for_local_config);
                if (!$res)
                {
                    $error[] = _l('Error during process');
                }
                else
                {
                    $data = array(
                        'LICENSE' => '#',
                        'DOMAIN' => getShopProtocol().$access_details['domain'].__PS_BASE_URI__,
                        'SC_UNIQUE_ID' => SCI::getConfigurationValue('SC_UNIQUE_ID'),
                        'compression_active' => $compression_value, ## si preprod(2) on envoi bien 2 au serveur pour qu'on puisse intervenir
                    );
                    makeCallToOurApi('Compression/Active', array(), $data);
                    switch ($compression_value) {
                        case 1:
                            $detail[] = _l('Image compression activated.');
                            break;
                        case 2:
                            $detail[] = _l('It seems you activated image compression on a test server.');
                            $detail[] = '<a href="'.getScExternalLink('support_image_compression').'" target="_blank">'._l('Please read this article').'</a>';
                            break;
                        default:
                            $detail[] = _l('Image compression disabled.');
                    }
                }
                break;
        }
    }
}
$return = array(
    'valid' => (empty($error) ? 1 : 0),
    'error' => (empty($error) ? 0 : 1),
    'error_list' => implode("\n", $error),
    'detail' => implode("\n", $detail),
);
exit(json_encode($return));
