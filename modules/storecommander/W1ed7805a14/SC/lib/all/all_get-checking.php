<?php

$type = Tools::getValue('type');
$infos = Tools::getValue('infos');

if (!empty($type))
{
    $licence = SCI::getConfigurationValue('SC_LICENSE_KEY');
    if (empty($licence))
    {
        $licence = 'demo';
    }
    $post = array(
        'licence' => $licence, 'email' => $sc_agent->email, 'type' => $type,
    );
    if (!empty($infos))
    {
        $post['infos'] = $infos;
    }
    $headers = array();
    sc_file_get_contents('http://api.storecommander.com/Tracking/InsertRow', 'POST', $post, $headers);
}
