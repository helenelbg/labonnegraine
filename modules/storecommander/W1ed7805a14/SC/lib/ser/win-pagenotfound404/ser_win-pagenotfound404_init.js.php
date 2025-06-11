<?php echo '<script type="text/javascript">'; ?>
    var lPageNotFound = new dhtmlXLayoutObject(wPageNotFound, "1C");
    lPageNotFound.cells('a').hideHeader();
    pagenotfound_grid=lPageNotFound.cells('a').attachGrid();
    pagenotfound_grid.setImagePath('lib/js/imgs/');
    pagenotfound_grid.setDateFormat("%Y-%m-%d");
    pagenotfound_grid.enableSmartRendering(true);
    pagenotfound_grid.init();

    pagenotfound_tb=lPageNotFound.cells('a').attachToolbar();
    pagenotfound_tb.setIconset('awesome');
    pagenotfound_tb.addButton("delete404", 0, "", "fa fa-minus-circle red", "fa fa-minus-circle red");
    pagenotfound_tb.setItemToolTip('delete404','<?php

 echo _l('Delete all items', 1); ?>');
    pagenotfound_tb.addButton("exportcsv", 0, "", "fad fa-file-csv green", "fad fa-file-csv green");
    pagenotfound_tb.setItemToolTip('exportcsv','<?php echo _l('Export', 1); ?>');
    pagenotfound_tb.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    pagenotfound_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid', 1); ?>');
    pagenotfound_tb.attachEvent("onClick",
        function(id){
            if (id=='refresh'){
                displayPageNotFound();
            }
            if (id=='exportcsv'){
                displayQuickExportWindow(pagenotfound_grid);
            }
            if (id=='delete404'){
                if (confirm('<?php echo _l('Do you want to delete all items?', 1); ?>'))
                    $.get("index.php?ajax=1&act=ser_win-pagenotfound404_update&action=deleteall",function(data){
                                    displayPageNotFound();
                                    dhtmlx.message({text:data,type:'info',expire:5000});
                                });
            }
        });

    function displayPageNotFound(callback)
    {
        pagenotfound_grid.clearAll();
        pagenotfound_grid.load("index.php?ajax=1&act=ser_win-pagenotfound404_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
            if (callback!='') eval(callback);
            });
    }
    displayPageNotFound();
<?php echo '</script>'; ?>