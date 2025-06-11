<?php
$post_action = Tools::getValue('action');
$current_control_filename = basename(__FILE__, '.php');
$cell_id = 'table_'.$current_control_filename;
if (!empty($post_action) && $post_action == 'do_check')
{
    $img_compression_enable = (int) SCI::getConfigurationValue('SC_IMAGECOMPRESSION_ACTIVE');

    $content = '';
    $results = 'OK';
    if (empty($img_compression_enable))
    {
        $results = 'KO';
        ob_start(); ?>
        <div style="padding: 20px;height: 100%;overflow: auto;">
            <img src="./lib/img/compression_icon.jpg" alt="" style="float: left; max-width: 100px;margin-right: 20px; margin-bottom: 20px;"/>
            <div>
                <p><strong><?php echo _l('Image compression is not enabled. You have two solutions :'); ?></strong></p>
                <ul style="display:block;overflow:hidden;list-style:none inside none;float:none;width:100%;padding:0;text-align:center;font-family:arial,Sans-Serif;">
                    <li>
                        <button class="btn_red" onClick="<?php echo $current_control_filename; ?>_do()" style="width:100%;cursor: pointer">
                            1.<?php echo _l('Open the eservice projects window to enable the image compression'); ?>
                        </button><br/>
                        <a href="<?php echo getScExternalLink('support_image_compression'); ?>" target="_blank" style="color:#39C;font-size:12px;"><?php echo _l('Read all about image compression'); ?></a>
                    </li>
                    <li>
                        <button class="btn_red" onClick="openNativeBOImageSettings()" style="width:100%;margin-top:20px;cursor: pointer">
                        2.<?php echo _l('Enable the use of webp image format in prestashop image settings'); ?>
                        </button>
                    </li>
                </ul>
            </div>
            <div style="clear: both"></div>
            <div style="margin-top: 30px; text-align: center;">

            </div>
        </div>
        <script>
            function <?php echo $current_control_filename; ?>_do() {
               loadWindoweServicesProject('image_compression');
            }
            function openNativeBOImageSettings() {
                wModifyImageSettings = dhxWins.createWindow("wModifyOrder", 50, 50, 1000, $(window).height()-75);
                wModifyImageSettings.setText('<?php echo _l('Image settings', 1); ?>');
                wModifyImageSettings.attachURL("<?php echo SC_PS_PATH_ADMIN_REL; ?>index.php?<?php echo version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'controller=AdminImages' : 'tab=AdminImages'; ?>&token=<?php echo $sc_agent->getPSToken('AdminImages'); ?>#conf_id_PS_IMAGE_QUALITY");
            }
        </script>
        <?php
        $content = ob_get_clean();
    }
    echo json_encode(array(
        'results' => $results,
        'contentType' => 'content',
        'content' => $content,
        'title' => _l('Image comp.'),
    ));
}
