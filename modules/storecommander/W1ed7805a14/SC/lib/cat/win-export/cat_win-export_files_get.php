<?php

$id_lang = (int) Tools::getValue('id_lang');
$exportConfig = array();

function getFiles()
{
    $dir = '../../export/';

    $open_dir = opendir($dir) or exit('Erreur');

    while ($filename = @readdir($open_dir))
    {
        if (!is_dir($dir.'/'.$filename) && $filename != '.' && $filename != '..' && $filename != 'index.php')
        {
            echo '<row id="'.$filename.'">';
            echo '<cell><![CDATA[<a href="index.php?ajax=1&act=all_get-file&type=export&file='.$filename.'&'.time().'" target="_blank" style="color: #000000;">'.$filename.'</a>]]></cell>';
            echo '<cell><![CDATA['.number_format(filesize($dir.$filename) / 1024, 2).']]></cell>';
            echo '<cell><![CDATA['.(date('Y-m-d H:i:s', filemtime($dir.$filename))).']]></cell>';
            echo '</row>';
        }
    }
    closedir($open_dir);
}

if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

?>
<rows>
    <head>
        <column id="filename" width="100" type="ro" align="left" sort="str"> <?php echo _l('Filename'); ?></column>
        <column id="filesize" width="100" type="ro" align="right" sort="int"><?php echo _l('Filesize'); ?> (Ko)</column>
        <column id="date" width="140" type="ro" align="right" sort="str"><?php echo _l('Date'); ?></column>
    </head>
    <?php
    getFiles();
    ?>
</rows>

