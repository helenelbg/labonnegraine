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
require_once dirname(__FILE__) . '/AdminEtsAmFormController.php';
class AdminEtsAmRSController extends AdminEtsAmFormController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        if (Tools::isSubmit('submitClearQRCodeCache')) {
            $this->submitClearQRCodeCache();
        }
        if (($getLevel = Tools::getValue('getLevelInput')) && Validate::isCleanHtml($getLevel)) {
            $this->getLevelInput();
        }
        if ((bool)Tools::isSubmit('deletefileBackend', false)) {
            $this->actionDeletefileBackend();
        }
    }
    protected function submitClearQRCodeCache()
    {
        Ets_affiliatemarketing::removeDir(EAM_PATH_IMAGE_BANER . 'qrcode');
        die(
            json_encode(
                array(
                    'success' => $this->l('Cleared QR code cache successfully'),
                )
            )
        );
    }
    protected function getLevelInput()
    {
        $count = 2;
        $quit = false;
        $level_fields = array();
        while ($quit == false) {
            $level_data = Configuration::get('ETS_AM_REF_SPONSOR_COST_LEVEL_' . $count, false);
            if ($level_data !== false) {
                array_push($level_fields, array(
                    'level' => $count,
                    'value' => $level_data
                ));
                $count++;
            } else {
                $quit = true;
            }
        }
        if (!empty($level_fields)) {
            die(json_encode(
                array(
                    'success' => true,
                    'data' => $level_fields
                )
            ));
        }
        die(json_encode(
            array(
                'success' => false,
                'data' => $level_fields
            )
        ));
    }
    protected function actionDeletefileBackend()
    {
        $name_config = Tools::getValue('name_config', false);
        if ($name_config && in_array($name_config, array('ETS_AM_REF_DEFAULT_BANNER', 'ETS_AM_REF_SOCIAL_IMG', 'ETS_AM_REF_INTRO_BANNER'))) {
            $file = Configuration::get($name_config);
            if ($file) {
                $path = EAM_PATH_IMAGE_BANER . $file;
                if (file_exists($path) && @unlink($path)) {
                    Configuration::updateValue($name_config, false);
                    die(json_encode(array(
                        'success' => true,
                        'message' => 'Deleted successfully'
                    )));
                }
            }
        }
        die(json_encode(array(
            'success' => false,
            'message' => 'Can not delete this file'
        )));
    }
    public function renderList()
    {
        $tabActive = Tools::getValue('tabActive','rs_program_conditions');
        if(!in_array($tabActive,array('rs_program_conditions','rs_program_reward_caculation','rs_program_voucher','rs_program_suab','rs_program_messages')))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEtsAmRS'));
        return $this->_renderList($tabActive);
    }
}
