<?php
$id_lang = (int) Tools::getValue('id_lang');
$id_product = (int) Tools::getValue('id_product');
$type = Tools::getValue('type');

function getLevelFromDB($parent_id)
{
    global $type;
    $sql = 'SELECT *
                    FROM '._DB_PREFIX_."sc_segment
                    WHERE id_parent = " .(int) $parent_id . "
                        AND access LIKE '%-".pSQL($type)."-%'
                    ORDER BY position,name";

    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $row)
    {
        $icon = 'fa fa-folder blue';
        if ($row['type'] == 'auto')
        {
            $icon = 'fad fa-retweet blue';
        }

        $manual_add = 0;
        if ($row['type'] == 'manual')
        {
            $manual_add = 1;
        }
        elseif ($row['auto_file'])
        {
            $file = $row['auto_file'].'.php';
            if (file_exists(SC_SEGMENTS_DIR.$file))
            {
                require_once SC_SEGMENTS_DIR.$file;
                $instance = new $row['auto_file']();
                if ($instance->manually_add_in == 'Y')
                {
                    $manual_add = 1;
                }
            }
        }

        echo '<row id="'.$row['id_segment'].'" open="1" >';
        echo '  <userdata name="manuel_add">'.(int) $manual_add.'</userdata>';
        echo '<cell><![CDATA[0]]></cell>';
        echo '<cell icon="'.$icon.'"><![CDATA[ '.$row['name'].']]></cell>';
        getLevelFromDB($row['id_segment']);
        echo '</row>'."\n";
    }
}

//XML HEADER

//include XML Header (as response will be in xml format)
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
<rows parent="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[,#text_filter]]></param></call>
</beforeInit>
<column id="present" width="50" type="ch" align="center" sort="str"><?php echo _l('Associated'); ?></column>
<column id="name" width="500" type="tree" align="left" sort="str"><?php echo _l('Name'); ?></column>
</head>
<?php
    getLevelFromDB(0);
?>
</rows>