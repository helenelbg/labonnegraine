<?php

    $idlist = Tools::getValue('idlist', 0);
    $id_lang = (int) Tools::getValue('id_lang');
    $forceAllProducts = (int) Tools::getValue('forceAllProducts', 0);
    $cntProducts = count(explode(',', $idlist));
    $accessory_filter = (int) Tools::getValue('accessory_filter', 0);
    $accessoriesFilterByProducts = (int) Tools::getValue('accessoriesFilterByProducts', 0);
    $id_category = Tools::getValue('id_category', 0);
    $filter_params = Tools::getValue('filter_params', '');
    if (!empty($filter_params))
    {
        $ar = $filter_params;
        $forceAllProducts = true;
        $filter_params = '';
        $filters = explode(',', $ar);
        foreach ($filters as $filter)
        {
            list($field, $search) = explode('|||', $filter);
            if (!empty($field) && !empty($search))
            {
                if (!empty($filter_params))
                {
                    $filter_params .= ',';
                }
                $filter_params .= $field.'|||'.$search;
            }
        }
    }
    $used = array();

    function getAccessories()
    {
        global $idlist,$id_lang,$forceAllProducts,$used,$accessory_filter,$accessoriesFilterByProducts,$id_category,$filter_params;
        if ($forceAllProducts == 0)
        {
            $sql = 'SELECT a.id_product_2,p.reference,pl.name,s.name AS supName,m.name AS manName '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ', ps.active, p.id_shop_default, ps.id_category_default' : ', p.active, p.id_category_default').(_s('CAT_PROPERTIES_ACCESSORY_IMAGE') ? ', img.id_image' : '').'
                    FROM '._DB_PREFIX_.'accessory a
                    LEFT JOIN '._DB_PREFIX_.'product p ON (a.id_product_2=p.id_product)
                    LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (a.id_product_2=pl.id_product AND pl.id_lang='.(int) $id_lang.' '.(SCMS ? (SCI::getSelectedShop() > 0 ? ' AND pl.id_shop='.(int) SCI::getSelectedShop() : ' AND pl.id_shop=p.id_shop_default ') : '').')
                    '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product=ps.id_product AND ps.id_shop='.(SCI::getSelectedShop() > 0 ? (int) SCI::getSelectedShop() : 'p.id_shop_default').')' : '').'
                    '.(_s('CAT_PROPERTIES_ACCESSORY_IMAGE') ? 'LEFT JOIN '._DB_PREFIX_.'image img ON img.id_product = p.id_product AND img.cover = 1' : '').'
                    LEFT JOIN '._DB_PREFIX_.'supplier s ON (p.id_supplier=s.id_supplier)
                    LEFT JOIN '._DB_PREFIX_.'supplier_lang sl ON (s.id_supplier=sl.id_supplier AND sl.id_lang='.(int) $id_lang.')
                    LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (p.id_manufacturer=m.id_manufacturer)
                    LEFT JOIN '._DB_PREFIX_.'manufacturer_lang ml ON (m.id_manufacturer=ml.id_manufacturer AND ml.id_lang='.(int) $id_lang.')
                    '.(($accessory_filter) ? 'LEFT JOIN '._DB_PREFIX_.'category_product cp ON (a.id_product_1=cp.id_product)
                            WHERE cp.id_category ='.(int) $id_category : ' ').
                    (($accessoriesFilterByProducts) ? 'WHERE a.id_product_1 IN ('.pInSQL($idlist).')' : ' ');
            if (!empty($filter_params))
            {
                $sql_filter = array();
                $filters = explode(',', $filter_params);
                $condition = (!empty($accessory_filter) || $accessoriesFilterByProducts ? ' AND ' : ' WHERE ');
                foreach ($filters as $filter)
                {
                    list($field, $search) = explode('|||', $filter);
                    if (!empty($field) && !empty($search))
                    {
                        switch ($field){
                            case 'id':
                                $sql_filter[] = 'p.`id_product` = '.(int) $search;
                                break;
                            case 'name':
                                $sql_filter[] = 'pl.`'.bqSQL($field)."` LIKE '%".pSQL($search)."%'";
                                break;
                            default:
                                $sql_filter[] = 'p.`'.bqSQL($field)."` LIKE '%".pSQL($search)."%'";
                                break;
                        }
                    }
                }
                if (!empty($sql_filter))
                {
                    foreach ($sql_filter as $k => $ro)
                    {
                        if ($k == 0)
                        {
                            $sql_filter[$k] = $condition.' '.$ro;
                        }
                        else
                        {
                            $sql_filter[$k] = 'AND '.$ro;
                        }
                    }
                    $sql .= implode(' ', $sql_filter);
                }
            }
            $sql .= ' GROUP BY a.id_product_2
                    ORDER BY pl.name ASC';
            $res = Db::getInstance()->ExecuteS($sql);
            $sql2 = 'SELECT DISTINCT a.id_product_2
                    FROM '._DB_PREFIX_.'accessory a
                    WHERE a.id_product_1 IN ('.pInSQL($idlist).')';
            $res2 = Db::getInstance()->ExecuteS($sql2);
            foreach ($res2 as $row2)
            {
                $used[$row2['id_product_2']] = 1;
            }
        }
        else
        {
            $sql = 'SELECT p.id_product AS id_product_2,p.reference,pl.name,s.name AS supName,m.name AS manName '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? ', ps.active, p.id_shop_default, ps.id_category_default' : ', p.active, p.id_category_default').(_s('CAT_PROPERTIES_ACCESSORY_IMAGE') ? ', img.id_image' : '').'
                    FROM '._DB_PREFIX_.'product p
                    LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (p.id_product=pl.id_product AND pl.id_lang='.(int) $id_lang.' '.(SCMS ? (SCI::getSelectedShop() > 0 ? ' AND pl.id_shop='.(int) SCI::getSelectedShop() : ' AND pl.id_shop=p.id_shop_default ') : '').')
                    '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product=ps.id_product AND ps.id_shop='.(SCI::getSelectedShop() > 0 ? (int) SCI::getSelectedShop() : 'p.id_shop_default').')' : '').'
                    '.(_s('CAT_PROPERTIES_ACCESSORY_IMAGE') ? 'LEFT JOIN '._DB_PREFIX_.'image img ON img.id_product = p.id_product AND img.cover = 1' : '').'
                    LEFT JOIN '._DB_PREFIX_.'supplier s ON (p.id_supplier=s.id_supplier)
                    LEFT JOIN '._DB_PREFIX_.'supplier_lang sl ON (s.id_supplier=sl.id_supplier AND sl.id_lang='.(int) $id_lang.')
                    LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (p.id_manufacturer=m.id_manufacturer)
                    LEFT JOIN '._DB_PREFIX_.'manufacturer_lang ml ON (m.id_manufacturer=ml.id_manufacturer AND ml.id_lang='.(int) $id_lang.')
                    '.(($accessory_filter) ? 'LEFT JOIN '._DB_PREFIX_.'category_product cp ON (p.id_product=cp.id_product)
                            WHERE cp.id_category ='.(int) $id_category.' ' : ' ').
                    (($accessoriesFilterByProducts) ? 'WHERE a.id_product_1 IN ('.pInSQL($idlist).') ' : '');
            if (!empty($filter_params))
            {
                $sql_filter = array();
                $filters = explode(',', $filter_params);
                $condition = (!empty($accessory_filter) || $accessoriesFilterByProducts ? ' AND ' : ' WHERE ');
                foreach ($filters as $filter)
                {
                    list($field, $search) = explode('|||', $filter);
                    if (!empty($field) && !empty($search))
                    {
                        switch ($field){
                            case 'id':
                                $sql_filter[] = 'p.`id_product` = '.(int) $search;
                                break;
                            case 'name':
                                $sql_filter[] = 'pl.`'.bqSQL($field)."` LIKE '%".pSQL($search)."%'";
                                break;
                            default:
                                $sql_filter[] = 'p.`'.bqSQL($field)."` LIKE '%".pSQL($search)."%'";
                                break;
                        }
                    }
                }
                if (!empty($sql_filter))
                {
                    foreach ($sql_filter as $k => $ro)
                    {
                        if ($k == 0)
                        {
                            $sql_filter[$k] = $condition.' '.$ro;
                        }
                        else
                        {
                            $sql_filter[$k] = 'AND '.$ro;
                        }
                    }
                    $sql .= implode(' ', $sql_filter);
                }
            }
            $sql .= '
                    GROUP BY id_product_2
                    ORDER BY pl.name ASC';
            if (empty($filter_params))
            {
                $sql .= ' LIMIT 1000';
            }
            $res = Db::getInstance()->ExecuteS($sql);
            $sql2 = 'SELECT DISTINCT a.id_product_2
                    FROM '._DB_PREFIX_.'accessory a
                    WHERE a.id_product_1 IN ('.pInSQL($idlist).')';
            $res2 = Db::getInstance()->ExecuteS($sql2);
            foreach ($res2 as $row2)
            {
                $used[$row2['id_product_2']] = 1;
            }
        }
        foreach ($res as $row)
        {
            echo '<row id="'.$row['id_product_2'].'">';
            echo '<userdata name="id_category_default">'.$row['id_category_default'].'</userdata>';
            if (SCMS)
            {
                echo '<userdata name="id_shop_default">'.$row['id_shop_default'].'</userdata>';
            }
            echo '<cell>'.$row['id_product_2'].'</cell>';
            echo '<cell>'.(sc_array_key_exists($row['id_product_2'], $used) ? 1 : 0).'</cell>';
            echo '<cell><![CDATA['.$row['active'].']]></cell>';
            if (_s('CAT_PROPERTIES_ACCESSORY_IMAGE'))
            {
                $defaultimg = 'lib/img/i.gif';
                if (!empty($row['id_image']))
                {
                    if (file_exists(SC_PS_PATH_REL.'img/p/'.getImgPath((int) $row['id_product_2'], (int) $row['id_image'], _s('CAT_PROD_GRID_IMAGE_SIZE'))))
                    {
                        echo "<cell><![CDATA[<img src='".SC_PS_PATH_REL.'img/p/'.getImgPath((int) $row['id_product_2'], (int) $row['id_image'], _s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>]]></cell>";
                    }
                    else
                    {
                        echo '<cell><![CDATA[<i class="fad fa-file-image" ></i>--]]></cell>';
                    }
                }
                else
                {
                    echo '<cell><![CDATA[<i class="fad fa-file-image" ></i>--]]></cell>';
                }
            }
            echo '<cell><![CDATA['.$row['reference'].']]></cell>';
            echo '<cell style="color:'.($row['active'] ? '#000000' : '#888888').'"><![CDATA['.$row['name'].']]></cell>';
            echo '<cell><![CDATA['.$row['supName'].']]></cell>';
            echo '<cell><![CDATA['.$row['manName'].']]></cell>';
            echo '</row>';
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
<rows>
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#numeric_filter,,#select_filter,<?php echo _s('CAT_PROPERTIES_ACCESSORY_IMAGE') ? ',' : ''; ?>#text_filter,#text_filter,#text_filter,#text_filter]]></param></call>
</beforeInit>
<column id="id" width="50" type="ro" align="right" sort="int"><?php echo _l('ID'); ?></column>
<column id="used" width="50" type="ch" align="center" sort="int"><?php echo _l('Used'); ?></column>
<column id="active" width="45" type="coro" align="center" sort="int"><?php echo _l('Active'); ?>
    <option value="0"><?php echo _l('No'); ?></option>
    <option value="1"><?php echo _l('Yes'); ?></option>
</column>
<?php if (_s('CAT_PROPERTIES_ACCESSORY_IMAGE')) { ?>
<column id="used" width="100" type="ro" align="center" sort="int"><?php echo _l('Image'); ?></column>
<?php }?>
<column id="reference" width="120" type="ro" align="left" sort="str"><?php echo _l('Reference'); ?></column>
<column id="name" width="200" type="ro" align="left" sort="str"><?php echo _l('Name'); ?></column>
<column id="supplier" width="120" type="ro" align="left" sort="str"><?php echo _l('Supplier'); ?></column>
<column id="manufacturer" width="120" type="ro" align="left" sort="str"><?php echo _l('Manufacturer'); ?></column>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_accessory').'</userdata>'."\n";
    getAccessories();

?>
</rows>