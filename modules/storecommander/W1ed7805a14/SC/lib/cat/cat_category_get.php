<?php

    $id_lang = (int) Tools::getValue('id_lang');
    $id_shop = (int) Tools::getValue('id_shop', SCI::getSelectedShop());
    $with_segment = (int) Tools::getValue('with_segment', '1');
    $forceDisplayAllCategories = (int) Tools::getValue('forceDisplayAllCategories', 0);
    $forExport = (int) Tools::getValue('forExport', 0);
    $hide_disable_cat = (int) Tools::getValue('hide_disable_cat', 0);
    $id_root_from_start = Tools::getValue('id', 0);
    $dynamic_call = Tools::getValue('dynamic', null);
    $only_segmentation = null;

    require_once SC_DIR.'lib/cat/cat_category_tools.php';

    /*
     * BIN Category
     */
    $sql = 'SELECT c.id_category,c.id_parent FROM '._DB_PREFIX_.'category c
                    LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $sc_agent->id_lang.")
                    WHERE cl.name LIKE '%SC Recycle Bin' OR cl.name LIKE '%".psql(_l('SC Recycle Bin'))."'";
    $res = Db::getInstance()->ExecuteS($sql);
    $bincategory = 0;
    if (count($res) == 0)
    {
        $newcategory = new Category();
        $newcategory->id_parent = 1;
        $newcategory->level_depth = 1;
        $newcategory->active = 0;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $newcategory->position = Category::getLastPosition(1, 0);
        }
        else
        {
            // bug PS1.4 - set position
            $_GET['id_parent'] = 1;
            $newcategory->position = Category::getLastPosition(1);
        }
        foreach ($languages as $lang)
        {
            $newcategory->link_rewrite[$lang['id_lang']] = 'category';
            $newcategory->name[$lang['id_lang']] = 'SC Recycle Bin';
        }
        $newcategory->save();
        $bincategory = $newcategory->id;
    }
    else
    {
        // fix bug in db
        if ($res[0]['id_category'] == $res[0]['id_parent'])
        {
            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'category SET id_parent = 1 WHERE id_category = '.(int) $res[0]['id_category']);
        }
        $bincategory = $res[0]['id_category'];
    }
    $binPresent = false;

    /*
     * Categories Home for MS
     */

    $root_cat = array();
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $shops = Shop::getShops(false);
        foreach ($shops as $shop)
        {
            $root_cat[] = $shop['id_category'];
        }
    }

    /*
     * Category ROOT
     */
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
    if (SCMS && $id_shop > 0)
    {
        $shop = new Shop($id_shop);
        $categ = new Category($shop->id_category);
        $id_root = $categ->id_parent;
    }

    /*
     * GET PARENT ID
     */
    if (!empty($dynamic_call))
    {
        if (substr($id_root_from_start, 0, 4) == 'seg_')
        {
            $only_segmentation = 1;
        }
        else
        {
            if ($id_root_from_start === 0)
            {
                $id_parent = (int) $ps_root;
            }
            else
            {
                $id_parent = (int) $id_root_from_start;
            }
        }
    }

    // ESERVICES
    $eservices_parent = 0;
    $eservices_cat_archived = SCI::getConfigurationValue('SC_ESERVICES_CATEGORYARCHIVED');
    $eservices_cat_id = SCI::getConfigurationValue('SC_ESERVICES_CATEGORY');

    $eservices_projects_byIdCat = array();
    $eservices_projects_authorizedStatus = array();
    $projects = array();
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

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo '<tree id="'.$id_root_from_start.'">';

    if (empty($only_segmentation))
    {
        /*
         * Get all categories
         */
        $array_cats = array();
        $array_children_cats = array();

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !SCMS)
        {
            $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        }

        $sql = 'SELECT c.*, cl.name, c.position '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop) ? ', cs.position' : '').'
                FROM '._DB_PREFIX_.'category c
                LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang='.(int) $id_lang.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop) ? " AND cl.id_shop='".(int) $id_shop."'" : '').')
                '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop) ? ' INNER JOIN '._DB_PREFIX_."category_shop cs ON (cs.id_category=c.id_category AND cs.id_shop='".(int) $id_shop."') " : '').
                (!empty($hide_disable_cat) ? ' WHERE c.active = 1 ' : '').'
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

        if (!empty($dynamic_call))
        {
            $id_root = $id_parent;
        }
        getLevelFromDB_PHP($id_root, true, 0, $dynamic_call);

        /*
         * Display Bin in Root
         */
        if (SCMS && !$binPresent && _r('ACT_CAT_DELETE_PRODUCT_COMBI') && empty($dynamic_call))
        {
            $icon = 'folder_delete.png';
            echo '<item '.
                                    ' id="'.$bincategory.'"'.
                                    ' text="'._l('SC Recycle Bin').'"'.
                                    ' im0="'.$icon.'"'.
                                    ' im1="'.$icon.'"'.
                                    ' im2="'.$icon.'"'.
                                    ' tooltip="'._l('Products and categories in recycle bin from all shops').'">';
            echo '      <userdata name="not_deletable">1</userdata>';
            echo '      <userdata name="is_recycle_bin">1</userdata>';
            echo '      <userdata name="is_home">0</userdata>';
            echo '      <userdata name="is_root">0</userdata>';
            echo '         <userdata name="is_segment">0</userdata>';
            echo '         <userdata name="parent_root">'.$id_root.'</userdata>';
            getLevelFromDB_PHP($bincategory, true);
            echo "</item>\n";
        }
    }

    /*
     * Display Segments
     */
    if (SCSG && $with_segment)
    {
        ## uniquement en dynamique
        if (!empty($dynamic_call))
        {
            ## uniquement lors de l'appel des enfants segments
            if (!empty($only_segmentation))
            {
                $exploded = explode('_', $id_root_from_start);
                $seg_root = $exploded[1];
                SegmentHook::getSegmentLevelFromDB($seg_root, 'catalog', $dynamic_call);
            }
            elseif ($id_root_from_start == 0)
            {
                ## uniquement lors du premier appel pour charger les segment au niveau du root
                SegmentHook::getSegmentLevelFromDB(0, 'catalog', $dynamic_call);
            }
        }
        else
        {
            SegmentHook::getSegmentLevelFromDB(0, 'catalog');
        }
    }

    /*
     * Display E-services
     */
    if (!empty($projects))
    {
        $icon = 'ruby.png';
        $show_eservice_cat = false;
        if (!empty($dynamic_call))
        {
            if ($id_root_from_start == 0 && empty($only_segmentation))
            {
                $show_eservice_cat = true;
            }
        }
        else
        {
            $show_eservice_cat = true;
        }
        if ($show_eservice_cat && !empty($eservices_cat_id))
        {
            echo '<item '.
                ' id="'.$eservices_cat_id.'"'.
                ' text="'._l('e-Services').'"'.
                ' im0="'.$icon.'"'.
                ' im1="'.$icon.'"'.
                ' im2="'.$icon.'"'.
                ' tooltip="">';
            echo '      <userdata name="not_deletable">1</userdata>';
            echo '      <userdata name="is_recycle_bin">1</userdata>';
            echo '      <userdata name="is_home">0</userdata>';
            echo '      <userdata name="is_root">0</userdata>';
            echo '         <userdata name="is_segment">0</userdata>';
            echo '         <userdata name="parent_root">'.$id_root.'</userdata>';
            echo '         <userdata name="is_eservices">1</userdata>';
            getLevelFromDB_PHP($eservices_cat_id, true, $eservices_cat_id);
            echo "</item>\n";
        }
    }

    echo '</tree>';
