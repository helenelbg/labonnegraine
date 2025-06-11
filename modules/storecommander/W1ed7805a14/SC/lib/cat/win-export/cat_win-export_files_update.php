<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $filename = (Tools::getValue('filenamexport'));

    if (isset($_POST['filenamexport']))
    {
        $exporttitle = explode(',', Tools::getValue('filenamexport', ''));

        foreach ($exporttitle as $exportvalue)
        {
            $dir = '../../export/';
        }
        $opendir = opendir($dir);

        if ($exportvalue != '')
        {
            $path = $dir.$exportvalue;

            while ($filelist = @readdir($opendir))
            {
                if (!is_dir($dir.'/'.$filelist) && $filelist != '.' && $filelist != '..' && $filelist != 'index.php')
                {
                    unlink($path);
                }
            }

            closedir($open_dir);
        }
        else
        {
            echo 'Files-update error';
        }
    }
    else
    {
        echo 'Files-update error';
    }
