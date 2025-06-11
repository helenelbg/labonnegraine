<?php echo '<script type="text/javascript">'; ?>
    dhxlCatImport=wCatExport.attachLayout("1C");
    dhxlCatImport.cells('a').hideHeader();

    wCatExport.gridExport=dhxlCatImport.cells('a').attachGrid();
    wCatExport.gridExport.setImagePath("lib/js/imgs/");
    wCatExport.gridExport.enableColumnMove(true);
    wCatExport.gridExport.enableMultiline(true);
    wCatExport.gridExport.init();

    wCatExport.tbOptions=dhxlCatImport.cells('a').attachToolbar();
    wCatExport.tbOptions.setIconset('awesome');
    wCatExport.tbOptions.addButton("exportcsv", 0, "", "fad fa-file-csv green", "fad fa-file-csv green");
    wCatExport.tbOptions.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.'); ?>');
    wCatExport.tbOptions.addButton("refresh", 0, "", "fa fa-sync green", "fa fa-sync green");
    wCatExport.tbOptions.setItemToolTip('refresh','<?php echo _l('Refresh', 1); ?>');
    wCatExport.tbOptions.attachEvent("onClick",
        function(id){
            if (id=='refresh')
            {
                displayCatExport();
            }
            if (id=='exportcsv'){
                var colNum=wCatExport.gridExport.getColumnsNum()*1 - 1;
                for(var i=colNum; i>0; i--)
                {
                    var isHidden=wCatExport.gridExport.isColumnHidden(i);
                    if(isHidden)
                        wCatExport.gridExport.deleteColumn(i);
                }

                displayQuickExportWindow(wCatExport.gridExport,1,null,null,true);
            }
        });

    displayCatExport();
    
//#####################################
//############ Load functions
//#####################################

function displayCatExport(callback)
{
    wCatExport.gridExport.clearAll(true);
    wCatExport.gridExport.load("index.php?ajax=1&act=cat_win-catexport_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
        if (callback)
        {
            eval(callback);
        }
        wCatExport.gridExport.enableHeaderMenu();
    });
}
<?php echo '</script>'; ?>
<div id="alertbox" style="width:400px;height:200px;color:#FFFFFF" onclick="stopCatAlert();">Click here to close alert.</div>