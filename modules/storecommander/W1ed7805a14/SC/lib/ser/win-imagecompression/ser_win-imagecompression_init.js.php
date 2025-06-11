<?php
    echo '<!doctype html>
<html>
    <head>
        <link type="text/css" rel="stylesheet" href="'.SC_CSSDHTMLX.'"/>
        <link type="text/css" rel="stylesheet" href="'.SC_CSSSTYLE.'"/>
        <link type="text/css" rel="stylesheet" href="lib/css/fontawesome/all.css"/>
        <script type="text/javascript" src="'.SC_JSDHTMLX.'">
        <script type="text/javascript" src="lib/js/message.js">
        <script type="text/javascript" src="'.SC_JQUERY.'">
        <script type="text/javascript" src="'.SC_JSFUNCTIONS.'">
        <script type="text/javascript">
            parent.col_eSP_itemslist.collapse();
            parent.col_eSP_config.progressOff();
        </script>
    </head>
<body>'; ?>
    <?php

    $id_project = (int) Tools::getValue('id_project');
    // checks before scanning
    $errors = array();
    if (!function_exists('exec'))
    {
        $errors[] = _l('Php exec function must be enable. You need to contact your administrator.');
    }
    if (!isTable('storecom_imagefile'))
    {
        $errors[] = _l('Table storecom_imagefile is missing in database. You need to contact your administrator.');
    }
    if (!is_writable(_PS_PROD_IMG_DIR_))
    {
        $errors[] = _l('Folder img/p must have writable permission. You can fix it by FTP.');
    }
    if (!is_writable(_PS_CAT_IMG_DIR_))
    {
        $errors[] = _l('Folder img/c must have writable permission. You can fix it by FTP.');
    }
    if (!is_writable(_PS_IMG_DIR_.'cms/'))
    {
        $errors[] = _l('Folder img/cms must have writable permission. You can fix it by FTP.');
    }
    if (!is_writable(_PS_ALL_THEMES_DIR_))
    {
        $errors[] = _l('Folder themes must have writable permission. You can fix it by FTP.');
    }

    $compression_is_disabled = (int) in_array(SCI::getConfigurationValue('SC_IMAGECOMPRESSION_ACTIVE'), array(0, 3));
    if (!empty($errors))
    {
        $html_errors = '<div style=\"font-family:Arial,sans-serif;font-size:14px;color:#444;padding:5px\">';
        $html_errors .= '   <p><b>'._l('You should modify your server configuration before start using image compression.').'</b></p>';
        $html_errors .= '   <ul>';
        foreach ($errors as $error)
        {
            $html_errors .= '       <li>'.$error.'</li>';
        }
        $html_errors .= '   </ul>';
        $html_errors .= '</div>'; ?>
        <?php echo '<script type="text/javascript">'; ?>
            let imageCompression_layout = new dhtmlXLayoutObject(parent.col_eSP_config, "1C");
            let imageCompression_layout_error = imageCompression_layout.cells('a');
            imageCompression_layout_error.setText("<?php echo _l('Configuration'); ?>");
            imageCompression_layout_error.attachHTMLString("<?php echo $html_errors; ?>");
        <?php echo '</script>'; ?>
    <?php
    }
    else
    { ?>
        <?php echo '<script type="text/javascript">'; ?>
            let imageCompression_layout_global = new dhtmlXLayoutObject(parent.col_eSP_config, "1C");
            let imageCompression_layout_global_cell = imageCompression_layout_global.cells('a');
            imageCompression_layout_global_cell.setText("<?php echo _l('Configuration'); ?>");
            imageCompression_layout_global_cell.showHeader();
            let imageCompression_layout = new dhtmlXLayoutObject(imageCompression_layout_global_cell, "2U");
            let imageCompression_Left_layout = imageCompression_layout.cells('a');
            let imageCompression_Left_content = new dhtmlXLayoutObject(imageCompression_Left_layout, "2E");

            let imageCompression_Right_layout = imageCompression_layout.cells('b');
            let imageCompression_Right_content = new dhtmlXLayoutObject(imageCompression_Right_layout, "2E");


            // Cell  bouton analyse
            let imageCompression_analyse_cell = imageCompression_Left_content.cells('a');
            imageCompression_analyse_cell.hideHeader();
            imageCompression_analyse_cell.setHeight(300);
            let formStructure = [
                {type: "label", offsetLeft: 25, label: "<?php echo _l('Step %s:', null, array('1')); ?>"},
                {
                    width: 300,
                    type: "button",
                    offsetLeft: 25,
                    name: 'start_scan',
                    value: '<?php echo '<i class=\"fad fa-play-circle blue\"></i> '._l('Start file analysis', 1); ?>'
                },
                {type: "label", offsetLeft: 25, label: "<?php echo _l('Step %s:', null, array('2')); ?>"},
                {
                    width: 300,
                    type: "button",
                    offsetLeft: 25,
                    name: 'demo_compress',
                    value: '<?php echo '<i class=\"fad fa-play-circle blue\"></i> '._l('Try compression on 5 images', 1); ?>'
                },
                {type: "label", offsetLeft: 25, label: "<?php echo _l('Step %s:', null, array('3')); ?>"},
                {
                    width: 300,
                    type: "checkbox",
                    position: "label-right",
                    labelWidth: 275,
                    offsetLeft: 25,
                    name: 'confirm_backup',
                    label: "<?php echo _l('I confirm that I have made a backup of img/ and themes/ folders before continuing', 1); ?>",
                    checked:<?php echo SCI::getConfigurationValue('SC_IMAGECOMPRESSION_BACKUP_CONFIRM') ? 'true' : 'false'; ?>
                },
                {
                    width: 42,
                    type: "btn2state",
                    position: "label-right",
                    labelWidth: 275,
                    offsetLeft: 25,
                    name: 'enable_compress',
                    label: "<?php echo _l('Enable image compression', 1); ?>",
                    checked:<?php echo $compression_is_disabled ? 'false' : 'true'; ?>,
                    disabled:<?php echo SCI::getConfigurationValue('SC_IMAGECOMPRESSION_BACKUP_CONFIRM') ? 'false' : 'true'; ?>
                }
            ];
            let imageCompression_analyse_form = imageCompression_analyse_cell.attachForm(formStructure);
            imageCompression_analyse_form.attachEvent("onButtonClick", function (name) {
                switch (name) {
                    case 'start_scan':
                        imageCompression_analyse_cell.progressOn();
                        $.post('index.php?ajax=1&act=ser_win-imagecompression_scan',
                            {
                                start: 1
                            }, function (data) {
                                let res = JSON.parse(data);
                                if (res.error == 1) {
                                    imageCompression_infos_cell.attachURL('index.php?ajax=1&act=ser_win-imagecompression_infos_get', null, {
                                        scan_over: 1,
                                        error: res.detail,
                                        process_duration: res.process_duration
                                    });
                                } else {
                                    displayInfos(1, res.process_duration);
                                }
                                displayStats();
                                imageCompression_analyse_cell.progressOff();
                            });
                        break;
                    case 'demo_compress':
                        launchDemoCompress();
                        break;
                }
            });

            function launchDemoCompress() {
                imageCompression_analyse_cell.progressOn();
                $.post('index.php?ajax=1&act=ser_win-imagecompression_demo',
                    {
                        start: 1
                    }, function (message) {
                        if (message === 'RELOAD') {
                            message = '<?php echo _l('Images are not all ready. Please wait few seconds and click on this link'); ?><a  onclick="parent.launchDemoCompress();" href="#"><?php echo _l('Reload'); ?></a>';
                        }
                        imageCompression_infos_cell.attachURL('index.php?ajax=1&act=ser_win-imagecompression_infos_get', null, {
                            message: message
                        });
                        imageCompression_analyse_cell.progressOff();
                    });
            }

            imageCompression_analyse_form.attachEvent("onChange", function (name) {
                switch (name) {
                    case 'confirm_backup':
                        let confirm_backup_value = imageCompression_analyse_form.getItemValue(name);
                        $.post('index.php?ajax=1&act=ser_win-imagecompression_update',
                            {
                                action: 'backup_confirmation',
                                value: confirm_backup_value
                            }, function (result) {
                                let res = JSON.parse(result);
                                if (res.error === 1) {
                                    imageCompression_infos_cell.attachURL('index.php?ajax=1&act=ser_win-imagecompression_infos_get', null, {
                                        message: res.error_list
                                    });
                                } else {
                                    if (confirm_backup_value) {
                                        imageCompression_analyse_form.enableItem('enable_compress');
                                    } else {
                                        imageCompression_analyse_form.setItemValue('enable_compress', 0);
                                        imageCompression_analyse_form.callEvent('onChange', ['enable_compress']);
                                        imageCompression_analyse_form.disableItem('enable_compress');
                                    }
                                }
                            });
                        break;
                    case 'enable_compress':
                        let enable_compress_value = imageCompression_analyse_form.getItemValue(name);
                        $.post('index.php?ajax=1&act=ser_win-imagecompression_update',
                            {
                                action: 'compression_activation',
                                value: enable_compress_value
                            }, function (result) {
                                let res = JSON.parse(result);
                                if (res.error === 1) {
                                    imageCompression_infos_cell.attachURL('index.php?ajax=1&act=ser_win-imagecompression_infos_get', null, {
                                        message: res.errorlist
                                    });
                                } else if (res.detail !== '' && res.detail !== undefined) {
                                    imageCompression_infos_cell.attachURL('index.php?ajax=1&act=ser_win-imagecompression_infos_get', null, {
                                        message: res.detail
                                    });
                                }
                            });
                        break;
                }
            });

            // Cell Infos supp
            let imageCompression_infos_cell = imageCompression_Left_content.cells('b');
            imageCompression_infos_cell.setWidth(470);
            imageCompression_infos_cell.setText('<?php echo _l('Informations'); ?>');
            displayInfos(null, 0);

            function displayInfos(scan_over = null, duration) {
                imageCompression_infos_cell.attachURL('index.php?ajax=1&act=ser_win-imagecompression_infos_get', null, {
                    scan_over: scan_over,
                    process_duration: duration,
                });
            }

            // Cell Stats
            let imageCompression_stats_cell = imageCompression_Right_content.cells('a');
            imageCompression_stats_cell.setHeight(350);
            imageCompression_stats_cell.setText('<?php echo _l('Stats'); ?>');
            displayStats();

            function displayStats() {
                imageCompression_stats_cell.attachURL('index.php?ajax=1&act=ser_win-imagecompression_stats_get', null, true);
            }

            //Cell Conversion fizz/credits
            let imageCompression_credit_cell = imageCompression_Right_content.cells('b');
            imageCompression_credit_cell.setHeight(300);
            imageCompression_credit_cell.setText('<?php echo _l('Credits'); ?>');
            displayCredit();

            function displayCredit() {
                imageCompression_credit_cell.attachURL('index.php?ajax=1&act=ser_win-imagecompression_credit_get&id_project=<?php echo (int) $id_project; ?>', null, true);
            }
        <?php echo '</script>'; ?>
    <?php } ?>
<?php echo '</body>'; ?>