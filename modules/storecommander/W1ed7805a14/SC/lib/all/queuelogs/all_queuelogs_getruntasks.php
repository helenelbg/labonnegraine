<?php

$ids = explode(',', Tools::getValue('ids', '0'));

$return = array();

foreach ($ids as $id)
{
    if (!empty($id) && is_numeric($id))
    {
        $return[] = QueueLog::getForRun($id);
        QueueLog::delete($id);
    }
}

if (!empty($return) && count($return))
{
    echo json_encode($return);
}
