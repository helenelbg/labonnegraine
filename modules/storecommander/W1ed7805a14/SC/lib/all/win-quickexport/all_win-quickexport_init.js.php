<div id="divQuickExport" style="display:none;box-sizing: content-box;height: 100%;"><textarea id="taQuickExport" style="width:100%;height:100%;padding:0;margin:0;"></textarea></div>
<?php echo '<script type="text/javascript">'; ?>
    function displayQuickExportWindow(grid,firstline=null,databrut=null,excluded_fields=null,plain_text=false,value_separator="\t"){
        if (!dhxWins.isWindow("wQuickExportWindow"))
        {
            wQuickExportWindow = dhxWins.createWindow("wQuickExportWindow", 50, 50, 450, 460);
            wQuickExportWindow.setText("<?php echo _l('Quick export window'); ?>");
            lQEW = new dhtmlXLayoutObject(wQuickExportWindow, "1C");
            lQEW.cells('a').hideHeader();
            let lQEW_sb = lQEW.cells('a').attachStatusBar(
                {
                    text:'<div style="white-space: normal;word-spacing: normal;word-break: normal;line-height: 22px;"><?php echo _l('Copy the contents of this window and paste it directly into your spreadsheet.'); ?></div>',
                    height:45
                }
            );
            wQuickExportWindow.attachEvent("onClose", function(win){
                wQuickExportWindow.hide();
                return false;
            });
            lQEW.cells('a').appendObject('divQuickExport');
            $('#divQuickExport').css('display','block');
            wQuickExportWindow._add_prop_tb=wQuickExportWindow.attachToolbar();
            wQuickExportWindow._add_prop_tb.setIconset('awesome');
            wQuickExportWindow._add_prop_tb.addButton("selectall", 0, "", "fa fa-bolt yellow", "fad fa-bolt grey");
            wQuickExportWindow._add_prop_tb.setItemToolTip('selectall','<?php echo _l('Select all'); ?>');
            // events
            wQuickExportWindow._add_prop_tb.attachEvent("onClick",function(id){
                if (id=="selectall")
                    $('#taQuickExport').select();
            });
        }else{
            wQuickExportWindow.show();
        }
        dhxWins.window('wQuickExportWindow').bringToTop();

        var csv = "";
        var filters = new Array();
        var types = new Array();
        var first = 1;

        if(firstline==1)
        {
            var nb_col = grid.getColumnsNum();
            var row = "";
            for (var i=0;i<nb_col;i++)
            {
                if(!grid.isColumnHidden(i) && (excluded_fields===null || !excluded_fields.includes(grid.getColumnId(i))))
                {
                    var name = grid.getColLabel(i);
                    if(row!="")
                        row = row + value_separator;
                    row = row +name;
                }
            };
            if(row!="")
            {
                row = row + "\n";
                csv = csv + row;
            }
        }

        grid.forEachRowA(function(rId){
            var row = "";
            grid.forEachCell(rId,function(cellObj,ind){
                if(excluded_fields===null || !excluded_fields.includes(grid.getColumnId(ind)))
                {
                    if(first==1)
                    {
                        if(grid.getFilterElement(ind)!=undefined)
                            filters[ind] = grid.getFilterElement(ind).value;
                        else
                            filters[ind] = "";
                        types[ind] = grid.getColType(ind);
                    }
                    if(!grid.isColumnHidden(ind) && grid.getRowIndex(rId)>=0)
                    {
                        if(!databrut && (types[ind]=="coro" || types[ind]=="co"))
                            var value = cellObj.getTitle();
                        <?php if (_s('APP_QUICKEXPORT_NUMBER_SEP') == '1') { ?>
                        else if($.isNumeric(cellObj.getValue()))
                            var value = cellObj.getValue().replace(".",",");
                        <?php } ?>else
                            var value = cellObj.getValue();
                        if(row!="")
                            row = row + value_separator;
                        row = row +value;
                    }
                }
            });
            if(row!="")
            {
                row = row + "\n";
                csv = csv + row;
            }
        });

        if(plain_text) {
            csv = csv.replace(/&amp;/g, "&").replace(/&lt;/g, "<").replace(/&gt;/g, ">");
            $('#taQuickExport').text(csv);
        } else {
            $('#taQuickExport').html(csv);
        }
        $('#taQuickExport').select();
    }
<?php echo '</script>'; ?>