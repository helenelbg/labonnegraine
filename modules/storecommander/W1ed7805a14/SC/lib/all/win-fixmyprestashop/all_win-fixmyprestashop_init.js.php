<?php echo '<script type="text/javascript">'; ?>
<?php
    ## defines js controls
    include dirname(__FILE__).'/all_win-fixmyprestashop_controls.php';
    foreach ($controls as &$control)
    {
        $control['tools'] = _l($control['tools']);
        $control['section'] = _l($control['section']);
        if (is_array($control['name']))
        {
            $control['name'] = substr(_l($control['name'][0], null, $control['name'][1]), 0, 250);
        }
        else
        {
            $control['name'] = substr(_l($control['name']), 0, 250);
        }
        if (is_array($control['description']))
        {
            $description = _l($control['description'][0], null, $control['description'][1]);
        }
        else
        {
            $description = _l($control['description']);
        }
    }
    $js_controls = json_encode($controls);
?>
    const js_controls = <?php echo $js_controls; ?>;

    wFixmyprestashop.setText("FixMyPrestashop");
    var only_active_lang = false;
    var actions_selected = '';

    dhxlSCExtCheck=wFixmyprestashop.attachLayout("3W");

    // Colonne des solutions externes
    dhxlSCExtCheck.colSolutions = dhxlSCExtCheck.cells('a');
    dhxlSCExtCheck.colSolutions.setText("<?php echo _l('Check the prerequisites for:'); ?>");
    dhxlSCExtCheck.colSolutions.setWidth(300);
    dhxlSCExtCheck.tbSolutions=dhxlSCExtCheck.colSolutions.attachToolbar();
    dhxlSCExtCheck.tbSolutions.setIconset('awesome');
    dhxlSCExtCheck.tbSolutions.addButton("refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
    dhxlSCExtCheck.tbSolutions.setItemToolTip('refresh','<?php echo _l('Refresh solution list and reinitialize checks', 1); ?>');
    dhxlSCExtCheck.tbSolutions.addButton("go_link", 100, "", "fad fa-globe-europe green", "fad fa-globe-europe green");
    dhxlSCExtCheck.tbSolutions.setItemToolTip('go_link','<?php echo _l('Go to solution website'); ?>');
    dhxlSCExtCheck.tbSolutions.addInput("filter_name", 100,"",100);
    dhxlSCExtCheck.tbSolutions.setItemToolTip('filter_name','<?php echo _l('Filter by name'); ?>');
    dhxlSCExtCheck.tbSolutions.addText('txt_filter_name', 100, '<?php echo _l('Filter by name'); ?>');
    dhxlSCExtCheck.tbSolutions.attachEvent("onClick",function(id) {
        switch(id){
            case 'refresh':
                actions_selected='';
                displaySolutionList();
                dhxlSCExtCheck.colCheck.setText("<?php echo _l('All checks'); ?>");
                displayChecks();
                break;
            case 'go_link':
                let current_solution_id = dhxlSCExtCheck.colSolutions.list.getSelected();
                let current_solution = dhxlSCExtCheck.colSolutions.list.get(current_solution_id);
                if(current_solution.external_link !== undefined && current_solution.external_link !== '') {
                    window.open(current_solution.external_link,"_blank");
                }
                break;
        }
    });

    // initialisation de la liste
    dhxlSCExtCheck.colSolutions.list = dhxlSCExtCheck.colSolutions.attachList({
        drag:false,
        select:true,
        template:'<div>#logo#</div><div><b>#brand#</b></div>',
        css:'fixmyps',
        height:80
    });

    displaySolutionList();

    dhxlSCExtCheck.colSolutions.list.attachEvent("onSelectChange", function (sel_arr){
        let selected_solution = dhxlSCExtCheck.colSolutions.list.get(sel_arr[0]);
        dhxlSCExtCheck.colCheck.setText("<?php echo _l('All checks for:'); ?> "+selected_solution.brand);
        if(selected_solution.actions.length > 0) {
            actions_selected = selected_solution.actions.join();
        } else {
            actions_selected = '';
        }
        displayChecks();
    });

    // Colonne des checks
    dhxlSCExtCheck.colCheck = dhxlSCExtCheck.cells('b');
    dhxlSCExtCheck.colCheck.setText("<?php echo _l('All checks'); ?>");

    dhxlSCExtCheck.tbChecks=dhxlSCExtCheck.colCheck.attachToolbar();
    dhxlSCExtCheck.tbChecks.setIconset('awesome');
    dhxlSCExtCheck.tbChecks.addButton("help", 0, "", "fad fa-question-circle blue", "fad fa-question-circle blue");
    dhxlSCExtCheck.tbChecks.setItemToolTip('help','<?php echo _l('Help'); ?>');
    dhxlSCExtCheck.tbChecks.addButton("print", 0, "", "fad fa-print", "fad fa-print");
    dhxlSCExtCheck.tbChecks.setItemToolTip('print','<?php echo _l('Print grid', 1); ?>');
    dhxlSCExtCheck.tbChecks.addButton("selectall", 0, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
    dhxlSCExtCheck.tbChecks.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
    dhxlSCExtCheck.tbChecks.addButtonTwoState("active_lang", 0, "", "fad fa-flag blue", "fad fa-flag blue");
    dhxlSCExtCheck.tbChecks.setItemToolTip('active_lang','<?php echo _l('Only active langs'); ?>');
    dhxlSCExtCheck.tbChecks.addButtonTwoState("check", 0, "", "fad fa-play-circle blue", "fad fa-play-circle blue");
    dhxlSCExtCheck.tbChecks.setItemToolTip('check','<?php echo _l('Start scanning'); ?>');
    dhxlSCExtCheck.tbChecks.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    dhxlSCExtCheck.tbChecks.setItemToolTip('refresh','<?php echo _l('Refresh grid'); ?>');
    dhxlSCExtCheck.tbChecks.attachEvent("onClick",
        function(id){
            if (id=='refresh')
            {
                displayChecks();
            }
            if (id=='selectall')
            {
                dhxlSCExtCheck.gridChecks.selectAll();
                getFixGridStat();
            }
            if (id=='help')
            {
                window.open('<?php echo getScExternalLink('support_fixmyprestashop_how_to'); ?>');
            }
            if (id=='print'){
                dhxlSCExtCheck.gridChecks.printView();
            }
        });
    dhxlSCExtCheck.tbChecks.attachEvent("onStateChange", function(id, state){
        if (id=='check')
        {
            if (state){
                resetFixResults();
                if (dhxlSCExtCheck.gridChecks.getSelectedRowId()==null) {
                    dhxlSCExtCheck.gridChecks.selectAll();
                }
                $.post('index.php?ajax=1&act=all_win-fixmyprestashop_update',{'action':'updateLastCheckDate'});
                waitingChecks();
            }else{
                waiting_start = false;
            }
        }
        if (id=='active_lang')
        {
            if (state){
                only_active_lang = true;
            }else{
                only_active_lang = false;
            }
        }
    });



    dhxlSCExtCheck.gridChecks=dhxlSCExtCheck.colCheck.attachGrid();
    dhxlSCExtCheck.gridChecks.setImagePath("lib/js/imgs/");
    dhxlSCExtCheck.gridChecks.enableSmartRendering(true);
    dhxlSCExtCheck.gridChecks.enableMultiselect(true);

    dhxlSCExtCheck.gridChecks_sb=dhxlSCExtCheck.colCheck.attachStatusBar();

    dhxlSCExtCheck.gridChecks.attachEvent("onRowSelect", function(id,ind){
        idxResults=dhxlSCExtCheck.gridChecks.getColIndexById('results');
        if(dhxlSCExtCheck.gridChecks.cells(id,idxResults).getValue()=="<?php echo _l('Error'); ?>")
            dhxlSCExtCheck.tabbar.tabs("table_"+id).setActive();
    });

    displayChecks();

    // Colonne des erreurs
    dhxlSCExtCheck.colError = dhxlSCExtCheck.cells('c');
    dhxlSCExtCheck.colError.setText("<?php echo _l('Errors'); ?>");


    dhxlSCExtCheck.tabbar = dhxlSCExtCheck.colError.attachTabbar();
    dhxlSCExtCheck.colError.collapse();


    //#####################################
    //############ Load functions
    //#####################################

    function displaySolutionList()
    {
        dhxlSCExtCheck.colSolutions.list.clearAll();
        $.post("index.php?ajax=1&act=all_win-fixmyprestashop_solutions",{action: "get_list"},function(data){
            filterScriptName();
            data = JSON.parse(data);
            dhxlSCExtCheck.colSolutions.list.parse(data, 'json');
        });
    }

    function displayChecks()
    {
        oldFilters=new Array();
        for(var i=0,l=dhxlSCExtCheck.gridChecks.getColumnsNum();i<l;i++)
        {
            if (dhxlSCExtCheck.gridChecks.getFilterElement(i)!=null && dhxlSCExtCheck.gridChecks.getFilterElement(i).value!='')
                oldFilters[dhxlSCExtCheck.gridChecks.getColumnId(i)]=dhxlSCExtCheck.gridChecks.getFilterElement(i).value;
        }
        dhxlSCExtCheck.gridChecks.clearAll(true);
        dhxlSCExtCheck.gridChecks.post("index.php?ajax=1&act=all_win-fixmyprestashop_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),"actions_selected="+actions_selected,function()
        {
            nb=dhxlSCExtCheck.gridChecks.getRowsNum();
            dhxlSCExtCheck.gridChecks._rowsNum = nb;
            for(var i=0;i<dhxlSCExtCheck.gridChecks.getColumnsNum();i++)
            {
                if (dhxlSCExtCheck.gridChecks.getFilterElement(i)!=null && oldFilters[dhxlSCExtCheck.gridChecks.getColumnId(i)]!=undefined)
                {
                    dhxlSCExtCheck.gridChecks.getFilterElement(i).value=oldFilters[dhxlSCExtCheck.gridChecks.getColumnId(i)];
                }
            }
            dhxlSCExtCheck.gridChecks.filterByAll();

            getFixGridStat();
        });
    }


    //#####################################
    //############ Check functions
    //#####################################
    var interval = "";
    var firstTab = "";
    var waiting_start = false;

    // Prepare les checks
    // Met les lignes en waiting
    // Lance le premier check
    // Lance le setinterval
    function waitingChecks()
    {
        dhxlSCExtCheck.tabbar.clearAll();
        firstTab = "";
        clearInterval(interval);
        interval = "";
        // Récupération des checks visibles
        var selectedChecks = dhxlSCExtCheck.gridChecks.getSelectedRowId();
        if(selectedChecks==null || selectedChecks=="")
            selectedChecks = 0;
        if(selectedChecks!="0")
        {
            idxResults=dhxlSCExtCheck.gridChecks.getColIndexById('results');
            idxIgnore=dhxlSCExtCheck.gridChecks.getColIndexById('ignore');
            var ids = selectedChecks.split(",");
            $.each( ids, function( num, id ) {
                if(!dhxlSCExtCheck.gridChecks.cells(id,idxIgnore).isChecked())
                {
                    dhxlSCExtCheck.gridChecks.cells(id,idxResults).setValue("<?php echo _l('Waiting'); ?>");
                    dhxlSCExtCheck.gridChecks.cells(id,idxResults).setBgColor('91c2ff');
                }
            });

            dhxlSCExtCheck.gridChecks.clearSelection();
        }

        waiting_start = true;
        doOneWaitingCheck();
    }

    var custom_param = "";

    // Execute le premier check en waiting
    // Si pas de check en waiting, execute checkResults
    function doOneWaitingCheck()
    {
        idxResults=dhxlSCExtCheck.gridChecks.getColIndexById('results');
        idxName=dhxlSCExtCheck.gridChecks.getColIndexById('name');
        if(waiting_start)
        {
            var firstWaiting = dhxlSCExtCheck.gridChecks.findCell("<?php echo _l('Waiting'); ?>",idxResults,true);
            if(firstWaiting[0]!=undefined && firstWaiting[0][0]!="")
            {
                var id = firstWaiting[0][0];

                //Réalisation du check en appelant le fichier dans Actions
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check="+id+"&only_active_lang="+only_active_lang+"&id_lang="+SC_ID_LANG, { "action": "do_check", "custom_param": custom_param },
                    function(data){
                        // Récupération du résultat
                        // Si OK
                        if(data.results=="OK")
                        {
                            // Ajoute OK et met case en vert
                            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setValue("OK");
                            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setBgColor('green');
                        }
                        // Si KO, récupération du ContentType et du contenu de l'onglet
                        else if(data.results=="KO")
                        {
                            // Ajoute Error et met case en rouge
                            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setValue("<?php echo _l('Error'); ?>");
                            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setBgColor('red');
                            if(data.content!="" || data.contentJs!="")
                            {
                                // Ajoute l'onglet à tabbar
                                if(firstTab=="")
                                    firstTab = "table_"+id;
                                dhxlSCExtCheck.tabbar.addTab("table_"+id,data.title,"100px");
                                // ajout du contenu
                                if(data.contentType=="grid")
                                {
                                    if (data.contentJs !== "") {
                                        setTimeout(function () {
                                            $('#jsExecute').html(data.contentJs);
                                            let show_title = true;
                                            let current_tabbar = dhxlSCExtCheck.tabbar.cells("table_" + id);
                                            let current_tabbar_object = current_tabbar.getAttachedObject();
                                            let current_tabbar_grid = null;
                                            let current_tabbar_toolbar = current_tabbar.getAttachedToolbar();
                                            switch(true){
                                                case (typeof (window.dhtmlXGridObject) == "function" && current_tabbar_object instanceof dhtmlXGridObject):
                                                    current_tabbar_grid = current_tabbar_object;
                                                    break;
                                                case (current_tabbar_object instanceof dhtmlXLayoutObject):
                                                    current_tabbar_toolbar = current_tabbar_object.cells('a').getAttachedToolbar();
                                                    let tmp_object = null;
                                                    if(current_tabbar_toolbar === undefined)
                                                    {
                                                        current_tabbar_toolbar = current_tabbar_object.cells('b').getAttachedToolbar();
                                                        tmp_object = current_tabbar_object.cells('b').getAttachedObject();
                                                    } else {
                                                        tmp_object = current_tabbar_object.cells('a').getAttachedObject();
                                                    }
                                                    if ((typeof (window.dhtmlXGridObject) == "function" && tmp_object instanceof dhtmlXGridObject)) {
                                                        current_tabbar_grid = tmp_object;
                                                        show_title = false;
                                                    }
                                                    break;
                                            }
                                            if (current_tabbar_grid && current_tabbar_toolbar !== null && current_tabbar_toolbar !== undefined) {
                                                let current_js_control = js_controls[id];
                                                if (current_js_control.segment_params !== undefined) {
                                                    <?php if (_r('MEN_MAR_SEGMENTATION')) { ?>
                                                    current_tabbar_toolbar.addButton("export_to_segment", 100, "", 'fad fa-chart-pie blue', 'fad fa-chart-pie blue');
                                                    current_tabbar_toolbar.setItemToolTip('export_to_segment', '<?php echo _l('Add the result lines to a new manual segment'); ?>');
                                                    <?php } ?>
                                                    if(show_title) {
                                                        current_tabbar_toolbar.addText("title", 999, dhxlSCExtCheck.gridChecks.cells(id, idxName).getValue());
                                                    }
                                                    current_tabbar_toolbar.attachEvent("onClick", function (id) {
                                                        switch (id) {
                                                            case 'export_to_segment':
                                                                <?php if (SCSG){ ?>
                                                                exportIdToSegment(current_tabbar_grid, current_js_control);
                                                                <?php }
                                                                else
                                                                { ?>
                                                                if (!dhxWins.isWindow('wSegTrialWindow')) {
                                                                    wSegTrialWindow = dhxWins.createWindow('wSegTrialWindow', 50, 50, 670, 550);
                                                                    wSegTrialWindow.setText('<?php echo _l('Segments management', 1); ?>');
                                                                }
                                                                wSegTrialWindow.attachURL('index.php?ajax=1&act=all_gettrialtime&id_lang='+SC_ID_LANG+'&item=segmentation');
                                                                <?php } ?>
                                                                break
                                                        }
                                                    });
                                                } else {
                                                    current_tabbar_toolbar.addText("title", 999, dhxlSCExtCheck.gridChecks.cells(id, idxName).getValue());
                                                }
                                            }
                                            if (current_tabbar_grid != null) {
                                                // Context menu for all Grids
                                                cmenu=new dhtmlXMenuObject();
                                                cmenu.renderAsContextMenu();
                                                function onGridResultFixMyPrestashopContextButtonClick(itemId){
                                                    if (itemId=="copyToClipBoard"){
                                                        if (lastColumnRightClicked!=undefined)
                                                        {
                                                            copyToClipBoard(current_tabbar_grid.cells(lastRowIDRightClicked,lastColumnRightClicked).getTitle());
                                                        }
                                                    }
                                                }
                                                cmenu.attachEvent("onClick", onGridResultFixMyPrestashopContextButtonClick);
                                                var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="1"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
                                                    '<item text="<?php echo _l('Copy to ClipBoard'); ?>" id="copyToClipBoard"/>'+
                                                    '</menu>';
                                                cmenu.loadStruct(contextMenuXML);
                                                current_tabbar_grid.enableContextMenu(cmenu);

                                                current_tabbar_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx){
                                                    lastColumnRightClicked=colidx;
                                                    lastRowIDRightClicked=rowid;
                                                    return true;
                                                });
                                            }
                                        }, 500);
                                    }
                                }
                                else
                                {
                                    if(data.content!="")
                                        dhxlSCExtCheck.tabbar.cells("table_"+id).attachHTMLString(data.content);
                                }

                            }
                        }
                        else if(data.results=="NeedActionJs")
                        {
                            if(data.contentJs!="")
                            {
                                $('#jsExecute').html(data.contentJs);
                            }
                        }

                        if(firstTab!="")
                        {
                            dhxlSCExtCheck.colError.expand();
                            dhxlSCExtCheck.tabbar.tabs(firstTab).setActive();
                        }
                        getFixGridStat();
                        doOneWaitingCheck();
                    }, "json");
            }
            else
            {
                clearInterval(interval);
                interval = "";
                custom_param = "";
                dhxlSCExtCheck.tbChecks.setItemState("check", false);
                waiting_start = false;
                var t=setTimeout(function(){checkResults()},5000);
            }
        }
        dhxlSCExtCheck.gridChecks.refreshFilters();
    }

    // Réalise tous les checks sélectionnés
    function doCheck(clearAll)
    {
        idxResults=dhxlSCExtCheck.gridChecks.getColIndexById('results');
        idxName=dhxlSCExtCheck.gridChecks.getColIndexById('name');
        // Vide tabbar
        if(clearAll==true)
        {
            dhxlSCExtCheck.tabbar.clearAll();
            firstTab = "";
        }

        // Récupération des checks visibles
        var selectedChecks = dhxlSCExtCheck.gridChecks.getSelectedRowId();
        if(selectedChecks==null || selectedChecks=="")
            selectedChecks = 0;
        if(selectedChecks!="0")
        {
            var ids = selectedChecks.split(",");
            $.each( ids, function( num, id ) {

                //Réalisation du check en appelant le fichier dans Actions
                $.post("index.php?ajax=1&act=all_win-fixmyprestashop_actions&check="+id+"&only_active_lang="+only_active_lang+"&id_lang="+SC_ID_LANG, { "action": "do_check" },
                    function(data){
                        // Récupération du résultat
                        // Si OK
                        if(data.results=="OK")
                        {
                            // Ajoute OK et met case en vert
                            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setValue("OK");
                            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setBgColor('green');
                        }
                        // Si KO, récupération du ContentType et du contenu de l'onglet
                        else if(data.results=="KO")
                        {
                            // Ajoute Error et met case en rouge
                            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setValue("<?php echo _l('Error'); ?>");
                            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setBgColor('red');
                            if(data.content!="" || data.contentJs!="")
                            {
                                // Ajoute l'onglet à tabbar
                                if(firstTab=="")
                                    firstTab = "table_"+id;
                                dhxlSCExtCheck.tabbar.addTab("table_"+id,data.title,"100px");
                                // ajout du contenu
                                if(data.contentType=="grid")
                                {
                                    if(data.contentJs!="")
                                    {
                                        $('#jsExecute').html(data.contentJs);
                                        var tmp = dhxlSCExtCheck.tabbar.cells("table_"+id).getAttachedToolbar();



                                        if(tmp!=undefined)
                                            tmp.addText("title", 999, dhxlSCExtCheck.gridChecks.cells(id,idxName).getValue());
                                    }
                                }
                                else
                                {
                                    if(data.content!="")
                                        dhxlSCExtCheck.tabbar.cells("table_"+id).attachHTMLString(data.content);
                                }

                            }
                        }

                        if(firstTab!="")
                        {
                            dhxlSCExtCheck.colError.expand();
                            dhxlSCExtCheck.tabbar.tabs(firstTab).setActive();
                        }
                    }, "json");
            });
        }

        var t=setTimeout(function(){checkResults()},1000);
    }

    // Vérification s'il y a des erreurs
    // Si pas d'erreur, ferme la grid de droite
    function checkResults()
    {
        idxResults=dhxlSCExtCheck.gridChecks.getColIndexById('results');
        dhxlSCExtCheck.gridChecks.selectAll();
        var selectedChecks = dhxlSCExtCheck.gridChecks.getSelectedRowId();
        if(selectedChecks==null || selectedChecks=="")
            selectedChecks = 0;
        if(selectedChecks!="0")
        {
            var ids = selectedChecks.split(",");
            var has_error = false;
            $.each( ids, function( num, id ) {
                if(dhxlSCExtCheck.gridChecks.cells(id,idxResults).getValue()=="<?php echo _l('Error'); ?>")
                    has_error = true;
            });
            if(has_error==false)
                dhxlSCExtCheck.colError.collapse();
        }
        dhxlSCExtCheck.gridChecks.clearSelection();
    }

    function getFixGridStat(){
        var filteredRows=dhxlSCExtCheck.gridChecks.getRowsNum();
        var selectedRows=(dhxlSCExtCheck.gridChecks.getSelectedRowId()?dhxlSCExtCheck.gridChecks.getSelectedRowId().split(',').length:0);
        idxResults=dhxlSCExtCheck.gridChecks.getColIndexById('results');

        var ok_nb = 0;
        var error_nb = 0;
        dhxlSCExtCheck.gridChecks.forEachRow(function(id){
            var val = dhxlSCExtCheck.gridChecks.cells(id,idxResults).getValue();
            if(val=="<?php echo _l('Error'); ?>")
                error_nb = error_nb*1 + 1;
            else if(val=="OK")
                ok_nb = ok_nb*1 + 1;
        });

        dhxlSCExtCheck.gridChecks_sb.setText(dhxlSCExtCheck.gridChecks._rowsNum+' '+(dhxlSCExtCheck.gridChecks._rowsNum>1?'<?php echo _l('controls'); ?>':'<?php echo _l('control'); ?>')+" - <?php echo _l('Filter')._l(':'); ?> "+filteredRows+" - <?php echo _l('Selection')._l(':'); ?> "+selectedRows+" - <?php echo _l('OK')._l(':'); ?> "+ok_nb+" - <?php echo _l('Error')._l(':'); ?> "+error_nb);
    }

    dhxlSCExtCheck.gridChecks.attachEvent("onFilterEnd", function(elements){
        getFixGridStat();
    });
    dhxlSCExtCheck.gridChecks.attachEvent("onSelectStateChanged", function(id){
        getFixGridStat();
    });

    function resetFixResults()
    {
        idxResults=dhxlSCExtCheck.gridChecks.getColIndexById('results');
        dhxlSCExtCheck.gridChecks.forEachRow(function(id){
            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setValue("");
            dhxlSCExtCheck.gridChecks.cells(id,idxResults).setBgColor('');
        });
    }

    function filterScriptName()
    {
        let inputFilterName=dhxlSCExtCheck.tbSolutions.getInput('filter_name');
        inputFilterName.onkeyup=function(){
            let search = $(this).val();
            dhxlSCExtCheck.colSolutions.list.filter("#brand#",search);
        };
    }

    function exportIdToSegment(tabgrid, control)
    {
        let segment_name = prompt('<?php echo _l('Name of your new segment', 1); ?>', control.name + ' <?php echo date('Ymd'); ?>');
        if (segment_name !== false && segment_name !== null && segment_name !== '' && segment_name !== undefined) {
            let row_array = tabgrid.getAllRowIds().split(',');
            let final_id_list = [];
            for (const row of row_array) {
                let content_row = row;
                if(control.segment_params.value_separator !== undefined){
                    content_row = row.split(control.segment_params.value_separator);
                    let index_needed = Number(control.segment_params.index_of_value_to_get);
                    content_row = content_row[index_needed];
                }
                final_id_list.push(content_row);
            }
            let id_list = [...new Set(final_id_list)];
            if (id_list.length > 0) {
                $.post('index.php?ajax=1&act=all_win-fixmyprestashop_update', {
                    'action': 'export_fix_to_segment',
                    'segment_name': segment_name,
                    'segment_item_type': control.segment_params.element_type,
                    'segment_access': control.segment_params.access,
                    'segment_item_list': id_list.join(',')
                }, function (res) {
                    let response = JSON.parse(res);
                    dhtmlx.message({text: response.message, type: response.state, expire: 5000});
                });
            } else {
                dhtmlx.message({text: "<?php echo _l('No data to save'); ?>", type: 'error', expire: 5000});
            }
        }
    }

    function openTerminatorAndFilter(action_id = '') {
        if (!dhxWins.isWindow('toolsTerminator')) {
            toolsTerminator = dhxWins.createWindow('toolsTerminator', 50, 50, $(window).width() - 100, $(window).height() - 100);
            toolsTerminator.setText('<?php echo _l('Shop cleaning and optimization', 1); ?>');
            toolsTerminator.attachEvent('onClose', function (win) {
                toolsTerminator.hide();
                return false;
            });
            $.get('index.php?ajax=1&act=all_win-terminator_init&action_id=' + action_id, function (data) {
                $('#jsExecute').html(data);
            });
        } else {
            $.get('index.php?ajax=1&act=all_win-terminator_init&action_id=' + action_id, function (data) {
                $('#jsExecute').html(data);
            });
            toolsTerminator.show();
        }
        toolsTerminator.bringToTop();
    }
<?php echo '</script>'; ?>