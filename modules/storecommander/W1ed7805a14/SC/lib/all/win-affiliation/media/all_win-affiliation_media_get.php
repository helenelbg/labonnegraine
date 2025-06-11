<?php

    $xml = '';

    $dir_path = _PS_IMG_DIR_.'/banner/';
    $dir = _PS_IMG_.'/banner/';

    $res = SCAffBanner::GetAll();
    foreach ($res as $row)
    {
        $infos = '';
        $width = 100;
        $height = 100;
        $size = @getimagesize($dir_path.$row->image);
        if (!empty($size))
        {
            $infos .= $size[0].'x'.$size[1].' ';

            if ($size[0] > $size[1])
            {
                if ($size[0] < $width)
                {
                    $width = $size[0];
                }
                $height = '';
            }
            elseif ($size[0] <= $size[1])
            {
                if ($size[1] < $height)
                {
                    $height = $size[1];
                }
                $width = '';
            }
        }

        $extension_upload = strtoupper(substr(strrchr($row->image, '.'), 1));
        if (!empty($extension_upload))
        {
            $infos .= $extension_upload;
        }

        $xml .= "<row id='".$row->id."'>";
        $xml .= '<cell><![CDATA[<img src="'.$dir.$row->image.'" width="'.$width.'px" height="'.$height.'px" />]]></cell>';
        $xml .= '<cell><![CDATA['.$row->name.']]></cell>';
        $xml .= '<cell><![CDATA['.$infos.']]></cell>';
        $xml .= '<cell>'.$row->active.'</cell>';
        $xml .= '<cell>'.$row->url.'</cell>';
        $xml .= '<userdata name="url">'.$row->image.'</userdata>';
        $xml .= '</row>';
    }
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
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter,#text_filter,#text_filter,#select_filter,#text_filter]]></param></call>
</beforeInit>
<column id="image" width="150" type="ro" align="center" sort="str_custom"><?php echo _l('Picture'); ?></column>
<column id="name" width="150" type="edtxt" align="left" sort="str_custom"><?php echo _l('Name'); ?></column>
<column id="info" width="80" type="ro" align="left" sort="int"><?php echo _l('Information'); ?></column>
<column id="active" width="80" type="coro" align="left"><?php echo _l('Status'); ?>
    <option value="1"><?php echo _l('Active'); ?></option>
    <option value="0"><?php echo _l('Inactive'); ?></option>
</column>
<column id="url" width="300" type="ed" align="left" sort="str"><?php echo _l('Redirection url'); ?></column>
<afterInit>
</afterInit>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('sctools_scaffiliation_medias').'</userdata>'."\n";
    echo $xml;
?>
</rows>
