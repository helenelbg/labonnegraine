<?php
$error_detail = Tools::getValue('error', null);
$message = Tools::getValue('message', null);
$scan_over = Tools::getValue('scan_over', null);
$process_duration = Tools::getValue('process_duration', 0);
$compression_disable_too_many_errors = (SCI::getConfigurationValue('SC_IMAGECOMPRESSION_ACTIVE') == 3 ? true : false);
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        #infos_get_content {
            font-family: Arial,sans-serif;
            font-size: 14px;
            color: #444;
            padding: 5px
        }
        #infos_get_content h2 {
            margin: 0
        }
        #infos_get_content a {
            text-align: center;
            display: block;
            width: 100%;
            padding: 10px 0;
        }
        #msg_warning{
            border: 1px solid #f72525;
            padding: 0 10px;
            margin-bottom: 10px;
            background: #f3ecec;
        }
    </style>
</head>
<body>
    <div id="infos_get_content">
    <?php if ($compression_disable_too_many_errors) { ?>
        <div id="msg_warning">
            <p><?php echo _l('It seems that there were many errors when compressing your images.'); ?></p>
            <p><?php echo _l('Please check the write permissions on the following folders:'); ?><br/><strong>img/p, img/c, img/cms</strong></p>
            <p><?php echo _l('If this is not the origin of the problem, please contact us with access to your database so that we can investigate.'); ?></p>
        </div>
    <?php } ?>
    <?php if (!empty($message)) { ?>
        <?php echo $message; ?>
    <?php }
else
{ ?>
        <?php if ($scan_over == 1) { ?>
            <p><?php echo _l('Analysis is over.').' '._l('Running time:').' '.round($process_duration, 2)._l('seconds'); ?></p>
            <?php
            if (!empty($error_detail))
            {
                echo '<br/><p><b>'._l('We have encountered some issues. Please check them or contact our support before start a new analysis.').'</p>';
                echo '<ul>'.$error_detail.'</li>';
            } ?>
        <?php }
            else
            { ?>
            <h2><?php echo _l('Objective of this tool:'); ?></h2>
            <p><?php echo _l('Store Commander allows you to compress the size of your shop\'s image files as much as possible in order to improve the display speed of your product pages for your visitors, boost your SEO and reduce your carbon impact.'); ?></p>
            <p><a href="<?php echo getScExternalLink('support_image_compression'); ?>" target="_blank"><?php echo _l('All information about image compression'); ?></a></p>
            <p><?php echo _l('3 step process:'); ?><br/>
            <ul>
                <li><?php echo '<strong>'._l('Step %s:', null, array('1')).'</strong><br/>'._l('Analysis of your image files: statistics inform you about the potential gains according to the size of your catalogue. No file in your shop is modified in this step. The analysis may take a few minutes.'); ?></li>
                <li><?php echo '<strong>'._l('Step %s:', null, array('2')).'</strong><br/>'._l('Display of the result of the compression of 5 test image files. No file from your shop is modified in this step.'); ?></i></li>
                <li><?php echo '<strong>'._l('Step %s:', null, array('3')).'</strong><br/>'._l('Switching to production of the compression of the image files: a part of your files is compressed each day.'); ?></li>
            </ul>
            </p>
            <p><?php echo _l('Every Tuesday morning, a new analysis of your image files will be launched automatically to update the list of files to be compressed.'); ?></p>
        <?php } ?>
    <?php } ?>
    </div>
</body>
</html>