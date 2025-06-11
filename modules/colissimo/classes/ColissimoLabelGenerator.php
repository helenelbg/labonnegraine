<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class ColissimoLabelGenerator
 */
class ColissimoLabelGenerator
{
    const CATEGORY_GIFT = 1;
    const CATEGORY_SAMPLES = 2;
    const CATEGORY_COMMERCIAL = 3;
    const CATEGORY_DOC = 4;
    const CATEGORY_OTHER = 5;
    const CATEGORY_MERCHANDISE_RETURN = 6;
    const COLISHIP_CATEGORY_GIFT = 2;
    const COLISHIP_CATEGORY_SAMPLES = 0;
    const COLISHIP_CATEGORY_COMMERCIAL = 4;
    const COLISHIP_CATEGORY_DOC = 1;
    const COLISHIP_CATEGORY_OTHER = 5;
    const COLISHIP_CATEGORY_MERCHANDISE_RETURN = 3;

    /** @var array $data */
    private $data;

    /** @var ColissimoLogger $logger */
    private $logger;

    /** @var ColissimoGenerateLabelRequest $request */
    private $request;

    /**
     * ColissimoLabelGenerator constructor.
     * @param ColissimoLogger $logger
     */
    public function __construct($logger)
    {
        $this->logger = $logger;
        $this->logger->setChannel('GenerateLabel');
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getProductsForCustomsDeclaration()
    {
        /** @var Order $order */
        $order = $this->data['order'];
        $productDetails = $this->data['products_detail'];
        if (empty($productDetails))
        {
          $productDetails = array();
        }
        $currency = new Currency((int) $order->id_currency);
        $products = $order->getProducts();
        $hsCode = Configuration::get('COLISSIMO_DEFAULT_HS_CODE');
        $articles = array();
        foreach ($products as $product) {
            if (array_key_exists($product['product_id'], $productDetails) && isset($productDetails[$product['product_id']][$product['product_attribute_id']])) {
                $productCustomDetails = ColissimoCustomProduct::getByIdProduct((int) $product['product_id']);
                $categoryCustomDetails = ColissimoCustomCategory::getByIdCategory((int) $product['id_category_default']);
                $shortDescription = $product['product_name'];

                if ($productCustomDetails->short_desc) {
                    $shortDescription = $productCustomDetails->short_desc;
                } elseif ($categoryCustomDetails->short_desc) {
                    $shortDescription = $categoryCustomDetails->short_desc;
                }
                if ($productCustomDetails->hs_code) {
                    $hsCode = $productCustomDetails->hs_code;
                } elseif ($categoryCustomDetails->hs_code) {
                    $hsCode = $categoryCustomDetails->hs_code;
                }
                if ($productCustomDetails->id_country_origin) {
                    $originCountry = Country::getIsoById((int) $productCustomDetails->id_country_origin);
                } elseif ($categoryCustomDetails->id_country_origin) {
                    $originCountry = Country::getIsoById((int) $categoryCustomDetails->id_country_origin);
                } else {
                    $originCountry = Country::getIsoById((int) Configuration::get('COLISSIMO_DEFAULT_ORIGIN_COUNTRY'));
                }
                $weight = (float) $product['product_weight'] ? ColissimoTools::weightInKG($product['product_weight']) : 0.05;
                $articles[] = array(
                    'description'   => $shortDescription,
                    'quantity'      => $productDetails[$product['product_id']][$product['product_attribute_id']],
                    'weight'        => $weight,
                    'value'         => (float) $product['unit_price_tax_excl'],
                    'hsCode'        => $hsCode,
                    'originCountry' => $originCountry,
                    'currency'      => $currency->iso_code,
                    'artref'        => $product['product_reference'],
                    'originalIdent' => 'A',
                );
            }
        }

        return $articles;
    }

    /**
     * @param bool $forcePdf
     */
    public function setLabelOutput($forcePdf = false, $poste = false)
    {
        if ( $poste == false )
        {
            $output = array(
                'x' => 0,
                'y' => 0,
                'outputPrintingType' => $forcePdf ? 'PDF_A4_300dpi' : Configuration::get('COLISSIMO_LABEL_FORMAT'),
            );
        }
        elseif ( $poste == 'controle1' )
        {
            $output = array(
                'x' => 0,
                'y' => 0,
                'outputPrintingType' => "DPL_10x15_203dpi",
            );
        }
        elseif ( $poste == 'controle2' )
        {
            $output = array(
                'x' => 0,
                'y' => 0,
                'outputPrintingType' => 'ZPL_10x15_203dpi',
            );
        }
        $this->request->setOutput($output);
    }

    /**
     * @throws Exception
     */
    public function setLabelService()
    {
        /** @var Order $order */
        $order = $this->data['order'];
        /** @var ColissimoService $colissimoService */
        $colissimoService = $this->data['colissimo_service'];
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $this->logger->info(sprintf(
                'Order #%d (%s) - Shipping type: PICKUP POINT DELIVERY',
                $this->data['order']->id,
                $this->data['order']->reference
            ));
            $pickupPoint = new ColissimoPickupPoint((int) $this->data['colissimo_order']->id_colissimo_pickup_point);
            $services = array(
                'productCode' => $pickupPoint->getProductCodeForAffranchissement(),
                'depositDate' => date('Y-m-d'),
            );
        } else {
            $this->logger->info(sprintf(
                'Order #%d (%s) - Shipping type: HOME DELIVERY',
                $this->data['order']->id,
                $this->data['order']->reference
            ));
            $services = array(
                'productCode' => $colissimoService->product_code,
                'depositDate' => date('Y-m-d'),
            );
        }
        $shippingAmountEUR = ColissimoTools::convertInEUR(
            $order->total_shipping_tax_excl,
            new Currency($order->id_currency)
        );
        $shippingAmountEUR = (float) $shippingAmountEUR ? $shippingAmountEUR : 0.01;
        $services['transportationAmount'] = (int) ($shippingAmountEUR * 100);
        $merchantAddress = new ColissimoMerchantAddress('sender');
        /** @var Address $customerAddress */
        $customerAddress = $this->data['customer_addr'];

        //error_log('pays2 : '.Country::getIsoById((int) $customerAddress->id_country));
        $isoTo = Country::getIsoById((int) $customerAddress->id_country);
        $isoFrom = $merchantAddress->countryCode;
        if (ColissimoTools::needCN23($isoFrom, $isoTo, $customerAddress->postcode)) {
            $services['totalAmount'] = $services['transportationAmount'];
            $services['returnTypeChoice'] = 3;
        }
        $services['orderNumber'] = (Configuration::get('COLISSIMO_LABEL_DISPLAY_REFERENCE')) ? $this->data['order']->reference : $this->data['order']->id;
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $services['commercialName'] = $merchantAddress->companyName;
        }
        if ($colissimoService->product_code == 'DOS' && in_array($isoTo, ColissimoTools::$isoPostalPartner)) {
            $services['reseauPostal'] =   $this->data['form_options']['postal_partner'];
        }
        $this->request->setShipmentServices($services);
    }

    /**
     *
     */
    public function setLabelCustomsDeclarations()
    {
        $merchantAddress = new ColissimoMerchantAddress('sender');
        /** @var Address $customerAddress */
        $customerAddress = $this->data['customer_addr'];
        $isoTo = Country::getIsoById((int) $customerAddress->id_country);
        $isoFrom = $merchantAddress->countryCode;
        if (ColissimoTools::needCN23($isoFrom, $isoTo, $customerAddress->postcode)) {
            $defaultCategory = self::CATEGORY_COMMERCIAL;
            $customsDeclaration = array(
                'includeCustomsDeclarations' => 1,
                'contents' => array(
                    'article' => $this->getProductsForCustomsDeclaration(),
                    'category' => array(
                        'value' => $this->data['cn23_category'] ? $this->data['cn23_category'] : $defaultCategory,
                    ),
                ),
                'description' => $this->data['label_articles_desc'],
            );
            if (isset($this->data['eoriUk']) && $this->data['eoriUk']) {
                $customsDeclaration['comments'] = 'EORI UK: '.$this->data['eoriUk'];
            }
            $this->logger->info(
                sprintf(
                    'Order #%d (%s) - Include customs declarations',
                    $this->data['order']->id,
                    $this->data['order']->reference
                ),
                array('data', $customsDeclaration)
            );
            $this->request->setCustomsOptions($customsDeclaration);
        }
    }

    /**
     * @param ColissimoLabel $colissimoLabel
     * @throws Exception
     */
    public function setReturnLabelCustomsDeclarations($colissimoLabel)
    {
        $merchantAddress = new ColissimoMerchantAddress('sender');
        /** @var Address $customerAddress */
        $customerAddress = $this->data['customer_addr'];
        $isoTo = Country::getIsoById((int) $customerAddress->id_country);
        $isoFrom = $merchantAddress->countryCode;
        if (ColissimoTools::needCN23($isoFrom, $isoTo, $customerAddress->postcode)) {
            if (Tools::substr($this->data['order']->invoice_date, 0, 10) != '0000-00-00') {
                $invoiceDate = Tools::substr($this->data['order']->invoice_date, 0, 10);
            } else {
                $invoiceDate = date('Y-m-d');
            }
            $customsDeclaration = array(
                'includeCustomsDeclarations' => 1,
                'contents' => array(
                    'article' => $this->getProductsForCustomsDeclaration(),
                    'category' => array(
                        'value' => self::CATEGORY_MERCHANDISE_RETURN,
                    ),
                    'original' => array(
                        array(
                            'originalIdent' => 'A',
                            'originalInvoiceNumber' => 'IN'.$this->data['order']->invoice_number,
                            'originalInvoiceDate' => $invoiceDate,
                            'originalParcelNumber' => $colissimoLabel->shipping_number,
                        ),
                    ),
                ),
            );
            $this->logger->info(
                sprintf(
                    'Order #%d (%s) - Include customs declarations',
                    $this->data['order']->id,
                    $this->data['order']->reference
                ),
                array('data', $customsDeclaration)
            );
            $this->request->setCustomsOptions($customsDeclaration);
        }
    }

    /**
     *
     */
    public function setLabelAddresses()
    {
        /** @var ColissimoService $colissimoService */
        $colissimoService = $this->data['colissimo_service'];
        /** @var ColissimoMerchantAddress $senderAddrObj */
        $senderAddrObj = $this->data['merchant_addr'];
        if ( !isset($this->data['additional_address']) )
        {
            $this->data['additional_address'] = '';
        }
        $additionalAddress = $this->data['additional_address'];
        $senderAddr = array(
            'senderParcelRef' => (Configuration::get('COLISSIMO_LABEL_DISPLAY_REFERENCE')) ? $this->data['order']->reference : $this->data['order']->id,//$this->data['order']->reference,
            'address' => array(
                'companyName' => $senderAddrObj->companyName,
                'lastName' => $senderAddrObj->lastName,
                'firstName' => $senderAddrObj->lastName,
                'line0' => $senderAddrObj->line0,
                'line1' => $senderAddrObj->line1,
                'line2' => $senderAddrObj->line2,
                'line3' => $senderAddrObj->line3,
                'countryCode' => $senderAddrObj->countryCode,
                'city' => $senderAddrObj->city,
                'zipCode' => $senderAddrObj->zipCode,
                'phoneNumber' => $senderAddrObj->phoneNumber,
                'email' => $senderAddrObj->email,
            ),
        );
        $this->request->setSenderAddress($senderAddr);
        /** @var Address $customerAddrDb */
        $customerDeliveryAddr = $this->data['customer_addr'];
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $customerAddrDb = $this->data['customer_addr'];
        } else {
            $customerAddrDb = $this->data['customer_addr'];
        }

        //error_log('GG '.print_r($this->data['customer_addr']->phone, true));
        $code_porte1 = '';
        $code_porte2 = '';
        if ( isset($additionalAddress->code_porte1) )
        {
            $code_porte1 = $additionalAddress->code_porte1;
        }
        if ( isset($additionalAddress->code_porte1) )
        {
            $code_porte2 = $additionalAddress->code_porte2;
        }
        //error_log('pays //'.$customerAddrDb->id_country.'// : '.Country::getIsoById($customerAddrDb->id_country));
        $customerAddr = array(
            'addresseeParcelRef' => (Configuration::get('COLISSIMO_LABEL_DISPLAY_REFERENCE')) ? $this->data['order']->reference : $this->data['order']->id,//$this->data['order']->reference,
            'codeBarForReference' => true,
            'address' => array(
                'companyName' => $customerAddrDb->company,
                'lastName' => $customerDeliveryAddr->lastname,
                'firstName' => $customerDeliveryAddr->firstname,
                'line2' => $customerAddrDb->address1,
                'line3' => $customerAddrDb->address2,
                'countryCode' => Country::getIsoById($customerAddrDb->id_country),
                'city' => $customerAddrDb->city,
                'zipCode' => $customerAddrDb->postcode,
                'phoneNumber' => $customerAddrDb->phone,
                'doorCode1' => $code_porte1,
                'doorCode2' => $code_porte2,
            ),
        );
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $customerAddr['address']['mobileNumber'] = $this->data['form_options']['mobile_phone'];
        } else {
            $customerAddr['address']['mobileNumber'] = $customerAddrDb->phone_mobile;
        }
        //error_log('GG '.print_r($customerAddr['address']['mobileNumber'], true));
        $customerAddr['address']['email'] = $this->data['customer']->email;
        if ($customerDeliveryAddr->id_state != "0") {
            $state = new State((int) $customerDeliveryAddr->id_state);
            $customerAddr['address']['stateOrProvinceCode'] = $state->iso_code;
        }
        $this->request->setAddresseeAddress($customerAddr);
    }

    /**
     *
     */
    public function setReturnLabelAddresses()
    {
        /** @var ColissimoMerchantAddress $merchantAddrObj */
        $merchantAddrObj = $this->data['merchant_addr'];
        if ($this->data['colissimo_service_initial']->type == ColissimoService::TYPE_RELAIS) {
            /** @var Address $customerAddrDb */
            $customerAddrDb = new Address((int) $this->data['order']->id_address_invoice);
            $mobilePhone = $this->data['form_options']['mobile_phone'];
        } else {
            /** @var Address $customerAddrDb */
            $customerAddrDb = $this->data['customer_addr'];
            $mobilePhone = $customerAddrDb->phone_mobile;
        }

        $customerAddr = array(
            'senderParcelRef' => (Configuration::get('COLISSIMO_LABEL_DISPLAY_REFERENCE')) ? $this->data['order']->reference : $this->data['order']->id,//$this->data['order']->reference,
            'address' => array(
                'companyName' => $customerAddrDb->company,
                'lastName' => $customerAddrDb->lastname,
                'firstName' => $customerAddrDb->firstname,
                'line2' => $customerAddrDb->address1,
                'line3' => $customerAddrDb->address2,
                'countryCode' => Country::getIsoById($customerAddrDb->id_country),
                'city' => $customerAddrDb->city,
                'zipCode' => $customerAddrDb->postcode,
                'mobileNumber' => $mobilePhone,
                'email' => $this->data['customer']->email,
            ),
        );
        $this->request->setSenderAddress($customerAddr);
        $merchantAddr = array(
            'addresseeParcelRef' => (Configuration::get('COLISSIMO_LABEL_DISPLAY_REFERENCE')) ? $this->data['order']->reference : $this->data['order']->id,//$this->data['order']->reference,
            'codeBarForReference' => true,
            'address' => array(
                'companyName' => $merchantAddrObj->companyName,
                'lastName' => $merchantAddrObj->lastName,
                'firstName' => $merchantAddrObj->firstName,
                'line0' => $merchantAddrObj->line0,
                'line1' => $merchantAddrObj->line1,
                'line2' => $merchantAddrObj->line2,
                'line3' => $merchantAddrObj->line3,
                'countryCode' => $merchantAddrObj->countryCode,
                'city' => $merchantAddrObj->city,
                'zipCode' => $merchantAddrObj->zipCode,
                'phoneNumber' => $merchantAddrObj->phoneNumber,
                'email' => $merchantAddrObj->email,
            ),
        );
        $this->request->setAddresseeAddress($merchantAddr);
    }

    /**
     * @throws Exception
     */
    public function setLabelOptions()
    {
        /** @var ColissimoService $colissimoService */
        $colissimoService = $this->data['colissimo_service'];
        $labelOptions = array();
        //@formatter:off
        if ($this->data['form_options']['insurance']) {
            /** @var Order $order */
            $order = $this->data['order'];
            $insuredAmount = $this->data['products_price'];
            $insuredAmountEUR = ColissimoTools::convertInEUR($insuredAmount, new Currency($order->id_currency));
            if ($colissimoService->type == ColissimoService::TYPE_RELAIS && (int) $insuredAmountEUR >= 1000) {
                $insuredAmountEUR = 1000;
            }
            $labelOptions['insuranceValue'] = $insuredAmountEUR * 100;
            $this->logger->info(sprintf(
                'Order #%d (%s) - Shipment insurance : %s EUR',
                $this->data['order']->id,
                $this->data['order']->reference,
                $insuredAmountEUR
            ));
        }
        //@formatter:on
        $labelOptions['weight'] = $this->data['form_options']['weight'];
        if ($this->data['form_options']['d150']) {
            if ($colissimoService->isMachinableOptionAvailable()) {
                $labelOptions['nonMachinable'] = 1;
                $this->logger->info(sprintf(
                    'Order #%d (%s) - Shipment non machinable',
                    $this->data['order']->id,
                    $this->data['order']->reference
                ));
            }
        }
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $pickupPoint = new ColissimoPickupPoint((int) $this->data['colissimo_order']->id_colissimo_pickup_point);
            $labelOptions['pickupLocationId'] = $pickupPoint->colissimo_id;
            $this->logger->info(sprintf(
                'Order #%d (%s) - Pickup point ID #%s',
                $this->data['order']->id,
                $this->data['order']->reference,
                $pickupPoint->colissimo_id
            ));
        }
        if ($this->data['form_options']['ta']) {
            $labelOptions['ftd'] = 1;
            $this->logger->info(sprintf(
                'Order #%d (%s) - Shipment free of taxes and fees',
                $this->data['order']->id,
                $this->data['order']->reference
            ));
        }
        if ( !isset($this->data['form_options']['ddp']) )
        {
            $this->data['form_options']['ddp'] = '';
        }
        if ($this->data['form_options']['ddp']) {
            $labelOptions['ddp'] = 1;
            $this->logger->info(sprintf(
                'Order #%d (%s) - Shippment with ddp offer',
                $this->data['order']->id,
                $this->data['order']->reference
            ));
        }
        $this->request->setShipmentOptions($labelOptions);
    }

    /**
     * @throws Exception
     */
    public function setReturnLabelOptions()
    {
        /** @var ColissimoService $colissimoService */
        $colissimoService = $this->data['colissimo_service'];
        $labelOptions = array();
        //@formatter:off
        if ($this->data['form_options']['insurance']) {
            /** @var Order $order */
            $order = $this->data['order'];
            $insuredAmount = $this->data['products_price'];
            $insuredAmountEUR = ColissimoTools::convertInEUR($insuredAmount, new Currency($order->id_currency));
            $labelOptions['insuranceValue'] = $insuredAmountEUR * 100;
            $this->logger->info(sprintf(
                'Order #%d (%s) - Return shipment insurance : %s EUR',
                $this->data['order']->id,
                $this->data['order']->reference,
                $insuredAmountEUR
            ));
        }
        //@formatter:on
        $labelOptions['weight'] = $this->data['form_options']['weight'];
        if ($this->data['form_options']['d150']) {
            if ($colissimoService->isMachinableOptionAvailable()) {
                $labelOptions['nonMachinable'] = 1;
                $this->logger->info(sprintf(
                    'Order #%d (%s) - Shipment non machinable',
                    $this->data['order']->id,
                    $this->data['order']->reference
                ));
            }
        }
        if ($colissimoService->type == ColissimoService::TYPE_RELAIS) {
            $pickupPoint = new ColissimoPickupPoint((int) $this->data['colissimo_order']->id_colissimo_pickup_point);
            $labelOptions['pickupLocationId'] = $pickupPoint->colissimo_id;
        }
        $labelOptions['ftd'] = 0;
        $this->request->setShipmentOptions($labelOptions);
    }

    /**
     *
     */
    public function setReturnCustomFields()
    {
        $infoText = array(
            'key' => 'CUSER_INFO_TEXT',
            'value' => sprintf('PS%s;%s', _PS_VERSION_, $this->data['version']),
        );
        $infoText3 = array(
            'key' => 'CUSER_INFO_TEXT_3',
            'value' => sprintf('PRESTASHOP'),
        );
        $infoEori = array(
            'key' => 'EORI',
            'value' => Configuration::get('COLISSIMO_EORI_NUMBER'),
        );
        $fields = array($infoText, $infoText3, $infoEori);
        $this->request->addCustomField($fields);
    }

    /**
     *
     */
    public function setCustomFields()
    {
        $infoText = array(
            'key' => 'CUSER_INFO_TEXT',
            'value' => sprintf('PS%s;%s', _PS_VERSION_, $this->data['version']),
        );
        $infoText3 = array(
            'key' => 'CUSER_INFO_TEXT_3',
            'value' => sprintf('PRESTASHOP'),
        );
        if ( !isset($this->data['eori']) )
        {
            $this->data['eori'] = '';
        }
        $infoEori = array(
            'key' => 'EORI',
            'value' => $this->data['eori'],
        );
        if (!isset($this->data['label_dimensions']))
        {
            $this->data['label_dimensions']['length'] = '';
            $this->data['label_dimensions']['width'] = '';
            $this->data['label_dimensions']['height'] = '';
        } 
        $infoLabelLenght = array(
            'key' => 'LENGTH',
            'value' => $this->data['label_dimensions']['length'],
        );
        $infoLabelWidth = array(
            'key' => 'WIDTH',
            'value' => $this->data['label_dimensions']['width'],
        );
        $infoLabelHeight = array(
            'key' => 'HEIGHT',
            'value' => $this->data['label_dimensions']['height'],
        );
        $fields = array($infoText, $infoText3, $infoEori, $infoLabelLenght, $infoLabelWidth, $infoLabelHeight);
        $this->request->addCustomField($fields);
    }

    /**
     * @param bool|int $returnLabel
     * @return ColissimoLabel
     * @throws Exception
     * @throws PrestaShopException
     */
    /*18/09/2023 - Modif -G- ajout d'un paramètre pour savoir le poste pour imprimer en ZPL ou DPL*/
    private function generateLabel($returnLabel = false, $poste = false)
    {
        //error_log('generateLabel');
        $this->request->buildRequest();
        $client = new ColissimoClient();
        $client->setRequest($this->request);
        $this->logger->info(
            sprintf('Order #%d (%s) - Request', $this->data['order']->id, $this->data['order']->reference),
            array('request' => json_decode($this->request->getRequest(true), true))
        );
        /** @var ColissimoGenerateLabelResponse $response */
        $response = $client->request();
        $this->logger->info(
            sprintf('Order #%d (%s) - Response', $this->data['order']->id, $this->data['order']->reference),
            array('response' => $response->response)
        );
        if (!$response->messages[0]['id']) {
            $label = new ColissimoLabel();
            $label->id_colissimo_order = (int) $this->data['colissimo_order']->id;
            $label->id_colissimo_deposit_slip = 0;

            if ( $poste == 'controle2' )
            {
                $extension = 'ZPL';
            }
            elseif ( $poste == 'controle1' )
            {
                $extension = 'DPL';
            }
            else
            {
                $extension = (int) $returnLabel ? 'pdf' : Tools::substr(Configuration::get('COLISSIMO_LABEL_FORMAT'), 0, 3);
            }

            //error_log('$extension : '.$extension);
            $label->label_format = pSQL(Tools::strtolower($extension));
            $label->return_label = (int) $returnLabel;
            $label->cn23 = $response->cn23 ? 1 : 0;
            $label->shipping_number = pSQL($response->parcelNumber);
            $label->coliship = 0;
            $label->migration = 0;
            $label->insurance = $this->data['form_options']['insurance'] ? '1' : '0';
            $label->file_deleted = 0;
            $label->save();
            $label->writeLabel(base64_decode($response->label));
            if ($returnLabel == false) {
                $order = new Order((int) $this->data['order']->id);
                if ( !isset($this->data['products_detail']) )
                {
                    $this->data['products_detail'] = array();
                }
                $productDetails = $this->data['products_detail'];
                foreach ($order->getProducts() as $product) {
                    if (is_array($productDetails) && array_key_exists($product['product_id'], $productDetails) && isset($productDetails[$product['product_id']][$product['product_attribute_id']])) {
                        $labelProduct = new ColissimoLabelProduct();
                        $labelProduct->id_colissimo_label = $label->id;
                        $labelProduct->id_product = $product['product_id'];
                        $labelProduct->id_product_attribute = $product['product_attribute_id'];
                        $labelProduct->quantity = (int) $productDetails[$product['product_id']][$product['product_attribute_id']];
                        $labelProduct->save();
                    }
                }
            }
            if (null !== $response->cn23) {
                $label->writeCN23File(base64_decode($response->cn23));
                $this->logger->info(sprintf(
                    'Order #%d (%s) - CN23 generated',
                    $this->data['order']->id,
                    $this->data['order']->reference
                ));
            }

            return $label;
        } else {
            $message = $response->messages[0];
            $this->logger->error(
                'Exception thrown: '.$message['messageContent'],
                array('details' => $response->messages)
            );
            throw new Exception(sprintf('%s (%s) - %s', $message['id'], $message['type'], $message['messageContent']));
        }
    }

    /**
     * @param ColissimoLabel $colissimoLabel
     * @return ColissimoLabel
     * @throws Exception
     * @throws PrestaShopException
     */
    public function generateReturn($colissimoLabel)
    {
        $this->logger->info(
            sprintf('Order #%d (%s) - Generate return label', $this->data['order']->id, $this->data['order']->reference)
        );
        $this->request = new ColissimoGenerateLabelRequest(
            ColissimoTools::getCredentials($this->data['order']->id_shop)
        );
        $this->setLabelOutput(true);
        $this->setLabelService();
        $this->setReturnLabelCustomsDeclarations($colissimoLabel);
        $this->setReturnLabelAddresses();
        $this->setReturnLabelOptions();
        $this->setReturnCustomFields();

        return $this->generateLabel($colissimoLabel->id);
    }

    /**
     * @return ColissimoLabel
     * @throws Exception
     * @throws PrestaShopException
     */
    /*18/09/2023 - Modif -G- ajout d'un paramètre pour savoir le poste pour imprimer en ZPL ou DPL*/
    public function generate($poste = false)
    {
        $this->logger->info(
            sprintf('Order #%d (%s) - Generate label', $this->data['order']->id, $this->data['order']->reference)
        );
        $this->request = new ColissimoGenerateLabelRequest(
            ColissimoTools::getCredentials($this->data['order']->id_shop)
        );
        $this->setLabelOutput(false, $poste);
        $this->setLabelService();
        $this->setLabelCustomsDeclarations();
        $this->setLabelAddresses();
        $this->setLabelOptions();
        $this->setCustomFields();

        return $this->generateLabel(false, $poste);
    }
}
