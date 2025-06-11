<?php

$id_image = Tools::getValue('id_image');
$id_lang = (int) Tools::getValue('id_lang');

$id_project = '';
$list_items = '';

$headers = array();
$posts = array();
$posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
$posts['LICENSE'] = '#';
$posts['URLCALLING'] = '#';
$posts['type'] = 'cutout';
if (defined('IS_SUBS') && IS_SUBS == '1')
{
    $posts['SUBSCRIPTION'] = '1';
}
$ret = makeCallToOurApi('Fizz/Project/GetByType', $headers, $posts);
if (!empty($ret['code']) && $ret['code'] == '200')
{
    $projects = $ret['project'];
}

if (!empty($projects))
{
    $id_project = $projects[0]['id_project'];
    $list_items = $projects[0]['list_items'];
}
else
{
    $name = Tools::getValue('name', '');
    $headers = array();
    $posts = array();
    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
    $posts['LICENSE'] = '#';
    $posts['URLCALLING'] = '#';
    $posts['type'] = 'cutout';
    $posts['name'] = _l('My images to cut out');
    if (defined('IS_SUBS') && IS_SUBS == '1')
    {
        $posts['SUBSCRIPTION'] = '1';
    }
    $iso = Language::getIsoById($id_lang);
    $posts['iso'] = ($iso == 'fr' ? 'fr' : 'en');
    $ret = makeCallToOurApi('Fizz/Project/Create', $headers, $posts);
    if (!empty($ret['code']) && $ret['code'] == '200')
    {
        $id_project = $ret['id_project'];
    }
}

if (!empty($id_project) && !empty($id_image))
{
    $images = explode(',', $id_image);
    foreach ($images as $id_image)
    {
        if (!empty($list_items) && strpos($list_items, '-'.$id_image.'-') === false)
        {
            $list_items .= $id_image.'-';
        }
        elseif (empty($list_items))
        {
            $list_items .= '-'.$id_image.'-';
        }
    }

    $headers = array();
    $posts = array();
    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
    $posts['LICENSE'] = '#';
    $posts['URLCALLING'] = '#';
    $posts['list_items'] = $list_items;
    $posts['status'] = '555';
    if (defined('IS_SUBS') && IS_SUBS == '1')
    {
        $posts['SUBSCRIPTION'] = '1';
    }
    $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);

    echo 'OK';
}
else
{
    echo 'KO';
}
