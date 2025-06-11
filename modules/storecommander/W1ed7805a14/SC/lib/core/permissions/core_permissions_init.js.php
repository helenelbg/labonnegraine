<?php echo '<script type="text/javascript">'; ?>
    var lPermissions = new dhtmlXLayoutObject(wCorePermissions, "2U");
    var col_profils = lPermissions.cells('a');
    var col_permissions = lPermissions.cells('b');

    // TREE DES PROFILS
        col_profils.setText('<?php echo _l('Profiles', 1); ?>');
        col_profils.setWidth(200);

        profils_tree=col_profils.attachTree();
        profils_tree._name='tree';
        profils_tree.autoScroll=false;
        profils_tree.setImagePath('lib/js/imgs/dhxtree_material/');
        profils_tree.enableSmartXMLParsing(true);
        profils_tree.enableDragAndDrop(true);
        profils_tree.setDragBehavior("simple");
        profils_tree._dragBehavior="simple";
        profils_tree.enableDragAndDropScrolling(true);

        profils_tb=col_profils.attachToolbar();
        profils_tb.setIconset('awesome');

        profils_tb.addButton("refresh", 1000, "", "fa fa-sync green", "fa fa-sync green");
        profils_tb.setItemToolTip('refresh','<?php echo _l('Refresh', 1); ?>');
        profils_tb.addButton("open_ps", 1000, "", "fa fa-prestashop", "fa fa-prestashop");
        profils_tb.setItemToolTip('open_ps','<?php echo _l('See in Prestashop', 1); ?>');
        profils_tb.addButton("delete", 1000, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
        profils_tb.setItemToolTip('delete','<?php echo _l('Reset permissions', 1); ?>');
        profils_tb.addButton("reset_pwd", 1000, "", "fa fa-user-lock red", "fa fa-minus-circle red");
        profils_tb.setItemToolTip('reset_pwd','<?php echo _l('Reset the password of the selection', 1); ?>');
        profils_tb.attachEvent("onClick", function(id){
            let selectionProfilId = profils_tree.getSelectedItemId();
            switch(id)
            {
                case 'refresh':
                    displayProfils();
                    break;
                case 'delete':
                    if(confirm('<?php echo _l('Are you sure that you want reset this permissions?', 1); ?>'))
                    {
                        $.post("index.php?ajax=1&act=core_permissions_delete&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id':selectionProfilId},function(data){
                            displayProfils("callbackProfils('"+selectionProfilId+"')");
                        });
                    }
                    break;
                case 'open_ps':
                    if (!dhxWins.isWindow("wSeePSProfile"))
                    {
                        wSeePSProfile = dhxWins.createWindow("wSeePSProfile", 50, 50, 1000, $(window).height()-75);
                        wSeePSProfile.setText('<?php echo _l('See the profile in Prestashop', 1); ?>');

                        let temp = selectionProfilId.split('_');
                        let url = "<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminAccess&token=<?php echo $sc_agent->getPSToken('AdminAccess'); ?>";
                        if(temp[0]==='em') {
                            url = "<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?tab=AdminEmployees&id_employee=" + temp[1] + "&updateemployee&token=<?php echo $sc_agent->getPSToken('AdminEmployees'); ?>";
                        }
                        wSeePSProfile.attachURL(url);
                        pushOneUsage('permissions-bo-link-adminemployees_updateemployee','core');
                        wSeePSProfile.attachEvent("onClose", function(win){
                            displayProfils("callbackProfils('"+selectionProfilId+"')");
                            return true;
                        });
                    }
                    break;
                case 'reset_pwd':
                    if(selectionProfilId === '')
                    {
                        return;
                    }

                    let tempId = selectionProfilId.split('_');
                    let confirmResetPwd = false;
                    switch(tempId[0])
                    {
                        case 'pr':
                            confirmResetPwd = confirm(`<?php echo _l('Do you really want to reset the password of all employees from')."\n"._l('Profil')._l(':'); ?> `+profils_tree.getItemText(selectionProfilId));
                            break;
                        case 'em':
                            confirmResetPwd = confirm(`<?php echo _l('Do you really want to reset the password for')."\n"._l('Employee')._l(':'); ?> `+profils_tree.getItemText(selectionProfilId))
                            break;
                    }
                    if(confirmResetPwd)
                    {
                        let resetConfirmationText = prompt(`<?php echo _l('Please, write "DELETE" to confirm reset'); ?>`, "");
                        if('DELETE' === resetConfirmationText)
                        {
                            $.post("index.php?ajax=1&act=core_permissions_update",{
                                action:'reset_pwd',
                                selection:selectionProfilId
                            },function(response){
                                if(response === 'OK')
                                {
                                    dhtmlx.message({
                                        text:'<?php echo _l('Password reseted'); ?>',
                                        type:'success',
                                        expire: 5000
                                    });
                                }
                                else if (response.length > 0)
                                {
                                    dhtmlx.message({
                                        text:response,
                                        type:'error',
                                        expire: 10000
                                    });
                                } else {
                                    dhtmlx.message({
                                        text:`<?php echo _l('an error occurred. Please try again.'); ?>`,
                                        type:'error',
                                        expire: 10000
                                    });
                                }
                            });
                        }
                    }
                    break;
            }
        });

        profils_tree.attachEvent("onClick",function(id){
            displayPermissions(id);
        });

        profils_tree.attachEvent("onDrag", function(sId,tId,id,sObject,tObject){
            if(sId!=tId)
            {
                var temp = sId.split('_');
                if(confirm('<?php echo _l('Do you want to duplicate these permissions?', 1); ?>'))
                {
                    $.post("index.php?ajax=1&act=core_permissions_duplicate&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_source':sId,'id_target':tId},function(data){
                        setTimeout("displayProfils('callbackProfils(\""+tId+"\")')",500); // avoid dhtmlx problem
                    });
                }
            }
            return false;
        });

    // GRID DES PERMISSIONS
        col_permissions.setText('<?php echo _l('Permissions', 1); ?>');
        permissions_grid=col_permissions.attachGrid();
        permissions_grid.setImagePath('lib/js/imgs/');
        permissions_grid.setHeader("<?php echo _l('Tool'); ?>,<?php echo _l('Section'); ?>,<?php echo _l('Name'); ?>,<?php echo _l('Access'); ?>,<?php echo _l('Description'); ?>,<?php echo _l('Profile access'); ?>,<?php echo _l('Different from profile:'); ?>");
        permissions_grid.setColumnIds("section1,section2,id,value,description,profil_value,profil_diff");
        permissions_grid.setInitWidths("75,75,200,60,*,60,60");
        permissions_grid.setColAlign("left,left,left,left,left,left,left");
        permissions_grid.setColTypes("ro,ro,ro,coro,ro,ro,ro");
          permissions_grid.enableMultiline(true);
          permissions_grid.enableMultiselect(true);
        permissions_grid.setColSorting("str,str,str,str,str,str,str");
        permissions_grid.attachHeader("#select_filter,#select_filter,#text_filter,#select_filter,#text_filter,#select_filter,#select_filter");
        permissions_grid.init();
        permissions_grid.enableHeaderMenu();

        permissions_tb=col_permissions.attachToolbar();
        permissions_tb.setIconset('awesome');
        permissions_tb.addButton("selectall", 0, "", "fa fa-bolt yellow", "fa fa-bolt yellow");
        permissions_tb.setItemToolTip('selectall','<?php echo _l('Select all', 1); ?>');
        permissions_tb.addButton("delete_mass", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
        permissions_tb.setItemToolTip('delete_mass','<?php echo _l('Delete access', 1); ?>');
        permissions_tb.addButton("add_mass", 0, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
        permissions_tb.setItemToolTip('add_mass','<?php echo _l('Give access', 1); ?>');
        permissions_tb.attachEvent("onClick", function(id){
            if(id=="add_mass")
            {
                var id = profils_tree.getSelectedItemId();
                var permissions = permissions_grid.getSelectedRowId();
                $.post("index.php?ajax=1&act=core_permissions_update&action=add_mass&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'profil':id,'permissions':permissions},function(data){
                    displayProfils("callbackProfils('"+id+"')");
                });
            }
            if(id=="delete_mass")
            {
                var id = profils_tree.getSelectedItemId();
                var permissions = permissions_grid.getSelectedRowId();
                $.post("index.php?ajax=1&act=core_permissions_update&action=delete_mass&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'profil':id,'permissions':permissions},function(data){
                    displayProfils("callbackProfils('"+id+"')");
                });
            }
            if (id=='selectall')
            {
                permissions_grid.selectAll();
            }
        });

        idxValue=permissions_grid.getColIndexById('value');
        var combo = permissions_grid.getCombo(idxValue);
        combo.put("1",'<?php echo _l('Yes', 1); ?>');
        combo.put("0",'<?php echo _l('No', 1); ?>');
        combo.save();

        idxProfilValue=permissions_grid.getColIndexById('profil_value');
        idxProfilDiff=permissions_grid.getColIndexById('profil_diff');
        var combo_profil = permissions_grid.getCombo(idxProfilValue);
        combo_profil.put("1",'<?php echo _l('Yes', 1); ?>');
        combo_profil.put("0",'<?php echo _l('No', 1); ?>');
        combo_profil.save();
        permissions_grid.setColumnHidden(idxProfilValue,true);
        permissions_grid.setColumnHidden(idxProfilDiff,true);

        permissionsDataProcessorURLBase="index.php?ajax=1&act=core_permissions_update";
        permissionsDataProcessor = new dataProcessor(permissionsDataProcessorURLBase);
        permissionsDataProcessor.enableDataNames(true);
        permissionsDataProcessor.enablePartialDataSend(true);
        permissionsDataProcessor.setUpdateMode('cell');
        permissionsDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
            var id = profils_tree.getSelectedItemId();
            displayProfils('callbackProfils("'+id+'")');
        });

        permissionsDataProcessor.init(permissions_grid);


    // INIT
            displayProfils();

    // FUNCTIONS
        function displayPermissions(id,callback)
        {
            permissions_grid.clearAll();

            var patt=/pr_/g;
            if(patt.test(id))
            {
                permissions_grid.setColumnHidden(idxProfilValue,true);
                permissions_grid.setColumnHidden(idxProfilDiff,true);
                permissions_grid.setColLabel(idxValue,"<?php echo _l('Profile access', 1); ?>");
            }
            else
            {
                permissions_grid.setColumnHidden(idxProfilValue,false);
                permissions_grid.setColumnHidden(idxProfilDiff,false);
                permissions_grid.setColLabel(idxValue,"<?php echo _l('Employee access', 1); ?>");

                var profil_id = profils_tree.getParentId(id);
                permissions_grid.setColLabel(idxProfilValue,"<?php echo _l('Profile access:', 1); ?> "+profils_tree.getItemText(profil_id));
                permissions_grid.setColLabel(idxProfilDiff,"<?php echo _l('Different from profile:', 1); ?> "+profils_tree.getItemText(profil_id));
            }

            if(id!=undefined && id!="")
            {
                permissions_grid.load("index.php?ajax=1&act=core_permissions_get&id="+id+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
                    getRowsNum=permissions_grid.getRowsNum();
                    permissions_grid.filterByAll();

                    if(id=="pr_1")
                    {
                        //permissions_grid.enableEditEvents(false, false, false);
                        permissions_grid.enableEditEvents(true, true, true);
                        is_super_admin = true;
                    }
                    else
                    {
                        var is_super_admin = false;
                        var super_admins = profils_tree.getAllSubItems("pr_1").split(',');
                        $.each(super_admins, function(index, id_child) {
                            if(id==id_child)
                                is_super_admin = true;
                        });
                        permissions_grid.enableEditEvents(true, true, true);
                    }

                    var ids=permissions_grid.getAllRowIds().split(",");
                    $.each(ids, function(index, id_row) {
                        var row_value = permissions_grid.cells(id_row,idxValue).getValue();
                        if(row_value==1)
                            permissions_grid.setRowColor(id_row,"#d4ffd5");
                        else
                            permissions_grid.setRowColor(id_row,"#ffdbdb");

                        permissions_grid.cells(id_row,idxProfilValue).setTextColor("#888888");
                        permissions_grid.cells(id_row,idxProfilDiff).setTextColor("#888888");

                        var tmp_exp = id_row.split("#");
                        if(is_super_admin && tmp_exp[1]!=undefined && tmp_exp[1]=="MEN_TOO_PERMISSIONS")
                        {
                            permissions_grid.cells(id_row,idxValue).setDisabled(true);
                            permissions_grid.cells(id_row,idxValue).setTextColor("#888888");
                        }
                    });

                    if (callback!='') eval(callback);
                });
            }
        }

        function displayProfils(callback)
        {
            profils_tree.deleteChildItems(0);
            permissions_grid.clearAll();
            profils_tree.load("index.php?ajax=1&act=core_permissions_profil_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
                profils_tree.openAllItems(0);
                if (callback!='') eval(callback);
            });
        }

        function callbackProfils(id)
        {
            var temp = id.split('_');
            if(temp[0]=='em')
            {
                var parent_id = profils_tree.getParentId(id);
                profils_tree.openItem(parent_id);
            }
            profils_tree.selectItem(id,true);
        }
<?php echo '</script>'; ?>
