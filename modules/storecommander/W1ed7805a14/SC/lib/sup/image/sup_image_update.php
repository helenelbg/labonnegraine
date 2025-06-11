<?php

$id_supplier = Tools::getValue('id_supplier', null);
$action = Tools::getValue('action', 0);
if (!empty($id_supplier))
{
    $supplier_ids = explode(',', $id_supplier);

    foreach ($supplier_ids as $id_supplier)
    {
        switch ($action) {
            case 'delete':
                $images_types = ImageType::getImagesTypes('suppliers');
                @unlink(_PS_SUPP_IMG_DIR_.$id_supplier.'.jpg');
                $hdpi = (bool) SCI::getConfigurationValue('PS_HIGHT_DPI');
                foreach ($images_types as $k => $image_type)
                {
                    @unlink(_PS_SUPP_IMG_DIR_.$id_supplier.'-'.stripslashes($image_type['name']).'.jpg');

                    if ($hdpi)
                    {
                        @unlink(_PS_SUPP_IMG_DIR_.$id_supplier.'-'.stripslashes($image_type['name']).'2x.jpg');
                    }
                }
                break;
        }
    }
}
