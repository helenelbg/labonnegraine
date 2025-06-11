<?php
$post_action = Tools::getValue('action');

function hasUpdated($array)
{
    $return = false;
    foreach ($array as $id => $row)
    {
        if ($row['updated'] == true)
        {
            $return = true;
        }
    }

    return $return;
}
if (!empty($post_action) && $post_action == 'do_check')
{
    $sql = 'SELECT pa.id_product, pa.id_product_attribute,
            (
                SELECT GROUP_CONCAT(a.`id_attribute_group`)
                FROM `'._DB_PREFIX_.'product_attribute_combination` pac
                    INNER JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                WHERE pa.`id_product_attribute` = pac.`id_product_attribute` ORDER BY pac.`id_attribute`
            ) as id_attributes
            FROM '._DB_PREFIX_.'product_attribute pa
            ORDER BY pa.id_product, pa.id_product_attribute LIMIT 1500';
    $resSQL = Db::getInstance()->ExecuteS($sql);
    $res = array();
    foreach ($resSQL as $r)
    {
        $r['id_attributes'] = explode(',', $r['id_attributes']);
        sort($r['id_attributes']);
        $r['id_attributes'] = implode(',', $r['id_attributes']);

        if (empty($res[$r['id_product']]))
        {
            $res[$r['id_product']] = array('ids' => $r['id_attributes'], 'updated' => false);
        }
        else
        {
            if ($res[$r['id_product']]['ids'] != $r['id_attributes'])
            {
                $ids = explode(',', $res[$r['id_product']]['ids']);
                $ids_new = explode(',', $r['id_attributes']);

                $new_ids = $ids;
                foreach ($ids_new as $id)
                {
                    if (!empty($id) && !in_array($id, $new_ids))
                    {
                        $new_ids[] = $id;
                    }
                }
                sort($new_ids);
                $res[$r['id_product']]['ids'] = implode(',', $new_ids);
                $res[$r['id_product']]['updated'] = true;
            }
        }
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (hasUpdated($res))
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">

            var colsNotSameAttrProduct = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_NOT_SAME_ATTRIBUTES").attachLayout("2U");

            dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_NOT_SAME_ATTRIBUTES").attachToolbar();
            
            // LISTE PRODUCTS
            colsNotSameAttrProduct.cells('a').setText("<?php echo _l('Products'); ?>");
        
            var gridLeftNotSameAttrProduct = colsNotSameAttrProduct.cells('a').attachGrid();
            gridLeftNotSameAttrProduct.setImagePath("lib/js/imgs/");
            gridLeftNotSameAttrProduct.enableSmartRendering(true);
            gridLeftNotSameAttrProduct.enableMultiselect(false);
    
            gridLeftNotSameAttrProduct.setHeader("<?php echo _l('ID'); ?>,<?php echo _l('Ref'); ?>,<?php echo _l('Name'); ?>");
            gridLeftNotSameAttrProduct.setColAlign("left,left,left");
            gridLeftNotSameAttrProduct.setColTypes("ro,ro,ro");
            gridLeftNotSameAttrProduct.setInitWidths("40,80,");
            gridLeftNotSameAttrProduct.setColSorting("int,str,str");
            gridLeftNotSameAttrProduct.attachHeader("#numeric_filter,#text_filter,#text_filter");
            gridLeftNotSameAttrProduct.init();
    
            var xml = '<rows>';
            <?php $nb = 0;
        foreach ($res as $id => $row)
        {
            if ($row['updated'] == true)
            {
                $product = new Product($id);
                ++$nb; ?>
                    xml = xml+'<row id="<?php echo $id; ?>">';
                    xml = xml+'    <userdata name="ids"><?php echo $row['ids']; ?></userdata>';
                    xml = xml+'    <cell><![CDATA[<?php echo $id; ?>]]></cell>';
                    xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $product->reference); ?>]]></cell>';
                    xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $product->name[$id_lang]); ?>]]></cell>';
                    xml = xml+'</row>';
            <?php
            }
        } ?>
            xml = xml+'</rows>';
            gridLeftNotSameAttrProduct.parse(xml);

            sbLeftNotSameAttrProduct=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_NOT_SAME_ATTRIBUTES").attachStatusBar();
            function getGridStat_LeftNotSameAttrProduct(){
                var filteredRows=gridLeftNotSameAttrProduct.getRowsNum();
                var selectedRows=(gridLeftNotSameAttrProduct.getSelectedRowId()?gridLeftNotSameAttrProduct.getSelectedRowId().split(',').length:0);
                sbLeftNotSameAttrProduct.setText('<?php echo $nb.' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridLeftNotSameAttrProduct.attachEvent("onFilterEnd", function(elements){
                getGridStat_LeftNotSameAttrProduct();
            });
            gridLeftNotSameAttrProduct.attachEvent("onSelectStateChanged", function(id){
                getGridStat_LeftNotSameAttrProduct();
            });
            getGridStat_LeftNotSameAttrProduct();

            gridLeftNotSameAttrProduct.attachEvent("onRowSelect", function(id,ind){
                var ids = gridLeftNotSameAttrProduct.getUserData(id, "ids");
                attrGet(ids);
            });

            // LISTE GROUPES ATTIBUTS
            var tbRightNotSameAttrProduct = colsNotSameAttrProduct.cells('b').attachToolbar();
            tbRightNotSameAttrProduct.setIconset('awesome');
            colsNotSameAttrProduct.cells('b').setText("<?php echo _l('Attribute groups'); ?>");
            tbRightNotSameAttrProduct.addButton("delete", 0, "", 'fa fa-minus-circle red', 'fa fa-minus-circle red');
            tbRightNotSameAttrProduct.setItemToolTip('delete','<?php echo _l('Delete'); ?>');
            tbRightNotSameAttrProduct.addButton("save", 0, "", 'fa fa-save blue', 'fa fa-save blue');
            tbRightNotSameAttrProduct.setItemToolTip('save','<?php echo _l('Save'); ?>');
            tbRightNotSameAttrProduct.attachEvent("onClick",
                function(id){
                if (id=='delete')
                {
                    gridRightNotSameAttrProduct.deleteSelectedRows();
                }
                if (id=='save')
                {
                    saveSameAttrProduct();
                }
            });
        
            var gridRightNotSameAttrProduct = colsNotSameAttrProduct.cells('b').attachGrid();
            gridRightNotSameAttrProduct.setImagePath("lib/js/imgs/");
            gridRightNotSameAttrProduct.enableSmartRendering(true);
            gridRightNotSameAttrProduct.enableMultiselect(true);
    
            gridRightNotSameAttrProduct.setHeader("<?php echo _l('ID'); ?>,<?php echo _l('Name'); ?>");
            gridRightNotSameAttrProduct.setColAlign("left,left");
            gridRightNotSameAttrProduct.setColTypes("ro,ro");
            gridRightNotSameAttrProduct.setInitWidths("40,");
            gridRightNotSameAttrProduct.init();
    
            var xml = '<rows>';
            <?php foreach ($res as $id => $row)
        {
            if ($row['updated'] == true)
            {
                $product = new Product($id); ?>
                    xml = xml+'<row id="<?php echo $id; ?>">';
                    xml = xml+'    <userdata name="ids"><?php echo $row['ids']; ?></userdata>';
                    xml = xml+'    <cell><![CDATA[<?php echo $id; ?>]]></cell>';
                    xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $product->reference); ?>]]></cell>';
                    xml = xml+'    <cell><![CDATA[<?php echo str_replace("'", "\'", $product->name[$id_lang]); ?>]]></cell>';
                    xml = xml+'</row>';
            <?php
            }
        } ?>
            xml = xml+'</rows>';

            function attrGet(ids)
            {
                gridRightNotSameAttrProduct.clearAll();
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_NOT_SAME_ATTRIBUTES&id_lang="+SC_ID_LANG, { "action": "attr_get", "ids": ids}, function(data){
                    if(data!=undefined && data!=null && data!="")
                    {
                        gridRightNotSameAttrProduct.parse(data);
                    }
                });
            }
            
            function saveSameAttrProduct()
            {
                var id_product = gridLeftNotSameAttrProduct.getSelectedRowId();
                gridRightNotSameAttrProduct.selectAll();
                var selectedGroupsAttr = gridRightNotSameAttrProduct.getSelectedRowId();
                if(selectedGroupsAttr==null || selectedGroupsAttr=="")
                    selectedGroupsAttr = 0;
                if(id_product==null || id_product=="")
                    id_product = 0;
                if(selectedGroupsAttr!="0" && id_product!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_NOT_SAME_ATTRIBUTES&id_lang="+SC_ID_LANG, { "action": "save_attr", "id_product": id_product, "ids": selectedGroupsAttr}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_NOT_SAME_ATTRIBUTES").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_NOT_SAME_ATTRIBUTES');
                         doCheck(false);
                    });
                }
            }
        </script>
        <?php
        $content_js = ob_get_clean();
    }
    echo json_encode(array(
            'results' => $results,
            'contentType' => 'grid',
            'content' => $content,
            'title' => _l('Diff. attr. groups'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'attr_get')
{
    $ids = Tools::getValue('ids');
    $id_lang = Tools::getValue('id_lang');
    $return = '';
    if (isset($ids))
    {
        $ids = explode(',', $ids);
        $return .= '<rows>';
        foreach ($ids as $id)
        {
            $group = new AttributeGroup($id);
            $return .= '<row id="'.$id.'">';
            $return .= '<cell><![CDATA['.$id.']]></cell>';
            $return .= '<cell><![CDATA['.$group->public_name[$id_lang].']]></cell>';
            $return .= '</row>';
        }
        $return .= '</rows>';
    }
    echo $return;
}
elseif (!empty($post_action) && $post_action == 'save_attr')
{
    $id_product = Tools::getValue('id_product');
    $ids = Tools::getValue('ids');
    if (!empty($ids) && !empty($id_product))
    {
        $default_values = array();

        $sql = 'SELECT pa.id_product, pa.id_product_attribute,
            (
                SELECT GROUP_CONCAT(a.`id_attribute_group`)
                FROM `'._DB_PREFIX_.'product_attribute_combination` pac
                    INNER JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_attribute`
                WHERE pa.`id_product_attribute` = pac.`id_product_attribute` ORDER BY pac.`id_attribute`
            ) as id_attributes
            FROM '._DB_PREFIX_."product_attribute pa
            WHERE pa.id_product = " .(int) $id_product . "
            ORDER BY pa.id_product, pa.id_product_attribute";
        $resSQL = Db::getInstance()->ExecuteS($sql);
        $res = array();
        foreach ($resSQL as $r)
        {
            if ($r['id_attributes'] != $ids)
            {
                $res[] = $r;
            }
        }

        $exp_ids = explode(',', $ids);
        foreach ($res as $row)
        {
            $to_delete = array();
            $to_insert = array();
            $exp_p_ids = explode(',', $row['id_attributes']);
            foreach ($exp_p_ids as $id)
            {
                if (!in_array($id, $exp_ids))
                {
                    $to_delete[] = $id;
                }
            }
            foreach ($exp_ids as $id)
            {
                if (!in_array($id, $exp_p_ids))
                {
                    $to_insert[] = $id;
                }
            }

            if (!empty($to_delete))
            {
                $sql = 'DELETE FROM `'._DB_PREFIX_."product_attribute_combination`
                        WHERE id_product_attribute='".(int) $row['id_product_attribute']."'
                            AND id_attribute IN (
                                SELECT a.`id_attribute`
                                FROM `"._DB_PREFIX_.'attribute` a
                                WHERE a.`id_attribute` = `'._DB_PREFIX_.'product_attribute_combination`.`id_attribute`
                                    AND a.`id_attribute_group` IN ('.pInSQL(implode(',', $to_delete)).')
                            )';
                $res = dbExecuteForeignKeyOff($sql);
            }

            if (!empty($to_insert))
            {
                foreach ($to_insert as $id)
                {
                    if (empty($default_values[$id]))
                    {
                        $sql = 'SELECT `id_attribute`
                            FROM `'._DB_PREFIX_."attribute`
                            WHERE `id_attribute_group` = " .(int) $id . "
                            ORDER BY position
                            LIMIT 1";
                        $res = Db::getInstance()->ExecuteS($sql);
                        if (!empty($res[0]['id_attribute']))
                        {
                            $default_values[$id] = $res[0]['id_attribute'];
                        }
                    }
                    if (!empty($default_values[$id]))
                    {
                        dbExecuteForeignKeyOff('INSERT INTO '._DB_PREFIX_."product_attribute_combination (id_product_attribute,id_attribute) VALUES ('".(int) $row['id_product_attribute']."','".(int) $default_values[$id]."')");
                    }
                }
            }
        }
    }
}
