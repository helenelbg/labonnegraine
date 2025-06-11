<?php

$attr_list = Tools::getValue('id_attribute', null);
$action = Tools::getValue('action', '0');
if (empty($attr_list))
{
    echo '<div style="display: flex;align-items: center;height: 100%;text-align: center;font-family:Arial,sans-serif">'._l('You need to select an attribute value row first').'</div>';
    exit;
}
$id_attribute = explode(',', $attr_list);
$allowed_format_extension = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
if ($action == 'delete')
{
    foreach ($id_attribute as $v)
    {
        foreach ($allowed_format_extension as $extension)
        {
            if (file_exists(_PS_COL_IMG_DIR_.$v.'.'.$extension))
            {
                @unlink(_PS_COL_IMG_DIR_.$v.'.'.$extension);
                break;
            }
        }
    }
}
if ($action == 'duplicate')
{
    $attributes = explode(',', Tools::getValue('attributes', 0));
    $id_group = Tools::getValue('id_group', 0);
    if ($id_group == 0 || $attributes == 0)
    {
        exit;
    }
    foreach ($attributes as $a)
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>='))
        {
            $srcAttr = new ProductAttribute($a);
            $newAttr = new ProductAttribute();
        }
        else
        {
            $srcAttr = new Attribute($a);
            $newAttr = new Attribute();
        }
        $newAttr->id_attribute_group = $id_group;
        foreach ($languages as $lang)
        {
            $newAttr->name[$lang['id_lang']] = ($srcAttr->name[$lang['id_lang']] != '' ? $srcAttr->name[$lang['id_lang']] : ' ');
        }
        $newAttr->color = $srcAttr->color;
        $newAttr->add();
        if (file_exists(_PS_COL_IMG_DIR_.$a.'.jpg'))
        {
            @copy(_PS_COL_IMG_DIR_.$a.'.jpg', _PS_COL_IMG_DIR_.$newAttr->id.'.jpg');
        }
    }
}
if ($action == 'add')
{
    $id_attribute = $id_attribute[0]; ?>
    <style type="text/css">@import url(<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.css);</style>
    <script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
    <script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
    <script type="text/javascript" src="<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.js"></script>
    <body style="margin:0;">
    <div id="file_uploader"></div>
    <script>
        <?php require_once SC_PLUPLOAD.'js/vault/vault_lang.php'; ?>
        let authorized_extensions = <?php echo json_encode($allowed_format_extension); ?>;
        <?php
        if (version_compare(_PS_VERSION_, '8.0', '>='))
        {?>
        authorized_extensions.push('webp');
        <?php } ?>
        let vaultObject = new dhx.Vault("file_uploader", {
            uploader: {
                target: 'index.php?ajax=1&act=all_upload&obj=attrtexture&from_vault=1&id_attribute=<?php echo $id_attribute; ?>',
                autosend: true,
                singleRequest:true
            },
            mode: "grid",

        });
        vaultObject.events.on("BeforeAdd", function (item) {
            let extension = item.file.name.split('.').pop();
            if (authorized_extensions.includes(extension.toLowerCase())) {
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
    } ?>
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
                    text: "<?php echo _l('Wrong file format', 1); ?> (" + authorized_extensions.join(',') + " <?php echo _l('only', 1); ?>)",
                    css: "dhx-error",
                    expire: 4000
                });
                return false;
            }
        });
        vaultObject.events.on("UploadComplete", function (files) {
            var error = 0;
            files.forEach(function (item) {
                let file_response = JSON.parse(item.request.response);
                if (file_response.error !== null) {
                    dhx.message({
                        text: "code:" + file_response.error.code + " " + file_response.error.message,
                        css: "dhx-error",
                        expire: 4000
                    });
                    error = error + 1;
                }
            });
            if (error === 0) {
                top.displayAttributes();
            }
        });
    </script>
    </body>
    <?php
}
