<?php

$manufacturer_list = Tools::getValue('manufacturer_list', null);
$action = Tools::getValue('action', 0);
if (!empty($manufacturer_list))
{
    $manufacturer_ids = explode(',', $manufacturer_list);

    foreach ($manufacturer_ids as $id_manufacturer)
    {
        switch ($action) {
            case 'delete':
                $images_types = ImageType::getImagesTypes('manufacturers');
                $generate_hight_dpi_images = (bool)SCI::getConfigurationValue('PS_HIGHT_DPI');
                @unlink(_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg');
                foreach ($images_types as $k => $image_type)
                {
                    @unlink(_PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'.jpg');
                    if ($generate_hight_dpi_images)
                    {
                        @unlink(_PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'2x.jpg');
                    }
                }
                break;
            default:
        }
    }
}
