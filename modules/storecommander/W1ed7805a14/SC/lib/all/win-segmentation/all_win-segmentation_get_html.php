<?php

$id_segment = Tools::getValue('id_segment', null);
$id_lang = Tools::getValue('id_lang', null);
if ($id_segment)
{
    $segment = new ScSegment($id_segment);
    $return = array();
    $html_segment = SegmentHook::hookByIdSegment('segmentAutoConfig', $segment, array('id_lang' => $id_lang, 'values' => $segment->auto_params));
    if (!empty($html_segment))
    {
        $instance = new $segment->auto_file();
        $html = '<strong style="font-size: 18px;">'._l($instance->name).'</strong><br/><br/>';
        $html .= '<form id="form_params">'.$html_segment.'</form>';
        echo $html;
    }
}
