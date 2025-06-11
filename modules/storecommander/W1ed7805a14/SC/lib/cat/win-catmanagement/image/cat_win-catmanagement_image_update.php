<?php

$list_id_image = Tools::getValue('list_id_image', 0);
$action = Tools::getValue('action', null);

if (!empty($list_id_image))
{
    $id_image_array = explode(',', $list_id_image);
    foreach ($id_image_array as $id_image)
    {
        switch ($action) {
            case 'thumbnail_regeneration':
                // Getting format generation
                $result = array(
                    'error' => array(),
                    'success' => null,
                );
                $formats = ImageType::getImagesTypes('categories');
                $formated_medium = ImageType::getFormattedName('medium');
                $generate_hight_dpi_images = (bool) Configuration::get('PS_HIGHT_DPI');
                foreach (scandir(_PS_CAT_IMG_DIR_, SCANDIR_SORT_NONE) as $image)
                {
                    if (preg_match('/^[0-9]*\.jpg$/', $image))
                    {
                        foreach ($formats as $imageType)
                        {
                            $newDir = _PS_CAT_IMG_DIR_;
                            if (!file_exists($newDir))
                            {
                                continue;
                            }

                            if (($imageType['name'] == $formated_medium) && is_file(_PS_CAT_IMG_DIR_.str_replace('.', '_thumb.', $image)))
                            {
                                $image = str_replace('.', '_thumb.', $image);
                            }
                            $source_file = _PS_CAT_IMG_DIR_.$image;

                            if (!file_exists($newDir.substr($image, 0, -4).'-'.stripslashes($imageType['name']).'.jpg'))
                            {
                                if (!file_exists($source_file) || !filesize($source_file))
                                {
                                    $result['error'][] = _l('Source file does not exist or is empty (%s)', null, array($source_file));
                                }
                                elseif (!ImageManager::resize($source_file, $newDir.substr(str_replace('_thumb.', '.', $image), 0, -4).'-'.stripslashes($imageType['name']).'.jpg', (int) $imageType['width'], (int) $imageType['height']))
                                {
                                    $result['error'][] = _l('Failed to resize image file (%s)', null, array($source_file));
                                }

                                if ($generate_hight_dpi_images)
                                {
                                    if (!ImageManager::resize($source_file, $newDir.substr($image, 0, -4).'-'.stripslashes($imageType['name']).'2x.jpg', (int) $imageType['width'] * 2, (int) $imageType['height'] * 2))
                                    {
                                        $result['error'][] = _l('Failed to resize image file to high resolution (%s)', null, array($source_file));
                                    }
                                }
                            }
                        }
                    }
                }
                $result['error'] = implode("\n", $result['error']);
                if (empty($result['error']))
                {
                    $result['success'] = 'OK';
                }
                exit(json_encode($result));
            default:
        }
    }
}
