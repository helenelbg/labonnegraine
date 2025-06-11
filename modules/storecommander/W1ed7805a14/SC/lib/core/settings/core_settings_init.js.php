<?php echo '<script type="text/javascript">'; ?>
    var lSettings = new dhtmlXLayoutObject(wCoreSettings, "1C");
    lSettings.cells('a').hideHeader();
    settings_grid=lSettings.cells('a').attachGrid();
    settings_grid.setImagePath('lib/js/imgs/');
    settings_grid.setHeader("<?php echo _l('Tool'); ?>,<?php echo _l('Section'); ?>,<?php echo _l('Item'); ?>,<?php echo _l('Value'); ?>,<?php echo _l('Description'); ?>,<?php echo _l('Default value'); ?>");
    settings_grid.setColumnIds("section1,section2,id,value,description,default_value");
    settings_grid.setInitWidths("75,75,200,100,400,100");
    settings_grid.setColAlign("left,left,left,left,left,left");
    settings_grid.setColTypes("ro,ro,ro,ed,ro,ro");
  settings_grid.enableSmartRendering(true);
  settings_grid.enableMultiline(true);
    settings_grid.setColSorting("str,str,str,str,str,str");
    settings_grid.attachHeader("#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter");
    settings_grid.init();

    settingsDataProcessorURLBase="index.php?ajax=1&act=core_settings_update";
    settingsDataProcessor = new dataProcessor(settingsDataProcessorURLBase);
    settingsDataProcessor.enableDataNames(true);
    settingsDataProcessor.enablePartialDataSend(true);
    settingsDataProcessor.setUpdateMode('cell');
    settingsDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
            if (action=='updateAndRefresh')
                dhtmlx.message({text:lang_refresh_SC,type:'error'});
        });

    settingsDataProcessor.init(settings_grid);  

    settings_tb=lSettings.cells('a').attachToolbar();
    settings_tb.setIconset('awesome');

    settings_tb.addButtonTwoState('lightNavigation', 0, "", "fa fa-mouse-pointer", "fa fa-mouse-pointer");
    settings_tb.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)', 1); ?>');
    settings_tb.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    settings_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
    settings_tb.attachEvent("onClick",
        function(id){

            if (id=='refresh'){
                displaySettings();
            }
        });


    settings_tb.attachEvent("onStateChange",function(id,state){
        if (id=='lightNavigation')
        {
            if (state)
            {
                settings_grid.enableLightMouseNavigation(true);
            }else{
                settings_grid.enableLightMouseNavigation(false);
            }
        }
    });



    function displaySettings(callback)
    {
        settings_grid.clearAll();
        settings_grid.load("index.php?ajax=1&act=core_settings_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
            getRowsNum=settings_grid.getRowsNum();
            <?php
                $param_id = null;
                $url_params = Tools::getValue('urlparams', null);
                if (!empty($url_params))
                {
                    if (!empty($url_params['id']))
                    {
                        $param_id = $url_params['id'];
                        unset($url_params['id']);
                    }
                    foreach ($url_params as $section => $value)
                    {
                        echo 'var idx'.$section.' = settings_grid.getColIndexById("'.$section.'");
                              var col'.$section.' = settings_grid.getFilterElement(idx'.$section.');
                              col'.$section.'.value = "'._l($value).'";'."\n";
                    }
                }
            ?>
            settings_grid.filterByAll();
            <?php if (!empty($param_id)) { ?>
            settings_grid.selectRowById('<?php echo $param_id; ?>');
            <?php } ?>
            if (callback!='') eval(callback);
        });
    }

    displaySettings();
<?php echo '</script>'; ?>