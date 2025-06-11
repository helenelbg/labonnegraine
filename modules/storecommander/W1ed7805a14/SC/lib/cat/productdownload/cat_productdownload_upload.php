<?php
if (!empty($_FILES))
{
    $id_product = (array_key_exists('id_product', $_REQUEST) ? (int) $_REQUEST['id_product'] : null);
    $id_product_download = (array_key_exists('id_product_download', $_REQUEST) ? (int) $_REQUEST['id_product_download'] : null);
    $id_lang = (array_key_exists('id_lang', $_REQUEST) ? (int) $_REQUEST['id_lang'] : Configuration::get('PS_LANG_DEFAULT'));
    $action = (array_key_exists('action', $_REQUEST) ? (string) $_REQUEST['action'] : null);
    if (!empty($id_product) && !empty($action))
    {
        $display_filename = basename($_FILES['file']['name']);
        $filename = ProductDownload::getNewFilename();
        if (move_uploaded_file($_FILES['file']['tmp_name'], _PS_DOWNLOAD_DIR_.$filename))
        {
            switch ($action) {
                case 'edit_file':
                    $download = new ProductDownload($id_product_download);
                    $download->id_product = $id_product;
                    $download->display_filename = $display_filename;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        @unlink(_PS_DOWNLOAD_DIR_.'/'.$download->filename);
                        $download->filename = (string) $filename;
                    }
                    else
                    {
                        @unlink(_PS_DOWNLOAD_DIR_.'/'.$download->physically_filename);
                        $download->physically_filename = (string) $filename;
                    }
                    if ($download->date_expiration == '0000-00-00 00:00:00')
                    {
                        $download->date_expiration = null;
                    }
                    $res = $download->save();
                    break;
                default:
                    $download = new ProductDownload();
                    $download->id_product = $id_product;
                    $download->display_filename = $display_filename;
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $download->filename = (string) $filename;
                        $download->date_add = date('Y-m-d H:i:s');
                    }
                    else
                    {
                        $download->physically_filename = (string) $filename;
                        $download->date_deposit = date('Y-m-d H:i:s');
                    }
                    $download->date_expiration = null;
                    $download->nb_days_accessible = null;
                    $download->nb_downloadable = null;
                    $download->active = 1;
                    $download->save();

                    $res = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product SET is_virtual=1'.(version_compare(_PS_VERSION_, '1.7.8.0', '>=') ? ', product_type="'.PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType::TYPE_VIRTUAL.'"' : '').' WHERE id_product = '.(int) $id_product);
            }
            if ($res)
            {
                // PM Cache
                if (!empty($id_product))
                {
                    ExtensionPMCM::clearFromIdsProduct($id_product);
                }
            }
        }
        else
        {
            exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 108, "message": "An error occurred during file upload. Please try again."}, "id" : "id"}');
        }
    }
}
else
{
    ?>
    <style type="text/css">@import url(<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.css);</style>
    <script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
    <script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
    <script type="text/javascript" src="<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.js"></script>
    <?php
    $error_uploadable = array();
    $id_lang = (int) Tools::getValue('id_lang', Configuration::get('PS_LANG_DEFAULT'));
    $id_product = (int) Tools::getValue('id_product', 0);
    $id_product_download = (int) Tools::getValue('id_product_download', 0);
    $action = 'add_file';
    if (!empty($id_product_download))
    {
        $action = 'edit_file';
    }
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        $product = new Product($id_product, false, null, (int) SCI::getSelectedShop());
    }
    else
    {
        $product = new Product($id_product);
    }
    if ($product->hasAttributes())
    {
        $error_uploadable[] = _l('A virtual product cannot have combinations.');
    }
    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
    {
        if ($product->advanced_stock_management)
        {
            $error_uploadable[] = _l('A virtual product cannot use the advanced stock management.');
        }
    }

    $size_limit = ini_get('upload_max_filesize');
    if (version_compare(_PS_VERSION_, '1.5.0.2', '>='))
    {
        $size_limit = Tools::getOctets($size_limit);
    }
    else
    {
        if (preg_match('/[0-9]+k/i', $option))
        {
            $size_limit = 1024 * (int) $option;
        }

        if (preg_match('/[0-9]+m/i', $option))
        {
            $size_limit = 1024 * 1024 * (int) $option;
        }

        if (preg_match('/[0-9]+g/i', $option))
        {
            $size_limit = 1024 * 1024 * 1024 * (int) $option;
        }
    }
    $size_limit = $size_limit / 1024 / 1024;

    if (!empty($id_product))
    {
        ?>
        <body style="margin:0;">
        <div id="file_uploader"></div>
        <script type="text/javascript">
            <?php require_once SC_PLUPLOAD.'js/vault/vault_lang.php'; ?>
            let check_product = <?php echo json_encode($error_uploadable); ?>;
            if(check_product.length > 0) {
                check_product.forEach(function(message) {
                    dhx.message({
                        text: message,
                        css: "dhx-error",
                        expire: 4000
                    });
                });
            } else {
                let vaultObject = new dhx.Vault("file_uploader", {
                    uploader: {
                        target: 'index.php?ajax=1&act=cat_productdownload_upload&id_product_download=<?php echo (int) $id_product_download; ?>&id_product=<?php echo (int) $id_product; ?>&id_lang=<?php echo (int) $id_lang; ?>&action=<?php echo $action; ?>',
                        autosend: false
                    }
                });
                vaultObject.events.on("beforeAdd", function(item) {

                    if (vaultObject.data.getLength() >= 1) {
                        dhx.message({
                            text: "<?php echo _l('Only one file by upload', 1); ?>",
                            css: "dhx-error",
                            expire: 4000
                        });
                        return false;
                    } else {
                        let size = item.file.size;
                        let size_kb = Number(priceFormat(size / 1024 / 1024));
                        let sizeLimit = <?php echo $size_limit; ?>;
                        if (!size_kb > sizeLimit) {
                            dhx.message({
                                text: "<?php echo _l('The file is too large. Maximum size allowed is: %1$d Mo. The file you are trying to upload is ', 1, array(number_format($size_limit, 2, '.', ''))); ?> " + size_kb + " Mo",
                                css: "dhx-error",
                                expire: 4000
                            });
                            return false;
                        }
                        return true;
                    }
                });
                vaultObject.events.on("UploadComplete", function(files){
                    var error = 0;
                    files.forEach(function(item){
                        if(item.request.response.length > 0) {
                            let file_response  = JSON.parse(item.request.response);
                            if(file_response.error !== null) {
                                vaultObject.data.update(item.id,{status:'failed'});
                                dhx.message({
                                    text: "code:"+file_response.error.code+" "+file_response.error.message,
                                    css: "dhx-error",
                                    expire: 4000
                                });
                                error = error+1;
                            }
                        }
                    });
                    if(error === 0) {
                        parent.displayProductDownload();
                        parent.prop_tb._productdownloadLayout.cells('b').collapse();
                    }
                });
                vaultObject.events.on("UploadFail", function(file){
                    console.log(file);
                    dhx.message({
                        text: "<?php echo _l('Error', 1); ?>",
                        css: "dhx-error",
                        expire: 4000
                    });
                });
            }
        </script>
        </body>
        <?php
    }
}
