<?php
$field_to_update = 'description';

$interface = 'sup';
$property_id = str_replace($interface.'_', '', basename(__FILE__, '_ckeditor.php'));
$id_lang = (int) Tools::getValue('id_lang', Configuration::get('PS_LANG_DEFAULT'));
$id_supplier = (int) Tools::getValue('id_supplier', 0);
$field_from_db = '';
$property_conf = $property_id.'_conf';

if ($id_supplier !== 0)
{
    $sql = 'SELECT `'.bqSQL($field_to_update).'` as field_from_db 
                FROM '._DB_PREFIX_.'supplier_lang 
                WHERE id_supplier='.(int) $id_supplier.' 
                AND id_lang='.(int) $id_lang;
    $field_from_db = Db::getInstance()->getValue($sql);
}

$iso = Language::getIsoById((int) $id_lang);

if (empty($iso))
{
    $iso = UISettings::getSetting('forceSCLangIso');
}
if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
{
    $sql = 'SELECT locale FROM '._DB_PREFIX_.'lang WHERE iso_code = "'.pSQL($iso).'"';
}
else
{
    $sql = 'SELECT language_code FROM '._DB_PREFIX_.'lang WHERE iso_code = "'.pSQL($iso).'"';
}
$lang_iso = Db::getInstance()->getValue($sql);
list($min, $maj) = explode('-', $lang_iso);
if (!empty($maj))
{
    $lang_iso = strtolower($min).'_'.strtoupper($maj);
}
else
{
    $lang_iso = strtolower($min).'_'.strtoupper($min);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
    <script src="lib/js/ckeditor/ckeditor.js?<?php echo rand(); ?>"></script>
</head>
<body style="padding:0px;margin:0px;">
<script type="text/javascript">
    let pathCSS = '<?php echo _THEME_CSS_DIR_; ?>';
    let fileCSS = '<?php echo version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? 'theme' : 'global'; ?>.css';
    let activeSCAYT = <?php echo _s('APP_CKEDITOR_AUTOCORRECT_ACTIVE') == '1' ? 'true' : 'false'; ?>;

    CKEDITOR.config.customConfig = "<?php echo SC_CKEDITOR_CONFIG; ?>";
    <?php if (_s('CAT_PROPERTIES_DESCRIPTION_CSS')) { ?>CKEDITOR.config.contentsCss = pathCSS + fileCSS;<?php } ?>
    <?php if (!_s('APP_CKEDITOR_CODESNIPPET_ACTIVE')) { ?>CKEDITOR.config.removePlugins = 'codesnippet';<?php } ?>
    CKEDITOR.config.language = '<?php echo $user_lang_iso; ?>';
    CKEDITOR.config.scayt_sLang = '<?php echo $lang_iso; ?>';
    CKEDITOR.config.toolbarCanCollapse = true;
    let OriginalFunction = CKEDITOR.tools.callFunction;

    CKEDITOR.tools.callFunction = function (n, x = null, y = null) {
        // liste des boutons de la toolbar. les ids entre short desc et desc ne sont pas les mêmes
        let need_to_save = [34, 37, 40, 43, 46, 49, 52, 58, 61, 64, 67, 55, 52, 68, 71, 74, 77, 82, 85, 127, 130, 133, 136, 139, 142, 145, 148, 151, 154, 157, 160, 161, 164, 167, 190, 170, 175, 193, 178];
        if (need_to_save.indexOf(n) !== -1) {
            parent.<?php echo $property_conf; ?>.not_save = 1;
        }
        OriginalFunction(n, x, y);
    }

    var tCKE1 = 0;
    var tCKE1Content = 0;

    function ajaxLoad(args, id_supplier, id_lang) {
        $('#id_supplier').val(id_supplier);
        $('#id_lang').val(id_lang);
        $.get("index.php?ajax=1&act=<?php echo $interface; ?>_<?php echo $property_id; ?>_get&content=<?php echo $field_to_update; ?>" + args, function (data) {
            tCKE1.setData(data);
            tCKE1Content = data;
            tCKE1.resetDirty();
            setTimeout(function () {
                putInBase()
            }, 500);
            parent.prop_tb._Layout_<?php echo $property_id; ?>.cells('a').progressOff();
        });
    }

    function ajaxSave() {
        $("#form_<?php echo $property_id; ?> textarea#<?php echo $field_to_update; ?>").val(tCKE1.getData());
        $.post("index.php", $("#form_<?php echo $property_id; ?>").serialize(), function (data) {
            parent.prop_tb._Layout_<?php echo $property_id; ?>.cells("a").progressOff();
            if (data === 'OK') {
                tCKE1.resetDirty();
                setTimeout(function () {
                    putInBase()
                }, 500);
            } else {
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                if (data === 'ERR|<?php echo $field_to_update; ?>_with_iframe') {
                    alert('<?php echo _l('Short description can\'t include an iframe or is invalid', 1); ?>');
                }
                if (data === 'ERR|<?php echo $field_to_update; ?>_invalid') {
                    alert('<?php echo _l('Short description is invalid', 1); ?>');
                }
                <?php } ?>
            }
        });
    }

    function checkChange() {
        <?php if (_s('CAT_NOTICE_SAVE_DESCRIPTION')) { ?>
        if (parent.<?php echo $property_conf; ?>.not_save === 1)
        {
            if (confirm('<?php echo _l('Do you want to save the descriptions?', 1); ?>')) {
                ajaxSave();
            }
            parent.<?php echo $property_conf; ?>.not_save = 0;
        }
        <?php } ?>
    }

    $(document).ready(function () {
        tCKE1 = CKEDITOR.replace('<?php echo $field_to_update; ?>', {
            on:
                {
                    'instanceReady': function (evt) {
                        evt.editor.execCommand('maximize');
                    }
                }
        });
        tCKE1.on('key', function () {
            parent. <?php echo $property_conf; ?>.not_save = 1;
        });
        setTimeout(function () {
            putInBase()
        }, 500);
    });

    function showShortDesc() {
        $("#container_field_from_db").show();
    }

    function hideShortDesc() {
        $("#container_field_from_db").hide();
    }

    function putInBase() {
        $("#base_field_from_db").val(tCKE1.getData());
    }
</script>
<form id="form_<?php echo $property_id; ?>" method="POST">
    <input name="ajax" type="hidden" value="1"/>
    <input name="act" type="hidden" value="<?php echo $interface; ?>_<?php echo $property_id; ?>_update"/>
    <input id="id_supplier" name="id_supplier" type="hidden" value="<?php echo $id_supplier; ?>"/>
    <input id="id_lang" name="id_lang" type="hidden" value="<?php echo $id_lang; ?>"/>
    <div id="container_field_from_db">
        <textarea id="<?php echo $field_to_update; ?>" name="<?php echo $field_to_update; ?>" rows="10" style="width: 100%; height: 100%;"><?php echo $field_from_db; ?></textarea>
    </div>
    <textarea id="base_field_from_db" rows="10" style="display:none;"><?php echo $field_from_db; ?></textarea>
</form>
</body>
</html>
