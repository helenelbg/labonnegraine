<?php

$action = Tools::getValue('action', null);
$allowedAction = array('enable', 'disable');

$response = array(
    'state' => 'error',
    'message' => '',
);

if (SC_DEMO)
{
    exit(json_encode($response));
}

if (empty($action) || !in_array($action, $allowedAction))
{
    $response['message'] = 'invalid params';
    exit(json_encode($response));
}

$scUniqueId = SCI::getConfigurationValue('SC_UNIQUE_ID');
switch ($action)
{
    case 'enable':
        $apiResult = makeDefaultCallToOurApi('externhall/autoupdate/set', array('unique-id' => $scUniqueId), array('autoupdate' => $action));
        if ((int) $apiResult['code'] == 200)
        {
            $response['state'] = 'success';
        }
        else
        {
            $response['message'] = _l('Error while activating the automatic update', true);
        }
        break;
    case 'disable':
        $apiResult = makeDefaultCallToOurApi('externhall/autoupdate/set', array('unique-id' => $scUniqueId), array('autoupdate' => $action));
        if ((int) $apiResult['code'] == 200)
        {
            $response['state'] = 'success';
        }
        else
        {
            $response['message'] = _l('Error while disabling the automatic update', true);
        }
        break;
}

exit(json_encode($response));
