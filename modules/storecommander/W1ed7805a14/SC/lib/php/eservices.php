<?php
/*
 * CONFIG
 */

$status = array(
    '0' => _l('Waiting configuration'),
    '1' => _l('Configuring'),
    '2' => _l('Configured'),
    '3' => _l('Quote requested'),
    '4' => _l('Quoted'),
    '5' => _l('Waiting payment'),
    '6' => _l('Paid'),
    '7' => _l('Waiting to start'),
    '8' => _l('Started'),
    '9' => _l("Creating at SC service's"),
    '10' => _l('Processing'),
    '11' => _l('Processed'),
    '12' => _l('Importing'),
    '13' => _l('Imported'),

    '101' => _l('Error during configuring'),
    '103' => _l('Error setting price'),
    '105' => _l('Error payment'),
    '107' => _l('Error during start'),
    '109' => _l("Error during creation at SC service's"),
    '110' => _l('Error during process'),
    '111' => _l('Processing but error with some elements'),
    '112' => _l('Error during import'),
    '113' => _l('Not enough Fizz. Refill your wallet and re-start project'),
    '114' => _l('Paused - No credits left'),

    '300' => _l('Store Commander validation required'),

    '555' => _l('Permanent'),
    '999' => _l('Archived'),
);
$status_color = array(
    '0' => '',
    '1' => '#C4DCED',
    '2' => '#C4DCED',
    '3' => '#F4E8A5',
    '4' => '#F4E8A5',
    '5' => '#FFE65B',
    '6' => '#FFE65B',
    '7' => '#FFD616',
    '8' => '#FFD616',
    '9' => '#CBE383',
    '10' => '#CBE383',
    '11' => '#CBE383',
    '12' => '#82C46C',
    '13' => '#82C46C',

    '101' => '#FF0000',
    '103' => '#FF0000',
    '105' => '#FF0000',
    '107' => '#FF0000',
    '109' => '#FF0000',
    '110' => '#FF0000',
    '111' => '#FFBE49',
    '112' => '#FF0000',
    '113' => '#FFBE49',
    '114' => '#FFBE49',

    '300' => '#FFBE49',

    '555' => '#D6D6D6',
    '999' => '#C8C8C8',
);
$status_stripes = array(
    '0' => '',
    '1' => '1',
    '2' => '',
    '3' => '1',
    '4' => '',
    '5' => '1',
    '6' => '',
    '7' => '1',
    '8' => '',
    '9' => '1',
    '10' => '1',
    '11' => '',
    '12' => '1',
    '13' => '',

    '101' => '',
    '103' => '',
    '105' => '',
    '107' => '',
    '109' => '',
    '110' => '',
    '111' => '',
    '112' => '',
    '113' => '1',

    '300' => '1',

    '555' => '1',
    '999' => '',
);

$types = array(
    'cutout' => _l('Cut out'),
    'dixit' => _l('Product translation Pro'),
    'image_compression' => _l('Image compression'),
);

/*
 * FUNCTIONS
 */

function eServices_sendListItems($id_project)
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
    if (!empty($ret['code']) && $ret['code'] == '200' && !empty($ret['project']))
    {
        if ($ret['project']['type'] == 'dixit')
        {
            $authorizedStatus = array('0', '1', '2', '3', '4', '7');
        }

        if (in_array($ret['project']['status'], $authorizedStatus))
        {
            $params = $ret['project']['params'];
            if (!empty($params))
            {
                $params = json_decode($params, true);
                if (!empty($params['id_category']))
                {
                    $id_category = $params['id_category'];

                    $sql = 'SELECT * FROM '._DB_PREFIX_.'category_product WHERE id_category='.(int)$id_category;
                    $products = Db::getInstance()->executeS($sql);

                    $list_items = '';
                    foreach ($products as $product)
                    {
                        $list_items .= $product['id_product'].'-';
                    }
                    if (!empty($list_items))
                    {
                        $list_items = '-'.$list_items;
                    }

                    $headers = array();
                    $posts = array();
                    $posts['KEY'] = 'gt789zef132kiy789u13v498ve15nhry98';
                    $posts['LICENSE'] = '#';
                    $posts['URLCALLING'] = '#';
                    $posts['list_items'] = $list_items;
                    if ($ret['project']['status'] > '1')
                    {
                        $posts['status'] = '2';
                    }
                    else
                    {
                        $posts['status'] = '1';
                    }
                    $posts['amount'] = '0';
                    if (defined('IS_SUBS') && IS_SUBS == '1')
                    {
                        $posts['SUBSCRIPTION'] = '1';
                    }
                    $ret = makeCallToOurApi('Fizz/Project/Update/'.$id_project, $headers, $posts);
                }
            }
        }
    }
}
