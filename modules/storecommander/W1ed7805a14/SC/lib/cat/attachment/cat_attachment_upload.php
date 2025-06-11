<style type="text/css">@import url(<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.css);</style>
<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.js"></script>
<?php
$id_lang = (int) Tools::getValue('id_lang', Configuration::get('PS_LANG_DEFAULT'));
$product_list = Tools::getValue('product_list', null);
if (!empty($product_list))
{
    ?>
<body style="margin:0;">
<div id="file_uploader"></div>
<script>
    <?php require_once SC_PLUPLOAD.'js/vault/vault_lang.php'; ?>
    let autosend = top.wCatAddAttachment._add_prop_tb.getItemState('attachment_checked');
    let vaultObject = new dhx.Vault("file_uploader", {
        uploader:{
            target: 'index.php?ajax=1&act=all_upload&obj=attachment&product_list=<?php echo $product_list; ?>&id_lang=<?php echo (int) $id_lang; ?>&linktoproduct='+top.wCatAddAttachment._linkToProducts,
            autosend:autosend
        }
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
                    expire: 4000
                });
                error = error+1;
            }
        });
        if(error === 0) {
            top.wCatAddAttachment.hide();
            top.displayAttachments('',true);
        }
    });
    vaultObject.events.on("BeforeAdd", function(item) {
        this.uploader.config.autosend = top.wCatAddAttachment._add_prop_tb.getItemState('attachment_checked');
        this.uploader.autosend = top.wCatAddAttachment._add_prop_tb.getItemState('attachment_checked');
        this.paint();

        let size = item.file.size;
        let filename_length = item.file.name.length;
        <?php $attachment_filename_length = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && array_key_exists('size', Attachment::$definition['fields']['name']) ? (int) Attachment::$definition['fields']['name']['size'] : 128); ?>
        if(filename_length > Number(<?php echo $attachment_filename_length; ?>)) {
            dhx.message({
                text: "<?php echo _l('The filename is too long. Maximum length allowed is: %1$d characters.', 1, array($attachment_filename_length)); ?> ",
                css: "dhx-error",
                expire: 4000
            });
            return false;
        }
        let size_kb = priceFormat(size / 1024 / 1024);
        let sizeLimit = <?php echo Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024; ?>;
        let predicate = size < sizeLimit;
        if (!predicate) {
            dhx.message({
                text: "<?php echo _l('The file is too large. Maximum size allowed is: %1$d Mo. The file you are trying to upload is ', 1, array(number_format((Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')), 2, '.', ''))); ?> " + size_kb + " Mo",
                css: "dhx-error",
                expire: 4000
            });
        }
        return predicate;
    });
</script>
</body>
<?php
} ?>