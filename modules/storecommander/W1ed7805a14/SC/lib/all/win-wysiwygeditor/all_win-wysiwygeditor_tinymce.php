<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
    <script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo SC_CSSSTYLE; ?>"/>
    <script type="text/javascript" src="lib/js/tiny_mce/tiny_mce.js"></script>
    <script type="text/javascript" src="lib/js/tiny_mce/jquery.tinymce.js"></script>
</head>
<body style="padding:0px;margin:0px;">
<?php
$iso = UISettings::getSetting('forceSCLangIso');
if (empty($iso))
{
    $iso = Language::getIsoById((int) ($sc_agent->id_lang));
}
echo '
        <script type="text/javascript">
        var iso = \''.(file_exists('lib/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
        var pathCSS = \''._THEME_CSS_DIR_.'\' ;
        var pathTiny = \'lib/js/tiny_mce/tiny_mce.js\' ;
        var add = \'lib/js/\' ;
        </script>';
?>
<script type="text/javascript">
    let current_rId = Number(<?php echo Tools::getValue('rId'); ?>);
    let current_cInd = Number(<?php echo Tools::getValue('cInd'); ?>);
    let current_cell = parent.last_selected_grid.cells(current_rId, current_cInd);
    let current_grid = parent.last_selected_grid;
    var sourceCodeContent = '';

    function getContentSourceCode() {
        return sourceCodeContent;
    }

    $(document).ready(function () {
        parent.wWysiwyg_layout_editor.progressOff();
        let tinyMce_instance = '#data_content_html';
        $(tinyMce_instance).tinymce({
            script_url: 'lib/js/tiny_mce/tiny_mce.js',
            mode: "specific_textareas",
            theme: "advanced",
            skin: "default",
            editor_selector: "rte",
            editor_deselector: "noEditor",
            plugins: "spellchecker,safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview",
            theme_advanced_buttons1: "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2: "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
            theme_advanced_buttons3: "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,pagebreak,|,fullscreen,|,spellchecker",
            theme_advanced_toolbar_location: "top",
            theme_advanced_toolbar_align: "left",
            theme_advanced_statusbar_location: "bottom",
            theme_advanced_resizing: true,
            theme_advanced_source_editor_width: 580,
            extended_valid_elements: "iframe[src|width|height|name|align]",
            <?php echo _s('CAT_PROPERTIES_DESCRIPTION_CSS') ? 'content_css : pathCSS+'.(version_compare(_PS_VERSION_, '1.7.0.0', '>=') ? '"theme.css"' : '"global.css"').',' : ''; ?>
            width: "100%",
            height: "100%",
            font_size_style_values: "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
            elements: "nourlconvert",
            entity_encoding: "raw",
            convert_urls: false,
            language: iso,
            onchange_callback: function (inst) {
                sourceCodeContent = inst.getBody().innerHTML;
            }
        });
        $(tinyMce_instance).val(current_cell.getValue());
    });
</script>
<textarea id="data_content_html" style="resize: none;"></textarea>
</body>
</html>