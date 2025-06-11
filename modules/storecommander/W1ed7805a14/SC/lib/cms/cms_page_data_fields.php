<?php

$colSettings['id_cms'] = array('text' => _l('id'), 'width' => 70, 'align' => 'left', 'type' => 'ro', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['meta_title'] = array('text' => _l('meta_title'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['meta_description'] = array('text' => _l('meta_description'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['meta_keywords'] = array('text' => _l('meta_keywords'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['content'] = array('text' => _l('Description'), 'width' => 200, 'align' => 'left', 'type' => 'rotxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['link_rewrite'] = array('text' => _l('link_rewrite'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
$colSettings['position'] = array('text' => _l('position'), 'width' => 70, 'align' => 'left', 'type' => 'ed', 'sort' => 'int', 'color' => '', 'filter' => '#numeric_filter');
$colSettings['active'] = array('text' => _l('active'), 'width' => 70, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
if (version_compare(_PS_VERSION_, '1.5.6.1', '>='))
{
    $colSettings['indexation'] = array('text' => _l('indexation'), 'width' => 70, 'align' => 'left', 'type' => 'coro', 'sort' => 'str', 'color' => '', 'filter' => '#select_filter', 'options' => array(0 => _l('No'), 1 => _l('Yes')));
}
if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
{
    $colSettings['head_seo_title'] = array('text' => _l('Head SEO title'), 'width' => 200, 'align' => 'left', 'type' => 'edtxt', 'sort' => 'str', 'color' => '', 'filter' => '#text_filter');
}
