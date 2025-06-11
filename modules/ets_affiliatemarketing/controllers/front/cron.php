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
 
if (!defined('_PS_VERSION_'))
    exit;
class Ets_affiliatemarketingCronModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    /** @var bool */
    public $ajax;
    public function display()
    {
        $this->ajax = 1;
        if (php_sapi_name() !== 'cli') {
            $this->ajaxDie('Forbidden call.');
        }
        $token = ($token = (string)Tools::getValue('secure', false)) && Validate::isCleanHtml($token) ? $token : '';
        $token_config = Configuration::getGlobalValue('ETS_AM_CRONJOB_TOKEN');
        if (!$token || $token !== $token_config) {
            die('Access denied');
        }
        Ets_AM::getInstance()->runCronjob();
        die('Cronjob done!');
    }
}