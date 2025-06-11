<?php

// HTTP headers for no cache etc

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Settings
$obj = Tools::getValue('obj', '');
switch ($obj) {
case 'mail_attachment':
    $targetDir = SC_MAIL_ATTACHMENT_DIR;
break;
default:
    exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 100, "message": "No action for obj '.$obj.'."}, "id" : "id"}');
}
// 5 minutes execution time
@set_time_limit(5 * 60);

// Get parameters
if ($obj == 'mail_attachment')
{
    $formId = Tools::getValue('formId', '');
    if (!is_dir($targetDir))
    {
        throw new RuntimeException(sprintf('Directory "%s" doesnt exists', $targetDir));
    }
    header('Content-Type: text/json');
    try
    {
        $mask = $targetDir.$formId.DIRECTORY_SEPARATOR.'*.*';
        array_map('unlink', glob($mask));
        rmdir($targetDir.$formId);
    }
    catch (Exception $e)
    {
        exit(json_encode(array(
            'state' => false,
            'extra' => array(
                'code' => 103,
                'message' => $e->getMessage(),
            ),
            'id' => 'id',
        )));
    }
    exit(json_encode(array(
        'state' => true,
    )));
}
