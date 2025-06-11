<?php echo '<script type="text/javascript">'; ?>
    // INITIALIZE LAYOUT
    dhxlQrCodeImageImporter_layout = wQrCodeImageImporter.attachLayout("1C");
    dhxlQrCodeImageImporter_layout.cells('a').hideHeader();

    displayQrCodeImageContent();

    function displayQrCodeImageContent(){
        dhxlQrCodeImageImporter_layout.cells('a').attachURL('index.php?ajax=1&act=cat_image_qrcode_get',null,{ids:cat_grid.getSelectedRowId()});
    }
<?php echo '</script>'; ?>
