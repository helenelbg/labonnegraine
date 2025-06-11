<?php
$post_action = Tools::getValue('action');
if (!empty($post_action) && $post_action == 'do_check')
{
    $values = array();
    $sql = 'SELECT fp1.*, pl.name, fvl.value
            FROM '._DB_PREFIX_.'feature_product fp1
                INNER JOIN '._DB_PREFIX_.'feature_product fp2 ON (fp1.id_feature = fp2.id_feature AND fp1.id_feature_value = fp2.id_feature_value AND fp1.id_product != fp2.id_product) 
                INNER JOIN '._DB_PREFIX_."product_lang pl ON (fp1.id_product = pl.id_product AND pl.id_lang = '".(int) SCI::getConfigurationValue('PS_LANG_DEFAULT')."')
                INNER JOIN "._DB_PREFIX_."feature_value_lang fvl ON (fp1.id_feature_value = fvl.id_feature_value AND fvl.id_lang = '".(int) SCI::getConfigurationValue('PS_LANG_DEFAULT')."')
                INNER JOIN "._DB_PREFIX_."feature_value fv ON (fp1.id_feature_value = fv.id_feature_value)
            WHERE fv.custom = '1' LIMIT 1500";
    $res = Db::getInstance()->ExecuteS($sql);
    if (!empty($res) && count($res) > 0)
    {
        foreach ($res as $product)
        {
            $values[$product['id_feature_value']][$product['id_product']] = array('name' => $product['name'], 'value' => $product['value']);
        }
    }

    $content = '';
    $content_js = '';
    $results = 'OK';
    if (!empty($values) && count($values) > 0)
    {
        $results = 'KO';
        ob_start(); ?>
        <script type="text/javascript">
    
            var tbCustomFeature = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_CUSTOM_FEATURE").attachToolbar();
            tbCustomFeature.setIconset('awesome');
            tbCustomFeature.addButton("selectall", 0, "", 'fa fa-bolt yellow', 'fa fa-bolt yellow');
            tbCustomFeature.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            tbCustomFeature.addButton("add", 0, "", 'fa fa-layer-group', 'fa fa-layer-group');
            tbCustomFeature.setItemToolTip('add','<?php echo _l('Duplicate value for each product'); ?>');
            tbCustomFeature.attachEvent("onClick",
                function(id){
                    if (id=='selectall')
                    {
                        gridCustomFeature.selectAll();
                        getGridStat_CustomFeature();
                    }
                    if (id=='add')
                    {
                        addCustomFeature()
                    }
                });
        
            var gridCustomFeature = dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_CUSTOM_FEATURE").attachGrid();
            gridCustomFeature.setImagePath("lib/js/imgs/");
            gridCustomFeature.enableSmartRendering(true);
            gridCustomFeature.enableMultiselect(true);
    
            gridCustomFeature.setHeader("<?php echo _l('Value'); ?>,<?php echo _l('Products'); ?>");
            gridCustomFeature.setInitWidths("150,50");
            gridCustomFeature.setColAlign("left,left");
            gridCustomFeature.setColTypes("ro,ro");
            gridCustomFeature.setColSorting("str,str");
            gridCustomFeature.attachHeader("#text_filter,#text_filter");
            gridCustomFeature.init();

            var xml = '<rows>';
            <?php foreach ($values as $id_feature_value => $products)
        {
            $name = '';
            $value = '';
            foreach ($products as $product)
            {
                if (!empty($name))
                {
                    $name .= ', ';
                }
                $name .= $product['name'];
                $value = $product['value'];
            } ?>
            xml = xml+'   <row id="<?php echo $id_feature_value; ?>">';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $value); ?>]]></cell>';
            xml = xml+'      <cell><![CDATA[<?php echo str_replace("'", "\'", $name); ?>]]></cell>';
            xml = xml+'   </row>';
            <?php
        } ?>
            xml = xml+'</rows>';
            gridCustomFeature.parse(xml);

            sbCustomFeature=dhxlSCExtCheck.tabbar.cells("table_CAT_PROD_WITHOUT_CATEGORY").attachStatusBar();
            function getGridStat_CustomFeature(){
                var filteredRows=gridCustomFeature.getRowsNum();
                var selectedRows=(gridCustomFeature.getSelectedRowId()?gridCustomFeature.getSelectedRowId().split(',').length:0);
                sbCustomFeature.setText('<?php echo count($values).' '._l('Errors'); ?>'+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
            }
            gridCustomFeature.attachEvent("onFilterEnd", function(elements){
                getGridStat_CustomFeature();
            });
            gridCustomFeature.attachEvent("onSelectStateChanged", function(id){
                getGridStat_CustomFeature();
            });
            getGridStat_CustomFeature();

            function addCustomFeature()
            {
                var selectedCustomFeatures = gridCustomFeature.getSelectedRowId();
                if(selectedCustomFeatures==null || selectedCustomFeatures=="")
                    selectedCustomFeatures = 0;
                if(selectedCustomFeatures!="0")
                {
                    $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check=CAT_PROD_CUSTOM_FEATURE&id_lang="+SC_ID_LANG, { "action": "dupplicate_feature_value", "ids": selectedCustomFeatures}, function(data){
                        dhxlSCExtCheck.tabbar.tabs("table_CAT_PROD_CUSTOM_FEATURE").close();

                         dhxlSCExtCheck.gridChecks.selectRowById('CAT_PROD_CUSTOM_FEATURE');
                         doCheck(false);
                    });
                }
            }
        </script>
        <?php $content_js = ob_get_clean();
    }
    echo json_encode(array(
            'results' => $results,
            'contentType' => 'grid',
            'content' => $content,
            'title' => _l('Custom Feature'),
            'contentJs' => $content_js,
    ));
}
elseif (!empty($post_action) && $post_action == 'dupplicate_feature_value')
{
    $post_ids = Tools::getValue('ids');
    if (!empty($post_ids))
    {
        $ids = explode(',', $post_ids);
        foreach ($ids as $id)
        {
            // Pour chaque produit utilisant cette valeur
            $products = Db::getInstance()->executeS('
                            SELECT *
                            FROM `'._DB_PREFIX_.'feature_product`
                            WHERE `id_feature_value` = '.(int) $id
            );
            if (count($products) > 1)
            {
                unset($products[0]);
                foreach ($products as $product)
                {
                    $res = Db::getInstance()->getRow('
                                SELECT * 
                                FROM `'._DB_PREFIX_.'feature_value`
                                WHERE `id_feature_value` = '.(int) $id
                            );
                    unset($res['id_feature_value']);

                    $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'feature_value` (';
                    $i = 0;
                    foreach ($res as $field => $value)
                    {
                        if ($i > 0)
                        {
                            $sql_insert .= ',';
                        }
                        $sql_insert .= '`'.bqSQL($field).'`';
                        ++$i;
                    }
                    $sql_insert .= ') VALUES (';
                    $i = 0;
                    foreach ($res as $field => $value)
                    {
                        if ($i > 0)
                        {
                            $sql_insert .= ',';
                        }
                        $sql_insert .= "'".$value."'";
                        ++$i;
                    }
                    $sql_insert .= ')';
                    $insert_id = dbExecuteForeignKeyOff($sql_insert, true);

                    // Dupplication de la valeur
                    if (!empty($insert_id))
                    {
                        // Dupplication de langues
                        $res = Db::getInstance()->executeS('
                                    SELECT * 
                                    FROM `'._DB_PREFIX_.'feature_value_lang`
                                    WHERE `id_feature_value` = '.(int) $id
                                );

                        foreach ($res as $row)
                        {
                            $row['id_feature_value'] = (int) $insert_id;

                            $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'feature_value_lang` (';
                            $i = 0;
                            foreach ($row as $field => $value)
                            {
                                if ($i > 0)
                                {
                                    $sql_insert .= ',';
                                }
                                $sql_insert .= '`'.bqSQL($field).'`';
                                ++$i;
                            }
                            $sql_insert .= ') VALUES (';
                            $i = 0;
                            foreach ($row as $field => $value)
                            {
                                if ($i > 0)
                                {
                                    $sql_insert .= ',';
                                }
                                $sql_insert .= "'".$value."'";
                                ++$i;
                            }
                            $sql_insert .= ')';
                            dbExecuteForeignKeyOff($sql_insert);
                        }

                        // Suppresion de l'ancienne association
                        dbExecuteForeignKeyOff('
                            DELETE FROM `'._DB_PREFIX_.'feature_product`
                            WHERE  id_feature = "'.(int) $product['id_feature'].'"
                                AND id_product = "'.(int) $product['id_product'].'"
                                AND id_feature_value = "'.(int) $id.'"
                        ');
                        // Ajout de la nouvelle association
                        dbExecuteForeignKeyOff('
                            INSERT INTO `'._DB_PREFIX_.'feature_product` (id_feature,id_product,id_feature_value)
                            VALUES ("'.(int) $product['id_feature'].'","'.(int) $product['id_product'].'","'.(int) $insert_id.'")
                        ');
                    }
                }
            }
        }
    }
}
