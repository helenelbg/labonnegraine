<?php

function getLevelFromDB($parent_id, $eservices_parent = 0, $dynamic_call = null)
{
    global $id_lang,$id_shop,$binPresent,$forceDisplayAllCategories,$root_cat,$id_root,$eservices_cat_id,$eservices_cat_archived,$eservices_projects_byIdCat,$eservices_projects_authorizedStatus;
    if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
    {
        $sql = 'SELECT c.active,c.id_category,name, c.id_parent FROM '._DB_PREFIX_.'category c
                            LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $id_lang.')
                            WHERE c.id_parent='.(int) $parent_id.'
                            GROUP BY c.id_category
                            ORDER BY c.position';
    }
    else
    {
        $sql = 'SELECT c.active,c.id_category,name, c.id_parent FROM '._DB_PREFIX_.'category c
                            LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $id_lang.' '.($id_shop > 0 ? 'AND cl.id_shop='.(int) $id_shop : '').')
                            '.(!$forceDisplayAllCategories && $id_shop && (int) $parent_id > 0 ? 'LEFT JOIN '._DB_PREFIX_.'category_shop cs ON (cs.id_category=c.id_category)' : '').'
                            WHERE c.id_parent='.(int) $parent_id.'
                            '.(!$forceDisplayAllCategories && $id_shop && (int) $parent_id > 0 ? ' AND cs.id_shop='.(int) $id_shop : '').'
                            GROUP BY c.id_category
                            '.(!$forceDisplayAllCategories && $id_shop && (int) $parent_id > 0 ? 'ORDER BY cs.position' : 'ORDER BY c.position').'
                            ';
    }
    $res = Db::getInstance()->ExecuteS($sql);
    foreach ($res as $k => $row)
    {
        ## on enlève les eservices des catégories générales
        if (!empty($eservices_cat_id) && $eservices_cat_id == $row['id_category'])
        {
            continue;
        }
        $style = '';
        if ($row['name'] == 'SoColissimo')
        {
            continue;
        }
        if ($row['name'] == '')
        {
            $sql2 = 'SELECT name FROM '._DB_PREFIX_.'category_lang
                                    WHERE id_lang='.(int) Configuration::get('PS_LANG_DEFAULT').'
                                        AND id_category='.(int) $row['id_category'];
            $res2 = Db::getInstance()->getRow($sql2);
            $style = 'style="background:lightblue" ';
        }
        $icon = ($row['active'] ? 'catalog.png' : 'folder_grey.png');
        if ($row['name'] == 'SC Recycle Bin')
        {
            $icon = 'folder_delete.png';
            $binPresent = true;
            if (!_r('ACT_CAT_DELETE_PRODUCT_COMBI'))
            {
                continue;
            }
        }

        $is_root = false;
        if ($row['id_parent'] == 0)
        {
            $is_root = true;
        }

        $is_home = false;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && sc_in_array($row['id_category'], $root_cat, 'catCategoryGet_rootcatgetLevelFromDB'))
        {
            $icon = 'folder_table.png';
            $is_home = true;
        }

        $is_eservices = 0;
        $in_eservices = 0;
        if ($eservices_cat_id == $row['id_category'])
        {
            $icon = 'ruby.png';
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
                $icon = 'folder_redgrey.png';
            }
            else
            {
                $icon = 'folder_red.png';
            }
        }

        $not_deletable = false;
        if ($is_home || $is_root)
        {
            $not_deletable = true;
        }

        echo '<item '.($style != '' ? $style : '').
            ' '.(!empty($array_children_cats[$row['id_category']]) ? 'child="1"' : '').
            ' id="'.$row['id_category'].'"'.($parent_id == 0 || $icon == 'folder_table.png' ? ' open="1"' : '').
            ' im0="'.$icon.'"'.
            ' im1="'.$icon.'"'.
            ' im2="'.$icon.'"'.
            ($row['name'] == 'SC Recycle Bin' ? ' tooltip="'._l('Products and categories in recycle bin from all shops').'"' : '').
            '><itemtext><![CDATA['.($row['name'] == 'SC Recycle Bin' ? _l('SC Recycle Bin') : ($style == '' ? $row['name'] : _l('To Translate:').' '.$res2['name'])).']]></itemtext>';
        echo '      <userdata name="not_deletable">'.(int) $not_deletable.'</userdata>';
        if ($row['name'] == 'SC Recycle Bin')
        {
            echo '      <userdata name="is_recycle_bin">1</userdata>';
        }
        else
        {
            echo '      <userdata name="is_recycle_bin">0</userdata>';
        }
        echo '      <userdata name="is_home">'.(int) $is_home.'</userdata>';
        echo '      <userdata name="is_root">'.(int) $is_root.'</userdata>';
        echo '         <userdata name="is_segment">0</userdata>';
        echo '         <userdata name="parent_root">'.$id_root.'</userdata>';
        echo '         <userdata name="is_eservices">'.$in_eservices.'</userdata>';
        echo '         <userdata name="not_associate_eservices">'.$not_associate_eservices.'</userdata>';
        echo '         <userdata name="eservices_id_project">'.$eservices_id_project.'</userdata>';
        if (empty($dynamic_call))
        {
            getLevelFromDB($row['id_category'], $in_eservices);
        }
        echo '</item>'."\n";
    }
}

function getLevelFromDB_PHP($id_parent, $limit_to_shop = false, $eservices_parent = 0, $dynamic_call = null)
{
    global $id_lang,$id_shop,$binPresent,$forceDisplayAllCategories,$root_cat,$array_cats,$array_children_cats,$id_root,$eservices_cat_id,$eservices_cat_archived,$eservices_projects_byIdCat,$eservices_projects_authorizedStatus;

    ## on enlève les eservices des catégories générales
    if (!empty($eservices_cat_id) && array_key_exists($eservices_cat_id, $array_cats))
    {
        unset($array_cats[$eservices_cat_id]);
    }

    if (!empty($array_children_cats[$id_parent]))
    {
        ksort($array_children_cats[$id_parent]);
        foreach ($array_children_cats[$id_parent] as $k => $id)
        {
            if (array_key_exists($id, $array_cats))
            {
                $row = $array_cats[$id];
            }
            else
            {
                continue;
            }

            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                if (!SCMS)
                {
                    $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
                }
                if (!empty($id_shop))
                {
                    $in_shop = false;
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
            }

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
            $icon = ($row['active'] ? 'catalog.png' : 'folder_grey.png');
            if ($row['name'] == 'SC Recycle Bin')
            {
                $icon = 'folder_delete.png';
                $binPresent = true;
                if (!_r('ACT_CAT_DELETE_PRODUCT_COMBI'))
                {
                    continue;
                }
            }

            $is_root = false;
            if ($row['id_parent'] == 0)
            {
                $is_root = true;
            }

            $is_home = false;
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && sc_in_array($row['id_category'], $root_cat, 'catCategoryGet_rootcatgetLevelFromDB_PHP'))
            {
                $icon = 'folder_table.png';
                $is_home = true;
            }

            $is_eservices = $in_eservices = $eservices_id_project = 0;
            if ($eservices_cat_id == $row['id_category'])
            {
                $icon = 'ruby.png';
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
                    $icon = 'folder_redgrey.png';
                }
                else
                {
                    $icon = 'folder_red.png';
                }
            }

            $not_deletable = false;
            if ($is_home || $is_root)
            {
                $not_deletable = true;
            }

            echo '<item '.($style != '' ? $style : '').
                ' '.(!empty($array_children_cats[$row['id_category']]) ? 'child="1"' : '').
                ' id="'.$row['id_category'].'"'.($row['id_parent'] == 0 || $icon == 'folder_table.png' || $eservices_cat_id == $row['id_category'] ? ' open="1"' : '').
                ' im0="'.$icon.'"'.
                ' im1="'.$icon.'"'.
                ' im2="'.$icon.'"'.
                ($row['name'] == 'SC Recycle Bin' ? ' tooltip="'._l('Products and categories in recycle bin from all shops').'"' : '').
                ">\n<itemtext><![CDATA[".($row['name'] == 'SC Recycle Bin' ? _l('SC Recycle Bin') : ($style == '' ? $row['name'] : _l('To Translate:').' '.$res2['name']))."]]></itemtext>\n";
            echo '      <userdata name="not_deletable">'.(int) $not_deletable.'</userdata>'."\n";
            if ($row['name'] == 'SC Recycle Bin')
            {
                echo '      <userdata name="is_recycle_bin">1</userdata>'."\n";
            }
            else
            {
                echo '      <userdata name="is_recycle_bin">0</userdata>'."\n";
            }
            echo '      <userdata name="is_home">'.(int) $is_home.'</userdata>'."\n";
            echo '      <userdata name="is_root">'.(int) $is_root.'</userdata>'."\n";
            echo '         <userdata name="is_segment">0</userdata>';
            echo '         <userdata name="parent_root">'.$id_root.'</userdata>';
            echo '         <userdata name="is_eservices">'.$in_eservices.'</userdata>';
            echo '         <userdata name="not_associate_eservices">'.$not_associate_eservices.'</userdata>';
            echo '         <userdata name="eservices_id_project">'.$eservices_id_project.'</userdata>';
            if (empty($dynamic_call))
            {
                getLevelFromDB_PHP($row['id_category'], $limit_to_shop, $in_eservices);
            }
            echo '</item>'."\n";
        }
    }
}
