<?php
$field_to_update = 'description';

$interface = 'sup';
$property_id = str_replace($interface.'_', '', basename(__FILE__, '_tinymce.php'));
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link type="text/css" rel="stylesheet" href="<?php echo SC_CSSSTYLE; ?>" />
<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<script type="text/javascript" src="lib/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="lib/js/tiny_mce/jquery.tinymce.js"></script>
<style>
    html{
        overflow: hidden;
    }
    html,
    body,
    #form_<?php echo $property_id; ?>,
    #tinycewrapper,
    .mceEditor,
    .mceLayout{
        width: 100%;
        height: 100%!important;
        margin: 0;
        box-sizing: content-box;
    }
</style>
</head>
<body>
<?php
$iso = UISettings::getSetting('forceSCLangIso');
if (empty($iso))
{
    $iso = Language::getIsoById((int) ($sc_agent->id_lang));
}
?>
<script type="text/javascript">
let iso = '<?php echo file_exists('lib/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en'; ?>';
let pathCSS = '<?php echo _THEME_CSS_DIR_; ?>';
let pathTiny = 'lib/js/tiny_mce/tiny_mce.js';
let add = 'lib/js/';
$(document).ready(function() {
<?php
    if (file_exists(SC_TOOLS_DIR.$interface.'_'.$property_id.'/tiny_config.php'))
    {
        require_once SC_TOOLS_DIR.$interface.'_'.$property_id.'/tiny_config.php';
    }
    else
    {
        require_once SC_DIR.'lib/'.$interface.'/'.$property_id.'/tiny_config.php';
    }
?>
});

var tMCE1=0;
var tMCE2=0;
var tMCE1Content=0;
var tMCE2Content=0;

function ajaxLoad(args,id_supplier,id_lang) {
    if (tMCE2==0) tMCE2 = $('#base_field_from_db').tinymce();
    $('#id_supplier').val(id_supplier);
    $('#id_lang').val(id_lang);
    tMCE2.setProgressState(1);
    $.get("index.php?ajax=1&act=<?php echo $interface; ?>_<?php echo $property_id; ?>_get&content=<?php echo $field_to_update; ?>"+args, function(data){
        tMCE2.setProgressState(0);
        tMCE2.setContent(data);
        tMCE2Content=data;
        tMCE2.isNotDirty=1; // change modified state of tinyMCE
        });
}
function ajaxSave() {
    if (tMCE2==0) tMCE2 = $('#base_field_from_db').tinymce();
    tMCE2.setProgressState(1);
    $.post("index.php", $("#form_<?php echo $property_id; ?>").serialize(), function(data){
            tMCE2.setProgressState(0);
            if (data==='OK')
            {
                tMCE2.isNotDirty=1;
            }else{
                if (data=='ERR|<?php echo $field_to_update; ?>_short_size')
                {
                    alert('<?php echo _l('Short description size must be < ', 1)._s('CAT_SHORT_DESC_SIZE'); ?>');
                }
                <?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
                if (data=='ERR|<?php echo $field_to_update; ?>_with_iframe')
                {
                    alert('<?php echo _l('Description can\'t include an iframe or is invalid', 1); ?>');
                }
                if (data=='ERR|<?php echo $field_to_update; ?>_invalid')
                {
                    alert('<?php echo _l('Description is invalid', 1); ?>');
                }
                <?php } ?>
            }
        });
}
function checkChange() {
    if (tMCE2==0) tMCE2 = $('#base_field_from_db').tinymce();
    if (tMCE2.isDirty()) {
       if (confirm('<?php echo _l('Do you want to save the descriptions?', 1); ?>')) {
           ajaxSave();
       }
    }
}
</script>
    <form id="form_<?php echo $property_id; ?>" method="POST">
        <input name="ajax" type="hidden" value="1"/>
        <input name="act" type="hidden" value="<?php echo $interface; ?>_<?php echo $property_id; ?>_update"/>
        <input id="id_supplier" name="id_supplier" type="hidden" value="<?php echo $id_supplier; ?>"/>
        <input id="id_lang" name="id_lang" type="hidden" value="<?php echo $id_lang; ?>"/>
        <div id="tinycewrapper">
            <textarea id="base_field_from_db" name="base_field_from_db" class="tinymce1 rte" cols="50" rows="30" style=""><?php echo $field_from_db; ?></textarea>
        </div>
    </form>
</body>
</html>