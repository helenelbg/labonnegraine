<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

if (isset($_SERVER['DropBox']) && $_SERVER['DropBox']) {
    require_once(readlink(dirname($_SERVER['SCRIPT_FILENAME']).'/../../../config/config.inc.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'sonice_etiquetage.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoPDF.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoTools.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoPDF.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoTools.php');
}


class SoNiceEtiquetageGetLabel extends SoNice_Etiquetage
{

    public function __construct()
    {
        parent::__construct();

        SoColissimoContext::restore($this->context);

        if (Tools::getValue('debug') || Configuration::get(
            'SONICE_ETQ_DEBUG',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        )) {
            $this->debug = true;
        }

        if ($this->debug) {
            @ini_set('display_errors', 'on');
            @define('_PS_DEBUG_SQL_', true);
            @error_reporting(E_ALL | E_STRICT);
        }
    }


    public function l($string, $specific = false)
    {
        if (!$specific) {
            $specific = basename(__FILE__, '.php');
        }

        return parent::l($string, $specific);
    }


    public function getLabelPDF()
    {
        ob_start();

        $checkbox = Tools::getValue('checkbox');
        $data = Tools::getValue('data');
        $nature = Tools::getValue('nature');
        $conf = unserialize(Configuration::get(
            'SONICE_ETQ_CONF',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));

        if (!is_array($checkbox) && !count($checkbox)) {
            die($this->l('Impossible to retrieve the id order array.'));
        }

        $pdfs = array();
        $count = 0;

        SoColissimoTools::cleanup();

        $employee_id = new Cookie('sonice_current_employee');
//        if (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME']) {
//            $url_base = $_SERVER['REQUEST_SCHEME'].'://';
//        } else {
            $url_base = Configuration::get(
                'PS_SSL_ENABLED',
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ) ? 'https://' : 'http://';
//        }
        $url_base .= htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/sonice_etiquetage/download/';
        $base_path = _PS_MODULE_DIR_.'sonice_etiquetage/download/';

        foreach ($checkbox as $k => $id_order) {
            if (!Validate::isInt($id_order)) {
                continue;
            }

            $colissimo = new SoColissimoPDF($id_order);

            if (!$colissimo instanceof SoColissimoPDF) {
                printf('Order %d : $colissimo is not an instance of class SoColissimoPDF.<br><br>', $id_order);
                continue;
            }

            if (is_array($data) && array_key_exists($id_order, $data)) {
                if (array_key_exists('ta', $data[$id_order])) {
                    $colissimo->ta = $data[$id_order]['ta'];
                }
                if (array_key_exists('meca', $data[$id_order])) {
                    $colissimo->meca = (bool)$data[$id_order]['meca'];
                }
                if (array_key_exists('rno', $data[$id_order])) {
                    $colissimo->rno = (int)$data[$id_order]['rno'];
                }
            }

            if (is_array($nature) && array_key_exists($id_order, $nature)) {
                $colissimo->nature = $nature[$id_order];
            }

            $call = $colissimo->callWS();

            if (!$call) {
                printf('Order %d : An error occured.<br>-> %s<br>-> %s<br><br>', $id_order, $colissimo->error->errorID, $colissimo->error->error);
                continue;
            }

            $pdfs[$id_order] = $colissimo->getFormattedResponse();

            if (!$pdfs[$id_order]['errorID'] && isset($pdfs[$id_order]['parcelNumber']) && Tools::strlen($pdfs[$id_order]['parcelNumber']) >= 10) {
                $date = new DateTime();

                $sql = 'INSERT INTO '._DB_PREFIX_.'sonice_etq_label (`id_order`, `parcel_number`, `pdfurl`, `date_add`)
                        VALUES (
                            '.(int)$id_order.',
                            "'.pSQL($pdfs[$id_order]['parcelNumber']).'",
                            "'.pSQL($pdfs[$id_order]['PdfUrl']).'",
                            "'.pSQL($date->format('Y-m-d H:i:s')).'"
                        )';

                Db::getInstance()->execute($sql);

                // Set server link to Label
                $pdfs[$id_order]['PdfUrl'] = $url_base.$pdfs[$id_order]['parcelNumber'].(preg_match('/^PDF/', $conf['output_print_type']) ? '.pdf' : '.prn');
                // Set server link to CN23
                $pdfs[$id_order]['cn23'] = file_exists($base_path.$pdfs[$id_order]['parcelNumber'].'_CN23.pdf') ?
                    $url_base.$pdfs[$id_order]['parcelNumber'].'_CN23.pdf' : false;

                // Add the shipping number to the Order
                $colissimo->order->shipping_number = $pdfs[$id_order]['parcelNumber'];
                $colissimo->order->update();

                // Update tracking number in SoNice Suivi de Colis, if exists
                if (SoColissimoTools::moduleIsInstalled('sonice_suivicolis')) {
                    $tracking_exists = Db::getInstance()->getRow('
						SELECT *
						FROM `'._DB_PREFIX_.'sonice_suivicolis`
						WHERE `id_order` = '.(int)$colissimo->order->id
                    );

                    if (is_array($tracking_exists) && count($tracking_exists) && $tracking_exists['shipping_number'] != $pdfs[$id_order]['parcelNumber']) {
                        try {
                            Db::getInstance()->execute('
							UPDATE `'._DB_PREFIX_.'sonice_suivicolis`
							SET `shipping_number` = "'.pSQL($pdfs[$id_order]['parcelNumber']).'"
							WHERE `id_order` = '.(int)$colissimo->order->id
                            );
                        } catch (Exception $e) {
                            if ($this->debug) {
                                printf('Error while updating SoNice Suivi de Colis : '.$e->getMessage());
                            }
                        }
                    }
                }

                // Add the tracking number to the OrderCarrier
                if (version_compare(_PS_VERSION_, '1.5.0.4', '>')) {
                    $id_order_carrier = SoColissimoPDF::getIdOrderCarrierByOrderId($colissimo->order->id);
                    if ($id_order_carrier) {
                        $order_carrier = new OrderCarrier($id_order_carrier);
                        if (Validate::isLoadedObject($order_carrier)) {
                            $order_carrier->tracking_number = $pdfs[$id_order]['parcelNumber'];
                            if ($order_carrier->update() && isset($conf['send_mail_creation']) && $conf['send_mail_creation']) {
                                // Send mail to customer - Copied from AdminOrderController
                                $carrier = new Carrier((int)$colissimo->order->id_carrier, $colissimo->order->id_lang);
                                if (!Validate::isLoadedObject($colissimo->customer)) {
                                    throw new PrestaShopException('Can\'t load Customer object');
                                }
                                if (!Validate::isLoadedObject($carrier)) {
                                    throw new PrestaShopException('Can\'t load Carrier object');
                                }
                                $template_vars = array(
                                    '{followup}' => str_replace(
                                        '@',
                                        $colissimo->order->shipping_number,
                                        $carrier->url ? $carrier->url : 'http://www.colissimo.fr/portail_colissimo/suivreResultat.do?parcelnumber=@'
                                    ),
                                    '{firstname}' => $colissimo->customer->firstname,
                                    '{lastname}' => $colissimo->customer->lastname,
                                    '{id_order}' => $colissimo->order->id,
                                    '{shipping_number}' => $colissimo->order->shipping_number,
                                    '{order_name}' => $colissimo->order->getUniqReference()
                                );

                                if (@Mail::Send(
                                    (int)$colissimo->order->id_lang,
                                    'in_transit',
                                    Mail::l('Package in transit', (int)$colissimo->order->id_lang),
                                    $template_vars,
                                    $colissimo->customer->email,
                                    $colissimo->customer->firstname.' '.$colissimo->customer->lastname,
                                    null,
                                    null,
                                    null,
                                    null,
                                    _PS_MAIL_DIR_,
                                    true,
                                    (int)$colissimo->order->id_shop)) {
                                    Hook::exec('actionAdminOrdersTrackingNumberUpdate', array(
                                        'order' => $colissimo->order,
                                        'customer' => $colissimo->customer,
                                        'carrier' => $carrier
                                    ));
                                } else {
                                    printf('An error occurred while sending an email to the customer.');
                                }
                            }
                        }
                    }
                }

                // Pass order as "Preparation en cours"
                if (isset($conf['new_order_state_created']) && is_numeric($conf['new_order_state_created']) &&
                    (int)$employee_id->variable_name && $colissimo->order->current_state != $conf['new_order_state_created']) {
                    $colissimo->order->setCurrentState((int)$conf['new_order_state_created'], (int)$employee_id->variable_name);
                }

                /** @see Order->shipping_number */
                if (version_compare(_PS_VERSION_, '1.5.0.4', '>=')) {
                    $id_order_carrier = Db::getInstance()->getValue(
                        'SELECT `id_order_carrier` FROM '._DB_PREFIX_.'order_carrier WHERE `id_order` = '.(int)$id_order
                    );
                    $order_carrier = new OrderCarrier((int)$id_order_carrier);

                    if (Validate::isLoadedObject($order_carrier)) {
                        $order_carrier->tracking_number = $pdfs[$id_order]['parcelNumber'];
                        $order_carrier->update();
                    }
                }

                // UTF-8 ZPL CODE
                $pdfs[$id_order]['zpl_code'] = '';// iconv('UTF-8', 'UTF-8//IGNORE', $pdfs[$id_order]['zpl_code']);
            } else {
                echo $this->l('Order').' #'.$id_order.' <details><summary>'.$this->l('More details').' (XML)</summary><br><pre>'.$colissimo->xmlpp($colissimo->request, true).'</pre></details>';
                printf('Web Service<br><pre>==> errorID -> %s<br>==> error -> %s</pre>', $pdfs[$id_order]['errorID'], $pdfs[$id_order]['error']);

                if ((count($checkbox) - 1) > $k) {
                    echo '<hr>';
                }
            }

            if ((++$count) > 2) {
                sleep(2);
                break;
            }
        }

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        // Delete parasite
        if (Tools::strlen($output) < 5) {
            $output = '';
        }

        die($callback.'('.Tools::jsonEncode(array('console' => $output, 'pdfs' => $pdfs)).')');
    }
}



$label = new SoNiceEtiquetageGetLabel();
$label->getLabelPDF();
