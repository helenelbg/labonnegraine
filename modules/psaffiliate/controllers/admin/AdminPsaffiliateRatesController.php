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

class AdminPsaffiliateRatesController extends AdminController
{
    public $fields_list;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->moduleObj->loadClasses('Affiliate');
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->action = 'view';
        parent::__construct();
    }

    public function initContent()
    {
        return parent::initContent();
    }

    public function renderList()
    {
        return $this->renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitCommissionRates')) {
            $names = array(
                'click' => $this->l('Per click'),
                'unique_click' => $this->l('Per unique click'),
                'sale' => $this->l('Per sale'),
                'sale_percent' => $this->l('Per sale'),
            );

            $data = array();
            foreach ($names as $name => $label) {
                if (Tools::getIsset($name)) {
                    $value = Tools::getValue($name);

                    if (!Validate::isFloat($value)) {
                        $this->errors[] = sprintf($this->l('Invalid value for "%s" field.'), $label);
                    }

                    $data[$name] = (float)$value;
                }
            }

            if (count($this->errors)) {
                return;
            }

            $old_data = $this->getGeneralRates();
            $diff = array_diff_assoc($data, $old_data);
            if (sizeof($diff)) {
                foreach ($diff as $name => $value) {
                    $data = array(
                        'id_affiliate' => 0,
                        'date' => date('Y-m-d H:i:s'),
                        'type' => $name,
                        'value' => $value,
                    );
                    Db::getInstance()->insert('aff_commission', $data);
                }
            }
        }
    }

    public function renderForm()
    {
        $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency = new Currency($id_currency);
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('General Commission Rates'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Per click'),
                    'name' => 'click',
                    'required' => true,
                    'suffix' => $currency->iso_code,
                    'class' => 'col-sm-3',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Per unique click'),
                    'name' => 'unique_click',
                    'required' => true,
                    'suffix' => $currency->iso_code,
                    'class' => 'col-sm-3',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Per sale'),
                    'name' => 'sale',
                    'required' => true,
                    'suffix' => $currency->iso_code,
                    'class' => 'col-sm-3',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Per sale'),
                    'name' => 'sale_percent',
                    'required' => true,
                    'suffix' => '%',
                    'class' => 'col-sm-3',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right',
                'name' => 'submitCommissionRates',
            ),
        );
        $this->fields_value = $this->getGeneralRates();

        $return = '';
        if (Tools::isSubmit('submitCommissionRates') && !count($this->errors)) {
            $this->module = Module::getInstanceByName('psaffiliate');
            $return .= $this->module->displayConfirmation($this->l('Success! General Commission Rates saved.'));
        }

        $return .= parent::renderForm();

        return $return;
    }

    public function getGeneralRates()
    {
        $db = Db::getInstance();
        $select = "SELECT t.`type`,t.`value` FROM (SELECT * FROM `"._DB_PREFIX_."aff_commission` WHERE `id_affiliate`='0' ORDER BY `date` DESC) t 
GROUP BY t.`type`";
        $array = $db->executeS($select);
        $return = array();
        foreach ($array as $a) {
            $return[$a['type']] = (float)$a['value'];
        }

        return $return;
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
