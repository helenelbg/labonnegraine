<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }
class Ybc_blogCapchaModuleFrontController extends ModuleFrontController
{
    public function init()
	{
		$this->create_image();
        die;
	}
    public function create_image()
    {
        $type = Tools::getValue('type','comment');
        if($type=='comment')
        {
            $security_code = $this->context->cookie->ybc_security_capcha_code;
        }
        else
            $security_code = $this->context->cookie->security_polls_capcha_code;
        $width = 100;  
        $height = 30;  
        $image = ImageCreate($width, $height);  
        $black = ImageColorAllocate($image, 27, 79, 166); 
        $noise_color = imagecolorallocate($image, 172,211,255);
        $background_color = imagecolorallocate($image, 255, 255, 255);      
        ImageFill($image,0, 0, $background_color); 
        for( $i=0; $i<($width*$height)/3; $i++ ) {
            imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
        }
        for( $i=0; $i<($width*$height)/150; $i++ ) {
            imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
        }
        ImageString($image, 5, 30, 6, $security_code, $black); 
        header("Content-Type: image/jpeg"); 
        ImageJpeg($image); 
        ImageDestroy($image); 
        exit();
    }
}