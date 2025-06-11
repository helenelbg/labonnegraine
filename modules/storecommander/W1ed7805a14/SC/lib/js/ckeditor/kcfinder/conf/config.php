<?php


/** This file is part of KCFinder project
  *
  *      @desc Base configuration file
  *   @package KCFinder
  *   @version 3.12
  *    @author Pavel Tzonkov <sunhater@sunhater.com>
  * @copyright 2010-2014 KCFinder Project
  *   @license http://opensource.org/licenses/GPL-3.0 GPLv3
  *   @license http://opensource.org/licenses/LGPL-3.0 LGPLv3
  *      @link http://kcfinder.sunhater.com
  */

/* IMPORTANT!!! Do not comment or remove uncommented settings in this file
   even if you are using session configuration.
   See http://kcfinder.sunhater.com/install for setting descriptions */
if(!defined('SC_INSTALL_MODE')) {
    define('SC_INSTALL_MODE', 0);
}
$_CONFIG = array(

// GENERAL SETTINGS

    'disabled' => false,
    'uploadURL' => (SC_INSTALL_MODE==1?"../../../../../../../../":"../../../../../../"),
    'uploadDir' => (SC_INSTALL_MODE==1?realpath("../../../../../../../../"):realpath("../../../../../../")),
    'theme' => "default",

    'types' => array(
        'img'  =>  "pdf doc docx xls xlsx zip jpeg jpg gif bmp png"
    ),


// IMAGE SETTINGS

    'imageDriversPriority' => "imagick gmagick gd",
    'jpegQuality' => 90,
    'thumbsDir' => ".thumbs",

    'maxImageWidth' => 0,
    'maxImageHeight' => 0,

    'thumbWidth' => 100,
    'thumbHeight' => 100,

    'watermark' => "",


// DISABLE / ENABLE SETTINGS

    'denyZipDownload' => false,
    'denyUpdateCheck' => false,
    'denyExtensionRename' => false,


// PERMISSION SETTINGS

    'dirPerms' => 0755,
    'filePerms' => 0644,

    'access' => array(

        'files' => array(
            'upload' => true,
            'delete' => true,
            'copy'   => true,
            'move'   => true,
            'rename' => true
        ),

        'dirs' => array(
            'create' => true,
            'delete' => true,
            'rename' => true
        )
    ),

    'deniedExts' => "exe com msi bat cgi pl php phps phtml php3 php4 php5 php6 py pyc pyo pcgi pcgi3 pcgi4 pcgi5 pchi6",


// MISC SETTINGS

    'filenameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'dirnameChangeChars' => array(/*
        ' ' => "_",
        ':' => "."
    */),

    'mime_magic' => "",

    'cookieDomain' => "",
    'cookiePath' => "",
    'cookiePrefix' => 'KCFINDER_',


// THE FOLLOWING SETTINGS CANNOT BE OVERRIDED WITH SESSION SETTINGS

    '_normalizeFilenames' => false,
    '_check4htaccess' => false,
    '_sessionVar' => "KCFINDER",

);

## SC Debug double slash quand ajout image
if(defined('__PS_BASE_URI__') && __PS_BASE_URI__ != '') {
    $inter = __PS_BASE_URI__;
} else {
    $inter = '/';
}
if(defined('_PS_BASE_URL_SSL_') && _PS_BASE_URL_SSL_ != '') {
    $path = _PS_BASE_URL_SSL_.$inter.'img';
} elseif(defined('_PS_BASE_URL_') && _PS_BASE_URL_ != '') {
    $path = _PS_BASE_URL_.$inter.'img';
} else {
    $path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http').'://'.$_SERVER['SERVER_NAME'].$inter.'img';
}
$_CONFIG['resourceTypes'][] = array('directory'=>$path);

?>