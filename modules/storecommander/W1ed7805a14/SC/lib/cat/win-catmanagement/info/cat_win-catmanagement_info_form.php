<?php
$id_row = (string) Tools::getValue('id_row', '');
$field = Tools::getValue('field', null);
$allowed_fields = array('description', 'additional_description');

if (!in_array($field, $allowed_fields))
{
    exit;
}

$id_shop = null;
if (SCMS)
{
    list($id_category, $id_lang, $id_shop) = explode('_', $id_row);
}
else
{
    list($id_category, $id_lang) = explode('_', $id_row);
}

$error = '';
$success = false;
if (isset($_POST['submitUpdate']) && isset($_POST['field']))
{
    $is_submit_update = (int) Tools::getValue('submitUpdate');
    $field_to_update = (string) Tools::getValue('field');
    if (in_array($field_to_update, $allowed_fields))
    {
        $description = (string) Tools::getValue($field_to_update);
        $_POST = null;
        if ($is_submit_update)
        {
            $sql = 'UPDATE '._DB_PREFIX_.'category_lang
                SET `'.bqSQL($field_to_update)."`='".pSQL($description, 1)."'
                WHERE id_category=".(int) $id_category.'
                AND id_lang='.(int) $id_lang;
            if (SCMS)
            {
                $sql .= ' AND id_shop='.(int) $id_shop;
            }
            Db::getInstance()->Execute($sql);
            $success = true;
        }
    }
}

$sql = 'SELECT `'.bqSQL($field).'`
        FROM '._DB_PREFIX_.'category_lang
        WHERE id_category='.(int) $id_category.'
        AND id_lang='.(int) $id_lang;
if (SCMS)
{
    $sql .= ' AND id_shop='.(int) $id_shop;
}
$description_val = Db::getInstance()->getValue($sql);
?>
    <link type="text/css" rel="stylesheet" href="<?php echo SC_CSSSTYLE; ?>" />
    <script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
    <script type="text/javascript">
        <?php echo 'var pathCSS = \''._THEME_CSS_DIR_.'\' ;'; ?>
    </script>
    <?php if (_s('APP_RICH_EDITOR') == 1) { ?>
        <script type="text/javascript" src="lib/js/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript" src="lib/js/tiny_mce/jquery.tinymce.js"></script>
        <?php
                $iso = Language::getIsoById((int) ($sc_agent->id_lang));
                echo '
        <script type="text/javascript">
        var iso = \''.(file_exists('lib/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
        var pathTiny = \'lib/js/tiny_mce/tiny_mce.js\' ;
        var add = \'lib/js/\' ;
        </script>';
        ?>
        <script type="text/javascript">
        $().ready(function() {
            $('textarea#<?php echo $field; ?>').tinymce({
                script_url : 'lib/js/tiny_mce/tiny_mce.js',
                mode : "specific_textareas",
                theme : "advanced",
                skin:"default",
                editor_selector : "rte",
                editor_deselector : "noEditor",
                plugins : "spellchecker,safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview",
                theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
                theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,pagebreak,|,fullscreen,|,spellchecker",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : true,
                theme_advanced_source_editor_width : 580,
                extended_valid_elements : "iframe[src|width|height|name|align]",
            <?php echo _s('CAT_PROPERTIES_DESCRIPTION_CSS') ? 'content_css : pathCSS+'.(version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? '"theme.css"' : '"global.css"').',' : ''; ?>
                width: "100%",
                height: "150px",
                font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
                elements : "nourlconvert",
                entity_encoding: "raw",
                convert_urls : false,
                language : iso
            });
        });
        </script>
    <?php }
        else
        {
            $iso = UISettings::getSetting('forceSCLangIso');
            if (empty($iso))
            {
                $iso = Language::getIsoById((int) ($sc_agent->id_lang));
            }
            $sql = 'SELECT '.(version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? 'locale' : 'language_code').'
                FROM '._DB_PREFIX_.'lang
                WHERE iso_code = "'.pSQL($iso).'"';
            $lang_iso = Db::getInstance()->getValue($sql);
            list($min, $maj) = explode('-', $lang_iso);
            $lang_iso = strtolower($min).'_'.strtoupper($maj); ?>
        <script src="lib/js/ckeditor/ckeditor.js?<?php echo rand(); ?>"></script>
        <script type="text/javascript">
        <?php echo 'var langIso = "'.$lang_iso.'" ;'; ?>

        var activeSCAYT = <?php echo _s('APP_CKEDITOR_AUTOCORRECT_ACTIVE') == '1' ? 'true' : 'false'; ?>;
        CKEDITOR.config.customConfig="<?php echo SC_CKEDITOR_CONFIG; ?>";
        CKEDITOR.config.language = '<?php echo $user_lang_iso; ?>';
        CKEDITOR.config.scayt_sLang = langIso;
        <?php if (!_s('APP_CKEDITOR_CODESNIPPET_ACTIVE')) { ?>CKEDITOR.config.removePlugins = 'codesnippet';<?php } ?>
        $(document).ready(function(){
            CKEDITOR.replace('<?php echo $field; ?>');
            <?php if (_s('CAT_PROPERTIES_DESCRIPTION_CSS')) { ?>CKEDITOR.config.contentsCss = pathCSS+"<?php echo version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? 'theme.css' : 'global.css'; ?>";<?php } ?>
        });
        </script>

    <?php
        } ?>
<script type="text/javascript">
    function ajaxSave() {
        $('#submitUpdate').submit();
    }
    <?php if ($success) { ?>
    parent.getCatManagementPropInfo();
    <?php if (version_compare(_PS_VERSION_, '8.0.0', '<')) { ?>
    parent.cat_prop_info.cells('b').collapse();
    <?php } ?>
    <?php } ?>
</script>

<form method="POST" action="" id="submitUpdate">
    <textarea name="<?php echo $field; ?>" id="<?php echo $field; ?>"><?php echo $description_val; ?></textarea>
    <input type="hidden" name="field" value="<?php echo $field; ?>"/>
    <input type="hidden" name="id_row" value="<?php echo $id_row; ?>"/>
    <input type="hidden" name="submitUpdate" value="1"/>
</form>
