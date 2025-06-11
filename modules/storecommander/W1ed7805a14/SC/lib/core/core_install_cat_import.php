<?php

$files = array_diff(scandir(SC_DIR.'data/cat_import/'), array_merge(array('.', '..', 'index.php', '.htaccess')));
$iso = 'EN';
$flag = false;
if ($user_lang_iso == 'fr')
{
    $iso = 'FR';
}
foreach ($files as $file)
{
    if (substr($file, 0, 2) == $iso)
    {
        @unlink(SC_CSV_IMPORT_DIR.$file);
        copy(SC_DIR.'data/cat_import/'.$file, SC_CSV_IMPORT_DIR.$file);
        if (file_exists(SC_CSV_IMPORT_DIR.$file))
        {
            $flag = true;
        }
    }
}
if ($flag)
{
    exit(_l('The CSV files have been installed. You can use them in the Import CSV tool.'));
}
exit(_l('The CSV files have not been installed. Check write permissions on ').SC_CSV_IMPORT_DIR);
