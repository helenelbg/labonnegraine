<?php

$mode = (int) Tools::getValue('mode');

if ($mode == 'move')
{
    $id_segment = (int) str_replace('seg_', '', Tools::getValue('segmentTarget'));
    $droppedCustomers = Tools::getValue('orders');
    $orders = explode(',', $droppedCustomers);

    if (!empty($orders) && !empty($id_segment))
    {
        foreach ($orders as $order)
        {
            if (!ScSegmentElement::checkInSegment($id_segment, $order, 'order'))
            {
                $segment_element = new ScSegmentElement();
                $segment_element->id_segment = (int) $id_segment;
                $segment_element->id_element = (int) $order;
                $segment_element->type_element = 'order';
                $segment_element->save();
            }
        }
    }
}
