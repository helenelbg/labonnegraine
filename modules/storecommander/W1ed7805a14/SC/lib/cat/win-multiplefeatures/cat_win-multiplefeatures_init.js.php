<?php echo '<script type="text/javascript">'; ?>
// layout
wCustomFeatureValueEditorLayout=wCustomFeatureValueEditor.attachLayout("1C");

// Cell A
wCustomFeatureValueEditorCell = wCustomFeatureValueEditorLayout.cells('a');
wCustomFeatureValueEditorCell.hideHeader();
wCustomFeatureValueEditorGrid = wCustomFeatureValueEditorCell.attachGrid();
<?php if (isField('position', 'feature_product')) { ?>
wCustomFeatureValueEditorGrid.enableMultiselect(true);
wCustomFeatureValueEditorGrid.enableDragAndDrop(true);
<?php } ?>
// Cell A Toolbar
wCustomFeatureValueEditorTb = wCustomFeatureValueEditorCell.attachToolbar()
wCustomFeatureValueEditorTb.setIconset('awesome');
<?php if (isField('position', 'feature_product')) { ?>
wCustomFeatureValueEditorTb.addButton("update_custom_value_position", 100, "", "fa fa-list-ol green", "fa fa-list-ol green");
wCustomFeatureValueEditorTb.setItemToolTip('update_custom_value_position','<?php echo _l('Save positions', 1); ?>');
<?php } ?>
wCustomFeatureValueEditorTb.addButton("add_custom_value", 100, "", "fa fa-plus-circle green", "fa fa-plus-circle green");
wCustomFeatureValueEditorTb.setItemToolTip('add_custom_value','<?php echo _l('Add custom feature', 1); ?>');
wCustomFeatureValueEditorTb.addButton("delete_custom_value", 100, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
wCustomFeatureValueEditorTb.setItemToolTip('delete_custom_value','<?php echo _l('Delete selected custom features', 1);
?>');
wCustomFeatureValueEditorTb.addButton("refresh_custom_value", 0, "", "fa fa-sync green", "fa fa-sync green");
wCustomFeatureValueEditorTb.setItemToolTip('refresh_custom_value','<?php echo _l('Refresh grid'); ?>');

wCustomFeatureValueEditorTb.attachEvent("onClick", function(id)
{
    if (id=='refresh_custom_value')
    {
        displayMultipleFeaturesCustomValues();
    }
    if (id=='add_custom_value')
    {
        let params = {
            "act": 'cat_win-multiplefeatures_update',
            "action": 'add',
            "featureId": '<?php echo (int) Tools::getValue('featureId'); ?>',
            "productIds": cat_grid.getSelectedRowId()
        };
        wCustomFeatureValueEditorCell.progressOn();
        params = new URLSearchParams({ ...ajaxDefaultParameters, ...params });
        $.post("index.php?"+params.toString()+"&"+new Date().getTime())
            .done(function(data){
                //wCustomFeatureValueEditorGrid.addRow(data.featureValueId,data.featureValueId);
                displayMultipleFeaturesCustomValues();
                wCustomFeatureValueEditorCell.progressOff();
            })
            .fail(function(xhr, status, error) {
            });
    }
    if (id=='delete_custom_value')
    {
        let params = {
            "act": 'cat_win-multiplefeatures_update',
            "action": 'delete',
            "featureId": '<?php echo (int) Tools::getValue('featureId'); ?>',
            "featureValueId": wCustomFeatureValueEditorGrid.getSelectedRowId(),
            "productIds": cat_grid.getSelectedRowId()
        }
        params = new URLSearchParams({ ...ajaxDefaultParameters, ...params });
        wCustomFeatureValueEditorCell.progressOn();
        $.post("index.php?"+params.toString()+"&"+new Date().getTime())
            .done(function(data){
                displayMultipleFeaturesCustomValues();
                wCustomFeatureValueEditorCell.progressOff();
            })
            .fail(function(xhr, status, error) {
            });
    }
    if (id=='update_custom_value_position')
    {
        var positions = [];
        wCustomFeatureValueEditorGrid.forEachRow(function(rId)
        {
            var rowIndex=wCustomFeatureValueEditorGrid.getRowIndex(rId);
            positions[rowIndex] = rId;
        });
        let params = {
            "act": 'cat_win-multiplefeatures_update',
            "action": 'position',
            "featureId": '<?php echo (int) Tools::getValue('featureId'); ?>',
            "productIds": cat_grid.getSelectedRowId(),
            'positions': positions
        }
        params = new URLSearchParams({ ...ajaxDefaultParameters, ...params });
        $.post("index.php?"+params.toString()+"&"+new Date().getTime())
            .done(function(data){
                displayMultipleFeaturesCustomValues();
            })
            .fail(function(xhr, status, error) {
            });
    }
});
wCustomFeatureValueEditorGrid.attachEvent("onEditCell", function(stage, rId, cIn, nValue, oValue){
    if (stage==2)
    {
        const forbidden_chars = [ '[', '^', '<', '>', '=', '{', '}', ']', '*', '$' ];
        if (forbidden_chars.some( char => nValue.includes(char))) {
            dhtmlx.message
            ({
                type: 'error',
                text: '<?php echo _l('Forbidden characters [^<>={}]*$'); ?>',
                expire: 3000
            });
            displayMultipleFeaturesCustomValues();
        }
        else
        {
            wCustomFeatureValueEditorGrid.setRowTextBold(rId);
            let params = {
                "act": 'cat_win-multiplefeatures_update',
                "action": 'update',
                    "featureId": '<?php echo (int)Tools::getValue('featureId'); ?>',
                'customValues': JSON.stringify(wCustomFeatureValueEditorGrid.getRowData(rId))
            };
                params = new URLSearchParams({...ajaxDefaultParameters, ...params});
                $.post("index.php?" + params.toString() + "&" + new Date().getTime())
                    .done(function (data) {
                    wCustomFeatureValueEditorGrid.cells(rId, cIn).setValue(nValue);
                    wCustomFeatureValueEditorGrid.setRowTextNormal(rId);
                })
                .fail(function(data){
                    wCustomFeatureValueEditorGrid.cells(rId, cIn).setValue(oValue);
                    wCustomFeatureValueEditorGrid.setRowTextNormal(rId);
                });
        }
    }

    return true;
});

multiplefeaturesFilter=0;
multiplefeaturesValuesFilter=0;
multiplefeatures_id_group=0;

const ajaxDefaultParameters = {
    "ajax": 1,
    "langId": SC_ID_LANG
};

displayMultipleFeaturesCustomValues();

// functions
/**
 *
 * @param scrollToRid
 */
function displayMultipleFeaturesCustomValues(scrollToRid=null)
{
    let params = {
        "act": 'cat_win-multiplefeatures_get',
        "featureId": '<?php echo (int) Tools::getValue('featureId'); ?>',
        //"filter": multiplefeaturesValuesFilter,
        "productIds": cat_grid.getSelectedRowId(),
        //'categoryId': catselection
    };
    params = new URLSearchParams({ ...ajaxDefaultParameters, ...params });

    $.post("index.php?"+params.toString()+"&"+new Date().getTime())
        .done(function(data){
            wCustomFeatureValueEditorGrid.parse(data);
            wCustomFeatureValueEditor.show();
            var nbLang = '<?php echo count(SCI::getActiveLangForSelectedShop('iso_code')); ?>'
            wCustomFeatureValueEditor.setDimension(100+nbLang*190,200+wCustomFeatureValueEditorGrid.getRowsNum()*30);
            wCustomFeatureValueEditor.setMinDimension(290, 400);
            wCustomFeatureValueEditor.center();

            if(scrollToRid!==null){
                wCustomFeatureValueEditorGrid.showRow(scrollToRid);
            }
        })
        .fail(function(xhr, status, error) {
        });
}

<?php echo '</script>'; ?>


