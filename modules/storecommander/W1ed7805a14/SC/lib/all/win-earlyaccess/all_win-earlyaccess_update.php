<?php
if (!defined('STORE_COMMANDER')) { exit; }

$response = [
    'status' => 'error',
    'message' => _l('Invalid access'),
];
$token = generateToken(date('YmdH'));
if((!Tools::isSubmit($token) || Tools::getValue($token) !== 'token')
    || (!Tools::isSubmit('email') || !($email = Tools::getValue('email')))
    || (!Tools::isSubmit('pwd') || !($pwd = Tools::getValue('pwd')))) {
    exit(json_encode($response));
}

$apiResult = makeDefaultCallToOurApi(
    '/EarlyAccess/Check',
    [
        'auth-mail' => $email,
        'auth-password' => $pwd
    ],
    [
        'url' => Tools::getShopDomainSsl(true) . __PS_BASE_URI__
    ]
);

if(!$apiResult)
{
    $response['message'] = _l('Bad call');
    exit(json_encode($response));
}

if((int)$apiResult['error'] > 0)
{
    $response['message'] = $apiResult['message'];
    exit(json_encode($response));
}

if(!setScSession('early_access',$apiResult['message']))
{
    $response['message'] = _l('Bad session saving');
    exit(json_encode($response));
}

$response['status'] = 'success';
$response['message'] = _l('Access granted');
exit(json_encode($response));
