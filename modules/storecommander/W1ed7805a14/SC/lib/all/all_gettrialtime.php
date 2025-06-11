<?php

$id_lang = Tools::getValue('id_lang', '');
$item = Tools::getValue('item', null);
$license_key = SCI::getConfigurationValue('SC_LICENSE_KEY', '');

$content = 'No data';
if (!empty($id_lang) && !empty($license_key))
{
    $iso = strtolower($user_lang_iso); //strtolower(Language::getIsoById((int)$id_lang));
    switch ($item){
        case 'segmentation':
            $content = sc_file_get_contents('https://www.storecommander.com/trial/getSegmentationInfo.php', 'GET', array('license' => $license_key, 'lang_iso' => $iso));
            break;
        default:
            $content = sc_file_get_contents('https://www.storecommander.com/trial/getTrialInfo.php', 'GET', array('license' => $license_key, 'lang_iso' => $iso));
    }
}

echo $content;
