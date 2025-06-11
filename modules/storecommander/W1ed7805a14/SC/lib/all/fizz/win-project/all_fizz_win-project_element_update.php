<?php

$id_lang = (int) Tools::getValue('id_lang');
$action = Tools::getValue('action', '');

/*
 * ACTION
*/
if (!empty($action) && $action == 'delete')
{
    $ids = (Tools::getValue('ids', 0));

    if (!empty($ids))
    {
        $ids = explode(',', $ids);
        $id_project = Tools::getValue('id_project');

        $project = null;
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
            }
        }
        if (!empty($project['id_project']))
        {
            if ($project['type'] == 'dixit')
            {
                if (in_array($project['status'], array('5', '6', '8', '9', '10', '105', '107', '109', '110', '111', '112', '113', '999')))
                {
                    exit('error_wrongstatus');
                }
            }

            if (!empty($project['list_items']) && $project['list_items'] != '-')
            {
                $deleted_ids = $ids;

                $res = explode('-', trim($project['list_items'], '-'));
                $new_list_items = '';
                foreach ($res as $id)
                {
                    if (!in_array($id, $deleted_ids))
                    {
                        $new_list_items .= $id.'-';
                    }
                }
                if (!empty($new_list_items))
                {
                    $new_list_items = '-'.$new_list_items;
                }

                if ($new_list_items == '--')
                {
                    $new_list_items = '';
                }

                $headers = array();
                $posts = array();
                $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                $posts['LICENSE'] = '#';
                $posts['URLCALLING'] = '#';
                $posts['list_items'] = $new_list_items;
                if (defined('IS_SUBS') && IS_SUBS == '1')
                {
                    $posts['SUBSCRIPTION'] = '1';
                }

                if ($project['type'] != 'cutout')
                {
                    if ($ret['project']['status'] > '1')
                    {
                        $posts['status'] = '2';
                    }
                    else
                    {
                        $posts['status'] = '1';
                    }
                }
                $posts['amount'] = '0';
                $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);

                if (!empty($deleted_ids) && array_key_exists('params', $project) && !empty($project['params']))
                {
                    $project_params = json_decode($project['params'], true);
                    if (array_key_exists('id_category', $project_params) && !empty($project_params['id_category']))
                    {
                        Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'category_product 
                                                WHERE id_category = '.(int) $project_params['id_category'].' 
                                                AND id_product IN ('.pInSQL(implode(',', $deleted_ids)).')');
                    }
                }
            }
        }
    }
}
