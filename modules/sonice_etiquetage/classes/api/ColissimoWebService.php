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
 * @package   sonice_etiquetage
 * @author    Alexandre D.
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice_etiquetage@common-services.com
 */

require_once dirname(__FILE__).'/MTOMSoapClient.php';

class ColissimoWebService
{

    const WS = 'https://ws.colissimo.fr/sls-ws/SlsServiceWS?wsdl';
    const WS_TELINTRANS = 'http://pfi.telintrans.fr/sls-ws/SlsServiceWS?wsdl';
    const WS_SUPERVISION = 'http://ws.colissimo.fr/supervisionWSShipping/supervision.jsp';

    protected $contract_number;
    protected $password;

    protected $configuration;
    protected $carriers;
    protected $tares;
    protected $demo;

    public function __construct($demo = false)
    {
        $this->configuration = unserialize(Configuration::get(
            'SONICE_ETQ_CONF',
            null,
            Context::getContext()->shop->id_shop_group,
            Context::getContext()->shop->id
        ));
        $this->carriers = unserialize(Configuration::get(
            'SONICE_ETQ_CARRIER',
            null,
            Context::getContext()->shop->id_shop_group,
            Context::getContext()->shop->id
        ));
        $this->tares = unserialize(Configuration::get(
            'SONICE_ETQ_TARE',
            null,
            Context::getContext()->shop->id_shop_group,
            Context::getContext()->shop->id
        ));
        
        $this->contract_number = $this->configuration['ContractNumber'];
        $this->password = $this->configuration['Password'];
        
        $this->demo = $demo ? $demo : (bool)Configuration::get('SONICE_ETQ_TEST');
    }

    /**
     * Check the availability of ColiPoste web services
     *
     * @return boolean Supervision status
     */
    public static function webServiceSupervision()
    {
        $supervision = Tools::file_get_contents(self::WS_SUPERVISION);

        if (preg_match('/\[OK\]/', $supervision)) {
            return (true);
        }

        return (false);
    }

    protected function call($webservice, $method, $parameters)
    {
        if ($this->demo) {
            return $this->callDemo();
        }

        $client = new MTOMSoapClient($webservice, array(
            'trace' => true,
            'exceptions' => true,
            'soap_version' => SOAP_1_1,
            'encoding' => 'utf-8'
        ));

        if (in_array($method, array('generateBordereauByParcelsNumbers'))) {
            $parameters = array($method.'Request' => $parameters);
        } else {
            $parameters = array(array($method.'Request' => $parameters));
        }

        $result = $client->__call(
            $method,
            $parameters
        );

        if (!$result instanceof stdClass) {
            throw new Exception('Soap call response is not a valid stdClass instance.');
        }

        return $result;
    }

    private function callDemo()
    {
        $response = new stdClass();
        $response->return = new stdClass();

        $response->return->messages = new stdClass();
        $response->return->messages->id = 0;
        $response->return->messages->messageContent = '';
        $response->return->messages->type = 'INFO';

        $response->return->labelResponse = new stdClass();
        $response->return->labelResponse->label = '';
        $response->return->labelResponse->pdfUrl = 'https://dl.dropboxusercontent.com/u/60698220/6A14039975109.pdf';
        $response->return->labelResponse->parcelNumber = '6A'.abs(rand(100000000000, 999999999999));

        switch ($this->configuration['output_print_type']) {
            case 'PDF_A4_300dpi':
                $response->return->labelResponse->label = Tools::file_get_contents(
                    dirname(__FILE__).'/pdf_a4_demo'
                );
                break;

            case 'PDF_10x15_300dpi':
                $response->return->labelResponse->label = Tools::file_get_contents(
                    dirname(__FILE__).'/pdf_a4_demo'
                );
                break;

            case 'ZPL_10x15_203dpi':
                $response->return->labelResponse->label = Tools::file_get_contents(
                    dirname(__FILE__).'/zpl_203dpi_demo'
                );
                break;

            case 'ZPL_10x15_300dpi':
                $response->return->labelResponse->label = Tools::file_get_contents(
                    dirname(__FILE__).'/zpl_300dpi_demo'
                );
                break;

            case 'DPL_10x15_203dpi':
                $response->return->labelResponse->label = Tools::file_get_contents(
                    dirname(__FILE__).'/dpl_203dpi_demo'
                );
                break;

            case 'DPL_10x15_300dpi':
                $response->return->labelResponse->label = Tools::file_get_contents(
                    dirname(__FILE__).'/dpl_300dpi_demo'
                );
                break;
        }

        return $response;
    }
}
