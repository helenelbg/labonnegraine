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

require_once(_PS_MODULE_DIR_.'sonice_etiquetage/sonice_etiquetage.php');
require_once(dirname(__FILE__).'/SoColissimoCurlParser.php');

abstract class SoColissimoWebService extends SoNice_Etiquetage
{

    /** Supervision link to check web service availability */
    const WS_SUPERVISION = 'http://ws.colissimo.fr/supervisionWSShipping/supervision.jsp';
    const SCE_WS = 'https://ws.colissimo.fr/sls-ws/SlsServiceWS';
//     const SCE_WS = 'http://pfi.telintrans.fr/sls-ws/SlsServiceWS';

    /** @var bool */
    protected $demo;

    /** @var mixed The web service response */
    public $response = null;
    /** @var SoColissimoCurlParser null */
    public $raw_response = null;
    public $origin_request = null;
    public $error = null;
    public $request;

    public $module_conf;
    public $carrier_conf;
    public $tare_conf;

    /** @var array Contains options for the soap client connection */
    protected $debug = false;


    public function __construct($demo = false)
    {
        parent::__construct();

        if ($demo) {
            $this->demo = (bool)$demo;
        } else {
            $this->demo = (bool)Configuration::get('SONICE_ETQ_TEST');
        }

        $this->module_conf = unserialize(Configuration::get('SONICE_ETQ_CONF', null, $this->context->shop->id_shop_group, $this->context->shop->id));
        $this->carrier_conf = unserialize(Configuration::get('SONICE_ETQ_CARRIER', null, $this->context->shop->id_shop_group, $this->context->shop->id));
        $this->tare_conf = unserialize(Configuration::get('SONICE_ETQ_TARE', null, $this->context->shop->id_shop_group, $this->context->shop->id));
    }


    public function l($string, $specific = false, $locale = null)
    {
        if (!$specific) {
            $specific = basename(__FILE__, '.php');
        }
        return parent::l($string, $specific);
    }


    private function _fillXML($params, &$sls_generate_label, &$document)
    {
        $requires_cdata = array(
            'description', 'instructions', 'commercialName', 'companyName', 'lastName', 'firstName',
            'line0', 'line1', 'line2', 'line3', 'city'
        );

        foreach ($params as $key => $value) {
            if (is_numeric($key)) {
                // only happens with articles
                $key = 'article';
            } elseif (Tools::substr($key, 0, 6) == 'field_') {
                $key = 'field';
            }

            if (!is_array($value)) {
                if ($value || in_array($key, array('transportationAmount', 'totalAmount'))) {
                    if ($key == 'totalAmount' && !$value) {
                        // TODO remove coz useless and we have to remove it for
                        // international shipping, compulsory element
                        continue;
                    }

                    if (in_array($key, $requires_cdata)) {
                        $tmp_element = $document->createElement($key);
                        $tmp_element->appendChild($document->createCDATASection($value));
                    } else {
                        $tmp_element = $document->createElement($key, $value);
                    }
                    $sls_generate_label->appendChild($tmp_element);
                }
            } else {
                $new_child = $document->createElement($key);
                $sls_generate_label->appendChild($new_child);
                $this->_fillXML($value, $new_child, $document);
            }
        }
    }


    protected function _callWS($params)
    {
        $document = new DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;
        $document->preserveWhiteSpace = true;

        $soap_envelope = $document->createElement('soap:Envelope');
        $soap_envelope->setAttribute('xmlns:soap', 'http://schemas.xmlsoap.org/soap/envelope/');
        $soap_envelope->setAttribute('xmlns:sls', 'http://sls.ws.coliposte.fr');
        $soap_body = $document->createElement('soap:Body');

        $sls_generate_label = $document->createElement('sls:generateLabel');
        $sls_generate_label_request = $document->createElement('generateLabelRequest');

        $soap_envelope->appendChild($soap_body);
        $soap_body->appendChild($sls_generate_label);
        $sls_generate_label->appendChild($sls_generate_label_request);
        $document->appendChild($soap_envelope);

        $this->_fillXML($params, $sls_generate_label_request, $document);

        $request = $document->saveXML();
//        var_dump($this->xmlpp($request));die;
        $this->origin_request = $request;
        $this->request = $request;

        $headers = array(
            'Accept-Encoding: gzip,deflate',
            'Content-Type: text/xml; charset="utf-8"',
            'SOAPAction: ""'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_URL, self::SCE_WS);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        // IF demo() ELSE webservice() ENDIF
        if ($this->demo && in_array($this->module_conf['output_print_type'], array('PDF_A4_300dpi'))) {
            $result = Tools::file_get_contents(dirname(__FILE__).'/../functions/demo_pdf_a4.xml');
        } elseif ($this->demo && in_array($this->module_conf['output_print_type'], array('PDF_10x15_300dpi'))) {
            $result = Tools::file_get_contents(dirname(__FILE__).'/../functions/demo_pdf_1015.xml');
        } elseif ($this->demo && in_array($this->module_conf['output_print_type'], array('ZPL_10x15_203dpi', 'ZPL_10x15_300dpi', 'DPL_10x15_203dpi', 'DPL_10x15_300dpi'))) {
            $result = Tools::file_get_contents(dirname(__FILE__).'/../functions/response_with_label_zpl_203dpi.txt');
        } else {
            $result = curl_exec($ch);
        }

        $this->raw_response = new SoColissimoCurlParser($result);
        $result = $this->xmldata($result);

        if ($result === false) {
            echo nl2br(print_r(curl_getinfo($ch), true));
            echo 'Request';
            print('<pre>'.print_r(htmlentities($request, ENT_QUOTES, 'UTF-8'), true).'</pre>');
            echo 'cURL error number:'.curl_errno($ch).'\n<br/>';
            echo 'cURL error message:'.curl_error($ch).'\n<br/>';
            curl_close($ch);
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return ($result);
    }

    public function xmldata($page)
    {
        if (empty($page)) {
            printf('%s(#%d): empty string passed to the function', basename(__FILE__), __LINE__);
            return (false);
        }

        $page_backup = $page;

        // On some system the following regexp failed because the response is too long
        // Minimize the response by removing header at beginning and kepping enough text to catch the XML code.
        // $page = Tools::substr($page, 100, 1250);
        // preg_match('/<soap:Envelope[\s\S]*soap:Envelope>/', $page, $page);

        $begin = strpos($page, '<soap:Envelope');
        $end = strpos($page, '</soap:Envelope>');

        $page_save = $page;
        $page = Tools::substr($page, $begin, $end - $begin + 16, 'iso-8859-1');
        $page_sub = $page;

        // $page = reset($page);
        try {
            $page = $this->xmlpp($page);
        } catch (Exception $e) {
            echo $e->getMessage().'<br>';
            ppp($page_save);
            ppp($page_sub);
            ppp($this->origin_request);
            ddd($page_backup);
        }

        // remove namespaces
        $page = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $page);
        $page = preg_replace('/[a-zA-Z0-9]+:([a-zA-Z0-9]+[ =>])/', '$1', $page);

        // filters
        $page = str_replace(array("\n", "\r", "\t"), '', $page);
        $page = trim(str_replace('"', "'", $page));

        $xml = simplexml_load_string($page, null, LIBXML_NOCDATA);

        if (!$xml instanceof SimpleXMLElement) {
            printf('%s(#%d): invalid string passed to the function: "%s"', basename(__FILE__), __LINE__, $page);
            return (false);
        }

        return ($xml);
    }


    /**
     * Check the availability of ColiPoste web services
     *
     * @return boolean Supervision status
     */
    public static function webServiceSupervision()
    {
        return true;
        $supervision = Tools::file_get_contents(self::WS_SUPERVISION);

        if (preg_match('/\[OK\]/', $supervision)) {
            return (true);
        }

        return (false);
    }


    public function xmlpp($xml, $html_output = false)
    {
        $xml_obj = new SimpleXMLElement($xml);

        $level = 4;
        $indent = 0;
        $pretty = array();

        $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
            $pretty[] = array_shift($xml);
        }

        foreach ($xml as $el) {
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                $pretty[] = str_repeat(' ', $indent).$el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    $indent -= $level;
                }
                if ($indent < 0) {
                    $indent += $level;
                }
                $pretty[] = str_repeat(' ', $indent).$el;
            }
        }
        $xml = implode("\n", $pretty);

        if ($html_output) {
            $xml = htmlentities($xml);
        }

        return $xml;
    }
}
