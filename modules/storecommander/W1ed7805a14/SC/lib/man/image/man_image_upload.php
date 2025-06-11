<style type="text/css">@import url(<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.css);</style>
<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.js"></script>
<?php
$id_lang = Tools::getValue('id_lang', null);
$manufacturer_list = Tools::getValue('manufacturer_list', null);
?>
<body style="margin:0;">
<div id="file_uploader"></div>
<script>
    <?php require_once SC_PLUPLOAD.'js/vault/vault_lang.php'; ?>
    let autosend = top.ll_toolbar.getItemState('auto_upload');
    let authorized_extensions = ["jpg","jpeg","png","gif","bmp"];
    <?php
    if (version_compare(_PS_VERSION_, '8.0', '>='))
    {?>
    authorized_extensions.push('webp');
    <?php } ?>
    let vaultObject = new dhx.Vault("file_uploader", {
        uploader:{
            target: 'index.php?ajax=1&act=all_upload&obj=manufacturer_logo&manufacturer_list=<?php echo $manufacturer_list; ?>&id_lang=<?php echo $id_lang; ?>',
            autosend:autosend
        },
        mode:"grid",

    });
    vaultObject.events.on("BeforeAdd", function(item){
        this.uploader.config.autosend = top.ll_toolbar.getItemState('auto_upload');
        this.uploader.autosend = top.ll_toolbar.getItemState('auto_upload');
        this.paint();
        let extension = item.file.name.split('.').pop();
        if(authorized_extensions.indexOf(extension.toLowerCase()) >= 0) {
            let fileSize = item.file.size;
            let fileSizeMo = fileSize / 1024 / 1024;
            <?php
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $limitSizeMo = (Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE'));
            }
            else
            {
                $limitSizeMo = (Configuration::get('PS_PRODUCT_PICTURE_MAX_SIZE') / 1024 / 1024);
            }
            ?>
            let limitSize = <?php echo $limitSizeMo; ?>;
            if (fileSizeMo > limitSize) {
                dhx.message({
                    text: "<?php echo _l('The file is too large. Maximum size allowed is: %1$d Mo. The file you are trying to upload is ', 1, array($limitSizeMo)); ?> " + fileSizeMo.toFixed(2) + " Mo",
                    css: "dhx-error",
                    expire: 4000
                });
                return false;
            }
        } else {
            dhx.message({
                text: "<?php echo _l('Wrong file format', 1); ?> ("+authorized_extensions.join(',')+" <?php echo _l('only', 1); ?>)",
                css: "dhx-error",
                expire: 4000
            });
            return false;
        }
    });
    vaultObject.events.on("UploadBegin", function(files){
        top.prop_tb._imagesUploadWindow['<?php echo $manufacturer_list; ?>'].park();
    });
    vaultObject.events.on("UploadComplete", function(files){
        var error = 0;
        files.forEach(function(item){
            let file_response  = JSON.parse(item.request.response);
            if(file_response.error !== null) {
                vaultObject.data.update(item.id,{status:'failed'});
                dhx.message({
                    text: "code:"+file_response.error.code+" "+file_response.error.message,
                    css: "dhx-error",
                    expire: 6000
                });
                error = error+1;
            }
        });
        if(error === 0) {
            top.prop_tb._imagesUploadWindow['<?php echo $manufacturer_list; ?>'].hide();
            top.displayImages();
            top.displayManufacturers();
        }
    });
</script>
</body>
