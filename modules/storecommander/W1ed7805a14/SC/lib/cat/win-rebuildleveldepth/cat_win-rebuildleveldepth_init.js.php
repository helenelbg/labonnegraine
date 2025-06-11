<?php echo '<script type="text/javascript">'; ?>
    let dhxlRebuildLevelDepth_layout_layout = wRebuildLevelDepth.attachLayout("1C");

    let dhxlRebuildLevelDepth_cell = dhxlRebuildLevelDepth_layout_layout.cells('a');
    dhxlRebuildLevelDepth_cell.hideHeader();

    let dhxlRebuildLevelDepth_toolbar = dhxlRebuildLevelDepth_cell.attachToolbar();
    dhxlRebuildLevelDepth_toolbar.setIconset('awesome');
    dhxlRebuildLevelDepth_toolbar.addButton("rebuild_refresh", 100, "", "fa fa-sync green", "fa fa-sync green");
    dhxlRebuildLevelDepth_toolbar.setItemToolTip('rebuild_refresh','<?php echo _l('Refresh grid', 1); ?>');
    dhxlRebuildLevelDepth_toolbar.addButton("rebuild_process", 100, "", "fad fa-tools green", "fad fa-tools green");
    dhxlRebuildLevelDepth_toolbar.setItemToolTip('rebuild_process','<?php echo _l('Check and fix categories', 1); ?>');

    let dhxlRebuildLevelDepth_layout_grid = dhxlRebuildLevelDepth_cell.attachGrid();
    var all_ids = '';
    dhxlRebuildLevelDepth_toolbar.attachEvent('onClick',function(itemid) {
        switch(itemid) {
            case 'rebuild_refresh':
                dhxlRebuildLevelDepth_layout_grid.displayRows();
                break;
            case 'rebuild_process':
                all_ids = dhxlRebuildLevelDepth_layout_grid.getAllRowIds();
                all_ids = all_ids.split(',');

                if(typeof cat_categoryPanel == 'object'){
                    cat_categoryPanel.progressOn();
                }
                if(typeof dhxLayout.cells('b') == 'object' && propertiesPanel == 'categories') {
                    dhxLayout.cells('b').progressOn();
                }

                dhxlRebuildLevelDepth_layout_grid.parseIdShopQueue();
                break;
        }
    });

    dhxlRebuildLevelDepth_layout_grid.parseIdShopQueue = function(){
        if(all_ids[0] !== undefined) {
            let id_shop = all_ids[0];
            $.post('index.php?ajax=1&act=cat_win-rebuildleveldepth_update',{id_shop: id_shop},function(response){
                dhxlRebuildLevelDepth_layout_grid.changeCellWhenDone(response);
                all_ids.splice(0,1);
                if(all_ids.length > 0) {
                    dhxlRebuildLevelDepth_layout_grid.parseIdShopQueue();
                } else {
                   if(typeof cat_categoryPanel == 'object'){
                        cat_categoryPanel.progressOff();
                    }
                    if(typeof dhxLayout.cells('b') == 'object' && propertiesPanel == 'categories') {
                        dhxLayout.cells('b').progressOff();
                    }
                }
            });
        }
    }

    dhxlRebuildLevelDepth_layout_grid.displayRows = function (){
        dhxlRebuildLevelDepth_layout_grid.clearAll(true);
        dhxlRebuildLevelDepth_layout_grid.load('index.php?ajax=1&act=cat_win-rebuildleveldepth_get');
    }

    dhxlRebuildLevelDepth_layout_grid.changeCellWhenDone = function (response){
        let response_arg = response.split('|');
        if (response_arg[1] === 'ok') {
            let rId = response_arg[0];
            let cId_status = dhxlRebuildLevelDepth_layout_grid.getColIndexById('status');
            let cell = dhxlRebuildLevelDepth_layout_grid.cells(rId, cId_status);
            cell.setValue("<?php echo _l('Done'); ?>");
            cell.setBgColor('green');
            dhxlRebuildLevelDepth_layout_grid.showRow(rId);
        }
    }

    dhxlRebuildLevelDepth_layout_grid.displayRows();

<?php echo '</script>'; ?>