<?php
if (!defined('STORE_COMMANDER')) { exit; }

if (!defined('SC_ExportCustomers_ACTIVE') || (int) SC_ExportCustomers_ACTIVE !== 1)
{
    exit;
}

$response = array(
    'state' => 'error',
    'message' => '',
);

$action = Tools::getValue('action');
$allowedActions = array(
    'delete',
);

if(empty($action)
    || !in_array($action,$allowedActions)
    || !Tools::isSubmit('selection'))
{
    $response['message'] = 'invalid params';
    exit(json_encode($response));
}

$selection = Tools::getValue('selection','');
$selection = explode(',',$selection);
if(empty($selection))
{
    $response['message'] = 'empty selection';
    exit(json_encode($response));
}

switch($action)
{
    case 'delete':
        $files = ExportCustomer::getExportFiles();
        if(empty($files))
        {
            $response['message'] = 'Files not found';
            exit(json_encode($response));
        }
        foreach($files as $path)
        {
            if(in_array(generateToken($path),$selection))
            {
                unlink($path);
                $response['state'] = 'success';
                $response['message'] = _l('File %s deleted', null, array('<b>'.basename($path).'</b>'));
                exit(json_encode($response));
            }
        }
        break;
}