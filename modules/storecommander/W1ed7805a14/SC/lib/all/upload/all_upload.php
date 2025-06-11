<?php

/**
 * upload.php.
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under GPL License.
 * Modified for Store Commander
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

// HTTP headers for no cache etc
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Settings
$obj = Tools::getValue('obj', '');
$fromVault = Tools::getValue('from_vault', false);
$result_message = '{"jsonrpc" : "2.0", "result" : "", "error" : null, "id" : "#ID#"}';
switch ($obj) {
    case 'attrtexture':
        $targetDir = _PS_COL_IMG_DIR_;
        $id_attribute = (int) Tools::getValue('id_attribute');
        break;
    case 'importcsv':
        $targetDir = SC_CSV_IMPORT_DIR;
        break;
    case 'importcsvcat':
        $targetDir = SC_CSV_IMPORT_DIR.'category/';
        break;
    case 'importcsvcus':
        $targetDir = SC_CSV_IMPORT_DIR.'customers/';
        break;
    case 'importcsvman':
        $targetDir = SC_CSV_IMPORT_DIR.'manufacturers/';
        break;
    case 'attachment':
        $targetDir = _PS_DOWNLOAD_DIR_;
        break;
    case 'mail_attachment':
        $targetDir = SC_MAIL_ATTACHMENT_DIR;
        break;
    case 'image':
        if (file_exists(SC_TOOLS_DIR.'lib/all/upload/upload-image.inc.php'))
        {
            require_once SC_TOOLS_DIR.'lib/all/upload/upload-image.inc.php';
        }
        else
        {
            require_once 'upload-image.inc.php';
        }
        $targetDir = _PS_TMP_IMG_DIR_;
        break;
    case 'manufacturer_logo':
        $targetDir = _PS_MANU_IMG_DIR_;
        break;
    case 'supplier_logo':
        $targetDir = _PS_SUPP_IMG_DIR_;
        break;
    default:
        exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 100, "message": "Failed to open target directory."}, "id" : "id"}');
}

//$cleanupTargetDir = false; // Remove old files
//$maxFileAge = 60 * 60; // Temp file age in seconds

// 5 minutes execution time
@set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Get parameters
if ($fromVault)
{
    if (!empty($_FILES) && array_key_exists('file', $_FILES))
    {
        $image_tmp = array();
        if (!empty($_FILES['file']))
        {
            foreach ($_FILES['file'] as $data_type => $data)
            {
                // si plusieurs images
                if (is_array($data))
                {
                    foreach ($data as $image_iteration => $value)
                    {
                        $image_tmp[$image_iteration][$data_type] = $value;
                    }
                }
                else
                {
                    $image_tmp[0][$data_type] = $data;
                }
            }
        }

        if (!empty($image_tmp))
        {
            $sql = '';
            foreach ($image_tmp as $image_file)
            {
                $_FILES['file'] = null;
                $_FILES['file'] = $image_file;

                $fileName = preg_replace('/[^\w\._]+/', '', $image_file['name']);

                if ($obj == 'attrtexture')
                {
                    $fileName = $id_attribute.'.jpg';
                }

                if (!file_exists($targetDir))
                {
                    @mkdir($targetDir);
                }

                if (file_exists($image_file['tmp_name']))
                {
                    // Open temp file
                    if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']))
                    {
                        // Open temp file
                        $out = fopen($targetDir.$fileName, 'wb');
                        if ($out)
                        {
                            // Read binary input stream and append it to temp file
                            $in = fopen($_FILES['file']['tmp_name'], 'rb');
                            if ($in)
                            {
                                while ($buff = fread($in, 4096))
                                {
                                    fwrite($out, $buff);
                                }
                            }
                            else
                            {
                                exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 121, "message": "Failed to open input stream."}, "id" : "id"}');
                            }
                            fclose($in);
                            fclose($out);
                            @unlink($_FILES['file']['tmp_name']);
                            getUpload();
                        }
                        else
                        {
                            exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 122, "message": "Failed to open output stream: '.join('/<br/>', explode('/', $targetDir.$fileName)).'<br/>This folder must be writeable."}, "id" : "id"}');
                        }
                    }
                    else
                    {
                        exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 123, "message": "Failed to move uploaded file."}, "id" : "id"}');
                    }
                }
            }
        }
    }
}
elseif ($obj == 'mail_attachment')
{
    if (!isset($_FILES))
    {
        return;
    }
    $formId = Tools::getValue('formId', '');
    $targetDir = $targetDir.$formId.DIRECTORY_SEPARATOR;
    $targetFile = $targetDir.$_FILES['file']['name'];
    if (!is_dir($targetDir) && !mkdir($targetDir, 0777, true))
    {
        throw new RuntimeException(sprintf('Directory "%s" was not created', $targetDir));
    }
    header('Content-Type: text/json');
    try
    {
        if ($_FILES['file']['size'] == 0)
        {
            $errorMessage = _l('problem uploading file, maybe the file is too big (max %s)', array(ini_get('upload_max_filesize')));
            throw new Exception($errorMessage);
        }
        move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);
    }
    catch (Exception $e)
    {
        exit(
        json_encode(array(
                        'state' => false,
                        'extra' => array(
                            'code' => 103,
                            'message' => $e->getMessage(),
                        ),
                        'id' => 'id',
                    ))
        );
    }
    exit(
    json_encode(array(
                    'state' => true,    // saved or not saved
                    'name' => $targetFile,   // server-name
                    'extra' => array(   // extra info, optional
                        'info' => $_FILES,
                        'param' => '',
                    ),
                ))
    );
}
else
{
    $chunk = isset($_REQUEST['chunk']) ? $_REQUEST['chunk'] : 0;
    $chunks = isset($_REQUEST['chunks']) ? $_REQUEST['chunks'] : 0;
    $fileName = (array_key_exists('name', $_REQUEST) ? $_REQUEST['name'] : (array_key_exists('file_fullname', $_REQUEST) ? $_REQUEST['file_fullname'] : ''));

    // Clean the fileName for security reasons
    $fileName = preg_replace('/[^\w\._]+/', '', $fileName);

    // Make sure the fileName is unique but only if chunking is disabled
    if (($obj != 'importcsv' && $obj != 'importcsvcus' && $obj != 'importcsvcat' && $obj != 'importcsvman') && $chunks < 2 && file_exists($targetDir.$fileName))
    {
        $ext = strrpos($fileName, '.');
        $fileName_a = substr($fileName, 0, $ext);
        $fileName_b = substr($fileName, $ext);
        $count = 1;
        while (file_exists($targetDir.$fileName_a.'_'.$count.$fileName_b))
        {
            ++$count;
        }

        $fileName = $fileName_a.'_'.$count.$fileName_b;
    }
    if ($obj == 'attrtexture')
    {
        $ext = strrpos($fileName, '.');
        $fileName_b = substr($fileName, $ext);
        $fileName = $id_attribute.$fileName_b;
    }

    // Create target dir
    if (!file_exists($targetDir))
    {
        @mkdir($targetDir);
    }

    // Look for the content type header
    if (isset($_SERVER['HTTP_CONTENT_TYPE']))
    {
        $contentType = $_SERVER['HTTP_CONTENT_TYPE'];
    }

    if (isset($_SERVER['CONTENT_TYPE']))
    {
        $contentType = $_SERVER['CONTENT_TYPE'];
    }

    // Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
    if (strpos($contentType, 'multipart') !== false)
    {
        if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']))
        {
            // Open temp file
            $out = fopen($targetDir.$fileName, $chunk == 0 ? 'wb' : 'ab');
            if ($out)
            {
                // Read binary input stream and append it to temp file
                $in = fopen($_FILES['file']['tmp_name'], 'rb');
                if ($in)
                {
                    while ($buff = fread($in, 4096))
                    {
                        fwrite($out, $buff);
                    }
                }
                else
                {
                    exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                }
                fclose($in);
                fclose($out);
                @unlink($_FILES['file']['tmp_name']);
                if ($chunks < 2 || $chunks == $chunk + 1)
                {
                    getUpload();
                }
            }
            else
            {
                exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 102, "message": "Failed to open output stream: '.join('/<br/>', explode('/', $targetDir.$fileName)).'<br/>This folder must be writeable."}, "id" : "id"}');
            }
        }
        else
        {
            exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        }
    }
    else
    {
        // Open temp file
        $out = fopen($targetDir.$fileName, $chunk == 0 ? 'wb' : 'ab');
        if ($out)
        {
            // Read binary input stream and append it to temp file
            $in = fopen('php://input', 'rb');
            if ($in)
            {
                while ($buff = fread($in, 4096))
                {
                    fwrite($out, $buff);
                }
            }
            else
            {
                exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 111, "message": "Failed to open input stream."}, "id" : "id"}');
            }
            fclose($in);
            fclose($out);
            if ($chunks < 2 || $chunks == $chunk + 1)
            {
                getUpload();
            }
        }
        else
        {
            exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 112, "message": "Failed to open output stream: '.join('/<br/>', explode('/', $targetDir.$fileName)).'<br/>This folder must be writeable."}, "id" : "id"}');
        }
    }
}
// AJOUTER DANS PS
$sql = '';
function getUpload()
{
    global $targetDir,$fileName,$_FILES,$languages,$obj,$sql,$result_message;
    $generate_hight_dpi_images = (bool) SCI::getConfigurationValue('PS_HIGHT_DPI');
    switch ($obj) {
        case 'attrtexture':
        case 'importcsv':
        case 'importcsvcus':
        case 'importcsvcat':
        case 'importcsvman':
            // nothing to create
            break;
        case 'attachment':
            $name = $_FILES['file']['name'];
            $file = $fileName;
            $mime = $_FILES['file']['type'];

                $sql = 'INSERT INTO `'._DB_PREFIX_."attachment` (file,file_name,mime) VALUES ('".psql($file)."','".psql($name)."','".psql($mime)."')";

            Db::getInstance()->Execute($sql);
            $id_attachment = Db::getInstance()->Insert_ID();
            $sqlstr = '';
            $name = substr($name, 0, -4);
            foreach ($languages as $lang)
            {
                $desc = '';
                if (_s('CAT_PROD_ATTCH_DESC') == '1')
                {
                    $desc = psql($name).'_'.psql($lang['iso_code']);
                }
                elseif (_s('CAT_PROD_ATTCH_DESC') == '2')
                {
                    $desc = psql($name);
                }
                $sqlstr .= '('.(int) $id_attachment.','.(int) $lang['id_lang'].',\''.psql($name).'\',\''.$desc.'\'),';
            }
            $sqlstr = trim($sqlstr, ',');
            $sql2 = 'INSERT INTO `'._DB_PREFIX_.'attachment_lang` (id_attachment,id_lang,name,description) VALUES '.$sqlstr;
            Db::getInstance()->Execute($sql2);
            $linktoproduct = Tools::getValue('linktoproduct', '0');
            $product_list = Tools::getValue('product_list', 'null');
            if ($linktoproduct && $product_list != 'null')
            {
                $sql = 'DELETE FROM `'._DB_PREFIX_.'product_attachment` WHERE `id_attachment` = '.(int) $id_attachment.' AND `id_product` IN ('.pInSQL($product_list).')';
                Db::getInstance()->Execute($sql);
                $sqlstr = array();
                $product_listarray = explode(',', $product_list);
                foreach ($product_listarray as $id_product)
                {
                    $sqlstr[] = '('.$id_product.','.$id_attachment.')';
                }
                $sqlstr = array_unique($sqlstr);
                $sql = 'INSERT INTO `'._DB_PREFIX_.'product_attachment` (id_product,id_attachment) VALUES '.psql(join(',', $sqlstr));
                Db::getInstance()->Execute($sql);

                $sql = 'UPDATE `'._DB_PREFIX_.'product` SET cache_has_attachments=1 WHERE `id_product` IN ('.pInSQL($product_list).')';
                Db::getInstance()->Execute($sql);
            }
            if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
            {
                clearstatcache();
                $file_size = @filesize(_PS_DOWNLOAD_DIR_.$file);
                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'attachment SET file_size = '.(int) $file_size.' WHERE id_attachment = '.(int) $id_attachment);
            }

            // PM Cache
            if (!empty($product_list))
            {
                ExtensionPMCM::clearFromIdsProduct($product_list);
            }
            break;
        case 'image':
            global $id_product,$id_image;
            $id_products = (Tools::getValue('product_list', 0));
            $attr_list = (Tools::getValue('attr_list', 0));
            $is_multiproduct = (Tools::getValue('is_multiproduct', 0));
            if ($is_multiproduct)
            {
                if (SCMS)
                {
                    $sql = 'SELECT id_product,id_product_attribute
                            FROM '._DB_PREFIX_.'product_attribute
                            WHERE id_product_attribute IN (SELECT id_product_attribute
                                                            FROM '._DB_PREFIX_.'product_attribute_shop
                                                            WHERE id_shop = '.(int) SCI::getSelectedShop().'
                                                            AND id_product_attribute IN ('.pInSQL($attr_list).'))';
                }
                else
                {
                    $sql = 'SELECT id_product,id_product_attribute
                        FROM '._DB_PREFIX_.'product_attribute
                        AND id_product_attribute IN ('.pInSQL($attr_list).')';
                }
                $res = Db::getInstance()->executeS($sql);
                $cache_product_attr = array();
                foreach ($res as $row)
                {
                    $cache_product_attr[$row['id_product']][] = (int) $row['id_product_attribute'];
                }
            }

            $id_products = explode(',', $id_products);
            foreach ($id_products as $id_product)
            {
                $highPos = Image::getHighestPosition($id_product);
                $image = new Image();
                $image->id_product = $id_product;
                ++$highPos;
                $image->position = $highPos;
                $legends = array();
                foreach ($languages as $lang)
                {
                    if (SCMS)
                    {
                        $product = new Product($id_product, false, $lang['id_lang'], (int) SCI::getSelectedShop());
                    }
                    else
                    {
                        $product = new Product($id_product, false, $lang['id_lang']);
                    }
                    $n = explode('\.', $fileName);
                    array_pop($n);
                    $legends[$lang['id_lang']] = str_replace(array('#', '[', '^', '<', '>', '=', '{', '}', ']', '*', '  '), '', Tools::substr($product->name, 0, 128));
                    if(_s('CAT_PROD_IMG_DEFAULT_LEGEND')
                        && _s('CAT_PROD_IMG_DEFAULT_LEGEND') == 1
                        && $product->id_manufacturer){
                        $manufacturer = new Manufacturer($product->id_manufacturer, $lang['id_lang']);
                        $legends[$lang['id_lang']] .= ' '.$manufacturer->name;
                    }
                }
                $image->legend = $legends;
                if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
                {
                    if (!Image::getCover($id_product))
                    {
                        $image->cover = 1;
                    }
                    else
                    {
                        $image->cover = 0;
                    }
                }
                if (SCMS)
                {
                    $image->id_shop_list = SCI::getSelectedShopActionList(false, $id_product);
                }
                if (!$image->add())
                {
                    exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 113, "message": "Error creating image object."}, "id" : "id"}');
                }
                $id_image = $image->id;
                $ext = substr(Tools::strtolower($fileName), Tools::strlen(Tools::strtolower($fileName)) - 3, 3);
                $imagesTypes = ImageType::getImagesTypes('products');
                $tmpName = $targetDir.$fileName;
                switch (_s('CAT_PROD_IMG_PNG_METHOD')){
                    case 0:
                        $newImageSourcePath = _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, '', 'jpg');
                        if (!copy($tmpName, $newImageSourcePath))
                        {
                            exit('{"jsonrpc" : "2.0", "result" : null, "rror" : {"code": 106, "message": "PS: An error occurred while copying image source"}, "id" : "id"}');
                        }

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
                                exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 106, "message": "PS: An error occurred while copying image '.stripslashes($imageType['name']).'"}, "id" : "id"}');
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
                        if (!imageResize($tmpName, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, '', 'jpg'), null, null, $ext))
                        {
                            exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 106, "message": "PS: An error occurred while copying image"}, "id" : "id"}');
                        }
                        foreach ($imagesTypes as $k => $imageType)
                        {
                            if (!imageResize($tmpName, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'jpg'), $imageType['width'], $imageType['height'], $ext))
                            {
                                exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 106, "message": "PS: An error occurred while copying image '.stripslashes($imageType['name']).'"}, "id" : "id"}');
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
                        if ($ext == 'png' && !imageResize($tmpName, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, '', 'png'), null, null, 'png'))
                        {
                            exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 106, "message": "PS: An error occurred while copying image"}, "id" : "id"}');
                        }
                        if (!imageResize($tmpName, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, '', 'jpg'), null, null, 'jpg'))
                        {
                            exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 106, "message": "PS: An error occurred while copying image"}, "id" : "id"}');
                        }
                        foreach ($imagesTypes as $k => $imageType)
                        {
                            if ($ext == 'png' && !imageResize($tmpName, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'png'), $imageType['width'], $imageType['height'], 'png'))
                            {
                                exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 106, "message": "PS: An error occurred while copying image '.stripslashes($imageType['name']).'"}, "id" : "id"}');
                            }
                            if (!imageResize($tmpName, _PS_IMG_DIR_.'p/'.getImgPath($id_product, $id_image, stripslashes($imageType['name']), 'jpg'), $imageType['width'], $imageType['height'], 'jpg'))
                            {
                                exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 106, "message": "PS: An error occurred while copying image '.stripslashes($imageType['name']).'"}, "id" : "id"}');
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
                $result_message = str_replace('#ID#', $id_image, $result_message);
                SCI::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_product));

                if (!Image::getCover($image->id_product))
                {
                    $first_img = Db::getInstance()->getRow('
                            SELECT `id_image` FROM `'._DB_PREFIX_.'image`
                            WHERE `id_product` = '.(int) $image->id_product);
                    Db::getInstance()->Execute('
                            UPDATE `'._DB_PREFIX_.'image`
                            SET `cover` = 1
                            WHERE `id_image` = '.(int) $first_img['id_image']);
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                    {
                        $sql = 'UPDATE `'._DB_PREFIX_.'image_shop` SET `cover` = 1 WHERE id_image='.(int) $first_img['id_image'].' AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true, $id_product)).')';
                        Db::getInstance()->Execute($sql);
                    }
                }

                if (!empty($attr_list))
                {
                    if ($is_multiproduct)
                    {
                        $attr_list = $cache_product_attr[$id_product];
                    }
                    if (!is_array($attr_list))
                    {
                        $attr_list = explode(',', $attr_list);
                    }
                    foreach ($attr_list as $attr)
                    {
                        if (!empty($attr))
                        {
                            $sql = 'INSERT INTO `'._DB_PREFIX_."product_attribute_image` (id_product_attribute,id_image) VALUES ('".(int) $attr."','".(int) $id_image."')";
                            Db::getInstance()->Execute($sql);
                        }
                    }
                }

                if (_s('CAT_PROD_IMG_SAVE_FILENAME'))
                {
                    $sql = 'UPDATE '._DB_PREFIX_."image SET sc_path='".psql($fileName)."' WHERE id_image = ".(int) $id_image;
                    Db::getInstance()->Execute($sql);
                }
            }
            @unlink($tmpName);

            // PM Cache
            if (!empty($id_products))
            {
                ExtensionPMCM::clearFromIdsProduct($id_products);
            }
            break;
        case 'manufacturer_logo':
            $manufacturer_list = (Tools::getValue('manufacturer_list', 0));
            $ids_manufacturer = explode(',', $manufacturer_list);
            $tmpName = $targetDir.$fileName;
            foreach ($ids_manufacturer as $id_manufacturer)
            {
                $newImageSourcePath = $targetDir.$id_manufacturer.'.jpg';
                if (file_exists(_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg'))
                {
                    @unlink(_PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg');
                }
                if (!copy($tmpName, $newImageSourcePath))
                {
                    exit('{"jsonrpc" : "2.0", "result" : null, "rror" : {"code": 106, "message": "PS: An error occurred while copying image source"}, "id" : "id"}');
                }
                else
                {
                    $images_types = ImageType::getImagesTypes('manufacturers');
                    foreach ($images_types as $k => $image_type)
                    {
                        ImageManager::resize(
                            _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
                            _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'.jpg',
                            (int) $image_type['width'],
                            (int) $image_type['height']
                        );

                        if ($generate_hight_dpi_images)
                        {
                            ImageManager::resize(
                                _PS_MANU_IMG_DIR_.$id_manufacturer.'.jpg',
                                _PS_MANU_IMG_DIR_.$id_manufacturer.'-'.stripslashes($image_type['name']).'2x.jpg',
                                (int) $image_type['width'] * 2,
                                (int) $image_type['height'] * 2
                            );
                        }
                    }
                }
            }
            @unlink($tmpName);
            break;
        case 'supplier_logo':
            $supplier_list = (Tools::getValue('supplier_list', 0));
            $ids_supplier = explode(',', $supplier_list);
            $tmpName = $targetDir.$fileName;
            foreach ($ids_supplier as $id_supplier)
            {
                $newImageSourcePath = $targetDir.$id_supplier.'.jpg';
                if (file_exists(_PS_SUPP_IMG_DIR_.$id_supplier.'.jpg'))
                {
                    @unlink(_PS_SUPP_IMG_DIR_.$id_supplier.'.jpg');
                }
                if (!copy($tmpName, $newImageSourcePath))
                {
                    exit('{"jsonrpc" : "2.0", "result" : null, "rror" : {"code": 106, "message": "PS: An error occurred while copying image source"}, "id" : "id"}');
                }
                else
                {
                    $images_types = ImageType::getImagesTypes('suppliers');
                    foreach ($images_types as $k => $image_type)
                    {
                        ImageManager::resize(
                            _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg',
                            _PS_SUPP_IMG_DIR_.$id_supplier.'-'.stripslashes($image_type['name']).'.jpg',
                            (int) $image_type['width'],
                            (int) $image_type['height']
                        );

                        if ($generate_hight_dpi_images)
                        {
                            ImageManager::resize(
                                _PS_SUPP_IMG_DIR_.$id_supplier.'.jpg',
                                _PS_SUPP_IMG_DIR_.$id_supplier.'-'.stripslashes($image_type['name']).'2x.jpg',
                                (int) $image_type['width'] * 2,
                                (int) $image_type['height'] * 2
                            );
                        }
                    }
                }
            }
            @unlink($tmpName);
            break;
        default:
            exit('{"jsonrpc" : "2.0", "result" : null, "error" : {"code": 105, "message": "Failed to create PS object."}, "id" : "id"}');
    }
    $products = Tools::getValue('product_list', 'null');
    if (!empty($products))
    {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product SET date_upd = '".pSQL(date('Y-m-d H:i:s'))."' WHERE id_product IN (".pInSQL($products).')');
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            Db::getInstance()->execute('UPDATE '._DB_PREFIX_."product_shop SET date_upd = '".pSQL(date('Y-m-d H:i:s'))."' WHERE id_product IN (".pInSQL($products).') AND id_shop IN ('.pInSQL(SCI::getSelectedShopActionList(true)).')');
        }
    }
}
exit($result_message);
