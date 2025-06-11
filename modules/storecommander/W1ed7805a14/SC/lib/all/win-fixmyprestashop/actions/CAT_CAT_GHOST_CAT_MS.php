<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pl.id_category, pl.name, pl.id_shop, s.name as shop_name, c.id_parent
            FROM '._DB_PREFIX_.'category_lang pl
                INNER JOIN '._DB_PREFIX_.'shop s ON (pl.id_shop = s.id_shop)
                INNER JOIN '._DB_PREFIX_.'category c ON (pl.id_category = c.id_category)
            WHERE pl.id_category not in (SELECT ps.id_category FROM '._DB_PREFIX_.'category_shop ps WHERE ps.id_shop = pl.id_shop)
            ORDER BY id_lang ASC LIMIT 1500';
    $res = Db::getInstance()->ExecuteS($sql);

    $content = '';
    $content_js = '';
    $results = 'OK';
    $nb = 0;
    if (!empty($res) && count($res) > 0)
    {
        $shops_home = array();
        $xml = '';
        foreach ($res as $category)
        {
            $sql = 'SELECT * FROM `'._DB_PREFIX_."category_product` WHERE id_category = '".(int) $category['id_category']."' LIMIT 1";
            $is_used = Db::getInstance()->ExecuteS($sql);

            $is_root = false;
            if ($category['id_parent'] == 0)
            {
                $is_root = true;
            }

            $cat_home_id = 0;
            if (empty($shops_home[$category['id_shop']]))
            {
                $cat_home = Category::getRootCategory(null, new Shop($category['id_shop']));
                if (!empty($cat_home->id))
                {
                    $shops_home[$category['id_shop']] = $cat_home;
                    $cat_home_id = $cat_home->id;
                }
            }
            else
            {
                $cat_home_id = $shops_home[$category['id_shop']]->id;
            }

            $is_recycle_bin = false;
            if ($category['name'] == 'SC Recycle Bin')
            {
                $is_recycle_bin = true;
            }

            $is_home = false;
            if ($cat_home_id == $category['id_category'])
            {
                $is_home = true;
            }

            $not_deletable = false;
            if ($is_home || $is_root)
            {
                $not_deletable = true;
            }
            if (!$is_root && !$is_recycle_bin)
            {
                ++$nb;
                $xml .= "xml = xml+'<row id=\"".$category['id_category'].'_'.$category['id_shop']."\">';
                xml = xml+'<userdata name=\"not_deletable\">".(int) $not_deletable."</userdata>';
                xml = xml+'<userdata name=\"is_home\">".(int) $is_home."</userdata>';
                xml = xml+'<userdata name=\"is_root\">".(int) $is_root."</userdata>';
                xml = xml+'<cell><![CDATA[".$category['id_category']."]]></cell>';
                xml = xml+'<cell><![CDATA[".str_replace('\\\\', '\\', str_replace("'", "\'", $category['name']))."]]></cell>';
                xml = xml+'<cell><![CDATA[".((!empty($is_used) && count($is_used) > 0) ? _l('Yes') : _l('No'))."]]></cell>';
                xml = xml+'<cell><![CDATA[".str_replace("'", "\'", $category['shop_name']).' (#'.$category['id_shop'].')'."]]></cell>';
                xml = xml+'</row>';";
            }
        }
        if (!empty($xml))
        {
            $results = 'KO';
            ob_start(); ?>
        <script type="text/javascript">
    
            var tbGhostCategory = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_GHOST_CAT_MS").attachToolbar();
            tbGhostCategory.setIconset('awesome');
            tbGhostCategory.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbGhostCategory.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbGhostCategory.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbGhostCategory.setItemToolTip('delete','<?php echo _l('Delete ghost translations for these shops'); ?>');
            tbGhostCategory.addButton("add", 0, "", 'fa fa-plus-circle green', 'fa fa-plus-circle green');
            tbGhostCategory.setItemToolTip('add','<?php echo _l('Recover missing associations with shops'); ?>');
            tbGhostCategory.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridGhostCategory.selectAll();
                        getGridStat_GhostCategory();
                    }
                    if (id=='delete')
                    {
                        deleteGhostCategory();
                    }
                    if (id=='add')
                    {
                        addGhostCategory()
                    }
                });
        
            var gridGhostCategory = dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_GHOST_CAT_MS").attachGrid();
            gridGhostCategory.setImagePath("lib/js/imgs/");

            gridGhostCategory.enableMultiselect(true);
    
            gridGhostCategory.setHeader("ID,<?php echo _l('Name'); ?>,<?php echo _l('Used?'); ?>,<?php echo _l('Shop'); ?>");
            gridGhostCategory.setInitWidths("100,110,110,200");
            gridGhostCategory.setColAlign("left,left,left,left");
            gridGhostCategory.setColTypes("ro,ro,ro,ro");
            gridGhostCategory.setColSorting("int,str,str,str");
            gridGhostCategory.attachHeader("#numeric_filter,#text_filter,#select_filter,#text_filter");
            gridGhostCategory.init();

            var xml = '<rows>';
            <?php echo $xml; ?>
            xml=xml+'</rows>';
            gridGhostCategory.parse(xml);

            sbGhostCategory=dhxlSCExtCheck.tabbar.cells("table_CAT_CAT_GHOST_CAT_MS").attachStatusBar();
            function getGridStat_GhostCategory(){
                var filteredRows=gridGhostCategory.getRowsNum();
                var selectedRows=(gridGhostCategory.getSelectedRowId()?gridGhostCategory.getSelectedRowId().split(',').length:0);
                sbGhostCategory.setText('<?php echo $nb.' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridGhostCategory.attachEvent("onFilterEnd", function(elements){
                getGridStat_GhostCategory();
            });
            gridGhostCategory.attachEvent("onSelectStateChanged", function(id){
                getGridStat_GhostCategory();
            });
            getGridStat_GhostCategory();

            function deleteGhostCategory()
            {
                var selectedGhostCategorys = gridGhostCategory.getSelectedRowId();
                if(selectedGhostCategorys==null || selectedGhostCategorys=="")
                    selectedGhostCategorys = 0;
                if(selectedGhostCategorys!="0")
                {
                    var final_ids = "";
                    var temp_ids = selectedGhostCategorys.split(",");
                    var has_home = false;
                    var has_root = false;
                    
                    $.each(temp_ids, function(num, id) {
                        var not_deletable = gridGhostCategory.getUserData(id,"not_deletable");
                        if(not_deletable!=1)
                        {
                            if(final_ids!="")
                                final_ids = final_ids + ",";
                            final_ids = final_ids + id;
                        }
                        else
                        {
                            var is_home = gridGhostCategory.getUserData(id,"is_home");
                            var is_root = gridGhostCategory.getUserData(id,"is_root");

                            if(is_home==1)
                                has_home = true;
                            if(is_root==1)
                                has_root = true;
                        }
                    });

                    if(has_home)
                        dhtmlx.message({text:'<?php echo _l('One of selected cagetories is the Home category of a shop, and can not be deleted', 1); ?>',type:'error',expire:5000});
                    if(has_root)
                        dhtmlx.message({text:'<?php echo _l('One of selected cagetories is the Root category, and can not be deleted', 1); ?>',type:'error',expire:5000});
                    
                    if(final_ids!="")
                    {
                        $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_CAT_GHOST_CAT_MS&id_lang="+SC_ID_LANG, { "action": "delete_categories", "ids": final_ids}, function(data){
                            dhxlSCExtCheck.tabbar.tabs("table_CAT_CAT_GHOST_CAT_MS").close();
    
                             dhxlSCExtCheck.gridChecks.selectRowById('CAT_CAT_GHOST_CAT_MS');
                             doCheck(false);
                        });
                    }
                }
            }

            function addGhostCategory()
            {
                var selectedGhostCategorys = gridGhostCategory.getSelectedRowId();
                if(selectedGhostCategorys==null || selectedGhostCategorys=="")
                    selectedGhostCategorys = 0;
                if(selectedGhostCategorys!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_CAT_GHOST_CAT_MS&id_lang="+SC_ID_LANG, { "action": "add_categorys", "ids": selectedGhostCategorys}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_CAT_GHOST_CAT_MS").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_CAT_GHOST_CAT_MS');
                         doCheck(false);
                    });
                }
            }
        </script>
        <?php $content_js = ob_get_clean();
        }
    }
    echo json_encode(array(
            'results' => $results,
            'contentType' => 'grid',
            'content' => $content,
            'title' => _l('Ghost category'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'delete_categories')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_category, $id_shop) = explode('_', $id);

            $sql = 'DELETE FROM '._DB_PREFIX_.'category_lang WHERE id_category = '.(int) $id_category.' AND id_shop = '.(int) $id_shop;
            dbExecuteForeignKeyOff($sql);
        }
    }
}
elseif (!empty($post_action) && $post_action == 'add_categorys')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            list($id_category, $id_shop) = explode('_', $id);

            $sql = 'SELECT pl.id_shop
            FROM '._DB_PREFIX_.'category_lang pl
            WHERE pl.id_category IN (SELECT ps.id_category FROM '._DB_PREFIX_.'category_shop ps WHERE ps.id_shop = pl.id_shop AND pl.id_category = '.(int) $id_category.')
                AND pl.id_category = '.(int) $id_category.' 
                AND pl.id_shop!='.(int) $id_shop.' 
            LIMIT 1';
            $base_shop_id = Db::getInstance()->executeS($sql);
            if (!empty($base_shop_id[0]['id_shop']))
            {
                $base_shop_id = $base_shop_id[0]['id_shop'];
                $category = new Category($id_category, null, $base_shop_id);
                $category->id_shop_list = array($id_shop);
                $category->save();
            }
        }
    }
}
