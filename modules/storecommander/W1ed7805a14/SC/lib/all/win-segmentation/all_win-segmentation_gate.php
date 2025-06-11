<?php

$id_lang = (int) Tools::getValue('id_lang', 0);
$segment_file = Tools::getValue('segment');
$function_name = Tools::getValue('function');
$params = Tools::getValue('params');

if (empty($params))
{
    $params = array();
}
$params['id_lang'] = $id_lang;

if (!empty($segment_file) && !empty($function_name))
{
    $file = $segment_file.'.php';
    if (file_exists(SC_SEGMENTS_DIR.$file))
    {
        require_once SC_SEGMENTS_DIR.$file;
        $return = $segment_file::$function_name($params);
        if (!empty($return))
        {
            echo $return;
        }
    }
}
