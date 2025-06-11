<?php

$grids = array(
    'grid_light' => 'id_cms,meta_title,link_rewrite,active',
    'grid_large' => 'id_cms,meta_title,meta_description,meta_keywords,link_rewrite,content,position,active',
    'grid_seo' => 'id_cms,meta_title,meta_description,meta_keywords,link_rewrite',
);
if (version_compare(_PS_VERSION_, '1.5.6.1', '>='))
{
    $grids = array(
        'grid_light' => 'id_cms,meta_title,link_rewrite,active',
        'grid_large' => 'id_cms,meta_title,meta_description,meta_keywords,link_rewrite,content,position,active,indexation',
        'grid_seo' => 'id_cms,meta_title,meta_description,meta_keywords,link_rewrite,indexation',
    );
}
if (version_compare(_PS_VERSION_, '1.7.5.0', '>='))
{
    $grids['grid_light'] = str_replace('meta_title,', 'meta_title,head_seo_title,', $grids['grid_light']);
    $grids['grid_large'] = str_replace('meta_title,', 'meta_title,head_seo_title,', $grids['grid_large']);
    $grids['grid_seo'] = str_replace('meta_title,', 'meta_title,head_seo_title,', $grids['grid_seo']);
}
