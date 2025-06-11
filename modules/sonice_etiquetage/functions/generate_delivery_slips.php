<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SARL SMC
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 *
 * @package   sonice_etiquetage
 * @author    debuss-a <alexandre@common-services.com>
 * @copyright Copyright(c) 2010-2015 S.A.R.L S.M.C - http://www.common-services.com
 * @license   Commercial license
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
}

require_once dirname(__FILE__).'/../classes/SoColissimoTools.php';
require_once dirname(__FILE__).'/../classes/SoColissimoContext.php';

if (version_compare(_PS_VERSION_, '1.5', '<')) {
    require_once dirname(__FILE__).'/../backward_compatibility/backward.php';
}

class DeliverySlipsGenerator
{

    public $path;
    public $url;
    /** @var Context */
    protected $context;

    public function __construct()
    {
        $this->path = _PS_MODULE_DIR_.'sonice_etiquetage/';
        $this->url = __PS_BASE_URI__.basename(_PS_MODULE_DIR_).'/sonice_etiquetage/';

        if (Tools::getValue('debug')) {
            @ini_set('display_errors', 'on');
            @define('_PS_DEBUG_SQL_', true);
            @error_reporting(E_ALL | E_STRICT);
        }

        $this->context = Context::getContext();
        SoColissimoContext::restore($this->context);
    }



    public function getDeliverySlips()
    {
        $checkbox = Tools::getValue('checkbox');
        $template = Tools::getValue('template', 'deliveries');

        if (!is_array($checkbox) || !count($checkbox)) {
            die('Impossible to retrieve the id order array.');
        }

        if (version_compare(_PS_VERSION_, '1.5.0.3', '>=')) {
            $query =
                'SELECT oi.*
                FROM `'._DB_PREFIX_.'order_invoice` oi
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = oi.`id_order`)
                WHERE o.`id_order` IN ('.pSQL(implode(', ', $checkbox)).')
                ORDER BY oi.delivery_date ASC';

            $order_invoice_list = Db::getInstance()->executeS($query);

            if (!count($order_invoice_list)) {
                die('No invoice was found.');
            }

            if ($template == 'invoices') {
                $template = PDF::TEMPLATE_INVOICE;
            } else {
                $template = PDF::TEMPLATE_DELIVERY_SLIP;
            }

            $token = Configuration::get(
                'SONICE_ETQ_TOKEN',
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            );
            if (!$token) {
                $token = md5(_COOKIE_KEY_.date('Y-m-d H:i:s'));
            }

            $pdf = new PDF(
                ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list),
                $template,
                Context::getContext()->smarty
            );
            file_put_contents($this->path.'download/delivery_slips_'.$token.'.pdf', $pdf->render(false));
        } else {
            die('This option is not available yet for this Prestashop version.');
        }

        $callback = Tools::getValue('callback');
        die($callback.'('.Tools::jsonEncode(array(
            'result' => true,
            'url' => $this->url.'download/delivery_slips_'.$token.'.pdf'
        )).')');
    }
}

$delivery_slips = new DeliverySlipsGenerator();
$delivery_slips->getDeliverySlips();
