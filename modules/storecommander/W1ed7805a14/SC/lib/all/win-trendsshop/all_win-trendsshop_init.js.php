<?php echo '<script type="text/javascript">'; ?>
    /*
     * INITIALIZE
     */
    dhxlTrendsShop=wTrendsShop.attachLayout("2U");

    // Filter Col
    dhxlTrendsShopFilters = dhxlTrendsShop.cells('a');
    dhxlTrendsShopFilters.setWidth(300);
    dhxlTrendsShopFilters.hideHeader();

    // -- Filter Col : Toolbar
    // -- Filter Col : tsf => trend shop
    var ts_filter_used = '';
    var ts_filter_views_list = [];
    var ts_filter_views = [];
    ts_filter_views.push(['all', 'obj', '<?php echo _l('All data'); ?>', '']);
    ts_custom_filter_views = <?php echo json_encode(CustomSettings::getCustomSettingDetail('all', 'ts_filters')); ?>;
    if(ts_custom_filter_views!= null && ts_custom_filter_views != '') {
        ts_custom_filter_views.forEach(function (item) {
            ts_filter_views.push([item.name, 'obj', item.name, '']);
            ts_filter_views_list[item.name] = [item.value];
        });
    }
    ts_filter_views.push(['separator1', 'sep', '', '']);
    ts_filter_views.push(['btn_save', 'obj', '<?php echo _l('Save filter group'); ?>', '']);
    ts_filter_views.push(['btn_del', 'obj', '<?php echo _l('Delete filter group'); ?>', '']);

    dhxlTrendsShopFilters_Tb = dhxlTrendsShopFilters.attachToolbar();
     dhxlTrendsShopFilters_Tb.setIconset('awesome');
    dhxlTrendsShopFilters_Tb.addButton("refresh", 10, "", "fa fa-sync green", "fa fa-sync green");
    dhxlTrendsShopFilters_Tb.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
    dhxlTrendsShopFilters_Tb.addButtonSelect('trends_shop_views',0,'<?php echo _l('Filter group'); ?>',ts_filter_views,'fad fa-flag blue','fad fa-flag blue',false,true);
    dhxlTrendsShopFilters_Tb.attachEvent("onClick",function(id){
        if (id=='refresh'){
            ts_filter_used = 'all';
            dhxlTrendsShopFilters_Tb.setItemText('trends_shop_views', '<?php echo _l('Filter group'); ?>');
            displayTrendsShopFiltersTree();
        }
        if(id=='all') {
            ts_filter_used = id;
            var getChecked=dhxlTrendsShopFilters_Tree.getAllChecked();
            var boxes_checked = getChecked.split(',');
            $.each(boxes_checked, function(index, id) {
                dhxlTrendsShopFilters_Tree.setCheck(id, 0);
            });
            dhxlTrendsShopFilters_Tb.setItemText('trends_shop_views', '<?php echo _l('All data'); ?>');
        }

        if (id=='btn_save'){
            var ts_filter_state_arr = [];
            var ts_filter_selections = dhxlTrendsShopFilters_Tree.getAllChecked();
            fillFilterArray(ts_filter_selections, ts_filter_state_arr);

            var ts_filter_view_encoded = ts_filter_state_arr.join();

            var ts_filter_view_name=prompt('<?php echo _l('Name of your filter group:', 1); ?>');
            if (ts_filter_view_name!=null && ts_filter_view_name!='') {
                $.post("index.php?ajax=1&act=all_win-trendsshop_tree_custom_update", {'action':'add', 'filter_view_encoded': ts_filter_view_encoded, 'filter_view_name': ts_filter_view_name}, function (data) {
                    if(data !== 'KO') {
                        var positionNew = 1;
                        for (var i = 0; i < ts_filter_views.length; i++) {
                            if(ts_filter_views[i][0] == 'btn_save') {
                                positionNew = i;
                            }
                        }
                        dhxlTrendsShopFilters_Tb.addListOption('trends_shop_views', data, positionNew,'button',data);
                        statusselection=dhxlTrendsShopFilters_Tree.getAllChecked();
                        ts_filter_views.push([data, 'obj', data, '']);
                        ts_filter_views_list[data] = [ts_filter_view_encoded];
                        dhtmlx.message({text:'<?php echo _l('Filter group added', 1); ?>',type:'info',expire:3000});
                        dhxlTrendsShopFilters_Tb.setItemText('trends_shop_views', ts_filter_view_name);
                    } else {
                        dhtmlx.message({text:'<?php echo _l('Error during add filter group', 1); ?>',type:'error',expire:3000});
                    }
                });
            } else {
                dhtmlx.message({text:'<?php echo _l('Please fill a valid name for filter group', 1); ?>',type:'error',expire:3000});
            }
        }

        if (id=='btn_del'){

            if(ts_filter_used == 'all') {
                alert('<?php echo _l('You can not delete this filter group', 1); ?>');
            } else {
                dhtmlx.confirm("<?php echo _l('You will delete the filter group', 1); ?>: "+ts_filter_used, function(result)
                {
                    if(result)
                    {
                        $.post("index.php?ajax=1&act=all_win-trendsshop_tree_custom_update", {'action':'delete', 'filter_used': ts_filter_used}, function (data) {
                            if(data !== 'KO') {
                                dhxlTrendsShopFilters_Tb.removeListOption('trends_shop_views', data);
                                dhtmlx.message({text:'<?php echo _l('Filter group', 1); ?>: '+ts_filter_used+' <?php echo _l('deleted', 1); ?>',type:'info',expire:3000});
                                ts_filter_used = 'all';
                                dhxlTrendsShopFilters_Tb.setItemText('trends_shop_views', '<?php echo _l('Filter group'); ?>');
                            }
                        });
                    }
                });
            }
        }

        //filter view selection
        if (ts_filter_views_list[id]!= null && ts_filter_views_list[id]!= undefined && ts_filter_views_list[id]!= ''){
            ts_filter_used = id;
            dhxlTrendsShopFilters_Tb.setItemText('trends_shop_views', ts_filter_used);
            checkBoxes(id);
        }
    });

    // -- Filter tree
    dhxlTrendsShopFilters_Tree = dhxlTrendsShopFilters.attachTree();
    dhxlTrendsShopFilters_Tree._name='trendsshopfilterstree';
    dhxlTrendsShopFilters_Tree.autoScroll=false;
    dhxlTrendsShopFilters_Tree.setImagePath('lib/js/imgs/dhxtree_material/');
    dhxlTrendsShopFilters_Tree.enableSmartXMLParsing(true);
    dhxlTrendsShopFilters_Tree.enableCheckBoxes(true, false);
    var ts_date_start = '';
    var ts_date_end = '';
    displayTrendsShopFiltersTree(1);


    // Pivot Col
    dhxlTrendsShopContent = dhxlTrendsShop.cells('b');
    dhxlTrendsShopContent.hideHeader();



    // -- Filter Col : tsf_content_ => trend shop content
    var ts_content_filter_used = '';
    var ts_content_filter_views_list = [];
    var ts_content_filter_views = [];
    ts_content_filter_views.push(['default', 'obj', '<?php echo _l('Default'); ?>', '']);
    <?php
        /*
         * config prédéfinies
         */
        $default_configs = array();
        #Boutiques
        if (SCMS)
        {
            $default_configs[] = array(
                'name' => _l('Shops'),
                'value' => array(
                    'rows' => array('shop'),
                    'columns' => array(
                        array(
                            'id' => 'date_add',
                            'group' => 'dateByYear',
                        ),
                    ),
                    'values' => array(
                        array(
                            'id' => 'total_paid_tax_excl',
                            'method' => 'sum',
                        ),
                        array(
                            'id' => 'total_paid_tax_incl',
                            'method' => 'sum',
                        ),
                    ),
                ),
            );
        }

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            #Transporteurs
            $default_configs[] = array(
                'name' => _l('Carriers'),
                'value' => array(
                    'rows' => array('country', 'carrier'),
                    'columns' => array(
                        array(
                            'id' => 'date_add',
                            'group' => 'dateByYear',
                        ),
                    ),
                    'values' => array(
                        array(
                            'id' => 'id_order',
                            'method' => 'count',
                        ),
                        array(
                            'id' => 'total_shipping_tax_excl',
                            'method' => 'sum',
                        ),
                        array(
                            'id' => 'total_shipping_tax_incl',
                            'method' => 'sum',
                        ),
                    ),
                ),
            );
        }

        # Moyens de paiement
        $default_configs[] = array(
            'name' => _l('Payment method'),
            'value' => array(
                'rows' => array('country', 'payment'),
                'columns' => array(
                    array(
                        'id' => 'date_add',
                        'group' => 'dateByYear',
                    ),
                ),
                'values' => array(
                    array(
                        'id' => 'id_order',
                        'method' => 'count',
                    ),
                    array(
                        'id' => 'total_paid_tax_excl',
                        'method' => 'sum',
                    ),
                    array(
                        'id' => 'total_paid_tax_incl',
                        'method' => 'sum',
                    ),
                ),
            ),
        );

        # Langues
        $default_configs[] = array(
            'name' => _l('Language'),
            'value' => array(
                'rows' => array('language'),
                'columns' => array(
                    array(
                        'id' => 'date_add',
                        'group' => 'dateByYear',
                    ),
                ),
                'values' => array(
                    array(
                        'id' => 'id_order',
                        'method' => 'count',
                    ),
                    array(
                        'id' => 'total_paid_tax_excl',
                        'method' => 'sum',
                    ),
                ),
            ),
        );

        # Pays
        $default_configs[] = array(
            'name' => _l('Country'),
            'value' => array(
                'rows' => array('country'),
                'columns' => array(
                    array(
                        'id' => 'date_add',
                        'group' => 'dateByYear',
                    ),
                ),
                'values' => array(
                    array(
                        'id' => 'id_order',
                        'method' => 'count',
                    ),
                    array(
                        'id' => 'total_paid_tax_excl',
                        'method' => 'sum',
                    ),
                ),
            ),
        );

        $dont_delete = array();
        foreach ($default_configs as $conf)
        {
            $dont_delete[] = $conf['name'];
        }

        if ($custom_configs = CustomSettings::getCustomSettingDetail('all', 'ts_content_filters'))
        {
            $all_configurations = array_merge($default_configs, $custom_configs);
        }
        else
        {
            $all_configurations = $default_configs;
        }

        if (version_compare(_PS_VERSION_, '1.5.0.1', '<'))
        {
            foreach ($all_configurations as $key_conf => $conf)
            {
                $already_total_paid = 0;
                foreach ($conf['value']['values'] as $key => $value)
                {
                    if ($value['id'] == 'total_paid_tax_excl')
                    {
                        unset($all_configurations[$key_conf]['value']['values'][$key]);
                    }
                    if ($value['id'] == 'total_paid_tax_incl')
                    {
                        $all_configurations[$key_conf]['value']['values'][$key]['id'] = 'total_paid';
                        $already_total_paid = 1;
                    }
                }
                if (empty($already_total_paid))
                {
                    $all_configurations[$key_conf]['value']['values'][] = array('id' => 'total_paid', 'method' => 'sum');
                }
                $all_configurations[$key_conf]['value']['values'] = array_values($all_configurations[$key_conf]['value']['values']);
            }
        }
    ?>
    tsc_dont_delete = ["<?php echo implode('","', $dont_delete); ?>"];
    ts_content_custom_filter_views = <?php echo json_encode($all_configurations); ?>;
    if(ts_content_custom_filter_views!== null && ts_content_custom_filter_views !== '') {
        ts_content_custom_filter_views.forEach(function (item) {
            ts_content_filter_views.push([item.name, 'obj', item.name, '']);
            ts_content_filter_views_list[item.name] = [item.value];
        });
    }
    ts_content_filter_views.push(['separator1', 'sep', '', '']);
    ts_content_filter_views.push(['btn_save', 'obj', '<?php echo _l('Save configuration'); ?>', '']);
    ts_content_filter_views.push(['btn_del', 'obj', '<?php echo _l('Delete configuration'); ?>', '']);

    // -- Pivot Col : Toolbar
    dhxlTrendsShopContent_Tb = dhxlTrendsShopContent.attachToolbar();
     dhxlTrendsShopContent_Tb.setIconset('awesome');
    dhxlTrendsShopContent_Tb.addButtonSelect('trends_shop_content_views',0,'<?php echo _l('Configuration'); ?>',ts_content_filter_views,'fad fa-flag blue','fad fa-flag blue',false,true);
    dhxlTrendsShopContent_Tb.addButton("refresh", 10, "", "fa fa-sync green", "fa fa-sync green");
    dhxlTrendsShopContent_Tb.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
    dhxlTrendsShopContent_Tb.addButton("exportcsv", 10, "", "fad fa-file-csv green", "fad fa-file-csv green");
    dhxlTrendsShopContent_Tb.setItemToolTip('exportcsv','<?php echo _l('Export pivot data into excel file.'); ?>');
    dhxlTrendsShopContent_Tb.attachEvent("onClick",function(id){
        contentPivot = dhxlTrendsShopContent.getFrame().contentWindow.pivot;
        if (id=='refresh')
        {
            if (ts_content_filter_used != null && ts_content_filter_used != undefined && ts_content_filter_used != '') {
                var fields = ts_content_filter_views_list[ts_content_filter_used];
                displayTrendsShopContent(fields);
            } else {
                displayTrendsShopContent();
            }
        }
        if (id=='exportcsv')
        {
            contentPivot.export({
                name:"pivot_data",
                url:"//export.dhtmlx.com/excel"
            });
        }
        if(id=='default') {
            ts_content_filter_used = id;
            dhxlTrendsShopContent_Tb.setItemText('trends_shop_content_views', '<?php echo _l('Default'); ?>');
            displayTrendsShopContent();
        }

        if (id=='btn_save'){
            var ts_content_filter_view_encoded = contentPivot.getFields();
            var ts_content_filter_view_name=prompt('<?php echo _l('Name of your configuration:', 1); ?>');
            if (ts_content_filter_view_name!==null && ts_content_filter_view_name!=='') {
                $.post("index.php?ajax=1&act=all_win-trendsshop_content_custom_update", {'action':'add', 'filter_view_encoded': ts_content_filter_view_encoded, 'filter_view_name': ts_content_filter_view_name}, function (data) {
                    if(data !== 'KO') {
                        var positionNew = 1;
                        for (var i = 0; i < ts_content_filter_views.length; i++) {
                            if(ts_content_filter_views[i][0] == 'btn_save') {
                                positionNew = i;
                            }
                        }
                        dhxlTrendsShopContent_Tb.addListOption('trends_shop_content_views', data, positionNew,'button',data);
                        ts_content_filter_views.push([data, 'obj', data, '']);
                        ts_content_filter_views_list[data] = [ts_content_filter_view_encoded];
                        dhtmlx.message({text:'<?php echo _l('Configuration added', 1); ?>',type:'info',expire:3000});
                        dhxlTrendsShopContent_Tb.setItemText('trends_shop_content_views', ts_content_filter_view_name);
                    } else {
                        dhtmlx.message({text:'<?php echo _l('Error during add configuration', 1); ?>',type:'error',expire:3000});
                    }
                });
            } else {
                dhtmlx.message({text:'<?php echo _l('Please choose a valid name for configuration', 1); ?>',type:'error',expire:3000});
            }
        }

        if (id=='btn_del'){
            if(ts_content_filter_used == 'default' ||
                tsc_dont_delete.indexOf(ts_content_filter_used) >= 0) {
                alert('<?php echo _l('You can not delete this configuration', 1); ?>');
            } else {
                dhtmlx.confirm("<?php echo _l('You will delete the configuration', 1); ?>: "+ts_content_filter_used, function(result)
                {
                    if(result)
                    {
                        $.post("index.php?ajax=1&act=all_win-trendsshop_content_custom_update", {'action':'delete', 'filter_used': ts_content_filter_used}, function (data) {
                            if(data !== 'KO') {
                                dhxlTrendsShopContent_Tb.removeListOption('trends_shop_content_views', data);
                                dhtmlx.message({text:'<?php echo _l('Configuration', 1); ?> : '+ts_content_filter_used+' <?php echo _l('deleted', 1); ?>',type:'info',expire:3000});
                                ts_filter_used = 'default';
                                dhxlTrendsShopContent_Tb.setItemText('trends_shop_content_views', '<?php echo _l('Configuration'); ?>');
                            }
                        });
                    }
                });
            }
        }

        //filter view selection
        if (ts_content_filter_views_list[id]!= null && ts_content_filter_views_list[id]!= undefined && ts_content_filter_views_list[id]!= ''){
            ts_content_filter_used = id;
            dhxlTrendsShopContent_Tb.setItemText('trends_shop_content_views', ts_content_filter_used);
            loadTsContent(contentPivot, ts_content_filter_used);
        }
    });
    displayTrendsShopContent();



    /*
     * FUNCTIONS
     */
    function loadTsContent(pivot, filter_name)
    {
        var fields = ts_content_filter_views_list[filter_name];
        displayTrendsShopContent(fields)
    }

    function displayTrendsShopContent(pivotFields)
    {
        var filters_selected = dhxlTrendsShopFilters_Tree.getAllChecked();
        var filter_arr = [];
        fillFilterArray(filters_selected, filter_arr);
        if(ts_date_start !== '' && ts_date_start !== 'undefined' && ts_date_start !== null) {
            filter_arr.push('date_start#'+ts_date_start);
        }
        if(ts_date_end !== '' && ts_date_end !== 'undefined' && ts_date_end !== null) {
            filter_arr.push('date_end#'+ts_date_end);
        }
        var fields = '';
        if(pivotFields !== '' && pivotFields !== 'undefined' && pivotFields !== null) {
            fields = JSON.stringify(pivotFields);
        }

        dhxlTrendsShopContent.progressOn();
        dhxlTrendsShopContent.attachURL('index.php?ajax=1&act=all_win-trendsshop_content&id_lang='+SC_ID_LANG,null,{filters : filter_arr, fields : fields});

    }

    function fillFilterArray(f_arg, f_array)
    {
        var filters = f_arg.split(',');
        $.each(filters, function(index, id) {
            if(id.substr(0,4) !== "none" && id != 'date_start' && id != 'date_end') {
                f_array.push(id);
            }
        });
    }

    function displayTrendsShopFiltersTree(firstime)
    {

        dhxlTrendsShopFilters_Tree.deleteChildItems(0);
        dhxlTrendsShopFilters_Tree.load("index.php?ajax=1&act=all_win-trendsshop_tree_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
        {
            if(firstime === 1) {
                dhxlTrendsShopFilters_Tree.attachEvent("onBeforeCheck", checkDate);
                dhxlTrendsShopFilters_Tree.attachEvent("onCheck", checkingTrendsShopTree);
            }
            dhxlTrendsShopFilters_Tree.closeAllItems(0);
        });
    }

    function checkBoxes(ts_view)
    {
        if (ts_filter_views_list[ts_view]!= null && ts_filter_views_list[ts_view]!= undefined && ts_filter_views_list[ts_view]!= '') {
            var ts_data = ts_filter_views_list[ts_view][0];
            var ts_splitedData = ts_data.split(',');
            $.each(ts_splitedData, function(index, id) {
                var parent = dhxlTrendsShopFilters_Tree.getParentId(id);
                dhxlTrendsShopFilters_Tree.openItem(parent);
                dhxlTrendsShopFilters_Tree.setCheck(id,1);
            });
        }
    }
    
    function checkDate(id_parent, state)
    {
        var pattern = new RegExp("[0-9]{8}");
        if(id_parent == "date_start") {
            if(state == 0) {
                var ts_date_start_value = prompt('<?php echo _l('Date start (format:YYYYMMDD)', 1); ?> :');
                if(pattern.test(ts_date_start_value) != true) {
                    alert("<?php echo _l('Invalid date format'); ?>");
                    return false;
                } else {
                    ts_date_start = ts_date_start_value;
                    dhxlTrendsShopFilters_Tree.setItemText(id_parent, '<?php echo _l('Starting'); ?> ' + ts_date_start_value);
                    dhxlTrendsShopFilters_Tree.setCheck(id_parent,1);
                }
            } else {
                ts_date_start = '';
                dhxlTrendsShopFilters_Tree.setItemText(id_parent, '<?php echo _l('Starting'); ?> [date]');
                dhxlTrendsShopFilters_Tree.setCheck(id_parent,0);
            }
        } else if (id_parent == "date_end") {
            if(state == 0) {
                var ts_date_end_value=prompt('<?php echo _l('Date end (format:YYYYMMDD)', 1); ?> :');
                if(pattern.test(ts_date_end_value) != true) {
                    alert("<?php echo _l('Invalid date format'); ?>");
                    return false;
                } else {
                    ts_date_end = ts_date_end_value;
                    dhxlTrendsShopFilters_Tree.setItemText(id_parent, '<?php echo _l('Ending'); ?> '+ts_date_end_value);
                    dhxlTrendsShopFilters_Tree.setCheck(id_parent,1);
                }
            }else {
                ts_date_end = '';
                dhxlTrendsShopFilters_Tree.setItemText(id_parent, '<?php echo _l('Ending'); ?> [date]');
                dhxlTrendsShopFilters_Tree.setCheck(id_parent,0);
            }
        } else {
            return true;
        }
    }

    function checkingTrendsShopTree(id_parent, state)
    {
        if(id_parent !== "date_start" && id_parent !== "date_end") {
            var children = dhxlTrendsShopFilters_Tree.getAllSubItems(id_parent);
            if(children != '' && children != "undefined" && children != null){
                var sub_items = children.split(',');
                $.each(sub_items, function(index, id) {
                    dhxlTrendsShopFilters_Tree.setCheck(id,state);
                });
            }
        }
    }
<?php echo '</script>'; ?>