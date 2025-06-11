<?php echo '<script type="text/javascript">'; ?>
    var selected_display_archived = 0;
    var selected_group_by_type = 0;

    // LAYOUT
    dhxleServicesProject_layout=weServicesProject.attachLayout("3L");

    var col_eSP_projects = dhxleServicesProject_layout.cells('a');
    col_eSP_projects.setText("<?php echo _l('Your projects'); ?>");
    eSP_projects_grid = col_eSP_projects.attachGrid();
    eSP_projects_grid.enableDragAndDrop(false);
    eSP_projects_grid.enableMultiselect(false);
    eSP_projects_grid.setImagePath('lib/js/imgs/');
    eSP_projects_grid.init();

    eSP_projects_grid_sb=col_eSP_projects.attachStatusBar();
    eSP_projects_grid_sb.setText('<div id="eSP_projects_started_statusqueue" style="color: #ff0000; font-weight: bold;display:none; margin-right: 20px;">'+loader_gif+' <?php echo _l("We are creating the elements at SC service's for the project", 1); ?> <span></span></div><div id="eSP_projects_checkstatus" style="color: #ff0000; font-weight: bold;display:none;">'+loader_gif+' <?php echo _l('We are checking the status of the project and importing data.', 1); ?></div>');

    // UISettings
    eSP_projects_grid._uisettings_prefix='fizz_projects';
    eSP_projects_grid._uisettings_name=eSP_projects_grid._uisettings_prefix;
    eSP_projects_grid._first_loading=1;
    // UISettings
    initGridUISettings(eSP_projects_grid);

    eSP_projects_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue) {
        idxStatus=eSP_projects_grid.getColIndexById('status');
        idxType=eSP_projects_grid.getColIndexById('type');
        if(cInd==idxStatus || cInd==idxType)
            return false;
    });
    var selected_project = 0;
    eSP_projects_grid.attachEvent("onRowSelect", function(id,ind){
        if(id!=selected_project)
        {
            selected_project = id;
            ESPloadConfig();

            displayESPElements();

            hideExtIcons();
            idxType=eSP_projects_grid.getColIndexById('type');
            var type = eSP_projects_grid.cells(selected_project,idxType).getValue();
            if(type=="cutout")
            {
                eSP_itemslist_tb.showItem("sepExt");
                eSP_itemslist_tb.showItem("eservices_list");

                col_eSP_config.collapse();
            }
            else
            {
                col_eSP_config.expand();
            }
        }
    });

    function ESPloadConfig()
    {
        idxType=eSP_projects_grid.getColIndexById('type');
        var type = eSP_projects_grid.cells(selected_project,idxType).getValue();
        col_eSP_config.progressOn();

        switch(type) {
            case 'image_compression':
                col_eSP_config.hideHeader();
                col_eSP_config.attachURL("index.php?ajax=1&act=ser_win-imagecompression_init&id_project="+selected_project,true,true);
                break;
            default:
                col_eSP_config.showHeader();
                col_eSP_config.attachURL("index.php?ajax=1&act=all_fizz_win-project_config&id_project="+selected_project+"&type="+type+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime());
        }
    }


    eSP_projects_tb = col_eSP_projects.attachToolbar();
    eSP_projects_tb.setIconset('awesome');
    eSP_projects_tb.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
    eSP_projects_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
    eSP_projects_tb.addButtonTwoState("display_archived", 100, "", "fad fa-folder grey", "fad fa-folder grey");
    eSP_projects_tb.setItemToolTip('display_archived','<?php echo _l('Show archived projects'); ?>');
    eSP_projects_tb.addButtonTwoState("group_by_type", 100, "", "fa fa-compress-arrows-alt green", "fa fa-compress-arrows-alt green");
    eSP_projects_tb.setItemToolTip('group_by_type','<?php echo _l('Group projects by type'); ?>');
    eSP_projects_tb.addSeparator('sep1', 100);
    <?php if (KAI9DF4 != 1) { ?>
    eSP_projects_tb.addButton("delete", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    eSP_projects_tb.setItemToolTip('delete','<?php echo _l('Delete project'); ?>');
    eSP_projects_tb.addSeparator('sep2', 100);
    eSP_projects_tb.addButton("checkStatus", 100, "", "fa fa-check green", "fa fa-check green");
    eSP_projects_tb.setItemToolTip('checkStatus','<?php echo _l('Check if processed & import datas'); ?>');
    eSP_projects_tb.addSeparator('sep3', 100);
    eSP_projects_tb.addButton("archive", 100, "", "fa fa-database red", "fa fa-database red");
    eSP_projects_tb.setItemToolTip('archive','<?php echo _l('Archive project'); ?>');
    if(selected_display_archived==1)
        eSP_projects_tb.setItemState('display_archived', true);
    <?php } ?>

    eSP_projects_tb.attachEvent("onClick",function(id){
        if(id=="refresh")
        {
            displayProjects();
        }
        if(id=="archive")
        {
            if(confirm('<?php echo _l('Are you sure you want to archive this project?', 1); ?>'))
            {
                var id_project = eSP_projects_grid.getSelectedRowId();
                if(id_project!=undefined && id_project!="" && id_project!=null && id_project!=0)
                {
                    $.post("index.php?ajax=1&act=all_fizz_win-project_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'action': 'archive','id_project': id_project},function(data)
                    {
                        displayProjects();
                    });
                }
            }
        }
        if(id=="checkStatus")
        {
            var id_project = eSP_projects_grid.getSelectedRowId();
            if(id_project!=undefined && id_project!="" && id_project!=null && id_project!=0)
            {
                $("#eSP_projects_checkstatus").show();
                $.post("index.php?ajax=1&act=all_fizz_win-project_checkstatus&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_project': id_project},function(data)
                {
                    $("#eSP_projects_checkstatus").hide();
                    if (data!=undefined && data!='' && data!=null && data!=0)
                    {
                        data = JSON.parse(data);
                        if(data.status!=undefined && data.status=="error")
                        {
                            var msg = data.message;
                            dhtmlx.message({text:msg,type:'error',expire:10000});
                        }
                        else if(data.status!=undefined && data.status=="success")
                        {
                            var msg = "<?php _l('Project finished and data imported successfully!'); ?>";
                            dhtmlx.message({text:msg,type:'success',expire:10000});
                        }
                        else if(data.status!=undefined && data.status=="info")
                        {
                            var msg = data.message;
                            dhtmlx.message({text:msg,expire:10000});
                        }
                    }
                    displayProjects();
                });
            }
        }
        if(id=="delete")
        {
            var id_project = eSP_projects_grid.getSelectedRowId();
            if(id_project!=undefined && id_project!="" && id_project!=null && id_project!=0)
            {
                if(confirm('<?php echo _l('Are you sure you want to delete this project?', 1); ?>'))
                {
                    idxStatus=eSP_projects_grid.getColIndexById('status');
                    var status = eSP_projects_grid.cells(id_project,idxStatus).getValue();
                    if(status=="0")
                    {
                        $.post("index.php?ajax=1&act=all_fizz_win-project_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'action': 'delete','id_project': id_project},function(data)
                        {
                            displayProjects();
                        });
                    }
                    else
                    {
                        var msg = '<?php echo _l('You can\'t delete project with this status', 1); ?>';
                        dhtmlx.message({text:msg,type:'error',expire:10000});
                    }
                }
            }
        }
    });
    eSP_projects_tb.attachEvent("onStateChange", function(id,state){
        if (id=='display_archived'){
            if (state) {
                selected_display_archived=1;
            }else{
                selected_display_archived=0;
            }
            displayProjects();
        }
        if (id=='group_by_type'){
            if (state) {
                selected_group_by_type=1;
                idxType=eSP_projects_grid.getColIndexById('type');
                eSP_projects_grid.groupBy(idxType);
            }else{
                selected_group_by_type=0;
                eSP_projects_grid.unGroup();
            }
        }
    });

    eSP_projects_grid.customGroupFormat=function(name,count){
        idxType=eSP_projects_grid.getColIndexById('type');
        var title = name;
        eSP_projects_grid.forEachRow(function(id){
            var val = eSP_projects_grid.cells(id,idxType).getValue();
            if(val==name)
            {
                title = eSP_projects_grid.cells(id,idxType).getTitle();
            }
        });

        if(count<=1)
            return title+" ("+count+" <?php echo _l('project'); ?>)";
        else
            return title+" ("+count+" <?php echo _l('projects'); ?>)";
    }

    function eSP_filterByType(type)
    {
        let filter_type = "";
        if(type!==undefined && type!==null && type!=="" && type!==0)
        {
            switch(type) {
                case "cutout":
                    filter_type = "<?php echo _l('Cut out'); ?>";
                    break;
                case "dixit":
                    filter_type = "<?php echo _l('Product translation Pro'); ?>";
                    break;
                case "image_compression":
                    filter_type = "<?php echo _l('Image compression'); ?>";
                    break;
            }
        }

        if(filter_type!=="")
        {
            eSP_projects_grid.getFilterElement(idxType).value = filter_type;
            eSP_projects_grid.filterByAll();
            switch(type) {
                case "image_compression":
                    let img_comp_id = eSP_projects_grid.getRowId(0);
                    eSP_projects_grid.selectRowById(img_comp_id,false,true,true);
                    break;
            }
        }
    }

    function displayProjects()
    {
        var id_selected = eSP_projects_grid.getSelectedRowId();

        col_eSP_projects.progressOn();
        eSP_projects_grid.clearAll(true);
        $.post("index.php?ajax=1&act=all_fizz_win-project_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'display_archived': selected_display_archived},function(data)
        {
            col_eSP_projects.progressOff();
            eSP_projects_grid.parse(data);

            // UISettings
            loadGridUISettings(eSP_projects_grid);
            eSP_projects_grid._first_loading=0;

            idxType=eSP_projects_grid.getColIndexById('type');

            if(selected_group_by_type=="1")
            {
                eSP_projects_grid.groupBy(idxType,["","#title","#cspan"]);
            }

            <?php
            $filter_type = Tools::getValue('filter_type');
            if (!empty($filter_type))
            {
                echo 'eSP_filterByType("'.$filter_type.'")';
            }
            ?>

            if(id_selected!=undefined && id_selected!=null && id_selected!="" && id_selected!=0)
            {
                eSP_projects_grid.selectRowById(id_selected,true,true,true);
                ESPloadConfig();
                displayESPElements();
            }

            idxStatus=eSP_projects_grid.getColIndexById('status');
            eSP_projects_grid.forEachRow(function(id){
                var status = eSP_projects_grid.cells(id,idxStatus).getValue();
                if(status=="8" || status=="9")
                {
                    if(eSP_started_processing!=undefined && eSP_started_processing==false)
                    {
                        eSP_started_id_project = id;
                        eSP_started_startCalls();
                    }
                }
            });
        });
    }
    displayProjects();

    var col_eSP_config = dhxleServicesProject_layout.cells('b');
    col_eSP_config.setText("<?php echo _l('Configuration'); ?>");
    col_eSP_config.attachURL('index.php?ajax=1&act=all_fizz_win-project_config');

    var col_eSP_itemslist = dhxleServicesProject_layout.cells('c');
    col_eSP_itemslist.setText("<?php echo _l('Items list'); ?>");
    col_eSP_itemslist.collapse();



    eSP_itemslist_tb=col_eSP_itemslist.attachToolbar();
    eSP_itemslist_tb.setIconset('awesome');

    var opts = [
        ['eservices_cutout', 'obj', '<?php echo _l('Cut the selected image out', 1); ?>', 'fad fa-game-board-alt fa-rotate-90'],
    ];
    eSP_itemslist_tb.addButtonSelect("eservices_list", 0, "",opts, "fa fa-gem red", "fa fa-gem red",false,true);
    eSP_itemslist_tb.setItemToolTip("eservices_list","<?php echo _l('e-Services', 1); ?>");
    eSP_itemslist_tb.addSeparator('sepExt', 0);
    eSP_itemslist_tb.addButton("exportcsv", 0, "", "fad fa-file-csv green", "fad fa-file-csv green");
    eSP_itemslist_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
    eSP_itemslist_tb.addButton("delete", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    eSP_itemslist_tb.setItemToolTip('delete','<?php echo _l('Remove selected items'); ?>');
    eSP_itemslist_tb.addButton("selectall", 0, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
    eSP_itemslist_tb.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
    eSP_itemslist_tb.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    eSP_itemslist_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
    eSP_itemslist_tb.attachEvent("onClick", function(id){
        if (id=='refresh')
        {
            displayESPElements();
        }
        if (id=='selectall')
        {
            eSP_itemslist_grid.selectAll();
            getESPGridStat();
        }
        if (id=='delete')
        {
            var selection=eSP_itemslist_grid.getSelectedRowId();
            if(selection!=undefined && selection!=null && selection!="")
            {


                    var id_project = eSP_projects_grid.getSelectedRowId();
                    $.get("index.php?ajax=1&act=all_fizz_win-project_element_update&action=delete&id_project="+id_project+"&ids="+selection+'&id_lang='+SC_ID_LANG,function(data){
                        if(data!=undefined && data!=null && data!="" && data!=0)
                        {
                            if(data=="error_wrongstatus")
                                dhtmlx.message({text:'<?php echo _l('You can\'t remove an element when the project has this status.', 1); ?>',type:'error',expire:15000});
                        }
                        else
                            displayESPElements();
                    });

            }
        }
        if (id=='exportcsv'){
            displayQuickExportWindow(eSP_itemslist_grid,1);
        }
        if(id=='eservices_cutout')
        {
            list=eSP_itemslist_grid.getSelectedRowId().split(',');
            if(list.length==1)
            {
                col_eSP_itemslist.progressOn();
                $.post("index.php?ajax=1&act=cat_image_cutout_upload&id_image="+list[0]+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){
                    if(data!=undefined && data!=null && data!="" && data!=0)
                    {
                        col_eSP_itemslist.progressOff();
                        if(data.type=="error")
                            dhtmlx.message({text:data.message,type:'error',expire:15000});
                        else
                        {
                            ClippingMagic.edit({
                                "image" : {
                                    "id" : data.id,
                                    "secret" : data.secret
                                },
                                "locale" : "<?php echo $user_lang_iso == 'fr' ? 'fr-FR' : 'en-US'; ?>"
                            }, callbackCutOutProject);
                        }
                    }
                    else
                        dhtmlx.message({text:'<?php echo _l('An error occured. Please contact our support.', 1); ?>',type:'error',expire:15000});
                },'JSON');
            }
            else if(list.length>1)
            {
                dhtmlx.message({text:'<?php echo _l('You must select only one image', 1); ?>',type:'error',expire:15000});
            }
            else if(list.length==0)
            {
                dhtmlx.message({text:'<?php echo _l('You must select one image', 1); ?>',type:'error',expire:15000});
            }
        }
    });

    eSP_itemslist_grid=col_eSP_itemslist.attachGrid();
    eSP_itemslist_grid._name='segmentation_element_grid';
    eSP_itemslist_grid.setImagePath("lib/js/imgs/");
    eSP_itemslist_grid.enableSmartRendering(true);
    eSP_itemslist_grid.enableDragAndDrop(false);
    eSP_itemslist_grid.enableMultiselect(true);

    eSP_itemslist_grid_sb=col_eSP_itemslist.attachStatusBar();


    function displayESPElements()
    {
        if(selected_project!=undefined && selected_project!=null && selected_project!="")
        {
            col_eSP_itemslist.progressOn();
            col_eSP_itemslist.expand();
            eSP_itemslist_grid.clearAll(true);
            eSP_itemslist_grid.load("index.php?ajax=1&act=all_fizz_win-project_element_get&id_project="+selected_project+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
            {
                col_eSP_itemslist.progressOff();
                eSP_itemslist_grid._rowsNum=eSP_itemslist_grid.getRowsNum();
                getESPGridStat();

            });
        }
    }
    function getESPGridStat(){
        let filteredRows=eSP_itemslist_grid.getRowsNum();
        let selectedRows=(eSP_itemslist_grid.getSelectedRowId()?eSP_itemslist_grid.getSelectedRowId().split(',').length:0);
        let nb_rows = (eSP_itemslist_grid._rowsNum!==undefined?eSP_itemslist_grid._rowsNum:0);
        eSP_itemslist_grid_sb.setText(nb_rows+' '+(nb_rows>1?'<?php echo _l('elements'); ?>':'<?php echo _l('element'); ?>')+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows);
    }
    eSP_itemslist_grid.attachEvent("onFilterEnd", function(elements){
        getESPGridStat();
    });
    eSP_itemslist_grid.attachEvent("onSelectStateChanged", function(id){
        getESPGridStat();
    });

    function hideExtIcons()
    {
        eSP_itemslist_tb.hideItem("sepExt");
        eSP_itemslist_tb.hideItem("eservices_list");
    }
    hideExtIcons();

    function callbackCutOutProject(opts)
    {
        if(opts!=undefined && opts!=null && opts!="")
        {
            if(opts.event=="result-generated")
            {
                col_eSP_itemslist.progressOn();
                $.post("index.php?ajax=1&act=cat_image_cutout_payment&id_image_cutout="+opts.image.id+"&id_image="+eSP_itemslist_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){
                    col_eSP_itemslist.progressOff();
                    displayESPElements();
                    if(data==undefined || (data!=undefined && data!="OK"))
                    {
                        dhtmlx.message({text:'<?php echo _l('An error occured. Please contact our support.', 1); ?>',type:'error',expire:15000});
                    }
                });
            }
        }
    }


    function setStatus(id_project, status)
    {
        if (id_project!=undefined && id_project!='' && id_project!=null && id_project!=0)
        {
            if (status!=undefined && status!='' && status!=null && status!=0)
            {
                col_eSP_config.progressOn();
                if(id_project!=eSP_projects_grid.getSelectedRowId())
                    eSP_projects_grid.selectRowById(id_project, true);
                $.post("index.php?ajax=1&act=all_fizz_win-project_update&action="+status+"&id_project="+id_project+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){
                    col_eSP_config.progressOff();
                    if (data!=undefined && data!='' && data!=null && data!=0)
                    {
                        if(data.status!=undefined && data.status=="success")
                        {
                            if(data.action!=undefined && data.action=="started")
                            {
                                if(eSP_started_processing!=undefined && eSP_started_processing==false)
                                {
                                    eSP_started_id_project = id_project;
                                    eSP_started_startCalls();
                                }
                            }
                        }
                        if(data.status!=undefined && data.status=="error")
                        {
                            var msg = data.message;
                            dhtmlx.message({text:msg,type:'error',expire:10000});
                        }
                        displayProjects();
                    }
                    else
                        displayProjects();
                },'json');
            }
        }
    }

    var eSP_started_interval = null;
    var eSP_started_id_project = null;
    var eSP_started_processing = false;
    var eSP_started_calling = false;
    var eSP_started_nbErrors = 0;

    function eSP_started_startCalls()
    {
        if(eSP_started_id_project!=undefined && eSP_started_id_project!="" && eSP_started_id_project!=null && eSP_started_id_project!=0)
        {
            if(eSP_started_processing!=undefined && eSP_started_processing==false)
            {
                eSP_started_processing = true;
                eSP_started_calling = false;
                eSP_started_nbErrors = 0;

                $('#eSP_projects_started_statusqueue').css('display','block');
                idxName=eSP_projects_grid.getColIndexById('name');
                var name = eSP_projects_grid.cells(eSP_started_id_project,idxName).getValue();
                $('#eSP_projects_started_statusqueue span').html(name+" #"+eSP_started_id_project);

                eSP_started_makeCall();
            }
        }
    }
    function eSP_started_makeCall()
    {
        if(eSP_started_id_project!=undefined && eSP_started_id_project!="" && eSP_started_id_project!=null && eSP_started_id_project!=0)
        {
            if(eSP_started_processing!=undefined && eSP_started_processing==true)
            {
                if(eSP_started_calling!=undefined && eSP_started_calling==false)
                {
                    eSP_started_calling = true;
                    $.post("index.php?ajax=1&act=all_fizz_win-project_started&id_project="+eSP_started_id_project+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){
                        var must_stop = false;

                        if (data!=undefined && data!='' && data!=null && data!=0)
                        {
                            try
                            {
                                data = JSON.parse(data);
                                if(data.status!=undefined && data.status=="success")
                                {

                                }
                                if(data.status!=undefined && data.status=="error")
                                {
                                    var msg = data.message;
                                    dhtmlx.message({text:msg,type:'error',expire:10000});
                                }

                                if(data.stop!=undefined && data.stop=="1")
                                    must_stop = true;
                            }
                            catch(e)
                            {
                                eSP_started_nbErrors = eSP_started_nbErrors*1 +1 ;
                            }
                        }
                        else
                            eSP_started_nbErrors = eSP_started_nbErrors*1 +1 ;

                        if(eSP_started_nbErrors>=3)
                            must_stop = true;

                        if(must_stop)
                        {
                            eSP_started_id_project = null;
                            eSP_started_processing = false;
                            eSP_started_calling = false;
                            eSP_started_nbErrors = 0;
                            $('#eSP_projects_started_statusqueue').css('display','none');
                            displayProjects();
                        }
                        else
                        {
                            eSP_started_calling = false;
                            eSP_started_makeCall();
                        }
                    });
                }
            }
        }
    }
    eSP_started_interval = setInterval(eSP_started_makeCall, 5000);
<?php echo '</script>'; ?>