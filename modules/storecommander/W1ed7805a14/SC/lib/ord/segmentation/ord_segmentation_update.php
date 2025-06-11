<?php

$id_lang = (int) Tools::getValue('id_lang');
$action = (int) Tools::getValue('action');
$type = Tools::getValue('type');
$id_element = (int) Tools::getValue('id_element');
$value = (Tools::getValue('value'));

if ($action == 'present')
{
    $ids = (Tools::getValue('ids'));
    $id_segment = (int) Tools::getValue('id_segment');
    if (!empty($id_segment) && !empty($type) && !empty($ids))
    {
        if ($value == '1' || $value == 'true' || $value == 1)
        {
            $value = 1;
        }
        else
        {
            $value = 0;
        }

        if ($value)
        {
            $ids = explode(',', $ids);
            $segment = new ScSegment($id_segment);
            foreach ($ids as $id_element)
            {
                if (!ScSegmentElement::checkInSegment($id_segment, $id_element, $type))
                {
                    $manual_add = 0;
                    if ($segment->type == 'manual')
                    {
                        $manual_add = 1;
                    }
                    elseif ($segment->auto_file)
                    {
                        $file = $segment->auto_file.'.php';
                        if (file_exists(SC_SEGMENTS_DIR.$file))
                        {
                            require_once SC_SEGMENTS_DIR.$file;
                            $instance = new $segment->auto_file();
                            if ($instance->manually_add_in == 'Y')
                            {
                                $manual_add = 1;
                            }
                        }
                    }
                    if ($manual_add)
                    {
                        $segment_element = new ScSegmentElement();
                        $segment_element->id_segment = (int) $id_segment;
                        $segment_element->id_element = (int) $id_element;
                        $segment_element->type_element = $type;
                        $segment_element->save();
                    }
                }
            }
        }
        else
        {
            $ids = explode(',', $ids);
            foreach ($ids as $id_element)
            {
                $sql = 'DELETE FROM '._DB_PREFIX_."sc_segment_element
                WHERE id_segment =".(int) $id_segment." AND type_element='".pSQL($type)."' AND id_element =".(int) $id_element;
                Db::getInstance()->Execute($sql);
            }
        }
    }
}
if ($action == 'mass_present')
{
    $segments = (Tools::getValue('segments'));
    $ids = (Tools::getValue('ids'));
    if (!empty($segments) && !empty($type) && !empty($ids))
    {
        if ($value == '1' || $value == 'true' || $value == 1)
        {
            $value = 1;
        }
        else
        {
            $value = 0;
        }

        $segments = explode(',', $segments);
        $ids = explode(',', $ids);
        foreach ($segments as $id_segment)
        {
            if ($value)
            {
                $segment = new ScSegment($id_segment);
                foreach ($ids as $id_element)
                {
                    if (!ScSegmentElement::checkInSegment($id_segment, $id_element, $type))
                    {
                        $manual_add = 0;
                        if ($segment->type == 'manual')
                        {
                            $manual_add = 1;
                        }
                        elseif ($segment->auto_file)
                        {
                            $file = $segment->auto_file.'.php';
                            if (file_exists(SC_SEGMENTS_DIR.$file))
                            {
                                require_once SC_SEGMENTS_DIR.$file;
                                $instance = new $segment->auto_file();
                                if ($instance->manually_add_in == 'Y')
                                {
                                    $manual_add = 1;
                                }
                            }
                        }
                        if ($manual_add)
                        {
                            $segment_element = new ScSegmentElement();
                            $segment_element->id_segment = (int) $id_segment;
                            $segment_element->id_element = (int) $id_element;
                            $segment_element->type_element = $type;
                            $segment_element->save();
                        }
                    }
                }
            }
            else
            {
                foreach ($ids as $id_element)
                {
                    $sql = 'DELETE FROM '._DB_PREFIX_."sc_segment_element
                    WHERE id_segment =".(int) $id_segment." AND type_element='".pSQL($type)."' AND id_element =".(int) $id_element;
                    Db::getInstance()->Execute($sql);
                }
            }
        }
    }
}
