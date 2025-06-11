<?php

/*
 * FUNCTIONS
 * REQUIRED (with same name, same params and same return)
 */

function imageResize($sourceFile, $destFile, $destWidth = null, $destHeight = null, $fileType = 'jpg')
{
    $type_sc = _s('CAT_PROD_IMAGE_GENERATION_METHOD');
    if (!file_exists($sourceFile))
    {
        return false;
    }

    $image_size = getimagesize($sourceFile);
    if (empty($image_size))
    {
        exit(json_encode(array('error' => array(
            'code' => '201',
            'message' => _l('Copy this file is impossible.<br/>Please try to upload image from Prestashop back office.<br/>If it doesn\'t work either there is a problem with this image file.'),
            ),
        )));
    }
    else
    {
        list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($sourceFile);
    }

    if (!empty($type_sc) && $type_sc == '2')
    {
        if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
            || (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG))
        {
            $fileType = 'png';
        }
    }

    if (!$sourceWidth)
    {
        return false;
    }

    if ($type_sc != '2')
    {
        $mime = image_type_to_mime_type($type);
    }

    if ($destWidth == null)
    {
        $destWidth = $sourceWidth;
    }
    if ($destHeight == null)
    {
        $destHeight = $sourceHeight;
    }

    $sourceImage = createSrcImage($type, $sourceFile);

    ## détection rotation
    if (function_exists('exif_read_data') && $type === IMAGETYPE_JPEG)
    {
        $exif = exif_read_data($sourceFile);
        if ($exif && isset($exif['Orientation']))
        {
            $orientation = $exif['Orientation'];
            if ($orientation != 1)
            {
                $deg = 0;
                switch ($orientation) {
                    case 3:
                        $deg = 180;
                        break;
                    case 6:
                        $deg = 270;
                        break;
                    case 8:
                        $deg = 90;
                        break;
                }
                if ($deg)
                {
                    $sourceImage = imagerotate($sourceImage, $deg, 0);
                    imagejpeg($sourceImage, $sourceFile, 95);
                    $sourceImage = createSrcImage($type, $sourceFile);
                }
            }
        }
    }

    $widthDiff = $destWidth / $sourceWidth;
    $heightDiff = $destHeight / $sourceHeight;

    if ($widthDiff > 1 and $heightDiff > 1)
    {
        $nextWidth = $sourceWidth;
        $nextHeight = $sourceHeight;
    }
    else
    {
        if (!empty($type_sc) && $type_sc == '2')
        {
            if ($sourceWidth / $sourceHeight > 1)
            {
                $nextWidth = $destWidth;
                $nextHeight = round($sourceHeight * $destWidth / $sourceWidth);
                $destHeight = $nextHeight;
            }
            else
            {
                $nextHeight = $destHeight;
                $nextWidth = round(($sourceWidth * $nextHeight) / $sourceHeight);
                $destWidth = $nextWidth;
            }
        }
        elseif (!empty($type_sc) && $type_sc == '1')
        {
            if ((int) Configuration::get('PS_IMAGE_GENERATION_METHOD') == 2 or ((int) Configuration::get('PS_IMAGE_GENERATION_METHOD') == 0 and $widthDiff < $heightDiff))
            {
                $nextHeight = $destHeight;
                $nextWidth = (int) (($sourceWidth * $nextHeight) / $sourceHeight);
                $destWidth = ((int) Configuration::get('PS_IMAGE_GENERATION_METHOD') == 0 ? $destWidth : $nextWidth);
            }
            else
            {
                $nextWidth = $destWidth;
                $nextHeight = (int) ($sourceHeight * $destWidth / $sourceWidth);
                $destHeight = ((int) Configuration::get('PS_IMAGE_GENERATION_METHOD') == 0 ? $destHeight : $nextHeight);
            }
        }
        else
        {
            if ((int) Configuration::get('PS_IMAGE_GENERATION_METHOD') == 2 or ((int) Configuration::get('PS_IMAGE_GENERATION_METHOD') == 0 and $widthDiff > $heightDiff))
            {
                $nextHeight = $destHeight;
                $nextWidth = (int) (($sourceWidth * $nextHeight) / $sourceHeight);
                $destWidth = ((int) Configuration::get('PS_IMAGE_GENERATION_METHOD') == 0 ? $destWidth : $nextWidth);
            }
            else
            {
                $nextWidth = $destWidth;
                $nextHeight = (int) ($sourceHeight * $destWidth / $sourceWidth);
                $destHeight = ((int) Configuration::get('PS_IMAGE_GENERATION_METHOD') == 0 ? $destHeight : $nextHeight);
            }
        }
    }

    if (!empty($type_sc) && $type_sc == '2')
    {
        $destImage = imagecreatetruecolor($destWidth, $destHeight);

        // If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
        if ($fileType == 'png' && $type == IMAGETYPE_PNG)
        {
            imagealphablending($destImage, false);
            imagesavealpha($destImage, true);
            $transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
            imagefilledrectangle($destImage, 0, 0, $destWidth, $destHeight, $transparent);
        }
        else
        {
            $white = imagecolorallocate($destImage, 255, 255, 255);
            imagefilledrectangle($destImage, 0, 0, $destWidth, $destHeight, $white);
        }

        imagecopyresampled($destImage, $sourceImage, (int) (($destWidth - $nextWidth) / 2), (int) (($destHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);

        $final_img = (returnDestImage($fileType, $destImage, $destFile));

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
                $source = \Tinify\fromFile($destFile);
                $preservedMeta = $source->preserve('copyright', 'creation', 'location');
                $preservedMeta->toFile($destFile);
            }
            catch (Exception $e)
            {
                return $final_img;
            }
        }

        return $final_img;
    }
    else
    {
        $borderWidth = (int) (($destWidth - $nextWidth) / 2);
        $borderHeight = (int) (($destHeight - $nextHeight) / 2);

        list($rsrc, $img) = img_create_from(($fileType == 'png' ? 'image/png' : $mime), $destWidth, $destHeight, $sourceFile);

        $bgcolor_settings = explode(',', _s('CAT_PROD_IMG_RESIZE_BGCOLOR'));
        if (count($bgcolor_settings) != 3)
        {
            $bgcolor_settings = array(255, 255, 255);
        }
        $bgcolor = imagecolorallocate($rsrc, $bgcolor_settings[0], $bgcolor_settings[1], $bgcolor_settings[2]);

        switch ($fileType) {
            case 'png':
            case 'gif':
                $t_indx = imagecolortransparent($img);
                if ($t_indx > 0)
                {
                    $t_color = imagecolorsforindex($img, $t_indx);
                    $t_indx = imagecolorallocate($rsrc, $t_color['red'], $t_color['green'], $t_color['blue']);
                    imagefill($rsrc, 0, 0, $t_indx);
                    imagecolortransparent($rsrc, $t_indx);
                }
                elseif ($fileType == 'png')
                {
                    imagealphablending($rsrc, false);
                    $color = imagecolorallocatealpha($rsrc, 0, 0, 0, 127);
                    imagefill($rsrc, 0, 0, $color);
                    imagesavealpha($rsrc, true);
                }
                break;
            default:
                imagefilledrectangle($rsrc, 0, 0, $destWidth, $destHeight, $bgcolor);
        }

        @imagecopyresampled($rsrc, $img, $borderWidth, $borderHeight, 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
        $final_img = img_create_to($fileType, $rsrc, $destFile);
        @imagedestroy($rsrc);
        @imagedestroy($img);

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
                $source = \Tinify\fromFile($destFile);
                $preservedMeta = $source->preserve('copyright', 'creation', 'location');
                $preservedMeta->toFile($destFile);
            }
            catch (Exception $e)
            {
                return $final_img;
            }
        }

        return $final_img;
    }
}

/*
 * FUNCTIONS
 * NOT REQUIRED (if not used)
 */
function createSrcImage($type, $filename)
{
    switch ($type) {
        case 1:
            return imagecreatefromgif($filename);
            break;
        case 3:
            return imagecreatefrompng($filename);
            break;
        case 18:
            return imagecreatefromwebp($filename);
            break;
        case 6:
            return imagecreatefrombmp($filename);
            break;
        case 2:
        default:
            return imagecreatefromjpeg($filename);
            break;
    }
}

function returnDestImage($type, $ressource, $filename)
{
    $flag = false;
    switch ($type)
    {
        case 'gif':
            $flag = imagegif($ressource, $filename);
            break;
        case 'png':
            $flag = imagepng($ressource, $filename, _s('CAT_PROD_IMG_PNGCOMPRESS'));
            break;
        case 'jpeg':
        default:
            $flag = imagejpeg($ressource, $filename, _s('CAT_PROD_IMG_JPGCOMPRESS'));
            break;
    }
    imagedestroy($ressource);

    return $flag;
}

function img_create_from($mime, $w, $h, $from)
{
    switch ($mime) {
        case 'image/jpg':
        case 'image/jpeg':
        case 'image/jpe':
        case 'image/pjpeg':
            $rsrc = @imagecreatetruecolor($w, $h);
            $img = @imagecreatefromjpeg($from);
            break;
        case 'image/png':
        case 'image/x-png':
            $rsrc = @imagecreatetruecolor($w, $h);
            $img = @imagecreatefrompng($from);
            break;
        case 'image/gif':
            $rsrc = @imagecreate($w, $h);
            $img = @imagecreatefromgif($from);
            break;
        case 'image/webp':
            $rsrc = @imagecreate($w, $h);
            $img = @imagecreatefromwebp($from);
            break;
        case 'image/bmp':
            $rsrc = @imagecreate($w, $h);
            $img = @imagecreatefrombmp($from);
            break;
    }

    return array($rsrc, $img);
}

function img_create_to($type, $rsrc, $to)
{
    $flag = false;
    switch ($type) {
        case 'jpg':
            if (_s('CAT_PROD_IMG_JPGPROGRESSIVE'))
            {
                @imageinterlace($rsrc, 1);
            }
            $flag = imagejpeg($rsrc, $to, _s('CAT_PROD_IMG_JPGCOMPRESS'));
            break;
        case 'png':
            $flag = imagepng($rsrc, $to, _s('CAT_PROD_IMG_PNGCOMPRESS'));
            break;
        case 'gif':
            $flag = imagegif($rsrc, $to);
            break;
    }

    return $flag;
}
