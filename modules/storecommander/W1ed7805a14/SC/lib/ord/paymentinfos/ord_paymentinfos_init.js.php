<?php if (version_compare(_PS_VERSION_, '1.5.0.13', '>='))
{
    $subprop_name = 'paymentinfos';
    $subprop_title = _l('Payment', 1);
    $icon = 'fad fa-money-bill-alt'; ?>
prop_tb.addListOption('panel', '<?php echo $subprop_name; ?>', 1, "button", '<?php echo $subprop_title; ?>', "<?php echo $icon; ?>");
allowed_properties_panel[allowed_properties_panel.length] = "<?php echo $subprop_name; ?>";

prop_tb.addButton("<?php echo $subprop_name; ?>_refresh",1000, "", "fa fa-sync green", "fa fa-sync green");
prop_tb.setItemToolTip('<?php echo $subprop_name; ?>_refresh', '<?php echo _l('Refresh grid', 1); ?>');

var needinitPaymentInfos = 1;
function initPaymentInfos() {
    if (needinitPaymentInfos) {
        prop_tb._PaymentInfosLayout = dhxLayout.cells('b').attachLayout('1C');
        prop_tb._PaymentInfosLayout.cells('a').hideHeader();
        dhxLayout.cells('b').showHeader();
        prop_tb._PaymentInfosGrid = prop_tb._PaymentInfosLayout.cells('a').attachGrid();
        prop_tb._PaymentInfosGrid.setImagePath("lib/js/imgs/");

        // UISettings
        prop_tb._PaymentInfosGrid._uisettings_prefix = 'ord_<?php echo $subprop_name; ?>';
        prop_tb._PaymentInfosGrid._uisettings_name = prop_tb._PaymentInfosGrid._uisettings_prefix;
        prop_tb._PaymentInfosGrid._first_loading = 1;

        // UISettings
        initGridUISettings(prop_tb._PaymentInfosGrid);

        needinitPaymentInfos = 0;
    }
}

function setPropertiesPanel_PaymentInfos(id) {
    switch (id) {
        case '<?php echo $subprop_name; ?>':
            hidePropTBButtons();
            prop_tb.showItem('<?php echo $subprop_name; ?>_refresh');
            prop_tb.setItemText('panel', '<?php echo $subprop_title; ?>');
            prop_tb.setItemImage('panel', '<?php echo $icon; ?>');
            needinitPaymentInfos = 1;
            initPaymentInfos();
            propertiesPanel = '<?php echo $subprop_name; ?>';
            if (lastOrderSelID != 0) {
                displayPaymentInfos();
            }
            break;
        case '<?php echo $subprop_name; ?>_refresh':
            displayPaymentInfos();
            break;
    }
}
prop_tb.attachEvent("onClick", setPropertiesPanel_PaymentInfos);

function displayPaymentInfos() {
    prop_tb._PaymentInfosGrid.clearAll(true);
    prop_tb._PaymentInfosGrid.load("index.php?ajax=1&act=ord_<?php echo $subprop_name; ?>_get&id_order=" + lastOrderSelID, function () {
        nb = prop_tb._PaymentInfosGrid.getRowsNum();
        prop_tb._sb.setText('');

        // UISettings
        loadGridUISettings(prop_tb._PaymentInfosGrid);

        // UISettings
        prop_tb._PaymentInfosGrid._first_loading = 0;
    });
}

let <?php echo $subprop_name; ?>_current_id = 0;
ord_grid.attachEvent("onRowSelect", function (id_order) {
    if (propertiesPanel == '<?php echo $subprop_name; ?>' && !dhxLayout.cells('b').isCollapsed()
        && (ord_grid.getSelectedRowId() !== null && <?php echo $subprop_name; ?>_current_id != id_order)) {
        displayPaymentInfos();
        <?php echo $subprop_name; ?>_current_id = id_order;
    }
});
<?php
}
?>