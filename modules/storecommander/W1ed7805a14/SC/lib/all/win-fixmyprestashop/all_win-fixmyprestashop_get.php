<?php
    $actions_selected = Tools::getValue('actions_selected', null);
    $xml = '';
    $id_lang = (int) (!empty($sc_agent->id_lang) ? $sc_agent->id_lang : Configuration::get('PS_LANG_DEFAULT'));
    include dirname(__FILE__).'/all_win-fixmyprestashop_controls.php';

    if(Configuration::get('PS_IMAGE_QUALITY') === 'webp_all'){
        unset($controls['CAT_SEO_IMAGE_COMPRESSION']);
    }

    if (!empty($actions_selected))
    {
        $actions_selected = explode(',', $actions_selected);
        $tmp = array();
        foreach ($actions_selected as $action)
        {
            if (array_key_exists($action, $controls))
            {
                $tmp[$action] = $controls[$action];
            }
        }
        $controls = $tmp;
    }

    foreach ($controls as $row)
    {
        $good_version = true;
        if (!empty($row['version_min']) && version_compare(_PS_VERSION_, $row['version_min'], '<'))
        {
            $good_version = false;
        }
        if (!empty($row['version_max']) && version_compare(_PS_VERSION_, $row['version_max'], '>'))
        {
            $good_version = false;
        }
        if ($good_version)
        {
            if (is_array($row['name']))
            {
                $name = _l($row['name'][0], null, $row['name'][1]);
            }
            else
            {
                $name = _l($row['name']);
            }
            if (is_array($row['description']))
            {
                $description = _l($row['description'][0], null, $row['description'][1]);
            }
            else
            {
                $description = _l($row['description']);
            }
            $xml .= "<row id='".$row['key']."'>";
            $xml .= '<cell><![CDATA[0]]></cell>';
            $xml .= '<cell><![CDATA['._l($row['tools']).']]></cell>';
            $xml .= '<cell><![CDATA['._l($row['section']).']]></cell>';
            $xml .= '<cell><![CDATA['.$name.']]></cell>';
            $xml .= '<cell></cell>';
            $xml .= '<cell><![CDATA['.$description.']]></cell>';
            $xml .= '</row>';
        }
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
        <call command="attachHeader"><param><![CDATA[,#select_filter,#select_filter,#text_filter,#select_filter,#text_filter]]></param></call>
    </beforeInit>

    <column id="ignore" width="60" type="ch" align="center" sort="int"><?php echo _l('Skip'); ?></column>
    <column id="tools" width="120" type="ro" align="left" sort="str"><?php echo _l('Tools'); ?></column>
    <column id="section" width="120" type="ro" align="left" sort="str"><?php echo _l('Section'); ?></column>
    <column id="name" width="400" type="ro" align="left" sort="str"><?php echo _l('Control'); ?></column>
    <column id="results" width="60" type="ro" align="center" sort="str" color=""><?php echo _l('Results'); ?></column>
    <column id="description" width="*" type="ro" align="left" sort="str"><?php echo _l('Description'); ?></column>

</head>
<?php
    echo $xml;
?>
</rows>
