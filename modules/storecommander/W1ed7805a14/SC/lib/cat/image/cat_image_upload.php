<style type="text/css">@import url(<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.css);</style>
<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.js"></script>
<?php
$id_lang = Tools::getValue('id_lang', null);
$product_list = $original_product_list = Tools::getValue('product_list', null);
$attr_list = Tools::getValue('attr_list', null);
$is_attr = Tools::getValue('is_attr', null);
$is_combination_multiproduct = Tools::getValue('multi', null);
if ($is_combination_multiproduct)
{
    $attr_arr = explode(',', $attr_list);
    $cache = array();
    foreach ($attr_arr as $row)
    {
        list($id_product, $id_product_attribute) = explode('_', $row);
        $cache['product'][$id_product] = (int) $id_product;
        $cache['attribute'][$id_product_attribute] = (int) $id_product_attribute;
    }
    if (!empty($cache))
    {
        $product_list = implode(',', $cache['product']);
        $attr_list = implode(',', $cache['attribute']);
    }
}
?>
<body style="margin:0;">
<div id="file_uploader"></div>
<script>
    <?php require_once SC_PLUPLOAD.'js/vault/vault_lang.php'; ?>
    let window_id = "<?php echo $original_product_list; ?>";
    let autosend = <?php echo (int) _s('CAT_PROPERTIES_IMAGE_AUTO_UPLOAD'); ?>;
    if(top.ll_toolbar !== undefined) {
        autosend = top.ll_toolbar.getItemState('auto_upload');
    }
    let authorized_extensions = ["jpg","jpeg","png","gif","bmp"];
    <?php
    if (version_compare(_PS_VERSION_, '8.0', '>='))
    {?>
        authorized_extensions.push('webp');
    <?php } ?>
    let vaultObject = new dhx.Vault("file_uploader", {
        uploader:{
            target: 'index.php?ajax=1&act=all_upload&from_vault=1&obj=image&id_lang=<?php echo (int) $id_lang.($is_combination_multiproduct ? '&is_multiproduct=1' : ''); ?>',
            autosend:autosend,
            singleRequest:true,
            params:{
                product_list:'<?php echo $product_list; ?>',
                attr_list:'<?php echo $attr_list; ?>'
            }
        },
        mode:"grid",
    });
    vaultObject.events.on("BeforeAdd", function(item){
        let autosend = <?php echo (int) _s('CAT_PROPERTIES_IMAGE_AUTO_UPLOAD'); ?>;
        if(top.ll_toolbar !== undefined) {
            autosend = top.ll_toolbar.getItemState('auto_upload');
        }
        this.uploader.config.autosend = autosend;
        this.uploader.autosend = autosend;
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
            ?>            let limitSize = <?php echo $limitSizeMo; ?>;
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
    var file_list_id = [];
    vaultObject.events.on("UploadComplete", function(files){
        var error = 0;
        files.forEach(function(item){
            let file_response  = JSON.parse(item.request.response);
            file_list_id.push(file_response.id);
            if(file_response.error !== null) {
                vaultObject.data.update(item.id,{status:'failed'});
                dhx.message({
                    text: "code:"+file_response.error.code+" "+file_response.error.message,
                    css: "dhx-error",
                    expire: 10000
                });
                error = error+1;
            }
        });
        if(error === 0) {
            if(top.prop_tb._imagesUploadWindow[window_id] !== undefined) {
                top.prop_tb._imagesUploadWindow[window_id].hide();
            }
            <?php
            if (!$is_attr)
            {
                echo 'top.displayImages();';
            }
            else
            {
                if ($is_combination_multiproduct)
                {
                    echo 'top.getCombinationMultiProductImages();';
                }
                else
                {
                    echo 'top.getCombinationsImages();';
                }
            }
            ?>
            if(top.prop_tb._imagesUploadWindow[window_id] !== undefined) {
                top.prop_tb._imagesUploadWindow[window_id].park();
            }
        }
    });
</script>
</body>
