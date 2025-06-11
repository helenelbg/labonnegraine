<?php echo '<script type="text/javascript">'; ?>

    var tbAction = null;
    var gridActions = null;
    var terminator_cell = "all";
    const terminator_action_id = '<?php echo Tools::getValue('action_id', ''); ?>';

    dhxlTerminator=toolsTerminator.attachLayout("2E");

    dhxlTerminator.cells('a').setText("<?php echo _l('Actions'); ?>");

    dhtmlXSideBar.prototype.templates.details = "<i class='sidebar #icons_path##icon#'></i><div class='dhxsidebar_item_text'>#text#</div>";
    dhxlTerminator.sbActions=dhxlTerminator.cells('a').attachSidebar({
        template: "details",
        width: 200
    });

    var sidebar_items = [
        {
            id:         "all",
            text:       '<?php echo _l('All actions', 1); ?>',
            icon:       "fa fa-bolt yellow",
            selected:   true
        },
        {
            id:         "sep1",
            type:       "separator"
        },
        {
            id:         "maintenance",
            text:       '<?php echo _l('Maintenance', 1); ?>',
            icon:       "fad fa-cog yellow"
        },
        {
            id:         "db",
            text:       '<?php echo _l('Database', 1); ?>',
            icon:       "fa fa-database red"
        },
        {
            id:         "module",
            text:       '<?php echo _l('Plugins', 1); ?>',
            icon:       "fa fa-cubes"
        },
        {
            id:         "other",
            text:       '<?php echo _l('Various', 1); ?>',
            icon:       "fa fa-cog"
        },
        {
            id:         "files",
            text:       '<?php echo _l('Files', 1); ?>',
            icon:       "fad fa-file white"
        }
    ];
    dhxlTerminator.sbActions.loadStruct({items:sidebar_items}, function(){
        // data loaded and rendered
        // your code here
    });
    dhxlTerminator.sbActions.attachEvent("onSelect", function(id, lastId){
        terminator_cell = id;
        loadTeminatorCell(id);
    });

     function loadTeminatorCell(cell_name)
     {
         var cell =  dhxlTerminator.sbActions.cells(cell_name);

         cell.detachObject();
         cell.detachToolbar();

         tbActions=cell.attachToolbar();
         tbActions.setIconset('awesome');
         tbActions.addButton("terminator_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
         tbActions.setItemToolTip('terminator_refresh','<?php echo _l('Refresh grid', 1); ?>');
         tbActions.addButton("terminator_start", 100, "", "fad fa-play-circle blue", "fad fa-play-circle blue");
         tbActions.setItemToolTip('terminator_start','<?php echo _l('Start actions', 1); ?>');
         tbActions.attachEvent("onClick",
             function(id){
                 if (id=='terminator_refresh')
                 {
                     displayTeminatorGrid();
                 }
                 if (id=='terminator_start')
                 {
                     if (confirm('<?php echo _l('Are you sure that you want to start actions?', 1); ?>'))
                     {
                         var checked=gridActions.getCheckedRows(0);
                         if(checked!="" && checked!=null && checked!=0)
                         {
                             idxParam=gridActions.getColIndexById('param');
                             var is_ok = true;
                             var params = {};
                             checked_array = checked.split(",");
                             $(checked_array).each(function(index, rId) {
                                 var has_param = gridActions.getUserData(rId,"has_param");
                                 if(has_param=="1")
                                 {
                                     var val = gridActions.cells(rId,idxParam).getValue();
                                     if(val=="" || val==null)
                                     {
                                         is_ok = false;
                                         gridActions.cells(rId,idxParam).setBgColor("#FF0000");
                                     }
                                     else
                                     {
                                         params[rId] = val;
                                         gridActions.cells(rId,idxParam).setBgColor("");
                                     }
                                 }
                             });
                             if(is_ok==false)
                             {
                                 var msg = '<?php echo _l('Some selected actions require a parameter!', 1); ?>';
                                 dhtmlx.message({text:msg,type:'error',expire:10000});
                             }
                             else
                             {
                                 cell.progressOn();
                                 $.post("index.php?ajax=1&act=all_win-terminator_play&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'actions': checked, 'params': params},function(data)
                                 {
                                     cell.progressOff();
                                     displayTeminatorGrid();
                                 });
                             }
                         }
                     }
                 }
             });

        gridActions=cell.attachGrid();
        gridActions.setImagePath("lib/js/imgs/");
        gridActions.enableSmartRendering(false);
        gridActions.enableMultiselect(false);

         gridActions.attachEvent("onRowSelect", function(id,ind){
             dhxlTerminator.cells('b').expand();
             loadTerminatorActionInfo(id);
         });
         gridActions.attachEvent("onCheck", function(rId,cInd,state){
             idxParam=gridActions.getColIndexById('param');
             var has_param = gridActions.getUserData(rId,"has_param");
             if(has_param=="1")
             {
                 var val = gridActions.cells(rId,idxParam).getValue();
                 if(state==true && (val=="" || val==null))
                 {
                     gridActions.cells(rId,idxParam).setBgColor("#FF0000");
                 }
                 else
                 {
                     gridActions.cells(rId,idxParam).setBgColor("");
                 }
             }
         });

         displayTeminatorGrid();
     }

     function displayTeminatorGrid()
     {
         gridActions.clearAll(true);
         dhxlTerminator.sbActions.cells(terminator_cell).progressOn();
         $.post("index.php?ajax=1&act=all_win-terminator_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'type': terminator_cell},function(data)
         {
             dhxlTerminator.sbActions.cells(terminator_cell).progressOff();
             gridActions.parse(data);
             if(terminator_action_id !== ''){
                 gridActions.selectRowById(terminator_action_id,false,true,true);
             }
         });
     }

    loadTeminatorCell("all");

    dhxlTerminator.cells('b').setText("<?php echo _l('Information'); ?>");
    dhxlTerminator.cells('b').setHeight(200);
    dhxlTerminator.cells('b').collapse();

    function loadTerminatorActionInfo(rId)
    {
        if(rId!=undefined && rId!="" && rId!=null && rId!=0)
        {
            dhxlTerminator.cells('b').attachURL("index.php?ajax=1&act=all_win-terminator_getinfo&action="+rId+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime());
        }
    }
<?php echo '</script>'; ?>