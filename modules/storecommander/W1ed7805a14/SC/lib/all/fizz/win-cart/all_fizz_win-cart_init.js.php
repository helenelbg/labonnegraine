<?php echo '<script type="text/javascript">'; ?>
    // LAYOUT
    dhxleServices_layout=weServices.attachLayout("2U");

    var col_eServicesLeft = dhxleServices_layout.cells('a');
    col_eServicesLeft.showHeader();
    col_eServicesLeft.setWidth("280");
    col_eServicesLeft.fixSize(true, false);
    var col_eServicesLeft_layout = col_eServicesLeft.attachLayout("2E");


    // ADD
    var cell_add =  col_eServicesLeft_layout.cells('a');
    cell_add.hideHeader();
    col_eServicesLeft.fixSize(true, true);
    cell_add.attachURL('index.php?ajax=1&act=all_fizz_win-cart_add');

    // WALLET
    var cell_wallet =  col_eServicesLeft_layout.cells('b');
    cell_wallet.hideHeader();
    cell_wallet.setHeight("80");
    cell_wallet.fixSize(true, true);
    cell_wallet.attachURL('index.php?ajax=1&act=all_fizz_win-cart_wallet');

    // CART
    var cell_eServicesPayment =  dhxleServices_layout.cells('b');
    cell_eServicesPayment.setText('<?php echo _l('e-Services', 1); ?>');
    cell_eServicesPayment.hideHeader();

    cell_eServicesPayment.attachURL('https://www.storecommander.com/<?php echo SC_ISO_LANG_FOR_EXTERNAL; ?>index.php?controller=cms&id_cms=45&is_eservices=1&ces=<?php echo sha1(SCI::getConfigurationValue('SC_LICENSE_KEY')); ?>');
<?php echo '</script>'; ?>