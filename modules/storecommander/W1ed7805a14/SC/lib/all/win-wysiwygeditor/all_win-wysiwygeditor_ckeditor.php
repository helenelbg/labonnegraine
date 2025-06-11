<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
<script src="lib/js/ckeditor/ckeditor.js?<?php echo rand(); ?>"></script>
</head>
<body style="padding:0px;margin:0px;">
<script type="text/javascript">
    let current_rId = '<?php echo Tools::getValue('rId'); ?>';
    let current_cInd = Number(<?php echo Tools::getValue('cInd'); ?>);
    let current_cell = parent.last_selected_grid.cells(current_rId, current_cInd);
    let current_grid = parent.last_selected_grid;
    let SC_ID_LANG =<?php echo $sc_agent->id_lang; ?>;
    <?php echo 'var pathCSS = \''._THEME_CSS_DIR_.'\' ;'; ?>
    <?php
    if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
    {
        echo 'var fileCSS = "theme.css" ;';
    }
    else
    {
        echo 'var fileCSS = "global.css" ;';
    }
    ?>
    let activeSCAYT = false;
    CKEDITOR.config.language = '<?php echo $user_lang_iso; ?>';
    CKEDITOR.config.customConfig="<?php echo SC_CKEDITOR_CONFIG; ?>";
    <?php if (_s('CAT_PROPERTIES_DESCRIPTION_CSS')) { ?>CKEDITOR.config.contentsCss = pathCSS+fileCSS ;<?php } ?>
    <?php if (!_s('APP_CKEDITOR_CODESNIPPET_ACTIVE')) { ?>CKEDITOR.config.removePlugins = 'codesnippet';<?php } ?>
    CKEDITOR.config.resize_enabled = false;
    var sourceCodeContent = '';

    function getContentSourceCode() {
        return sourceCodeContent;
    }

    $(document).ready(function(){
        let ckEditor_instance = 'data_content_html';
        let wCKeditor = CKEDITOR.replace(ckEditor_instance, {
            on: {
                'instanceReady': function (evt) {
                    evt.editor.execCommand('maximize');
                    parent.wWysiwyg_layout_editor.progressOff();
                }
            }
        });
        wCKeditor.setData(current_cell.getValue());

        CKEDITOR.instances[ckEditor_instance].on('change', function () {
            sourceCodeContent = CKEDITOR.instances[ckEditor_instance].getData();
        });
    });
</script>
<textarea id="data_content_html" style="resize: none;"></textarea>
</body>
</html>