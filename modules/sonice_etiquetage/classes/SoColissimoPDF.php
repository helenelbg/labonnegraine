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

require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoWebService.php');
require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoSession.php');
require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoNiceEtiquetageValidate.php');
require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoNiceEtiquetageHsCode.php');
require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoNiceEtiquetageSupport.php');
require_once(_PS_MODULE_DIR_.'sonice_etiquetage/classes/SoColissimoTools.php');

final class SoColissimoPDF extends SoColissimoWebService
{

    const FUNCTION_WS = 'getLetterColissimo';
    const SCE_TABLE = 'sonice_etq_label';
    const SOCOLISSIMO_TABLE = 'socolissimo_delivery_info';

    public $id_lang;
    public $path;
    public $product_list;
    public $order;
    public $customer;
    public $address;
    public $carrier;
    public $params;
    public $ta = 0;
    public $meca = 0;
    public $rno = 0;
    public $nature = 3;



    public function __construct($id_order)
    {
        parent::__construct();

        $this->customer = null;
        $this->product_list = null;
        $this->address = null;

        $this->path = _PS_MODULE_DIR_.'sonice_etiquetage/';
        $this->protocol = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
        $this->url = $this->protocol.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/sonice_etiquetage/';

        if (!is_null($id_order)) {
            $this->order = new Order((int)$id_order);
        }

        if (Validate::isLoadedObject($this->order)) {
            $this->customer = new Customer((int)$this->order->id_customer);
            $this->product_list = $this->order->getProductsDetail();
            $this->address = new Address((int)$this->order->id_address_delivery);
            $this->carrier = new Carrier((int)$this->order->id_carrier);
        }

        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $this->id_lang = (int)Context::getContext()->language->id;
            $this->context = Context::getContext();
            SoColissimoContext::restore($this->context);
        } else {
            require_once(dirname(__FILE__).'/../backward_compatibility/backward.php');

            $this->context = Context::getContext();
            $this->id_lang = isset(Context::getContext()->language) && Validate::isLoadedObject(Context::getContext()->language) ?
                (int)Context::getContext()->language->id : Configuration::get('PS_LANG_DEFAULT');
            $this->context->shop->id = null;
            $this->context->shop->id_shop_group = null;
        }
    }


    public function l($string, $specific = false, $locale = null)
    {
        if (!$specific) {
            $specific = basename(__FILE__, '.php');
        }
        return parent::l($string, $specific);
    }



    /**
     * Call Coliposte Web Service
     *
     * @return bool
     */
    public function callWS($login = null, $pwd = null, $params = null)
    {
        if (!$this->webServiceSupervision()) {
            $this->error = new StdClass();
            $this->error->errorID = 'SoColissimoPDF line '.__LINE__;
            $this->error->error = $this->l(
                'Network Supervision Failed. The Coliposte web service is currently having some trouble or is in maintenance. Please try again in a moment.'
            );

            return (false);
        }

        try {
            if (is_array($params) && count($params)) {
                $parameters = $params;
            } else {
                $parameters = $this->_getParams();
                if ($login && $pwd) {
                    $parameters['letter']['password'] = $pwd;
                    $parameters['letter']['contractNumber'] = $login;
                }
            }

            $this->response = parent::_callWS($parameters);
        } catch (Exception $e) {
            $this->error = new StdClass();
            $this->error->errorID = 'SoColissimoPDF line '.__LINE__;
            $this->error->error = sprintf('%s : %s', $e->getLine(), $e->getMessage());

            return (false);
        }

        return (true);
    }

    /**
     * Call Coliposte Web Service
     *
     * @return bool
     */
    public function callWS2($poste, $login = null, $pwd = null, $params = null)
    {
        if (!$this->webServiceSupervision()) {
            $this->error = new StdClass();
            $this->error->errorID = 'SoColissimoPDF line '.__LINE__;
            $this->error->error = $this->l(
                'Network Supervision Failed. The Coliposte web service is currently having some trouble or is in maintenance. Please try again in a moment.'
            );

            return (false);
        }

        try {
            if (is_array($params) && count($params)) {
                $parameters = $params;
            } else {
                $parameters = $this->_getParams2($poste);
                if ($login && $pwd) {
                    $parameters['letter']['password'] = $pwd;
                    $parameters['letter']['contractNumber'] = $login;
                }
            }

            $this->response = parent::_callWS($parameters);
        } catch (Exception $e) {
            $this->error = new StdClass();
            $this->error->errorID = 'SoColissimoPDF line '.__LINE__;
            $this->error->error = sprintf('%s : %s', $e->getLine(), $e->getMessage());

            return (false);
        }

        return (true);
    }



    public function getFormattedResponse()
    {
        $result = array();
        $result['errorID'] = null;
        $result['error'] = null;
        $result['PdfUrl'] = null;

        if (!$this->response instanceof SimpleXMLElement) {
            $result['errorID'] = 'SoColissimoPDF #'.__LINE__;
            $result['error'] = $this->l('The result does not have an XML format.');
            return ($result);
        }

        $elements = $this->response->xpath('//Envelope/Body/generateLabelResponse/*');

        if (!isset($elements[0]) || !isset($elements[0]->messages)) {
            // TODO get faultcode etc...
            $result['errorID'] = 'SoColissimoPDF line '.__LINE__;
            $result['error'] = 'Wrong Message';

            if (isset($elements[0]->faultcode) && isset($elements[0]->faultstring)) {
                echo '<pre>'.print_r($elements, true).'</pre>';
            }

            return ($result);
        }

        if ($elements[0]->messages->type == 'ERROR') {
            $result['errorID'] = $elements[0]->messages->id;
            $result['error'] = $elements[0]->messages->messageContent;

            $documentation_url = SoNiceEtiquetageSupport::getDocumentationLink((string)$elements[0]->messages->id);
            if ($documentation_url) {
                $result['error'] = sprintf('<a href="%s" target="_blank">%s</a>', $documentation_url, $result['error']);
            }

            return ($result);
        }

        $result['parcelNumber'] = (string)$elements[0]->labelResponse->parcelNumber;
        if ($this->demo) {
            $result['parcelNumber'] = '6A'.abs(rand(100000000000, 999999999999));
        }

        // Download label
        if (isset($elements[0]->labelResponse->pdfUrl) && Tools::strlen((string)$elements[0]->labelResponse->pdfUrl)) {
            $label = Tools::file_get_contents(str_replace('&amp;', '&', (string)$elements[0]->labelResponse->pdfUrl));
//            $label = $this->raw_response->parse()->getLabelBinary();

            if (file_put_contents($this->path.'download/'.$result['parcelNumber'].'.pdf', $label)) {
                $result['PdfUrl'] = str_replace('&amp;', '&', (string)$elements[0]->labelResponse->pdfUrl);
            }
        } elseif (isset($elements[0]->labelResponse->label) &&
            in_array($this->module_conf['output_print_type'], array('PDF_A4_300dpi', 'PDF_10x15_300dpi'))) {
            $label = $this->raw_response->parse()->getLabelBinary();

            if (!file_put_contents(dirname(__FILE__).'/../download/'.$result['parcelNumber'].'.pdf', $label)) {
                echo $this->l('ERROR, unable to save label on the server. Please check the access permission of the folder ').
                    _PS_MODULE_DIR_.'sonice_etiquetage/download/';
                ppp($this->response->asXML());
                ppp($this->raw_response);
            }
        } elseif (!isset($elements[0]->labelResponse->pdfUrl) &&
            in_array($this->module_conf['output_print_type'], array('ZPL_10x15_203dpi', 'ZPL_10x15_300dpi', 'DPL_10x15_203dpi', 'DPL_10x15_300dpi'))) {
            $label = $this->raw_response->parse()->getLabelBinary();

            if (!file_put_contents(dirname(__FILE__).'/../download/'.$result['parcelNumber'].'.prn', $label)) {
                echo $this->l('ERROR, unable to save label on the server. Please check the access permission of the folder ').
                    _PS_MODULE_DIR_.'sonice_etiquetage/download/';
                ppp($this->response->asXML());
                ppp($this->raw_response);
            }
        }

        // CN23
        if ($this->raw_response->has_cn23) {
            $cn23 = $this->raw_response->parse()->getCN23Binary();

            if (!file_put_contents(dirname(__FILE__).'/../download/'.$result['parcelNumber'].'_CN23.pdf', $cn23)) {
                echo $this->l('ERROR, unable to save CN23 on the server. Please check the access permission of the folder ').
                    _PS_MODULE_DIR_.'sonice_etiquetage/download/';
            }
        }

        // Add ZPL binary
        // TODO
        $result['zpl_code'] = $this->raw_response->label_binary;

        return ($result);
    }



    /**
     * Fill the parameters for the web service
     *
     * @return array
     */
     private function _getParams()
     {
         $date_deposite = new DateTime();

         if (!Tools::strlen($this->module_conf['ContractNumber']) || !Tools::strlen($this->module_conf['Password']) ||
             !Tools::strlen($this->module_conf['companyName']) || !Tools::strlen($this->module_conf['Line2']) ||
             !Tools::strlen($this->module_conf['PostalCode']) || !Tools::strlen($this->module_conf['City'])) {
             die($this->l('You need to configure the module with your login and address details.'));
         }

         if (Tools::strlen($this->module_conf['deposit_date'])) {
             $date_deposite->modify('+'.(int)$this->module_conf['deposit_date'].' Day');
         }

         $order_total_weight = (float)$this->order->getTotalWeight();
         $session_order_total_weight = SoColissimoSession::getOrderWeightStatic($this->order->id);
         $order_is_in_session = SoColissimoSession::isInSession($this->order->id);

         if (isset($this->module_conf['weight_unit']) && $this->module_conf['weight_unit'] == 'g') {
             $order_total_weight /= 1000;
             $session_order_total_weight /= 1000;
         }

         if ($order_is_in_session && $session_order_total_weight && $order_total_weight != $session_order_total_weight) {
             $order_total_weight = $session_order_total_weight;
         }

         if (!$order_total_weight || $order_total_weight < 0.01) {
             $order_total_weight = 0.01;
         }

         $order_total_weight = Tools::ps_round($order_total_weight, 2);

         // If order not in a session then tare was not applied
         if (!$order_is_in_session) {
             $order_total_weight = self::getTaredWeight($order_total_weight);
             SoColissimoSession::setOrderWeightStatic((int)$this->order->id, (float)$order_total_weight);
         }

         $phone_mobile = null;
         try {
             //if ($this->module_conf['compatibility']) {
                 $phone_mobile = Db::getInstance()->getValue(
                     'SELECT `cephonenumber`
                     FROM `'._DB_PREFIX_.'socolissimo_delivery_info`
                     WHERE `id_cart` = '.(int)$this->order->id_cart
                 );
             /*} else {
                 $phone_mobile = Db::getInstance()->getValue(
                     'SELECT `telephone`
                     FROM `'._DB_PREFIX_.'so_delivery`
                     WHERE `cart_id` = '.(int)$this->order->id_cart
                 );
             }*/
         } catch (Exception $excep) {
             $phone_mobile = $this->address->phone_mobile;
         }
         if ( substr($phone_mobile, 0, 2) != '06' && substr($phone_mobile, 0, 2) != '07' )
         {
             $phone_mobile = '0606060606';
         }

         $door_codes = array('code_1' => '', 'code_2' => '');
         try {
             if ($this->module_conf['compatibility']) {
                 $door_codes = array_merge(array(), Db::getInstance()->executeS(
                     'SELECT `cedoorcode1` AS "code_1", `cedoorcode2` AS "code_2"
                     FROM `'._DB_PREFIX_.'socolissimo_delivery_info`
                     WHERE `id_cart` = '.(int)$this->order->id_cart
                 ));

                 $door_codes = reset($door_codes);
             }
         } catch (Exception $excep) {
             $door_codes = array('code_1' => '', 'code_2' => '');
         }

         if (Country::getIsoById((int)$this->address->id_country) == 'FR' && !preg_match('/^(0033|\+33|0)[67](\d{2}){4}$/', $phone_mobile) || !$phone_mobile) {
             $phone_mobile = $this->address->phone_mobile;
         }

         $total_shipping_fee = version_compare(_PS_VERSION_, '1.5', '>=') ?
             $this->order->total_shipping_tax_incl : $this->order->total_shipping;

         $total_paid_tax_incl = version_compare(_PS_VERSION_, '1.5', '>=') ?
             $this->order->total_paid_tax_incl : $this->order->getTotalProductsWithTaxes();

         $line3 = Tools::strlen($this->address->address1) > 35 ? Tools::substr($this->address->address1, 35) : '';
         $state = new State($this->address->id_state);
         if (Validate::isLoadedObject($state)) {
             $state = ', '.$state->name;
         } else {
             $state = '';
         }

          // $this->updateSenderAddress();

         $params = array(
             'contractNumber' => SoNiceEtiquetageValidate::sanitize($this->module_conf['ContractNumber'], 'N6', 'contractNumber'),
             'password' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Password'], 'AN6..15', 'password'),
             'outputFormat' => array(
                 'x' => 0,
                 'y' => 0,
                 'outputPrintingType' => $this->module_conf['output_print_type'],
             ),
             'letter' => array(
                 'service' => array(
                     'productCode' => $this->_getDeliveryMode(),
                     'depositDate' => $date_deposite->format('Y-m-d'),
                     'transportationAmount' => SoNiceEtiquetageValidate::sanitize($total_shipping_fee * 100, 'N', 'transportationAmount'),
                     'totalAmount' => SoNiceEtiquetageValidate::sanitize($total_shipping_fee * 100, 'N', 'totalAmount'), // International
                     'orderNumber' => SoNiceEtiquetageValidate::sanitize($this->order->id, 'N0..30', 'orderNumber'),
                     'commercialName' => SoNiceEtiquetageValidate::sanitize($this->module_conf['companyName'], 'AN', 'commercialName'),
                     'returnTypeChoice' => $this->module_conf['returnTypeChoice'], // International
                 ),
                 'parcel' => array(
                     'insuranceValue' => $this->ta ? $this->ta : null,
                     'recommendationLevel' => $this->rno ? 'R'.$this->rno : null,
                     'weight' => SoNiceEtiquetageValidate::sanitize($order_total_weight, 'N', 'weight'),
                     'nonMachinable' => false,
                     'instructions' => SoNiceEtiquetageValidate::sanitize($this->_getInstruction(), 'AN0..70', 'instructions'),
                     'pickupLocationId' => SoNiceEtiquetageValidate::sanitize($this->_getPickupPointID(), 'N0..6', 'pickupLocationId'),
                 ),
                 'customsDeclarations' => $this->_getCustomsDeclarations(),
                 'sender' => array(
                     'senderParcelRef' => SoNiceEtiquetageValidate::sanitize('EXP'.$this->order->id, 'AN', 'senderParcelRef'),
                     'address' => array(
                         'companyName' => SoNiceEtiquetageValidate::sanitize($this->module_conf['companyName'], 'AN0..35', 'sender::companyName'),
                         'lastName' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Name'], 'A0..35', 'sender::lastName'),
                         'firstName' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Surname'], 'A0..29', 'sender::firstName'),
                         'line0' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Line0'], 'AN0..35', 'sender::line0'),
                         'line1' => null,
                         'line2' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Line2'], 'AN1..35', 'sender::line2'),
                         'line3' => null,
                         'countryCode' => array_key_exists('countryCode', $this->module_conf) && Tools::strlen($this->module_conf['countryCode']) == 2 ?
                             $this->module_conf['countryCode'] : Country::getIsoById(Configuration::get(
                                 'PS_SHOP_COUNTRY_ID',
                                 null,
                                 $this->context->shop->id_shop_group,
                                 $this->context->shop->id
                             )),
                         'city' => SoNiceEtiquetageValidate::sanitize($this->module_conf['City'], 'AN1..35', 'sender::city'),
                         'zipCode' => SoNiceEtiquetageValidate::sanitize($this->module_conf['PostalCode'], 'CP0..9', 'sender::zipCode'),
                         'phoneNumber' => SoNiceEtiquetageValidate::sanitize($this->module_conf['phoneNumber'], 'TP0..15', 'sender::phoneNumber'),
                         'mobileNumber' => null,
                         'doorCode1' => null,
                         'doorCode2' => null,
                         'email' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Mail'], 'AN0..80', 'sender::email'),
                         'intercom' => null,
                         'language' => 'FR',
                     )
                 ),
                 'addressee' => array(
                     'addresseeParcelRef' => SoNiceEtiquetageValidate::sanitize('EXP'.$this->order->id, 'AN0..15', 'addressee::addresseeParcelRef'),
                     'address' => array(
                         'companyName' => (in_array($this->_getDeliveryMode(), array('DOM', 'DOS', 'BOM', 'BOS', 'COLD', 'COL', 'COM', 'CDS', 'COLI'))) ? // TODO _getDeliveryMode() in $var - avoid double call to DB
                             SoNiceEtiquetageValidate::sanitize($this->address->company, 'AN0..35', 'addressee::companyName') : '',
                         'lastName' => SoNiceEtiquetageValidate::sanitize($this->address->lastname, 'A0..35', 'addressee::lastName'),
                         'firstName' => SoNiceEtiquetageValidate::sanitize($this->address->firstname, 'A0..35', 'addressee::firstName'),
                         'line0' => SoNiceEtiquetageValidate::sanitize($this->address->address2, 'AN0..35', 'addressee::line0'),
                         'line1' => null,
                         'line2' => SoNiceEtiquetageValidate::sanitize($this->address->address1, 'AN1..35', 'addressee::line2'),
                         'line3' => SoNiceEtiquetageValidate::sanitize($line3, 'AN0..35', 'addressee::line3'),
                         'countryCode' => SoNiceEtiquetageValidate::sanitize(Country::getIsoById((int)$this->address->id_country), 'A2', 'addressee::countryCode'),
                         'city' => SoNiceEtiquetageValidate::sanitize($this->address->city.$state, 'AN1..35', 'addressee::city'),
                         'zipCode' => SoNiceEtiquetageValidate::sanitize($this->address->postcode, 'CP0..9', 'addressee::zipCode'),
                         'phoneNumber' => SoNiceEtiquetageValidate::sanitize($this->address->phone, 'TP0..15', 'addressee::phoneNumber', Country::getIsoById((int)$this->address->id_country)),
                         'mobileNumber' => $phone_mobile
                             ? SoNiceEtiquetageValidate::sanitize($phone_mobile, 'TP0..15', 'addressee::mobileNumber', Country::getIsoById((int)$this->address->id_country))
                             : SoNiceEtiquetageValidate::sanitize($this->address->phone_mobile, 'TP0..15', 'addressee::mobileNumber', Country::getIsoById((int)$this->address->id_country)),
                         'doorCode1' => $door_codes['code_1'],
                         'doorCode2' => $door_codes['code_2'],
                         'email' => SoNiceEtiquetageValidate::sanitize($this->customer->email, 'AN0..80', 'addressee::email'),
                         'intercom' => null
                     )
                 )
             )
         );

 //		if ($this->getCodeReseau() === 'X00')
 //		{
 //			// Ce bloc sert à renseigner les informations liées à un point de retrait pour les colis dont le code réseau est X00
 //			$params['fields'] = array(
 //				'field_code' => array(
 //					'key' => 'PUDO_NETWORK_CODE',
 //					'value' => 'X00'
 //				),
 //				'field_name' => array(
 //					'key' => 'PUDO_POINT_NAME',
 //					'value' => $this->getRelayName()
 //				),
 //				'field_address1' => array(
 //					'key' => 'PUDO_POINT_ADDRESS_1',
 //					'value' => $params['letter']['addressee']['address']['line0']
 //				),
 //				'field_address2' => array(
 //					'key' => 'PUDO_POINT_ADDRESS_2',
 //					'value' => ''
 //				),
 //				'field_address3' => array(
 //					'key' => 'PUDO_POINT_ADDRESS_3',
 //					'value' => $params['letter']['addressee']['address']['line2']
 //				),
 //				'field_address4' => array(
 //					'key' => 'PUDO_POINT_ADDRESS_4',
 //					'value' => ''
 //				),
 //				'field_town' => array(
 //					'key' => 'PUDO_POINT_TOWN',
 //					'value' => $params['letter']['addressee']['address']['city']
 //				),
 //				'field_zipcode' => array(
 //					'key' => 'PUDO_POINT_ZIP_CODE',
 //					'value' => $params['letter']['addressee']['address']['zipCode']
 //				),
 //				'field_country_code' => array(
 //					'key' => 'PUDO_POINT_COUNTRY_CODE',
 //					'value' => $params['letter']['addressee']['address']['countryCode']
 //				),
 //				'field_customer_account_number' => array(
 //					'key' => 'CUSTOMER_ACCOUNT_NUMBER',
 //					'value' => ''
 //				)
 //			);
 //		}

         $this->validateParameters($params, $order_total_weight);

         return ($params);
     }

     private function _getParams2($poste)
     {
         $date_deposite = new DateTime();

         if (!Tools::strlen($this->module_conf['ContractNumber']) || !Tools::strlen($this->module_conf['Password']) ||
             !Tools::strlen($this->module_conf['companyName']) || !Tools::strlen($this->module_conf['Line2']) ||
             !Tools::strlen($this->module_conf['PostalCode']) || !Tools::strlen($this->module_conf['City'])) {
             die($this->l('You need to configure the module with your login and address details.'));
         }

         if (Tools::strlen($this->module_conf['deposit_date'])) {
             $date_deposite->modify('+'.(int)$this->module_conf['deposit_date'].' Day');
         }

         $order_total_weight = (float)$this->order->getTotalWeight();
         $session_order_total_weight = SoColissimoSession::getOrderWeightStatic($this->order->id);
         $order_is_in_session = SoColissimoSession::isInSession($this->order->id);

         if (isset($this->module_conf['weight_unit']) && $this->module_conf['weight_unit'] == 'g') {
             $order_total_weight /= 1000;
             $session_order_total_weight /= 1000;
         }

         if ($order_is_in_session && $session_order_total_weight && $order_total_weight != $session_order_total_weight) {
             $order_total_weight = $session_order_total_weight;
         }

         if (!$order_total_weight || $order_total_weight < 0.01) {
             $order_total_weight = 0.01;
         }

         $order_total_weight = Tools::ps_round($order_total_weight, 2);

         // If order not in a session then tare was not applied
         if (!$order_is_in_session) {
             $order_total_weight = self::getTaredWeight($order_total_weight);
             SoColissimoSession::setOrderWeightStatic((int)$this->order->id, (float)$order_total_weight);
         }

         $phone_mobile = null;
         try {
             //if ($this->module_conf['compatibility']) {
                 $phone_mobile = Db::getInstance()->getValue(
                     'SELECT `cephonenumber`
                     FROM `'._DB_PREFIX_.'socolissimo_delivery_info`
                     WHERE `id_cart` = '.(int)$this->order->id_cart
                 );
             /*} else {
                 $phone_mobile = Db::getInstance()->getValue(
                     'SELECT `telephone`
                     FROM `'._DB_PREFIX_.'so_delivery`
                     WHERE `cart_id` = '.(int)$this->order->id_cart
                 );
             }*/
         } catch (Exception $excep) {
             $phone_mobile = $this->address->phone_mobile;
         }
         if ( substr($phone_mobile, 0, 2) != '06' && substr($phone_mobile, 0, 2) != '07' )
         {
             $phone_mobile = '0606060606';
         }

         $door_codes = array('code_1' => '', 'code_2' => '');
         try {
             if ($this->module_conf['compatibility']) {
                 $door_codes = array_merge(array(), Db::getInstance()->executeS(
                     'SELECT `cedoorcode1` AS "code_1", `cedoorcode2` AS "code_2"
                     FROM `'._DB_PREFIX_.'socolissimo_delivery_info`
                     WHERE `id_cart` = '.(int)$this->order->id_cart
                 ));

                 $door_codes = reset($door_codes);
             }
         } catch (Exception $excep) {
             $door_codes = array('code_1' => '', 'code_2' => '');
         }

         if (Country::getIsoById((int)$this->address->id_country) == 'FR' && !preg_match('/^(0033|\+33|0)[67](\d{2}){4}$/', $phone_mobile) || !$phone_mobile) {
             $phone_mobile = $this->address->phone_mobile;
         }

         $total_shipping_fee = version_compare(_PS_VERSION_, '1.5', '>=') ?
             $this->order->total_shipping_tax_incl : $this->order->total_shipping;

         $total_paid_tax_incl = version_compare(_PS_VERSION_, '1.5', '>=') ?
             $this->order->total_paid_tax_incl : $this->order->getTotalProductsWithTaxes();

         $line3 = Tools::strlen($this->address->address1) > 35 ? Tools::substr($this->address->address1, 35) : '';
         $state = new State($this->address->id_state);
         if (Validate::isLoadedObject($state)) {
             $state = ', '.$state->name;
         } else {
             $state = '';
         }

          // $this->updateSenderAddress();

         $params = array(
             'contractNumber' => SoNiceEtiquetageValidate::sanitize($this->module_conf['ContractNumber'], 'N6', 'contractNumber'),
             'password' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Password'], 'AN6..15', 'password'),
             'outputFormat' => array(
                 'x' => 0,
                 'y' => 0,
                 'outputPrintingType' => $poste == 'controle1' ? 'DPL_10x15_203dpi' : 'ZPL_10x15_203dpi',
                 //'outputPrintingType' => $this->module_conf['output_print_type'],
             ),
             'letter' => array(
                 'service' => array(
                     'productCode' => $this->_getDeliveryMode(),
                     'depositDate' => $date_deposite->format('Y-m-d'),
                     'transportationAmount' => SoNiceEtiquetageValidate::sanitize($total_shipping_fee * 100, 'N', 'transportationAmount'),
                     'totalAmount' => SoNiceEtiquetageValidate::sanitize($total_shipping_fee * 100, 'N', 'totalAmount'), // International
                     'orderNumber' => SoNiceEtiquetageValidate::sanitize($this->order->id, 'N0..30', 'orderNumber'),
                     'commercialName' => SoNiceEtiquetageValidate::sanitize($this->module_conf['companyName'], 'AN', 'commercialName'),
                     'returnTypeChoice' => $this->module_conf['returnTypeChoice'], // International
                 ),
                 'parcel' => array(
                     'insuranceValue' => $this->ta ? $this->ta : null,
                     'recommendationLevel' => $this->rno ? 'R'.$this->rno : null,
                     'weight' => SoNiceEtiquetageValidate::sanitize($order_total_weight, 'N', 'weight'),
                     'nonMachinable' => false,
                     'instructions' => SoNiceEtiquetageValidate::sanitize($this->_getInstruction(), 'AN0..70', 'instructions'),
                     'pickupLocationId' => SoNiceEtiquetageValidate::sanitize($this->_getPickupPointID(), 'N0..6', 'pickupLocationId'),
                 ),
                 'customsDeclarations' => $this->_getCustomsDeclarations(),
                 'sender' => array(
                     'senderParcelRef' => SoNiceEtiquetageValidate::sanitize('EXP'.$this->order->id, 'AN', 'senderParcelRef'),
                     'address' => array(
                         'companyName' => SoNiceEtiquetageValidate::sanitize($this->module_conf['companyName'], 'AN0..35', 'sender::companyName'),
                         'lastName' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Name'], 'A0..35', 'sender::lastName'),
                         'firstName' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Surname'], 'A0..29', 'sender::firstName'),
                         'line0' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Line0'], 'AN0..35', 'sender::line0'),
                         'line1' => null,
                         'line2' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Line2'], 'AN1..35', 'sender::line2'),
                         'line3' => null,
                         'countryCode' => array_key_exists('countryCode', $this->module_conf) && Tools::strlen($this->module_conf['countryCode']) == 2 ?
                             $this->module_conf['countryCode'] : Country::getIsoById(Configuration::get(
                                 'PS_SHOP_COUNTRY_ID',
                                 null,
                                 $this->context->shop->id_shop_group,
                                 $this->context->shop->id
                             )),
                         'city' => SoNiceEtiquetageValidate::sanitize($this->module_conf['City'], 'AN1..35', 'sender::city'),
                         'zipCode' => SoNiceEtiquetageValidate::sanitize($this->module_conf['PostalCode'], 'CP0..9', 'sender::zipCode'),
                         'phoneNumber' => SoNiceEtiquetageValidate::sanitize($this->module_conf['phoneNumber'], 'TP0..15', 'sender::phoneNumber'),
                         'mobileNumber' => null,
                         'doorCode1' => null,
                         'doorCode2' => null,
                         'email' => SoNiceEtiquetageValidate::sanitize($this->module_conf['Mail'], 'AN0..80', 'sender::email'),
                         'intercom' => null,
                         'language' => 'FR',
                     )
                 ),
                 'addressee' => array(
                     'addresseeParcelRef' => SoNiceEtiquetageValidate::sanitize('EXP'.$this->order->id, 'AN0..15', 'addressee::addresseeParcelRef'),
                     'address' => array(
                         'companyName' => (in_array($this->_getDeliveryMode(), array('DOM', 'DOS', 'BOM', 'BOS', 'COLD', 'COL', 'COM', 'CDS', 'COLI'))) ? // TODO _getDeliveryMode() in $var - avoid double call to DB
                             SoNiceEtiquetageValidate::sanitize($this->address->company, 'AN0..35', 'addressee::companyName') : '',
                         'lastName' => SoNiceEtiquetageValidate::sanitize($this->address->lastname, 'A0..35', 'addressee::lastName'),
                         'firstName' => SoNiceEtiquetageValidate::sanitize($this->address->firstname, 'A0..35', 'addressee::firstName'),
                         'line0' => SoNiceEtiquetageValidate::sanitize($this->address->address2, 'AN0..35', 'addressee::line0'),
                         'line1' => null,
                         'line2' => SoNiceEtiquetageValidate::sanitize($this->address->address1, 'AN1..35', 'addressee::line2'),
                         'line3' => SoNiceEtiquetageValidate::sanitize($line3, 'AN0..35', 'addressee::line3'),
                         'countryCode' => SoNiceEtiquetageValidate::sanitize(Country::getIsoById((int)$this->address->id_country), 'A2', 'addressee::countryCode'),
                         'city' => SoNiceEtiquetageValidate::sanitize($this->address->city.$state, 'AN1..35', 'addressee::city'),
                         'zipCode' => SoNiceEtiquetageValidate::sanitize($this->address->postcode, 'CP0..9', 'addressee::zipCode'),
                         'phoneNumber' => SoNiceEtiquetageValidate::sanitize($this->address->phone, 'TP0..15', 'addressee::phoneNumber', Country::getIsoById((int)$this->address->id_country)),
                         'mobileNumber' => $phone_mobile
                             ? SoNiceEtiquetageValidate::sanitize($phone_mobile, 'TP0..15', 'addressee::mobileNumber', Country::getIsoById((int)$this->address->id_country))
                             : SoNiceEtiquetageValidate::sanitize($this->address->phone_mobile, 'TP0..15', 'addressee::mobileNumber', Country::getIsoById((int)$this->address->id_country)),
                         'doorCode1' => $door_codes['code_1'],
                         'doorCode2' => $door_codes['code_2'],
                         'email' => SoNiceEtiquetageValidate::sanitize($this->customer->email, 'AN0..80', 'addressee::email'),
                         'intercom' => null
                     )
                 )
             )
         );

 //		if ($this->getCodeReseau() === 'X00')
 //		{
 //			// Ce bloc sert à renseigner les informations liées à un point de retrait pour les colis dont le code réseau est X00
 //			$params['fields'] = array(
 //				'field_code' => array(
 //					'key' => 'PUDO_NETWORK_CODE',
 //					'value' => 'X00'
 //				),
 //				'field_name' => array(
 //					'key' => 'PUDO_POINT_NAME',
 //					'value' => $this->getRelayName()
 //				),
 //				'field_address1' => array(
 //					'key' => 'PUDO_POINT_ADDRESS_1',
 //					'value' => $params['letter']['addressee']['address']['line0']
 //				),
 //				'field_address2' => array(
 //					'key' => 'PUDO_POINT_ADDRESS_2',
 //					'value' => ''
 //				),
 //				'field_address3' => array(
 //					'key' => 'PUDO_POINT_ADDRESS_3',
 //					'value' => $params['letter']['addressee']['address']['line2']
 //				),
 //				'field_address4' => array(
 //					'key' => 'PUDO_POINT_ADDRESS_4',
 //					'value' => ''
 //				),
 //				'field_town' => array(
 //					'key' => 'PUDO_POINT_TOWN',
 //					'value' => $params['letter']['addressee']['address']['city']
 //				),
 //				'field_zipcode' => array(
 //					'key' => 'PUDO_POINT_ZIP_CODE',
 //					'value' => $params['letter']['addressee']['address']['zipCode']
 //				),
 //				'field_country_code' => array(
 //					'key' => 'PUDO_POINT_COUNTRY_CODE',
 //					'value' => $params['letter']['addressee']['address']['countryCode']
 //				),
 //				'field_customer_account_number' => array(
 //					'key' => 'CUSTOMER_ACCOUNT_NUMBER',
 //					'value' => ''
 //				)
 //			);
 //		}

         $this->validateParameters($params, $order_total_weight);

         return ($params);
     }

    private function updateSenderAddress()
    {
        $products = $this->order->getProducts();

        if (!is_array($products) || !count($products)) {
            return false;
        }

        $product = reset($products);
        $id_warehouse = array_key_exists('id_warehouse', $product) ?
            $product['id_warehouse'] : 0;

        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $id_warehouse) {
            $warehouse = new Warehouse($id_warehouse) ;

            if (Validate::isLoadedObject($warehouse)) {
                if ($id_address = (int)$warehouse->id_address) {
                    $address = new Address($id_address);

                    if (Validate::isLoadedObject($address)) {
                        $this->module_conf['Line2'] = $address->address1;
                        $this->module_conf['Line0'] = $address->address2;
                        $this->module_conf['countryCode'] = Country::getIsoById($address->id_country);
                        $this->module_conf['city'] = $address->city;
                        $this->module_conf['zipCode'] = $address->postcode;
                        $this->module_conf['phoneNumber'] = $address->phone;
                        $this->module_conf['mobileNumber'] = $address->phone_mobile;
                    }
                }
            }
        }

        return true;
    }

    private function getCodeReseau()
    {
        if ($this->module_conf['compatibility']) {
            return Db::getInstance()->getValue(
                'SELECT `codereseau`
				FROM `'._DB_PREFIX_.'socolissimo_delivery_info`
				WHERE `id_cart` = '.(int)$this->order->id_cart);
        }

        return Db::getInstance()->getValue(
            'SELECT `codereseau`
			FROM `'._DB_PREFIX_.'so_delivery`
			WHERE `cart_id` = '.(int)$this->order->id_cart
        );
    }

    private function getRelayName()
    {
        if ($this->module_conf['compatibility']) {
            return Db::getInstance()->getValue(
                'SELECT `prname`
				FROM `'._DB_PREFIX_.'socolissimo_delivery_info`
				WHERE `id_cart` = '.(int)$this->order->id_cart);
        }

        return Db::getInstance()->getValue(
            'SELECT `libelle`
			FROM `'._DB_PREFIX_.'so_delivery`
			WHERE `cart_id` = '.(int)$this->order->id_cart
        );
    }


    private function validateParameters(&$params, &$order_total_weight)
    {
        // Check Monaco
        if (isset($params['letter']['addressee']['address']['city']) && Tools::strtolower($params['letter']['addressee']['address']['city']) == 'monaco' &&
            Tools::strtolower($params['letter']['addressee']['address']['countryCode']) != 'mc') {
            $params['letter']['addressee']['address']['countryCode'] = 'MC';
        }

        // Pass Europe delivery to DOS not DOM
        if (isset($params['letter']['service']['productCode']) && !in_array($params['letter']['service']['productCode'], array('COLI', 'COM', 'CDS', 'CMT')) &&
            in_array(Tools::strtoupper($params['letter']['addressee']['address']['countryCode']), array('DE', 'ES', 'LU', 'GB', 'NL'))) {
            $params['letter']['service']['productCode'] = 'DOS';
        }

        // Pass Europe delivery to DOS not COLI
        if (isset($params['letter']['service']['productCode']) && $params['letter']['service']['productCode'] == 'COLI' &&
            in_array(Tools::strtoupper($params['letter']['addressee']['address']['countryCode']), array('DE', 'ES', 'LU', 'GB', 'NL'))) {

            $params['letter']['service']['productCode'] = 'DOS';

        }

        // Check delivery country to switch to COLI
        if (isset($params['letter']['service']['productCode']) && !in_array($params['letter']['service']['productCode'], array('COLI', 'COM', 'CDS', 'CMT')) && // CMT here because new Europe API
            !in_array(Tools::strtoupper($params['letter']['addressee']['address']['countryCode']), array('FR', 'BE', 'MC', 'AD', 'DE', 'ES', 'LU', 'GB', 'NL'))) {
            if (in_array($params['letter']['service']['productCode'], array('DOM', 'DOS')) &&
                in_array(Tools::strtoupper($params['letter']['addressee']['address']['countryCode']), array('RE', 'GP', 'GY', 'MQ', 'YT', 'SM', 'BL', 'PM', 'PF', 'NC', 'WF'))) {
                // Do Nothing
            } elseif (in_array(Tools::strtoupper($params['letter']['addressee']['address']['countryCode']), array('RE', 'GP', 'GY', 'MQ', 'YT', 'SM', 'BL', 'PM', 'PF', 'NC', 'WF'))) {
                $params['letter']['service']['productCode'] = 'COM';
            } else {
                $params['letter']['service']['productCode'] = 'COLI';
            }
        }

        // If COLI and no shipping fee then set it to 1EUR
        if (isset($params['letter']['service']['productCode']) && $params['letter']['service']['productCode'] == 'COLI') {

            $params['letter']['service']['transportationAmount'] = max(100, (int)$params['letter']['service']['transportationAmount']);
            $params['letter']['service']['totalAmount'] = max(100, (int)$params['letter']['service']['totalAmount']);

        }

        // Double check COM
        if (isset($params['letter']['service']['productCode']) && in_array($params['letter']['service']['productCode'], array('COLI')) &&
            in_array(Tools::strtoupper($params['letter']['addressee']['address']['countryCode']), array('RE', 'GP', 'GY', 'MQ', 'YT', 'SM', 'BL', 'PM', 'PF', 'NC', 'WF'))) {
            $params['letter']['service']['productCode'] = 'COM';
        }

        // Check delivery mode CDI / ACP
        if (isset($params['letter']['service']['productCode']) && in_array($params['letter']['service']['productCode'], array('CDI', 'ACP'))) {
            $params['letter']['service']['productCode'] = 'BPR';
        }

        // Check total weight = SUM(article_weight * article_quantity)
        if (isset($params['letter']['customsDeclarations']['contents'])) {
            $tmp_total_article_weight = 0;
            foreach ($params['letter']['customsDeclarations']['contents'] as $article) {
                if (isset($article['weight']) && isset($article['quantity'])) {
                    $tmp_total_article_weight += (float)$article['weight'] * $article['quantity'];
                }
            }

            if (isset($this->module_conf['weight_unit']) && $this->module_conf['weight_unit'] == 'g') {
                $tmp_total_article_weight /= 1000;
            }

            if ($order_total_weight < $tmp_total_article_weight) {
                $params['letter']['parcel']['weight'] = (float)$tmp_total_article_weight;
                SoColissimoSession::setOrderWeightStatic((int)$this->order->id, (float)$tmp_total_article_weight);
            }
        }

        // Check belgium delivery (BOS / BOM / BDP / CMT)
        if (isset($params['letter']['addressee']['address']['countryCode']) && $params['letter']['addressee']['address']['countryCode'] == 'BE' && in_array($params['letter']['service']['productCode'], array('DOM', 'DOS', 'BPR', 'A2P'))) {
            if ($params['letter']['service']['productCode'] == 'DOM') {
                $params['letter']['service']['productCode'] = 'BOM';
            } elseif ($params['letter']['service']['productCode'] == 'DOS') {
                $params['letter']['service']['productCode'] = 'BOS';
            } elseif ($params['letter']['service']['productCode'] == 'BPR') {
                $params['letter']['service']['productCode'] = 'BDP';
            } elseif ($params['letter']['service']['productCode'] == 'A2P') {
                $params['letter']['service']['productCode'] = 'CMT';
            }
        }

        // Lastname missing
        // Marketplace order for instance
        if (!isset($params['letter']['addressee']['address']['lastName']) || Tools::strlen($params['letter']['addressee']['address']['lastName']) < 2) {
            if (strpos($params['letter']['addressee']['address']['firstName'], ' co ')) {
                $full_name = explode('co', $params['letter']['addressee']['address']['firstName']);
                $full_name = explode(' ', reset($full_name));
                $params['letter']['addressee']['address']['firstName'] = array_shift($full_name);
                $params['letter']['addressee']['address']['lastName'] = implode(' ', $full_name);
            } else {
                $full_name = explode(' ', $params['letter']['addressee']['address']['firstName']);
                $params['letter']['addressee']['address']['firstName'] = array_shift($full_name);
                $params['letter']['addressee']['address']['lastName'] = implode(' ', $full_name);
            }
        }
        // Firstname missing
        if (!isset($params['letter']['addressee']['address']['firstName']) || Tools::strlen($params['letter']['addressee']['address']['firstName']) < 2) {
            if (strpos($params['letter']['addressee']['address']['lastName'], ' co ')) {
                $full_name = explode('co', $params['letter']['addressee']['address']['lastName']);
                $full_name = explode(' ', reset($full_name));
                $params['letter']['addressee']['address']['lastName'] = array_shift($full_name);
                $params['letter']['addressee']['address']['firstName'] = implode(' ', $full_name);
            } else {
                $full_name = explode(' ', $params['letter']['addressee']['address']['lastName']);
                $params['letter']['addressee']['address']['lastName'] = array_shift($full_name);
                $params['letter']['addressee']['address']['firstName'] = implode(' ', $full_name);
            }
        }
    }


    private function _getCustomsDeclarations()
    {
        $customs_declarations = null;
        $iso_country = Country::getIsoById((int)$this->address->id_country);
        $iso_country_europe = array(
            'FR', 'BE', 'NL', 'DE', 'GB', 'LU', 'ES', 'PT', 'AT', 'CZ', 'HU', 'SK', 'SI', 'LT', 'LV', 'EE', 'CH'
        );
        $is_europe_delivery = false;

        if (in_array($iso_country, array('FR', 'BE', 'MC'))) {
            return ($customs_declarations);
        }

        if (in_array($iso_country, $iso_country_europe)) {
            $is_europe_delivery = true;
        }

        $articles = array();
        foreach ($this->order->getProducts() as $id_product => $product) {
            if ($product['product_price'] == 0) {
                $product['product_price'] = 1;
            } elseif (!$is_europe_delivery) {
                // For Europe we send price without tax
                // For WorldWide we send price with tax
                // Because of some stuff for accountancy
                $product['product_price'] = $product['total_price_tax_incl'];
            }

            $articles[$id_product] = array(
                'description' => SoNiceEtiquetageValidate::sanitize(Tools::substr(strip_tags($product['product_name']), 0, 63), 'AN1..64'),
                'quantity' => (int)SoNiceEtiquetageValidate::sanitize($product['product_quantity'], 'N'),
                'weight' => ($product['product_weight'] >= 0.01) ? (float)SoNiceEtiquetageValidate::sanitize($product['product_weight'], 'N') : 0.01,
                'value' => (float)SoNiceEtiquetageValidate::sanitize($product['product_price'], 'N'),
                'hsCode' => SoNiceEtiquetageHsCode::getProductHsCode($product['product_id']),
                'originCountry' => 'FR'
            );

            if (isset($this->module_conf['weight_unit']) && $this->module_conf['weight_unit'] == 'g' && $articles[$id_product]['weight'] != 0.01) {
                $articles[$id_product]['weight'] /= 1000;
            }
        }

        $articles['category'] = array(
            'value' => $this->nature
        );

        $invoice_format = '%1$s%2$06d';

        if (Configuration::get(
            'PS_INVOICE_USE_YEAR',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        )) {
            $invoice_format = Configuration::get(
                'PS_INVOICE_YEAR_POS',
                null,
                $this->context->shop->id_shop_group,
                $this->context->shop->id
            ) ? '%1$s%3$s/%2$06d' : '%1$s%2$06d/%3$s';
        }

        $customs_declarations = array(
            'includeCustomsDeclarations' => true,
            'contents' => $articles,
            'invoiceNumber' => sprintf(
                $invoice_format,
                Configuration::get(
                    'PS_INVOICE_PREFIX',
                    null,
                    $this->context->shop->id_shop_group,
                    $this->context->shop->id
                ),
                $this->order->invoice_number
            ),
            'licenceNumber' => null,
            'certificatNumber' => null
        );

        return ($customs_declarations);
    }



    private function _getInstruction()
    {
        if ($this->module_conf['compatibility']) {
            $instruction = @Db::getInstance()->getValue(
                'SELECT `cedeliveryinformation`
				FROM '._DB_PREFIX_.self::SOCOLISSIMO_TABLE.'
				WHERE `id_cart` = '.(int)$this->order->id_cart
            );
        } else {
            $instruction = @Db::getInstance()->getValue(
                'SELECT `informations`
				FROM '._DB_PREFIX_.'so_delivery
				WHERE `cart_id` = '.(int)$this->order->id_cart
            );
        }

        if ($this->address->other) {
            $instruction .= '|'.$this->address->other;
        }

        if (!$instruction) {
            return ('');
        }

        $instruction = str_replace(array('|', ';', '¤', 'Æ', 'æ'), '', Tools::substr($instruction, 0, 69));
        $instruction = preg_replace('/(&.*;)/', '', $instruction);
        $instruction = trim(strip_tags($instruction));

        return ($instruction);
    }



    /**
     * Return the delivery mode
     *
     * @return string
     */
    private function _getDeliveryMode()
    {
        if ($this->module_conf['compatibility']) {
            $delivery_mode = Db::getInstance()->getValue(
                'SELECT `delivery_mode`
				FROM `'._DB_PREFIX_.self::SOCOLISSIMO_TABLE.'`
				WHERE `id_cart` = '.(int)$this->order->id_cart.'
				AND `id_customer` = '.(int)$this->order->id_customer
            );
        } else {
            $delivery_mode = Db::getInstance()->getValue(
                'SELECT `type`
				FROM `'._DB_PREFIX_.'so_delivery`
				WHERE `cart_id` = '.(int)$this->order->id_cart.'
				AND `customer_id` = '.(int)$this->order->id_customer
            );
        }

        // Module Colissimo Simplicite has only 1 carrier for both home and pickup delivery
        // So no need to go further, return the delivery mode from Db.
        if ($delivery_mode && in_array($this->carrier->external_module_name, array('socolissimo', 'soflexibilite', 'soliberte')) &&
            SoColissimoTools::moduleIsEnabled($this->carrier->external_module_name)) {
            return $delivery_mode;
        }

        $carrier_conf_mapping = unserialize(Configuration::get(
            'SONICE_ETQ_CARRIER_MAPPING',
            null,
            $this->context->shop->id_shop_group,
            $this->context->shop->id
        ));

        $delivery_mode = 'DOS';

        if (array_key_exists($this->carrier->id, $carrier_conf_mapping) && $carrier_conf_mapping[$this->carrier->id]) {
            $delivery_mode = $carrier_conf_mapping[$this->order->id_carrier];
        } else {
            $latest_id_carrier = (int)Db::getInstance()->getValue(
                'SELECT `id_carrier`
                FROM `'._DB_PREFIX_.'carrier`
                WHERE `id_reference` IN (
                    SELECT `id_reference`
                    FROM `'._DB_PREFIX_.'carrier`
                    WHERE `id_carrier` = '.(int)$this->carrier->id.'
                )
                AND `id_carrier` != '.(int)$this->carrier->id.'
                ORDER BY `id_carrier` DESC'
            );

            if (array_key_exists($latest_id_carrier, $carrier_conf_mapping) &&
                $carrier_conf_mapping[$latest_id_carrier]) {
                $delivery_mode = $carrier_conf_mapping[$latest_id_carrier];
            }
        }

        if (in_array($delivery_mode, array('CDI', 'ACP'))) {
            return 'BPR';
        }

//        if (!$delivery_mode) {
//            if (array_key_exists($this->order->id_carrier, $carrier_conf_mapping) && $carrier_conf_mapping[$this->order->id_carrier]) {
//                return ($carrier_conf_mapping[$this->order->id_carrier]);
//            } else {
//                $latest_id_carrier = (int)Db::getInstance()->getValue(
//                    'SELECT `id_carrier`
//					FROM `'._DB_PREFIX_.'carrier`
//					WHERE `id_reference` IN (
//						SELECT `id_reference`
//						FROM `'._DB_PREFIX_.'carrier`
//						WHERE `id_carrier` = '.(int)$this->order->id_carrier.'
//					)
//					AND `id_carrier` != '.(int)$this->order->id_carrier.'
//					ORDER BY `id_carrier` DESC
//				');
//
//                if (array_key_exists($latest_id_carrier, $carrier_conf_mapping) && $carrier_conf_mapping[$latest_id_carrier]) {
//                    return $carrier_conf_mapping[$latest_id_carrier];
//                }
//            }
//
//            return ('DOM');
//        } elseif (array_key_exists($this->order->id_carrier, $carrier_conf_mapping) && $carrier_conf_mapping[$this->order->id_carrier]) {
//            return $carrier_conf_mapping[$this->order->id_carrier];
//        }
//
//        if (in_array($delivery_mode, array('CDI')))
//            ; // TODO

        if (!$delivery_mode) {
            $delivery_mode = 'DOS';
        }

        return ($delivery_mode);
    }



    /**
     * Return the pickup point ID
     *
     * @return string
     */
    private function _getPickupPointID()
    {
        $prid = 0;

        if ($this->module_conf['compatibility']) {
            $prid = (int)@Db::getInstance()->getValue(
                'SELECT `prid`
				FROM '._DB_PREFIX_.self::SOCOLISSIMO_TABLE.'
				WHERE `id_cart` = '.(int)$this->order->id_cart.'
				AND `id_customer` = '.(int)$this->order->id_customer
            );
        } else {
            $prid = (int)@Db::getInstance()->getValue(
                'SELECT `point_id`
				FROM '._DB_PREFIX_.'so_delivery
				WHERE `cart_id` = '.(int)$this->order->id_cart.'
				AND `customer_id` = '.(int)$this->order->id_customer
            );
        }

        if (!$prid && Tools::strtolower($this->order->payment) == 'cdiscount' &&
            (int)filter_var($this->address->other, FILTER_SANITIZE_NUMBER_INT)) {
            $prid = sprintf('%06s', filter_var($this->address->other, FILTER_SANITIZE_NUMBER_INT));
        }

        if (!$prid) {
            return ('');
        }

        return (sprintf('%06d', $prid));
    }


    /**
     * Get the orders in an interval of 30 days
     *
     * @param bool $detail Add more informations about customer, address, carrier than just the ID
     * @param string $offset
     * @param string $row
     * @param bool $reversed
     * @return array|bool
     * @throws PrestaShopDatabaseException
     * @see $row http://dev.mysql.com/doc/refman/5.0/en/select.html "To retrieve all rows from a certain offset up to the end of the result set, you can use some large number for the second parameter."
     */
    public static function getOrders($detail = false, $offset = '0', $row = '18446744073709551615', $reversed = false)
    {
        $carrier_conf = array_filter(array_merge(array(0), (array)unserialize(Configuration::get('SONICE_ETQ_CARRIER'))));
        $status_conf = array_filter(array_merge(array(0), (array)unserialize(Configuration::get('SONICE_ETQ_STATUS'))));

        if (!$carrier_conf || !is_array($carrier_conf) || !$status_conf || !is_array($status_conf)) {
            return false;
        }

        $id_shop_filter = '';
        if (version_compare(_PS_VERSION_, '1.5', '>=') && isset(Context::getContext()->shop) &&
            Validate::isLoadedObject(Context::getContext()->shop)) {
            $id_shop_filter = ' AND o.`id_shop` = '.(int)Context::getContext()->shop->id;
        }

        $sql = 'SELECT SQL_CALC_FOUND_ROWS o.`id_order`, o.`id_carrier`, o.`id_customer`, o.`id_address_delivery`, o.`date_add`, oh.`id_order_state`
                FROM '._DB_PREFIX_.'orders AS o
                LEFT JOIN `'._DB_PREFIX_.'order_history` AS oh ON (o.`id_order` = oh.`id_order`)
                WHERE oh.`date_add` = (
                    SELECT MAX(`date_add`)
                    FROM `'._DB_PREFIX_.'order_history` oh2
                    WHERE o.`id_order` = oh2.`id_order`
                )
                AND o.`date_add` > DATE_ADD(NOW(), INTERVAL - 30 DAY)
                AND o.`id_carrier` IN ('.pSQL(implode(', ', $carrier_conf)).')
                AND oh.`id_order_history` = ( -- In case of 2 status were saved at the same time and we fetch order whereas status is not good
                	SELECT MAX(`id_order_history`)
                	FROM `'._DB_PREFIX_.'order_history` oh3
                	WHERE o.`id_order` = oh3.`id_order`
                )
                AND oh.`id_order_state` IN ('.pSQL(implode(', ', $status_conf)).')
                '.$id_shop_filter.'
                GROUP BY o.`id_order` ORDER BY oh.`id_order` '.($reversed ? '' : 'DESC').' LIMIT '.(int)$offset.', '.(int)$row;

        $result = Db::getInstance()->executeS($sql);
        $total = Db::getInstance()->getValue('SELECT FOUND_ROWS()');

        if ($detail && is_array($result) && count($result)) {
            foreach ($result as $key => $order) {
                if (!isset($order['id_carrier']) || !isset($order['id_customer']) ||
                    !isset($order['id_address_delivery']) || !isset($order['id_order'])) {
                    continue;
                }

                $tmp_order = new Order((int)$order['id_order']);
                if (!Validate::isLoadedObject($tmp_order) || $tmp_order->isVirtual()) {
                    continue;
                }

                $carrier = new Carrier((int)$order['id_carrier']);
                if (Validate::isLoadedObject($carrier)) {
                    $result[$key]['carrier_name'] = $carrier->name;
                }

                $customer = new Customer((int)$order['id_customer']);
                if (Validate::isLoadedObject($customer)) {
                    $result[$key]['customer_firstname'] = $customer->firstname;
                    $result[$key]['customer_lastname'] = $customer->lastname;
                }

                $address = new Address((int)$order['id_address_delivery']);
                if (Validate::isLoadedObject($address)) {
                    $result[$key]['address_alias'] = $address->alias;
                    $result[$key]['address_address1'] = $address->address1;
                    $result[$key]['address_postcode'] = $address->postcode;
                    $result[$key]['address_city'] = $address->city;
                    $result[$key]['address_country'] = $address->country;
                }
            }
        }

        $ret = array();
        $ret['total'] = $total;
        $ret['pages'] = ceil($total / 20);
        $ret['orders'] = $result;

        return ($ret);
    }



    public static function carrierIsAllowedModification($id_carrier)
    {
        $carrier_conf_mapping = unserialize(Configuration::get('SONICE_ETQ_CARRIER_MAPPING'));

        if (!is_array($carrier_conf_mapping) || !count($carrier_conf_mapping)) {
            return (false);
        }

        if (array_key_exists($id_carrier, $carrier_conf_mapping)) {
            if (in_array($carrier_conf_mapping[$id_carrier], array('DOM', 'DOS', 'RDV'))) {
                return (true);
            }
        }

        return (false);
    }



    public static function getCarriersAllowedModification()
    {
        $carrier_conf_mapping = unserialize(Configuration::get('SONICE_ETQ_CARRIER_MAPPING'));
        $carrier_list = array();

        if (!is_array($carrier_conf_mapping) || !count($carrier_conf_mapping)) {
            return ($carrier_list);
        }

        $id_dom = array_keys($carrier_conf_mapping, 'DOM');
        if (is_array($id_dom) && !empty($id_dom)) {
            foreach ($id_dom as $dom) {
                $tmp_carrier = new Carrier($dom);
                if (!$tmp_carrier->deleted) {
                    $carrier_list[] = $tmp_carrier;
                }
            }
        }

        $id_dos = array_keys($carrier_conf_mapping, 'DOS');
        if (is_array($id_dos) && !empty($id_dos)) {
            foreach ($id_dos as $dos) {
                $tmp_carrier = new Carrier($dos);
                if (!$tmp_carrier->deleted) {
                    $carrier_list[] = new Carrier($dos);
                }
            }
        }

        $id_rdv = array_keys($carrier_conf_mapping, 'RDV');
        if (is_array($id_rdv) && !empty($id_rdv)) {
            foreach ($id_rdv as $rdv) {
                $tmp_carrier = new Carrier($rdv);
                if (!$tmp_carrier->deleted) {
                    $carrier_list[] = new Carrier($rdv);
                }
            }
        }

        return ($carrier_list);
    }



    public static function getSelectedOrders($orders)
    {
        if (!is_array($orders) || !count($orders)) {
            return (false);
        }

        $sql = 'SELECT o.`id_order`, o.`id_carrier`, o.`id_customer`, o.`id_address_delivery`, DATE_FORMAT(o.`date_add`, "%Y-%m-%d") AS date_add
                FROM '._DB_PREFIX_.'orders AS o
                WHERE `id_order` IN ('.pSQL(implode(', ', $orders)).')
                GROUP BY o.`id_order` ORDER BY o.`id_order` DESC';

        $result = Db::getInstance()->executeS($sql);

        if (is_array($result) && count($result)) {
            foreach ($result as $key => $order) {
                if (!isset($order['id_carrier'])) {
                    continue;
                }

                $current_order = new Order((int)$order['id_order']);
                $result[$key]['currency'] = '';
                if (Validate::isLoadedObject($current_order)) {
                    $currency = Currency::getCurrency((int)$current_order->id_currency);

                    $result[$key]['currency'] = array_key_exists('sign', $currency) ?
                        $currency['sign'] : $currency['iso_code'];
                }

                $carrier = new Carrier((int)$order['id_carrier']);
                if (Validate::isLoadedObject($carrier)) {
                    $result[$key]['carrier_name'] = $carrier->name;
                    $result[$key]['carrier_allow_modify'] = self::carrierIsAllowedModification((int)$order['id_carrier']);
                }
                $customer = new Customer((int)$order['id_customer']);
                if (Validate::isLoadedObject($customer)) {
                    $result[$key]['customer_firstname'] = $customer->firstname;
                    $result[$key]['customer_lastname'] = $customer->lastname;
                }

                $address = new Address((int)$order['id_address_delivery']);
                if (Validate::isLoadedObject($address)) {
                    $result[$key]['address_alias'] = $address->alias;
                    $result[$key]['address_address1'] = $address->address1;
                    $result[$key]['address_postcode'] = $address->postcode;
                    $result[$key]['address_city'] = $address->city;
                    $result[$key]['address_country'] = $address->country;
                }

                $i = 0;
                $products = $current_order->getProducts();
                foreach ($products as $product) {
                    if (version_compare(_PS_VERSION_, '1.5', '>=')) {
                        $i += $product['wholesale_price'];
                    } else {
                        $i += $product['total_wt'];
                    }
                }

                $result[$key]['ta'] = (int)($i * 100);
            }
        }

        // Compat with pagination.tpl
        $ret = array();
        $ret['orders'] = $result;

        return ($ret);
    }



    public static function getLabelForExpedition($id_session)
    {
        if (!$id_session) {
            return (false);
        }

        $sql = 'SELECT *
                FROM '._DB_PREFIX_.self::SCE_TABLE.' label, '._DB_PREFIX_.'sonice_etq_session_detail session
                WHERE label.`sent` = 0
                AND session.`id_session` = '.(int)$id_session.'
                AND label.`id_order` = session.`id_order`';

        $result = Db::getInstance()->executeS($sql);
        $order_history = array();

        if (is_array($result) && count($result)) {
            foreach ($result as $key => $package) {
                if (in_array($package['id_order'], $order_history)) {
                    unset($result[$key]);
                    continue;
                }
                $order_history[] = $package['id_order'];

                $order = new Order((int)$package['id_order']);
                if (!Validate::isLoadedObject($order)) {
                    continue;
                }

                $address = new Address((int)$order->id_address_delivery);
                if (Validate::isLoadedObject($address)) {
                    $result[$key]['address'] = $address->address1.', '.$address->postcode.' '.$address->city;
                }

                $customer = new Customer((int)$order->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    $result[$key]['customer'] = $customer->firstname.' '.$customer->lastname;
                }

                $result[$key]['date'] = $order->date_add;
            }
        } else {
            $result = array();
        }

        return ($result);
    }



    public static function getLabelGoneForExpedition($id_session)
    {
        if (!$id_session) {
            return (false);
        }

        $sql = 'SELECT *
                FROM '._DB_PREFIX_.self::SCE_TABLE.' label, '._DB_PREFIX_.'sonice_etq_session_detail session
                WHERE label.`sent` = 1
                AND session.`id_session` = '.(int)$id_session.'
                AND label.`id_order` = session.`id_order`';

        $result = Db::getInstance()->executeS($sql);
        $order_history = array();

        if (is_array($result) && count($result)) {
            foreach ($result as $key => $package) {
                if (in_array($package['id_order'], $order_history)) {
                    unset($result[$key]);
                    continue;
                }
                $order_history[] = $package['id_order'];

                $order = new Order((int)$package['id_order']);
                if (!Validate::isLoadedObject($order)) {
                    continue;
                }

                $address = new Address((int)$order->id_address_delivery);
                if (Validate::isLoadedObject($address)) {
                    $result[$key]['address'] = $address->address1.', '.$address->postcode.' '.$address->city;
                }

                $customer = new Customer((int)$order->id_customer);
                if (Validate::isLoadedObject($customer)) {
                    $result[$key]['customer'] = $customer->firstname.' '.$customer->lastname;
                }

                $result[$key]['date'] = $order->date_add;
            }
        } else {
            $result = array();
        }

        return ($result);
    }



    public static function getLabels()
    {
        if (!class_exists('Context')) {
            require_once(dirname(__FILE__).'/../backward_compatibility/backward.php');
        }

        $module_conf = unserialize(Configuration::get('SONICE_ETQ_CONF', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id));
        $url_base = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';
        $url_base .= htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/sonice_etiquetage/download/';

        $labels = array();
        $sql = 'SELECT *
                FROM '._DB_PREFIX_.self::SCE_TABLE.'
                WHERE `date_add` > DATE_ADD(NOW(), INTERVAL - 30 DAY)
                ORDER BY `id_order` DESC';

        $result = Db::getInstance()->executeS($sql);

        if (!$result) {
            return ($labels);
        }

        foreach ($result as $label) {
            $labels[$label['id_order']] = $label;

            $order = new Order((int)$label['id_order']);
            $carrier = new Carrier((int)$order->id_carrier);

            if (Validate::isLoadedObject($order) && Validate::isLoadedObject($carrier) && Tools::strlen($carrier->url)) {
                $labels[$label['id_order']]['url'] = str_replace('@', $label['parcel_number'], $carrier->url);
            } else {
                $labels[$label['id_order']]['url'] = null;
            }

            $extension = '.pdf';
            if (preg_match('(DPL|ZPL)', $module_conf['output_print_type'])) {
                $extension = '.prn';
            }

            $labels[$label['id_order']]['pdfurl'] = $url_base.$label['parcel_number'].$extension;
            $labels[$label['id_order']]['zpl_code'] = ''; // @file_get_contents(dirname(__FILE__).'/../download/'.$label['parcel_number'].'.prn');
        }

        return ($labels);
    }


    public static function getTaredWeight($weight)
    {
        $tare_conf = unserialize(Configuration::get('SONICE_ETQ_TARE', null, Context::getContext()->shop->id_shop_group, Context::getContext()->shop->id));

        if (!is_array($tare_conf) || !count($tare_conf)) {
            return ($weight);
        }

        foreach ($tare_conf as $tare) {
            if ($weight >= (float)$tare['from'] && $weight < $tare['to']) {
                $weight += (float)$tare['weight'];
                break;
            }
        }

        return ((float)$weight);
    }



    public static function getParcelNumberByIdOrder($id_order)
    {
        if (!is_int($id_order)) {
            return (false);
        }
        return (Db::getInstance()->getValue('SELECT `parcel_number` FROM '._DB_PREFIX_.self::SCE_TABLE.' WHERE `id_order` = '.(int)$id_order));
    }



    public static function getLabelInformationByIdOrder($id_order)
    {
        if (!is_int($id_order)) {
            return (false);
        }

        $data = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.self::SCE_TABLE.' WHERE `id_order` = '.(int)$id_order);

        if (!is_array($data) || !count($data) || !$data) {
            return (array());
        }
        return ($data[0]);
    }



    public static function getIdOrderByParcelNumber($parcel_number)
    {
        if (!$parcel_number) {
            return (false);
        }
        return ((int)Db::getInstance()->getValue('SELECT `id_order` FROM '._DB_PREFIX_.self::SCE_TABLE.' WHERE `parcel_number` = "'.pSQL($parcel_number).'"'));
    }



    public static function setParcelAsSent($parcel_number)
    {
        if (!$parcel_number) {
            return (false);
        }
        return (Db::getInstance()->execute('UPDATE '._DB_PREFIX_.self::SCE_TABLE.' SET `sent` = 1 WHERE `parcel_number` = "'.pSQL($parcel_number).'"'));
    }



    public static function deleteLabelByIdOrder($id_order)
    {
        if (!$id_order) {
            return (false);
        }
        return (Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'sonice_etq_label WHERE `id_order` = '.(int)$id_order));
    }



    public static function getIdOrderCarrierByOrderId($id_order)
    {
        if (!$id_order) {
            return (false);
        }
        return (Db::getInstance()->getValue('SELECT `id_order_carrier` FROM `'._DB_PREFIX_.'order_carrier` WHERE `id_order` = '.(int)$id_order));
    }
}
