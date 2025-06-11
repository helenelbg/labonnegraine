<?php
$title = _l('Cart', 1);
$icon = 'fad fa-shopping-basket grey';
?>
prop_tb.addListOption('panel', 'cart', 3, "button", '<?php echo $title; ?>', "<?php echo $icon; ?>");
allowed_properties_panel[allowed_properties_panel.length] = "cart";

prop_tb.addButton("cart_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
prop_tb.setItemToolTip('cart_refresh','<?php echo _l('Refresh grid', 1); ?>');
prop_tb.addButton("exportcsv",1000, "", "fad fa-file-csv green", "fad fa-file-csv green");
prop_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1); ?>');
prop_tb.addButton("gotocatalog", 1000, "", 'fad fa-external-link green', 'fad fa-external-link green');
prop_tb.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.'); ?>');


needinitcart = 1;
function initcart(){
    if (needinitcart)
    {
        prop_tb._cartLayout = dhxLayout.cells('b').attachLayout('1C');
        prop_tb._cartLayout.cells('a').hideHeader();
        dhxLayout.cells('b').showHeader();
        prop_tb._cartGrid = prop_tb._cartLayout.cells('a').attachGrid();
        prop_tb._cartGrid.setImagePath("lib/js/imgs/");

        // UISettings
        prop_tb._cartGrid._uisettings_prefix='cus_cart';
        prop_tb._cartGrid._uisettings_name=prop_tb._cartGrid._uisettings_prefix;
        prop_tb._cartGrid._first_loading=1;

        // UISettings
        initGridUISettings(prop_tb._cartGrid);

        needinitcart=0;
    }
}


function setPropertiesPanel_cart(id){
    if (id=='cart')
    {
        if(lastCustomerSelID!=undefined && lastCustomerSelID!="")
        {
            idxLastname=cus_grid.getColIndexById('lastname');
            idxFirstname=cus_grid.getColIndexById('firstname');
            dhxLayout.cells('b').setText('<?php echo _l('Properties', 1).' '._l('of', 1); ?> '+cus_grid.cells(lastCustomerSelID,idxFirstname).getValue()+" "+cus_grid.cells(lastCustomerSelID,idxLastname).getValue());
        }
        hidePropTBButtons();
        prop_tb.showItem('exportcsv');
        prop_tb.showItem('cart_refresh');
        prop_tb.showItem('gotocatalog');
        prop_tb.setItemText('panel', '<?php echo $title; ?>');
        prop_tb.setItemImage('panel', '<?php echo $icon; ?>');
        needinitcart = 1;
        initcart();
        propertiesPanel='cart';
        if (lastCustomerSelID!=0)
        {
            displayCart();
        }
    }
    if (id=='cart_refresh')
    {
        displayCart();
    }
    if (id=='exportcsv'){
        displayQuickExportWindow(prop_tb._cartGrid,1);
    }
    if(id=='gotocatalog')
    {
        selection=prop_tb._cartGrid.getSelectedRowId();
        if (selection!='' && selection!=null)
        {
            var rowIds = selection.split(",");
            var rowId = rowIds[0];

            var open_cat_grid_ids  = prop_tb._cartGrid.getUserData(rowId, "open_cat_grid");
            if (open_cat_grid_ids!='' && open_cat_grid_ids!=null)
            {
                var url = "?page=cat_tree&open_cat_grid="+open_cat_grid_ids;
                window.open(url,'_blank');
            }
        }
    }
}
prop_tb.attachEvent("onClick", setPropertiesPanel_cart);


function displayCart()
{
    var customers_id = "";
    let idxIdAddress=cus_grid.getColIndexById('id_address');
    if(gridView!="grid_address" && idxIdAddress==undefined) {
        customers_id = cus_grid.getSelectedRowId();
    } else {
        idxIdCustomer=cus_grid.getColIndexById('id_customer');
        customers_id = cus_grid.cells(lastCustomerSelID,idxIdCustomer).getValue();
    }
    prop_tb._cartGrid.clearAll(true);
    $.post("index.php?ajax=1&act=cus_cart_get&id_lang="+SC_ID_LANG,{'id_customer': customers_id},function(data)
    {
        prop_tb._cartGrid.parse(data);
        nb=prop_tb._cartGrid.getRowsNum();
        prop_tb._sb.setText(nb+' '+(nb>1?'<?php echo _l('products', 1); ?>':'<?php echo _l('product', 1); ?>'));

        // UISettings
        loadGridUISettings(prop_tb._cartGrid);

        // UISettings
        prop_tb._cartGrid._first_loading=0;
    });
}

let cart_current_id = 0;
cus_grid.attachEvent("onSelectStateChanged",function (idcustomer){
    if (propertiesPanel=='cart' && !dhxLayout.cells('b').isCollapsed() && (cus_grid.getSelectedRowId()!==null && cart_current_id!=idcustomer)){
        displayCart();
        cart_current_id=idcustomer;
    }
});