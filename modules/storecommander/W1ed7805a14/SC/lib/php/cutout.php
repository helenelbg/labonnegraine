<?php

function cutout_createImage($id_image, $image_code)
{
    if (file_exists(SC_TOOLS_DIR.'lib/all/upload/upload-image.inc.php'))
    {
        require_once SC_TOOLS_DIR.'lib/all/upload/upload-image.inc.php';
    }
    else
    {
        require_once SC_DIR.'lib/all/upload/upload-image.inc.php';
    }

    $image = new Image((int) $id_image);
    $path = SC_PS_PATH_REL.'img/p/'.getImgPath((int) $image->id_product, (int) $id_image);

    file_put_contents($path, $image_code);

    $tmp = explode('/', $path);
    $fileName = end($tmp);

    $generate_hight_dpi_images = (bool) SCI::getConfigurationValue('PS_HIGHT_DPI');
    $id_product = $image->id_product;
    $id_image = $image->id;
    $ext = substr(Tools::strtolower($fileName), Tools::strlen(Tools::strtolower($fileName)) - 3, 3);
    $imagesTypes = ImageType::getImagesTypes('products');
    $tmpName = $path;
    switch (_s('CAT_PROD_IMG_PNG_METHOD')){
        case 0:
            $newImageSourcePath = _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, '', 'jpg');

            $tinypng = _s('CAT_PROD_IMG_TINYPNG');
            if (!empty($tinypng))
            {
                require_once SC_DIR.'lib/php/tinypng/lib/Tinify/Exception.php';
                require_once SC_DIR.'lib/php/tinypng/lib/Tinify/ResultMeta.php';
                require_once SC_DIR.'lib/php/tinypng/lib/Tinify/Result.php';
                require_once SC_DIR.'lib/php/tinypng/lib/Tinify/Source.php';
                require_once SC_DIR.'lib/php/tinypng/lib/Tinify/Client.php';
                require_once SC_DIR.'lib/php/tinypng/lib/Tinify.php';

                try
                {
                    \Tinify\setKey($tinypng);
                    \Tinify\validate();
                    $source = \Tinify\fromFile($newImageSourcePath);
                    $preservedMeta = $source->preserve('copyright', 'creation', 'location');
                    $preservedMeta->toFile($newImageSourcePath);
                }
                catch (Exception $e)
                {
                }
            }

            foreach ($imagesTypes as $k => $imageType)
            {
                if (!imageResize($newImageSourcePath, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'jpg'), $imageType['width'], $imageType['height'], 'jpg'))
                {
                    return 0;
                }
                else
                {
                    if ($generate_hight_dpi_images)
                    {
                        $name = _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'jpg');
                        $name = str_replace('.jpg', '2x.jpg', $name);
                        imageResize($newImageSourcePath, $name, $imageType['width'] * 2, $imageType['height'] * 2, 'jpg');
                    }
                }
            }
            break;
        case 1:
            foreach ($imagesTypes as $k => $imageType)
            {
                if (!imageResize($tmpName, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'jpg'), $imageType['width'], $imageType['height'], $ext))
                {
                    return 0;
                }
                else
                {
                    if ($generate_hight_dpi_images)
                    {
                        $name = _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'jpg');
                        $name = str_replace('.jpg', '2x.jpg', $name);
                        imageResize($tmpName, $name, $imageType['width'] * 2, $imageType['height'] * 2, $ext);
                    }
                }
            }
            break;
        case 2:
            foreach ($imagesTypes as $k => $imageType)
            {
                if ($ext == 'png' && !imageResize($tmpName, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'png'), $imageType['width'], $imageType['height'], 'png'))
                {
                    return 0;
                }
                if (!imageResize($tmpName, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'jpg'), $imageType['width'], $imageType['height'], 'jpg'))
                {
                    return 0;
                }
                else
                {
                    if ($generate_hight_dpi_images)
                    {
                        $name = _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'jpg');
                        $name = str_replace('.jpg', '2x.jpg', $name);
                        imageResize($tmpName, $name, $imageType['width'] * 2, $imageType['height'] * 2, 'jpg');
                    }
                }
            }
            break;
    }
    SCI::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_product));
}
