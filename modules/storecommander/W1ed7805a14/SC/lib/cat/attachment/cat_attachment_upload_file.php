<?php
if (!empty($_FILES))
{
    $id_attachment = (array_key_exists('id_attachment', $_REQUEST) ? (int) $_REQUEST['id_attachment'] : null);
    $id_lang = (array_key_exists('id_lang', $_REQUEST) ? (int) $_REQUEST['id_lang'] : Configuration::get('PS_LANG_DEFAULT'));
    $action = (array_key_exists('action', $_REQUEST) ? (string) $_REQUEST['action'] : null);
    if (!empty($id_attachment) && !empty($action))
    {
        switch ($action) {
            case 'edit_file':
                $filename = basename($_FILES['file']['name']);
                $mime = basename($_FILES['file']['type']);
                if (move_uploaded_file($_FILES['file']['tmp_name'], _PS_DOWNLOAD_DIR_.$filename))
                {
                    $attachment = new Attachment((int) $id_attachment);
                    $attachment->file = (string) $filename;
                    $attachment->file_name = (string) $filename;
                    $attachment->mime = (string) $_FILES['file']['type'];
                    $attachment->save();
                }
                else
                {
                    exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 108, "message": "An error occurred during file upload. Please try again."}, "id" : "id"}');
                }
                break;
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
    $id_attachment = (int) Tools::getValue('ids', 0);
    $id_lang = (int) Tools::getValue('id_lang', Configuration::get('PS_LANG_DEFAULT'));
    if (!empty($id_attachment))
    {
        ?>
        <body style="margin:0;">
        <div id="file_uploader"></div>
        <script type="text/javascript">
            <?php require_once SC_PLUPLOAD.'js/vault/vault_lang.php'; ?>
            let vaultObject = new dhx.Vault("file_uploader", {
                uploader:{
                    target: 'index.php?ajax=1&act=cat_attachment_upload_file&id_attachment=<?php echo (int) $id_attachment; ?>&id_lang=<?php echo (int) $id_lang; ?>&action=edit_file',
                    autosend:false
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
                    let filename_length = item.file.name.length;
                    <?php $attachment_filename_length = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && array_key_exists('size', Attachment::$definition['fields']['name']) ? (int) Attachment::$definition['fields']['name']['size'] : 32); ?>
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
                    parent.displayAttachments();
                    parent.prop_tb._attachmentsLayout.cells('b').collapse();
                }
            });
            vaultObject.events.on("UploadFail", function(file){
                dhx.message({
                    text: "<?php echo _l('Error', 1); ?>",
                    css: "dhx-error",
                    expire: 4000
                });
            });
        </script>
        </body>
    <?php
    }
}
