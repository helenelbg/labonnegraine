<?php

$ids = Tools::getValue('ids', null);
$type = Tools::getValue('type', 'products');
if (!empty($ids))
{
    include SC_DIR.'lib/php/qrcode.php';
    $crypt = substr(hash('sha256', SCI::getConfigurationValue('SC_UNIQUE_ID'), false), 0, 20);
    $access_details = access_details();
    if (strpos('http://', $access_details['domain']) === false)
    {
        $access_details['domain'] = 'http://'.$access_details['domain'];
    }
    $domain = str_replace('http://', 'https://', $access_details['domain'].__PS_BASE_URI__);
    $params = array(
        $crypt,
        $type,
        $ids,
        $sc_agent->id_lang,
        SCI::getSelectedShop(),
    );
    $data = $domain.'modules/'.SC_MODULE_FOLDER_NAME.'/ork/qrcode/qrcode_init.php?data='.base64_encode(implode('|||', $params));
    $options = array(
        'h' => '200',
        'w' => '200',
    );
    $generator = new QRCode($data, $options);
    /* Output directly to standard output. */
    $generator->output_image();
}
else
{
    switch ($type) {
        case 'attributes':
            echo '<div style="display: flex;align-items: center;height: 100%;text-align: center;font-family:Arial,sans-serif">'._l('You need to select an attribute value row first').'</div>';
            break;
        default:
            echo _l('You need to select a product before');
    }
    exit;
}
