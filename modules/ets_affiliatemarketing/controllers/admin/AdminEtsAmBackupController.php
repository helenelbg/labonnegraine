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
 * Class AdminEtsAmDashboardController
 * @property Ets_affiliatemarketing $module;
 */
class AdminEtsAmBackupController extends ModuleAdminController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
    }
    public function renderList()
    {
        $this->renderImportExportForm();
        $this->context->smarty->assign($this->module->getAssign('import_export'));
        return $this->module->display(_PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php', 'admin_form.tpl');
    }
    public function renderImportExportForm()
    {
        if (Tools::isSubmit('exportAllData', false)) {
            $export = new Ets_ImportExport();
            $export->generateArchive();
        } elseif (Tools::isSubmit('importAllData', false)) {
            $import = new Ets_ImportExport();
            $this->context->smarty->assign(
                array(
                    'restore_reward' => (int)Tools::getValue('restore_reward', false),
                    'restore_config' => (int)Tools::getValue('restore_config', false),
                    'delete_reawrd' => (int)Tools::getValue('delete_reawrd', false),
                )
            );
            $errors = $import->processImport(false,
                (int)Tools::getValue('restore_reward', false) ? true : false,
                (int)Tools::getValue('restore_config', false) ? true : false,
                (int)Tools::getValue('delete_reward', false) ? true : false
            );
            if ($errors) {
                $this->module->_html .= $this->module->displayError($errors);
            } else {
                $this->module->_html .= $this->module->displayConfirmation($this->l('Import successfully'));
            }
        }
        $this->module->_html .= $this->module->display($this->module->getLocalPath(), 'import_export.tpl');
    }
}