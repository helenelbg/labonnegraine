<?php

if (!file_exists(SC_CSV_EXPORT_DIR))
{
    $writePermissions = octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))), -3));
    @mkdir(SC_CSV_EXPORT_DIR, $writePermissions);
    if (!file_exists(SC_CSV_EXPORT_DIR))
    {
        exit(SC_CSV_EXPORT_DIR._l(':').' '._l('This folder cannot be created by Store Commander, you need to create it by FTP with writing permission.'));
    }
}
if (!file_exists(SC_TOOLS_DIR))
{
    $writePermissions = octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))), -3));
    @mkdir(SC_TOOLS_DIR, $writePermissions);
    if (!file_exists(SC_TOOLS_DIR))
    {
        exit(SC_TOOLS_DIR._l(':').' '._l('This folder cannot be created by Store Commander, you need to create it by FTP with writing permission.'));
    }
}
if (!file_exists(SC_TOOLS_DIR.'cat_export/'))
{
    $writePermissions = octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))), -3));
    @mkdir(SC_TOOLS_DIR.'cat_export/', $writePermissions);
    if (!file_exists(SC_TOOLS_DIR.'cat_export/'))
    {
        exit(SC_TOOLS_DIR.'cat_export/'._l(':').' '._l('This folder cannot be created by Store Commander, you need to create it by FTP with writing permission.'));
    }
}
if (!file_exists(SC_TOOLS_DIR.'cat_categories_sel/'))
{
    $writePermissions = octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))), -3));
    @mkdir(SC_TOOLS_DIR.'cat_categories_sel/', $writePermissions);
    if (!file_exists(SC_TOOLS_DIR.'cat_categories_sel/'))
    {
        exit(SC_TOOLS_DIR.'cat_categories_sel/'._l(':').' '._l('This folder cannot be created by Store Commander, you need to create it by FTP with writing permission.'));
    }
}

$files = array_diff(scandir(SC_DIR.'data/cat_export/'), array_merge(array('.', '..', 'index.php', '.htaccess')));

foreach ($files as $file)
{
    if (!file_exists(SC_TOOLS_DIR.'cat_export/'.$file))
    {
        copy(SC_DIR.'data/cat_export/'.$file, SC_TOOLS_DIR.'cat_export/'.$file);
    }
}

if (file_exists(SC_DIR.'data/cat_export/'.$file))
{
    exit(_l('The CSV files have been installed. You can use them in the Export CSV tool.'));
}
exit(_l('The CSV files have not been installed. Check write permissions on ').SC_TOOLS_DIR);
