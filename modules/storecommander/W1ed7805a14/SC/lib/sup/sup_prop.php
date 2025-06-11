<script type="text/javascript">
    var allowed_properties_panel = [];
    prop_tb=dhxLayout.cells('b').attachToolbar();
    prop_tb.setIconset('awesome');
    prop_tb._sb=dhxLayout.cells('b').attachStatusBar();
    icons=[
<?php
    echo eval('?>'.$pluginSupplierProperties['Title'].'<?php ');
?>];
<?php
    echo eval('?>'.$pluginSupplierProperties['ToolbarButtons'].'<?php ');
?>


    prop_tb.addButtonSelect('panel',0,'<?php echo _l('Descriptions', 1); ?>',icons,'fa fa-search blue','fa fa-search blue',false,true);
    prop_tb.setItemToolTip('panel','<?php echo _l('Select properties panel', 1); ?>');

    function hidePropTBButtons()
    {
        prop_tb.forEachItem(function(itemId){
            prop_tb.hideItem(itemId);
        });
        prop_tb.showItem('panel');
        prop_tb.showItem('help');
    }

    function setPropertiesPanel(id){
        <?php echo $prop_toolbar_js_action; ?>
    }

    prop_tb.attachEvent("onClick", setPropertiesPanel);

    function setPropertiesPanelState(id,state){
<?php
    echo eval('?>'.$pluginSupplierProperties['ToolbarStateActions'].'<?php ');
?>
    }

    prop_tb.attachEvent("onStateChange", setPropertiesPanelState);


//#####################################
//############ Load functions
//#####################################

<?php
    echo eval('?>'.$pluginSupplierProperties['DisplayPlugin'].'<?php ');

    //##################
    //##################
    //################## Add internal extensions
    //##################
    //##################

    @$files = scandir(SC_DIR.'lib/sup/');
    foreach ($files as $item)
    {
        if (!in_array($item, array('.', '..')))
        {
            if (is_dir(SC_TOOLS_DIR.'lib/sup/'.$item)
                && file_exists(SC_TOOLS_DIR.'lib/sup/'.$item.'/sup_'.$item.'_init.js.php')
                && substr($item, 0, 4) != 'win-')
            {
                // OVERRIDE
                require_once SC_TOOLS_DIR.'lib/sup/'.$item.'/sup_'.$item.'_init.js.php';
            }
            elseif (is_dir(SC_DIR.'lib/sup/'.$item)
                && file_exists(SC_DIR.'lib/sup/'.$item.'/sup_'.$item.'_init.js.php')
                && substr($item, 0, 4) != 'win-')
            {
                // STANDARD BEHAVIOR
                require_once SC_DIR.'lib/sup/'.$item.'/sup_'.$item.'_init.js.php';
            }
        }
    }
?>


    var allTabs=["description","image",'products','seo','shopshare'];
    var localTabs=prop_tb.getAllListOptions('panel');
    for (let i = 0; i < allTabs.length; i++) {
        if (localTabs.includes(allTabs[i])) {
            prop_tb.setListOptionPosition('panel', allTabs[i], i + 1);
        }
    }

    prop_tb.addButton("help",1200, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    prop_tb.setItemToolTip('help','<?php echo _l('Help'); ?>');

    if(!allowed_properties_panel.includes(propertiesPanel) && allowed_properties_panel.length>0) {
        propertiesPanel = allowed_properties_panel[0];
    }
    if(allowed_properties_panel.length>0) {
        prop_tb.callEvent("onClick", [propertiesPanel]);
    }
    else
    {
        prop_tb.unload();
        dhxLayout.cells('b').collapse();
    }


//#####################################
//############ SORT LIST OPTIONS
//#####################################
var all_panel_options = {};
prop_tb.forEachListOption('panel', function(optionId){
     all_panel_options[prop_tb.getListOptionText('panel', optionId)] = optionId;
});

var myObj = all_panel_options,
keys = [],
k, i, len;

for (k in myObj)
{
    if (myObj.hasOwnProperty(k)) {
        keys.push(k);
    }
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

</script>
