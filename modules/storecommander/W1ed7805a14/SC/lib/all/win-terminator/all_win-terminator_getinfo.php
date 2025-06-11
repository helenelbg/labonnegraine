<?php

$id_lang = (int) Tools::getValue('id_lang');
$action = (Tools::getValue('action', ''));
if (!empty($action))
{
    require dirname(__FILE__).'/tools.php';
    $terminatorTools = new terminatorTools();
    require dirname(__FILE__).'/actions.php';

    if (!empty($actions[$action]['info']))
    {
        ?>
        <div style=" margin: 10px; padding: 10px; background-color: #D0E1EC;border: 4px solid #3D88BA; color: #2E2A25; margin-bottom: 0px;font-family:Arial,sans-serif">
            <img src="lib/img/information_big.png" style="float:left; margin-right: 20px; margin-bottom: 10px;" />
            <h3 style="margin-top: 0px;"><?php echo _l('Action information'); ?></h3>
            <?php echo $actions[$action]['info']; ?>
            <div style="clear: both;"></div>
        </div>
        <?php
    }
}
