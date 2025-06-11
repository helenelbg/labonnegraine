<script type="text/javascript">
    function inArray(array, p_val) {
        var l = array.length;
        for(var i = 0; i < l; i++) {
            if(array[i] == p_val) {
                return true;
            }
        }
        return false;
    }

    var allowed_properties_panel = new Array();
    var is_prop_4columns = false;
    var prop_4column_layout = null;
    prop_tb=dhxLayout.cells('b').attachToolbar();
    prop_tb.setIconset('awesome');
    prop_tb._sb=dhxLayout.cells('b').attachStatusBar();
    icons=Array(
        <?php
        echo eval('?>'.$pluginProductProperties['Title'].'<?php ');
        ?>
    );
    <?php
    echo eval('?>'.$pluginProductProperties['ToolbarButtons'].'<?php ');
    ?>


    prop_tb.addButtonSelect('panel',0,'<?php echo _l('Combinations', 1); ?>',icons,'fa fa-search blue','fa fa-search blue',false,true);
    prop_tb.setItemToolTip('panel','<?php echo _l('Select properties panel', 1); ?>');

    function hidePropTBButtons()
    {
        is_prop_4columns = false;
        prop_4column_layout = null;
        prop_tb.forEachItem(function(itemId){
            prop_tb.hideItem(itemId);
        });
        prop_tb.showItem('panel');
        prop_tb.showItem('help');
    }

    function setPropertiesPanel(id){
        if (id=='help'){
            <?php echo "window.open('".getScExternalLink('support_home')."');"; ?>
        }
        <?php echo $prop_toolbar_js_action; ?>
    }

    prop_tb.attachEvent("onClick", setPropertiesPanel);

    function setPropertiesPanelState(id,state){
        <?php
        echo eval('?>'.$pluginProductProperties['ToolbarStateActions'].'<?php ');
        ?>
    }

    prop_tb.attachEvent("onStateChange", setPropertiesPanelState);


    //#####################################
    //############ Load functions
    //#####################################

    <?php
    echo eval('?>'.$pluginProductProperties['DisplayPlugin'].'<?php ');

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !Combination::isFeatureActive())
    {
        ?>
    if (propertiesPanel=='combinations' || propertiesPanel=='combinationmultiproduct')
        propertiesPanel='images';
    <?php
    }

    //##################
    //##################
    //################## Add internal extensions
    //##################
    //##################

    @$files = scandir(SC_DIR.'lib/cat/');
    if (file_exists(SC_TOOLS_DIR.'lib/cat/'))
    {
        @$files_tools = scandir(SC_TOOLS_DIR.'lib/cat/');
        if (!empty($files_tools))
        {
            @$files = array_merge($files, $files_tools);
        }
    }

    if (($key = array_search('description', $files)) !== false)
    {
        unset($files[$key]);
    }
    $files[0] = 'description';
    $have_sub_properties = array(
        'combination',
        'combinationmultiproduct',
        'description',
    );

    foreach ($files as $item)
    {
        if ($item != '.' && $item != '..')
        {
            if (is_dir(SC_TOOLS_DIR.'lib/cat/'.$item) && substr($item, 0, 4) != 'win-')
            {
                // OVERRIDE
                if (file_exists(SC_TOOLS_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.js.php'))
                {
                    require_once SC_TOOLS_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.js.php';
                }
                elseif (file_exists(SC_TOOLS_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.php'))
                {
                    require_once SC_TOOLS_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.php';
                }
                elseif (is_dir(SC_DIR.'lib/cat/'.$item) && file_exists(SC_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.js.php') && substr($item, 0, 4) != 'win-')
                {
                    require_once SC_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.js.php';
                }
                elseif (is_dir(SC_DIR.'lib/cat/'.$item) && file_exists(SC_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.php') && substr($item, 0, 4) != 'win-')
                {
                    require_once SC_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.php';
                }
                if (in_array($item, $have_sub_properties))
                {
                    @$sub_files = scandir(SC_TOOLS_DIR.'lib/cat/'.$item);
                    $sub_items_override = array();
                    foreach ($sub_files as $sub_item)
                    {
                        if ($sub_item != '.' && $sub_item != '..')
                        {
                            if (is_dir(SC_TOOLS_DIR.'lib/cat/'.$item.'/'.$sub_item) && file_exists(SC_TOOLS_DIR.'lib/cat/'.$item.'/'.$sub_item.'/cat_'.$item.'_'.$sub_item.'_init.js.php'))
                            {
                                require_once SC_TOOLS_DIR.'lib/cat/'.$item.'/'.$sub_item.'/cat_'.$item.'_'.$sub_item.'_init.js.php';
                                $sub_items_override[] = $sub_item;
                            }
                        }
                    }
                    // STANDARD BEHAVIOR
                    @$sub_files = scandir(SC_DIR.'lib/cat/'.$item);
                    foreach ($sub_files as $sub_item)
                    {
                        if ($sub_item != '.' && $sub_item != '..' && !in_array($sub_item, $sub_items_override))
                        {
                            if (is_dir(SC_DIR.'lib/cat/'.$item.'/'.$sub_item) && file_exists(SC_DIR.'lib/cat/'.$item.'/'.$sub_item.'/cat_'.$item.'_'.$sub_item.'_init.js.php'))
                            {
                                require_once SC_DIR.'lib/cat/'.$item.'/'.$sub_item.'/cat_'.$item.'_'.$sub_item.'_init.js.php';
                            }
                        }
                    }
                }
            }
            elseif (is_dir(SC_DIR.'lib/cat/'.$item) && file_exists(SC_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.js.php') && substr($item, 0, 4) != 'win-')
            {
                // STANDARD BEHAVIOR
                require_once SC_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.js.php';
                if (in_array($item, $have_sub_properties))
                {
                    @$sub_files = scandir(SC_DIR.'lib/cat/'.$item);
                    foreach ($sub_files as $sub_item)
                    {
                        if ($sub_item != '.' && $sub_item != '..')
                        {
                            if (is_dir(SC_DIR.'lib/cat/'.$item.'/'.$sub_item) && file_exists(SC_DIR.'lib/cat/'.$item.'/'.$sub_item.'/cat_'.$item.'_'.$sub_item.'_init.js.php'))
                            {
                                require_once SC_DIR.'lib/cat/'.$item.'/'.$sub_item.'/cat_'.$item.'_'.$sub_item.'_init.js.php';
                            }
                        }
                    }
                }
            }
            elseif (is_dir(SC_DIR.'lib/cat/'.$item) && file_exists(SC_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.php') && substr($item, 0, 4) != 'win-')
            {
                // STANDARD BEHAVIOR
                require_once SC_DIR.'lib/cat/'.$item.'/cat_'.$item.'_init.php';
                if (in_array($item, $have_sub_properties))
                {
                    @$sub_files = scandir(SC_DIR.'lib/cat/'.$item);
                    foreach ($sub_files as $sub_item)
                    {
                        if ($sub_item != '.' && $sub_item != '..')
                        {
                            if (is_dir(SC_DIR.'lib/cat/'.$item.'/'.$sub_item) && file_exists(SC_DIR.'lib/cat/'.$item.'/'.$sub_item.'/cat_'.$item.'_'.$sub_item.'_init.php'))
                            {
                                require_once SC_DIR.'lib/cat/'.$item.'/'.$sub_item.'/cat_'.$item.'_'.$sub_item.'_init.php';
                            }
                        }
                    }
                }
            }
        }
    }
    ?>


    var allTabs=["combinations","combinationmultiproduct", "descriptions", "images", "accessories", "attachments", "specificprices", "discounts", "features", "tags", "categories", "customizations", "shopshare", "msproduct", "mscombination", "warehouseshare", "warehousestock", "customerbyproduct"];
    var localTabs=prop_tb.getAllListOptions('panel');
    for (var i = 0; i < allTabs.length; i++) {
        if (in_array(allTabs[i],localTabs))
            prop_tb.setListOptionPosition('panel',allTabs[i], i+1);
    }

    prop_tb.addButton("help",1200, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    prop_tb.setItemToolTip('help','<?php echo _l('Help'); ?>');

    if(!inArray(allowed_properties_panel, propertiesPanel) && allowed_properties_panel.length>0)
        propertiesPanel = allowed_properties_panel[0];
    if(allowed_properties_panel.length>0){
        prop_tb.callEvent("onClick",[propertiesPanel]);
    }
    else
    {
        prop_tb.unload();
        dhxLayout.cells('b').collapse();
    }


    //#####################################
    //############ SORT LIST OPTIONS
    //#####################################
    var all_panel_options = new Object();
    prop_tb.forEachListOption('panel', function(optionId){
        var pos = prop_tb.getListOptionPosition('panel', optionId);
        var text = prop_tb.getListOptionText('panel', optionId);
        all_panel_options[text] = optionId;
    });

    var myObj = all_panel_options,
        keys = [],
        k, i, len;

    for (k in myObj)
    {
        if (myObj.hasOwnProperty(k))
            keys.push(k);
    }

    function frsort(a,b) {
        return a.localeCompare(b);
    }

    keys.sort(frsort);
    len = keys.length;

    for (i = 0; i < len; i++)
    {
        k = keys[i];
        prop_tb.setListOptionPosition('panel', myObj[k], (i*1+1));
    }

    <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '<') || Combination::isFeatureActive()) { ?>
    prop_tb.attachEvent("onClick", function(id){
        if (id=='combinations')
        {
            hideSubpropertiesItems();
            switch("<?php echo _s('CAT_PROD_COMBI_DEFAULT_SUBCOMBI'); ?>"){
                case "image":
                    initCombinationImage();
                    break;
                case "shopshare":
                    break;
                case "specificprice":
                    initCombinationSpecificPrices();
                    break;
                case "stats":
                    initCombinationStats();
                    break;
                case "supplier":
                    initCombinationSuppliershare();
                    break;
                case "warehouseshare":
                    initCombinationWarehouseshare();
                    break;
                default:
                    initCombinationImage();
            }
        }else if(id=='combinationmultiproduct')
        {
            hideCombinationMultiProduct_SubpropertiesItems();
            initCombinationMultiProductImage();
        }
    });
    <?php } ?>
</script>
