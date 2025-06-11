<?php

$mode = (int) Tools::getValue('mode');

if ($mode == 'move')
{
    $id_segment = (int) str_replace('seg_', '', Tools::getValue('segmentTarget'));
    $droppedCustomers = Tools::getValue('customers');
    $customers = explode(',', $droppedCustomers);

    if (!empty($customers) && !empty($id_segment))
    {
        foreach ($customers as $customer)
        {
            if (!ScSegmentElement::checkInSegment($id_segment, $customer, 'customer'))
            {
                $segment_element = new ScSegmentElement();
                $segment_element->id_segment = (int) $id_segment;
                $segment_element->id_element = (int) $customer;
                $segment_element->type_element = 'customer';
                $segment_element->save();
            }
        }
    }
}
