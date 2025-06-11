<?php if (_r('GRI_CUS_PROPERTIES_GRID_NOTES'))
{
    $subprop_name = 'notes';
    $subprop_title = _l('Notes', 1);
    $icon = 'fad fa-pen-fancy'; ?>
    prop_tb.addListOption('panel', '<?php echo $subprop_name; ?>', 3, "button", '<?php echo $subprop_title; ?>', "<?php echo $icon; ?>");
    allowed_properties_panel[allowed_properties_panel.length] = "<?php echo $subprop_name; ?>";
    prop_tb.addButton("notes_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
    prop_tb.setItemToolTip('notes_refresh','<?php echo _l('Refresh', 1); ?>');
    prop_tb.addButton("notes_save",1000, "", "fa fa-save blue", "fa fa-save blue");
    prop_tb.setItemToolTip('notes_save','<?php echo _l('Save', 1); ?>');

    needinitNotes = 1;
    function initNotes(){
        if (needinitNotes)
        {
            prop_tb._notesLayout = dhxLayout.cells('b').attachLayout('1C');
            prop_tb._notesLayout.cells('a').hideHeader();
            dhxLayout.cells('b').showHeader();
            needinitNotes=0;
        }
    }


    function setPropertiesPanel_Notes(id){
        if (id=='<?php echo $subprop_name; ?>')
        {
            if(lastCustomerSelID!=undefined && lastCustomerSelID!="")
            {
                idxLastname=cus_grid.getColIndexById('lastname');
                idxFirstname=cus_grid.getColIndexById('firstname');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cus_grid.cells(lastCustomerSelID,idxFirstname).getValue()+" "+cus_grid.cells(lastCustomerSelID,idxLastname).getValue());
            }
            hidePropTBButtons();
            prop_tb.showItem('notes_refresh');
            prop_tb.showItem('notes_save');
            prop_tb.setItemText('panel', '<?php echo $subprop_title; ?>');
            prop_tb.setItemImage('panel', '<?php echo $icon; ?>');
            needinitNotes = 1;
            initNotes();
            propertiesPanel='<?php echo $subprop_name; ?>';
            if (lastCustomerSelID!=0)
            {
                displayNotes();
            }
        }
        if (id=='notes_refresh')
        {
            displayNotes();
        }
        if (id=='notes_save')
        {
            let note_content = $('#note_textarea').val();
            $.post('index.php?ajax=1&act=cus_notes_update',{id_customer:customers_id,content:note_content},function(data)
            {
                if(data == 'OK') {
                    dhtmlx.message({text:'<?php echo _l('Note saved'); ?>',type:'success',expire:5000});
                    displayNotes();
                } else {
                    dhtmlx.message({text:'<?php echo _l('Error'); ?>',type:'error',expire:5000});
                }
            });
        }
    }
    prop_tb.attachEvent("onClick", setPropertiesPanel_Notes);


    function displayNotes()
    {
        let idxIdAddress=cus_grid.getColIndexById('id_address');
        if(gridView!="grid_address" && idxIdAddress==undefined) {
            customers_id = lastCustomerSelID;
        } else {
            idxIdCustomer = cus_grid.getColIndexById('id_customer');
            customers_id = cus_grid.cells(lastCustomerSelID,idxIdCustomer).getValue();
        }
        $.post('index.php?ajax=1&act=cus_notes_get',{id_customer:customers_id},function(data)
        {
            prop_tb._notesLayout.cells('a').attachHTMLString('<textarea id="note_textarea" style="resize: none;box-sizing: border-box;width: 100%;height: 100%;">'+data+'</textarea>');
        });
    }

    let <?php echo $subprop_name; ?>_current_id = 0;
    cus_grid.attachEvent("onRowSelect",function (idcustomer){
        lastCustomerSelID = idcustomer
        if (propertiesPanel=='<?php echo $subprop_name; ?>' && !dhxLayout.cells('b').isCollapsed() && (cus_grid.getSelectedRowId()!==null && <?php echo $subprop_name; ?>_current_id!=idcustomer)){
            displayNotes();
            if(lastCustomerSelID!=undefined && lastCustomerSelID!="")
            {
                idxLastname=cus_grid.getColIndexById('lastname');
                idxFirstname=cus_grid.getColIndexById('firstname');
                dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cus_grid.cells(lastCustomerSelID,idxFirstname).getValue()+" "+cus_grid.cells(lastCustomerSelID,idxLastname).getValue());
            }
            <?php echo $subprop_name; ?>_current_id=idcustomer;
        }
    });
<?php
}
?>