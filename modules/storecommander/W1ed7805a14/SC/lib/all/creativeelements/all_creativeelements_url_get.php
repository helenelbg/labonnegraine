<?php

// HEADER
if (stristr($_SERVER['HTTP_ACCEPT'], 'application/json'))
{
    header('Content-type: application/json');
}
else
{
    header('Content-type: text/json');
}
$id = Tools::getValue('id');
$type = Tools::getValue('type');

$reponse = array(
    'status' => 'success',
    'data' => \Sc\Lib\Extension\ScCreativeElements::EditLink($id, $type, $id_lang, $sc_agent),
);

echo json_encode($reponse);
exit;
