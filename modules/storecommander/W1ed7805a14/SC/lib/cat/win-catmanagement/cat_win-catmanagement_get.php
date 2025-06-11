<?php

$id_lang = (int) Tools::getValue('id_lang');
$id_shop = (int) Tools::getValue('id_shop', 0);

$root_cat = array();
if (SCMS)
{
    $shops = Shop::getShops(false, null, true);
    foreach ($shops as $shop_id)
    {
        $shopObjet = new Shop($shop_id);
        $root_cat_temp = Category::getRootCategory($id_lang, $shopObjet);
        $root_cat[] = $root_cat_temp->id;
    }
}

// parent_id => catégorie parente
// others : true => on veut toutes les catégories situées dans la catégorie parente
// id_start : si others=false, on ne veut que la catégorie id_start située dans la catégorie parente
function getLevelFromDB($parent_id, $others = true, $id_start = 0, $is_bin = false)
{
    global $id_lang,$id_shop,$root_cat,$user_lang_iso;

    if (!empty($parent_id) || $parent_id === 0)
    {
        $where = '';
        if (!$others && !empty($id_start))
        {
            $where .= " AND c.id_category='".(int) $id_start."'";
        }

        if (SCMS && !empty($id_shop) && !$is_bin)
        {
            $sql = 'SELECT cl.*,c.*
                FROM '._DB_PREFIX_.'category c
                    LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $id_lang.' AND cl.id_shop='.(int) $id_shop.')
                    INNER JOIN '._DB_PREFIX_.'category_shop cs ON (cs.id_category=c.id_category AND cs.id_shop='.(int) $id_shop.')
                WHERE c.id_parent='.(int) $parent_id.'
                    '.$where.'
                GROUP BY c.id_category
                ORDER BY c.nleft';
        }
        else
        {
            $sql = 'SELECT cl.*,c.*
                FROM '._DB_PREFIX_.'category c
                    LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $id_lang.')
                WHERE c.id_parent='.(int) $parent_id.'
                    '.$where.'
                GROUP BY c.id_category
                ORDER BY c.nleft';
        }
        $res = Db::getInstance()->ExecuteS($sql);
        if (!empty($res))
        {
            foreach ($res as $k => $row)
            {
                if (!empty($row['id_category']))
                {
                    $style = '';

                    if ($row['name'] == '')
                    {
                        $sql2 = 'SELECT name FROM '._DB_PREFIX_.'category_lang
                                WHERE id_lang='.(int) Configuration::get('PS_LANG_DEFAULT').'
                                    AND id_category='.$row['id_category'];
                        $res2 = Db::getInstance()->getRow($sql2);
                        $style = 'background:lightblue';
                    }

                    $description = strip_tags($row['description']);

                    if (SCMS)
                    {
                        $shops = '';
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $sql_shop = 'SELECT s.name
                                FROM '._DB_PREFIX_.'category_shop cs
                                    INNER JOIN '._DB_PREFIX_.'shop s ON (cs.id_shop=s.id_shop)
                                WHERE cs.id_category='.(int) $row['id_category'].'
                                ORDER BY s.name';
                            $res_shop = Db::getInstance()->executeS($sql_shop);
                            foreach ($res_shop as $shop)
                            {
                                if (!empty($shop['name']))
                                {
                                    if (!empty($shops))
                                    {
                                        $shops .= ',';
                                    }
                                    $shops .= $shop['name'];
                                }
                            }
                        }
                    }

                    $nb_product = 0;
                    $sql_nb_product = 'SELECT id_product
                            FROM '._DB_PREFIX_.'category_product
                            WHERE id_category='.(int) $row['id_category'];
                    $res_nb_product = Db::getInstance()->executeS($sql_nb_product);
                    if (!empty($res_nb_product))
                    {
                        $nb_product = count($res_nb_product);
                    }

                    $nb_product_seo = 0;
                    if (empty($id_shop) || !SCMS)
                    {
                        $sql_nb_product_seo = 'SELECT id_product
                            FROM '._DB_PREFIX_."product
                            WHERE id_category_default='".(int) $row['id_category']."'";
                    }
                    else
                    {
                        $sql_nb_product_seo = 'SELECT id_product
                            FROM '._DB_PREFIX_."product_shop
                            WHERE id_category_default='".(int) $row['id_category']."'
                                AND id_shop = '".(int) $id_shop."'";
                    }
                    $res_nb_product_seo = Db::getInstance()->executeS($sql_nb_product_seo);
                    if (!empty($res_nb_product_seo))
                    {
                        $nb_product_seo = count($res_nb_product_seo);
                    }

                    $image = '';
                    $filename = '/'.(int) $row['id_category'].'-'._s('CAT_PROD_GRID_IMAGE_SIZE').'.jpg';
                    @$checkfile = filemtime(_PS_CAT_IMG_DIR_.$filename);
                    if ($checkfile !== false)
                    {
                        $image = '<img src="'.SC_PS_PATH_REL.'img/c'.$filename.'?'.$checkfile.'" height="'.getGridImageHeight().'px" alt="" />';
                    }
                    else
                    {
                        $image = '<img src="'.SC_PS_PATH_REL.'img/c/'.(int) $row['id_category'].'.jpg'.'?'.$checkfile.'" height="'.getGridImageHeight().'px" alt="" />';
                    }
                    if (empty($image))
                    {
                        $image = '<img src="'.SC_PS_PATH_REL.'img/c/'.$user_lang_iso.'.jpg" height="'.getGridImageHeight().'px" alt="" />';
                    }

                    $thumbnail = '';
                    $filename = '/'.(int) $row['id_category'].'-'._s('CAT_PROD_GRID_IMAGE_SIZE').'_thumb.jpg';
                    @$checkfile = filemtime(_PS_CAT_IMG_DIR_.$filename);
                    if ($checkfile !== false)
                    {
                        $thumbnail = '<img src="'.SC_PS_PATH_REL.'img/c'.$filename.'?'.$checkfile.'" height="'.getGridImageHeight().'px" alt="" />';
                    }
                    else
                    {
                        if (file_exists(SC_PS_PATH_REL.'img/c/'.(int) $row['id_category'].'_thumb.jpg'))
                        {
                            $thumbnail = '<img src="'.SC_PS_PATH_REL.'img/c/'.(int) $row['id_category'].'_thumb.jpg'.'?'.$checkfile.'" height="'.getGridImageHeight().'px" alt="" />';
                        }
                    }
                    if (empty($thumbnail))
                    {
                        $thumbnail = '<img src="'.SC_PS_PATH_REL.'img/c/'.$user_lang_iso.'.jpg" height="'.getGridImageHeight().'px" alt="" />';
                    }

                    $is_root = false;
                    if ($row['id_parent'] == 0)
                    {
                        $is_root = true;
                    }

                    $is_home = false;
                    if (SCMS && sc_in_array($row['id_category'], $root_cat, 'catWinCatManagGet_rootcatgetLevelFromDB'))
                    {
                        $is_home = true;
                    }

                    $not_deletable = false;
                    if ($is_home || $is_root)
                    {
                        $not_deletable = true;
                    }

                    $is_recycle_bin = false;
                    if ($row['name'] == _l('SC Recycle Bin') || $row['name'] == ('SC Recycle Bin'))
                    {
                        $is_recycle_bin = true;
                    }

                    $icon = ($row['active'] ? 'fa fa-folder yellow' : 'fad fa-folder grey');

                    if ($is_recycle_bin)
                    {
                        $row['name'] = _l('SC Recycle Bin');
                        $icon = 'fa fa-trash-alt red';
                    }
                    if ($is_home)
                    {
                        $icon = 'fa fa-folder-open';
                    }

                    echo '<row style="'.$style.'"'.
                                        ' id="'.$row['id_category'].'"'.($parent_id == 0 ? ' open="1"' : '').'>'.
                                        '<cell icon="'.$icon.'"><![CDATA[ '.($style == '' ? $row['name'] : _l('To Translate:').' '.$res2['name']).']]></cell>';
                    if (SCMS)
                    {
                        echo '<cell><![CDATA['.$shops.']]></cell>';
                    }
                    echo '<cell>'.$row['id_category'].'</cell>'
//                                        .'<cell><![CDATA['.$row['position'].']]></cell>'
                                        .'<cell><![CDATA['.$image.']]></cell>'
                                        .'<cell><![CDATA['.$thumbnail.']]></cell>'
                                        .'<cell><![CDATA['.($style == '' ? $row['name'] : _l('To Translate:').' '.$res2['name']).']]></cell>'
                                        .'<cell><![CDATA['.$description.']]></cell>'
                                        .'<cell>'.$nb_product.'</cell>'
                                        .'<cell>'.$nb_product_seo.'</cell>'
                                        .'<cell>'.$row['active'].'</cell>';

                    echo '      <userdata name="not_deletable">'.(int) $not_deletable.'</userdata>';
                    echo '      <userdata name="is_recycle_bin">'.(int) $is_recycle_bin.'</userdata>';
                    echo '      <userdata name="is_home">'.(int) $is_home.'</userdata>';
                    echo '      <userdata name="is_root">'.(int) $is_root.'</userdata>';

                    getLevelFromDB($row['id_category'], true, 0, false);
                    echo '</row>'."\n";
                }
            }
        }
    }
}

function getLevelFromDB_PHP($id_parent, $others = true, $id_start = 0, $limit_to_shop = false)
{
    global $id_lang,$id_shop,$root_cat,$array_cats,$user_lang_iso,$array_children_cats;
    if (!empty($array_children_cats[$id_parent]))
    {
        ksort($array_children_cats[$id_parent]);
        foreach ($array_children_cats[$id_parent] as $k => $id)
        {
            $row = $array_cats[$id];
            if (!$others && !empty($id_start) && $id_start != $row['id_category'])
            {
                continue;
            }
            if (empty($row['id_category']))
            {
                continue;
            }

            $style = '';

            if ($row['name'] == '')
            {
                $sql2 = 'SELECT name FROM '._DB_PREFIX_.'category_lang
                                WHERE id_lang='.(int) Configuration::get('PS_LANG_DEFAULT').'
                                    AND id_category='.$row['id_category'];
                $res2 = Db::getInstance()->getRow($sql2);
                $style = 'background:lightblue';
            }

            $description = strip_tags($row['description']);

            if (SCMS)
            {
                $in_shop = false;
                $shops = '';
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $sql_shop = 'SELECT s.name, s.id_shop
                                FROM '._DB_PREFIX_.'category_shop cs
                                    INNER JOIN '._DB_PREFIX_.'shop s ON (cs.id_shop=s.id_shop)
                                WHERE cs.id_category='.(int) $row['id_category'].'
                                ORDER BY s.name';
                    $res_shop = Db::getInstance()->executeS($sql_shop);
                    foreach ($res_shop as $shop)
                    {
                        if (!empty($shop['name']))
                        {
                            if (!empty($shops))
                            {
                                $shops .= ',';
                            }
                            $shops .= $shop['name'];
                        }
                        if (!empty($shop['id_shop']) && !empty($id_shop) && $shop['id_shop'] == $id_shop)
                        {
                            $in_shop = true;
                        }
                    }
                }
                if (!$in_shop && !empty($limit_to_shop))
                {
                    continue;
                }
            }

            $nb_product = 0;
            $sql_nb_product = 'SELECT id_product
                            FROM '._DB_PREFIX_.'category_product
                            WHERE id_category='.(int) $row['id_category'];
            $res_nb_product = Db::getInstance()->executeS($sql_nb_product);
            if (!empty($res_nb_product))
            {
                $nb_product = count($res_nb_product);
            }

            $nb_product_seo = 0;
            if (empty($id_shop) || !SCMS)
            {
                $sql_nb_product_seo = 'SELECT id_product
                            FROM '._DB_PREFIX_."product
                            WHERE id_category_default='".(int) $row['id_category']."'";
            }
            else
            {
                $sql_nb_product_seo = 'SELECT id_product
                            FROM '._DB_PREFIX_."product_shop
                            WHERE id_category_default='".(int) $row['id_category']."'
                                AND id_shop = '".(int) $id_shop."'";
            }
            $res_nb_product_seo = Db::getInstance()->executeS($sql_nb_product_seo);
            if (!empty($res_nb_product_seo))
            {
                $nb_product_seo = count($res_nb_product_seo);
            }

            $image = '';
            $filename = (int) $row['id_category'].'-'._s('CAT_PROD_GRID_IMAGE_SIZE').'.jpg';
            $checkfile = @filemtime(_PS_CAT_IMG_DIR_.$filename);
            if ($checkfile !== false)
            {
                $image = '<img src="'.SC_PS_PATH_REL.'img/c/'.$filename.'?'.$checkfile.'" height="'.getGridImageHeight().'px" alt="" />';
            }
            elseif (file_exists(SC_PS_PATH_REL.'img/c/'.(int) $row['id_category'].'.jpg'))
            {
                $image = '<img src="'.SC_PS_PATH_REL.'img/c/'.(int) $row['id_category'].'.jpg'.'?'.$checkfile.'" height="'.getGridImageHeight().'px" alt="" />';
            }
            if (empty($image))
            {
                if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                {
                    $image = '<img src="'.SC_PS_PATH_REL.'img/404.gif" height="'.getGridImageHeight().'px" alt="" />';
                }
                elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $image = '<img src="'.SC_PS_PATH_REL.'img/c/fr.jpg" height="'.getGridImageHeight().'px" alt="" />';
                }
                else
                {
                    $image = '<img src="'.SC_PS_PATH_REL.'img/c/en.jpg" height="'.getGridImageHeight().'px" alt="" />';
                }
            }

            $thumbnail = '';
            $filename = (int) $row['id_category'].'-'._s('CAT_PROD_GRID_IMAGE_SIZE').'_thumb.jpg';
            $checkfile = @filemtime(_PS_CAT_IMG_DIR_.$filename);
            if ($checkfile !== false)
            {
                $thumbnail = '<img src="'.SC_PS_PATH_REL.'img/c/'.$filename.'?'.$checkfile.'" height="'.getGridImageHeight().'px" alt="" />';
            }
            elseif (file_exists(SC_PS_PATH_REL.'img/c/'.(int) $row['id_category'].'_thumb.jpg'))
            {
                $thumbnail = '<img src="'.SC_PS_PATH_REL.'img/c/'.(int) $row['id_category'].'_thumb.jpg'.'?'.$checkfile.'" height="'.getGridImageHeight().'px" alt="" />';
            }
            if (empty($thumbnail))
            {
                if (file_exists(_PS_TMP_IMG_DIR_.'category_'.$row['id_category'].'-thumb.jpg'))
                {
                    $thumbnail = '<img src="'.SC_PS_PATH_REL.'img/tmp/category_'.$row['id_category'].'-thumb.jpg'.'?'.time().'" style="max-width:100%;" alt="" />';
                }
                else
                {
                    $thumbnail = '<img src="'.SC_PS_PATH_REL.'img/c/'.$user_lang_iso.'.jpg" height="'.getGridImageHeight().'px" alt="" />';
                }
            }

            $is_root = false;
            if ($row['id_parent'] == 0)
            {
                $is_root = true;
            }

            $is_home = false;
            if (SCMS && sc_in_array($row['id_category'], $root_cat, 'catWinCatManagGet_rootcatgetLevelFromDB_PHP'))
            {
                $is_home = true;
            }

            $not_deletable = false;
            if ($is_home || $is_root)
            {
                $not_deletable = true;
            }

            $is_recycle_bin = false;
            if ($row['name'] == _l('SC Recycle Bin') || $row['name'] == ('SC Recycle Bin'))
            {
                $is_recycle_bin = true;
            }

            $icon = ($row['active'] ? 'fa fa-folder yellow' : 'fad fa-folder grey');

            if ($is_recycle_bin)
            {
                $row['name'] = _l('SC Recycle Bin');
                $icon = 'fa fa-trash-alt red';
            }
            if ($is_home)
            {
                $icon = 'fa fa-folder-open';
            }

            echo '<row style="'.$style.'"'.
                    ' id="'.$row['id_category'].'"'.($row['id_parent'] == 0 ? ' open="1"' : '').'>'.
                    '<cell icon="'.$icon.'"><![CDATA[ '.($style == '' ? $row['name'] : _l('To Translate:').' '.$res2['name']).']]></cell>';
            if (SCMS)
            {
                echo '<cell><![CDATA['.$shops.']]></cell>';
            }
            echo '<cell>'.$row['id_category'].'</cell>'
//                    .'<cell><![CDATA['.$row['position'].']]></cell>'
                    .'<cell><![CDATA['.$image.']]></cell>'
                    .'<cell><![CDATA['.$thumbnail.']]></cell>'
                    .'<cell><![CDATA['.($style == '' ? $row['name'] : _l('To Translate:').' '.$res2['name']).']]></cell>'
                    .'<cell><![CDATA['.$description.']]></cell>'
                    .'<cell>'.$nb_product.'</cell>'
                    .'<cell>'.$nb_product_seo.'</cell>'
                    .'<cell>'.$row['active'].'</cell>';

            echo SC_Ext::readCustomCategoriesGridConfigXML('addRowValueInGet', $row);

            echo '      <userdata name="not_deletable">'.(int) $not_deletable.'</userdata>';
            echo '      <userdata name="is_recycle_bin">'.(int) $is_recycle_bin.'</userdata>';
            echo '      <userdata name="is_home">'.(int) $is_home.'</userdata>';
            echo '      <userdata name="is_root">'.(int) $is_root.'</userdata>';
            echo '      <userdata name="enableClick_tree">0</userdata>';
            echo '      <userdata name="enableClick_shops">0</userdata>';
            echo '      <userdata name="enableClick_id_category">0</userdata>';
            echo '      <userdata name="enableClick_image">0</userdata>';
            echo '      <userdata name="enableClick_name">1</userdata>';
//            echo '      <userdata name="enableClick_position">1</userdata>';
            echo '      <userdata name="enableClick_description">0</userdata>';
            echo '      <userdata name="enableClick_nb_products">0</userdata>';
            echo '      <userdata name="enableClick_nb_products_seo">0</userdata>';
            echo '      <userdata name="enableClick_active">1</userdata>';

            getLevelFromDB_PHP($row['id_category'], true, 0, $limit_to_shop);
            echo '</row>'."\n";
        }
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
<!--<call command="attachHeader"><param><![CDATA[#text_filter--><?php //if (SCMS) {?><!--,#text_filter--><?php //}?><!--,#numeric_filter,#numeric_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#select_filter--><?php //echo SC_Ext::readCustomCategoriesGridConfigXML('addFilterInGet');?><!--]]></param></call>-->
<call command="attachHeader"><param><![CDATA[#text_filter<?php if (SCMS) { ?>,#text_filter<?php } ?>,#numeric_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#select_filter<?php echo SC_Ext::readCustomCategoriesGridConfigXML('addFilterInGet'); ?>]]></param></call>
</beforeInit>
<column id="tree" width="250" type="tree" align="left" sort="na"><?php echo _l('Categories'); ?></column>
<?php if (SCMS) { ?>
<column id="shops" width="100" type="ro" align="left" sort="na"><?php echo _l('Shops'); ?></column>
<?php } ?>
<column id="id_category" width="40" type="ro" align="right" sort="na"><?php echo _l('ID'); ?></column>
<!--<column id="position" width="80" type="ed" align="center" sort="na">--><?php //echo _l('Position');?><!--</column>-->
<column id="image" width="80" type="ro" align="center" sort="na"><?php echo _l('Image'); ?></column>
<column id="thumnail" width="80" type="ro" align="center" sort="na"><?php echo _l('Image thumbnail'); ?></column>
<column id="name" width="120" type="ed" align="left" sort="na"><?php echo _l('Name'); ?></column>
<column id="description" width="200" type="ro" align="left" sort="na"><?php echo _l('Description'); ?></column>
<column id="nb_products" width="40" type="ro" align="right" sort="na"><?php echo _l('Products nb'); ?></column>
<column id="nb_products_seo" width="40" type="ro" align="right" sort="na"><?php echo _l('SEO products nb'); ?></column>
<column id="active" width="45" type="coro" align="center" sort="na"><?php echo _l('Active'); ?>
    <option value="0"><?php echo _l('No'); ?></option>
    <option value="1"><?php echo _l('Yes'); ?></option>
</column>
<?php echo SC_Ext::readCustomCategoriesGridConfigXML('addHeaderInGet');
?>
</head>
<?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cat_catmanagement_treegrid').'</userdata>'."\n";
    $init = 0;
    $ps_root = 0;
    $sql_root = 'SELECT *
            FROM '._DB_PREFIX_.'category
            WHERE id_parent = 0';
    $res_root = Db::getInstance()->ExecuteS($sql_root);
    if (!empty($res_root[0]['id_category']))
    {
        $ps_root = $res_root[0]['id_category'];
    }
    $others = true;
    $id_start = 0;

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)
        {
            $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        }

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop))
    {
        $shop = new Shop($id_shop);
        $sql = 'SELECT c.id_parent
                    FROM `'._DB_PREFIX_.'category` c
                    WHERE c.`id_category` = "'.(int) $shop->id_category.'"';
        $res = Db::getInstance()->getRow($sql);
        $init = $res['id_parent'];
        $others = false;
        $id_start = $shop->id_category;
    }
    echo '<userdata name="parent_root">'.$init.'</userdata>'."\n";

    $array_cats = array();
    $array_children_cats = array();

    $sql = 'SELECT c.*, cl.*, c.position '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop) ? ', cs.position' : '').'
            FROM '._DB_PREFIX_.'category c
            LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop) ? " AND cl.id_shop='".(int) $id_shop."'" : '').')
            '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop) ? ' INNER JOIN '._DB_PREFIX_."category_shop cs ON (cs.id_category=c.id_category AND cs.id_shop='".(int) $id_shop."') " : '').'
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

    getLevelFromDB_PHP($init, $others, $id_start, true);

    // BIN
    $sql = 'SELECT c.*, cl.*
                    FROM '._DB_PREFIX_.'category c
                    LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $sc_agent->id_lang.")
                    WHERE cl.name LIKE '%SC Recycle Bin' OR cl.name LIKE '%".psql(_l('SC Recycle Bin'))."'
                    GROUP BY c.id_category";
    $res = Db::getInstance()->ExecuteS($sql);
    $bincategory = 0;
    $bincategory_nleft = 0;
    $bincategory_nright = 0;
    $bincategory_parent = 0;
    if (count($res) > 0)
    {
        $bincategory = $res[0]['id_category'];
        $bincategory_nleft = $res[0]['nleft'];
        $bincategory_nright = $res[0]['nright'];
        $bincategory_parent = $res[0]['id_parent'];

        if (!$others)
        {
            $cat_parent = new Category($id_start);

            if (!($cat_parent->nleft < $bincategory_nleft && $bincategory_nright < $cat_parent->nright))
            {
                getLevelFromDB_PHP($bincategory_parent, false, $bincategory, false);
            }
        }
        else
        {
            $cat_parent = new Category($init);

            if (!($cat_parent->nleft < $bincategory_nleft && $bincategory_nright < $cat_parent->nright))
            {
                getLevelFromDB_PHP($bincategory_parent, false, $bincategory, false);
            }
        }
    }
?>
</rows>
