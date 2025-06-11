<?php

if (Tools::getValue('file'))
{
    /* Admin can directly access to file */
    $filename = Tools::getValue('file');
    if (!file_exists(_PS_DOWNLOAD_DIR_.$filename))
    {
        exit('File not found');
    }
}

$name = Tools::getValue('name', $filename);

/* Set headers for download */
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize(_PS_DOWNLOAD_DIR_.$filename));
header('Content-Disposition: attachment; filename="'.$name.'"');
readfile(_PS_DOWNLOAD_DIR_.$filename);
exit;
