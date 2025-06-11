<?php
$id_lang = (int) Tools::getValue('id_lang', 0);
$id_project = (Tools::getValue('id_project'));

$xml = '';

$col_partner = false;
$col_imported = false;

if (!empty($id_project))
{
    $res = array();

    $project = null;
    $headers = array();
    $posts = array();
    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
    $posts['LICENSE'] = '#';
    $posts['URLCALLING'] = '#';
    if (defined('IS_SUBS') && IS_SUBS == '1')
    {
        $posts['SUBSCRIPTION'] = '1';
    }
    $ret = makeCallToOurApi('Fizz/Project/Get/'.$id_project, $headers, $posts);
    if (!empty($ret['code']) && $ret['code'] == '200')
    {
        $project = $ret['project'];
    }

    if ($project['type'] == 'dixit' && in_array($project['status'], array('9', '10', '13', '109', '110', '111', '11', '12', '112', '113')))
    {
        $col_partner = true;
    }
    if ($project['type'] == 'dixit' && in_array($project['status'], array('10', '11', '12', '13', '112')))
    {
        $col_imported = true;
    }

    if (!empty($project['list_items']) && $project['list_items'] != '-')
    {
        /*
         * INIT
         */
        $params = (!empty($project['params']) ? json_decode($project['params'], true) : '');

        /*
         * ROWS
         */
        $res = explode('-', trim($project['list_items'], '-'));
        foreach ($res as $id)
        {
            if (empty($id))
            {
                continue;
            }
            $type = '';
            $name = '';
            $infos = '';
            $in_partner = _l('Not yet');
            $in_partner_color = '';
            $imported = _l('Not yet');
            $imported_color = '';

            if ($project['type'] == 'dixit')
            {
                $type = _l('Product');
                if (SCMS)
                {
                    $id_shop_default = Db::getInstance()->getValue('SELECT id_shop_default FROM `'._DB_PREFIX_.'product` WHERE `id_product` = "'.(int) $id.'"');
                    $element = new Product($id, false, null, $id_shop_default);
                }
                else
                {
                    $element = new Product($id);
                }
                $element_info = $element->name[$id_lang];
                $infos = '#'.$id.' / '._l('Ref:').' '.$element->reference;

                if ($col_partner)
                {
                    if (!empty($params['transactionId'][$id]))
                    {
                        if ($params['transactionId'][$id] == 'error')
                        {
                            $in_partner = _l('Error - We will retry');
                            $in_partner_color = '#FF0000';
                        }
                        elseif ($params['transactionId'][$id] == 'reerror')
                        {
                            $in_partner = _l('Error');
                            $in_partner_color = '#FF0000';
                        }
                        if (is_numeric($params['transactionId'][$id]))
                        {
                            $in_partner = _l('Created');
                            $in_partner_color = '#82C46C';
                        }
                    }
                }
                if ($col_imported)
                {
                    if (isset($params['importedPdt'][$id]))
                    {
                        if (!empty($params['importedPdt'][$id]))
                        {
                            $imported = _l('Imported');
                            $imported_color = '#82C46C';
                        }
                        else
                        {
                            $imported = _l('Error');
                            $imported_color = '#FF0000';
                        }
                    }
                }
            }
            elseif ($project['type'] == 'cutout')
            {
                $type = _l('Image');
                $element = new Image($id);
                if (file_exists(SC_PS_PATH_REL.'img/p/'.getImgPath((int) $element->id_product, (int) $element->id, _s('CAT_PROD_GRID_IMAGE_SIZE'))))
                {
                    $element_info = "<img src='".SC_PS_PATH_REL.'img/p/'.getImgPath((int) $element->id_product, (int) $element->id, _s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>";
                }
                else
                {
                    $element_info = "<img src='".$defaultimg."'/>";
                }

                if (SCMS)
                {
                    $product = new Product($element->id_product, true);
                }
                else
                {
                    $product = new Product($element->id_product);
                }
                $infos = _l('Product').' #'.$element->id_product.' / '._l('Ref:').' '.$product->reference;
            }

            $xml .= "<row id='".$id."'>";
            $xml .= '<cell><![CDATA['.$id.']]></cell>';
            $xml .= '<cell><![CDATA['.$type.']]></cell>';
            $xml .= '<cell><![CDATA['.$element_info.']]></cell>';
            if ($col_partner)
            {
                $xml .= "<cell bgColor='".$in_partner_color."'><![CDATA[".$in_partner.']]></cell>';
            }
            if ($col_imported)
            {
                $xml .= "<cell bgColor='".$imported_color."'><![CDATA[".$imported.']]></cell>';
            }
            $xml .= '<cell><![CDATA['.$infos.']]></cell>';
            $xml .= '</row>';
        }
    }
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
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#text_filter,#select_filter,#text_filter<?php if ($col_partner) { ?>,#select_filter<?php } ?>,#text_filter]]></param></call>
        </beforeInit>
        <column id="id" width="100" type="ro" align="left" sort="str"><?php echo _l('Id'); ?></column>
        <column id="type_element" width="100" type="ro" align="left" sort="str"><?php echo _l('Type'); ?></column>
        <column id="element" width="200" type="ro" align="left" sort="str"><?php echo _l('Element'); ?></column>
        <?php if ($col_partner) { ?>
            <column id="in_partner" width="100" type="ro" align="left" sort="str"><?php echo _l('Created at SC service\'s'); ?></column>
        <?php }
        if ($col_imported) { ?>
            <column id="imported" width="100" type="ro" align="left" sort="str"><?php echo _l('Imported'); ?></column>
        <?php }?>
        <column id="infos" width="300" type="ro" align="left" sort="str"><?php echo _l('Additional information'); ?></column>
        <afterInit>
            <call command="enableHeaderMenu"></call>
        </afterInit>
    </head>
    <?php
    echo $xml;
    ?>
</rows>
