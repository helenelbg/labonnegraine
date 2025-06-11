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
 
class Ets_aff_qr_code{
    public static function createQRCode($filename, $url) {
        $url = preg_match("#^https?\:\/\/#", $url) ? $url : "http://{$url}";
        if(!Ets_affiliatemarketing::isImageName($filename) || !Validate::isUrl($url))
            return false;
        if(file_exists(EAM_PATH_IMAGE_BANER.'qrcode/'.$filename))
            return true;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://chart.apis.google.com/chart');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "chs=200x200&cht=qr&chl=" . urlencode($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $img = curl_exec($ch);
        curl_close($ch);
        if($img) {
            if(!is_dir(EAM_PATH_IMAGE_BANER.'qrcode'))
            {
                Ets_AM::createPath(EAM_PATH_IMAGE_BANER.'qrcode');
                Tools::copy(dirname(__FILE__).'/index.php',EAM_PATH_IMAGE_BANER.'qrcode/index.php');
            }
            return @file_put_contents(EAM_PATH_IMAGE_BANER.'qrcode/'.$filename, $img);
        }
        return false;
    }
}