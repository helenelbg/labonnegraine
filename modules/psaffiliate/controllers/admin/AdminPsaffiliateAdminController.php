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

class AdminPsaffiliateAdminController extends AdminController
{
    public $module;
    public $action;

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        $this->meta_title = "PS Affiliate";
    }

    public function renderList()
    {
        $this->moduleObj->loadClasses(array('Sale', 'Tracking', 'Payment', 'Affiliate', 'Campaign'));
        $this->context->controller->addJs($this->moduleObj->getPath().'views/js/admin/statistics.js');
        $this->context->controller->addJs($this->moduleObj->getPath().'views/js/admin/highcharts.min.js');
        $this->context->controller->addJqueryUi('ui.datepicker');

        $currency = new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        $js_def = array(
            'currencySign' => $currency->sign,
            'currencyFormat' => $currency->format,
            'currencyBlank' => $currency->blank,
            'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
        );
        if (method_exists('Media', 'addJsDef')) {
            Media::addJsDef($js_def);
        } else {
            $script = '<script>'.PHP_EOL;
            foreach ($js_def as $k => $v) {
                if (!is_numeric($v)) {
                    $v = '"'.$v.'"';
                }
                $script .= 'var '.$k.' = '.$v.';'.PHP_EOL;
            }
            $script .= '</script>'.PHP_EOL;
        }

        $sales = Sale::getAffiliateSales(false, 10, true);
        $traffic = Tracking::getAffiliateTraffic(false, 10, true);
        $payments = Payment::getAffiliatePayments(false, 10, true);
        $last_affiliates = Affiliate::getAffiliates(false, 10, true, true);
        $best_affiliates = Affiliate::getAffiliates(false, 10, true, true, 'overall_commission');
        $campaigns = Campaign::getCampaigns(false, 10, true, true, 'overall_commission');
        $this->context->smarty->assign(array(
            'sales' => $sales,
            'traffic' => $traffic,
            'payments' => $payments,
            'last_affiliates' => $last_affiliates,
            'best_affiliates' => $best_affiliates,
            'campaigns' => $campaigns,
            'discover_tpl' => $this->moduleObj->getDiscoverTpl(),
        ));
        $display = $this->context->smarty->fetch(_PS_MODULE_DIR_.'psaffiliate/views/templates/admin/dashboard.tpl');
        if (isset($script)) {
            $display .= PHP_EOL.$script;
        }
        parent::renderList();

        return $display;
    }
}
