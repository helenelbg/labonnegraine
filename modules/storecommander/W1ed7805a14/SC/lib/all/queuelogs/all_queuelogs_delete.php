<?php

$ids = explode(',', Tools::getValue('ids', '0'));

foreach ($ids as $id)
{
    if (!empty($id) && is_numeric($id))
    {
        QueueLog::delete($id);
    }
}
