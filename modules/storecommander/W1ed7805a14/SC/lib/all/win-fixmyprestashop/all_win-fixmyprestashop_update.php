<?php

$action = Tools::getValue('action', null);
if (!empty($action))
{
    switch ($action) {
        case 'updateLastCheckDate':
            SCI::updateConfigurationValue('SC_FIXMYPS_LAST_CHECK', date('Y-m-d'));
            break;
        case 'export_fix_to_segment':
            $response = array(
                'state' => 'error',
                'message' => '',
            );

            $segment = array(
                'name' => (string) Tools::getValue('segment_name', null),
                'access' => (string) Tools::getValue('segment_access', null),
                'item_type' => (string) Tools::getValue('segment_item_type', null),
                'item_list' => (string) Tools::getValue('segment_item_list', null),
            );

            if (!empty($segment['name']) && !empty($segment['item_type']) && !empty($segment['item_list']))
            {
                $segment['item_list'] = explode(',', $segment['item_list']);

                $fix_segment_object = new ScSegment();
                $fix_segment_object->id_parent = 0;
                $fix_segment_object->name = (string) $segment['name'];
                $fix_segment_object->type = 'manual';
                $fix_segment_object->access = (string) $segment['access'];
                $fix_segment_object->add();

                if (!empty($fix_segment_object->id))
                {
                    $sql = array();
                    foreach ($segment['item_list'] as $id_element)
                    {
                        $sql[] = 'INSERT INTO '._DB_PREFIX_.'sc_segment_element
                                    SET id_segment = '.(int) $fix_segment_object->id.',
                                    id_element = '.(int) $id_element.',
                                    type_element = "'.pSQL($segment['item_type']).'";';
                    }
                    $segmentInserted = 0;
                    foreach($sql as $rowSql)
                    {
                        $segmentInserted += (int)Db::getInstance()->execute($rowSql);
                    }

                    if ($segmentInserted === count($sql))
                    {
                        $response['state'] = 'success';
                        $response['message'] = _l('Segment and items saved');
                    }
                    else
                    {
                        $response['message'] = _l('Unable to add data to final table. Please contact our support.');
                    }
                }
                else
                {
                    $response['message'] = _l('Unable to save segment');
                }
            }
            else
            {
                $response['message'] = _l('Invalid param');
            }
            echo json_encode($response);
            break;
    }
}
