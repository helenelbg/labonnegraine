<?php

?>
<script type="text/javascript">

    var allowed_properties_panel = new Array();
    prop_tb=dhxLayout.cells('b').attachToolbar();
    prop_tb.setIconset('awesome');
    prop_tb._sb=dhxLayout.cells('b').attachStatusBar();
    icons=Array();

    prop_tb.addButtonSelect('panel',0,'<?php echo _l('Orders and products', 1); ?>',icons,'fa fa-search blue','fa fa-search blue',false,true);
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
        if (id=='help'){
            <?php echo "window.open('".getScExternalLink('support_customers')."');"; ?>
        }
        
        prop_tb._sb.setText('');

        dhxLayout.cells('b').showHeader();
    }

    prop_tb.attachEvent("onClick", setPropertiesPanel);

    function setPropertiesPanelState(id,state){

    }

    prop_tb.attachEvent("onStateChange", setPropertiesPanelState);


//#####################################
//############ Load functions
//#####################################

<?php
    echo eval('?>'.$pluginProductProperties['DisplayPlugin'].'<?php ');

    //##################
    //##################
    //################## Add internal extensions
    //##################
    //##################

    @$files = scandir(SC_DIR.'lib/cusm/');
    foreach ($files as $item)
    {
        if ($item != '.' && $item != '..')
        {
            if (is_dir(SC_DIR.'lib/cusm/'.$item) && file_exists(SC_DIR.'lib/cusm/'.$item.'/cusm_'.$item.'_init.js.php') && substr($item, 0, 4) != 'win-')
            {
                require_once SC_DIR.'lib/cusm/'.$item.'/cusm_'.$item.'_init.js.php';
            }
            elseif (is_dir(SC_DIR.'lib/cusm/'.$item) && file_exists(SC_DIR.'lib/cusm/'.$item.'/cusm_'.$item.'_init.php') && substr($item, 0, 4) != 'win-')
            {
                require_once SC_DIR.'lib/cusm/'.$item.'/cusm_'.$item.'_init.php';
            }
        }
    }
?>



    prop_tb.addButton("help",1200, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    prop_tb.setItemToolTip('help','<?php echo _l('Help'); ?>');

    if(!in_array(propertiesPanel, allowed_properties_panel) && allowed_properties_panel.length>0)
        propertiesPanel = allowed_properties_panel[0];
    if(allowed_properties_panel.length>0)
        prop_tb.callEvent("onClick",[propertiesPanel]);
    else
    {
        prop_tb.unload();
        dhxLayout.cells('b').collapse();
    }
    
</script>