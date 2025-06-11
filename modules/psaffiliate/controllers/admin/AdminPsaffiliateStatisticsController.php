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

class AdminPsaffiliateStatisticsController extends AdminController
{
    public $module;
    public $dateModifier = '-90 days';

    public function __construct()
    {
        $this->moduleObj = Module::getInstanceByName('psaffiliate');
        $this->bootstrap = true;
        parent::__construct();
    }

    public function initContent()
    {
        if (Tools::getValue('action')) {
            $action = Tools::getValue('action');
            $data = array();
            if ($action == "generateChart") {
                $chart = Tools::getValue('chart');
                if (Tools::getValue('id_campaign')) {
                    $id_campaign = (int)Tools::getValue('id_campaign');
                    $extrasql_where = " AND `id_campaign`='".(int)$id_campaign."' ";
                } elseif (Tools::getValue('id_affiliate')) {
                    $id_affiliate = (int)Tools::getValue('id_affiliate');
                    $extrasql_where = " AND `id_affiliate`='".(int)$id_affiliate."' ";
                } else {
                    $extrasql_where = "";
                }

                $subtitle = "";
                if (Tools::getValue('datepickerFrom') && Tools::getValue('datepickerTo')) {
                    $subtitle = Tools::displayDate(Tools::getValue('datepickerFrom'))." - ".Tools::displayDate(Tools::getValue('datepickerTo'));
                }

                if (!$chart) {
                    $data['error'] = $this->l('No chart selected');
                } elseif ($chart == "traffic") {
                    $sql = "SELECT DATE_FORMAT(`date`, '%Y/%m/%d') `date`, COUNT(*) `count` FROM `"._DB_PREFIX_."aff_tracking` WHERE ".$this->dateBetween('date').$extrasql_where." GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`) ORDER BY `date` ASC";
                    $chart = Db::getInstance()->executeS($sql);
                    $chart_data_0 = $this->buildDays($chart);

                    $sql = "SELECT DATE_FORMAT(`date`, '%Y/%m/%d') `date`, COUNT(*) `count` FROM `"._DB_PREFIX_."aff_tracking` WHERE `unique_visit`='1' AND ".$this->dateBetween('date').$extrasql_where." GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`) ORDER BY `date` ASC";
                    $chart = Db::getInstance()->executeS($sql);
                    $chart_data_1 = $this->buildDays($chart);

                    $sql = "SELECT DATE_FORMAT(`date`, '%Y/%m/%d') `date`, SUM(`commission`) `count` FROM `"._DB_PREFIX_."aff_tracking` WHERE ".$this->dateBetween('date').$extrasql_where." GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`) ORDER BY `date` ASC";
                    $chart = Db::getInstance()->executeS($sql);
                    $chart_data_2 = $this->buildDays($chart);

                    $data = array(
                        'title' => array(
                            'text' => $this->l('Traffic'),
                        ),
                        'subtitle' => array(
                            'text' => $subtitle,
                        ),
                        'chart' => array(
                            'type' => 'spline',
                            'zoomType' => 'x',
                        ),
                        'xAxis' => array(
                            'type' => 'datetime',
                        ),
                        'yAxis' => array(
                            array(
                                'title' => array(
                                    'text' => $this->l('Visits'),
                                ),
                            ),
                            array(
                                'title' => array(
                                    'text' => $this->l('Commissions'),
                                ),
                                'labels' => array(
                                    'formatter' => 'moneyFormat',
                                ),
                                'opposite' => true,
                            ),
                        ),
                        'series' => array(
                            array(
                                'name' => $this->l('Total visits'),
                                'type' => 'spline',
                                'data' => $chart_data_0,
                                'yAxis' => 0,
                            ),
                            array(
                                'name' => $this->l('Unique visits'),
                                'type' => 'spline',
                                'data' => $chart_data_1,
                                'yAxis' => 0,
                            ),
                            array(
                                'name' => $this->l('Commissions'),
                                'type' => 'spline',
                                'data' => $chart_data_2,
                                'yAxis' => 1,
                                'tooltip' => array(
                                    'pointFormatter' => 'moneyFormat',
                                ),
                            ),
                        ),
                        'tooltip' => array(
                            'dateTimeLabelFormats' => array(
                                'day' => '%A, %b %e, %Y',
                                'hour' => '%A, %b %e, %Y',
                                'minute' => '%A, %b %e, %Y',
                                'second' => '%A, %b %e, %Y',
                            ),
                            'followPointer' => true,
                            'shared' => true,
                        ),
                    );
                } elseif ($chart == "sales") {
                    $sql = "SELECT DATE_FORMAT(`date`, '%Y/%m/%d') `date`, COUNT(*) `count` FROM `"._DB_PREFIX_."aff_sales` WHERE ".$this->dateBetween('date').$extrasql_where." GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`) ORDER BY `date` ASC";
                    $chart = Db::getInstance()->executeS($sql);
                    $chart_data_0 = $this->buildDays($chart);

                    $sql = "SELECT DATE_FORMAT(`date`, '%Y/%m/%d') `date`, SUM(o.`total_products_wt` - o.`total_discounts_tax_incl`) as `count` FROM `"._DB_PREFIX_."aff_sales` s LEFT JOIN `"._DB_PREFIX_."orders` o ON (o.`id_order`=s.`id_order`) WHERE ".$this->dateBetween('date').$extrasql_where." GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`) ORDER BY `date` ASC";
                    $chart = Db::getInstance()->executeS($sql);
                    $chart_data_1 = $this->buildDays($chart);

                    $sql = "SELECT DATE_FORMAT(`date`, '%Y/%m/%d') `date`, SUM(`commission`) `count` FROM `"._DB_PREFIX_."aff_sales` WHERE ".$this->dateBetween('date').$extrasql_where." GROUP BY YEAR(`date`), MONTH(`date`), DAY(`date`) ORDER BY `date` ASC";
                    $chart = Db::getInstance()->executeS($sql);
                    $chart_data_2 = $this->buildDays($chart);

                    $data = array(
                        'title' => array(
                            'text' => $this->l('Sales'),
                        ),
                        'subtitle' => array(
                            'text' => $subtitle,
                        ),
                        'chart' => array(
                            'type' => 'spline',
                            'zoomType' => 'x',
                        ),
                        'xAxis' => array(
                            'type' => 'datetime',
                        ),
                        'yAxis' => array(
                            array(
                                'title' => array(
                                    'text' => $this->l('Sales no.'),
                                ),
                            ),
                            array(
                                'title' => array(
                                    'text' => $this->l('Commissions'),
                                ),
                                'labels' => array(
                                    'formatter' => 'moneyFormat',
                                ),
                                'opposite' => true,
                            ),
                            array(
                                'title' => array(
                                    'text' => $this->l('Sales'),
                                ),
                                'labels' => array(
                                    'formatter' => 'moneyFormat',
                                ),
                                'opposite' => true,
                            ),
                        ),
                        'series' => array(
                            array(
                                'name' => $this->l('Sales no.'),
                                'type' => 'spline',
                                'yAxis' => 0,
                                'data' => $chart_data_0,
                                'dashStyle' => 'shortDot',
                            ),
                            array(
                                'name' => $this->l('Sales'),
                                'type' => 'spline',
                                'yAxis' => 1,
                                'data' => $chart_data_1,
                                'tooltip' => array(
                                    'pointFormatter' => 'moneyFormat',
                                ),
                            ),
                            array(
                                'name' => $this->l('Commissions'),
                                'type' => 'spline',
                                'yAxis' => 1,
                                'data' => $chart_data_2,
                                'tooltip' => array(
                                    'pointFormatter' => 'moneyFormat',
                                ),
                            ),
                        ),
                        'tooltip' => array(
                            'dateTimeLabelFormats' => array(
                                'day' => '%A, %b %e, %Y',
                                'hour' => '%A, %b %e, %Y',
                                'minute' => '%A, %b %e, %Y',
                                'second' => '%A, %b %e, %Y',
                            ),
                            'followPointer' => true,
                            'shared' => true,
                        ),
                    );
                } elseif ($chart == "affiliates_registration") {
                    $sql = "SELECT DATE_FORMAT(`date_created`, '%Y/%m/%d') `date`, COUNT(*) `count` FROM `"._DB_PREFIX_."aff_affiliates` WHERE ".$this->dateBetween('date_created').$extrasql_where." GROUP BY YEAR(`date_created`), MONTH(`date_created`), DAY(`date_created`) ORDER BY `date` ASC";
                    $chart = Db::getInstance()->executeS($sql);
                    $chart_data_0 = $this->buildDays($chart);
                    $data = array(
                        'title' => array(
                            'text' => $this->l('Affiliates registration'),
                        ),
                        'subtitle' => array(
                            'text' => $subtitle,
                        ),
                        'chart' => array(
                            'type' => 'spline',
                            'zoomType' => 'x',
                        ),
                        'xAxis' => array(
                            'type' => 'datetime',
                        ),
                        'yAxis' => array(
                            array(
                                'title' => array(
                                    'text' => $this->l('Registrations'),
                                ),
                            ),
                        ),
                        'series' => array(
                            array(
                                'name' => $this->l('Registrations'),
                                'type' => 'spline',
                                'data' => $chart_data_0,
                                'yAxis' => 0,
                            ),
                        ),
                        'tooltip' => array(
                            'dateTimeLabelFormats' => array(
                                'day' => '%A, %b %e, %Y',
                                'hour' => '%A, %b %e, %Y',
                                'minute' => '%A, %b %e, %Y',
                                'second' => '%A, %b %e, %Y',
                            ),
                            'followPointer' => true,
                            'shared' => true,
                        ),
                    );
                } else {
                    $data['error'] = $this->l('Selected chart does not exist');
                }
            }
            die(Tools::jsonEncode($data));
        }
        $display = parent::initContent();

        return $display;
    }

    public function renderList()
    {
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

        $datepickerFrom = Tools::getValue('datepickerFrom', date('Y-m-d', strtotime('-90 days')));
        $datepickerTo = Tools::getValue('datepickerTo', date('Y-m-d'));
        $this->context->smarty->assign(array(
            'datepickerFrom' => $datepickerFrom,
            'datepickerTo' => $datepickerTo,
        ));
        $display = $this->context->smarty->fetch(_PS_MODULE_DIR_.'psaffiliate/views/templates/admin/statistics.tpl');
        if (isset($script)) {
            $display .= PHP_EOL.$script;
        }

        return $display;
    }

    public function buildDays($chart, $keepLast = false)
    {
        $array = array();
        foreach ($chart as $ch) {
            $array[$ch['date']] = $ch['count'];
        }
        $chart_array = array();
        if ($chart && is_array($chart) && sizeof($chart)) {
            $datepickerFrom = Tools::getValue('datepickerFrom', date('Y-m-d', strtotime($this->dateModifier)));
            $datepickerTo = Tools::getValue('datepickerTo', date('Y-m-d'));

            $start_date = new DateTime($datepickerFrom);
            $end_date = new DateTime($datepickerTo);
            $end_date->modify('+ 1 day');

            $chart = $array;

            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($start_date, $interval, $end_date);
            if ($keepLast) {
                $lastKnownValue = false;
                $lastKnownValueArray = array();
            }
            foreach ($period as $dt) {
                $day = $dt->format("Y/m/d");
                if (isset($chart[$day])) {
                    $chart_array[$day] = (float)$chart[$day];
                    if ($keepLast) {
                        if (sizeof($lastKnownValueArray) && $lastKnownValue === false) {
                            foreach ($lastKnownValueArray as $d) {
                                $chart_array[$d] = (float)$chart[$day];
                            }
                        }
                        $lastKnownValue = (float)$chart[$day];
                    }
                } elseif (!$keepLast) {
                    $chart_array[$day] = 0;
                } else {
                    if ($lastKnownValue === false) {
                        $lastKnownValueArray[] = $day;
                    } else {
                        $chart_array[$day] = $lastKnownValue;
                    }
                }
            }
        }
        $chart_data = array();
        ksort($chart_array);
        foreach ($chart_array as $key => $c) {
            $dateTime = DateTime::createFromFormat('Y/m/d', $key);
            $chart_data[] = array('x' => $dateTime->getTimestamp() * 1000, 'y' => (float)$c);
        }

        return $chart_data;
    }

    public function dateBetween($field)
    {
        $datepickerFrom = Tools::getValue('datepickerFrom', date('Y-m-d', strtotime($this->dateModifier)));
        $datepickerTo = Tools::getValue('datepickerTo', date('Y-m-d'));

        $dateBetween = "(`".pSQL($field)."` >= '".pSQL($datepickerFrom)." 00:00:00' AND `".pSQL($field)."` <= '".pSQL($datepickerTo)." 23:59:59')";

        return $dateBetween;
    }

    public function l($string, $class = null, $addslashes = false, $htmlentities = true)
    {
        return Translate::getModuleTranslation('psaffiliate', $string, get_class($this));
    }
}
