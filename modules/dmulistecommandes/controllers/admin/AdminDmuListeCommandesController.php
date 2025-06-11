<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from SARL DREAM ME UP
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL DREAM ME UP is strictly forbidden.
 *
 *   .--.
 *   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
 *   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
 *   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
 *        w w w . d r e a m - m e - u p . f r       '
 *
 *  @author    Dream me up <prestashop@dream-me-up.fr>
 *  @copyright 2007 - 2023 Dream me up
 *  @license   All Rights Reserved
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminDmuListeCommandesController extends ModuleAdminController
{
    protected $id_vue;
    protected $_groupBy;
    protected $special_search;
    protected $statuses_array;
    protected $carriers_array;

    public function preProcess()
    {
        require_once _PS_MODULE_DIR_ . '/dmulistecommandes/classes/VuesCommandes.php';

        if (!Module::isEnabled('dmulistecommandes')) {
            Tools::redirectAdmin('index.php?controller=AdminOrders&token=' . Tools::getAdminTokenLite('AdminOrders'));
        }

        if (!isset($this->context)) {
            $this->context = Context::getContext();
        }

        // Sélection de la vue (et enregistrement)
        if (Tools::getIsset('id_vue')) {
            $this->id_vue = (int) Tools::getValue('id_vue');
            $this->context->cookie->id_vue = $this->id_vue;
        } elseif (isset($this->context->cookie->id_vue)) {
            $this->id_vue = $this->context->cookie->id_vue;
        } else {
            $this->id_vue = (int) VuesCommandes::getDefaultId();
        }

        // Actions groupées
        if (Tools::getIsset('orderBox')) {
            $orders_array = [];
            if (Tools::isSubmit('submitBulkupdateOrderStatusorder') && Tools::getIsset('status')) {
                $id_order_state = (int) Tools::getValue('status');
                $order_state = new OrderState($id_order_state);

                if (!Validate::isLoadedObject($order_state)) {
                    $this->errors[] = Tools::displayError('Order status #' . $id_order_state . ' cannot be loaded');
                } else {
                    foreach (Tools::getValue('orderBox') as $id_order) {
                        $order = new Order((int) $id_order);
                        if (!Validate::isLoadedObject($order)) {
                            $this->errors[] = sprintf(Tools::displayError('Order #%d cannot be loaded'), $id_order);
                        } else {
                            $current_order_state = $order->getCurrentOrderState();
                            if ($current_order_state->id == $order_state->id) {
                                $this->errors[] = $this->displayWarning(
                                    'Order #' . $id_order . ' has already been assigned this status.'
                                );
                            } else {
                                $history = new OrderHistory();
                                $history->id_order = $order->id;
                                $history->id_employee = (int) $this->context->employee->id;

                                $use_existings_payment = !$order->hasInvoice();
                                $history->changeIdOrderState((int) $order_state->id, $order, $use_existings_payment);

                                $carrier = new Carrier($order->id_carrier, $order->id_lang);
                                $templateVars = [];

                                if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
                                    if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING')
                                        && $order->getShippingNumber()) {
                                        $templateVars = [
                                            '{followup}' => str_replace('@', $order->getShippingNumber(), $carrier->url),
                                        ];
                                    }
                                } else {
                                    if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING')
                                        && $order->shipping_number) {
                                        $templateVars = [
                                            '{followup}' => str_replace('@', $order->shipping_number, $carrier->url),
                                        ];
                                    }
                                }

                                if ($history->addWithemail(true, $templateVars)) {
                                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                                        foreach ($order->getProducts() as $product) {
                                            if (StockAvailable::dependsOnStock($product['product_id'])) {
                                                StockAvailable::synchronize(
                                                    $product['product_id'],
                                                    (int) $product['id_shop']
                                                );
                                            }
                                        }
                                    }
                                } else {
                                    $error = 'Cannot change status for order #' . $id_order . '.';
                                    $this->errors[] = Tools::displayError($error);
                                }
                            }
                        }
                    }
                }
            } else {
                foreach (Tools::getValue('orderBox') as $id_order) {
                    $orders_array[] = $id_order;
                }
                sort($orders_array);
            }

            if (Tools::isSubmit('submitBulkgenerateInvoicesorder')) {
                ob_end_clean();
                $sql = 'SELECT oi.*
                        FROM `' . _DB_PREFIX_ . 'order_invoice` oi
                        LEFT JOIN `' . _DB_PREFIX_ . 'orders` o
                            ON (o.`id_order` = oi.`id_order`)
                        WHERE oi.id_order IN (' . implode(',', $orders_array) . ')
                            '/* .Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o') */ . '
                        ORDER BY oi.date_add ASC';
                $order_invoices_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                $collection = ObjectModel::hydrateCollection('OrderInvoice', $order_invoices_list);
                $this->context->currency = Currency::getDefaultCurrency();
                $pdf = new PDF($collection, PDF::TEMPLATE_INVOICE, $this->context->smarty);
                $pdf->render();
                exit;
            }
            if (Tools::isSubmit('submitBulkgenerateDeliveriesorder')) {
                ob_end_clean();
                $sql = 'SELECT oi.*
                        FROM `' . _DB_PREFIX_ . 'order_invoice` oi
                        LEFT JOIN `' . _DB_PREFIX_ . 'orders` o
                            ON (o.`id_order` = oi.`id_order`)
                        WHERE oi.id_order IN (' . implode(',', $orders_array) . ')
                            '/* .Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o') */ . '
                        ORDER BY oi.delivery_date ASC';
                $order_invoices_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                $collection = ObjectModel::hydrateCollection('OrderInvoice', $order_invoices_list);
                $this->context->currency = Currency::getDefaultCurrency();
                $pdf = new PDF($collection, PDF::TEMPLATE_DELIVERY_SLIP, $this->context->smarty);
                $pdf->render();
                exit;
            }
        }
    }

    public function __construct()
    {
        $this->preProcess();

        $this->bootstrap = true;
        $this->table = 'order';
        $this->className = 'Order';
        $this->addRowAction('view');
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->deleted = false;
        $this->context = Context::getContext();
        $this->tpl_list_vars = ['token' => Tools::getAdminTokenLite('AdminDmuListeCommandes')];

        $this->token = Tools::getAdminToken(
            $this->module . (int) Tab::getIdFromClassName($this->module) . (int) Context::getContext()->cookie->id_employee
        );

        parent::__construct();

        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $this->_select = '
            a.id_currency, a.id_order AS id_pdf, a.id_customer, a.id_address_delivery, a.reference, oc.tracking_number,
            CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
            osl.`name` AS `osname`,
            os.`color`,
            IF((SELECT so.id_order FROM `' . _DB_PREFIX_ . 'orders` so
                WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
            country_lang.name as cname,
            cl.id_reference AS carrier_id_reference,
            IF(a.valid, 1, 0) badge_success,
            0 AS product_search';
        } else {
            $this->_select = '
            a.id_currency, a.id_order AS id_pdf, a.id_customer, a.id_address_delivery, a.reference, a.shipping_number, oc.tracking_number,
            CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
            osl.`name` AS `osname`,
            os.`color`,
            IF((SELECT so.id_order FROM `' . _DB_PREFIX_ . 'orders` so
                WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
            country_lang.name as cname,
            cl.id_reference AS carrier_id_reference,
            IF(a.valid, 1, 0) badge_success,
            0 AS product_search';
        }

        if (Module::isInstalled('deliverydateswizardpro') && Module::isEnabled('deliverydateswizardpro')) {
            $this->_select .= ', 
                (SELECT o.ddw_order_date
                FROM `' . _DB_PREFIX_ . 'orders` o
                WHERE o.id_order = a.id_order)
                    as ddw_order_date,
                (SELECT o.ddw_order_time
                FROM `' . _DB_PREFIX_ . 'orders` o
                WHERE o.id_order = a.id_order)
                    as ddw_order_time';
        }

        // Colonnes suppplémentaires
        if (Configuration::getGlobalValue('DMU_SHOW_COL_COUNTRY')
            || Configuration::getGlobalValue('DMU_SHOW_COL_STATE')
            || Configuration::getGlobalValue('DMU_SHOW_COL_POSTCODE')) {
            $this->_select .= ', ad.id_country, ad.id_state, ad.postcode';
            if (Configuration::getGlobalValue('DMU_SHOW_COL_COUNTRY')) {
                $this->_select .= ', cou.name as country';
            }
            if (Configuration::getGlobalValue('DMU_SHOW_COL_STATE')) {
                $this->_select .= ', sta.name as state';
            }
        }

        $this->_join = '
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` c
            ON (c.`id_customer` = a.`id_customer`)
        INNER JOIN `' . _DB_PREFIX_ . 'address` address
            ON address.id_address = a.id_address_delivery
        INNER JOIN `' . _DB_PREFIX_ . 'country` country
            ON address.id_country = country.id_country
        INNER JOIN `' . _DB_PREFIX_ . 'country_lang` country_lang
            ON (country.`id_country` = country_lang.`id_country`
                AND country_lang.`id_lang` = ' . (int) $this->context->language->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os
            ON (os.`id_order_state` = a.`current_state`)
        LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl
            ON (os.`id_order_state` = osl.`id_order_state`
                AND osl.`id_lang` = ' . (int) $this->context->language->id . ')
        LEFT JOIN `' . _DB_PREFIX_ . 'carrier` cl
            ON (cl.`id_carrier` = a.`id_carrier`)
        LEFT JOIN `' . _DB_PREFIX_ . 'order_carrier` oc
        ON (oc.`id_carrier` = a.`id_carrier` AND oc.`id_order` = a.`id_order`)';

        // Colonnes suppplémentaires
        if (Configuration::getGlobalValue('DMU_SHOW_COL_COUNTRY')
            || Configuration::getGlobalValue('DMU_SHOW_COL_STATE')
            || Configuration::getGlobalValue('DMU_SHOW_COL_POSTCODE')) {
            $this->_join .= '
            LEFT JOIN `' . _DB_PREFIX_ . 'address` ad
                ON ad.id_address = a.id_address_delivery';
            if (Configuration::getGlobalValue('DMU_SHOW_COL_COUNTRY')) {
                $this->_join .= '
                LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cou
                    ON cou.id_country = ad.id_country
                    AND cou.id_lang = ' . (int) $this->context->language->id;
            }
            if (Configuration::getGlobalValue('DMU_SHOW_COL_STATE')) {
                $this->_join .= '
                LEFT JOIN `' . _DB_PREFIX_ . 'state` sta
                    ON sta.id_state = ad.id_state';
            }
        }

        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';

        // ** ** Recherche par produit
        if (Tools::isSubmit('submitResetorder')) {
            $this->context->cookie->dmulistecommandesorderFilter_product_search = null;
        } else {
            if (Tools::getIsset('orderFilter_product')
                || isset($this->context->cookie->dmulistecommandesorderFilter_product_search)) {
                if (Tools::getIsset('orderFilter_product')) {
                    $product_name = Tools::getValue('orderFilter_product');
                } else {
                    $product_name = $this->context->cookie->dmulistecommandesorderFilter_product_search;
                }
				
                if (preg_match('/[a-zA-Z]/', $product_name)) {
                    $words = explode(' ', $product_name);
                    $req = '';
                    foreach ($words as $word) {
                        $req .= " AND (_od.product_name LIKE '%" . $word .
                            "%' OR _od.product_reference LIKE '%" . $word . "%') ";
                    }
                    $this->_join .= ' INNER JOIN `' . _DB_PREFIX_ . 'order_detail` _od
                                        ON _od.id_order = a.id_order ' . $req;
                    $this->_group = ' GROUP BY a.id_order ';
                    $this->_groupBy = ' GROUP BY a.id_order ';
                    $this->context->cookie->dmulistecommandesorderFilter_total_paid_tax_incl = null;
                    $this->context->cookie->dmulistecommandesorderFilter_product_search = $product_name;
                } else {
                    $this->context->cookie->dmulistecommandesorderFilter_product_search = null;
                }
                if (isset($this->context->cookie->dmulistecommandesorderFilter_product_search)) {
                    $product_search = $this->context->cookie->dmulistecommandesorderFilter_product_search;
                    $this->special_search['product'] = $product_search;
                }
            }
        }
		
		// Dorian
		if (Tools::getIsset('orderFilter_id_cart')) {
            $orderFilter_id_cart = Tools::getValue('orderFilter_id_cart'); 
			if($orderFilter_id_cart){
				$this->_where .=  " OR o.utm_medium LIKE '%" . $orderFilter_id_cart ."%' ";
			}
        }
		
        // ** ** Recherche par code postal
        if (Tools::getIsset('orderFilter_postcode')) {
            $this->context->cookie->dmulistecommandesorderFilter_postcode = Tools::getValue('orderFilter_postcode');
        }
        if (isset($this->context->cookie->dmulistecommandesorderFilter_postcode)) {
            $postcode = $this->context->cookie->dmulistecommandesorderFilter_postcode;
            $this->_where .= ' AND ad.postcode LIKE \'' . $postcode . '%\' ';
        }
        if (Tools::getIsset('orderFilter_postcode_from')) {
            unset($this->context->cookie->dmulistecommandesorderFilter_postcode);
            unset($this->context->cookie->dmulistecommandesorderFilter_postcode_from);
            unset($this->context->cookie->dmulistecommandesorderFilter_postcode_to);
            $postcode_from = Tools::getValue('orderFilter_postcode_from');
            $postcode_to = Tools::getIsset('orderFilter_postcode_from') ?
                Tools::getValue('orderFilter_postcode_to') : null;
            if ($postcode_from) {
                $this->context->cookie->dmulistecommandesorderFilter_postcode_from = $postcode_from;
            }
            if ($postcode_to) {
                $this->context->cookie->dmulistecommandesorderFilter_postcode_to = $postcode_to;
            }
        }
        if (isset($this->context->cookie->dmulistecommandesorderFilter_postcode_from)) {
            $postcode_from = $this->context->cookie->dmulistecommandesorderFilter_postcode_from;
            $postcode_to = $this->context->cookie->dmulistecommandesorderFilter_postcode_to;
            $postcode_to = $postcode_to ? $postcode_to : $postcode_from;
            $this->_where .= ' AND ad.postcode >= \'' . $postcode_from . '\'
                                AND ad.postcode <= \'' . $postcode_to . '\' ';
            $this->context->cookie->dmulistecommandesorderFilter_postcode = '%';
        }
        if (Tools::isSubmit('submitResetorder')) {
            unset($this->context->cookie->dmulistecommandesorderFilter_postcode);
            unset($this->context->cookie->dmulistecommandesorderFilter_postcode_from);
            unset($this->context->cookie->dmulistecommandesorderFilter_postcode_to);
        }

        // Sélection des statuts à afficher selon la vue
        $selected_view = new VuesCommandes($this->id_vue);
        if ($selected_view->statuts) {
            $this->_where = ' AND (os.`id_order_state` IS NULL OR os.`id_order_state` 
                IN (0, NULL, ' . $selected_view->statuts . ')) ';
        }

        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }

        $carriers = Carrier::getCarriers(
            $this->context->language->id,
            false,
            false,
            false,
            null,
            Carrier::ALL_CARRIERS
        );
        foreach ($carriers as $carrier) {
            $this->carriers_array[$carrier['id_reference']] = $carrier['name'];
        }

        $this->fields_list = [
            'id_order' => [
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'reference' => [
                'title' => $this->l('Reference', 'AdminDmuListeCommandesController'),
            ],
        ];

        if (Configuration::getGlobalValue('DMU_SHOW_COL_CART')) {
            $this->fields_list = array_merge($this->fields_list, [
                'id_cart' => [
                    'title' => $this->l('Cart ID', 'AdminDmuListeCommandesController'),
                    'align' => 'text-center',
                    'class' => 'fixed-width-xs',
                    'orderby' => true,
                ],
            ]);
        }

        if (Configuration::getGlobalValue('DMU_SHOW_COL_INVOICE')) {
            $this->fields_list = array_merge($this->fields_list, [
                'invoice_number' => [
                    'title' => $this->l('Invoice Number', 'AdminDmuListeCommandesController'),
                    'align' => 'text-center',
                    'class' => 'fixed-width-xs',
                    'orderby' => true,
                    'callback' => 'callbackInvoiceNumber',
                ],
            ]);
        }
		
		// Ajout Dorian - Début
		$this->fields_list = array_merge($this->fields_list, [
			'invoice_number' => [
				'title' => $this->l('Codes utilisés', 'AdminDmuListeCommandesController'),
				'align' => 'text-center',
				'class' => 'fixed-width-xs',
				'havingFilter' => true,
				'orderby' => true,
				'callback' => 'callbackCodesUtilises',
			],
		]);
		// Ajout Dorian - Fin
		
		// Ajout Dorian - Début
		$this->fields_list = array_merge($this->fields_list, [
			'id_cart' => [
				'title' => $this->l('UTM', 'AdminDmuListeCommandesController'),
				'align' => 'text-center',
				'class' => 'fixed-width-xs',
				'havingFilter' => true,
				'orderby' => true,
				'callback' => 'utmMedium',
			],
		]);
		// Ajout Dorian - Fin

        if (Configuration::getGlobalValue('DMU_SHOW_COL_NEW')) {
            $this->fields_list = array_merge($this->fields_list, [
                'new' => [
                    'title' => $this->l('New client', 'AdminDmuListeCommandesController'),
                    'align' => 'text-center',
                    /* 'type' => 'bool', */
                    'tmpTableFilter' => true,
                    'orderby' => false,
                    'callback' => 'callbackNewCustomer',
                ],
            ]);
        }
        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, [
                'company' => [
                    'title' => $this->l('Company', 'AdminDmuListeCommandesController'),
                    'filter_key' => 'c!company',
                ],
            ]);
        }

        $this->fields_list = array_merge($this->fields_list, [
            'customer' => [
                'title' => $this->l('Customer'),
                'havingFilter' => true,
                'callback' => 'callbackCustomer',
                'class' => 'order_customer',
            ],
            'total_paid_tax_incl' => [
                'title' => $this->l('Total / products'),
                'align' => 'text-right',
                /* 'type' => 'price', */
                'currency' => true,
                'callback' => 'callbackOrderTotal',
                'badge_success' => true,
            ],
        ]);

        if (Configuration::getGlobalValue('DMU_SHOW_COL_GIFT')) {
            $giftImage = Context::getContext()->smarty->fetch(_PS_MODULE_DIR_ . '/dmulistecommandes/views/templates/admin/dmu_liste_commandes/gift-image.tpl', [
                'url_img' => Tools::getHttpHost(true) . __PS_BASE_URI__,
                'message' => $this->l('Gift wrapping / Message'),
            ]);

            $this->fields_list = array_merge($this->fields_list, [
                'gift' => [
                    'title' => Tools::getIsset('exportorder') ? $this->l('Gift') : $giftImage,
                    'orderby' => false,
                    'search' => false,
                    'callback' => 'callbackMessage',
                ],
            ]);
        }

        if (Configuration::getGlobalValue('DMU_SHOW_COL_PAYMENT')) {
            $this->fields_list = array_merge($this->fields_list, [
                'payment' => [
                    'title' => $this->l('Payment'),
                ],
            ]);
        }

        $this->fields_list = array_merge($this->fields_list, [
            'osname' => [
                'title' => $this->l('Status'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname',
                'callback' => 'callbackStatus',
            ],
            'date_add' => [
                'title' => $this->l('Date'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            ],
            'id_pdf' => [
                'title' => $this->l('PDF'),
                'align' => 'text-center',
                'callback' => 'callbackPDFIcons',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
            ],
        ]);
        if (Module::isInstalled('deliverydateswizardpro') && Module::isEnabled('deliverydateswizardpro')) {
            $this->fields_list = array_merge($this->fields_list, [
                'ddw_order_date' => [
                    'title' => $this->l('Delivery Date'),
                    'align' => 'text-right',
                    'type' => 'datetime',
                    'filter_key' => 'ddw_order_date',
                    'callback' => 'callbackDeliveryDatesWizardPro',
                    'class' => 'fixed-width-xl',
                    'width' => '100',
                ],
            ]);
        }
        if (Configuration::getGlobalValue('DMU_SHOW_COL_CARRIER')) {
            $this->fields_list = array_merge($this->fields_list, [
                'carrier_id_reference' => [
                    'title' => $this->l('Shipping'),
                    'type' => 'select',
                    'list' => $this->carriers_array,
                    'filter_key' => 'cl!id_reference',
                    'filter_type' => 'int',
                    'callback' => 'callbackCarrier',
                    'class' => 'order_carrier',
                ],
            ]);
        }
        if (Configuration::getGlobalValue('DMU_SHOW_COL_COUNTRY')) {
            $this->fields_list = array_merge($this->fields_list, [
                'country' => [
                    'title' => $this->l('Country'),
                    'havingFilter' => true,
                ],
            ]);
        }
        if (Configuration::getGlobalValue('DMU_SHOW_COL_STATE')) {
            $this->fields_list = array_merge($this->fields_list, [
                'state' => [
                    'title' => $this->l('State'),
                    'havingFilter' => true,
                ],
            ]);
        }
        if (Configuration::getGlobalValue('DMU_SHOW_COL_POSTCODE')) {
            $this->fields_list = array_merge($this->fields_list, [
                'postcode' => [
                    'title' => $this->l('Zip code'),
                    'havingFilter' => true,
                ],
            ]);
        }

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_ORDER;

        $this->bulk_actions = [
            'generateInvoices' => ['text' => $this->l('Print Order Invoices'), 'icon' => 'icon-print'],
            'generateDeliveries' => ['text' => $this->l('Print Order Deliveries'), 'icon' => 'icon-truck'],
            // 'divider' => array('text' => 'divider'),
            // 'updateOrderStatus' => array('text' => $this->l('Change Order Status'), 'icon' => 'icon-refresh')
        ];
    }

    public function callbackDeliveryDatesWizardPro($ddw_order_date, $tr)
    {
        $return = Tools::displayDate($tr['ddw_order_date']) . ' - ';
        if ('' != $tr['ddw_order_time']) {
            $return .= ' (' . $tr['ddw_order_time'] . ')';
        }

        return $return;
    }

    public function callbackNewCustomer($new, $tr)
    {
        if (Tools::getIsset('exportorder')) {
            return $new;
        }
        $img = version_compare(_PS_VERSION_, '1.7', '<') ? 'tick.png' : 'enabled.gif';

        $this->context->smarty->assign([
            'img' => ($tr['new'] ? $img : ''),
            'new' => $new,
        ]);

        return $this->createTemplate('callback_new_customer.tpl')->fetch();
    }

    public function callbackCustomer($customer, $tr)
    {
        $delivery_address = new Address($tr['id_address_delivery']);
        $customer = new Customer($tr['id_customer']);
        $iso = Country::getIsoById($delivery_address->id_country);

        $customer_name = Tools::strtoupper($customer->lastname) . ' ' . Tools::ucfirst($customer->firstname);
        $customer_name_short = $customer_name;
        if (preg_match("/^[a-zA-ZÀ-ÖØ-öø-ÿœŒ'\ ]+$/", $customer_name)) {
            $customer_name = Tools::ucfirst($customer->firstname) . ' ' . Tools::strtoupper($customer->lastname);
            $customer_name_short = Tools::strtoupper(Tools::substr($customer->firstname, 0, 1) . '. ' .
                $customer->lastname);
        }

        $this->context->smarty->assign([
            'iso' => (file_exists(_PS_MODULE_DIR_ . '/dmulistecommandes/views/img/flags/' . Tools::strtolower($iso) . '.gif') ?
                Tools::strtolower($iso) : ''),
            'customer_name' => $customer_name,
            'customer_name_short' => $customer_name_short,
            'customer' => $customer,
            'token' => Tools::getAdminTokenLite('AdminCustomers'),
            'ps7' => version_compare(_PS_VERSION_, '1.7', '>='),
            'delivery_address' => $delivery_address,
            'address_format' => nl2br(AddressFormat::generateAddress($delivery_address)),
        ]);

        return $this->createTemplate('callback_customer.tpl')->fetch();
    }

    public function callbackOrderTotal($total, $tr)
    {
        $order = new Order($tr['id_order']);
        $products = $order->getProducts();

        $rest = 0;
        $products_qty = 0;
        if ($products) {
            $cumulation = 0; // modif faites : total_wt => total_price_tax_incl
            foreach ($products as $product) {
                $cumulation += $product['total_price_tax_incl'];
                $products_qty += $product['product_quantity'];
            }
            $rest = $total - $cumulation;
        }

        $this->context->smarty->assign([
            'price_total' => Tools::displayPrice($total, (int) $order->id_currency),
            'products_qty' => $products_qty,
            'dmu_show_buttons' => Configuration::getGlobalValue('DMU_SHOW_BUTTONS'),
            'products' => $products,
            'rest' => Tools::displayPrice($rest),
            'total' => Tools::displayPrice($total),
        ]);

        return $this->createTemplate('callback_order_total.tpl')->fetch();
    }

    public function callbackMessage($gift, $tr)
    {
        $is_message = false;
        $gift_message = null;

        $all_cmessages = [];
        if ($cmessages = CustomerMessage::getMessagesByOrderId($tr['id_order'])) {
            $is_message = true;
            foreach ($cmessages as $cm) {
                $all_cmessages[] = $cm['message'];
            }
        }
        $messages = Message::getMessagesByOrderId($tr['id_order']);
        foreach ($messages as $key => $message) {
            if (in_array($message['message'], $all_cmessages)) {
                unset($messages[$key]);
            }
        }

        if ($gift) {
            $order = new Order($tr['id_order']);
            $gift_message = $order->gift_message;
        }

        if ($messages || $gift) {
            $is_message = true;
        }
        $this->context->smarty->assign([
            'gift' => $gift,
            'gift_message' => nl2br($gift_message),
            'is_message' => $is_message,
            'cmessages' => $cmessages,
            'messages' => $messages,
            'exportorder' => Tools::getIsset('exportorder'),
        ]);

        return $this->createTemplate('callback_message.tpl')->fetch();
    }

    public function callbackStatus($status, $tr)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'order_history` oh
                LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl
                    ON osl.id_order_state = oh.id_order_state
                    AND osl.id_lang = ' . (int) $this->context->language->id . '
                WHERE oh.id_order = ' . (int) $tr['id_order'] . '
                ORDER BY oh.date_add';
        $order_history = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        $this->context->smarty->assign([
            'order_history' => $order_history,
            'status' => $status,
        ]);

        return $this->createTemplate('callback_status.tpl')->fetch();
    }

    public function callbackPDFIcons($id_order, $tr)
    {
        static $valid_order_state = [];

        $order = new Order($id_order);
        if (!Validate::isLoadedObject($order)) {
            return '';
        }

        if (!isset($valid_order_state[$order->current_state])) {
            $valid_order_state[$order->current_state] = Validate::isLoadedObject($order->getCurrentOrderState());
        }

        if (!$valid_order_state[$order->current_state]) {
            return '';
        }

        $this->context->smarty->assign([
            'order' => $order,
            'order_state' => $order->getCurrentOrderState(),
            'tr' => $tr,
        ]);

        return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
    }

    public function callbackCarrier($id_reference, $tr)
    {
        $sql = 'SELECT `id_carrier` FROM `' . _DB_PREFIX_ . 'carrier`
                WHERE id_reference = ' . (int) $id_reference . '
                ORDER BY deleted ASC, id_carrier DESC';
        $carrier = false;
        $background = false;
        $order_weight = false;
        if ($id_carrier = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql)) {
            $carrier = new Carrier($id_carrier);

            $dmu_carriers_colors = json_decode(Configuration::getGlobalValue('DMU_CARRIERS_COLORS'), true);
            $background = isset($dmu_carriers_colors[$id_reference]) ? $dmu_carriers_colors[$id_reference] : '#e98328';

            // Récupération du poids du colis
            $sql = 'SELECT SUM(weight) FROM `' . _DB_PREFIX_ . 'order_carrier`
                    WHERE id_order = ' . (int) $tr['id_order'];
            $order_weight = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);

            $url = null;

            if (!empty($tr['tracking_number'])) {
                $tr['shipping_number'] = $tr['tracking_number'];
            }

            if (isset($tr['shipping_number']) && !empty($tr['shipping_number'])) {
                if ($carrier->url) {
                    $url = str_replace('@', $tr['shipping_number'], $carrier->url);
                } else {
                    $url = 'nourl';
                }
            }
        }

        $this->context->smarty->assign([
            'tr' => $tr,
            'carrier' => $carrier,
            'background' => $background,
            'text_bg' => VuesCommandes::textColor($background),
            'order_weight' => round($order_weight, 3),
            'id_carrier' => $id_carrier,
            'url' => (isset($url) ? $url : null),
            'exportorder' => Tools::getIsset('exportorder'),
        ]);

        return $this->createTemplate('callback_carrier.tpl')->fetch();
    }

    public function callbackInvoiceNumber($invoice_number)
    {
        if (empty($invoice_number)) {
            return '--';
        } else {
            $oi = OrderInvoice::getInvoiceByNumber($invoice_number);
            if ($oi) {
                return $oi->getInvoiceNumberFormatted($this->context->language->id);
            } else {
                return '--';
            }
        }
    }
	
	// Ajout Dorian - Début
	public function callbackCodesUtilises($code, $tr)
    {
		$codes = [];
		$order = new Order($tr['id_order']);
		$cart_rules = $order->getCartRules();

		foreach($cart_rules as $cart_rule){
			
			$cartrule = new CartRule($cart_rule['id_cart_rule']);
			
			if($cartrule->code){
				
				// exclure les codes automatiques, genre "-20% sur....", voire même "10% à partir de 100€"
				// pour ne garder que les codes inscrit par le client
			
				if(strpos($cartrule->code, "QD_") === 0){
					continue;
				}
				if(isset($cart_rule['name'])){
					$codes[] = '<div style="padding: 5px 0">'.$cart_rule['name'].'</div>';
					//$codes[] = '<div style="padding: 5px 0">'.$cartrule->code.'</div>';
				}
			}
		}
				
        return implode('',$codes);

    }
	// Ajout Dorian - Fin
	
	// Ajout Dorian - Début
	public function utmMedium($code, $tr)
    {
		$id_order = (int) $tr['id_order'];
		$order = new Order($tr['id_order']);
		//$cart_rules = $order->getCartRules();
		
		$sql = 'SELECT utm_medium FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = '.$id_order;
		$res = Db::getInstance()->executeS($sql);
	  
		$utm_medium = $res[0]['utm_medium'];
        return $utm_medium;

    }
	// Ajout Dorian - Fin

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->addJquery();
            $this->addCSS(__PS_BASE_URI__ . 'modules/dmulistecommandes/views/css/backoffice.css');
            $this->addJS(__PS_BASE_URI__ . 'modules/dmulistecommandes/views/js/backoffice.js');
        } else {
            $this->context->controller->addJquery();
            $this->context->controller->addCSS(_PS_MODULE_DIR_ . '/dmulistecommandes/views/css/backoffice.css');
            $this->context->controller->addJS(_PS_MODULE_DIR_ . '/dmulistecommandes/views/js/backoffice.js');
        }
    }

    public function renderKpis()
    {
        if (!Configuration::getGlobalValue('DMU_SHOW_KPI')) {
            return;
        }

        $time = time();
        $kpis = [];

        /* The data generation is located in AdminStatsControllerCore */

        $helper = new HelperKpi();
        $helper->id = 'box-conversion-rate';
        $helper->icon = 'icon-sort-by-attributes-alt';
        // $helper->chart = true;
        $helper->color = 'color1';
        $helper->title = $this->l('Conversion Rate', null, null, false);
        $helper->subtitle = $this->l('30 days', null, null, false);
        if (false !== ConfigurationKPI::get('CONVERSION_RATE')) {
            $helper->value = ConfigurationKPI::get('CONVERSION_RATE');
        }
        if (false !== ConfigurationKPI::get('CONVERSION_RATE_CHART')) {
            $helper->data = ConfigurationKPI::get('CONVERSION_RATE_CHART');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=conversion_rate';
        $helper->refresh = (bool) (ConfigurationKPI::get('CONVERSION_RATE_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-carts';
        $helper->icon = 'icon-shopping-cart';
        $helper->color = 'color2';
        $helper->title = $this->l('Abandoned Carts', null, null, false);
        $helper->subtitle = $this->l('Today', null, null, false);
        $helper->href = $this->context->link->getAdminLink('AdminCarts') . '&action=filterOnlyAbandonedCarts';
        if (false !== ConfigurationKPI::get('ABANDONED_CARTS')) {
            $helper->value = ConfigurationKPI::get('ABANDONED_CARTS');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=abandoned_cart';
        $helper->refresh = (bool) (ConfigurationKPI::get('ABANDONED_CARTS_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-average-order';
        $helper->icon = 'icon-money';
        $helper->color = 'color3';
        $helper->title = $this->l('Average Order Value', null, null, false);
        $helper->subtitle = $this->l('30 days', null, null, false);
        if (false !== ConfigurationKPI::get('AVG_ORDER_VALUE')) {
            $helper->value = ConfigurationKPI::get('AVG_ORDER_VALUE');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats')
            . '&ajax=1&action=getKpi&kpi=average_order_value';
        $helper->refresh = (bool) (ConfigurationKPI::get('AVG_ORDER_VALUE_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        $helper = new HelperKpi();
        $helper->id = 'box-net-profit-visit';
        $helper->icon = 'icon-user';
        $helper->color = 'color4';
        $helper->title = $this->l('Net Profit per Visit', null, null, false);
        $helper->subtitle = $this->l('30 days', null, null, false);
        if (false !== ConfigurationKPI::get('NETPROFIT_VISIT')) {
            $helper->value = ConfigurationKPI::get('NETPROFIT_VISIT');
        }
        $helper->source = $this->context->link->getAdminLink('AdminStats') . '&ajax=1&action=getKpi&kpi=netprofit_visit';
        $helper->refresh = (bool) (ConfigurationKPI::get('NETPROFIT_VISIT_EXPIRE') < $time);
        $kpis[] = $helper->generate();

        if ($this->id_vue) {
            $view = new VuesCommandes($this->id_vue);

            $helper = new HelperKpi();
            $helper->id = 'kpi_listecommandes';
            $helper->icon = 'icon-eye';
            $helper->color = 'color1';
            $helper->title = $view->name;
            $helper->subtitle = $this->l('View selection :');
            $helper->value = '...';
            $kpis[] = $helper->generate();
        }

        $this->context->smarty->assign([
            'kpis' => $kpis,
        ]);

        return $this->createTemplate('render_kpis.tpl')->fetch();
    }

    public function renderViews()
    {
        if (($views = VuesCommandes::getViews()) && Configuration::getGlobalValue('DMU_SHOW_VIEWS')) {
            $module_link = 'index.php?controller=AdminModules&configure=dmulistecommandes&module_tab=Views&token='
                . Tools::getAdminTokenLite('AdminModules');

            $this->context->smarty->assign([
                'module_link' => $module_link,
                'views' => $views,
                'token' => Tools::getAdminTokenLite('AdminDmuListeCommandes'),
                'id_vue' => $this->id_vue,
                'inf_ps6' => version_compare(_PS_VERSION_, '1.6', '<'),
            ]);

            return $this->createTemplate('render_views.tpl')->fetch();
        }
    }

    public function renderBulkPanel()
    {
        $this->context->smarty->assign([
            'order_states' => OrderState::getOrderStates($this->context->language->id),
        ]);

        return $this->createTemplate('render_bulk_panel.tpl')->fetch();
    }

    public function renderList()
    {
        return $this->beforeList() . parent::renderList() . $this->afterList();
    }

    public function beforeList()
    {
        $views = VuesCommandes::getViews();

        $dlc_status_on_line = Configuration::getGlobalValue('DMU_STATUS_ON_LINE') ? 'true' : 'false';

        $dlc_warning_txt = str_replace('"', '\\"', str_replace('\\', '\\\\', Tools::strtoupper($this->l('Warning !'))));
        $dlc_status_change_txt = $this->l('Are you sure you want to change the status for these %d orders ?');
        $dlc_status_change_txt = str_replace('\\', '\\\\', $dlc_status_change_txt);
        $dlc_status_change_txt = str_replace('"', '\\"', $dlc_status_change_txt);
        $dlc_views_list_txt = str_replace('"', '\\"', str_replace('\\', '\\\\', $this->l('Choose a view')));
        $dlc_tracking_txt = str_replace('"', '\\"', str_replace('\\', '\\\\', $this->l('Enter a tracking number :')));

        $this->context->smarty->assign([
            'dlc_oldversion' => (version_compare(_PS_VERSION_, '1.6', '<') ? 'true' : 'false'),
            'adminOrders_token' => Tools::getAdminTokenLite('AdminOrders'),
            'adminDmuListeCommandes_token' => Tools::getAdminTokenLite('AdminDmuListeCommandes'),
            'dlc_status_on_line' => $dlc_status_on_line,
            'dlc_show_buttons' => (Configuration::getGlobalValue('DMU_SHOW_BUTTONS') ? 'true' : 'false'),
            'dlc_warning_txt' => $dlc_warning_txt,
            'dlc_status_change_txt' => $dlc_status_change_txt,
            'dlc_views_list_txt' => $dlc_views_list_txt,
            'dlc_tracking_txt' => $dlc_tracking_txt,
            'dlc_from_txt' => str_replace('"', '\\"', $this->l('From')),
            'dlc_to_txt' => str_replace('"', '\\"', $this->l('To')),
            'dlc_postcode_from' => $this->context->cookie->dmulistecommandesorderFilter_postcode_from,
            'dlc_postcode_to' => $this->context->cookie->dmulistecommandesorderFilter_postcode_to,
            'views_list' => $views,
            'special_searches' => (isset($this->special_search) ? $this->special_search : null),
        ]);

        return $this->createTemplate('before_list.tpl')->fetch()
            . $this->renderViews()
            . $this->renderBulkPanel();
    }

    public function afterList()
    {
        $this->context->smarty->assign([
            'orderbox' => (Configuration::getGlobalValue('DMU_SAVE_CHECKED_ORDERS') && Tools::getIsset('orderBox') ?
                Tools::getValue('orderBox') : null),
        ]);

        return $this->createTemplate('after_list.tpl')->fetch();
    }

    public function displayAjaxSetTrackingNumber()
    {
        $ajax = ['success' => false];
        if (Tools::getIsset('id_order') && Tools::getIsset('tracking_number')) {
            $id_order = (int) Tools::getValue('id_order');
            $tracking_number = Tools::getValue('tracking_number');
            if (version_compare(_PS_VERSION_, '8.0.0', '<')) {
                $sql = 'UPDATE `' . _DB_PREFIX_ . 'orders`
                    SET shipping_number = \'' . pSQL($tracking_number) . '\'
                    WHERE id_order = ' . (int) $id_order;
                Db::getInstance()->execute($sql);
            }
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'order_carrier`
                    SET tracking_number = \'' . pSQL($tracking_number) . '\'
                    WHERE id_order = ' . (int) $id_order;
            Db::getInstance()->execute($sql);
            $sql = 'SELECT c.id_reference
                    FROM `' . _DB_PREFIX_ . 'carrier` c
                    INNER JOIN `' . _DB_PREFIX_ . 'orders` o
                        ON o.id_carrier = c.id_carrier
                    WHERE o.id_order = ' . (int) $id_order;
            $id_reference = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $ajax = [
                'success' => true,
                'id_order' => (int) $id_order,
                'html' => $this->callbackCarrier($id_reference, [
                    'id_order' => (int) $id_order,
                    'shipping_number' => pSQL($tracking_number),
                ]),
            ];
        }
        exit(json_encode($ajax));
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['new_order'] = [
            'href' => str_replace('sell/orders', 'sell/orders/new', $this->context->link->getAdminLink('AdminOrders')),
            'desc' => $this->l('Add new order'),
            'icon' => 'process-icon-new',
        ];
    }
}
