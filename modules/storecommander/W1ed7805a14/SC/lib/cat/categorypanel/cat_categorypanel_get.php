<?php
    $id_lang = (int) Tools::getValue('id_lang');
    $id_product = (int) Tools::getValue('id_product');
    $for_mb = Tools::getValue('for_mb', 1);

    function getLevelFromDB_PHP($id_parent, $eservices_parent = 0)
    {
        global $id_lang,$for_mb,$array_cats,$array_children_cats,$shops ,$eservices_cat_id,$eservices_cat_archived,$eservices_projects_byIdCat,$eservices_projects_authorizedStatus;

        if (!empty($array_children_cats[$id_parent]))
        {
            ksort($array_children_cats[$id_parent]);
            foreach ($array_children_cats[$id_parent] as $k => $id)
            {
                $row = $array_cats[$id];
                $style = '';
                if ($row['name'] == 'SoColissimo')
                {
                    continue;
                }
                if ($row['name'] == '')
                {
                    $sql2 = 'SELECT name FROM '._DB_PREFIX_.'category_lang
                                        WHERE id_lang='.(int) Configuration::get('PS_LANG_DEFAULT').'
                                            AND id_category='.$row['id_category'];
                    $res2 = Db::getInstance()->getRow($sql2);
                    $style = 'style="background:lightblue" ';
                }

                $icon = ($row['active'] ? 'fa fa-folder yellow' : 'fad fa-folder grey');

                $is_eservices = 0;
                $in_eservices = 0;
                $eservices_id_project = 0;
                if ($eservices_cat_id == $row['id_category'])
                {
                    $icon = 'fa fa-gem red';
                    $is_eservices = 1;
                    $in_eservices = 1;
                }
                else
                {
                    if (!empty($eservices_parent))
                    {
                        $in_eservices = 1;
                    }
                }
                $not_associate_eservices = 0;
                if (!empty($in_eservices))
                {
                    if (!empty($is_eservices) || $eservices_cat_archived == $row['id_category'])
                    {
                        $not_associate_eservices = 1;
                    }
                    else
                    {
                        if (!empty($eservices_projects_byIdCat[$row['id_category']]))
                        {
                            $eservices_id_project = $eservices_projects_byIdCat[$row['id_category']]['id_project'];
                            $good_status = $eservices_projects_authorizedStatus[$eservices_projects_byIdCat[$row['id_category']]['id_project']];
                            if (!in_array($eservices_projects_byIdCat[$row['id_category']]['status'], $good_status))
                            {
                                $not_associate_eservices = 1;
                            }
                        }
                    }
                }
                if (($is_eservices || $in_eservices) && $eservices_cat_id != $row['id_category'])
                {
                    if ($eservices_cat_archived == $row['id_category'] || $row['id_parent'] == $eservices_cat_archived)
                    {
                        $icon = 'fa fa-folder redgrey';
                    }
                    else
                    {
                        $icon = 'fa fa-folder red';
                    }
                }

                if (sc_in_array($row['name'], array('SC Recycle Bin', 'SC Corbeille'), 'catCategorypanel_corbeille'))
                {
                    $icon = 'fa fa-trash-alt red';
                }

                echo '<row '.($style != '' ? $style : '').
                ' id="'.$row['id_category'].'"'.($row['id_parent'] == 0 ? ' open="1"' : '').'>'.
                '<cell>'.$row['id_category'].'</cell>'.
                    '     <userdata name="not_associate_eservices">'.$not_associate_eservices.'</userdata>'.
                    '     <userdata name="eservices_id_project">'.$eservices_id_project.'</userdata>'.
                    '<cell '.($not_associate_eservices ? 'type="ro"' : '').'>'.($in_eservices ? '' : '0').'</cell>'.
                '<cell icon="'.$icon.'"><![CDATA['.($style == '' ? $row['name'] : _l('To Translate:').' '.$res2['name']).']]></cell>';
                if (SCMS)
                {
                    foreach ($shops as $idS => $nameS)
                    {
                        echo '<cell '.($in_eservices ? 'type="ro"' : '').'>'.($in_eservices ? '' : '0').'</cell>';
                    }
                }
                else
                {
                    echo '<cell '.($in_eservices ? 'type="ro"' : '').'>'.($in_eservices ? '' : '0').'</cell>';
                }
                echo '<cell>0</cell>';
                getLevelFromDB_PHP($row['id_category'], $in_eservices);
                echo '</row>'."\n";
            }
        }
    }

$shops = array();
if (SCMS)
{
    $sql = 'SELECT s.id_shop, s.name
                FROM '._DB_PREFIX_.'shop s
                    INNER JOIN '._DB_PREFIX_.'product_shop ps ON ps.id_shop = s.id_shop
                    '.((!empty($sc_agent->id_employee)) ? ' INNER JOIN '._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int) $sc_agent->id_employee."') " : '')."
                WHERE s.deleted!='1' AND s.id_shop IN(".pInSQL(implode(',',SCI::getShopsByProduct($id_product))).")
                GROUP BY s.id_shop
                ORDER BY s.name";

    $res = Db::getInstance()->executeS($sql);
    foreach ($res as $shop)
    {
        $shops[$shop['id_shop']] = str_replace('&', _l('and'), $shop['name']).' (#'.$shop['id_shop'].')';
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
<call command="attachHeader"><param><![CDATA[#text_filter,,#text_filter<?php if (SCMS)
{
    foreach ($shops as $idS => $nameS)
    {
        echo ',';
    }
}
else
{
    echo ',';
} ?>]]></param></call>
</beforeInit>
<column id="id_category" width="40" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<column id="used" width="50" type="ch" align="center" sort="str"><?php echo _l('Used'); ?></column>
<column id="name" width="250" type="tree" align="left" sort="str"><?php echo _l('Name'); ?></column>
<?php if (SCMS)
{
    foreach ($shops as $idS => $nameS)
    {
        ?>
        <column id="default_shop_<?php echo $idS; ?>" width="50" type="ra" align="center" sort="str"><?php echo _l('Default'); ?> <?php echo $nameS; ?></column>
        <?php
    }
}
else
{
    ?><column id="default" width="50" type="ra" align="center" sort="str"><?php echo _l('Default'); ?></column><?php
}
?>
<column id="filter_used" width="0" type="ro" align="left" sort="int"></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_categorypanel').'</userdata>'."\n";
    if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
    {
        echo '<row '.
                        ' id="1">'.
                        '<cell>1</cell>'.
                        '<cell>0</cell>'.
                        '<cell icon="fa fa-folder yellow"><![CDATA['._l('Home').']]></cell>'.
                        '<cell>0</cell>'.
                        '<cell>0</cell>'.
            '</row>';
    }
    $id_root = 0;
    $ps_root = 0;
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $sql_root = 'SELECT *
                FROM '._DB_PREFIX_.'category
                WHERE id_parent = 0';
        $res_root = Db::getInstance()->ExecuteS($sql_root);
        if (!empty($res_root[0]['id_category']))
        {
            $ps_root = $res_root[0]['id_category'];
        }
    }
    if (!empty($ps_root))
    {
        $id_root = $ps_root;
    }
    $id_shop = SCI::getSelectedShop();
    if (SCMS && $id_shop > 0)
    {
        $shop = new Shop($id_shop);
        $categ = new Category($shop->id_category);
        $id_root = $categ->id_parent;
    }

    // ESERVICES
    $eservices_parent = 0;
    $eservices_cat_archived = SCI::getConfigurationValue('SC_ESERVICES_CATEGORYARCHIVED');
    $eservices_cat_id = SCI::getConfigurationValue('SC_ESERVICES_CATEGORY');
    $projects = array();
    $eservices_projects_byIdCat = array();
    $eservices_projects_authorizedStatus = array();
    $headers = array();
    $posts = array();
    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
    $posts['LICENSE'] = '#';
    $posts['URLCALLING'] = '#';
    if (defined('IS_SUBS') && IS_SUBS == '1')
    {
        $posts['SUBSCRIPTION'] = '1';
    }
    $ret = makeCallToOurApi('Fizz/Project/GetAll', $headers, $posts);
    if (!empty($ret['code']) && $ret['code'] == '200')
    {
        $projects = $ret['project'];
    }
    foreach ($projects as $key => $project)
    {
        $params = json_decode($project['params'], true);
        if (!empty($params['id_category']))
        {
            $eservices_projects_byIdCat[$params['id_category']] = $project;
        }

        if ($project['type'] == 'dixit')
        {
            $eservices_projects_authorizedStatus[$project['id_project']] = array('0', '1', '2', '3', '4', '7');
        }
    }

    $array_cats = array();
    $array_children_cats = array();

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)
    {
        $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
    }

    $sql = 'SELECT c.*, cl.name, c.position '.(((SCMS && $for_mb == 1) || (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)) && !empty($id_shop) ? ', cs.position' : '').'
            FROM '._DB_PREFIX_.'category c
            LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $id_lang.(((SCMS && $for_mb == 1) || (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)) && !empty($id_shop) ? ' AND cl.id_shop = '.(int) $id_shop : '').')
            '.(((SCMS && $for_mb == 1) || (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)) && !empty($id_shop) ? ' INNER JOIN '._DB_PREFIX_.'category_shop cs ON (cs.id_category=c.id_category AND cs.id_shop='.(int) $id_shop.') ' : '').'
            GROUP BY c.id_category
            ORDER BY c.`nleft` ASC';
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $k => $row)
    {
        $array_cats[$row['id_category']] = $row;

        if (!isset($array_children_cats[$row['id_parent']]))
        {
            $array_children_cats[$row['id_parent']] = array();
        }
        $array_children_cats[$row['id_parent']][str_pad($row['position'], 5, '0', STR_PAD_LEFT).str_pad($row['id_category'], 12, '0', STR_PAD_LEFT)] = $row['id_category'];
    }

    getLevelFromDB_PHP($id_root);
?>
</rows>
