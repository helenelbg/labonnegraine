<?php

$id_image = Tools::getValue('id_image');

$return = array('type' => 'error', 'message' => _l('No image sended', 1));

if (!empty($id_image))
{
    $sql = 'SELECT * FROM `'._DB_PREFIX_.'image` WHERE id_image = '.(int) $id_image;
    $res = Db::getInstance()->getRow($sql);
    if (!empty($res['id_product']))
    {
        $id_product = $res['id_product'];
        if (file_exists(_PS_IMG_DIR_.'p/'.getImgPath((int) $id_product, (int) $id_image)))
        {
            $path = _PS_IMG_DIR_.'p/'.getImgPath((int) $id_product, (int) $id_image);
            $return = CutOut::upload($path);
        }
        else
        {
            $return = array('type' => 'error', 'message' => _l('No image founded'));
        }
    }
    else
    {
        $return = array('type' => 'error', 'message' => _l('No image founded'));
    }
}

echo json_encode($return);
