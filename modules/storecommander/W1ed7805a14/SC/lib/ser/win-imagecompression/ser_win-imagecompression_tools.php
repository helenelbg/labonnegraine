<?php

$analysis_config = array(
    'themes' => array(
        'priority' => 10,
        'path' => _PS_ALL_THEMES_DIR_,
    ),
    'homepage' => array(
        'priority' => 20,
        'only_product' => true,
    ),
    'bestseller' => array(
        'priority' => 30,
        'only_product' => true,
    ),
    'news' => array(
        'priority' => 40,
        'only_product' => true,
    ),
    'official' => array(
        'priority' => 50,
    ),
    'p' => array(
        'priority' => 60,
        'path' => _PS_PROD_IMG_DIR_,
        'only_catalog' => true,
        'only_product' => true,
    ),
    'c' => array(
        'priority' => 70,
        'path' => _PS_CAT_IMG_DIR_,
        'only_catalog' => true,
    ),
    'cms' => array(
        'priority' => 80,
        'path' => _PS_IMG_DIR_.'cms/',
    ),
    'm' => array(
        'priority' => 90,
        'path' => _PS_MANU_IMG_DIR_,
        'only_catalog' => true,
    ),
    'su' => array(
        'priority' => 100,
        'path' => _PS_SUPP_IMG_DIR_,
        'only_catalog' => true,
    ),
    'scenes' => array(
        'priority' => 110,
        'path' => _PS_IMG_DIR_.'scenes/',
        'only_catalog' => true,
    ),
    'scenes_thumbs' => array(
        'priority' => 120,
        'path' => _PS_IMG_DIR_.'scenes/thumbs/',
        'only_catalog' => true,
    ),
    'other' => array(
        'priority' => 130,
    ),
    'obsolete' => array(
        'priority' => 140,
    ),
);

$analysis_params = array(
    'minimal_size' => 5120, ## 5ko
    'ps_img_extension' => '.jpg',
    'priorities' => array(),
    'path' => array(),
    'item_catalog' => array(),
    'item_catalog_priority' => array(),
    'path_to_imgcompression_callback' => '/modules/'.SC_MODULE_FOLDER_NAME.'/ork/imgcompression/imgcompression_callback.php',
);
$compression_status = array(
    'untreated' => 0,
    'sent_for_compression' => 1,
    'compressed' => 2,
    'error' => 3,
    'not_writable' => 4,
    'ignored' => 5, ## ex: compressed img > size_origin
);

foreach ($analysis_config as $item => $row)
{
    $analysis_params['priorities'][$item] = $row['priority'];
    if (array_key_exists('path', $row))
    {
        $analysis_params['path'][$item] = $row['path'];
    }
    if (array_key_exists('only_catalog', $row))
    {
        $analysis_params['item_catalog'][$item] = $row['only_catalog'];
        $analysis_params['item_catalog_priority'][$item] = $row['priority'];
    }
    if (array_key_exists('only_product', $row))
    {
        $analysis_params['item_product_priority'][$item] = $row['priority'];
    }
}
$analysis_params['images_type'] = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'image_type');
$analysis_params['images_type_by_priority'] = array(
    $analysis_params['priorities']['homepage'] => array(),
    $analysis_params['priorities']['bestseller'] => array(),
    $analysis_params['priorities']['news'] => array(),
    $analysis_params['priorities']['p'] => array(),
    $analysis_params['priorities']['c'] => array(),
    $analysis_params['priorities']['m'] => array(),
    $analysis_params['priorities']['su'] => array(),
    $analysis_params['priorities']['scenes'] => array(),
);
## 10 formats max par image
foreach ($analysis_params['images_type'] as $type)
{
    $type_already_add = array(
        'homepage' => count($analysis_params['images_type_by_priority'][$analysis_params['priorities']['homepage']]) + 1,
        'bestseller' => count($analysis_params['images_type_by_priority'][$analysis_params['priorities']['bestseller']]) + 1,
        'news' => count($analysis_params['images_type_by_priority'][$analysis_params['priorities']['news']]) + 1,
        'p' => count($analysis_params['images_type_by_priority'][$analysis_params['priorities']['p']]) + 1,
        'c' => count($analysis_params['images_type_by_priority'][$analysis_params['priorities']['c']]) + 1,
        'm' => count($analysis_params['images_type_by_priority'][$analysis_params['priorities']['m']]) + 1,
        'su' => count($analysis_params['images_type_by_priority'][$analysis_params['priorities']['su']]) + 1,
        'scenes' => count($analysis_params['images_type_by_priority'][$analysis_params['priorities']['scenes']]) + 1,
    );
    if ($type['products'] == 1 && $type_already_add['homepage'] < 11)
    {
        $analysis_params['images_type_by_priority'][$analysis_params['priorities']['homepage']][$type['name']] = array(
            'width' => (int) $type['width'],
            'height' => (int) $type['height'],
        );
    }
    if ($type['products'] == 1 && $type_already_add['bestseller'] < 11)
    {
        $analysis_params['images_type_by_priority'][$analysis_params['priorities']['bestseller']][$type['name']] = array(
            'width' => (int) $type['width'],
            'height' => (int) $type['height'],
        );
    }
    if ($type['products'] == 1 && $type_already_add['news'] < 11)
    {
        $analysis_params['images_type_by_priority'][$analysis_params['priorities']['news']][$type['name']] = array(
            'width' => (int) $type['width'],
            'height' => (int) $type['height'],
        );
    }
    if ($type['products'] == 1 && $type_already_add['p'] < 11)
    {
        $analysis_params['images_type_by_priority'][$analysis_params['priorities']['p']][$type['name']] = array(
            'width' => (int) $type['width'],
            'height' => (int) $type['height'],
        );
    }
    if ($type['categories'] == 1 && $type_already_add['c'] < 11)
    {
        $analysis_params['images_type_by_priority'][$analysis_params['priorities']['c']][$type['name']] = array(
            'width' => (int) $type['width'],
            'height' => (int) $type['height'],
        );
    }
    if ($type['manufacturers'] == 1 && $type_already_add['m'] < 11)
    {
        $analysis_params['images_type_by_priority'][$analysis_params['priorities']['m']][$type['name']] = array(
            'width' => (int) $type['width'],
            'height' => (int) $type['height'],
        );
    }
    if ($type['suppliers'] == 1 && $type_already_add['su'] < 11)
    {
        $analysis_params['images_type_by_priority'][$analysis_params['priorities']['su']][$type['name']] = array(
            'width' => (int) $type['width'],
            'height' => (int) $type['height'],
        );
    }
    if (array_key_exists('scenes', $type) && $type['scenes'] == 1 && $type_already_add['scenes'] < 11)
    {
        $analysis_params['images_type_by_priority'][$analysis_params['priorities']['scenes']][$type['name']] = array(
            'width' => (int) $type['width'],
            'height' => (int) $type['height'],
        );
    }
}
