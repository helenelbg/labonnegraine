<?php

class ExportOrderFilter extends ObjectModel
{
    public $id_extension_export_order_filter;

    /** @var string Name */
    public $name;

    /** @var string Name */
    public $id_shop;

    /** @var string Description */
    public $description;

    /** @var string static definition */
    public $static_definition;

    /** @var string dynamic definition */
    public $dynamic_definition;

    /** @var string Object creation date */
    public $date_add;

    /** @var string Object last modification date */
    public $date_upd;

    public static $preview_limit = 100;

    public static $definition = array(
        'table' => SC_DB_PREFIX.'extension_export_order_filter',
        'primary' => 'id_extension_export_order_filter',
        'fields' => array(
            'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 128),
            'description' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'static_definition' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'dynamic_definition' => array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function getFilterList()
    {
        return Db::getInstance()->executeS('SELECT * 
                                                FROM `'._DB_PREFIX_.self::$definition['table'].'`');
    }

    public function getFields()
    {
        parent::validateFields();

        $sd = urldecode($this->static_definition);
        $dd = urldecode($this->dynamic_definition);

        ExportOrderTools::addLog('CList edit : Class.getFields()');
        ExportOrderTools::addLog('CList edit : SD:'.$this->static_definition.' becomes '.$sd);
        ExportOrderTools::addLog('CList edit : DD:'.$this->dynamic_definition.' becomes '.$dd);

        $fields = array();
        $fields['id_shop'] = (int) $this->id_shop;
        $fields['name'] = pSQL($this->name);
        $fields['description'] = pSQL($this->description);
        $fields['static_definition'] = pSQL($sd);
        // need to preserve HTML tages because of pSQL (it truncates if a rule operator is <=)
        $fields['dynamic_definition'] = pSQL($dd, true);
        $fields['date_add'] = pSQL($this->date_add);
        $fields['date_upd'] = pSQL($this->date_upd);

        return $fields;
    }

    /**
     * Build the SQL query to load all datas.
     */
    public function getSqlQuery($aliases, $id_lang, $orderby = null)
    {
        /*
            Filter Rules Data Storage :
                --> each rule begins by : operator| where operator could be NONE, NOT
                                // useful to apply a NOT to the whole rule
                --> Each rule might be defined by :
                        ruleName => ruleoperator|value
                                // example to include newsletter subscriptions : newsletter_fo=>NONE|1
                        ruleName => ruleoperator|operator|value
                                // example to apply a pattern to firstname : first_name=>NONE|LIKE|DOE
                        ruleName => ruleoperator|value1|operator1|operator2|value2
                                // example to apply a pattern to nb of orders : nb_orders=>NONE|1|<|<=|3
                --> each rule is separated from each other by this pattern : __operator__
                    where operator is the SQL link between the rules : AND, OR

         */

        /*
           select o.id_order,o.invoice_number,o.id_customer,o.id_address_delivery,o.id_address_invoice,o.gift,o.module,o.total_paid_real,
                   c.lastname,c.firstname,a1.city,c1.iso_code,c2.iso_code from ps_orders o

           inner join ps_customer c on o.id_customer=c.id_customer
           inner join ps_address a1 on o.id_address_delivery=a1.id_address
           inner join ps_country c1 on a1.id_country=c1.id_country
           inner join ps_country_lang cl1 on c1.id_country=cl1.id_country and cl1.id_lang=2
           inner join ps_address a2 on o.id_address_invoice=a2.id_address
           inner join ps_country c2 on a2.id_country=c2.id_country
           inner join ps_country_lang cl2 on c2.id_country=cl2.id_country and cl2.id_lang=2

           where o.id_order IN
           (
           select io.id_order from ps_orders io

           inner join ps_customer ic on io.id_customer=ic.id_customer
           inner join ps_address ia1 on io.id_address_delivery=ia1.id_address
           inner join ps_country ic1 on ia1.id_country=ic1.id_country
           inner join ps_country_lang icl1 on ic1.id_country=icl1.id_country and icl1.id_lang=2
           inner join ps_address ia2 on io.id_address_invoice=ia2.id_address
           inner join ps_country ic2 on ia2.id_country=ic2.id_country
           inner join ps_country_lang icl2 on ic2.id_country=icl2.id_country and icl2.id_lang=2

           where         (select oh.id_order_state from ps_orders o1
                       left join ps_order_history oh on o1.id_order=oh.id_order
                       where o1.id_order=io.id_order
                       order by oh.date_add desc limit 0, 1) IN (2,3,4,5)
                   AND io.date_add>'2011-12-01 00:00:00'
           )

           SQL query is made of two parts :
               > one is the query to list the orders to include in the response
                   > only one field is get (id_order)
                   > apply filter rules
               > one is the query to get the fields included in the input aliases list

        */

        $sql = '';
        ExportOrderTools::addLog('CList run :'.$this->id.' for aliases '.ExportOrderTools::captureVarDump($aliases).' and id_lang '.(int) $id_lang);

        // ********************************************************************************************************
        // Build the ORDER_IN clause
        //
        // what tables to include according to fields to export ?
        $includeCustomers = ExportOrderFields::isFieldOfThisType('customer', $aliases);
        $includeAddressesDelivery = ExportOrderFields::isFieldOfThisType('address_delivery', $aliases);
        $includeAddressesInvoice = ExportOrderFields::isFieldOfThisType('address_invoice', $aliases);
        $includeCustomerGroups = ExportOrderFields::isFieldOfThisType('group', $aliases);
        $includeCarrier = ExportOrderFields::isFieldOfThisType('carrier', $aliases);
        $includeCurrency = ExportOrderFields::isFieldOfThisType('currency', $aliases);
        $includeLastOrderHistory = ExportOrderFields::isFieldOfThisType('last_order_history', $aliases);
        $includeOrderDetail = ExportOrderFields::isFieldOfThisType('orderDetail', $aliases);
        $includeOrderDelivery = ExportOrderFields::isFieldOfThisType('orderDelivery', $aliases);
        $includeOrderProduct = ExportOrderFields::isFieldOfThisType('orderDetailProduct', $aliases);
        $includeOrderProductDelivery = ExportOrderFields::isFieldOfThisType('orderDetailDelivery', $aliases);
        $includeOrderProductCategory = ExportOrderFields::isFieldOfThisType('orderDetailCategory', $aliases);
        $includeOrderProductSupplier = ExportOrderFields::isFieldOfThisType('orderDetailSupplier', $aliases);
        $includeOrderProductManufacturer = ExportOrderFields::isFieldOfThisType('orderDetailManufacturer', $aliases);
        $includeOrderSlip = ExportOrderFields::isFieldOfThisType('order_slip', $aliases);
        $includeOrderInvoice = ExportOrderFields::isFieldOfThisType('orderInvoice', $aliases);
        if ($includeCustomerGroups)
        {
            $includeCustomers = true;
        }        // to include customers groups, have to include also customers
        if ($includeOrderProductCategory || $includeOrderProductSupplier || $includeOrderProductManufacturer)
        {
            $includeOrderProduct = true;
        }        // to include order products joined informations, have to include also order details products
        if ($includeOrderProduct)
        {
            $includeOrderDetail = true;
        }        // to include order products, have to include also order details
        if ($includeOrderProductDelivery)
        {
            $includeAddressesDelivery = true;
            $includeOrderDetail = true;
        }
        if ($includeOrderDelivery)
        {
            $includeAddressesDelivery = true;
        }
        if ($includeOrderDetail)
        {
            /*$includeOrderSlip = false;        // to include order products, have to include also order details
            foreach($aliases as $num=>$alias)
            {
                if($alias=="Calculated_Slip")
                    unset($aliases[$num]);
            }*/
        }
        $includes = array('includeCustomers' => $includeCustomers,
                            'includeAddressesDelivery' => $includeAddressesDelivery,
                            'includeAddressesInvoice' => $includeAddressesInvoice,
                            'includeCustomerGroups' => $includeCustomerGroups,
                            'includeCarrier' => $includeCarrier,
                            'includeCurrency' => $includeCurrency,
                            'includeLastOrderHistory' => $includeLastOrderHistory,
                            'includeOrderDetail' => $includeOrderDetail,
                            'includeOrderProduct' => $includeOrderProduct,
                            'includeOrderProductCategory' => $includeOrderProductCategory,
                            'includeOrderProductSupplier' => $includeOrderProductSupplier,
                            'includeOrderProductManufacturer' => $includeOrderProductManufacturer,
                            'includeOrderSlip' => $includeOrderSlip,
                            'includeOrderInvoice' => $includeOrderInvoice,
                            );
        ExportOrderTools::addLog('CList run fields :'.$this->id.'.Ic:'.$includeCustomers.',Iad:'.$includeAddressesDelivery.',Iai:'.$includeAddressesInvoice.',Ig:'.$includeCustomerGroups.',Iod:'.$includeOrderDetail);
        $mainSql = $this->getTableClause($aliases, $includes, true, $id_lang);

        // ********************************************************************************************************
        // Build the ORDER_IN clause
        //
        // analyze the dynamic definition if any to build the where clause
        $where = null;
        if ($this->dynamic_definition)
        {
            // what tables to include according to filter rules ?
            $includeCustomers = (strpos($this->dynamic_definition, 'c_') !== false);
            $includeAddressesDelivery = (strpos($this->dynamic_definition, 'a_d_') !== false);
            $includeAddressesInvoice = (strpos($this->dynamic_definition, 'a_i_') !== false);
            $includeCustomerGroups = (strpos($this->dynamic_definition, 'cg_') !== false);
            $includeProducts = (strpos($this->dynamic_definition, 'o_p_') !== false);
            $includeProductCategories = (strpos($this->dynamic_definition, 'o_p_cat_') !== false);
            $includeCarrier = (strpos($this->dynamic_definition, 'o_ca_') !== false);
            $includeCurrency = (strpos($this->dynamic_definition, 'o_cu_') !== false);
            $includeLastOrderHistory = (strpos($this->dynamic_definition, 'olh_') !== false);
            if ($includeCustomerGroups)
            {
                $includeCustomers = true;
            }                        // to include customers groups, have to include also customers
            if ($includeProductCategories)
            {
                $includeProductCategories = true;
            }        // to include product categories, have to include also products (but I think the rule o_p_cat_order_pdt_category will trigger o_p_ as well as o_p_cat_
            $includes = array('includeCustomers' => $includeCustomers,
                                'includeAddressesDelivery' => $includeAddressesDelivery,
                                'includeAddressesInvoice' => $includeAddressesInvoice,
                                'includeCustomerGroups' => $includeCustomerGroups,
                                'includeProducts' => $includeProducts,
                                'includeOrderInvoice' => $includeOrderInvoice,
                                'includeProductCategories' => $includeProductCategories,
                                'includeCarrier' => $includeCarrier,
                                'includeCurrency' => $includeCurrency,
                                'includeLastOrderHistory' => $includeLastOrderHistory,
                                );
            ExportOrderTools::addLog('CList run filter :'.$this->id.'.Ic:'.$includeCustomers.',Iad:'.$includeAddressesDelivery.',Iai:'.$includeAddressesInvoice.',Ig:'.$includeCustomerGroups.',Ip:'.$includeProducts.',Ipc:'.$includeProductCategories);
            $sqlInClause = $this->getTableClause(array('Order_Id'), $includes, false, $id_lang);

            // build the where clause
            $where = $this->analyzeRules($this->dynamic_definition);
        }

        // build the query
        if ($where)
        {
            if (ExportOrderTools::isSCMS())
            {
                if (!empty($this->id_shop))
                {
                    $sql .= $mainSql.'WHERE o.id_shop = "'.(int) $this->id_shop.'" AND o.id_order IN ('.$sqlInClause.' WHERE 1=1 '.$where.')';
                }
                else
                {
                    $sql .= $mainSql.'WHERE o.id_shop IN ('.pSQL(ExportOrderTools::getAllShopsId()).') AND o.id_order IN ('.$sqlInClause.' WHERE 1=1 '.$where.')';
                }
            }
            else
            {
                $sql .= $mainSql.'WHERE o.id_order IN ('.$sqlInClause.' WHERE 1=1 '.$where.')';
            }
        }
        else
        {
            $sql .= $mainSql;
        }

        if ($includeOrderDetail)
        {
            $sql .= ' GROUP BY od.`id_order_detail`';
        }
        else
        {
            $sql .= ' GROUP BY o.`id_order`';
        }

        // don't forget to also order by order_id if product details is to be exported
        if ($includeOrderDetail)
        {
            if ($orderby)
            {
                $orderby .= ', o.`id_order`';
            }        // other fields will be applied before this new one
            else
            {
                $orderby = 'o.`id_order`';
            }
        }

        // any order by to apply ?
        if ($orderby)
        {
            $sql .= ' ORDER BY '.$orderby;
        }

        // echo $sql;die();

        return $sql;
    }

    /**
     * Build the FROM clause for the inpue list of aliases and the linked tables to include.
     */
    public function getTableClause($aliases, $includes, $forSelectClause, $id_lang)
    {
        // build the field list from the aliases list
        $sqlFields = ExportOrderFields::getSqlFields($aliases, $forSelectClause, $id_lang);

        if ($forSelectClause)
        {
            $sqlFields .= ', o.`id_order` AS temp_id_order';
        }
        if(array_key_exists('includeOrderDetail', $includes) && !empty($includes["includeOrderDetail"]))
        {
            $sqlFields .= ', od.product_id as temp_product_id';
            $sqlFields .= ', od.product_attribute_id as temp_product_attribute_id';
        }

        //                'includeProducts'=>$includeProducts,
        //                'includeProductCategories'=>$includeProductCategories

        //                        INNER JOIN `' . _DB_PREFIX_ . 'group` cg ON c.`id_default_group`=cg.`id_group`
        //                        LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` cgl ON cg.`id_group`=cgl.`id_group` AND cgl.id_lang='.(int) $id_lang.'

        $sql = 'SELECT '.$sqlFields.' FROM `'._DB_PREFIX_.'orders` o '.
            (array_key_exists('includeCustomers', $includes) && !empty($includes['includeCustomers']) ? '
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON o.`id_customer`=c.`id_customer`
            ' : '').
            (array_key_exists('includeAddressesDelivery', $includes) && !empty($includes['includeAddressesDelivery']) ? '
            LEFT JOIN `'._DB_PREFIX_.'address` ad ON o.`id_address_delivery`=ad.`id_address`
            LEFT JOIN `'._DB_PREFIX_.'country` cnd ON ad.`id_country`=cnd.`id_country`
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cld ON ad.`id_country`=cld.`id_country` AND cld.id_lang='.(int) $id_lang.'
            LEFT JOIN `'._DB_PREFIX_.'state` std ON ad.`id_state`=std.`id_state`
            ' : '').
            (array_key_exists('includeAddressesInvoice', $includes) && !empty($includes['includeAddressesInvoice']) ? '
            LEFT JOIN `'._DB_PREFIX_.'address` ai ON o.`id_address_invoice`=ai.`id_address`
            LEFT JOIN `'._DB_PREFIX_.'country` cni ON ai.`id_country`=cni.`id_country`
            LEFT JOIN `'._DB_PREFIX_.'country_lang` cli ON ai.`id_country`=cli.`id_country` AND cli.id_lang='.(int) $id_lang.'
            LEFT JOIN `'._DB_PREFIX_.'state` sti ON ai.`id_state`=sti.`id_state`
            ' : '').
            (array_key_exists('includeCustomerGroups', $includes) && !empty($includes['includeCustomerGroups']) ?
                (version_compare(_PS_VERSION_, '1.3.0.4', '>=') ? '
            LEFT JOIN `'._DB_PREFIX_.'group` cg ON cg.`id_group`=c.`id_default_group`
            LEFT JOIN `'._DB_PREFIX_.'group_lang` cgl ON cgl.`id_group`=cg.`id_group` AND cgl.id_lang='.(int) $id_lang.'
            ' : '
            LEFT JOIN `'._DB_PREFIX_.'customer_group` cgr ON c.`id_customer`=cgr.`id_customer`
            LEFT JOIN `'._DB_PREFIX_.'group` cg ON cgr.`id_group`=cg.`id_group`
            LEFT JOIN `'._DB_PREFIX_.'group_lang` cgl ON cgl.`id_group`=cg.`id_group` AND cgl.id_lang='.(int) $id_lang) : '').
            (array_key_exists('includeProducts', $includes) && !empty($includes['includeProducts']) ? '
            LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.`id_order`=od.`id_order`
            ' : '').
            (array_key_exists('includeProductCategories', $includes) && !empty($includes['includeProductCategories']) ? '
            LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON od.`product_id`=cp.`id_product`
            ' : '').
            (array_key_exists('includeCarrier', $includes) && !empty($includes['includeCarrier']) ? '
            LEFT JOIN `'._DB_PREFIX_.'carrier` ca ON o.`id_carrier`=ca.`id_carrier`
            ' : '').
            (array_key_exists('includeOrderDetail', $includes) && !empty($includes['includeOrderDetail']) ? '
                        LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.`id_order`=od.`id_order`
                        ' : '').
            (array_key_exists('includeOrderInvoice', $includes) && !empty($includes['includeOrderInvoice']) ? '
            LEFT JOIN `'._DB_PREFIX_.'order_invoice` odin ON o.`id_order`=odin.`id_order`
                        ' : '').
            (array_key_exists('includeOrderProduct', $includes) && !empty($includes['includeOrderProduct']) ? '
                        LEFT JOIN `'._DB_PREFIX_.'product` odp ON od.`product_id`=odp.`id_product`
            '.(ExportOrderTools::isNewerPs15x() && !empty($this->id_shop) ? ' LEFT JOIN `'._DB_PREFIX_.'product_shop` odp_shop ON ( odp_shop.`id_product`=odp.`id_product` AND odp_shop.`id_shop`="'.(int) $this->id_shop.'")' : '').'
                        LEFT JOIN `'._DB_PREFIX_.'product_attribute` odpa ON od.`product_attribute_id`=odpa.`id_product_attribute`
                                './* (ExportOrderTools::isPs15x() && !empty($this->id_shop)?' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` odpa_shop ON ( odpa_shop.`id_product_attribute`=odpa.`id_product_attribute` AND odp_shop.`id_shop`="'.(int)($this->id_shop).'")':''). */ '
                        ' : '').
            (array_key_exists('includeOrderProductCategory', $includes) && !empty($includes['includeOrderProductCategory']) ? '
                        LEFT JOIN `'._DB_PREFIX_.'category_lang` odpcl ON odp.`id_category_default`=odpcl.`id_category` AND odpcl.id_lang='.(int) $id_lang.' '.(ExportOrderTools::isNewerPs15x() ? (!empty($this->id_shop) ? 'AND odpcl.`id_shop`="'.(int) $this->id_shop.'"' : 'AND odpcl.`id_shop` IN ('.pSQL(ExportOrderTools::getAllShopsId()).')') : '').'
                        ' : '').
            (array_key_exists('includeOrderProductSupplier', $includes) && !empty($includes['includeOrderProductSupplier']) ? '
                        LEFT JOIN `'._DB_PREFIX_.'supplier` odps ON odp.`id_supplier`=odps.`id_supplier`
                        ' : '').
            (array_key_exists('includeOrderProductManufacturer', $includes) && !empty($includes['includeOrderProductManufacturer']) ? '
                        LEFT JOIN `'._DB_PREFIX_.'manufacturer` odpm ON odp.`id_manufacturer`=odpm.`id_manufacturer`
                        ' : '').
            (array_key_exists('includeCurrency', $includes) && !empty($includes['includeCurrency']) ? '
                        LEFT JOIN `'._DB_PREFIX_.'currency` cu ON o.`id_currency`=cu.`id_currency`
                                               '.(version_compare(_PS_VERSION_, '1.7.6', '>')? ' LEFT JOIN `'._DB_PREFIX_.'currency_lang` cu_lang ON ( cu.`id_currency`=cu_lang.`id_currency` AND cu_lang.`id_lang`="'.(int) $id_lang.'")
                        ' : ''):'').
            (array_key_exists('includeLastOrderHistory', $includes) && !empty($includes['includeLastOrderHistory']) ? '
                        LEFT JOIN '._DB_PREFIX_.'order_history oh1 ON (oh1.id_order=o.id_order AND oh1.id_order_history=(SELECT MAX(oh11.id_order_history) FROM '._DB_PREFIX_.'order_history oh11 WHERE oh11.id_order=oh1.id_order ))
                        ' : '')/*.
            (!array_key_exists('includeOrderSlip', $includes)?'':$includes['includeOrderSlip']?'
            LEFT JOIN `' . _DB_PREFIX_ . 'order_slip` os ON (o.`id_order`=os.`id_order`)
                LEFT JOIN `' . _DB_PREFIX_ . 'order_slip_detail` osd ON (osd.`id_order_slip`=os.`id_order_slip`)
                    LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` osd_pd ON (osd.`id_order_detail`=osd_pd.`id_order_detail`)
            ':'')*/
        ;

        return $sql;
    }

    /**
     * Get an array of rows as datas retreive from this list.
     */
    public function getList($aliases, $id_lang, $orderby = null)
    {
        // get from the whole SQL query
        $sql = $this->getSqlQuery($aliases, $id_lang, $orderby);
        ExportOrderTools::addLog('CList run :'.$this->id.' - get SQL : '.$sql);

        // if some valid SQL to execute
        if (Tools::strlen($sql) > 0)
        {
            // get datas
            $datas = Db::getInstance()->executeS($sql);
            ExportOrderTools::addLog('CList run :'.$this->id.' - get RAW DATAS : '.ExportOrderTools::captureVarDump($datas));
            if(!$datas)
            {
                ExportOrderTools::addLog('CList run :'.$this->id.' - get Error : '.Db::getInstance()->getMsgError());
            }
        }
        else
        {
            $datas = null;
        }

        return $datas;
    }

    /**
     * Get an array of rows as orders descriptors to be displayed as a list preview.
     */
    public function getDisplayOrderFilter($id_lang)
    {
        // the aliases are the columns to be displayed to user
        //                return $this->getList(array('Order_Id', 'Carrier', 'Order_Shipping_Number', 'Gender', 'Email', 'Address_Delivery_Country', 'Order_Module', 'Order_Total_Paid_Real', 'Currency_Iso_Code', 'Order_Date_Add'), $id_lang);
        //                return $this->getList(array('Order_Id', 'Email', 'Address_Delivery_Country', 'Order_Module', 'Order_Total_Products_ET', 'Order_Total_Paid_Real', 'Currency_Iso_Code', 'Order_Date_Add', 'Order_Gift'), $id_lang);
        $return = null;

        if (ExportOrderTools::isSCMS())
        {
            $return = $this->getList(array('Order_Id', 'Shop_Id', 'Order_Display_Customer', 'Order_Display_Total', 'Order_Module', 'Address_Delivery_Country', 'Carrier', 'Order_Date_Add', 'Order_Invoice_Number'), $id_lang, 'o.`date_add` DESC');
        }
        elseif (ExportOrderTools::isNewerPs15x())
        {
            $return = $this->getList(array('Order_Id', 'Order_Display_Customer', 'Order_Display_Total', 'Order_Module', 'Address_Delivery_Country', 'Carrier', 'Order_Date_Add', 'Order_Invoice_Number'), $id_lang, 'o.`date_add` DESC');
        }
        else
        {
            $return = $this->getList(array('Order_Id', 'Order_Display_Customer', 'Order_Display_Total', 'Order_Module', 'Address_Delivery_Country', 'Carrier', 'Order_Date_Add'), $id_lang, 'o.`date_add` DESC');
        }

        return $return;
    }

    /**
     * Export this list.
     */
    public function export($id_export_template, $id_lang, $expectedNumberOfOrders)
    {
        ExportOrderTools::addLog('**************** CList export : get order list for export ****************');

        $excluded_fields = array(
            'temp_id_order',
            'temp_product_id',
            'temp_product_attribute_id',
            'temp_id_warehouse',
            'tb_total_shipping_tax_excl',
            'tb_total_shipping_tax_amount',
            'tb_carrier_tax_rate',
            'tb_total_discounts_tax_incl',
            'tb_total_discounts_tax_excl',
            'tb_total_discounts_tax_amount',
            'tb_total_discounts_tax_rate',
            'tbc_total_shipping_tax_excl',
            'tbc_total_shipping_tax_amount',
            'tbc_carrier_tax_rate',
            'tbc_total_discounts_tax_incl',
            'tbc_total_discounts_tax_excl',
            'tbc_total_discounts_tax_amount',
            'tbc_total_discounts_tax_rate',
            'tbc_country_name',
            'ttbc_country_name',
            'fr_tax_name',
            'fr_total_shipping_tax_excl',
            'fr_total_shipping_tax_amount',
            'fr_carrier_tax_rate',
            'fr_total_discounts_tax_incl',
            'fr_total_discounts_tax_excl',
            'fr_total_discounts_tax_amount',
            'fr_total_discounts_tax_rate',
            'fr_country_name',
            'fr_country_iso',
        );

        // load the export template
        $exportTemplate = new ExportOrderMapping($id_export_template);
        if (!Validate::isLoadedObject($exportTemplate))
        {
            return 'Unable to load the export template of id '.(int) $id_export_template;
        }
        ExportOrderTools::addLog('CList export : load template '.(int) $id_export_template.' ('.$exportTemplate->name.') to export to '.$exportTemplate->export_format.' format');

        $this->_fillTaxRates();

        // Lang
        $idLangFr = LanguageCore::getIdByIso('fr');
        if ($id_lang == $idLangFr)
        {
            $lang_QC = 2;
        }
        else
        {
            $lang_QC = 1;
        }

        // get datas (column names are sql alias as defined in ExportOrderMapping)
        $datas = $this->getList($exportTemplate->getAliases(), $id_lang);
        // some datas ?
        if ($datas && !empty($datas))
        {
            // compare to the expected number of orders (size of the list displayed to user before he asked for export)
            if (count($datas) != $expectedNumberOfOrders)
            {
                // TODO : log more informations to be able to understand why this difference occurs
                ExportOrderTools::addLog('WARNING - number of orders listed ('.$expectedNumberOfOrders.') don\'t match the number of orders exported ('.count($datas).')');
            }

            // load export format properties
            $props = ExportOrderTools::explodeKeyValue($exportTemplate->format_properties, '@@', '::');
            if (!isset($props['display_breakdown_shipping']))
            {
                $props['display_breakdown_shipping'] = 1;
            }
            if (!isset($props['display_breakdown_discounts']))
            {
                $props['display_breakdown_discounts'] = 1;
            }
            // change some props
            if (array_key_exists('delimitor', $props) && $props['delimitor'] == 'tab')
            {
                $props['delimitor'] = "\t";        // use "\t" and not '\t' or tabulation will be reduced to characters \t
                ExportOrderTools::addLog('CList export : replace TAB delimitor');
            }

            // CSV format and something to export
            if ($exportTemplate->export_format == 'CSV' && !empty($datas))
            {
                // build output buffer
                $buffer = '';

                // echo "<pre>";print_r($datas);echo "</pre>";die();

                // For calculated fields
                // Prepare datas arrays
                $print_totals = true;
                $totals = array();
                // Breakdown & Slip
                $_has_tb = false;
                $_has_pb = false;
                $_has_pbttc = false;
                $_has_icb = false;
                $_has_dcb = false;
                $_has_slip = false;
                $_has_ttb = false;
                $_has_tbc = false;
                $_has_ttbc = false;
                $_has_fr = false;

                if (
                    (isset($datas[0]['Calculated_Taxes_breakdown']) && isset($datas[0]['tb_tax_name']))
                    ||
                    (isset($datas[0]['Calculated_Payment_breakdown']) && isset($datas[0]['pb_payment']))
                    ||
                    (isset($datas[0]['Calculated_Payment_TTC_breakdown']) && isset($datas[0]['pbttc_payment']))
                    ||
                    (isset($datas[0]['Calculated_Invoice_country_breakdown']) && isset($datas[0]['icb_country']))
                    ||
                    (isset($datas[0]['Calculated_Delivery_country_breakdown']) && isset($datas[0]['dcb_country']))
                    ||
                    isset($datas[0]['Calculated_Slip'])
                    ||
                    isset($datas[0]['Total_Excl_Taxes_By_Taxes_breakdown'])
                    ||
                    isset($datas[0]['Calculated_Taxes_breakdown_by_Country'])
                    ||
                    isset($datas[0]['Total_Excl_Taxes_By_Taxes_breakdown_by_Country'])
                    ||
                    isset($datas[0]['Calculated_Taxes_breakdown_for_FR'])
                ) {
                    $new_datas = array();
                    $print_totals = true;

                    if (isset($datas[0]['Calculated_Taxes_breakdown']) && isset($datas[0]['tb_tax_name']))
                    {
                        $_has_tb = true;
                    }
                    if (isset($datas[0]['Total_Excl_Taxes_By_Taxes_breakdown']))
                    {
                        $_has_ttb = true;
                    }
                    if (isset($datas[0]['Total_Excl_Taxes_By_Taxes_breakdown_by_Country']))
                    {
                        $_has_ttbc = true;
                    }
                    if (isset($datas[0]['Calculated_Payment_breakdown']) && isset($datas[0]['pb_payment']))
                    {
                        $_has_pb = true;
                    }
                    if (isset($datas[0]['Calculated_Payment_TTC_breakdown']) && isset($datas[0]['pbttc_payment']))
                    {
                        $_has_pbttc = true;
                        $suffix_pbttc = ' '.(($lang_QC == 2) ? 'TTC' : 'IT');
                    }
                    if (isset($datas[0]['Calculated_Invoice_country_breakdown']) && isset($datas[0]['icb_country']))
                    {
                        $_has_icb = true;
                        $suffix_icb = ' - '.(($lang_QC == 2) ? 'facturation' : 'invoice');
                    }
                    if (isset($datas[0]['Calculated_Delivery_country_breakdown']) && isset($datas[0]['dcb_country']))
                    {
                        $_has_dcb = true;
                        $suffix_dcb = ' - '.(($lang_QC == 2) ? 'livraison' : 'delivery');
                    }
                    if (isset($datas[0]['Calculated_Slip']))
                    {
                        $_has_slip = true;
                    }
                    if (isset($datas[0]['Calculated_Taxes_breakdown_by_Country']))
                    {
                        $_has_tbc = true;
                    }
                    if (isset($datas[0]['Calculated_Taxes_breakdown_for_FR']))
                    {
                        $_has_fr = true;
                    }

                    if ($_has_ttb)
                    {
                        $t_taxes = array();
                        $ttb_prefix = (($lang_QC == 2) ? 'Ventes HT - TVA ' : 'Sales prices excl. tax - Tax ');
                        $ttb_amount_prefix = (($lang_QC == 2) ? 'Montant TVA - TVA ' : 'Amount tax - Tax ');
                        $sql_all_used_taxes = 'SELECT DISTINCT(tax_rate) as tax_name FROM '._DB_PREFIX_.'order_detail';
                        $all_used_taxes = Db::getInstance()->executeS($sql_all_used_taxes);
                        foreach ($all_used_taxes as $all_used_taxe)
                        {
                            $all_used_taxe['tax_name'] = number_format($all_used_taxe['tax_name'], 1, '.', '');

                            // Prix HT
                            $t_taxes[$ttb_prefix.$all_used_taxe['tax_name']] = $ttb_prefix.$all_used_taxe['tax_name'];
                            $totals[$ttb_prefix.$all_used_taxe['tax_name']] = 0;

                            // TVA montant
                            $t_taxes[$ttb_amount_prefix.$all_used_taxe['tax_name']] = $ttb_amount_prefix.$all_used_taxe['tax_name'];
                            $totals[$ttb_amount_prefix.$all_used_taxe['tax_name']] = 0;
                        }

                        if (ExportOrderTools::isNewerPs12x())
                        {
                            $sql_all_used_taxes = 'SELECT DISTINCT(carrier_tax_rate) as carrier_tax_rate FROM '._DB_PREFIX_.'orders';
                            $all_used_taxes = Db::getInstance()->executeS($sql_all_used_taxes);
                            foreach ($all_used_taxes as $all_used_taxe)
                            {
                                $all_used_taxe['carrier_tax_rate'] = number_format($all_used_taxe['carrier_tax_rate'], 1, '.', '');
                                $name = $ttb_prefix.$all_used_taxe['carrier_tax_rate'];
                                if (empty($t_taxes[$name]))
                                {
                                    $t_taxes[$name] = $name;
                                    $totals[$name] = 0;
                                }
                                $name = $ttb_amount_prefix.$all_used_taxe['carrier_tax_rate'];
                                if (empty($t_taxes[$name]))
                                {
                                    $t_taxes[$name] = $name;
                                    $totals[$name] = 0;
                                }
                            }
                        }

                        foreach ($datas as $num => $data)
                        {
                            if (ExportOrderTools::isNewerPs15x())
                            {
                                $sql_ht_prices = 'SELECT  o.`total_discounts` AS total_discounts_tax_incl, o.`total_discounts_tax_excl` AS total_discounts_tax_excl, (o.`total_discounts`-o.`total_discounts_tax_excl`) AS total_discounts_tax_amount, (((o.`total_discounts_tax_incl`/o.`total_discounts_tax_excl`)-1)*100) AS total_discounts_tax_rate
                                                                                                                                FROM '._DB_PREFIX_.'orders o
                                                                                                                                WHERE o.id_order = "'.(int) $data['temp_id_order'].'"';
                            }
                            elseif (ExportOrderTools::isNewerPs13x())
                            {
                                $sql_ht_prices = 'SELECT o.`total_discounts` AS total_discounts_tax_incl, (o.`total_discounts`/(1+(o.`carrier_tax_rate`/100))) AS total_discounts_tax_excl, (o.`total_discounts`-(o.`total_discounts`/(1+(o.`carrier_tax_rate`/100)))) AS total_discounts_tax_amount, o.`carrier_tax_rate` AS total_discounts_tax_rate
                                                                                                                                FROM '._DB_PREFIX_.'orders o
                                                                                                                                WHERE o.id_order = "'.(int) $data['temp_id_order'].'"';
                            }
                            if (!empty($sql_ht_prices))
                            {
                                $all_ht_prices = Db::getInstance()->executeS($sql_ht_prices);
                            }
                            if (!empty($all_ht_prices[0]))
                            {
                                $all_ht_prices = $all_ht_prices[0];

                                if (($all_ht_prices['total_discounts_tax_incl'] > 0 || $all_ht_prices['total_discounts_tax_incl'] < 0) && $all_ht_prices['total_discounts_tax_rate'] <= 0)
                                {
                                    $all_ht_prices['total_discounts_tax_rate'] = number_format($all_ht_prices['tax_name'], 1, '.', '');
                                    $all_ht_prices['total_discounts_tax_excl'] = number_format($all_ht_prices['total_discounts_tax_excl'] / (1 + ($all_ht_prices['total_discounts_tax_rate'] / 100)), 2, '.', '');
                                    $amout_reduc_discounts_tax = number_format(number_format($all_ht_prices['total_discounts_tax_incl'], 2, '.', '') - $all_ht_prices['total_discounts_tax_excl'], 2, '.', '');
                                    $all_ht_prices['total_discounts_tax_excl'] = number_format($all_ht_prices['total_discounts_tax_excl'], 1, '.', '');
                                    $all_ht_prices['total_discounts_tax_amount'] = number_format($amout_reduc_discounts_tax, 1, '.', '');
                                }
                                if (!empty($all_ht_prices['total_discounts_tax_amount']))
                                {
                                    $all_ht_prices['total_discounts_tax_rate'] = number_format((float) $all_ht_prices['total_discounts_tax_rate'], 1, '.', '');
                                    $all_ht_prices['total_discounts_tax_rate'] = $this->_goodTaxRate($all_ht_prices['total_discounts_tax_rate']);

                                    $name = $ttb_prefix.$all_ht_prices['total_discounts_tax_rate'];
                                    if (empty($t_taxes[$name]))
                                    {
                                        $t_taxes[$name] = $name;
                                        $totals[$name] = 0;
                                    }
                                    $name = $ttb_amount_prefix.$all_ht_prices['total_discounts_tax_rate'];
                                    if (empty($t_taxes[$name]))
                                    {
                                        $t_taxes[$name] = $name;
                                        $totals[$name] = 0;
                                    }
                                }
                            }
                        }
                    }
                    if ($_has_ttbc)
                    {
                        $tc_taxes = array();
                        $ttbc_prefix = (($lang_QC == 2) ? 'Ventes HT - TVA ' : 'Sales prices excl. tax - Tax ');
                        $ttbc_amount_prefix = (($lang_QC == 2) ? 'Montant TVA - TVA ' : 'Amount tax - Tax ');
                        $sql_all_used_taxes = 'SELECT od.tax_rate AS tax_name, cl.name AS country_name
                                                                        FROM '._DB_PREFIX_.'order_detail od
                                                                                INNER JOIN '._DB_PREFIX_.'orders o ON (o.id_order=od.id_order)
                                                                                        INNER JOIN '._DB_PREFIX_.'address a ON (a.id_address=o.id_address_delivery)
                                                                                                INNER JOIN '._DB_PREFIX_.'country_lang cl ON (a.id_country=cl.id_country AND cl.id_lang = "'.(int) $id_lang.'")
                                                                        GROUP BY od.tax_rate, cl.name';
                        $all_used_taxes = Db::getInstance()->executeS($sql_all_used_taxes);
                        $ttbc_suffix = ' - ';
                        foreach ($all_used_taxes as $all_used_taxe)
                        {
                            $temp_suffix = $ttbc_suffix.$all_used_taxe['country_name'];

                            $all_used_taxe['tax_name'] = number_format($all_used_taxe['tax_name'], 1, '.', '');

                            // Prix HT
                            $tc_taxes[$ttbc_prefix.$all_used_taxe['tax_name'].$temp_suffix] = $ttbc_prefix.$all_used_taxe['tax_name'].$temp_suffix;
                            $totals[$ttbc_prefix.$all_used_taxe['tax_name'].$temp_suffix] = 0;

                            // TVA montant
                            $tc_taxes[$ttbc_amount_prefix.$all_used_taxe['tax_name'].$temp_suffix] = $ttbc_amount_prefix.$all_used_taxe['tax_name'].$temp_suffix;
                            $totals[$ttbc_amount_prefix.$all_used_taxe['tax_name'].$temp_suffix] = 0;
                        }

                        if (ExportOrderTools::isNewerPs12x())
                        {
                            $sql_all_used_taxes = 'SELECT o.carrier_tax_rate, cl.name AS country_name
                                                                                FROM '._DB_PREFIX_.'orders o
                                                                                        INNER JOIN '._DB_PREFIX_.'address a ON (a.id_address=o.id_address_delivery)
                                                                                                INNER JOIN '._DB_PREFIX_.'country_lang cl ON (a.id_country=cl.id_country AND cl.id_lang = "'.(int) $id_lang.'")
                                                                                GROUP BY o.carrier_tax_rate, cl.name';
                            $all_used_taxes = Db::getInstance()->executeS($sql_all_used_taxes);
                            foreach ($all_used_taxes as $all_used_taxe)
                            {
                                $temp_suffix = $ttbc_suffix.$all_used_taxe['country_name'];

                                $all_used_taxe['carrier_tax_rate'] = number_format($all_used_taxe['carrier_tax_rate'], 1, '.', '');
                                $name = $ttbc_prefix.$all_used_taxe['carrier_tax_rate'].$temp_suffix;
                                if (empty($tc_taxes[$name]))
                                {
                                    $tc_taxes[$name] = $name;
                                    $totals[$name] = 0;
                                }
                                $name = $ttbc_amount_prefix.$all_used_taxe['carrier_tax_rate'].$temp_suffix;
                                if (empty($tc_taxes[$name]))
                                {
                                    $tc_taxes[$name] = $name;
                                    $totals[$name] = 0;
                                }
                            }
                        }
                    }
                    // echo "test1<pre>";print_r($datas);echo "</pre>";die();
                    if ($_has_tb)
                    {
                        $taxes = array();
                        $tb_prefix = (($lang_QC == 2) ? 'Prix HT - TVA ' : 'Prices excl. tax - Tax ');
                        $tb_amount_prefix = (($lang_QC == 2) ? 'Montant TVA - TVA ' : 'Amount tax - Tax ');

                        foreach ($datas as $num => $data)
                        {
                            if (!empty($data['tb_tax_name']))
                            {
                                if (!is_numeric($data['tb_tax_name']))
                                {
                                    $data['tb_tax_name'] = ExportOrderTools::parseFloat($data['tb_tax_name']);
                                }
                                $data['tb_tax_name'] = number_format($data['tb_tax_name'], 1, '.', '');
                            }

                            // Prix HT
                            $taxes[$tb_prefix.$data['tb_tax_name']] = $tb_prefix.$data['tb_tax_name'];
                            $totals[$tb_prefix.$data['tb_tax_name']] = 0;

                            // TVA montant
                            $tbc_tax_amount = $data['Calculated_Taxes_Amount_breakdown'];
                            if (!empty($tbc_tax_amount) && ($tbc_tax_amount > 0 || $tbc_tax_amount < 0))
                            {
                                $taxes[$tb_amount_prefix.$data['tb_tax_name']] = $tb_amount_prefix.$data['tb_tax_name'];
                                $totals[$tb_amount_prefix.$data['tb_tax_name']] = 0;
                            }
                            if (ExportOrderTools::isNewerPs12x() && $props['display_breakdown_shipping'] == '1')
                            {
                                $data['tb_carrier_tax_rate'] = number_format($data['tb_carrier_tax_rate'], 1, '.', '');
                                $data['tb_carrier_tax_rate'] = $this->_goodTaxRate($data['tb_carrier_tax_rate']);
                                $datas[$num]['tb_carrier_tax_rate'] = $data['tb_carrier_tax_rate'];
                                $tb_shipping_tax_excl = $data['tb_total_shipping_tax_excl'];
                                if ($data['tb_tax_name'] != $data['tb_carrier_tax_rate'] && !empty($tb_shipping_tax_excl) && ($tb_shipping_tax_excl > 0 || $tb_shipping_tax_excl < 0))
                                {
                                    $taxes[$tb_prefix.$data['tb_carrier_tax_rate']] = $tb_prefix.$data['tb_carrier_tax_rate'];

                                    $totals[$tb_prefix.$data['tb_carrier_tax_rate']] = 0;
                                }
                                $tb_shipping_amount = $data['tb_total_shipping_tax_amount'];
                                if ($data['tb_tax_name'] != $data['tb_carrier_tax_rate'] && !empty($tb_shipping_amount) && ($tb_shipping_amount > 0 || $tb_shipping_amount < 0))
                                {
                                    $taxes[$tb_amount_prefix.$data['tb_carrier_tax_rate']] = $tb_amount_prefix.$data['tb_carrier_tax_rate'];

                                    $totals[$tb_amount_prefix.$data['tb_carrier_tax_rate']] = 0;
                                }
                            }
                            if (($data['tb_total_discounts_tax_incl'] > 0 || $data['tb_total_discounts_tax_incl'] < 0) && $data['tb_total_discounts_tax_rate'] <= 0)
                            {
                                $data['tb_total_discounts_tax_rate'] = number_format($data['tb_tax_name'], 1, '.', '');
                                $data['tb_total_discounts_tax_rate'] = $this->_goodTaxRate($data['tb_total_discounts_tax_rate']);
                                $datas[$num]['tb_total_discounts_tax_rate'] = $data['tb_total_discounts_tax_rate'];
                                $data['tb_total_discounts_tax_excl'] = number_format($data['tb_total_discounts_tax_excl'] / (1 + ($data['tb_total_discounts_tax_rate'] / 100)), 2, '.', '');
                                $amout_reduc_discounts_tax = number_format(number_format($data['tb_total_discounts_tax_incl'], 2, '.', '') - $data['tb_total_discounts_tax_excl'], 2, '.', '');
                                $data['tb_total_discounts_tax_excl'] = number_format($data['tb_total_discounts_tax_excl'], 1, '.', '');
                                $data['tb_total_discounts_tax_amount'] = number_format($amout_reduc_discounts_tax, 1, '.', '');

                                $datas[$num]['tb_total_discounts_tax_rate'] = $data['tb_total_discounts_tax_rate'];
                                $datas[$num]['tb_total_discounts_tax_amount'] = $data['tb_total_discounts_tax_amount'];
                                $datas[$num]['tb_total_discounts_tax_excl'] = $data['tb_total_discounts_tax_excl'];
                                $datas[$num]['Calculated_Taxes_Amount_breakdown'] -= $amout_reduc_discounts_tax;
                            }
                            if ($props['display_breakdown_discounts'] == '1')
                            {
                                $data['tb_total_discounts_tax_rate'] = number_format($data['tb_total_discounts_tax_rate'], 1, '.', '');
                                $data['tb_total_discounts_tax_rate'] = ExportOrderTools::applyMarginTaxRate($data['tb_total_discounts_tax_rate']);
                                $datas[$num]['tb_total_discounts_tax_rate'] = $data['tb_total_discounts_tax_rate'];
                                if ($data['tb_tax_name'] != $data['tb_total_discounts_tax_rate'] && !empty($data['tb_total_discounts_tax_excl']) && ($data['tb_total_discounts_tax_excl'] > 0 || $data['tb_total_discounts_tax_excl'] < 0))
                                {
                                    $taxes[$tb_prefix.$data['tb_total_discounts_tax_rate']] = $tb_prefix.$data['tb_total_discounts_tax_rate'];

                                    $totals[$tb_prefix.$data['tb_total_discounts_tax_rate']] = 0;
                                }
                                if ($data['tb_tax_name'] != $data['tb_total_discounts_tax_rate'] && !empty($data['tb_total_discounts_tax_amount']) && ($data['tb_total_discounts_tax_amount'] > 0 || $data['tb_total_discounts_tax_amount'] < 0))
                                {
                                    $taxes[$tb_amount_prefix.$data['tb_total_discounts_tax_rate']] = $tb_amount_prefix.$data['tb_total_discounts_tax_rate'];

                                    $totals[$tb_amount_prefix.$data['tb_total_discounts_tax_rate']] = 0;
                                }
                            }
                        }
                    }
                    if ($_has_tbc)
                    {
                        $taxes_country = array();
                        $tbc_prefix = (($lang_QC == 2) ? 'Prix HT - TVA ' : 'Prices excl. tax - Tax ');
                        $tbc_amount_prefix = (($lang_QC == 2) ? 'Montant TVA - TVA ' : 'Amount tax - Tax ');
                        $tbc_suffix = ' - ';

                        foreach ($datas as $num => $data)
                        {
                            $temp_suffix = $tbc_suffix.$data['tbc_country_name'];

                            if (!empty($data['tbc_tax_name']))
                            {
                                if (!is_numeric($data['tbc_tax_name']))
                                {
                                    $data['tbc_tax_name'] = ExportOrderTools::parseFloat($data['tbc_tax_name']);
                                }
                                $data['tbc_tax_name'] = number_format($data['tbc_tax_name'], 1, '.', '');
                            }

                            // Prix HT
                            $taxes_country[$tbc_prefix.$data['tbc_tax_name'].$temp_suffix] = $tbc_prefix.$data['tbc_tax_name'].$temp_suffix;
                            $totals[$tbc_prefix.$data['tbc_tax_name'].$temp_suffix] = 0;

                            // TVA montant
                            $tbc_tax_amount = $data['Calculated_Taxes_breakdown_by_Country_Amount'];
                            if (!empty($tbc_tax_amount) && ($tbc_tax_amount > 0 || $tbc_tax_amount < 0))
                            {
                                $taxes_country[$tbc_amount_prefix.$data['tbc_tax_name'].$temp_suffix] = $tbc_amount_prefix.$data['tbc_tax_name'].$temp_suffix;
                                $totals[$tbc_amount_prefix.$data['tbc_tax_name'].$temp_suffix] = 0;
                            }

                            if (ExportOrderTools::isNewerPs12x() && $props['display_breakdown_shipping'] == '1')
                            {
                                $data['tbc_carrier_tax_rate'] = number_format($data['tbc_carrier_tax_rate'], 1, '.', '');
                                $data['tbc_carrier_tax_rate'] = $this->_goodTaxRate($data['tbc_carrier_tax_rate']);
                                $datas[$num]['tbc_carrier_tax_rate'] = $data['tbc_carrier_tax_rate'];
                                $tbc_shipping_tax_excl = $data['tbc_total_shipping_tax_excl'];
                                if ($data['tbc_tax_name'] != $data['tbc_carrier_tax_rate'] && !empty($tbc_shipping_tax_excl) && ($tbc_shipping_tax_excl > 0 || $tbc_shipping_tax_excl < 0))
                                {
                                    $taxes[$tbc_prefix.$data['tbc_carrier_tax_rate'].$temp_suffix] = $tbc_prefix.$data['tbc_carrier_tax_rate'].$temp_suffix;

                                    $totals[$tbc_prefix.$data['tbc_carrier_tax_rate'].$temp_suffix] = 0;
                                }
                                $tbc_shipping_amount = $data['tbc_total_shipping_tax_amount'];
                                if ($data['tbc_tax_name'] != $data['tbc_carrier_tax_rate'] && !empty($tbc_shipping_amount) && ($tbc_shipping_amount > 0 || $tbc_shipping_amount < 0))
                                {
                                    $taxes[$tbc_amount_prefix.$data['tbc_carrier_tax_rate'].$temp_suffix] = $tbc_amount_prefix.$data['tbc_carrier_tax_rate'].$temp_suffix;

                                    $totals[$tbc_amount_prefix.$data['tbc_carrier_tax_rate'].$temp_suffix] = 0;
                                }
                            }

                            if (($data['tbc_total_discounts_tax_incl'] > 0 || $data['tbc_total_discounts_tax_incl'] < 0) && $data['tbc_total_discounts_tax_rate'] <= 0)
                            {
                                $data['tbc_total_discounts_tax_rate'] = number_format($data['tbc_tax_name'], 1, '.', '');
                                $data['tbc_total_discounts_tax_rate'] = $this->_goodTaxRate($data['tbc_total_discounts_tax_rate']);
                                $datas[$num]['tbc_total_discounts_tax_rate'] = $data['tbc_total_discounts_tax_rate'];
                                $data['tbc_total_discounts_tax_excl'] = number_format($data['tbc_total_discounts_tax_excl'] / (1 + ($data['tbc_total_discounts_tax_rate'] / 100)), 2, '.', '');
                                $amout_reduc_discounts_tax = number_format(number_format($data['tbc_total_discounts_tax_incl'], 2, '.', '') - $data['tbc_total_discounts_tax_excl'], 2, '.', '');
                                $data['tbc_total_discounts_tax_excl'] = number_format($data['tbc_total_discounts_tax_excl'], 1, '.', '');
                                $data['tbc_total_discounts_tax_amount'] = number_format($amout_reduc_discounts_tax, 1, '.', '');

                                $datas[$num]['tbc_total_discounts_tax_rate'] = $data['tbc_total_discounts_tax_rate'];
                                $datas[$num]['tbc_total_discounts_tax_amount'] = $data['tbc_total_discounts_tax_amount'];
                                $datas[$num]['tbc_total_discounts_tax_excl'] = $data['tbc_total_discounts_tax_excl'];
                                $datas[$num]['Calculated_Taxes_breakdown_by_Country_Amount'] -= $amout_reduc_discounts_tax;
                            }
                            if ($props['display_breakdown_discounts'] == '1')
                            {
                                $data['tbc_total_discounts_tax_rate'] = number_format($data['tbc_total_discounts_tax_rate'], 1, '.', '');
                                $data['tbc_total_discounts_tax_rate'] = $this->_goodTaxRate($data['tbc_total_discounts_tax_rate']);
                                $datas[$num]['tbc_total_discounts_tax_rate'] = $data['tbc_total_discounts_tax_rate'];
                                if ($data['tbc_tax_name'] != $data['tbc_total_discounts_tax_rate'] && !empty($data['tbc_total_discounts_tax_excl']) && ($data['tbc_total_discounts_tax_excl'] > 0 || $data['tbc_total_discounts_tax_excl'] < 0))
                                {
                                    $taxes_country[$tbc_prefix.$data['tbc_total_discounts_tax_rate'].$temp_suffix] = $tbc_prefix.$data['tbc_total_discounts_tax_rate'].$temp_suffix;

                                    $totals[$tbc_prefix.$data['tbc_total_discounts_tax_rate'].$temp_suffix] = 0;
                                }
                                if ($data['tbc_tax_name'] != $data['tbc_total_discounts_tax_rate'] && !empty($data['tbc_total_discounts_tax_amount']) && ($data['tbc_total_discounts_tax_amount'] > 0 || $data['tbc_total_discounts_tax_amount'] < 0))
                                {
                                    $taxes_country[$tbc_amount_prefix.$data['tbc_total_discounts_tax_rate'].$temp_suffix] = $tbc_amount_prefix.$data['tbc_total_discounts_tax_rate'].$temp_suffix;

                                    $totals[$tbc_amount_prefix.$data['tbc_total_discounts_tax_rate'].$temp_suffix] = 0;
                                }
                            }
                        }
                    }
                    if ($_has_fr)
                    {
                        $taxes_country = array();
                        $fr_prefix = (($lang_QC == 2) ? 'Prix HT - TVA ' : 'Prices excl. tax - Tax ');
                        $fr_amount_prefix = (($lang_QC == 2) ? 'Montant TVA - TVA ' : 'Amount tax - Tax ');
                        $fr_suffix = ' - ';
                        $countries_in_EU = array(
                            'DE' => '1',
                            'AT' => '1',
                            'BE' => '1',
                            'BG' => '1',
                            'CY' => '1',
                            'HR' => '1',
                            'DK' => '1',
                            'ES' => '1',
                            'EE' => '1',
                            'FI' => '1',
                            'EL' => '1',
                            'HU' => '1',
                            'IE' => '1',
                            'IT' => '1',
                            'LV' => '1',
                            'LT' => '1',
                            'LU' => '1',
                            'MT' => '1',
                            'NL' => '1',
                            'PL' => '1',
                            'PT' => '1',
                            'RO' => '1',
                            'GB' => '1',
                            'SK' => '1',
                            'SI' => '1',
                            'SE' => '1',
                            'CZ' => '1',
                            'GR' => '1',
                        );
                        foreach ($datas as $num => $data)
                        {
                            if (!empty($countries_in_EU[$data['fr_country_iso']]))
                            {
                                $temp_suffix = $fr_suffix.$data['fr_country_name'];
                                if (!empty($data['fr_tax_name']))
                                {
                                    if (!is_numeric($data['fr_tax_name']))
                                    {
                                        $data['fr_tax_name'] = ExportOrderTools::parseFloat($data['fr_tax_name']);
                                    }
                                    $data['fr_tax_name'] = number_format($data['fr_tax_name'], 1, '.', '');
                                }

                                if ($data['fr_tax_name'] > 0 || $data['fr_tax_name'] < 0)
                                {
                                    // Prix HT
                                    $taxes_country[$fr_prefix.$data['fr_tax_name'].$temp_suffix] = $fr_prefix.$data['fr_tax_name'].$temp_suffix;
                                    $totals[$fr_prefix.$data['fr_tax_name'].$temp_suffix] = 0;

                                    // TVA montant
                                    $fr_tax_amount = $data['Calculated_Taxes_breakdown_for_FR_Amount'];
                                    if (!empty($fr_tax_amount) && ($fr_tax_amount > 0 || $fr_tax_amount < 0))
                                    {
                                        $taxes_country[$fr_amount_prefix.$data['fr_tax_name'].$temp_suffix] = $fr_amount_prefix.$data['fr_tax_name'].$temp_suffix;
                                        $totals[$fr_amount_prefix.$data['fr_tax_name'].$temp_suffix] = 0;
                                    }
                                }

                                if (ExportOrderTools::isNewerPs12x() && $props['display_breakdown_shipping'] == '1')
                                {
                                    $data['fr_carrier_tax_rate'] = number_format($data['fr_carrier_tax_rate'], 1, '.', '');
                                    $data['fr_carrier_tax_rate'] = $this->_goodTaxRate($data['fr_carrier_tax_rate']);
                                    $datas[$num]['fr_carrier_tax_rate'] = $data['fr_carrier_tax_rate'];
                                    if ($data['fr_carrier_tax_rate'] > 0 || $data['fr_carrier_tax_rate'] < 0)
                                    {
                                        $fr_shipping_tax_excl = $data['fr_total_shipping_tax_excl'];
                                        if ($data['fr_tax_name'] != $data['fr_carrier_tax_rate'] && !empty($fr_shipping_tax_excl) && ($fr_shipping_tax_excl > 0 || $fr_shipping_tax_excl < 0))
                                        {
                                            $taxes_country[$fr_prefix.$data['fr_carrier_tax_rate'].$temp_suffix] = $fr_prefix.$data['fr_carrier_tax_rate'].$temp_suffix;

                                            $totals[$fr_prefix.$data['fr_carrier_tax_rate'].$temp_suffix] = 0;
                                        }
                                        $fr_shipping_amount = $data['fr_total_shipping_tax_amount'];
                                        if ($data['fr_tax_name'] != $data['fr_carrier_tax_rate'] && !empty($fr_shipping_amount) && ($fr_shipping_amount > 0 || $fr_shipping_amount < 0))
                                        {
                                            $taxes_country[$fr_amount_prefix.$data['fr_carrier_tax_rate'].$temp_suffix] = $fr_amount_prefix.$data['fr_carrier_tax_rate'].$temp_suffix;

                                            $totals[$fr_amount_prefix.$data['fr_carrier_tax_rate'].$temp_suffix] = 0;
                                        }
                                    }
                                }
                                if (($data['fr_total_discounts_tax_incl'] > 0 || $data['fr_total_discounts_tax_incl'] < 0) && $data['fr_total_discounts_tax_rate'] <= 0)
                                {
                                    $data['fr_total_discounts_tax_rate'] = number_format($data['fr_tax_name'], 1, '.', '');
                                    $data['fr_total_discounts_tax_rate'] = $this->_goodTaxRate($data['fr_total_discounts_tax_rate']);
                                    $datas[$num]['fr_total_discounts_tax_rate'] = $data['fr_total_discounts_tax_rate'];
                                    $data['fr_total_discounts_tax_excl'] = number_format($data['fr_total_discounts_tax_excl'] / (1 + ($data['fr_total_discounts_tax_rate'] / 100)), 2, '.', '');
                                    $amout_reduc_discounts_tax = number_format(number_format($data['fr_total_discounts_tax_incl'], 2, '.', '') - $data['fr_total_discounts_tax_excl'], 2, '.', '');
                                    $data['fr_total_discounts_tax_excl'] = number_format($data['fr_total_discounts_tax_excl'], 1, '.', '');
                                    $data['fr_total_discounts_tax_amount'] = number_format($amout_reduc_discounts_tax, 1, '.', '');

                                    $datas[$num]['fr_total_discounts_tax_amount'] = $data['fr_total_discounts_tax_amount'];
                                    $datas[$num]['fr_total_discounts_tax_rate'] = $data['fr_total_discounts_tax_rate'];
                                    $datas[$num]['fr_total_discounts_tax_excl'] = $data['fr_total_discounts_tax_excl'];
                                    $datas[$num]['Calculated_Taxes_breakdown_for_FR_Amount'] -= $amout_reduc_discounts_tax;
                                }
                                if ($props['display_breakdown_discounts'] == '1')
                                {
                                    $data['fr_total_discounts_tax_rate'] = number_format(round($data['fr_total_discounts_tax_rate'], 1), 1, '.', '');
                                    if (($data['fr_total_discounts_tax_excl'] > 0 || $data['fr_total_discounts_tax_excl'] < 0) && $data['fr_tax_name'] != $data['fr_total_discounts_tax_rate'])
                                    {
                                        $taxes_country[$fr_prefix.$data['fr_total_discounts_tax_rate'].$temp_suffix] = $fr_prefix.$data['fr_total_discounts_tax_rate'].$temp_suffix;

                                        $totals[$fr_prefix.$data['fr_total_discounts_tax_rate'].$temp_suffix] = 0;
                                    }
                                    if (($data['fr_total_discounts_tax_amount'] > 0 || $data['fr_total_discounts_tax_amount'] < 0) && $data['fr_tax_name'] != $data['fr_total_discounts_tax_rate'])
                                    {
                                        $taxes_country[$fr_amount_prefix.$data['fr_total_discounts_tax_rate'].$temp_suffix] = $fr_amount_prefix.$data['fr_total_discounts_tax_rate'].$temp_suffix;

                                        $totals[$fr_amount_prefix.$data['fr_total_discounts_tax_rate'].$temp_suffix] = 0;
                                    }
                                }
                            }
                            else
                            {
                                unset($datas[$num]);
                            }
                        }
                        $temp_datas = $datas;
                        $datas = array();
                        foreach ($temp_datas as $temp_data)
                        {
                            $datas[] = $temp_data;
                        }
                    }
                    if ($_has_pb)
                    {
                        $payments = array();
                        foreach ($datas as $num => $data)
                        {
                            $payments[$data['pb_payment']] = $data['pb_payment'];

                            $totals[$data['pb_payment']] = 0;
                        }
                    }
                    if ($_has_pbttc)
                    {
                        $paymentsttc = array();
                        foreach ($datas as $num => $data)
                        {
                            $paymentsttc[$data['pbttc_payment'].$suffix_pbttc] = $data['pbttc_payment'].$suffix_pbttc;

                            $totals[$data['pbttc_payment'].$suffix_pbttc] = 0;
                        }
                    }
                    if ($_has_icb)
                    {
                        $countries_icb = array();
                        foreach ($datas as $num => $data)
                        {
                            $countries_icb[$data['icb_country'].$suffix_icb] = $data['icb_country'].$suffix_icb;

                            $totals[$data['icb_country'].$suffix_icb] = 0;
                        }
                    }
                    if ($_has_dcb)
                    {
                        $countries_dcb = array();
                        foreach ($datas as $num => $data)
                        {
                            $countries_dcb[$data['dcb_country'].$suffix_dcb] = $data['dcb_country'].$suffix_dcb;

                            $totals[$data['dcb_country'].$suffix_dcb] = 0;
                        }
                    }

                    // echo "test1<pre>";print_r($datas);echo "</pre>";die();
                    // Refactor all lines
                    $old_id = 0;
                    foreach ($datas as $num => $data)
                    {
                        if ($_has_tb)
                        {
                            $actual_value_tb = $data['Calculated_Taxes_breakdown'];
                            $actual_value_amount_tb = $data['Calculated_Taxes_Amount_breakdown'];
                            if (!empty($data['tb_tax_name']))
                            {
                                if (!is_numeric($data['tb_tax_name']))
                                {
                                    $data['tb_tax_name'] = ExportOrderTools::parseFloat($data['tb_tax_name']);
                                }
                                $data['tb_tax_name'] = number_format($data['tb_tax_name'], 1, '.', '');
                            }
                            $actual_tax = $data['tb_tax_name'];
                        }
                        if ($_has_tbc)
                        {
                            $actual_value_tbc = $data['Calculated_Taxes_breakdown_by_Country'];
                            $actual_value_amount_tbc = $data['Calculated_Taxes_breakdown_by_Country_Amount'];
                            if (!empty($data['tbc_tax_name']))
                            {
                                if (!is_numeric($data['tbc_tax_name']))
                                {
                                    $data['tbc_tax_name'] = ExportOrderTools::parseFloat($data['tbc_tax_name']);
                                }
                                $data['tbc_tax_name'] = number_format($data['tbc_tax_name'], 1, '.', '');
                            }
                            $actual_tax_tbc = $data['tbc_tax_name'];
                            $actual_tbc_suffix = $tbc_suffix.$data['tbc_country_name'];
                        }
                        if ($_has_fr)
                        {
                            $actual_value_fr = $data['Calculated_Taxes_breakdown_for_FR'];
                            $actual_value_amount_fr = $data['Calculated_Taxes_breakdown_for_FR_Amount'];
                            if (!empty($data['fr_tax_name']))
                            {
                                if (!is_numeric($data['fr_tax_name']))
                                {
                                    $data['fr_tax_name'] = ExportOrderTools::parseFloat($data['fr_tax_name']);
                                }
                                $data['fr_tax_name'] = number_format($data['fr_tax_name'], 1, '.', '');
                            }
                            $actual_tax_fr = $data['fr_tax_name'];
                            $actual_fr_suffix = $fr_suffix.$data['fr_country_name'];
                        }
                        if ($_has_ttbc)
                        {
                            $actual_ttbc_suffix = $ttbc_suffix.$data['ttbc_country_name'];
                        }
                        if ($_has_pb)
                        {
                            $actual_value_pb = $data['Calculated_Payment_breakdown'];
                            $actual_payment = $data['pb_payment'];
                        }
                        if ($_has_pbttc)
                        {
                            $actual_value_pbttc = $data['Calculated_Payment_TTC_breakdown'];
                            $actual_paymentttc = $data['pbttc_payment'];
                        }
                        if ($_has_icb)
                        {
                            $actual_value_icb = $data['Calculated_Invoice_country_breakdown'];
                            $actual_country_icb = $data['icb_country'].$suffix_icb;
                        }
                        if ($_has_dcb)
                        {
                            $actual_value_dcb = $data['Calculated_Delivery_country_breakdown'];
                            $actual_country_dcb = $data['dcb_country'].$suffix_dcb;
                        }
                        if ($_has_slip)
                        {
                            if (!empty($data['id_slips']) && $data['id_slips'] > 0)
                            {
                                $data['id_slips'] = '';
                                $data['Calculated_Slip'] = 0;
                                $data['Calculated_Slip_TTC'] = 0;
                                $data['Calculated_Slip_Shipping'] = 0;
                                $data['Calculated_Slip_Shipping_TTC'] = 0;

                                $sql = new DbQuery();
                                $sql->select('GROUP_CONCAT(DISTINCT(CONCAT(os.`id_order_slip`,"(",DATE_FORMAT(os.`date_add`,"%Y-%m-%d"),")"))) as id_slips')
                                    ->select('SUM(os.`total_shipping_tax_incl`) as Calculated_Slip_Shipping_TTC')
                                    ->select('SUM(os.`total_shipping_tax_excl`) as Calculated_Slip_Shipping')
                                    ->select('os.`shipping_cost`')
                                    ->from('order_slip', 'os')
                                    ->where('os.`id_order` = '.(int) $data['temp_id_order']);
                                $slips = Db::getInstance()->getRow($sql);
                                if (!empty($slips))
                                {
                                    // echo "<pre>";print_r($slips);echo "</pre>";die();
                                    $data['id_slips'] = $slips['id_slips'];
                                    $data['Calculated_Slip_Shipping'] = $slips['Calculated_Slip_Shipping'];
                                    $data['Calculated_Slip_Shipping_TTC'] = $slips['Calculated_Slip_Shipping_TTC'];
                                }

                                $sql2 = new DbQuery();
                                $sql2->select('SUM(osd.`amount_tax_excl`) as Calculated_Slip')
                                    ->select('SUM(osd.`amount_tax_incl`) as Calculated_Slip_TTC')
                                    ->from('order_slip', 'os')
                                    ->leftJoin('order_slip_detail', 'osd', 'osd.`id_order_slip` = os.`id_order_slip`')
                                    ->where('os.`id_order` = '.(int) $data['temp_id_order']);
                                $slips2 = Db::getInstance()->getRow($sql2);
                                if (!empty($slips2))
                                {
                                    $data['Calculated_Slip'] = $slips2['Calculated_Slip'];
                                    $data['Calculated_Slip_TTC'] = $slips2['Calculated_Slip_TTC'];
                                }
                                $sql = $slips = $sql2 = $slips2 = null;
                            }
                            else
                            {
                                $data['id_slips'] = '';
                                $data['Calculated_Slip'] = 0;
                                $data['Calculated_Slip_Shipping'] = 0;
                                $data['Calculated_Slip_Shipping_TTC'] = 0;
                            }
                        }
                        foreach ($data as $key => $value)
                        {
                            $new_datas[$num][$key] = $value;
                            if ($_has_tb)
                            {
                                if ($key == 'tb_tax_name')
                                {
                                    unset($new_datas[$num]['tb_tax_name']);
                                }
                                elseif ($key == 'Calculated_Taxes_breakdown')
                                {
                                    unset($new_datas[$num]['Calculated_Taxes_Amount_breakdown']);
                                    unset($new_datas[$num]['Calculated_Taxes_breakdown']);

                                    foreach ($taxes as $tax)
                                    {
                                        $new_datas[$num][$tax] = '0';
                                    }
                                    if (isset($new_datas[$num][$tb_prefix.$actual_tax]))
                                    {
                                        $new_datas[$num][$tb_prefix.$actual_tax] = $actual_value_tb;
                                        $totals[$tb_prefix.$actual_tax] += $actual_value_tb;
                                    }
                                    if (isset($new_datas[$num][$tb_amount_prefix.$actual_tax]))
                                    {
                                        $new_datas[$num][$tb_amount_prefix.$actual_tax] = $actual_value_amount_tb;
                                        $totals[$tb_amount_prefix.$actual_tax] += $actual_value_amount_tb;
                                    }
                                }
                            }
                            if ($_has_tbc)
                            {
                                if ($key == 'tbc_tax_name')
                                {
                                    unset($new_datas[$num]['tbc_tax_name']);
                                }
                                elseif ($key == 'Calculated_Taxes_breakdown_by_Country')
                                {
                                    unset($new_datas[$num]['Calculated_Taxes_breakdown_by_Country_Amount']);
                                    unset($new_datas[$num]['Calculated_Taxes_breakdown_by_Country']);

                                    foreach ($taxes_country as $tax)
                                    {
                                        $new_datas[$num][$tax] = '0';
                                    }
                                    if (isset($new_datas[$num][$tbc_prefix.$actual_tax_tbc.$actual_tbc_suffix]))
                                    {
                                        $new_datas[$num][$tbc_prefix.$actual_tax_tbc.$actual_tbc_suffix] = $actual_value_tbc;
                                        $totals[$tbc_prefix.$actual_tax_tbc.$actual_tbc_suffix] += $actual_value_tbc;
                                    }
                                    if (isset($new_datas[$num][$tbc_amount_prefix.$actual_tax_tbc.$actual_tbc_suffix]))
                                    {
                                        $new_datas[$num][$tbc_amount_prefix.$actual_tax_tbc.$actual_tbc_suffix] = $actual_value_amount_tbc;
                                        $totals[$tbc_amount_prefix.$actual_tax_tbc.$actual_tbc_suffix] += $actual_value_amount_tbc;
                                    }
                                }
                            }
                            if ($_has_fr)
                            {
                                /*if($key=="fr_tax_name")
                                    unset($new_datas[$num]["fr_tax_name"]);*/
                                if ($key == 'Calculated_Taxes_breakdown_for_FR')
                                {
                                    unset($new_datas[$num]['Calculated_Taxes_breakdown_for_FR_Amount']);
                                    unset($new_datas[$num]['Calculated_Taxes_breakdown_for_FR']);

                                    foreach ($taxes_country as $tax)
                                    {
                                        $new_datas[$num][$tax] = '0';
                                    }
                                    if (isset($new_datas[$num][$fr_prefix.$actual_tax_fr.$actual_fr_suffix]))
                                    {
                                        $new_datas[$num][$fr_prefix.$actual_tax_fr.$actual_fr_suffix] = $actual_value_fr;
                                        $totals[$fr_prefix.$actual_tax_fr.$actual_fr_suffix] += $actual_value_fr;
                                    }
                                    if (isset($new_datas[$num][$fr_amount_prefix.$actual_tax_fr.$actual_fr_suffix]))
                                    {
                                        $new_datas[$num][$fr_amount_prefix.$actual_tax_fr.$actual_fr_suffix] = $actual_value_amount_fr;
                                        $totals[$fr_amount_prefix.$actual_tax_fr.$actual_fr_suffix] += $actual_value_amount_fr;
                                    }
                                }
                            }
                            if ($_has_ttb)
                            {
                                if ($key == 'Total_Excl_Taxes_By_Taxes_breakdown')
                                {
                                    unset($new_datas[$num]['Total_Excl_Taxes_By_Taxes_breakdown']);

                                    foreach ($t_taxes as $tax)
                                    {
                                        $new_datas[$num][$tax] = '0';
                                    }

                                    $sql_ht_prices = 'SELECT od.tax_rate AS tax_name, (od.`total_price_tax_excl`) as ht_price, 
                                                            (od.total_price_tax_incl-od.total_price_tax_excl) as tax_price, 
                                                            o.`total_shipping_tax_excl`, 
                                                            o.`total_shipping_tax_incl`, o.`carrier_tax_rate`,
                                                            o.`total_discounts` AS total_discounts_tax_incl, 
                                                            o.`total_discounts_tax_excl` AS total_discounts_tax_excl, 
                                                            (o.`total_discounts`-o.`total_discounts_tax_excl`) AS total_discounts_tax_amount, 
                                                            (((o.`total_discounts_tax_incl`/o.`total_discounts_tax_excl`)-1)*100) AS total_discounts_tax_rate,
                                                            (SELECT free_shipping 
                                                                FROM '._DB_PREFIX_.'order_cart_rule odcr
                                                                WHERE odcr.id_order = o.id_order
                                                                AND odcr.free_shipping = 1
                                                                LIMIT 1
                                                            ) AS contains_free_shipping
                                                        FROM '._DB_PREFIX_.'order_detail od
                                                        LEFT JOIN '._DB_PREFIX_.'order_detail_tax odt ON odt.id_order_detail = od.id_order_detail
                                                        INNER JOIN '._DB_PREFIX_.'orders o ON (o.id_order = od.id_order)
                                                        WHERE od.id_order = "'.(int) $data['temp_id_order'].'"';

                                    $all_ht_prices = Db::getInstance()->executeS($sql_ht_prices);
                                    $i = 0;
                                    $last = count($all_ht_prices)-1;
                                    foreach ($all_ht_prices as $all_ht_price)
                                    {
                                        if (!empty($all_ht_price['tax_name']))
                                        {
                                            $all_ht_price['tax_name'] = number_format($all_ht_price['tax_name'], 1, '.', '');
                                        }
                                        else
                                        {
                                            $all_ht_price['tax_name'] = '0.0';
                                        }

                                        $new_datas[$num][$ttb_prefix.$all_ht_price['tax_name']] += $all_ht_price['ht_price'];
                                        $totals[$ttb_prefix.$all_ht_price['tax_name']] += $all_ht_price['ht_price'];

                                        $new_datas[$num][$ttb_amount_prefix.$all_ht_price['tax_name']] += $all_ht_price['tax_price'];
                                        $totals[$ttb_amount_prefix.$all_ht_price['tax_name']] += $all_ht_price['tax_price'];

                                        if ($i == $last)
                                        {

                                            ## retirer les montants de transport des montants de réductions si free_shipping
                                            if(!empty($all_ht_price['contains_free_shipping'])) {
                                                $all_ht_price['total_discounts_tax_excl'] -= $all_ht_price['total_shipping_tax_excl'];
                                                $all_ht_price['total_discounts_tax_incl'] -= $all_ht_price['total_shipping_tax_incl'];
                                                $amout_reduc_discounts_tax = number_format(number_format($all_ht_price['total_discounts_tax_incl'], 2, '.', '') - $all_ht_price['total_discounts_tax_excl'], 2, '.', '');
                                                $all_ht_price['total_discounts_tax_amount'] = number_format($amout_reduc_discounts_tax, 1, '.', '');
                                            }

                                            if (($all_ht_price['total_discounts_tax_incl'] > 0 || $all_ht_price['total_discounts_tax_incl'] < 0) && $all_ht_price['total_discounts_tax_rate'] <= 0)
                                            {
                                                $all_ht_price['total_discounts_tax_rate'] = number_format($all_ht_price['tax_name'], 1, '.', '');
                                                $all_ht_price['total_discounts_tax_excl'] = number_format($all_ht_price['total_discounts_tax_excl'] / (1 + ($all_ht_price['total_discounts_tax_rate'] / 100)), 2, '.', '');
                                                $amout_reduc_discounts_tax = number_format(number_format($all_ht_price['total_discounts_tax_incl'], 2, '.', '') - $all_ht_price['total_discounts_tax_excl'], 2, '.', '');
                                                $all_ht_price['total_discounts_tax_excl'] = number_format($all_ht_price['total_discounts_tax_excl'], 1, '.', '');
                                                $all_ht_price['total_discounts_tax_amount'] = number_format($amout_reduc_discounts_tax, 1, '.', '');
                                            }
                                            if (!empty($all_ht_price['total_discounts_tax_amount']))
                                            {
                                                $all_ht_price['total_discounts_tax_rate'] = number_format($all_ht_price['total_discounts_tax_rate'], 1, '.', '');
                                                $all_ht_price['total_discounts_tax_rate'] = ExportOrderTools::applyMarginTaxRate($all_ht_price['total_discounts_tax_rate']);

                                                $new_datas[$num][$ttb_prefix.$all_ht_price['total_discounts_tax_rate']] -= $all_ht_price['total_discounts_tax_excl'];
                                                $new_datas[$num][$ttb_prefix.$all_ht_price['total_discounts_tax_rate']] = max($new_datas[$num][$ttb_prefix.$all_ht_price['total_discounts_tax_rate']], 0);

                                                $totals[$ttb_prefix.$all_ht_price['total_discounts_tax_rate']] -= $all_ht_price['total_discounts_tax_excl'];
                                                $totals[$ttb_prefix.$all_ht_price['total_discounts_tax_rate']] = max($totals[$ttb_prefix.$all_ht_price['total_discounts_tax_rate']], 0);

                                                ## soustraire montant taxe reduction au montant général de tva
                                                $new_datas[$num][$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']] -= $all_ht_price['total_discounts_tax_amount'];
                                                $new_datas[$num][$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']] = max($new_datas[$num][$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']], 0);

                                                $totals[$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']] -= $all_ht_price['total_discounts_tax_amount'];
                                                $totals[$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']] = max($totals[$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']], 0);
                                            }

                                            ## vider les montants de transport si free_shipping
                                            if(!empty($all_ht_price['contains_free_shipping'])) {
                                                $all_ht_price['total_shipping_tax_incl'] = $all_ht_price['total_shipping_tax_excl'] = 0;
                                            }

                                            $all_ht_price['carrier_tax_rate'] = number_format($all_ht_price['carrier_tax_rate'], 1, '.', '');
                                            $all_ht_price['carrier_tax_rate'] = ExportOrderTools::applyMarginTaxRate($all_ht_price['carrier_tax_rate']);

                                            if (empty($new_datas[$num][$ttb_prefix.$all_ht_price['carrier_tax_rate']]))
                                            {
                                                $new_datas[$num][$ttb_prefix.$all_ht_price['carrier_tax_rate']] = 0;
                                            }
                                            $new_datas[$num][$ttb_prefix.$all_ht_price['carrier_tax_rate']] += $all_ht_price['total_shipping_tax_excl'];

                                            if (empty($totals[$ttb_prefix.$all_ht_price['carrier_tax_rate']]))
                                            {
                                                $totals[$ttb_prefix.$all_ht_price['carrier_tax_rate']] = 0;
                                            }
                                            $totals[$ttb_prefix.$all_ht_price['carrier_tax_rate']] += $all_ht_price['total_shipping_tax_excl'];

                                            $amount_shipping_tax = $all_ht_price['total_shipping_tax_incl'] - $all_ht_price['total_shipping_tax_excl'];
                                            $new_datas[$num][$ttb_amount_prefix.$all_ht_price['carrier_tax_rate']] += $amount_shipping_tax;
                                            $totals[$ttb_amount_prefix.$all_ht_price['carrier_tax_rate']] += $amount_shipping_tax;
                                        }

                                        ++$i;
                                    }
                                }
                            }
                            if ($_has_ttbc)
                            {
                                if ($key == 'Total_Excl_Taxes_By_Taxes_breakdown_by_Country')
                                {
                                    unset($new_datas[$num]['Total_Excl_Taxes_By_Taxes_breakdown_by_Country']);

                                    foreach ($tc_taxes as $tax)
                                    {
                                        $new_datas[$num][$tax] = '0';
                                    }

                                    if (ExportOrderTools::isNewerPs15x())
                                    {
                                        $sql_ht_prices = 'SELECT od.tax_name, (od.`total_price_tax_excl`) as ht_price, (od.`total_price_tax_incl`-od.`total_price_tax_excl`) as tax_price, o.`total_shipping_tax_excl`, o.`total_shipping_tax_incl`, o.`carrier_tax_rate`
                                                                                                                                , o.`total_discounts` AS total_discounts_tax_incl, o.`total_discounts_tax_excl` AS total_discounts_tax_excl, (o.`total_discounts`-o.`total_discounts_tax_excl`) AS total_discounts_tax_amount, (((o.`total_discounts_tax_incl`/o.`total_discounts_tax_excl`)-1)*100) AS total_discounts_tax_rate
                                                                                                                        FROM '._DB_PREFIX_.'order_detail od
                                                                                                                                INNER JOIN '._DB_PREFIX_.'orders o ON (o.id_order = od.id_order)
                                                                                                                        WHERE od.id_order = "'.(int) $data['temp_id_order'].'"';
                                    }
                                    elseif (ExportOrderTools::isNewerPs13x())
                                    {
                                        $sql_ht_prices = 'SELECT od.tax_rate AS tax_name, (od.`sc_qc_product_price`*od.`product_quantity`) as ht_price, ((((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`) - (od.`sc_qc_product_price`*od.`product_quantity`)) as tax_price, o.`carrier_tax_rate`, (o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100))) AS `total_shipping_tax_excl`, `total_shipping` AS `total_shipping_tax_incl`
                                                                                                                                , o.`total_discounts` AS total_discounts_tax_incl, (o.`total_discounts`/(1+(o.`carrier_tax_rate`/100))) AS total_discounts_tax_excl, (o.`total_discounts`-(o.`total_discounts`/(1+(o.`carrier_tax_rate`/100)))) AS total_discounts_tax_amount, o.`carrier_tax_rate` AS total_discounts_tax_rate
                                                                                                                        FROM '._DB_PREFIX_.'order_detail  od
                                                                                                                                INNER JOIN '._DB_PREFIX_.'orders o ON (o.id_order = od.id_order)
                                                                                                                        WHERE od.id_order = "'.(int) $data['temp_id_order'].'"';
                                    }
                                    else
                                    {
                                        $sql_ht_prices = 'SELECT od.tax_rate AS tax_name, (od.`product_price`*od.`product_quantity`) as ht_price, ((((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`) - (od.`sc_qc_product_price`*od.`product_quantity`)) as tax_price, o.`carrier_tax_rate`, (o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100))) AS `total_shipping_tax_excl`, `total_shipping` AS `total_shipping_tax_incl`
                                                                                                                        FROM '._DB_PREFIX_.'order_detail od
                                                                                                                                INNER JOIN '._DB_PREFIX_.'orders o ON (o.id_order = od.id_order)
                                                                                                                        WHERE od.id_order = "'.(int) $data['temp_id_order'].'"';
                                    }
                                    $all_ht_prices = Db::getInstance()->executeS($sql_ht_prices);
                                    $i = 0;
                                    foreach ($all_ht_prices as $all_ht_price)
                                    {
                                        if (!empty($all_ht_price['tax_name']))
                                        {
                                            $all_ht_price['tax_name'] = number_format($all_ht_price['tax_name'], 1, '.', '');
                                        }
                                        else
                                        {
                                            $all_ht_price['tax_name'] = '0.0';
                                        }

                                        $new_datas[$num][$ttbc_prefix.$all_ht_price['tax_name'].$actual_ttbc_suffix] += $all_ht_price['ht_price'];
                                        $totals[$ttbc_prefix.$all_ht_price['tax_name'].$actual_ttbc_suffix] += $all_ht_price['ht_price'];

                                        $new_datas[$num][$ttbc_amount_prefix.$all_ht_price['tax_name'].$actual_ttbc_suffix] += $all_ht_price['tax_price'];
                                        $totals[$ttbc_amount_prefix.$all_ht_price['tax_name'].$actual_ttbc_suffix] += $all_ht_price['tax_price'];

                                        if (ExportOrderTools::isNewerPs12x() && $i == 0)
                                        {
                                            $all_ht_price['carrier_tax_rate'] = number_format($all_ht_price['carrier_tax_rate'], 1, '.', '');
                                            $all_ht_price['carrier_tax_rate'] = ExportOrderTools::applyMarginTaxRate($all_ht_price['carrier_tax_rate']);

                                            if (empty($new_datas[$num][$ttbc_prefix.$all_ht_price['carrier_tax_rate'].$actual_ttbc_suffix]))
                                            {
                                                $new_datas[$num][$ttbc_prefix.$all_ht_price['carrier_tax_rate'].$actual_ttbc_suffix] = 0;
                                            }
                                            $new_datas[$num][$ttbc_prefix.$all_ht_price['carrier_tax_rate'].$actual_ttbc_suffix] += $all_ht_price['total_shipping_tax_excl'];

                                            if (empty($totals[$ttbc_prefix.$all_ht_price['carrier_tax_rate'].$actual_ttbc_suffix]))
                                            {
                                                $totals[$ttbc_prefix.$all_ht_price['carrier_tax_rate'].$actual_ttbc_suffix] = 0;
                                            }
                                            $totals[$ttbc_prefix.$all_ht_price['carrier_tax_rate'].$actual_ttbc_suffix] += $all_ht_price['total_shipping_tax_excl'];

                                            $amount_shipping_tax = $all_ht_price['total_shipping_tax_incl'] - $all_ht_price['total_shipping_tax_excl'];
                                            $new_datas[$num][$ttbc_amount_prefix.$all_ht_price['carrier_tax_rate'].$actual_ttbc_suffix] += $amount_shipping_tax;
                                            $totals[$ttbc_amount_prefix.$all_ht_price['carrier_tax_rate'].$actual_ttbc_suffix] += $amount_shipping_tax;
                                        }
                                        if (ExportOrderTools::isNewerPs13x() && $i == 0)
                                        {
                                            if (($all_ht_price['total_discounts_tax_incl'] > 0 || $all_ht_price['total_discounts_tax_incl'] < 0) && $all_ht_price['total_discounts_tax_rate'] <= 0)
                                            {
                                                $all_ht_price['total_discounts_tax_rate'] = number_format($all_ht_price['tax_name'], 1, '.', '');
                                                $all_ht_price['total_discounts_tax_excl'] = number_format($all_ht_price['total_discounts_tax_excl'] / (1 + ($all_ht_price['total_discounts_tax_rate'] / 100)), 2, '.', '');
                                                $amout_reduc_discounts_tax = number_format(number_format($all_ht_price['total_discounts_tax_incl'], 2, '.', '') - $all_ht_price['total_discounts_tax_excl'], 2, '.', '');
                                                $all_ht_price['total_discounts_tax_excl'] = number_format($all_ht_price['total_discounts_tax_excl'], 1, '.', '');
                                                $all_ht_price['total_discounts_tax_amount'] = number_format($amout_reduc_discounts_tax, 1, '.', '');
                                            }
                                            if (!empty($all_ht_price['total_discounts_tax_amount']))
                                            {
                                                $all_ht_price['total_discounts_tax_rate'] = number_format($all_ht_price['total_discounts_tax_rate'], 1, '.', '');
                                                $all_ht_price['total_discounts_tax_rate'] = ExportOrderTools::applyMarginTaxRate($all_ht_price['total_discounts_tax_rate']);

                                                $new_datas[$num][$ttb_prefix.$all_ht_price['total_discounts_tax_rate']] -= $all_ht_price['total_discounts_tax_excl'];
                                                $new_datas[$num][$ttb_prefix.$all_ht_price['total_discounts_tax_rate']] = max($new_datas[$num][$ttb_prefix.$all_ht_price['total_discounts_tax_rate']], 0);

                                                $totals[$ttb_prefix.$all_ht_price['total_discounts_tax_rate']] -= $all_ht_price['total_discounts_tax_excl'];
                                                $totals[$ttb_prefix.$all_ht_price['total_discounts_tax_rate']] = max($totals[$ttb_prefix.$all_ht_price['total_discounts_tax_rate']], 0);

                                                $new_datas[$num][$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']] -= $all_ht_price['total_discounts_tax_amount'];
                                                $new_datas[$num][$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']] = max($new_datas[$num][$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']], 0);

                                                $totals[$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']] -= $all_ht_price['total_discounts_tax_amount'];
                                                $totals[$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']] = max($totals[$ttb_amount_prefix.$all_ht_price['total_discounts_tax_rate']], 0);
                                            }
                                        }
                                        ++$i;
                                    }
                                }
                            }
                            if ($_has_pb)
                            {
                                if ($key == 'pb_payment')
                                {
                                    unset($new_datas[$num]['pb_payment']);
                                }
                                elseif ($key == 'Calculated_Payment_breakdown')
                                {
                                    unset($new_datas[$num]['Calculated_Payment_breakdown']);

                                    foreach ($payments as $payment)
                                    {
                                        $new_datas[$num][$payment] = '0';
                                    }
                                    if (isset($new_datas[$num][$actual_payment]))
                                    {
                                        $new_datas[$num][$actual_payment] = $actual_value_pb;

                                        if ($old_id != $data['temp_id_order'])
                                        {
                                            $totals[$actual_payment] += $actual_value_pb;
                                        }
                                    }
                                }
                            }
                            if ($_has_pbttc)
                            {
                                if ($key == 'pbttc_payment')
                                {
                                    unset($new_datas[$num]['pbttc_payment']);
                                }
                                elseif ($key == 'Calculated_Payment_TTC_breakdown')
                                {
                                    unset($new_datas[$num]['Calculated_Payment_TTC_breakdown']);

                                    foreach ($paymentsttc as $payment)
                                    {
                                        $new_datas[$num][$payment] = '0';
                                    }
                                    if (isset($new_datas[$num][$actual_paymentttc.$suffix_pbttc]))
                                    {
                                        $new_datas[$num][$actual_paymentttc.$suffix_pbttc] = $actual_value_pbttc;

                                        if ($old_id != $data['temp_id_order'])
                                        {
                                            $totals[$actual_paymentttc.$suffix_pbttc] += $actual_value_pbttc;
                                        }
                                    }
                                }
                            }
                            if ($_has_icb)
                            {
                                if ($key == 'icb_country')
                                {
                                    unset($new_datas[$num]['icb_country']);
                                }
                                elseif ($key == 'Calculated_Invoice_country_breakdown')
                                {
                                    unset($new_datas[$num]['Calculated_Invoice_country_breakdown']);

                                    foreach ($countries_icb as $country)
                                    {
                                        $new_datas[$num][$country] = '0';
                                    }
                                    if (isset($new_datas[$num][$actual_country_icb]))
                                    {
                                        $new_datas[$num][$actual_country_icb] = $actual_value_icb;

                                        if ($old_id != $data['temp_id_order'])
                                        {
                                            $totals[$actual_country_icb] += $actual_value_icb;
                                        }
                                    }
                                }
                            }
                            if ($_has_dcb)
                            {
                                if ($key == 'dcb_country')
                                {
                                    unset($new_datas[$num]['dcb_country']);
                                }
                                elseif ($key == 'Calculated_Delivery_country_breakdown')
                                {
                                    unset($new_datas[$num]['Calculated_Delivery_country_breakdown']);

                                    foreach ($countries_dcb as $country)
                                    {
                                        $new_datas[$num][$country] = '0';
                                    }
                                    if (isset($new_datas[$num][$actual_country_dcb]))
                                    {
                                        $new_datas[$num][$actual_country_dcb] = $actual_value_dcb;

                                        if ($old_id != $data['temp_id_order'])
                                        {
                                            $totals[$actual_country_dcb] += $actual_value_dcb;
                                        }
                                    }
                                }
                            }
                        }
                        $old_id = $data['temp_id_order'];
                    }
                    $datas = $new_datas;
                }

                // echo "<pre>";print_r($datas);echo "</pre>";die();

                // header row if any data and template has the options set
                if (count($datas) > 0 && (int) $props['display_header'] == 1)
                {
                    $i = 0;
                    foreach ($datas[0] as $key => $value)
                    {
                        if (!in_array($key, $excluded_fields))
                        {
                            if ($i > 0)
                            {
                                $buffer .= $props['delimitor'];
                            }
                            // look for translation of this key
                            $buffer .= ExportOrderFields::getFieldsTranslation($key, $lang_QC);

                            if ($print_totals)
                            {
                                if (ExportOrderFields::getFieldTotal($key) != '')
                                {
                                    $totals[$key] = 0;
                                }
                            }
                            ++$i;
                        }
                    }
                    $buffer .= "\n";
                    ExportOrderTools::addLog('CList export : display header '.$buffer);
                }
                // export datas
                $old_id = 0;
                $old_data = null;
                foreach ($datas as $data)
                {
                    SC_Ext::readCustomExportOrderConfigXML('rowDataBefore', $data);

                    /*if($data["temp_id_order"]=="207")
                    {
                        echo "<pre>";print_r($data);die();
                    }*/
                    if ($props['display_breakdown_shipping'] == '1' || $props['display_breakdown_discounts'] == '1')
                    {
                        if ($_has_tb && $old_id != $data['temp_id_order'] && $old_id != 0)
                        {
                            if ($props['display_breakdown_shipping'] == '1')
                            {
                                $old_data['tb_carrier_tax_rate'] = number_format($old_data['tb_carrier_tax_rate'], 1, '.', '');
                                $i = 0;
                                foreach ($old_data as $key => $value)
                                {
                                    if (!in_array($key, $excluded_fields))
                                    {
                                        if ($i > 0)
                                        {
                                            $buffer .= $props['delimitor'];
                                        }

                                        if ($i == 0 && $key != $tb_prefix.$old_data['tb_carrier_tax_rate'])
                                        {
                                            $buffer .= (($lang_QC == 2) ? 'Frais de port pour la commande '.$old_data['temp_id_order'] : 'Shipping for order '.$old_data['temp_id_order']);
                                        }

                                        if ($key == $tb_prefix.$old_data['tb_carrier_tax_rate'])
                                        {
                                            if (isset($old_data['tb_total_shipping_tax_excl']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['tb_total_shipping_tax_excl'] = str_replace('.', ',', $old_data['tb_total_shipping_tax_excl']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tb_total_shipping_tax_excl']);

                                            $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tb_total_shipping_tax_excl']) : '';
                                        }
                                        elseif ($key == $tb_amount_prefix.$old_data['tb_carrier_tax_rate'])
                                        {
                                            if (isset($old_data['tb_total_shipping_tax_amount']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['tb_total_shipping_tax_amount'] = str_replace('.', ',', $old_data['tb_total_shipping_tax_amount']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tb_total_shipping_tax_amount']);

                                            $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tb_total_shipping_tax_amount']) : '';
                                        }
                                        else
                                        {
                                            $buffer .= '';
                                        }
                                        ++$i;
                                    }
                                }
                                $buffer .= "\n";
                            }

                            if (!empty($old_data['tb_total_discounts_tax_rate']) && $props['display_breakdown_discounts'] == '1')
                            {
                                $old_data['tb_total_discounts_tax_rate'] = number_format($old_data['tb_total_discounts_tax_rate'], 1, '.', '');
                                $old_data['tb_total_discounts_tax_rate'] = ExportOrderTools::applyMarginTaxRate($old_data['tb_total_discounts_tax_rate']);
                                $i = 0;
                                foreach ($old_data as $key => $value)
                                {
                                    if (!in_array($key, $excluded_fields))
                                    {
                                        if ($i > 0)
                                        {
                                            $buffer .= $props['delimitor'];
                                        }

                                        if ($i == 0 && $key != $tb_prefix.$old_data['tb_total_discounts_tax_rate'])
                                        {
                                            $buffer .= (($lang_QC == 2) ? 'Total des réductions pour la commande '.$old_data['temp_id_order'] : 'Reductions total for order '.$old_data['temp_id_order']);
                                        }

                                        if ($key == $tb_prefix.$old_data['tb_total_discounts_tax_rate'])
                                        {
                                            if (isset($old_data['tb_total_discounts_tax_excl']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['tb_total_discounts_tax_excl'] = str_replace('.', ',', $old_data['tb_total_discounts_tax_excl']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) - str_replace(',', '.', $old_data['tb_total_discounts_tax_excl']);

                                            $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['tb_total_discounts_tax_excl']) : '';
                                        }
                                        elseif ($key == $tb_amount_prefix.$old_data['tb_total_discounts_tax_rate'])
                                        {
                                            if (isset($old_data['tb_total_discounts_tax_amount']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['tb_total_discounts_tax_amount'] = str_replace('.', ',', $old_data['tb_total_discounts_tax_amount']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) - str_replace(',', '.', $old_data['tb_total_discounts_tax_amount']);

                                            $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['tb_total_discounts_tax_amount']) : '';
                                        }
                                        else
                                        {
                                            $buffer .= '';
                                        }
                                        ++$i;
                                    }
                                }
                                $buffer .= "\n";
                            }
                        }
                        if ($_has_tbc && $old_id != $data['temp_id_order'] && $old_id != 0)
                        {
                            if ($props['display_breakdown_shipping'] == '1')
                            {
                                $actual_tbc_suffix = $tbc_suffix.$old_data['tbc_country_name'];
                                $old_data['tbc_carrier_tax_rate'] = number_format($old_data['tbc_carrier_tax_rate'], 1, '.', '');
                                $i = 0;
                                foreach ($old_data as $key => $value)
                                {
                                    if (!in_array($key, $excluded_fields))
                                    {
                                        if ($i > 0)
                                        {
                                            $buffer .= $props['delimitor'];
                                        }

                                        if ($i == 0 && $key != $tbc_prefix.$old_data['tbc_carrier_tax_rate'].$actual_tbc_suffix)
                                        {
                                            $buffer .= (($lang_QC == 2) ? 'Frais de port pour la commande '.$old_data['temp_id_order'] : 'Shipping for order '.$old_data['temp_id_order']);
                                        }

                                        if ($key == $tbc_prefix.$old_data['tbc_carrier_tax_rate'].$actual_tbc_suffix)
                                        {
                                            if (isset($old_data['tbc_total_shipping_tax_excl']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['tbc_total_shipping_tax_excl'] = str_replace('.', ',', $old_data['tbc_total_shipping_tax_excl']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tbc_total_shipping_tax_excl']);

                                            $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tbc_total_shipping_tax_excl']) : '';
                                        }
                                        elseif ($key == $tbc_amount_prefix.$old_data['tbc_carrier_tax_rate'].$actual_tbc_suffix)
                                        {
                                            if (isset($old_data['tbc_total_shipping_tax_amount']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['tbc_total_shipping_tax_amount'] = str_replace('.', ',', $old_data['tbc_total_shipping_tax_amount']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tbc_total_shipping_tax_amount']);

                                            $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tbc_total_shipping_tax_amount']) : '';
                                        }
                                        else
                                        {
                                            $buffer .= '';
                                        }
                                        ++$i;
                                    }
                                }
                                $buffer .= "\n";
                            }

                            if (!empty($old_data['tbc_total_discounts_tax_rate']) && $props['display_breakdown_discounts'] == '1')
                            {
                                $actual_tbc_suffix = $tbc_suffix.$old_data['tbc_country_name'];
                                $old_data['tbc_total_discounts_tax_rate'] = number_format($old_data['tbc_total_discounts_tax_rate'], 1, '.', '');
                                $i = 0;
                                foreach ($old_data as $key => $value)
                                {
                                    if (!in_array($key, $excluded_fields))
                                    {
                                        if ($i > 0)
                                        {
                                            $buffer .= $props['delimitor'];
                                        }

                                        if ($i == 0 && $key != $tbc_prefix.$old_data['tbc_total_discounts_tax_rate'].$actual_tbc_suffix)
                                        {
                                            $buffer .= (($lang_QC == 2) ? 'Total des réductions pour la commande '.$old_data['temp_id_order'] : 'Reductions total for order '.$old_data['temp_id_order']);
                                        }

                                        if ($key == $tbc_prefix.$old_data['tbc_total_discounts_tax_rate'].$actual_tbc_suffix)
                                        {
                                            if (isset($old_data['tbc_total_discounts_tax_excl']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['tbc_total_discounts_tax_excl'] = str_replace('.', ',', $old_data['tbc_total_discounts_tax_excl']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tbc_total_discounts_tax_excl']);

                                            $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['tbc_total_discounts_tax_excl']) : '';
                                        }
                                        elseif ($key == $tbc_amount_prefix.$old_data['tbc_carrier_tax_rate'].$actual_tbc_suffix)
                                        {
                                            if (isset($old_data['tbc_total_discounts_tax_amount']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['tbc_total_discounts_tax_amount'] = str_replace('.', ',', $old_data['tbc_total_discounts_tax_amount']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tbc_total_discounts_tax_amount']);

                                            $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tbc_total_discounts_tax_amount']) : '';
                                        }
                                        else
                                        {
                                            $buffer .= '';
                                        }
                                        ++$i;
                                    }
                                }
                                $buffer .= "\n";
                            }
                        }
                        if ($_has_fr && $old_id != $data['temp_id_order'] && $old_id != 0)
                        {
                            if ($props['display_breakdown_shipping'] == '1')
                            {
                                $actual_fr_suffix = $fr_suffix.$old_data['fr_country_name'];
                                $old_data['fr_carrier_tax_rate'] = number_format($old_data['fr_carrier_tax_rate'], 1, '.', '');
                                $i = 0;
                                foreach ($old_data as $key => $value)
                                {
                                    if (!in_array($key, $excluded_fields))
                                    {
                                        if ($i > 0)
                                        {
                                            $buffer .= $props['delimitor'];
                                        }

                                        if ($i == 0 && $key != $fr_prefix.$old_data['fr_carrier_tax_rate'].$actual_fr_suffix)
                                        {
                                            $buffer .= (($lang_QC == 2) ? 'Frais de port pour la commande '.$old_data['temp_id_order'] : 'Shipping for order '.$old_data['temp_id_order']);
                                        }

                                        if ($key == $fr_prefix.$old_data['fr_carrier_tax_rate'].$actual_fr_suffix)
                                        {
                                            if (isset($old_data['fr_total_shipping_tax_excl']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['fr_total_shipping_tax_excl'] = str_replace('.', ',', $old_data['fr_total_shipping_tax_excl']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['fr_total_shipping_tax_excl']);

                                            $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['fr_total_shipping_tax_excl']) : '';
                                        }
                                        elseif ($key == $fr_amount_prefix.$old_data['fr_carrier_tax_rate'].$actual_fr_suffix)
                                        {
                                            if (isset($old_data['fr_total_shipping_tax_amount']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['fr_total_shipping_tax_amount'] = str_replace('.', ',', $old_data['fr_total_shipping_tax_amount']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['fr_total_shipping_tax_amount']);

                                            $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['fr_total_shipping_tax_amount']) : '';
                                        }
                                        else
                                        {
                                            $buffer .= '';
                                        }
                                        ++$i;
                                    }
                                }
                                $buffer .= "\n";
                            }

                            if (/* !empty($old_data["fr_total_discounts_tax_rate"]) && */ $props['display_breakdown_discounts'] == '1')
                            {
                                $actual_fr_suffix = $fr_suffix.$old_data['fr_country_name'];
                                $old_data['fr_total_discounts_tax_rate'] = number_format($old_data['fr_total_discounts_tax_rate'], 1, '.', '');
                                /*if($old_data["fr_total_discounts_tax_rate"]<=0)
                                {
                                    $old_data["fr_total_discounts_tax_rate"] = number_format($old_data["fr_tax_name"], 1, ".", "");
                                    $old_data["fr_total_discounts_tax_excl"] = number_format(($old_data["fr_total_discounts_tax_excl"]/(1+($old_data["fr_total_discounts_tax_rate"]/100))), 1, ".", "");
                                }*/
                                $i = 0;
                                foreach ($old_data as $key => $value)
                                {
                                    if (!in_array($key, $excluded_fields))
                                    {
                                        if ($i > 0)
                                        {
                                            $buffer .= $props['delimitor'];
                                        }

                                        if ($i == 0 && $key != $fr_prefix.$old_data['fr_total_discounts_tax_rate'].$actual_fr_suffix)
                                        {
                                            $buffer .= (($lang_QC == 2) ? 'Total des réductions pour la commande '.$old_data['temp_id_order'] : 'Reductions total for order '.$old_data['temp_id_order']);
                                        }

                                        if ($key == $fr_prefix.$old_data['fr_total_discounts_tax_rate'].$actual_fr_suffix)
                                        {
                                            if (isset($old_data['fr_total_discounts_tax_excl']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['fr_total_discounts_tax_excl'] = str_replace('.', ',', $old_data['fr_total_discounts_tax_excl']);
                                            }

                                            $totals[$key] = str_replace(',', '.', $totals[$key]) - str_replace(',', '.', $old_data['fr_total_discounts_tax_excl']);

                                            $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['fr_total_discounts_tax_excl']) : '';
                                        }
                                        elseif ($key == $fr_amount_prefix.$old_data['fr_total_discounts_tax_rate'].$actual_fr_suffix)
                                        {
                                            if (isset($old_data['fr_total_discounts_tax_amount']) && $exportTemplate->separator == 2)
                                            {
                                                $old_data['fr_total_discounts_tax_amount'] = str_replace('.', ',', $old_data['fr_total_discounts_tax_amount']);
                                            }
                                            $totals[$key] = str_replace(',', '.', $totals[$key]) - str_replace(',', '.', $old_data['fr_total_discounts_tax_amount']);

                                            $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['fr_total_discounts_tax_amount']) : '';
                                        }
                                        else
                                        {
                                            $buffer .= '';
                                        }
                                        ++$i;
                                    }
                                }
                                $buffer .= "\n";
                            }
                        }
                    }

                    $sql= 'SELECT free_shipping 
                                FROM '._DB_PREFIX_.'order_cart_rule odcr
                                WHERE odcr.id_order = '.(int)$data['temp_id_order'].'
                                AND odcr.free_shipping = 1';
                    $order_free_shipping = (int)Db::getInstance()->getValue($sql);

                    $i = 0;
                    foreach ($data as $key => $value)
                    {
                        if (!in_array($key, $excluded_fields))
                        {
                            if ($i > 0)
                            {
                                $buffer .= $props['delimitor'];
                            }
                            // format date fields

                            if ($value != '' && strpos($key, '_Date') !== false)
                            {
                                @list($date, $hour) = explode(' ', $value);
                                @list($year, $month, $day) = explode('-', $date);
                                if (checkdate($month, $day, $year))
                                {
                                    if ($key != 'Order_Invoice_Date_without_time')
                                    {
                                        if (ExportOrderTools::isNewerPs16x())
                                        {
                                            $value = Tools::displayDate($value, null, true);
                                        }
                                        else
                                        {
                                            $value = Tools::displayDate($value, $id_lang, true);
                                        }
                                    }
                                    else
                                    {
                                        if (ExportOrderTools::isNewerPs16x())
                                        {
                                            $value = Tools::displayDate($value, null, false);
                                        }
                                        else
                                        {
                                            $value = Tools::displayDate($value, $id_lang, false);
                                        }
                                    }
                                }
                            }

                            if ($key == 'Order_Detail_Product_Actual_Quantity')
                            {
                                $value = (int) self::getProductQty($data['temp_product_id'], $data['temp_product_attribute_id'], $data['temp_id_warehouse']);
                            }
                            ## si commande a la livraison gratuite liee à un bon de reduction, alors total shipping = 0
                            if ($key == 'Order_Total_Shipping_ET' || $key == 'Order_Total_Shipping')
                            {
                                if($order_free_shipping)
                                {
                                    $value = 0;
                                }
                            }
                            if ($key == 'Order_Customer_Message')
                            {
                                $value = '"'.str_replace('"', '""', $value).'"';
                            }
                            // deal with default value
                            //                                                $buffer .= $value!=''?ExportOrderTools::prepareValueForCsv($value):ExportOrderFields::getDefaultValues($key, $id_lang);

                            if (ExportOrderFields::getFieldPrice($key) && $exportTemplate->separator == 2)
                            {
                                $temp_value = str_replace('.', ',', $value);
                            }
                            elseif ($exportTemplate->separator == 2 && is_numeric($value))
                            {
                                $temp_value = str_replace('.', ',', $value);
                            }
                            else
                            {
                                $temp_value = $value;
                            }

                            $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($temp_value) : '';

                            if ($print_totals)
                            {
                                if (ExportOrderFields::getFieldTotal($key) == 'orderDetail')
                                {
                                    $totals[$key] += (float) $value;
                                }
                                if (ExportOrderFields::getFieldTotal($key) == 'order' && $old_id != $data['temp_id_order'])
                                {
                                    $totals[$key] += (float) $value;
                                }
                            }

                            ++$i;
                        }
                    }
                    $buffer .= "\n";

                    $old_id = $data['temp_id_order'];
                    $old_data = $data;
                }

                if ($props['display_breakdown_shipping'] == '1' || $props['display_breakdown_discounts'] == '1')
                {
                    if ($_has_tb)
                    {
                        if ($props['display_breakdown_shipping'] == '1')
                        {
                            $old_data['tb_carrier_tax_rate'] = number_format($old_data['tb_carrier_tax_rate'], 1, '.', '');
                            $i = 0;
                            foreach ($old_data as $key => $value)
                            {
                                if (!in_array($key, $excluded_fields))
                                {
                                    if ($i > 0)
                                    {
                                        $buffer .= $props['delimitor'];
                                    }

                                    if ($i == 0 && $key != $tb_prefix.$old_data['tb_carrier_tax_rate'])
                                    {
                                        $buffer .= (($lang_QC == 2) ? 'Frais de port pour la commande '.$old_data['temp_id_order'] : 'Shipping for order '.$old_data['temp_id_order']);
                                    }

                                    if ($key == $tb_prefix.$old_data['tb_carrier_tax_rate'])
                                    {
                                        if (isset($old_data['tb_total_shipping_tax_excl']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['tb_total_shipping_tax_excl'] = str_replace('.', ',', $old_data['tb_total_shipping_tax_excl']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tb_total_shipping_tax_excl']);

                                        $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tb_total_shipping_tax_excl']) : '';
                                    }
                                    elseif ($key == $tb_amount_prefix.$old_data['tb_carrier_tax_rate'])
                                    {
                                        if (isset($old_data['tb_total_shipping_tax_amount']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['tb_total_shipping_tax_amount'] = str_replace('.', ',', $old_data['tb_total_shipping_tax_amount']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tb_total_shipping_tax_amount']);

                                        $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tb_total_shipping_tax_amount']) : '';
                                    }
                                    else
                                    {
                                        $buffer .= '';
                                    }
                                    ++$i;
                                }
                            }
                            $buffer .= "\n";
                        }

                        if (!empty($old_data['tb_total_discounts_tax_rate']) && $props['display_breakdown_discounts'] == '1')
                        {
                            $old_data['tb_total_discounts_tax_rate'] = number_format($old_data['tb_total_discounts_tax_rate'], 1, '.', '');
                            $old_data['tb_total_discounts_tax_rate'] = ExportOrderTools::applyMarginTaxRate($old_data['tb_total_discounts_tax_rate']);
                            $i = 0;
                            foreach ($old_data as $key => $value)
                            {
                                if (!in_array($key, $excluded_fields))
                                {
                                    if ($i > 0)
                                    {
                                        $buffer .= $props['delimitor'];
                                    }

                                    if ($i == 0 && $key != $tb_prefix.$old_data['tb_total_discounts_tax_rate'])
                                    {
                                        $buffer .= (($lang_QC == 2) ? 'Total des réductions pour la commande '.$old_data['temp_id_order'] : 'Reductions total for order '.$old_data['temp_id_order']);
                                    }

                                    if ($key == $tb_prefix.$old_data['tb_total_discounts_tax_rate'])
                                    {
                                        if (isset($old_data['tb_total_discounts_tax_excl']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['tb_total_discounts_tax_excl'] = str_replace('.', ',', $old_data['tb_total_discounts_tax_excl']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) - str_replace(',', '.', $old_data['tb_total_discounts_tax_excl']);

                                        $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['tb_total_discounts_tax_excl']) : '';
                                    }
                                    elseif ($key == $tb_amount_prefix.$old_data['tb_total_discounts_tax_rate'])
                                    {
                                        if (isset($old_data['tb_total_discounts_tax_amount']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['tb_total_discounts_tax_amount'] = str_replace('.', ',', $old_data['tb_total_discounts_tax_amount']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) - str_replace(',', '.', $old_data['tb_total_discounts_tax_amount']);

                                        $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['tb_total_discounts_tax_amount']) : '';
                                    }
                                    else
                                    {
                                        $buffer .= '';
                                    }
                                    ++$i;
                                }
                            }
                            $buffer .= "\n";
                        }
                    }
                    if ($_has_tbc)
                    {
                        if ($props['display_breakdown_shipping'] == '1')
                        {
                            if ($tbc_suffix && isset($old_data['tbc_country_name']))
                            {
                                $actual_tbc_suffix = $tbc_suffix.$old_data['tbc_country_name'];
                            }
                            else
                            {
                                $actual_tbc_suffix = '';
                            }
                            if (isset($old_data['tbc_carrier_tax_rate']))
                            {
                                $old_data['tbc_carrier_tax_rate'] = number_format($old_data['tbc_carrier_tax_rate'], 1, '.', '');
                            }
                            else
                            {
                                $old_data['tbc_carrier_tax_rate'] = 0;
                            }
                            $i = 0;
                            foreach ($old_data as $key => $value)
                            {
                                if (!in_array($key, $excluded_fields))
                                {
                                    if ($i > 0)
                                    {
                                        $buffer .= $props['delimitor'];
                                    }

                                    if ($i == 0 && $key != $tbc_prefix.$old_data['tbc_carrier_tax_rate'].$actual_tbc_suffix)
                                    {
                                        $buffer .= (($lang_QC == 2) ? 'Frais de port pour la commande '.$old_data['temp_id_order'] : 'Shipping for order '.$old_data['temp_id_order']);
                                    }

                                    if ($key == $tbc_prefix.$old_data['tbc_carrier_tax_rate'].$actual_tbc_suffix)
                                    {
                                        if (isset($old_data['tbc_total_shipping_tax_excl']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['tbc_total_shipping_tax_excl'] = str_replace('.', ',', $old_data['tbc_total_shipping_tax_excl']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tbc_total_shipping_tax_excl']);

                                        $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tbc_total_shipping_tax_excl']) : '';
                                    }
                                    elseif ($key == $tbc_amount_prefix.$old_data['tbc_carrier_tax_rate'].$actual_tbc_suffix)
                                    {
                                        if (isset($old_data['tbc_total_shipping_tax_amount']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['tbc_total_shipping_tax_amount'] = str_replace('.', ',', $old_data['tbc_total_shipping_tax_amount']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tbc_total_shipping_tax_amount']);

                                        $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tbc_total_shipping_tax_amount']) : '';
                                    }
                                    else
                                    {
                                        $buffer .= '';
                                    }
                                    ++$i;
                                }
                            }
                            $buffer .= "\n";
                        }
                        if (!empty($old_data['tbc_total_discounts_tax_rate']) && $props['display_breakdown_discounts'] == '1')
                        {
                            $actual_tbc_suffix = $tbc_suffix.$old_data['tbc_country_name'];
                            $old_data['tbc_total_discounts_tax_rate'] = number_format($old_data['tbc_total_discounts_tax_rate'], 1, '.', '');
                            $i = 0;
                            foreach ($old_data as $key => $value)
                            {
                                if (!in_array($key, $excluded_fields))
                                {
                                    if ($i > 0)
                                    {
                                        $buffer .= $props['delimitor'];
                                    }

                                    if ($i == 0 && $key != $tbc_prefix.$old_data['tbc_total_discounts_tax_rate'].$actual_tbc_suffix)
                                    {
                                        $buffer .= (($lang_QC == 2) ? 'Total des réductions pour la commande '.$old_data['temp_id_order'] : 'Reductions total for order '.$old_data['temp_id_order']);
                                    }

                                    if ($key == $tbc_prefix.$old_data['tbc_total_discounts_tax_rate'].$actual_tbc_suffix)
                                    {
                                        if (isset($old_data['tbc_total_discounts_tax_excl']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['tbc_total_discounts_tax_excl'] = str_replace('.', ',', $old_data['tbc_total_discounts_tax_excl']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tbc_total_discounts_tax_excl']);

                                        $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['tbc_total_discounts_tax_excl']) : '';
                                    }
                                    elseif ($key == $tbc_amount_prefix.$old_data['tbc_carrier_tax_rate'].$actual_tbc_suffix)
                                    {
                                        if (isset($old_data['tbc_total_discounts_tax_amount']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['tbc_total_discounts_tax_amount'] = str_replace('.', ',', $old_data['tbc_total_discounts_tax_amount']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['tbc_total_discounts_tax_amount']);

                                        $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['tbc_total_discounts_tax_amount']) : '';
                                    }
                                    else
                                    {
                                        $buffer .= '';
                                    }
                                    ++$i;
                                }
                            }
                            $buffer .= "\n";
                        }
                    }
                    if ($_has_fr)
                    {
                        if ($props['display_breakdown_shipping'] == '1')
                        {
                            $actual_fr_suffix = $fr_suffix.(isset($old_data['fr_country_name']) ? $old_data['fr_country_name'] : '');
                            $old_data['fr_carrier_tax_rate'] = number_format(isset($old_data['fr_carrier_tax_rate']) ? $old_data['fr_carrier_tax_rate'] : 0, 1, '.', '');
                            $i = 0;
                            foreach ($old_data as $key => $value)
                            {
                                if (!in_array($key, $excluded_fields))
                                {
                                    if ($i > 0)
                                    {
                                        $buffer .= $props['delimitor'];
                                    }

                                    if ($i == 0 && $key != $fr_prefix.$old_data['fr_carrier_tax_rate'].$actual_fr_suffix)
                                    {
                                        $buffer .= (($lang_QC == 2) ? 'Frais de port pour la commande '.$old_data['temp_id_order'] : 'Shipping for order '.$old_data['temp_id_order']);
                                    }

                                    if ($key == $fr_prefix.$old_data['fr_carrier_tax_rate'].$actual_fr_suffix)
                                    {
                                        if (isset($old_data['fr_total_shipping_tax_excl']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['fr_total_shipping_tax_excl'] = str_replace('.', ',', $old_data['fr_total_shipping_tax_excl']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['fr_total_shipping_tax_excl']);

                                        $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['fr_total_shipping_tax_excl']) : '';
                                    }
                                    elseif ($key == $fr_amount_prefix.$old_data['fr_carrier_tax_rate'].$actual_fr_suffix)
                                    {
                                        if (isset($old_data['fr_total_shipping_tax_amount']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['fr_total_shipping_tax_amount'] = str_replace('.', ',', $old_data['fr_total_shipping_tax_amount']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) + str_replace(',', '.', $old_data['fr_total_shipping_tax_amount']);

                                        $buffer .= $value != '' ? ExportOrderTools::prepareValueForCsv($old_data['fr_total_shipping_tax_amount']) : '';
                                    }
                                    else
                                    {
                                        $buffer .= '';
                                    }
                                    ++$i;
                                }
                            }
                            $buffer .= "\n";
                        }

                        if (/* !empty($old_data["fr_total_discounts_tax_rate"]) && */ $props['display_breakdown_discounts'] == '1')
                        {
                            $actual_fr_suffix = $fr_suffix.(isset($old_data['fr_country_name']) ? $old_data['fr_country_name'] : '');
                            $old_data['fr_total_discounts_tax_rate'] = number_format(isset($old_data['fr_total_discounts_tax_rate']) ? $old_data['fr_total_discounts_tax_rate'] : 0, 1, '.', '');
                            /*if($old_data["fr_total_discounts_tax_rate"]<=0)
                            {
                                $old_data["fr_total_discounts_tax_rate"] = number_format($old_data["fr_tax_name"], 1, ".", "");
                                $old_data["fr_total_discounts_tax_excl"] = number_format(($old_data["fr_total_discounts_tax_excl"]/(1+($old_data["fr_total_discounts_tax_rate"]/100))), 1, ".", "");
                            }*/
                            $i = 0;
                            foreach ($old_data as $key => $value)
                            {
                                if (!in_array($key, $excluded_fields))
                                {
                                    if ($i > 0)
                                    {
                                        $buffer .= $props['delimitor'];
                                    }

                                    if ($i == 0 && $key != $fr_prefix.$old_data['fr_total_discounts_tax_rate'].$actual_fr_suffix)
                                    {
                                        $buffer .= (($lang_QC == 2) ? 'Total des réductions pour la commande '.$old_data['temp_id_order'] : 'Reductions total for order '.$old_data['temp_id_order']);
                                    }

                                    if ($key == $fr_prefix.$old_data['fr_total_discounts_tax_rate'].$actual_fr_suffix)
                                    {
                                        if (isset($old_data['fr_total_discounts_tax_excl']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['fr_total_discounts_tax_excl'] = str_replace('.', ',', $old_data['fr_total_discounts_tax_excl']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) - str_replace(',', '.', $old_data['fr_total_discounts_tax_excl']);

                                        $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['fr_total_discounts_tax_excl']) : '';
                                    }
                                    elseif ($key == $fr_amount_prefix.$old_data['fr_total_discounts_tax_rate'].$actual_fr_suffix)
                                    {
                                        if (isset($old_data['fr_total_discounts_tax_amount']) && $exportTemplate->separator == 2)
                                        {
                                            $old_data['fr_total_discounts_tax_amount'] = str_replace('.', ',', $old_data['fr_total_discounts_tax_amount']);
                                        }
                                        $totals[$key] = str_replace(',', '.', $totals[$key]) - str_replace(',', '.', $old_data['fr_total_discounts_tax_amount']);

                                        $buffer .= $value != '' ? '-'.ExportOrderTools::prepareValueForCsv($old_data['fr_total_discounts_tax_amount']) : '';
                                    }
                                    else
                                    {
                                        $buffer .= '';
                                    }
                                    ++$i;
                                }
                            }
                            $buffer .= "\n";
                        }
                    }
                }

                if ($print_totals && !empty($datas))
                {
                    $i = 0;
                    foreach ($datas[0] as $key => $value)
                    {
                        if (!in_array($key, $excluded_fields))
                        {
                            if ($i > 0)
                            {
                                $buffer .= $props['delimitor'];
                            }
                            ++$i;
                        }
                    }
                    $buffer .= "\n";

                    $i = 0;
                    foreach ($datas[0] as $key => $value)
                    {
                        if (!in_array($key, $excluded_fields))
                        {
                            if ($i > 0)
                            {
                                $buffer .= $props['delimitor'];
                            }

                            if (isset($totals[$key]) && $exportTemplate->separator == 2)
                            {
                                $totals[$key] = str_replace('.', ',', $totals[$key]);
                            }

                            $buffer .= (isset($totals[$key])) ? ExportOrderTools::prepareValueForCsv($totals[$key]) : '';

                            ++$i;
                        }
                    }
                }

                $buffer = trim($buffer,"$props[delimitor]\n"); ##on supprime les lignes vides défniie par delimiteur et saut de ligne
                ExportOrderTools::addLog('CList export : finally get a buffer of '.Tools::strlen($buffer).' chars');
                // echo nl2br($buffer);die();
                return $buffer;
            }
        }

        return '';
    }

    /**
     * Merge two arrays assuming we append the second to the first matching the first column names and setting unknow columns to default "".
     */
    private function mergeDatas($arr1, $arr2)
    {
        // if no data in array1, the return the second array of result of merging
        if ($arr1 == null || count($arr1) == 0)
        {
            return $arr2;
        }

        // if no data in array2, then return the unchanged array 1
        if ($arr2 == null || count($arr2) == 0)
        {
            return $arr1;
        }

        // build the array2 keys list
        $key1s = array_keys($arr1[0]);

        // append the datas
        $i = count($arr1);
        foreach ($arr2 as $row)
        {
            $arr1[$i] = array();
            // fill the row with default values
            // deal with default values
            foreach ($key1s as $key)
            {
                //                                $arr1[$i][$key] = ExportOrderFields::getDefaultValues($key, $id_lang);
                $arr1[$i][$key] = '';
            }

            foreach ($key1s as $key)
            {
                // if the key exists in the array 2, override the value in new row
                if (array_key_exists($key, $arr2[0]))
                {
                    $arr1[$i][$key] = $row[$key];
                }
            }
            ++$i;
        }

        return $arr1;
    }

    /**
     * Analyze input rules to produce a sql where clause.
     *
     * @param rules : whole rule string get from DB
     */
    private function analyzeRules($rules)
    {
        $where = '';
        ExportOrderTools::addLog('CList run :'.$this->id.' Analyze rules : '.$rules);
        /*
        each rule is separated from each other by this pattern : __operator__
                where operator is the SQL link between the rules : AND, OR
                might find also some options (common or rule specific)

        Known rules (prefixes are : c=customers, ad=address_delivery, ai=address_invoice, cg=customer_group, o=orders) :

            c_gender=>NONE|=|1                                                // filter on customers with gender=1 (value get in db when user defined the criteria)
            c_first_name=>NONE|LIKE|John                        // filter on customers with first_name like '%DOE%'
            c_last_name=>NONE|LIKE|DOE                                // filter on customers with last_name like '%DOE%'
            c_birthday_date=>NONE|1996-04-21|<=                // filter on customers with birthday_date>=1996-04-21
            c_birthday_date=>NONE|1996-04-21[<=|>|1975-01-01                // filter on customers with 1996-04-21<=birthday_date<2000-01-01
            c_email=>NONE|ENDS|free.fr                                // filter on customers with email ending by free.fr
            c_date_add=>NONE|2011-04-21|<=                        // filter on customers added from 2011-04-21

            a_d_company=>NONE|LIKE|Alaloop                        // filter on delivery address at company like '%Alaloop%'
            a_d_address=>NONE|LIKE|rue                                // filter on delivery customers with an address containing "rue" either in one of the 3 address fields

            a_i_company=>NONE|LIKE|Alaloop                        // filter on invoice customers with an address at company like '%Alaloop%'
            a_i_address=>NONE|LIKE|rue                                // filter on invoice customers with an address containing "rue" either in one of the 3 address fields

            o_order_state                                                         // filter on customers with orders in specific states
            o_order_date                                                         // filter on customers with orders applying a date clause
            o_p_order_product                                                // filter on customers with orders containing product list
            o_p_cat_order_pdt_category                                // filter on customers with orders containing product category list

            g_group=>NONE|gp1,gp2,...                                // filter on customers that belong to these groups

        Date rule descriptor :
                date1|operator1|operator2|date2
            or
                N|unit for last 6 month for example
        Examples :
            c_all=>NONE|1__AND__n_all=>NONE|1
            c_all=>NONE|1__AND__c_first_name=>NONE|like|DOE
            c_all=>NONE|1__AND__c_news=>NONE|=|0__AND__c_last_name=>NONE|like|DOE
            c_all=>NONE|1__AND__n_all=>NONE|1__AND__c_birthday_date=>NONE|1996-04-21|<=
            c_all=>NONE|1__AND__n_all=>NONE|1__AND__n_email=>NONE|ENDS|free.fr
            c_all=>NONE|1__AND__n_all=>NONE|1__AND__n_email=>NONE|ENDS|free.fr__AND__c_a_company=>NONE|LIKE|Alaloop
            c_all=>NONE|1__AND__c_n_date_add=>NONE|2011-03-27|>=|<=|2011-04-01
            c_all=>NONE|1__AND__c_n_date_add=>NONE|2011-04-03|>=||
            c_all=>NONE|1__AND__c_n_date_add=>NONE|10|DAY
            c_all=>NONE|1__AND__c_o_order_state=>4,5__AND__c_o_order_agg_nb=>1|>=||__AND__c_o_order_agg_sum=>40|>=||__AND__c_o_order_date=>NONE|>=|100|DAY
            c_all=>NONE|1__AND__n_all=>NONE|1|c_a_postcode=>NONE|BEGINS|64
            c_all=>NONE|1__AND__n_all=>NONE|1__AND__n_email=>NONE|ENDS|gmail.com__AND__c_email=>NONE|ENDS|prestashop.com__AND__c_birthday_date=>NONE|1996-04-21|<=||__AND__c_gender=>NONE|=|1__AND__n_date_add=>NONE|2011-04-20|<=||
            c_all=>NONE|1__AND__c_a_address=>NONE|LIKE|rue
        */

        /*
            Inter rule operators :
                AND
                OR
            Main rule operator :
                NOT
            Number operators :
                <
                <=
                =
                >=
                >
            String operators :
                LIKE
                BEGINS
                ENDS
                REGEXP        - not implemented
                =
        */

        // explode the rules
        $parts = explode('__', $rules);

        // TODO : deal with OR operators - have to include () before and after logical parts
        // Example :
        //                 AND 1==1 OR c.`lastname` LIKE '%DOE%'
        // must become
        //                 AND (1==1 OR c.`lastname` LIKE '%DOE%')
        // OR has an aggregative behaviour

        // load all rules in a array with informations : type (rule or operator), operator, rule_name, rule_descriptor
        // Useful because some rules must be treated grouped like order rules : c_o_order_number & c_o_order_sum for example
        $ruleDescriptors = array();
        ExportOrderTools::addLog('CList run :'.$this->id.' - rule before parsing : '.$rules);
        foreach ($parts as $rule)
        {
            // is this an operator ?
            if ($rule == 'AND' || $rule == 'OR')
            {
                $ruleDescriptors[] = array('type' => 'operator', 'operator' => $rule);
            }
            else // or a rule ?
            {
                $subparts = explode('=>', $rule);
                $ruleName = $subparts[0];
                $ruleDescriptors[] = array('type' => 'rule', 'name' => $ruleName, 'descriptor' => $subparts[1]);
            }
        }
        ExportOrderTools::addLog('CList run :'.$this->id.' - rule descriptors : '.ExportOrderTools::captureVarDump($ruleDescriptors));

        // loop through all rules
        $lastInterRuleOperator = ' AND ';
        foreach ($ruleDescriptors  as $rd)
        {
            // is this an operator ?
            if ($rd['type'] == 'operator')
            {
                $lastInterRuleOperator = ' '.$rd['operator'].' ';
            }
            else
            {
                ExportOrderTools::addLog('CList run :'.$this->id.' - / rule '.$rd['name'].' : '.$rd['descriptor']);

                // get the rule's name & descriptor
                $ruleDescriptor = explode('|', $rd['descriptor']);

                $sqlClause = '';
                switch ($rd['name'])
                {
                    // *************************************************************************
                    //                                                                CUSTOMER RULES
                    //
                    case 'c_gender':
                        // c_gender=>NONE|=|1
                        $sqlClause = $this->getSqlRule('c.`id_gender`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_first_name':
                        // c_first_name=>NONE|LIKE|John
                        $sqlClause = $this->getSqlRule('c.`firstname`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_last_name':
                        // c_last_name=>NONE|LIKE|DOE
                        $sqlClause = $this->getSqlRule('c.`lastname`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_birthday_date':
                        // c_birthday_date=>NONE|1996-04-21|<=||
                        // c_birthday_date=>NONE|1996-04-21[<=|<|2000-01-01
                        // c_birthday_date=>NONE|>=|10|YEAR
                        $sqlClause = $this->getSqlDateRule('c.`birthday`', $ruleDescriptor);
                        break;
                    case 'c_email':
                        // c_email=>NONE|ENDS|free.fr
                        $sqlClause = $this->getSqlRule('c.`email`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;

                        // *************************************************************************
                        //                                                                DELIVERY ADDRESS RULES
                    //
                    case 'a_d_company':
                        // c_a_company=>NONE|LIKE|Alaloop
                        $sqlClause = $this->getSqlRule('ad.`company`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_d_first_name':
                        // c_a_first_name=>NONE|LIKE|DOE
                        $sqlClause = $this->getSqlRule('ad.`firstname`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_d_last_name':
                        // c_a_last_name=>NONE|LIKE|DOE
                        $sqlClause = $this->getSqlRule('ad.`lastname`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_d_address':
                        // filter on either address1, address2 or other
                        $sqlClause = $this->getSqlRule(array('ad.`address1`', 'ad.`address2`', 'ad.`other`'), $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_d_postcode':
                        // c_a_postcode=>NONE|BEGINS|64
                        $sqlClause = $this->getSqlRule('ad.`postcode`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_d_city':
                        // c_a_city=>NONE|EQUAL|Bayonne
                        $sqlClause = $this->getSqlRule('ad.`city`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_d_state':
                        // c_a_state=>NONE|EQUAL|MA
                        $sqlClause = $this->getSqlRule('std.`name`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_d_country':
                        // c_a_country=>NONE|EQUAL|FRANCE
                        $sqlClause = $this->getSqlRule('cld.`name`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_d_vat_number':
                        // a_i_vat_number=>NONE|EQUAL|FRANCE
                        $sqlClause = $this->getSqlRule('ad.`vat_number`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;

                        // *************************************************************************
                        //                                                                INVOICE ADDRESS RULES
                    //
                    case 'a_i_company':
                        // c_a_company=>NONE|LIKE|Alaloop
                        $sqlClause = $this->getSqlRule('ai.`company`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_i_first_name':
                        // c_a_first_name=>NONE|LIKE|DOE
                        $sqlClause = $this->getSqlRule('ai.`firstname`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_i_last_name':
                        // c_a_last_name=>NONE|LIKE|DOE
                        $sqlClause = $this->getSqlRule('ai.`lastname`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_i_address':
                        // filter on either address1, address2 or other
                        $sqlClause = $this->getSqlRule(array('ai.`address1`', 'ai.`address2`', 'ai.`other`'), $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_i_postcode':
                        // c_a_postcode=>NONE|BEGINS|64
                        $sqlClause = $this->getSqlRule('ai.`postcode`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_i_city':
                        // c_a_city=>NONE|EQUAL|Bayonne
                        $sqlClause = $this->getSqlRule('ai.`city`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_i_state':
                        // c_a_state=>NONE|EQUAL|MA
                        $sqlClause = $this->getSqlRule('sti.`name`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_i_country':
                        // c_a_country=>NONE|EQUAL|FRANCE
                        $sqlClause = $this->getSqlRule('cli.`name`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'a_i_vat_number':
                        // a_i_vat_number=>NONE|EQUAL|FRANCE
                        $sqlClause = $this->getSqlRule('ai.`vat_number`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;

                        // *************************************************************************
                        //                                                                CUSTOMER ORDERS RULES
                    //
                        /*
                            Order rule is made of :
                                > states order to consider
                                > date constraint
                                > many others

                            o_order_state --> mandatory if any order rule
                            o_order_date --> normal date rule
                            o_p_order_product --> filter on orders products
                            o_p_cat_order_pdt_category --> filter on orders products categories

                        */
                        // Order rules
                    case 'o_order_state':
                        $sqlClause = $this->getStatOrderRules($rd['name'], $ruleDescriptor);
                        break;
                    case 'o_payment_mode':
                        $sqlClause = $this->getSqlMultiple('o.`payment`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'o_shipping_number':
                        if (ExportOrderTools::isNewerPs15x())
                        {
                            $sqlClause = $this->getSqlRule('odin.`number`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        }
                        else
                        {
                            +$sqlClause = $this->getSqlRule('o.`shipping_number`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        }
                        break;

                    case 'o_ca_carrier':
                        $sqlClause = $this->getSqlRule('ca.`id_reference`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'o_cu_currency':
                        $sqlClause = $this->getSqlRule('cu.`id_currency`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'o_date_add':
                        $sqlClause = $this->getSqlDateRule('o.`date_add`', $ruleDescriptor);
                        break;
                    case 'o_date_invoice':
                        $sqlClause = $this->getSqlDateRule('o.`invoice_date`', $ruleDescriptor);
                        break;
                    case 'o_date_delivery':
                        $sqlClause = $this->getSqlDateRule('o.`delivery_date`', $ruleDescriptor);
                        break;
                    case 'o_gift':
                        $sqlClause = $this->getSqlRule('o.`gift`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'o_invoice':
                        $sqlClause = $this->getSqlRule('o.`invoice_number`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;

                    case 'o_total_real':
                        $sqlClause = $this->getSqlNumberRule('o.`total_paid_real`', $ruleDescriptor);
                        break;
                    case 'o_total_paid':
                        $sqlClause = $this->getSqlNumberRule('o.`total_paid`', $ruleDescriptor);
                        break;
                    case 'o_total_discount':
                        $sqlClause = $this->getSqlNumberRule('o.`total_discounts`', $ruleDescriptor);
                        break;
                    case 'o_total_products_et':
                        $sqlClause = $this->getSqlNumberRule('o.`total_products`', $ruleDescriptor);
                        break;
                    case 'o_total_products_it':
                        $sqlClause = $this->getSqlNumberRule('o.`total_products_wt`', $ruleDescriptor);
                        break;
                    case 'o_total_shipping':
                        $sqlClause = $this->getSqlNumberRule('o.`total_shipping`', $ruleDescriptor);
                        break;

                    case 'o_p_order_product':
                        // c_o_p_order_product=>NONE|7,8,10,11
                        $sqlClause = $this->getStatOrderRules($rd['name'], $ruleDescriptor);
                        break;
                    case 'o_p_cat_order_pdt_category':
                        // c_o_p_cat_order_pdt_category=>NONE|7,8,10,11
                        $sqlClause = $this->getStatOrderRules($rd['name'], $ruleDescriptor);
                        break;

                        // *************************************************************************
                        //                                                                CUSTOMER GROUP RULES
                    //
                    case 'cg_customerGroup':
                        // cg_customerGroup=>NONE|gp1,gp2,...
                        $sqlClause = $this->getSqlGroupRule('cg.`id_group`', $ruleDescriptor[1]);
                        break;

                    case 'os_date_add':
                        $sqlClause = ' o.id_order IN (SELECT os.id_order FROM '._DB_PREFIX_.'order_slip os WHERE ';

                        $field = 'os.date_add';
                        $rangeRule = count($ruleDescriptor) >= 5;
                        if ($rangeRule)
                        {
                            if ($ruleDescriptor[3] != '')
                            {
                                $sqlClause .= '('.
                                    'DATE('.$field.')'.$ruleDescriptor[2]."STR_TO_DATE('".$ruleDescriptor[1]."','%Y-%m-%d')".
                                    ' AND '.
                                    'DATE('.$field.')'.$ruleDescriptor[3]."STR_TO_DATE('".$ruleDescriptor[4]."','%Y-%m-%d')".
                                    ')';
                            }
                            else
                            {
                                $sqlClause .= 'DATE('.$field.')'.$ruleDescriptor[2]."STR_TO_DATE('".$ruleDescriptor[1]."','%Y-%m-%d')";
                            }
                        }
                        else
                        {
                            $sqlClause .= 'DATE('.$field.')'.$ruleDescriptor[1].'DATE_SUB(CURDATE(),INTERVAL '.$ruleDescriptor[2].' '.$ruleDescriptor[3].')';
                        }
                        $sqlClause .= ' ) ';
                        break;

                    case 'olh_date_add':
                        $sqlClause = $this->getSqlDateRule('oh1.`date_add`', $ruleDescriptor);
                        break;
                }

                // deal with main rule operator (NONE or NOT)
                if ($ruleDescriptor[0] == 'NOT')
                {
                    $sqlClause = 'NOT('.$sqlClause.')';
                }
                // append the rule as sql clause
                $where .= $lastInterRuleOperator.$sqlClause;
            }
        }

        // return null or a valid where clause
        return $where == '' ? null : $where;
    }

    /**
     * Get a rule by its name if exists.
     */
    public static function getRuleByName($name, $ruleDescriptors)
    {
        foreach ($ruleDescriptors as $ruleDescriptor)
        {
            if (($ruleDescriptor['type'] == 'rule' || $ruleDescriptor['type'] == 'options') && $ruleDescriptor['name'] == $name)
            {
                return $ruleDescriptor['descriptor'];
            }
        }

        return null;
    }

    /**
     * Translate a rule descriptor in SQL
     * $fields : could be either a single field (string) or many one (array of string).
     */
    private function getSqlRule($fields, $valueOperator1, $value1, $valueOperator2 = null, $value2 = null)
    {
        // special operator1 (could not be in operator2)
        if (in_array($valueOperator1, array('like', 'begin', 'end', 'equal')))
        {
            // string comparison are all case insensitive
            $value1 = Tools::strtolower($value1);
            if (is_array($fields))
            {
                for ($i = 0; $i < count($fields); ++$i)
                {
                    $fields[$i] = 'LOWER('.$fields[$i].')';
                }
            }
            else
            {
                $fields = 'LOWER('.$fields.')';
            }
        }
        if ($valueOperator1 == 'like')
        {
            $value1 = '%'.Tools::strtolower($value1).'%';
        }
        elseif ($valueOperator1 == 'begin')
        {
            $valueOperator1 = 'like';
            $value1 = Tools::strtolower($value1).'%';
        }
        elseif ($valueOperator1 == 'end')
        {
            $valueOperator1 = 'like';
            $value1 = '%'.Tools::strtolower($value1);
        }
        elseif ($valueOperator1 == 'equal')
        {
            $valueOperator1 = '=';
            $value1 = Tools::strtolower($value1);
        }

        // always quote the \ and the ' characters
        $value1 = str_replace('\\', '\\\\', $value1);
        $value1 = str_replace("'", "\\'", $value1);
        $value2 = str_replace('\\', '\\\\', $value2);
        $value2 = str_replace("'", "\\'", $value2);

        // build clause
        if (is_array($fields))
        {
            if (!$valueOperator2)
            {
                $sqlClause = '(';
                for ($i = 0; $i < count($fields); ++$i)
                {
                    $field = $fields[$i];
                    if ($i > 0)
                    {
                        $sqlClause .= ' OR ';
                    }
                    $sqlClause .= $field.' '.$valueOperator1.' '."'".$value1."'";
                }
                $sqlClause .= ')';
            }
            else
            {
                $sqlClause = '(';
                for ($i = 0; $i < count($fields); ++$i)
                {
                    $field = $fields[$i];
                    if ($i > 0)
                    {
                        $sqlClause .= ' OR ';
                    }
                    $sqlClause .= '('.$field.' '.$valueOperator1.' '."'".$value1."' AND ".$field.' '.$valueOperator2.' '."'".$value2."')";
                }
                $sqlClause .= ')';
            }
        }
        else
        {
            if (!$valueOperator2)
            {
                $sqlClause = $fields.' '.$valueOperator1.' '."'".$value1."'";
            }
            else
            {
                $sqlClause = '('.$fields.' '.$valueOperator1.' '."'".$value1."' AND ".$fields.' '.$valueOperator2.' '."'".$value2."')";
            }
        }

        return $sqlClause;
    }

    private function getSqlMultiple($fields, $valueOperator1, $value1)
    {
        $sqlClause = '';

        $sql_tmp = '';
        $vals = explode(',', $value1);
        foreach ($vals as $val)
        {
            if (!empty($sql_tmp))
            {
                $sql_tmp .= ' OR ';
            }
            $sql_tmp .= ' ('.$fields.' '.$valueOperator1.' '."'".$val."') ";
        }

        if (!empty($sql_tmp))
        {
            $sqlClause = '('.$sql_tmp.')';
        }

        return $sqlClause;
    }

    /**
     * Translate a date rule descriptor in SQL.
     */
    private function getSqlDateRule($field, $ruleDescriptor)
    {
        $sqlClause = '';

        // is this a range rule or a last rule ?
        $rangeRule = count($ruleDescriptor) >= 5;

        if ($rangeRule)
        {
            // rule_name|date1|operator1|operator2|date2
            // 0        |1    |2        |3        |4
            // DATE(a.date_upd)>=STR_TO_DATE('2011-03-12','%Y-%m-%d')
            if ($ruleDescriptor[3] != '')
            {
                $sqlClause = '('.
                            'DATE('.$field.')'.$ruleDescriptor[2]."STR_TO_DATE('".$ruleDescriptor[1]."','%Y-%m-%d')".
                            ' AND '.
                            'DATE('.$field.')'.$ruleDescriptor[3]."STR_TO_DATE('".$ruleDescriptor[4]."','%Y-%m-%d')".
                            ')';
            }
            else
            {
                $sqlClause = 'DATE('.$field.')'.$ruleDescriptor[2]."STR_TO_DATE('".$ruleDescriptor[1]."','%Y-%m-%d')";
            }
        }
        else
        {
            // rule_name|operator|N|unit
            // 0        |1             |2|3
            // DATE(o.date_upd)>=DATE_SUB(CURDATE(),INTERVAL 6 MONTH)
            $sqlClause = 'DATE('.$field.')'.$ruleDescriptor[1].'DATE_SUB(CURDATE(),INTERVAL '.$ruleDescriptor[2].' '.$ruleDescriptor[3].')';
        }

        return $sqlClause;
    }

    /**
     * Translate a Number rule descriptor in SQL.
     */
    private function getSqlNumberRule($field, $ruleDescriptor)
    {
        $sqlClause = '';

        // first part of rule
        $sqlClause = '('.$field.$ruleDescriptor[2].$ruleDescriptor[1];

        // is there a second part ?
        if ($ruleDescriptor[3] != '')
        {
            $sqlClause .= ' AND '.$field.$ruleDescriptor[3].$ruleDescriptor[4];
        }

        $sqlClause .= ')';

        return $sqlClause;
    }

    /**
     * Translate a group rule descriptor in SQL.
     */
    private function getSqlGroupRule($field, $groupList)
    {
        $sql = '';
        $groups = explode(',', $groupList);
        if (count($groups) > 0)
        {
            $sql .= $field.'IN (';
            for ($i = 0; $i < count($groups); ++$i)
            {
                $group = $groups[$i];
                if ($i > 0)
                {
                    $sql .= ',';
                }
                $sql .= $group;
            }
            $sql .= ')';
        }

        return $sql;
    }

    /**
     * Translate an order Stat rule descriptor in SQL.
                                    0   |1
     */
    private function getStatOrderRules($ruleName, $ruleDescriptor)
    {
        $sql = '';

        if ($ruleName == 'o_order_state')
        {
            // list order states to consider
            $sql .= '(select oh.`id_order_state` from `'._DB_PREFIX_.'orders` o1
                                                        left join `'._DB_PREFIX_.'order_history` oh on o1.`id_order`=oh.`id_order`
                                                        where o1.`id_order`=o.`id_order`
                                                        order by oh.`date_add` desc, oh.`id_order_history` desc limit 0, 1) IN ('.$ruleDescriptor[1].')';

            return $sql;
        }
        elseif ($ruleName == 'o_p_order_product')
        {
            // filter on orders products
            $sqlClause = 'od.`product_id` IN ('.$ruleDescriptor[1].')';

            return $sqlClause;
        }
        elseif ($ruleName == 'o_p_cat_order_pdt_category')
        {
            // filter on orders products categories
            $sqlClause = 'cp.`id_category` IN ('.$ruleDescriptor[1].')';

            return $sqlClause;
        }
    }

    public function getShopName($id)
    {
        $return = '';
        if (!empty($id))
        {
            $shop = Shop::getShop($id);
            $return = $shop['name'];
        }

        return $return;
    }

    public static function getProductQty($id_product, $id_product_attribute = null, $id_warehouse = null)
    {
        $return = 0;
        if (empty($id_product))
        {
            return $return;
        }

        if (!empty($id_product_attribute))
        {
            $return = self::_getProductAttributeQty($id_product, $id_product_attribute, $id_warehouse);
        }
        else
        {
            $return = self::_getProductQty($id_product, $id_product_attribute, $id_warehouse);
        }

        return $return;
    }

    public static function _getProductQty($id_product, $id_product_attribute = null, $id_warehouse = null)
    {
        $return = 0;
        if (empty($id_product))
        {
            return $return;
        }

        $id_shop = 0;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $id_shop = Context::getContext()->shop->id;
            if (empty($id_shop))
            {
                $id_shop = Configuration::get('PS_SHOP_DEFAULT');
            }
        }

        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $product = new Product($id_product, false, null, (int) $id_shop);
        }
        else
        {
            $product = new Product($id_product);
        }

        if (SCAS && empty($id_warehouse))
        {
            return 0;
        }

        $type_advanced_stock_management = 1; // Not Advanced Stock Management
        $is_advanced_stock_management = false;
        $has_combination = false;
        $not_in_warehouse = true;
        $without_warehouse = true;
        $usable_quantity = 0;

        if (SCAS)
        {
            // Produit utilise la gestion avancée
            if ($product->advanced_stock_management == 1)
            {
                $is_advanced_stock_management = true;
                $type_advanced_stock_management = 2; // With Advanced Stock Management

                $query = new DbQuery();
                $query->select('st.id_warehouse');
                $query->from('stock', 'st');
                $query->where('st.id_product = '.(int) $id_product.'
                                                        AND st.id_product_attribute = 0
                                                        AND st.id_warehouse != 0
                                                        AND usable_quantity >0'
                );
                $rslt = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                if (count($rslt) > 0)
                {
                    $id_warehouse = $rslt[0]['id_warehouse'];
                }

                // Produit est lié à l'entrepôt
                $temp_check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int) $id_product, 0,
                    (int) $id_warehouse);
                if (!empty($temp_check_in_warehouse))
                {
                    $not_in_warehouse = false;
                }

                if (!StockAvailable::dependsOnStock((int) $id_product, (int) $id_shop))
                {
                    $type_advanced_stock_management = 3;
                }// With Advanced Stock Management + Manual management
            }
        }

        if ($product->hasAttributes())
        {
            $has_combination = true;
        }

        if (SCAS && $type_advanced_stock_management == 2 && $has_combination && !$not_in_warehouse) // Si PS>=1.5 & Stock Avancé & possède des déclinaisons & est dans l'entrepot
        {
            $query = new DbQuery();
            $query->select('SUM(st.usable_quantity) as usable_quantity');
            $query->from('stock', 'st');
            $query->where('st.id_product = '.(int) $id_product.'');
            $query->where('st.id_warehouse = '.(int) $id_warehouse.'');
            $query->where('st.id_product_attribute != 0');
            $quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
            $return = $quantities['usable_quantity'];
        }
        elseif (SCAS && $type_advanced_stock_management == 2 && !$has_combination && !$not_in_warehouse) // Si PS>=1.5 & Stock Avancé & n'a pas de déclinaison & est dans l'entrepot
        {
            $query = new DbQuery();
            $query->select('SUM(usable_quantity) as usable_quantity');
            $query->from('stock');
            $query->where('id_product = '.(int) $id_product.'');
            $query->where('id_warehouse = '.(int) $id_warehouse.'');
            $quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
            $return = $quantities['usable_quantity'];
        }
        elseif (SCAS && $type_advanced_stock_management == 2 && $not_in_warehouse) // Si PS>=1.5 & Stock Avancé & n'a pas de déclinaison & n'est pas l'entrepot
        {
            $return = 0;
        }
        elseif (/* !SCAS && */
            version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $has_combination
        ) { // Si PS>=1.5 & pas Stock Avancé & possède des déclinaisons
            $query = new DbQuery();
            $query->select('SUM(quantity)');
            $query->from('stock_available');
            $query->where('id_product = '.(int) $id_product);
            $query->where('id_product_attribute != 0');
            if (SCMS)
            {
                $query = StockAvailable::addSqlShopRestriction($query, (int) $id_shop);
            }

            $return = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        }
        elseif (/* !SCAS && */
            version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !$has_combination
        ) { // Si PS>=1.5 & pas Stock Avancé & possède des déclinaisons
            $query = new DbQuery();
            $query->select('quantity');
            $query->from('stock_available');
            $query->where('id_product = '.(int) $id_product);
            $query->where('id_product_attribute = 0');
            if (SCMS)
            {
                $query = StockAvailable::addSqlShopRestriction($query, (int) $id_shop);
            }

            $return = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        }
        else
        {
            $return = $product->quantity;
        }

        if (empty($return))
        {
            $return = 0;
        }

        return $return;
    }

    public static function _getProductAttributeQty($id_product, $id_product_attribute = null)
    {
        $return = 0;
        if (empty($id_product))
        {
            return $return;
        }

        $id_shop = 0;
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $id_shop = Context::getContext()->shop->id;
            if (empty($id_shop))
            {
                $id_shop = Configuration::get('PS_SHOP_DEFAULT');
            }
        }

        if (SCMS)
        {
            $product = new Product($id_product, false, null, (int) $id_shop);
        }
        else
        {
            $product = new Product($id_product);
        }

        if (SCAS && empty($id_warehouse))
        {
            return 0;
        }

        $is_advanced_stock_management = false;
        $type_advanced_stock_management = 1; // Not Advanced Stock Management
        $not_in_warehouse = true;
        $without_warehouse = true;
        if (SCAS)
        {
            if ($product->advanced_stock_management == 1)
            {
                $is_advanced_stock_management = true;
                $type_advanced_stock_management = 2; // With Advanced Stock Management

                $query = new DbQuery();
                $query->select('st.id_warehouse');
                $query->from('stock', 'st');
                $query->where('st.id_product = '.(int) $id_product.'
                                                        AND st.id_product_attribute = '.(int) $id_product_attribute.'
                                                        AND st.id_warehouse != 0
                                                        AND usable_quantity >0'
                );
                $rslt = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                if (count($rslt) > 0)
                {
                    $id_warehouse = $rslt[0]['id_warehouse'];
                }

                if (!StockAvailable::dependsOnStock((int) $id_product, (int) $id_shop))
                {
                    $type_advanced_stock_management = 3;
                }// With Advanced Stock Management + Manual management
            }

            // Déclinaison est liée à l'entrepôt
            $temp_check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int) $id_product,
                (int) $id_product_attribute, (int) $id_warehouse);
            if (!empty($temp_check_in_warehouse))
            {
                $not_in_warehouse = false;
                $without_warehouse = false;
            }
        }

        if (SCAS && $type_advanced_stock_management == 2 && !$not_in_warehouse) // Si PS>=1.5 & Stock Avancé & est dans l'entrepot
        {
            $query = new DbQuery();
            $query->select('SUM(st.usable_quantity) as usable_quantity');
            $query->from('stock', 'st');
            $query->where('st.id_product = '.(int) $id_product.'');
            $query->where('st.id_warehouse = '.(int) $id_warehouse.'');
            $query->where('st.id_product_attribute = '.(int) $id_product_attribute.'');
            $quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
            $return = $quantities['usable_quantity'];
        }
        elseif (SCAS && $type_advanced_stock_management == 2 && $not_in_warehouse) // Si PS>=1.5 & Stock Avancé & n'est pas l'entrepot
        {
            $return = 0;
        }
        elseif ((!SCAS || (SCAS && $type_advanced_stock_management == 1)) && version_compare(_PS_VERSION_, '1.5.0.0', '>=')) // Si PS>=1.5 & pas Stock Avancé
        {
            $query = new DbQuery();
            $query->select('SUM(quantity)');
            $query->from('stock_available');
            $query->where('id_product = '.(int) $id_product);
            $query->where('id_product_attribute = '.(int) $id_product_attribute);
            if (SCMS)
            {
                $query = StockAvailable::addSqlShopRestriction($query, (int) $id_shop);
            }

            $return = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
        }
        else
        {
            if (SCMS)
            {
                $combination = new Combination($id_product_attribute, null, (int) $id_shop);
            }
            elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $combination = new Combination($id_product_attribute);
            }
            else
            {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                                SELECT quantity
                                FROM `'._DB_PREFIX_.'product_attribute`
                                WHERE id_product_attribute = "'.(int) $id_product_attribute.'"');
                $combination = new stdClass();
                if (!empty($result[0]['quantity']) && is_numeric($result[0]['quantity']))
                {
                    $combination->quantity = (int) $result[0]['quantity'];
                }
                else
                {
                    $combination->quantity = 0;
                }
            }
            $return = $combination->quantity;
        }

        if (empty($return))
        {
            $return = 0;
        }

        return $return;
    }

    public $exist_tax_rates;

    public function _fillTaxRates()
    {
        $margin = Configuration::get('SC_QUICKACCOUNTING_MARGIN');
        $exclude = Configuration::get('SC_QUICKACCOUNTING_EXCLUDE_RATE');
        $excludes = array();
        $exps = explode("\n", $exclude);
        foreach ($exps as $exp)
        {
            $exp = trim($exp);
            if (!empty($exp))
            {
                $rate = number_format($exp, 1, '.', '');
                $excludes[$rate] = $rate;
            }
        }

        $exist_tax_rates = array(
            array('rate' => '100.0', 'min' => (100 - $margin), 'max' => (100 + $margin)),
            array('rate' => '0.0', 'min' => (0 - $margin), 'max' => $margin),
        );

        $sql = 'SELECT DISTINCT(rate) as rate FROM '._DB_PREFIX_.'tax';
        $rates = Db::getInstance()->executeS($sql);
        foreach ($rates as $rate)
        {
            // $rate["rate"] = round($rate["rate"],1);
            $rate['rate'] = number_format($rate['rate'], 1, '.', '');
            if (!isset($excludes[$rate['rate']]))
            {
                $exist_tax_rates[] = array('rate' => $rate['rate'], 'min' => ($rate['rate'] - $margin), 'max' => ($rate['rate'] + $margin));
            }
        }
        $this->exist_tax_rates = $exist_tax_rates;
    }

    public function _goodTaxRate($rate)
    {
        $rate = number_format($rate, 1, '.', '');
        $return = $rate;

        if (!isset($this->exist_tax_rates[$rate]))
        {
            foreach ($this->exist_tax_rates as $exist_tax_rate)
            {
                if ($exist_tax_rate['min'] <= $rate && $rate <= $exist_tax_rate['max'])
                {
                    $return = $exist_tax_rate['rate'];
                    break;
                }
            }
        }

        return $return;
    }

    /**
     * @param array $id_order_list
     * @return bool
     *
     */
    public function fillTax($id_order_list)
    {
        if(is_array($id_order_list))
        {
            $id_order_list = implode(',',$id_order_list);
        }
        $sql = 'SELECT *
                FROM '._DB_PREFIX_.'order_detail
                WHERE
                (
                    sc_qc_product_price IS NULL
                    OR sc_qc_product_price = ""
                    OR sc_qc_product_price = 0
                )
                    AND id_order IN ('.pInSQL($id_order_list).')';
        $data = Db::getInstance()->executeS($sql);
        if ($data)
        {
            foreach ($data as $row)
            {
                $group_reduction = 1;
                if (/* version_compare(_PS_VERSION_, '1.5.0.0', '<') && */ !empty($row['group_reduction']))
                {
                    $group_reduction = 1 - $row['group_reduction'] / 100;
                }

                if ($row['reduction_percent'] != 0)
                {
                    $row['product_price'] = ($row['product_price'] - $row['product_price'] * ($row['reduction_percent'] * 0.01));
                }

                if ($row['reduction_amount'] != 0)
                {
                    if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
                    {
                        $row['product_price'] = ($row['product_price'] - ($row['reduction_amount'] / (1 + $row['tax_rate'] / 100)));
                    }
                    else
                    {
                        $row['product_price'] = ($row['product_price'] - $row['reduction_amount_tax_excl']);
                    }
                }

                if ($row['group_reduction'] > 0)
                {
                    $row['product_price'] = $row['product_price'] * $group_reduction;
                }

                $sql = 'UPDATE '._DB_PREFIX_.'order_detail 
                        SET sc_qc_product_price="'.pSQL($row['product_price']).'" 
                        WHERE id_order_detail = '.(int) $row['id_order_detail'];
                Db::getInstance()->execute($sql);
            }
        }

        $sql = 'SELECT od.id_order_detail, od.id_tax_rules_group, od.id_order, a.id_country, a.id_state
                FROM '._DB_PREFIX_.'order_detail od
                INNER JOIN `'._DB_PREFIX_.'orders` o 
                    ON (od.`id_order` = o.`id_order`)
                INNER JOIN `'._DB_PREFIX_.'address` a 
                    ON (a.`id_address` = o.`id_address_delivery`)
                WHERE
                    (
                        od.tax_name IS NULL
                        OR od.tax_name = ""
                    )
                    AND ( od.id_tax_rules_group>0 && od.id_tax_rules_group!="")
                    AND od.id_order IN ('.pInSQL($id_order_list).')';
        $data = Db::getInstance()->executeS($sql);
        foreach ($data as $row) {
            $sql = 'SELECT t.rate
                FROM `' . _DB_PREFIX_ . 'tax_rule` tr
                LEFT JOIN `' . _DB_PREFIX_ . 'tax` t 
                    ON (t.`id_tax` = tr.`id_tax`)
                WHERE tr.`id_tax_rules_group` = ' . (int)$row['id_tax_rules_group'] . '
                    AND tr.`id_country` = ' . (int)$row['id_country'] . '
                    AND tr.`id_state` = ' . (int)$row['id_state'];
            $tmp = Db::getInstance()->getValue($sql);

            $rate = number_format($tmp, 1, '.', '');

            $sql = 'UPDATE ' . _DB_PREFIX_ . 'order_detail 
                    SET tax_name="' . pSQL($rate) . '",
                        tax_rate="' . pSQL($rate) . '" 
                    WHERE id_order_detail = ' . (int)$row['id_order_detail'];
            Db::getInstance()->execute($sql);
        }

        $margin = Configuration::get('SC_QUICKACCOUNTING_MARGIN');
        $exclude = Configuration::get('SC_QUICKACCOUNTING_EXCLUDE_RATE');
        $excludes = array();
        $exps = explode("\n", $exclude);
        foreach ($exps as $exp)
        {
            $exp = trim($exp);
            if (!empty($exp))
            {
                $rate = number_format($exp, 1, '.', '');
                $excludes[$rate] = $rate;
            }
        }

        $exist_tax_rates = array(
            array('rate' => '100.0', 'min' => (100 - $margin), 'max' => (100 + $margin)),
            array('rate' => '0.0', 'min' => (0 - $margin), 'max' => $margin),
        );

        $sql = 'SELECT DISTINCT(rate) as rate FROM '._DB_PREFIX_.'tax';
        $rates = Db::getInstance()->executeS($sql);
        foreach ($rates as $rate)
        {
            // $rate["rate"] = round($rate["rate"],1);
            $rate['rate'] = number_format($rate['rate'], 1, '.', '');
            if (!isset($excludes[$rate['rate']]))
            {
                $exist_tax_rates[] = array('rate' => $rate['rate'], 'min' => ($rate['rate'] - $margin), 'max' => ($rate['rate'] + $margin));
            }
        }

        $sql = 'SELECT id_order_detail, unit_price_tax_excl, unit_price_tax_incl, sc_qc_product_price
                FROM '._DB_PREFIX_.'order_detail
                WHERE
                    (
                        tax_rate IS NULL
                        OR tax_rate = ""
                        OR tax_rate = 0
                        OR tax_name IS NULL
                        OR tax_name = ""
                        OR ( id_tax_rules_group=0 || id_tax_rules_group="")
                    )
                    AND id_order IN ('.pInSQL($id_order_list).')';
        $data = Db::getInstance()->executeS($sql);
        if($data) {
            foreach ($data as $row)
            {
                $rate = number_format((($row['unit_price_tax_incl'] / $row['unit_price_tax_excl']) - 1) * 100, 1, '.', '');

                if (!isset($exist_tax_rates[$rate]))
                {
                    foreach ($exist_tax_rates as $exist_tax_rate)
                    {
                        if ($exist_tax_rate['min'] <= $rate && $rate <= $exist_tax_rate['max'])
                        {
                            $rate = $exist_tax_rate['rate'];
                            break;
                        }
                    }
                }

                if ($rate <= -100)
                {
                    $rate = '0';
                }
                if (empty($rate))
                {
                    $rate = '0';
                }
                $sql = 'UPDATE '._DB_PREFIX_.'order_detail 
                        SET tax_rate="'.pSQL($rate).'", 
                            tax_name="'.pSQL($rate).'" 
                        WHERE id_order_detail = '.(int) $row['id_order_detail'];
                Db::getInstance()->execute($sql);
            }
        }
        return true;
    }
}
