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

/**
 * Class AdminEtsAmCronjobController
 * @property Ets_affiliatemarketing $module;
 */
class AdminEtsAmCronjobController extends ModuleAdminController
{
    public function init()
    {
        parent::init();
        if (!Configuration::get('ETS_AM_CRONJOB_TOKEN')) {
            $this->module->generateTokenCronjob();
        }
        $this->bootstrap = true;
        if ((bool)Tools::isSubmit('updateCronjobSecureCode', false)) {
            if (($secure_code = Tools::getValue('secure_code')) && Validate::isCleanHtml($secure_code)) {
                Configuration::updateGlobalValue('ETS_AM_CRONJOB_TOKEN', $secure_code);
                die(json_encode(array(
                    'success' => true,
                    'message' => $this->l('Cronjob token updated successfully'),
                    'secure' => $secure_code,
                )));
            }
            die(json_encode(array(
                'success' => false,
                'message' => !$secure_code ? $this->l('Cronjob secure token is required') : $this->l('Cronjob secure token is not valid'),
            )));
        }
        if ((bool)Tools::isSubmit('close_cronjob_alert', false)) {
            $this->context->cookie->closed_alert_cronjob = 1;
            $this->context->cookie->write();
            die(json_encode(array(
                'success' => true,
                'message' => ''
            )));
        }
        if (Tools::isSubmit('ETS_AM_SAVE_LOG')) {
            $ETS_AM_SAVE_LOG = (int)Tools::getValue('ETS_AM_SAVE_LOG');
            Configuration::updateGlobalValue('ETS_AM_SAVE_LOG', $ETS_AM_SAVE_LOG);
            die(
                json_encode(array(
                    'success' => $this->l('Updated successful')
                ))
            );
        }
        if (Tools::isSubmit('clear_log')) {
            $cleared = false;
            if (file_exists(_PS_ETS_EAM_LOG_DIR_ . '/aff_cronjob.log')) {
                @unlink(_PS_ETS_EAM_LOG_DIR_ . '/aff_cronjob.log');
                $cleared = true;
            }
            die(json_encode(array(
                'success' => $cleared ? $this->l('Log cleared') : false,
                'error' => !$cleared ? $this->l('Log is empty. Nothing to do!') : false,
            )));
        }
    }

    public function postProcess()
    {
        parent::postProcess();
        if(Tools::isSubmit('etsAmRunCronjob'))
            Ets_AM::getInstance()->runCronjob();
    }
    public function renderList()
    {
        $tabActive = Tools::getValue('tabActive','cronjob_config');
        if(!in_array($tabActive,array('cronjob_config','cronjob_history')))
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminEtsAmCronjob'));
        if(Tools::isSubmit('saveCronjobSettings'))
        {
            $ETS_AM_CRONJOB_NUMBER_EMAIL = Tools::getValue('ETS_AM_CRONJOB_NUMBER_EMAIL');
            if($ETS_AM_CRONJOB_NUMBER_EMAIL=='')
                $this->module->_errors[] = $this->l('Maximum number of email sent every time cronjob file run is required');
            elseif(!Validate::isInt($ETS_AM_CRONJOB_NUMBER_EMAIL) || $ETS_AM_CRONJOB_NUMBER_EMAIL <=0)
            {
                $this->module->_errors[] = $this->l('Maximum number of email sent every time cronjob file run is not valid');
            }
            else
            {
                Configuration::updateGlobalValue('ETS_AM_CRONJOB_NUMBER_EMAIL',$ETS_AM_CRONJOB_NUMBER_EMAIL);
                $this->module->_html .= $this->module->displayConfirmation($this->l('Updated successfull'));
            }
        }
        if($tabActive=='cronjob_config')
            $this->cronjobSettings();
        elseif($tabActive=='cronjob_history')
            $this->cronjobHistory();
        $this->context->smarty->assign($this->module->getAssign($tabActive));
        return ($this->module->_errors ? $this->module->displayError($this->module->_errors): '').$this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'admin_form.tpl');
    }
    public function cronjobSettings()
    {
        $this->context->smarty->assign(array(
            'cronjob_token' => Configuration::getGlobalValue('ETS_AM_CRONJOB_TOKEN'),
            'ETS_AM_CRONJOB_NUMBER_EMAIL' => Configuration::getGlobalValue('ETS_AM_CRONJOB_NUMBER_EMAIL') ? :5,
            'cronjob_link' => $this->context->link->getAdminLink('AdminEtsAmCronjob'),
            'cronjob_dir' => _PS_MODULE_DIR_ . 'ets_affiliatemarketing/cronjob.php',
            'cronjob_demo' => Ets_AM::getBaseUrl(true) . 'cronjob.php',
            'loyaltyPrograEnabled' => Configuration::get('ETS_AM_LOYALTY_ENABLED'),
            'loyaltyRewardAvailability' => Configuration::get('ETS_AM_LOYALTY_MAX_DAY'),
            'info_cronjob' => $this->module->displayInfoRunCronJob(),
            'php_path' => (defined('PHP_BINDIR') && PHP_BINDIR && is_string(PHP_BINDIR) ? PHP_BINDIR . '/' : '') . 'php',
        ));
        $this->module->_html .= $this->module->display($this->module->getLocalPath(), 'cronjob_settings.tpl');
    }
    public function cronjobHistory()
    {
        $log_path = _PS_ETS_EAM_LOG_DIR_ . 'aff_cronjob.log';
        $log = '';
        if (file_exists($log_path)) {
            $log = Tools::file_get_contents($log_path);
        }
        $this->context->smarty->assign(array(
            'log' => $log,
            'ETS_AM_SAVE_LOG' => Configuration::getGlobalValue('ETS_AM_SAVE_LOG'),
            'post_url' => $this->context->link->getAdminLink('AdminEtsAmCronjob', true),
            'loyaltyPrograEnabled' => Configuration::get('ETS_AM_LOYALTY_ENABLED'),
            'loyaltyRewardAvailability' => Configuration::get('ETS_AM_LOYALTY_MAX_DAY'),
            'info_cronjob' => $this->module->displayInfoRunCronJob(),
        ));
        $this->module->_html = $this->module->display(_PS_MODULE_DIR_.$this->module->name.DIRECTORY_SEPARATOR.$this->module->name.'.php', 'cronjob_history.tpl');
    }
}
