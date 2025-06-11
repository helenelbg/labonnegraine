<?php

$mode = (int) Tools::getValue('mode');

if ($mode == 'move')
{
    $id_segment = (int) str_replace('seg_', '', Tools::getValue('segmentTarget'));
    $droppedCustomers = Tools::getValue('discussions');
    $discussions = explode(',', $droppedCustomers);

    if (!empty($discussions) && !empty($id_segment))
    {
        foreach ($discussions as $discussion)
        {
            if (!ScSegmentElement::checkInSegment($id_segment, $discussion, 'customer_service'))
            {
                $segment_element = new ScSegmentElement();
                $segment_element->id_segment = (int) $id_segment;
                $segment_element->id_element = (int) $discussion;
                $segment_element->type_element = 'customer_service';
                $segment_element->save();
            }
        }
    }
}
