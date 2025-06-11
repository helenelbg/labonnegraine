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

class AdminPsaffiliateProductRatesController extends AdminController
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
        $display = "";
        if (Tools::getValue('submitProductCommissionRates')) {
            $this->postProcess();
        }
        $id_lang = Context::getContext()->language->id;
        $array = Db::getInstance()->executeS('SELECT p.`id_product`, p.`reference`, pl.`name`, IFNULL(pr.`rate_percent`, -1) `rate_percent`, IFNULL(pr.`rate_value`, -1) `rate_value`, IFNULL(pr.`multiplier`, 1) `multiplier` FROM `'._DB_PREFIX_.'product` p LEFT JOIN `'._DB_PREFIX_.'aff_product_rates` pr ON (pr.`id_product` = p.`id_product`) LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = "'.(int)$id_lang.'" AND pl.`id_shop` = "'.(int)$this->context->shop->id.'") ORDER BY p.`id_product`');
        $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));

        foreach ($array as &$row) {
            if (isset(Tools::getValue('rates_value')[(int)$row['id_product']])) {
                $row['rate_value'] = Tools::getValue('rates_value')[(int)$row['id_product']];
            }
            if (isset(Tools::getValue('rates_percent')[(int)$row['id_product']])) {
                $row['rate_percent'] = Tools::getValue('rates_percent')[(int)$row['id_product']];
            }
            if (isset(Tools::getValue('multiplier')[(int)$row['id_product']])) {
                $row['multiplier'] = Tools::getValue('multiplier')[(int)$row['id_product']];
            }
        }

        $this->context->smarty->assign(array(
            'products' => $array,
            'currency_iso' => $currency->iso_code,
        ));
        $display .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'psaffiliate/views/templates/admin/product_rates.tpl');

        return $display;
    }

    public function postProcess()
    {
        if (Tools::getValue('submitProductCommissionRates')) {
            $rates_value = Tools::getValue('rates_value');
            $rates_percent = Tools::getValue('rates_percent');
            $multipliers = Tools::getValue('multiplier');

            $rates_value = array_map('trim', $rates_value);
            $rates_percent = array_map('trim', $rates_percent);
            $multipliers = array_map('trim', $multipliers);

            $is_not_ok = array();

            foreach ($rates_value as $id_product => $value) {
                if (!Validate::isFloat($value)) {
                    $this->errors[] = sprintf(
                        $this->l('Invalid commision value for product with ID #%s.'),
                        $id_product
                    );
                    $is_not_ok[] = $id_product;
                }
            }

            foreach ($rates_percent as $id_product => $value) {
                if (!Validate::isFloat($value)) {
                    $this->errors[] = sprintf(
                        $this->l('Invalid commision percent for product with ID #%s.'),
                        $id_product
                    );
                    $is_not_ok[] = $id_product;
                }
            }

            foreach ($multipliers as $id_product => $value) {
                if (!Validate::isFloat($value)) {
                    $this->errors[] = sprintf($this->l('Invalid multiplier for product with ID #%s.'), $id_product);
                    $is_not_ok[] = $id_product;
                }
            }

            if (count($this->errors)) {
                //return ;
            }

            foreach (array_keys($rates_value) as $key) {
                if (in_array($key, $is_not_ok)) {
                    continue;
                }
                $array = array(
                    'id_product' => (int)$key,
                    'rate_value' => (float)str_replace(',', '.', $rates_value[$key]),
                    'rate_percent' => (float)str_replace(',', '.', $rates_percent[$key]),
                    'multiplier' => (float)str_replace(',', '.', $multipliers[$key]),
                );
                if (!Db::getInstance()->insert('aff_product_rates', $array, false, true, Db::REPLACE)) {
                    $this->errors[] = $this->l('An error occured.');
                }
            }

            $this->confirmations[] = $this->l('Settings saved.');
        }
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
