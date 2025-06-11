<?php

$current_url = Tools::getValue('current_url');

$newHash = 'W'.Tools::substr(md5(date('YmdHis')._COOKIE_KEY_), 0, 10);
$oldHash = Configuration::get('SC_FOLDER_HASH');

$exp = explode($oldHash, SC_DIR);
$dir_base = $exp[0];

$old_dir = $dir_base.$oldHash;
$new_dir = $dir_base.$newHash;

if (rename($old_dir, $new_dir))
{
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'configuration WHERE `name` = "SC_FOLDER_HASH"');
        Configuration::updateValue('SC_FOLDER_HASH', $newHash, false, 0, 0);
    }
    else
    {
        Configuration::updateValue('SC_FOLDER_HASH', $newHash);
    }

    echo str_replace($oldHash, $newHash, $current_url);
}
