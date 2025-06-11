<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminPsaffiliateConfigurationController extends AdminController
{
    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->bootstrap = true;
        parent::__construct();
        $this->moduleObj->loadClasses('AffConf');
        $this->AffConf = new AffConf;
    }

    public function renderList()
    {
        $output = "";
        if (((bool)Tools::isSubmit('submitAdminPsaffiliateConfiguration')) == true) {
            $postProcess = $this->postProcess();
            if (is_bool($postProcess) && $postProcess) {
                $output .= $this->moduleObj->displayConfirmation($this->l('Settings updated'));
            } else {
                $output .= $this->moduleObj->displayError($postProcess);
            }
        }
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->module = $this->moduleObj;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAdminPsaffiliateConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminPsaffiliateConfiguration', false);
        $helper->token = Tools::getAdminTokenLite('AdminPsaffiliateConfiguration');

        $helper->tpl_vars = array(
            'fields_value' => $this->AffConf->getConfiguration(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        $AffConf = new AffConf;

        return $output.$helper->generateForm(array($AffConf->getConfigForm()));
    }

    public function postProcess()
    {
        $AffConf = new AffConf;
        $validateFields = $AffConf->validateFields();
        if (!$validateFields) {
            $form_values = AffConf::getConfiguration();
            $lang_fields = AffConf::getLangFields();
            $langs = Language::getLanguages();
            $db = Db::getInstance();
            $data = array();
            $data_lang = array();
            $i = 0;
            foreach (array_keys($form_values) as $key) {
                if (Tools::substr($key, -2) == "[]") {
                    $key = Tools::substr($key, 0, -2);
                }
                if (in_array($key, $lang_fields)) {
                    foreach ($langs as $lang) {
                        $data_lang[] = array(
                            'name' => $key,
                            'id_lang' => (int)$lang['id_lang'],
                            'value' => pSQL(Tools::getValue($key."_".(int)$lang['id_lang'])),
                        );
                    }
                } else {
                    $data[$i]['name'] = $key;
                    if (!is_array(Tools::getValue($key, null))) {
                        $data[$i]['value'] = pSQL(Tools::getValue($key, null));
                    } else {
                        $data[$i]['value'] = Tools::jsonEncode(Tools::getValue($key, null));
                    }
                }
                $i++;
            }

            return (bool)$db->insert('aff_configuration', $data, true, false, Db::REPLACE) &&
            (bool)$db->insert('aff_configuration_lang', $data_lang, true, false, Db::REPLACE);
        } else {
            return $validateFields;
        }
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
