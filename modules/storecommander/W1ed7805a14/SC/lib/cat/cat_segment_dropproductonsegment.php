<?php

$mode = (int) Tools::getValue('mode');

if ($mode == 'move')
{
    $id_segment = (int) str_replace('seg_', '', Tools::getValue('segmentTarget'));
    $droppedProducts = Tools::getValue('products');
    $products = explode(',', $droppedProducts);

    if (!empty($products) && !empty($id_segment))
    {
        foreach ($products as $product)
        {
            if (!ScSegmentElement::checkInSegment($id_segment, $product, 'product'))
            {
                $segment_element = new ScSegmentElement();
                $segment_element->id_segment = (int) $id_segment;
                $segment_element->id_element = (int) $product;
                $segment_element->type_element = 'product';
                $segment_element->save();
            }
        }
    }
}
