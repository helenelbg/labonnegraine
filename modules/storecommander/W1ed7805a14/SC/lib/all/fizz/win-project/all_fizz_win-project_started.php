<?php

$id_lang = Tools::getValue('id_lang');
$id_project = Tools::getValue('id_project');

$return = array('stop' => '1');

if (!empty($id_project))
{
    $headers = array();
    $posts = array();
    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
    $posts['LICENSE'] = '#';
    $posts['URLCALLING'] = '#';
    if (defined('IS_SUBS') && IS_SUBS == '1')
    {
        $posts['SUBSCRIPTION'] = '1';
    }
    $ret = makeCallToOurApi('Fizz/Project/Get/'.$id_project, $headers, $posts);
    if (!empty($ret['code']) && $ret['code'] == '200')
    {
        $project = $ret['project'];

        $type = $project['type'];

        $func = $type.'_action_started';
        if (function_exists($func))
        {
            $return = $func($project);
        }
    }
}
else
{
    $return = array('status' => 'error', 'message' => _l('No project'), 'stop' => '1');
}

exit(json_encode($return));
