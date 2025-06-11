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
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'sonice_etiquetage.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoColissimoPDF.php'));
    require_once(readlink(_PS_MODULE_DIR_.'sonice_etiquetage'.DIRECTORY_SEPARATOR.'classes/SoNiceEtiquetageSupport.php'));
} else {
    require_once(dirname(__FILE__).'/../../../config/config.inc.php');
    require_once(dirname(__FILE__).'/../sonice_etiquetage.php');
    require_once(dirname(__FILE__).'/../classes/SoColissimoPDF.php');
    require_once(dirname(__FILE__).'/../classes/SoNiceEtiquetageSupport.php');
}


class SoNiceEtiquetageCheckLogin extends SoNice_Etiquetage
{

    public function __construct()
    {
        parent::__construct();

        if (Tools::getValue('debug')) {
            $this->debug = true;
        }

        if ($this->debug) {
            @ini_set('display_errors', 'on');
            @define('_PS_DEBUG_SQL_', true);
            @error_reporting(E_ALL | E_STRICT);
        }
    }

    public function checkThat()
    {
        ob_start();

        $login = Tools::getValue('return_info');

        if (!is_array($login) && !count($login)) {
            die($this->l('Impossible to retrieve the login informations.'));
        }

        $id_order = (int)Db::getInstance()->getValue(
            'SELECT `id_order` FROM `'._DB_PREFIX_.'orders` ORDER BY `id_order` DESC'
        );

        $info = new stdClass();
        $pdf = new SoColissimoPDF($id_order);

        if (!$pdf instanceof SoColissimoPDF) {
            $info->status = false;
            $info->errorID = 'SoNice';
            $info->error = $this->l('An error occured while initialising SoColissimoPDF class.');
        } else {
            $params = array(
                'contractNumber' => $login['ContractNumber'],
                'password' => $login['Password'],
                'outputFormat' => array(
                    'x' => 0,
                    'y' => 0,
                    'outputPrintingType' => 'PDF_A4_300dpi',
                ),
                'letter' => array(
                    'service' => array(
                        'productCode' => 'DOM',
                        'depositDate' => date('Y-m-d'),
                        'transportationAmount' => 0,
                        'totalAmount' => null,
                        'orderNumber' => 42,
                        'commercialName' => 'Test',
                        'returnTypeChoice' => null,
                    ),
                    'parcel' => array(
                        'weight' => 0.3,
                        'nonMachinable' => false,
                        'instructions' => null,
                        'pickupLocationId' => null,
                    ),
                    'sender' => array(
                        'senderParcelRef' => 'EXP4242',
                        'address' => array(
                            'companyName' => 'Test',
                            'lastName' => null,
                            'firstName' => null,
                            'line0' => null,
                            'line1' => null,
                            'line2' => '51 avenue Paul Doumer',
                            'line3' => null,
                            'countryCode' => 'FR',
                            'city' => 'Paris',
                            'zipCode' => '75116',
                            'phoneNumber' => null,
                            'mobileNumber' => null,
                            'doorCode1' => null,
                            'doorCode2' => null,
                            'email' => null,
                            'intercom' => null,
                            'language' => 'FR',
                        )
                    ),
                    'addressee' => array(
                        'addresseeParcelRef' => 'ABCD123',
                        'address' => array(
                            'companyName' => null,
                            'lastName' => 'Doe',
                            'firstName' => 'John',
                            'line0' => null,
                            'line1' => null,
                            'line2' => '13 rue de la Loge',
                            'line3' => null,
                            'countryCode' => 'FR',
                            'city' => 'Marseille',
                            'zipCode' => '13000',
                            'phoneNumber' => null,
                            'mobileNumber' => null,
                            'doorCode1' => null,
                            'doorCode2' => null,
                            'email' => 'test@mytest.fr',
                            'intercom' => null
                        )
                    )
                )
            );

            $call = $pdf->callWS($login['ContractNumber'], $login['Password'], $params);

            if ($call) {
                $response = $pdf->getFormattedResponse();
                if (!is_null($response['errorID']) && $response['errorID'] >= 30000 && $response['errorID'] <= 30008) {
                    $info->status = false;
                    $info->errorID = $response['errorID'];
                    $info->error = $response['error'];

                    $documentation_url = SoNiceEtiquetageSupport::getDocumentationLink((string)$response['errorID']);
                    if ($documentation_url) {
                        $info->error .= sprintf(' (<a href="%s" target="_blank">%s</a>)', $documentation_url, $documentation_url);
                    }
                } else {
                    $info->status = true;
                    $info->errorID = 0;
                    $info->error = '';
                }
            } else {
                $info->status = false;
                $info->errorID = 'SoNice';
                $info->error = $this->l('Error with the web service call.');
            }
        }

        $callback = Tools::getValue('callback');
        $output = ob_get_clean();

        die($callback.'('.Tools::jsonEncode(array(
                'console' => $output,
                'info' => $info,
                'request' => '<pre>'.$pdf->xmlpp($pdf->origin_request, true).'</pre>',
                'response' => '<pre>'.$pdf->xmlpp($pdf->response->asXML(), true).'</pre>'
            )).')');
    }
}



$login = new SoNiceEtiquetageCheckLogin();
$login->checkThat();
