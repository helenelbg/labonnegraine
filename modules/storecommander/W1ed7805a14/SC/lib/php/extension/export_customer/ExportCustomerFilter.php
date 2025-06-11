<?php

class ExportCustomerFilter extends ObjectModel
{
    public $id_extension_export_customer_filter;

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

    // special field to deal with logical relation to other list
    private $otherListRuleDescriptor = null;

    private $_orderStateSql = null;

    private $contain_address = null;

    public static $definition = array(
        'table' => SC_DB_PREFIX . 'extension_export_customer_filter',
        'primary' => 'id_extension_export_customer_filter',
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
                                                FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`');
    }

    public static function getAll($except = null)
    {
        $query = 'SELECT `'.self::$definition['primary'].'`, `name`
                    FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` 
                    WHERE 1=1 ' . ($except == null ? '' : ' AND `name`<>"' . $except . '"') . '
                    ORDER BY `name`';
        return Db::getInstance()->executeS($query);
    }

    public function getFields()
    {
        parent::validateFields();

        $sd = urldecode($this->static_definition);
        $dd = urldecode($this->dynamic_definition);

        ExportCustomerTools::addLog('CList edit : Class.getFields()');
        ExportCustomerTools::addLog('CList edit : SD:' . $this->static_definition . ' becomes ' . $sd);
        ExportCustomerTools::addLog('CList edit : DD:' . $this->dynamic_definition . ' becomes ' . $dd);
        $fields = array();
        $fields['name'] = pSQL($this->name);
        $fields['id_shop'] = (int)($this->id_shop);
        $fields['description'] = pSQL($this->description);
        $fields['static_definition'] = pSQL($sd);
        // need to preserve HTML tages because of pSQL (it truncates if a rule operator is <=)
        $fields['dynamic_definition'] = pSQL($dd, true);
        $fields['date_add'] = pSQL($this->date_add);
        $fields['date_upd'] = pSQL($this->date_upd);
        return $fields;
    }

    /**
     * Build the SQL query to load all datas
     */
    public function getSqlQuery($aliases, $id_lang)
    {
        /*
         * For future developments, have to deal with orders criteria with sql query such as :
             select c.id_customer, c.firstname, c.lastname,
                (select count(*) from ps_orders where id_customer=c.id_customer) as number_of_orders,
                (select sum(total_paid) from ps_orders where id_customer=c.id_customer) as total_of_orders,
                (select date_add from ps_orders where id_customer=c.id_customer order by date_add desc limit 1) as last_order_date
            from ps_customer c
            where (select count(*) from ps_orders where id_customer=c.id_customer)>1
    
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
        $sql = '';
        ExportCustomerTools::addLog('CList run :' . $this->id . ' for aliases ' . ExportCustomerTools::captureVarDump($aliases) . ' and id_lang ' . (int)$id_lang);

        // include customers ? include newsletter table ?
        $includeCustomers = (strpos($this->dynamic_definition, 'c_') !== false);
        $includeAddresses = (strpos($this->dynamic_definition, 'a_') !== false || ExportCustomerFields::isFieldOfThisType('address', $aliases));
        $includeGroups = (strpos($this->dynamic_definition, 'g_') !== false || ExportCustomerFields::isFieldOfThisType('group', $aliases));
        $includeGroupDefault = (ExportCustomerFields::isFieldOfThisType('group_default', $aliases));
        $includeOrders = (strpos($this->dynamic_definition, 'o_') !== false || ExportCustomerFields::isFieldOfThisType('order', $aliases));
        // only one field in this category and it could not be get as an alias, only a filter
        $includeOrderProducts = (strpos($this->dynamic_definition, 'o_p_') !== false);
        // only one field in this category and it could not be get as an alias, only a filter
        $includeOrderProductsCategories = (strpos($this->dynamic_definition, 'o_p_cat_') !== false);
        $includeNewsletter = (strpos($this->dynamic_definition, 'n_') !== false);
        ExportCustomerTools::addLog('CList run :' . $this->id . '.Ic:' . $includeCustomers . ',Ia:' . $includeAddresses . ',Ig:' . $includeGroups . ',Io:' . $includeOrders . ',In:' . $includeNewsletter);

        if (strpos($this->dynamic_definition, 'o_order_without') !== false) {
            $tmp = $this->dynamic_definition;
            $tmp = explode('__', $tmp);
            foreach ($tmp as $entry) {
                if (strpos($entry, 'o_order_without') !== false) {
                    $entry_value = explode('|', $entry);
                    if ($entry_value[2] == 1) {
                        $includeOrders = false;
                        $includeOrderProducts = false;
                        $includeOrderProductsCategories = false;
                        break;
                    }
                }
            }
        }

        // if have to include customers, then ok for this query
        if ($includeCustomers) {
            // analyze the dynamic definition if any
            $where = null;
            if ($this->dynamic_definition) {
                // get SQL fields from the export template
                $sqlFields = ExportCustomerFields::getSqlSelectFields('c', $aliases, $id_lang);

                $sqlFields_temp = $sqlFields;
                if (in_array('Order_Number', $aliases)) {
                    $sqlFields_temp = str_replace(",{Order_Number}", "", $sqlFields_temp);
                }
                if (in_array('Order_Total_Amount', $aliases)) {
                    $sqlFields_temp = str_replace(",{Order_Total_Amount}", "", $sqlFields_temp);
                }

                if ($includeAddresses) {
                    $this->contain_address = 1;
                }

                // build the where clause
                $where = $this->analyzeRules('c', $this->dynamic_definition, $sqlFields_temp, ExportCustomerFields::needAggregation($aliases));
                if ($where) {
                    $specialSelectFields = '';
                    if (in_array('Order_Number', $aliases)) {
                        $fieldTxt = $this->getOrderNumberSelectField(true);
                        if ($fieldTxt) {
                            $sqlFields = str_replace("{Order_Number}", $fieldTxt, $sqlFields);
                        }
                    }
                    if (in_array('Order_Total_Amount', $aliases)) {
                        $fieldTxt = $this->getOrderTotalAmountSelectField(true);
                        if ($fieldTxt) {
                            $sqlFields = str_replace("{Order_Total_Amount}", $fieldTxt, $sqlFields);
                        }
                    }


                    $stateIds = $this->extractOrderStateList();

                    $sql .= 'SELECT ' . $sqlFields;
                    SC_Ext::readCustomExportCustomerConfigXML('SQLSelectDataSelect', $sql, $aliases);
                    $sql .= ' FROM `' . _DB_PREFIX_ . 'customer` c ';
                    SC_Ext::readCustomExportCustomerConfigXML('SQLSelectDataLeftJoin', $sql, $aliases);
                    $sql .= ($includeAddresses ? '
                        LEFT JOIN `' . _DB_PREFIX_ . 'address` a ON (c.`id_customer`=a.`id_customer` AND a.`deleted`=0)
                        LEFT JOIN `' . _DB_PREFIX_ . 'country` cn ON a.`id_country`=cn.`id_country`
                        LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (a.`id_country`=cl.`id_country` AND cl.`id_lang`=' . (int)$id_lang . ')
                        LEFT JOIN `' . _DB_PREFIX_ . 'state` st ON a.`id_state`=st.`id_state`
                        ' : '') .
                        ($includeGroups ? '
                        LEFT JOIN `' . _DB_PREFIX_ . 'customer_group` cgr ON c.`id_customer`=cgr.`id_customer`
                        LEFT JOIN `' . _DB_PREFIX_ . 'group` g ON cgr.`id_group`=g.`id_group`
                        LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` gl ON (g.`id_group`=gl.`id_group` AND gl.`id_lang`=' . (int)$id_lang . ')
                        ' : '') .
                        ($includeGroupDefault ? '
                        LEFT JOIN `' . _DB_PREFIX_ . 'group_lang` gl_default ON (c.`id_default_group`=gl_default.`id_group` AND gl_default.`id_lang`=' . (int)$id_lang . ')
                        ' : '') .
                        (($includeOrders || $includeOrderProducts || $includeOrderProductsCategories) ? '
                        INNER JOIN `' . _DB_PREFIX_ . 'orders` ord ON c.`id_customer`=ord.`id_customer`
                        INNER JOIN `' . _DB_PREFIX_ . 'order_history` ord_oh ON (ord.`id_order`=ord_oh.`id_order` ' . ((!empty($stateIds)) ? ' AND ord_oh.id_order_history=(SELECT MAX(oh11.id_order_history) FROM ' . _DB_PREFIX_ . 'order_history oh11 WHERE oh11.id_order=ord_oh.id_order ) ' : '') . ')
                        ' : '') .
                        (($includeOrderProducts || $includeOrderProductsCategories) ? '
                        LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON ord.`id_order`=od.`id_order`
                        ' : '') .
                        ($includeOrderProductsCategories ? '
                        LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON od.`product_id`=cp.`id_product`
                        ' : '')
                        . ' WHERE 1=1 ';

                    if (SCMS && !empty($sql)) {
                        if (!empty($this->id_shop)) {
                            $sql .= ' AND c.id_shop = "' . (int)($this->id_shop) . '" ';
                        } else {
                            $sql .= ' AND c.id_shop IN (' . pSQL(ExportCustomerTools::getAllShopsId()) . ') ';
                        }
                    }
                    if ($includeOrders || $includeOrderProducts || $includeOrderProductsCategories) {
                        if (!empty($stateIds)) {
                            $sql .= ' AND ord_oh.`id_order_state` IN (' . pInSQL($stateIds) . ') ';
                        }
                    }
                    //((!empty($stateIds))?' AND ord_oh.`id_order_state` IN ('.pSQL($stateIds)

                    $sql .= $where;
                }
            }
        }

        // if have to include ps_newsletter, then union with this query
        if ($includeNewsletter) {
            // analyze the dynamic definition if any
            $where = null;
            if ($this->dynamic_definition) {
                // get SQL fields from the export template
                $sqlFields = ExportCustomerFields::getSqlSelectFields('n', $aliases, $id_lang);

                $sqlFields_temp = $sqlFields;
                if (in_array('Order_Number', $aliases)) {
                    $sqlFields_temp = str_replace("{Order_Number},", "", $sqlFields_temp);
                }
                if (in_array('Order_Total_Amount', $aliases)) {
                    $sqlFields_temp = str_replace("{Order_Total_Amount},", "", $sqlFields_temp);
                }

                // build the where clause
                $where = $this->analyzeRules('n', $this->dynamic_definition, $sqlFields_temp, false, true);    // false : no aggregation field in this table !
                if ($where) {
                    if (in_array('Order_Number', $aliases)) {
                        $sqlFields = str_replace("{Order_Number}", '0 AS Order_Number', $sqlFields);
                    }
                    if (in_array('Order_Total_Amount', $aliases)) {
                        $sqlFields = str_replace("{Order_Total_Amount}", '0 AS Order_Total_Amount', $sqlFields);
                    }

                    // prepare UNION if expected
                    if ($includeCustomers) {
                        $sql = '(' . $sql . ') UNION (';
                        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
                            $sql .= 'SELECT ' . $sqlFields . ' FROM `' . _DB_PREFIX_ . 'emailsubscription` n WHERE 1=1' . $where;
                        } else {
                            $sql .= 'SELECT ' . $sqlFields . ' FROM `' . _DB_PREFIX_ . 'newsletter` n WHERE 1=1' . $where;
                        }
                    }

                    if (SCMS && !empty($sql)) {
                        if (!empty($this->id_shop)) {
                            $sql .= ' AND n.id_shop = "' . (int)($this->id_shop) . '" ';
                        } else {
                            $sql .= ' AND n.id_shop IN (' . pSQL(ExportCustomerTools::getAllShopsId()) . ') ';
                        }
                    }
                    if ($includeCustomers) {
                        $sql .= ')';
                    }
                }
            }
        }

//        echo $sql.'<br />';
//        die();
        return $sql;
    }

    /**
     * Get an array of rows as datas retreive from this list
     */
    public function getList($aliases, $id_lang, $limit = null)
    {
        // if we will have to apply some logical operation with another list, then be sure to include the "Id_Customer" key in aliases
        $keyAliasAdded = false;
        if (!in_array('Id_Customer', $aliases)) {
            $aliases[] = 'Id_Customer';
            $keyAliasAdded = true;
        }

        // get from the whole SQL query
        $sql = $this->getSqlQuery($aliases, $id_lang);
        if (!empty($limit) && $limit > 0) {
            $sql .= " LIMIT " . (int)($limit) . " ";
        }
        ExportCustomerTools::addLog('CList run :' . $this->id . ' - get SQL : ' . $sql);

        // if some valid SQL to execute
        if (Tools::strlen($sql) > 0) {
            // get datas
            //echo "<pre>".$sql."</pre>";die();
            $datas = Db::getInstance()->executeS($sql);
            ExportCustomerTools::addLog('CList run :' . $this->id . ' - get RAW DATAS : ' . ExportCustomerTools::captureVarDump($datas));
            if(!$datas)
            {
                ExportCustomerTools::addLog('CList run :'.$this->id.' - get Error : '.Db::getInstance()->getMsgError());
            }

            // apply some other special rules ?
            if ($this->otherListRuleDescriptor != null) {
                ExportCustomerTools::addLog('CList run :' . $this->id . ' - apply other list rule : ' . $this->otherListRuleDescriptor[1] . ' ' . $this->otherListRuleDescriptor[2]);

                // have also to load the other list's datas
                $otherList = new ScCustomerFilter((int)($this->otherListRuleDescriptor[2]));
                if (!Validate::isLoadedObject($otherList)) {
                    ExportCustomerTools::addLog('CList run :' . $this->id . ' - apply other list rule : Unable to load Customer list of ID=' . $this->otherListRuleDescriptor[2]);
                } else {
                    // Load datas from this list
                    $otherDatas = $otherList->getList($aliases, $id_lang);
                    if ($otherDatas == null) {
                        ExportCustomerTools::addLog('CList run :' . $this->id . ' - apply other list rule : No datas loaded from list of ID=' . $this->otherListRuleDescriptor[2]);
                    } else {
                        ExportCustomerTools::addLog('CList run :' . $this->id . ' - apply the other list rule : ' . $this->otherListRuleDescriptor[1] . ' on ' . count($otherDatas) . ' other loaded raw datas');
                        // apply the rule operator : UNION, INTERSECTION, NOT IN with key column to compare lists is : 'Id_Customer'
                        switch ($this->otherListRuleDescriptor[1]) {
                            case 'UNION':
                                foreach ($otherDatas as $otherData) {
                                    $found = false;
                                    foreach ($datas as $data) {
                                        if ($data['Id_Customer'] === $otherData['Id_Customer']) {
                                            $found = true;
                                            break;
                                        }
                                    }
                                    // if not found, then ok to keep the data
                                    if (!$found) {
                                        $datas[] = $otherData;
                                    }
                                }
                                break;
                            case 'INTERSECTION':
                                $finalDatas = array();
                                foreach ($datas as $data) {
                                    $found = false;
                                    foreach ($otherDatas as $otherData) {
                                        if ($data['Id_Customer'] === $otherData['Id_Customer']) {
                                            $found = true;
                                            break;
                                        }
                                    }
                                    // if found, then ok to keep the data
                                    if ($found) {
                                        $finalDatas[] = $data;
                                    }
                                }
                                $datas = $finalDatas;
                                break;
                            case 'NOT IN':
                                $finalDatas = array();
                                foreach ($datas as $data) {
                                    $found = false;
                                    foreach ($otherDatas as $otherData) {
                                        if ($data['Id_Customer'] === $otherData['Id_Customer']) {
                                            $found = true;
                                            break;
                                        }
                                    }
                                    // if not found, then ok to keep the data
                                    if (!$found) {
                                        $finalDatas[] = $data;
                                    }
                                }
                                $datas = $finalDatas;
                                break;
                        }
                        ExportCustomerTools::addLog('CList run :' . $this->id . ' - finally compute RAW DATAS : ' . ExportCustomerTools::captureVarDump($datas));
                    }
                }
            }

            // for some aliases, have to perform some post computations, such as get the last order amount, number of products, ...
            $includeLastOrderDate = in_array('SPECIAL_LAST_ORDER_DATE', $aliases);
            $includeLastOrderAmount = in_array('SPECIAL_LAST_ORDER_AMOUNT', $aliases);
            if ($includeLastOrderDate || $includeLastOrderAmount) {
                // extract the list of all order state ids
                $stateIds = $this->extractOrderStateList();
                if ($stateIds) {
                    // execute the following query for all the customers in the list
                    /*
                        SELECT
                            (
                            SELECT o.`id_order` FROM `ps_orders` o
                            WHERE
                            o.`id_customer`=c.`id_customer` AND
                            (select oh.`id_order_state` from `ps_orders` o1
                                                        left join `ps_order_history` oh on o1.`id_order`=oh.`id_order`
                                                        where o1.`id_order`=o.`id_order`
                                                        order by oh.`date_add` desc limit 0, 1) IN (5,12,2)
                            ORDER BY o.`date_add` DESC limit 0,1
                            ) as order_id,
    
                            (
                            SELECT o.`total_paid_real` FROM `ps_orders` o
                            WHERE
                            o.`id_customer`=c.`id_customer` AND
                            (select oh.`id_order_state` from `ps_orders` o1
                                                        left join `ps_order_history` oh on o1.`id_order`=oh.`id_order`
                                                        where o1.`id_order`=o.`id_order`
                                                        order by oh.`date_add` desc limit 0, 1) IN (5,12,2)
                            ORDER BY o.`date_add` DESC limit 0,1
                            ) as order_amount
    
                        FROM `ps_customer` c
                        WHERE c.`id_customer` IN (1,2,4)
                    */
                    $query = 'SELECT c.`id_customer` as Id_Customer';

                    // last order date ?
                    if ($includeLastOrderDate) {
                        $query .= ' ,
                            (
                            SELECT o.`date_add` FROM `' . _DB_PREFIX_ . 'orders` o
                            WHERE
                            o.`id_customer`=c.`id_customer` AND
                            (select oh.`id_order_state` from `' . _DB_PREFIX_ . 'orders` o1
                                                        left join `' . _DB_PREFIX_ . 'order_history` oh on o1.`id_order`=oh.`id_order`
                                                        where o1.`id_order`=o.`id_order`
                                                        order by oh.`date_add` desc limit 0, 1) IN (' . pInSQL($stateIds) . ')
                            ORDER BY o.`date_add` DESC limit 0,1
                            ) as order_date
                        ';
                    }

                    // last order amount ?
                    if ($includeLastOrderAmount) {
                        $query .= ' ,
                            (
                            SELECT o.`total_paid_real` FROM `' . _DB_PREFIX_ . 'orders` o
                            WHERE
                            o.`id_customer`=c.`id_customer` AND
                            (select oh.`id_order_state` from `' . _DB_PREFIX_ . 'orders` o1
                                                        left join `' . _DB_PREFIX_ . 'order_history` oh on o1.`id_order`=oh.`id_order`
                                                        where o1.`id_order`=o.`id_order`
                                                        order by oh.`date_add` desc limit 0, 1) IN (' . pInSQL($stateIds) . ')
                            ORDER BY o.`date_add` DESC limit 0,1
                            ) as order_amount
                        ';
                    }

                    $query .= 'FROM `' . _DB_PREFIX_ . 'customer` c ';
                    $query .= 'WHERE c.`id_customer` IN (';
                    $first = true;
                    foreach ($datas as $data) {
                        $query .= ($first ? '' : ',') . $data['Id_Customer'];
                        $first = false;
                    }
                    $query .= ')';

                    // execute query
                    ExportCustomerTools::addLog('CList run :' . $this->id . ' - last order query : ' . $query);
                    $lastOrderDatas = Db::getInstance()->executeS($query);
                    ExportCustomerTools::addLog('CList run :' . $this->id . ' - get last order infos : ' . ExportCustomerTools::captureVarDump($lastOrderDatas));

                    // create a useful map to store these datas : key=>array where key is the id_customer and array the associative array of infos anout last order
                    $lastOrderMap = array();
                    foreach ($lastOrderDatas as $lastOrderData) {
                        $info = array();
                        if ($includeLastOrderDate) {
                            $info['SPECIAL_LAST_ORDER_DATE'] = $lastOrderData['order_date'];
                        }
                        if ($includeLastOrderAmount) {
                            $info['SPECIAL_LAST_ORDER_AMOUNT'] = $lastOrderData['order_amount'];
                        }

                        $lastOrderMap[$lastOrderData['Id_Customer']] = $info;
                    }

                    // so append these datas to each row of datas to be exported
                    foreach ($datas as &$data) {
                        if ($includeLastOrderDate) {
                            $data['SPECIAL_LAST_ORDER_DATE'] = $lastOrderMap[$data['Id_Customer']]['SPECIAL_LAST_ORDER_DATE'];
                        }
                        if ($includeLastOrderAmount) {
                            $data['SPECIAL_LAST_ORDER_AMOUNT'] = $lastOrderMap[$data['Id_Customer']]['SPECIAL_LAST_ORDER_AMOUNT'];
                        }
                    }
                } else {
                    ExportCustomerTools::addLog("CList run :" . $this->id . " - can't extract order state for last_order fields with definition " . $this->dynamic_definition);
                }
            }

            // if key has been appended to aliases, then remove it now
            if ($keyAliasAdded) {
                foreach ($datas as &$data) {
                    unset($data['Id_Customer']);
                }
            }
        } else {
            $datas = null;
        }

        return $datas;
    }

    /**
     * Get an array of rows as customer descriptors to be displayed as a list preview
     */
    public function getDisplayCustomerFilter($id_lang, $limit = null)
    {
        // the aliases are the columns to be displayed to user
        if (SCMS) {
            return $this->getList(array('Id_Customer', 'Shop_Id', 'First_name', 'Last_name', 'Email', 'Date_add'), $id_lang, $limit);
        } else {
            return $this->getList(array('Id_Customer', 'First_name', 'Last_name', 'Email', 'Date_add'), $id_lang, $limit);
        }
    }

    /**
     * Export this list
     */
    public function export($id_export_template, $id_lang, $expectedNumberOfCustomers)
    {
        $cookie = Context::getContext()->cookie;

        ExportCustomerTools::addLog('**************** CList export : get customer list for export ****************');

        // load the export template
        $exportTemplate = new ExportCustomerMapping($id_export_template);
        if (!Validate::isLoadedObject($exportTemplate)) {
            return 'Unable to load the export template of id ' . (int)$id_export_template;
        }
        ExportCustomerTools::addLog('CList export : load template ' . (int)$id_export_template . ' (' . $exportTemplate->name . ') to export to ' . $exportTemplate->export_format . ' format');

        ## if field to export need order
        if(strpos($exportTemplate->fields, 'orderFields') !== false)
        {
            ## but nothing set into filter about order
            if(strpos($this->dynamic_definition, 'c_o_') === false)
            {
                throw new \Exception(_l('The template <b>%s</b> contains at least one order field.<br>You must include order states in the <b>%s</b> filter', null, [$exportTemplate->name, $this->name]));
            }
        }

        // get datas (column names are sql alias as defined in ExportCustomerMapping)
        $datas = $this->getList($exportTemplate->getAliases(), $id_lang);

        // some datas ?
        if ($datas && !empty($datas)) {
            // compare to the expected number of customers (size of the list displayed to user before he asked for export)
            if (count($datas) != $expectedNumberOfCustomers) {
                // TODO : log more informations to be able to understand why this difference occurs
                ExportCustomerTools::addLog('WARNING - number of customers listed (' . $expectedNumberOfCustomers . ') don\'t match the number of customers exported (' . count($datas) . ')');
            }

            // load export format properties
            $props = self::explodeKeyValue($exportTemplate->format_properties, '@@', '::');
            // change some props
            if (array_key_exists('delimitor', $props) && $props['delimitor'] == 'tab') {
                $props['delimitor'] = "\t";    // use "\t" and not '\t' or tabulation will be reduced to characters \t
                ExportCustomerTools::addLog('CList export : replace TAB delimitor');
            }

            $sc_id_lang = ExportCustomer::getRightIdLang($id_lang);

            // CSV format and something to export
            if ($exportTemplate->export_format == 'CSV' && count($datas) > 0) {
                // build output buffer
                $buffer = '';
                // header row if any data and template has the options set
                if (count($datas) > 0 && (int)($props['display_header']) == 1) {
                    $i = 0;
                    foreach ($datas[0] as $key => $value) {
                        if ($i > 0) {
                            $buffer .= $props['delimitor'];
                        }
                        // look for translation of this key
                        $buffer .= ExportCustomerFields::getFieldsTranslation($key, $sc_id_lang);
                        $i++;
                    }
                    $buffer .= "\n";
                    ExportCustomerTools::addLog('CList export : display header ' . $buffer);
                }
                // export datas
                foreach ($datas as $data) {
                    $i = 0;
                    foreach ($data as $key => $value) {
                        if ($i > 0) {
                            $buffer .= $props['delimitor'];
                        }
                        // deal with default value

                        if ($exportTemplate->separator == 2 && is_numeric($value)) {
                            $temp_value = str_replace(".", ",", $value);
                        } else {
                            $temp_value = $value;
                        }

                        $buffer .= $temp_value != '' ? ExportCustomerTools::prepareValueForCsv($temp_value) : ExportCustomerFields::getDefaultValues($key, $sc_id_lang);
                        $i++;
                    }
                    $buffer .= "\n";
                }

                //echo "<pre>".$buffer."</pre>";die();
                ExportCustomerTools::addLog('CList export : finally get a buffer of ' . Tools::strlen($buffer) . ' chars');
                return $buffer;
            }
        }

        return '';
    }
    /*	public function delete()
    {
        echo '**************** DELETE object<br />';
    }
    public function update()
    {
        echo '**************** UPDATE object<br />';
    }
    public function add()
    {
        echo '**************** ADD object<br />';
    }*/
    /**
     * Tranform a list like
     *        key(1)del2val(1)del1...del1key(n)del2val(n)
     * into a map
     *        [key(1)=>val(1), ..., key(n)=>val(n)]
     */
    public static function explodeKeyValue($value, $del1, $del2)
    {
        $back = array();
        // delimitor::|@@encoding::UTF-8 becomes ['delimitor::|', 'encoding::UTF-8']
        $array1 = explode($del1, $value);
        foreach ($array1 as $val) {
            // delimitor::| becomes ['delimitor'=>'|']
            $array2 = explode($del2, $val);
            $back[$array2[0]] = $array2[1];
        }
        return $back;
    }

    /**
     * Analyze input rules to produce a sql where clause (added with an aggregation clause and a having).
     * Filter rules by the input family.
     *
     * @param family : could be either c or n
     * @param rules : whole rule string get from DB
     * @param sqlFields : sqlFields to be included
     * @param aggregation : have to apply or not an aggregation on sql fields
     * @param isNewsletter : to avoid aggregations for newsletter
     */
    private function analyzeRules($family, $rules, $sqlFields = null, $aggregation = false, $isNewsletter = false)
    {
        $where = '';
        ExportCustomerTools::addLog('CList run :' . $this->id . ' Analyze rules for ' . $family . ' : ' . $rules);
        /*
        each rule is separated from each other by this pattern : __operator__
                where operator is the SQL link between the rules : AND, OR
                might find also some options (common or rule specific)
    
        Known rules (prefixes are : c=customers, a=address, g=group, o=orders, n=front-office newsletter) :
            c_all=>NONE|1							// include all customers
            c_news=>NONE|=|1						// filter on customers ok for newsletter
            c_gender=>NONE|=|1						// filter on customers with gender=1 (value get in db when user defined the criteria)
            c_first_name=>NONE|LIKE|John			// filter on customers with first_name like '%DOE%'
            c_last_name=>NONE|LIKE|DOE				// filter on customers with last_name like '%DOE%'
            c_birthday_date=>NONE|1996-04-21|<=		// filter on customers with birthday_date>=1996-04-21
            c_birthday_date=>NONE|1996-04-21[<=|>|1975-01-01		// filter on customers with 1996-04-21<=birthday_date<2000-01-01
            c_email=>NONE|ENDS|free.fr				// filter on customers with email ending by free.fr
            c_date_add=>NONE|2011-04-21|<=			// filter on customers added from 2011-04-21
    
            c_a_company=>NONE|LIKE|Alaloop			// filter on customers with an address at company like '%Alaloop%'
            c_a_address=>NONE|LIKE|rue				// filter on customers with an address containing "rue" either in one of the 3 address fields
    
            c_o_order_state 						// filter on customers with orders in specific states
            c_o_order_date 							// filter on customers with orders applying a date clause
            c_o_order_agg_sum						// filter on customers with a number of orders orders
            c_o_order_agg_nb 						// filter on customers with a total sum of orders
            c_o_p_order_product						// filter on customers with orders containing product list
            c_o_p_cat_order_pdt_category				// filter on customers with orders containing product category list
    
            n_all=>NONE|1							// include all subscribers from front-office newsletter block
            n_email=>NONE|ENDS|free.fr				// filter subscribers from front-office newsletter block with email ending by free.fr
            n_date_add=>NONE|2011-04-21|<=			// filter subscribers from front-office newsletter block added from 2011-04-21
    
            g_group=>NONE|gp1,gp2,...				// filter on customers that belong to these groups
    
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
                REGEXP	- not implemented
                =
        */

        $orderNumberRuleDescriptor = null;
        $orderTotalAmountRuleDescriptor = null;

        // explode the rules
        $parts = explode('__', $rules);

        // TODO : deal with OR operators - have to include () before and after logical parts
        // Example :
        // 		AND 1==1 OR c.`lastname` LIKE '%DOE%'
        // must become
        // 		AND (1==1 OR c.`lastname` LIKE '%DOE%')
        // OR has an aggregative behaviour

        // load all rules in a array with informations : type (rule or operator), operator, rule_name, rule_descriptor
        // Useful because some rules must be treated grouped like order rules : c_o_order_number & c_o_order_sum for example
        $ruleDescriptors = array();
        foreach ($parts as $rule) {
            // is this an operator ?
            if ($rule == 'AND' || $rule == 'OR') {
                $ruleDescriptors[] = array('type' => 'operator', 'operator' => $rule);
            } else    // or a rule ?
            {
                $subparts = explode('=>', $rule);
                $ruleName = $subparts[0];
                $ruleDescriptors[] = array('type' => 'rule', 'name' => $ruleName, 'descriptor' => $subparts[1]);
            }
        }
        ExportCustomerTools::addLog('CList run :' . $this->id . ' - rule descriptors : ' . ExportCustomerTools::captureVarDump($ruleDescriptors));

        // loop through all rules
        $lastInterRuleOperator = ' AND ';
        //		$havingOperator = null;
        //		$having = '';
        foreach ($ruleDescriptors as $rd) {
            // is this an operator ?
            if ($rd['type'] == 'operator') {
                // if an having clause is already begun, then have also to store this operator as a possible inter having operator
                //				if (Tools::strlen($having)>0)
                //					$havingOperator = ' '.$rd['operator'].' ';

                $lastInterRuleOperator = ' ' . $rd['operator'] . ' ';
            } else {
                // filter the rule by the family (check the rule name prefix)
                if (strpos($rd['name'], $family . '_') === false) {
                    continue;
                }

                ExportCustomerTools::addLog('CList run :' . $this->id . ' - Family ' . $family . ' / rule ' . $rd['name'] . ' : ' . $rd['descriptor']);

                // get the rule's name & descriptor
                $ruleDescriptor = explode('|', $rd['descriptor']);

                $sqlClause = '';
                switch ($rd['name']) {
                    // *************************************************************************
                    //								CUSTOMER RULES
                    //
                    case 'c_all':
                        // c_all=>NONE|1
                        $sqlClause = '1=1';
                        break;
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
                    case 'c_news':
                        // c_news=>NONE|=|1
                        $sqlClause = $this->getSqlRule('c.`newsletter`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_lang':
                        // c_lang=>NONE|1,2,3
                        $sqlClause = $this->getSqlGroupRule('c.`id_lang`', $ruleDescriptor[1]);
                        break;
                    case 'c_optin':
                        // c_optin=>NONE|=|1
                        $sqlClause = $this->getSqlRule('c.`optin`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_website':
                        // c_website=>NONE|LIKE|www.website.com
                        $sqlClause = $this->getSqlRule('c.`website`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_note':
                        // c_note=>NONE|LIKE|note
                        $sqlClause = $this->getSqlRule('c.`note`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_active':
                        // c_active=>NONE|=|1
                        $sqlClause = $this->getSqlRule('c.`active`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_deleted':
                        // c_deleted=>NONE|=|1
                        $sqlClause = $this->getSqlRule('c.`deleted`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_date_add':
                        // c_date_add=>NONE|2011-04-21|<=
                        // c_date_add=>NONE|2010-04-21[<=|<|2011-01-01
                        // c_date_add=>NONE|30|DAY
                        $sqlClause = $this->getSqlDateRule('c.`date_add`', $ruleDescriptor);
                        break;

                    // *************************************************************************
                    //								CUSTOMER ADDRESS RULES
                    //
                    case 'c_a_company':
                        // c_a_company=>NONE|LIKE|Alaloop
                        $sqlClause = $this->getSqlRule('a.`company`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_a_first_name':
                        // c_a_first_name=>NONE|LIKE|DOE
                        $sqlClause = $this->getSqlRule('a.`firstname`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_a_last_name':
                        // c_a_last_name=>NONE|LIKE|DOE
                        $sqlClause = $this->getSqlRule('a.`lastname`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_a_address':
                        // filter on either address1, address2 or other
                        $sqlClause = $this->getSqlRule(array('a.`address1`', 'a.`address2`', 'a.`other`'), $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_a_postcode':
                        // c_a_postcode=>NONE|BEGINS|64
                        $sqlClause = $this->getSqlRule('a.`postcode`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_a_city':
                        // c_a_city=>NONE|EQUAL|Bayonne
                        $sqlClause = $this->getSqlRule('a.`city`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_a_state':
                        // c_a_state=>NONE|EQUAL|MA
                        $sqlClause = $this->getSqlRule('st.`name`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_a_country':
                        // c_a_country=>NONE|EQUAL|FRANCE
                        $sqlClause = $this->getSqlRule('cl.`name`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_a_phone':
                        // c_a_phone=>NONE|BEGIN|06
                        $sqlClause = $this->getSqlRule('a.`phone`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;
                    case 'c_a_mobilephone':
                        // c_a_mobilephone=>NONE|BEGIN|06
                        $sqlClause = $this->getSqlRule('a.`phone_mobile`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;

                    // *************************************************************************
                    //								CUSTOMER ORDERS RULES
                    //
                    /*
                        Purposes : be able to filter customers ...
                            > ... that have ordered 3 times in last one year - orders of specific states
                            > ... that have an order in a specific state (wait for check for example) in the last 6 month
    
                        So, have to set for a CustomerFilter which order states are to be considered
    
                        Order rule is made of :
                            > states order to consider
                            > number of orders
                            > total amount of orders
                            > date constraint
                        Order rules might be :
                            c_o_order_stats=>state1,state2,...|order_number|value1|operator1|operator2|value2|inter_rule_operator|order_sum|value1|operator1|operator2|value2|order_date|date_descriptor
                            c_o_order_rule_date=>state1,state2,...|value1|operator1|operator2|value2
    
    
                        c_o_order_state --> mandatory if any order rule
                        c_o_order_date --> normal date rule
                        c_o_p_order_product --> filter on orders products
                        c_o_p_cat_order_pdt_category --> filter on orders products categories
                        c_o_order_agg_sum --> implies row aggregation (on $sqlFields) so must be executed at last --> increase an having clause
                        c_o_order_agg_nb --> implies row aggregation (on $sqlFields) so must be executed at last --> increase an having clause
    
                    */
                    // Order rules
                    case 'c_o_order_state':
                        $sqlClause = $this->getStatOrderRules($rd['name'], $ruleDescriptor);
                        break;
                    /*					case 'c_o_order_agg_sum':
                                    case 'c_o_order_agg_nb':
                                        // if an having operator is defined then apply it
                                        if ($havingOperator!=null)
                                            $having .= $havingOperator;
                                        // complete the having clause
                                        $having .= $this->getStatOrderRules($rd['name'], $ruleDescriptor);
                                        // will have to aggregate the query to produce stat values
                                        $aggregation = true;
                                        // don't append anything now to the where clause
                                        continue 2;*/
                    case 'c_o_order_agg_sum':
                        $orderTotalAmountRuleDescriptor = $ruleDescriptor;
                        break;
                    case 'c_o_order_agg_nb':
                        $orderNumberRuleDescriptor = $ruleDescriptor;
                        break;
                    case 'c_o_order_without':
                        if (!empty($ruleDescriptor[2])) {
                            $sqlClause = $this->getSqlRule('c.`id_customer` NOT IN (SELECT id_customer FROM ' . _DB_PREFIX_ . 'orders)', '', null);
                        }
                        break;
                    case 'c_o_order_date':
                        //						$sqlClause = $this->getSqlDateRule('o.`date_upd`', $ruleDescriptor);
                        $sqlClause = $this->getSqlDateRule('ord.`date_add`', $ruleDescriptor);
                        break;
                    case 'c_o_p_order_product':
                        // c_o_p_order_product=>NONE|7,8,10,11
                        $sqlClause = $this->getStatOrderRules($rd['name'], $ruleDescriptor);
                        break;
                    case 'c_o_p_cat_order_pdt_category':
                        // c_o_p_cat_order_pdt_category=>NONE|7,8,10,11
                        $sqlClause = $this->getStatOrderRules($rd['name'], $ruleDescriptor);
                        break;

                    // *************************************************************************
                    //								CUSTOMER GROUP RULES
                    //
                    case 'c_g_group':
                        // c_g_group=>NONE|gp1,gp2,...
                        $sqlClause = $this->getSqlGroupRule('gl.`id_group`', $ruleDescriptor[1]);
                        break;

                    // *************************************************************************
                    //								CUSTOMER OTHER LISTS RULES
                    //
                    case 'c_logic_list':
                        // c_logic_list=>NONE|UNION|2
                        // store this rule descriptor to be used in getList() function
                        $this->otherListRuleDescriptor = $ruleDescriptor;
                        $sqlClause = '1=1';    // dummy SQL
                        break;

                    // *************************************************************************
                    //								NEWSLETTER RULES
                    //
                    case 'n_all':
                        // the n_all rule can only be taken into account if the ps_newsletter table exists
                        if (isTable('newsletter')) {
                            $sqlClause = '1=1';
                        } else {
                            if (isTable('emailsubscription')) {
                                $sqlClause = '1=1';
                            }
                        }
                        break;

                    case 'n_email':
                        // n_email=>NONE|ENDS|free.fr
                        $sqlClause = $this->getSqlRule('n.`email`', $ruleDescriptor[1], $ruleDescriptor[2]);
                        break;

                    case 'n_date_add':
                        // n_date_add=>NONE|2011-04-21|<=
                        // n_date_add=>NONE|2010-04-21[<=|<|2011-01-01
                        // n_date_add=>NONE|30|DAY
                        $sqlClause = $this->getSqlDateRule('n.`newsletter_date_add`', $ruleDescriptor);
                        break;
                }

                // append this clause only if something to append !
                if (Tools::strlen($sqlClause) > 0) {
                    // deal with main rule operator (NONE or NOT)
                    if ($ruleDescriptor[0] == 'NOT') {
                        $sqlClause = 'NOT(' . $sqlClause . ')';
                    }
                    // append the rule as sql clause
                    $where .= $lastInterRuleOperator . $sqlClause;
                }
            }
        }

        // add in the where clause a filter on the number of orders
        if ($orderNumberRuleDescriptor) {
            // the field name (it's a complex text)
            $fieldName = $this->getOrderNumberSelectField(false);
            if ($fieldName) {
                // build the clause using the descriptor
                $txt = '(' . $fieldName . $orderNumberRuleDescriptor[2] . $orderNumberRuleDescriptor[1];
                if ($orderNumberRuleDescriptor[3] != '') {
                    $txt .= ' AND ' . $fieldName . $orderNumberRuleDescriptor[3] . $orderNumberRuleDescriptor[4];
                }
                $txt .= ')';
                $txt = $orderNumberRuleDescriptor[0] == 'NOT' ? 'NOT(' . $txt . ')' : $txt;

                // add to where clause
                $where .= (Tools::strlen($where) > 0 ? ' AND ' : '') . $txt;
            }
        }
        // add in the where clause a filter on the totl amount of orders
        if ($orderTotalAmountRuleDescriptor) {
            // the field name (it's a complex text)
            $fieldName = $this->getOrderTotalAmountSelectField(false);
            if ($fieldName) {
                // build the clause using the descriptor
                $txt = '(' . $fieldName . $orderTotalAmountRuleDescriptor[2] . $orderTotalAmountRuleDescriptor[1];
                if ($orderTotalAmountRuleDescriptor[3] != '') {
                    $txt .= ' AND ' . $fieldName . $orderTotalAmountRuleDescriptor[3] . $orderTotalAmountRuleDescriptor[4];
                }
                $txt .= ')';
                $txt = $orderTotalAmountRuleDescriptor[0] == 'NOT' ? 'NOT(' . $txt . ')' : $txt;

                // add to where clause
                $where .= (Tools::strlen($where) > 0 ? ' AND ' : '') . $txt;
            }
        }

        // deal with the $sqlFields to build the group by clause
        if (!$isNewsletter) {
            // SQL fields :
            //	c.`id_customer` As Id_Customer,c.`firstname` As First_name,c.`lastname` As Last_name,c.`email` As Email,c.`date_add` As Date_add
            // must become :
            // c.`id_customer`, c.`firstname`, c.`lastname`, c.`email`, c.`date_add`
            // delete the gender field if any (no need to aggregate on it)
            /*$groupClause = preg_replace('#CASE.*Gender,#', '', $sqlFields);
    
            $exp1 = explode(",(SELECT GROUP_CONCAT( gl2.name",$groupClause);
            if(!empty($exp1[1]))
            {
                $exp2 = explode("As CustomerGroup,",$exp1[1]);
                $groupClause = $exp1[0].",".$exp2[1];
            }
            $exp1 = explode(",(SELECT GROUP_CONCAT( cg2.id_group",$groupClause);
            if(!empty($exp1[1]))
            {
                $exp2 = explode("As CustomerIdGroup,",$exp1[1]);
                $groupClause = $exp1[0].",".$exp2[1];
            }
    
            // delete all the texts between As and next , (see the ? operator that makes the * ungreedy)
            $groupClause = preg_replace('# As .*?,#i', ',', $groupClause);
            // delete the last text after the last As ...
            $groupClause = preg_replace('# As .*$#i', '', $groupClause);
            // last check, remove all aggregation fields from this group by clause !
            $groupClause = ExportCustomerFields::removeAggregationFields($groupClause);
            ExportCustomerTools::addLog('CList run :'.$this->id.' - Perform aggregation : SQL FIELDS : '.$sqlFields.' BECOME '.$groupClause);
    //			$where .= ' group by '.$groupClause .($having!=''?' having '.$having:'');
            $groupClause = str_replace("0,","",$groupClause);
            $where .= ' group by '.$groupClause;*/
            $where .= ' GROUP BY c.`id_customer`';
            if (!empty($this->contain_address)) {
                $where .= ' ,a.`id_address`';
            }
        }

        // return null or a valid where clause
        return $where == '' ? null : $where;
    }

    /**
     * Get a rule by its name if exists
     */
    public static function getRuleByName($name, $ruleDescriptors)
    {
        foreach ($ruleDescriptors as $ruleDescriptor) {
            if (($ruleDescriptor['type'] == 'rule' || $ruleDescriptor['type'] == 'options') && $ruleDescriptor['name'] == $name) {
                return $ruleDescriptor['descriptor'];
            }
        }
        return null;
    }

    /**
     * Translate a rule descriptor in SQL
     * $fields : could be either a single field (string) or many one (array of string)
     */
    private function getSqlRule($fields, $valueOperator1, $value1, $valueOperator2 = null, $value2 = null)
    {
        // special operator1 (could not be in operator2)
        if (in_array($valueOperator1, array('like', 'begin', 'end', 'equal', 'in'))) {
            // string comparison are all case insensitive
            $value1 = Tools::strtolower($value1);
            if (is_array($fields)) {
                for ($i = 0; $i < count($fields); $i++) {
                    $fields[$i] = 'LOWER(' . $fields[$i] . ')';
                }
            } else {
                $fields = 'LOWER(' . $fields . ')';
            }
        }

        switch ($valueOperator1) {
            case 'like':
                $value1 = ' "%' . Tools::strtolower($value1) . '%"';
                break;
            case 'begin':
                $valueOperator1 = 'like';
                $value1 = ' "' . Tools::strtolower($value1) . '%"';
                break;
            case 'end':
                $valueOperator1 = 'like';
                $value1 = ' "%' . Tools::strtolower($value1) . '"';
                break;
            case 'equal':
                $valueOperator1 = '=';
                $value1 = ' "' . Tools::strtolower($value1) . '"';
                break;
            default:
        }

        // always quote the \ and the ' characters
        $value1 = str_replace("\\", "\\\\", $value1);
        $value1 = str_replace("'", "\\'", $value1);
        if ($value2) {
            $value2 = str_replace("\\", "\\\\", $value2);
            $value2 = str_replace("'", "\\'", $value2);
        }

        if ($valueOperator1 == 'in') {
            $valueOperator1 = 'in';
            $data = explode(',', $value1);
            $value1 = " ('";
            $value1 .= implode("','", $data);
            $value1 .= "')";
        }

        // build clause
        if (is_array($fields)) {
            if (!$valueOperator2) {
                $sqlClause = "(";
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    if ($i > 0) {
                        $sqlClause .= " OR ";
                    }
                    $sqlClause .= $field . ' ' . $valueOperator1 . $value1;
                }
                $sqlClause .= ")";
            } else {
                $sqlClause = "(";
                for ($i = 0; $i < count($fields); $i++) {
                    $field = $fields[$i];
                    if ($i > 0) {
                        $sqlClause .= " OR ";
                    }
                    $sqlClause .= '(' . $field . ' ' . $valueOperator1 . $value1 . " AND " . $field . ' ' . $valueOperator2 . ' ' . "'" . $value2 . "')";
                }
                $sqlClause .= ")";
            }
        } else {
            if (!$valueOperator2) {
                $sqlClause = $fields . ' ' . $valueOperator1 . $value1;
            } else {
                $sqlClause = '(' . $fields . ' ' . $valueOperator1 . $value1 . " AND " . $fields . ' ' . $valueOperator2 . ' ' . "'" . $value2 . "')";
            }

        }
        return $sqlClause;
    }

    /**
     * Translate a date rule descriptor in SQL
     */
    private function getSqlDateRule($field, $ruleDescriptor)
    {
        $sqlClause = '';

        // is this a range rule or a last rule ?
        $rangeRule = count($ruleDescriptor) >= 5;

        $monthchoice = count($ruleDescriptor) == 3;

        if ($monthchoice) {
            $sqlClause = 'MONTH(c.`birthday`) ' . $ruleDescriptor[1] . ' ' . (int)$ruleDescriptor[2];
        } elseif ($rangeRule) {
            // rule_name|date1|operator1|operator2|date2
            // 0        |1    |2        |3        |4
            // DATE(a.date_upd)>=STR_TO_DATE('2011-03-12','%Y-%m-%d')
            if ($ruleDescriptor[3] != '') {
                $sqlClause = '(' .
                    "DATE(" . $field . ")" . $ruleDescriptor[2] . "STR_TO_DATE('" . $ruleDescriptor[1] . "','%Y-%m-%d')" .
                    ' AND ' .
                    "DATE(" . $field . ")" . $ruleDescriptor[3] . "STR_TO_DATE('" . $ruleDescriptor[4] . "','%Y-%m-%d')" .
                    ')';
            } else {
                $sqlClause = "DATE(" . $field . ")" . $ruleDescriptor[2] . "STR_TO_DATE('" . $ruleDescriptor[1] . "','%Y-%m-%d')";
            }
        } else {
            // rule_name|operator|N|unit
            // 0        |1	     |2|3
            // DATE(o.date_upd)>=DATE_SUB(CURDATE(),INTERVAL 6 MONTH)
            $sqlClause = "DATE(" . $field . ")" . $ruleDescriptor[1] . "DATE_SUB(CURDATE(),INTERVAL " . $ruleDescriptor[2] . " " . $ruleDescriptor[3] . ")";
        }

        return $sqlClause;
    }

    /**
     * Translate a group rule descriptor in SQL
     */
    private function getSqlGroupRule($field, $groupList)
    {
        $sql = '';
        $groups = explode(',', $groupList);
        if (count($groups) > 0) {
            $sql .= $field . 'IN (';
            for ($i = 0; $i < count($groups); $i++) {
                $group = $groups[$i];
                if ($i > 0) {
                    $sql .= ',';
                }
                $sql .= $group;
            }
            $sql .= ')';
        }

        return $sql;
    }

    /**
     * Translate an order Stat rule descriptor in SQL
     *
     * Descriptors might be :
     * c_o_order_state                        // filter on customers with orders in specific states
     * c_o_order_agg_sum                        // filter on customers with a number of orders orders
     * c_o_order_agg_nb                        // filter on customers with a total sum of orders
     *
     * c_o_order_state=>NONE|state1,state2
     * 0   |1
     * c_o_p_order_product=>NONE|product_id1,product_id2,...
     * 0   |1
     * c_o_p_cat_order_pdt_category=>NONE|category_id1,category_id2,...
     * 0   |1
     *
     * c_o_order_agg_sum=>NONE|value1|operator1|operator2|value2
     * 0   |1     |2        |3          |4
     * c_o_order_agg_nb=>NONE|value1|operator1|operator2|value2
     * 0      |1     |2        |3        |4
     */
    private function getStatOrderRules($ruleName, $ruleDescriptor)
    {
        $sql = '';

        /*
            SELECT o.id_order, count(*), sum(`total_paid_real`), c.`id_customer` As Id_Customer,c.`firstname` As First_name,c.`lastname` As Last_name,c.`email` As Email,c.`date_add` As Date_add FROM `ps_customer` c INNER JOIN `ps_orders` o on c.id_customer=o.id_customer
            WHERE 1=1 AND 1=1 AND
                    (select oh.id_order_state from ps_orders o1
                        left join ps_order_history oh on o1.id_order=oh.id_order
                    where o1.id_order=o.id_order
                    order by oh.date_add desc limit 0, 1) IN (4,5) AND
                    DATE(o.`date_upd`)>=DATE_SUB(CURDATE(),INTERVAL 100 DAY)
            group by c.id_customer, c.`firstname`, c.`lastname`, c.`email`, c.`date_add`
            having count(*)>=1 and sum(`total_paid_real`)>10
        */

        if ($ruleName == 'c_o_order_state') {
            $this->_orderStateSql = '(select oh.`id_order_state` from `' . _DB_PREFIX_ . 'orders` o1
                            left join `' . _DB_PREFIX_ . 'order_history` oh on o1.`id_order`=oh.`id_order`
                            where o1.`id_order`=o.`id_order`
                            order by oh.`date_add` desc limit 0, 1) IN (' . $ruleDescriptor[1] . ')';
            // list order states to consider
            //$sql .= $this->_orderStateSql;
            return $sql;
        } else {
            if ($ruleName == 'c_o_p_order_product') {
                // filter on orders products
                $sqlClause = 'od.`product_id` IN (' . $ruleDescriptor[1] . ')';
                return $sqlClause;
            } else {
                if ($ruleName == 'c_o_p_cat_order_pdt_category') {
                    // filter on orders products categories
                    $sqlClause = 'cp.`id_category` IN (' . $ruleDescriptor[1] . ')';
                    return $sqlClause;
                } else {
                    if ($ruleName == 'c_o_order_agg_nb') {
                        $fieldName = 'count(*)';
                    } else {
                        if ($ruleName == 'c_o_order_agg_sum') {
                            $fieldName = 'sum(`total_paid_real`)';
                        }
                    }

                    $having = '(' . $fieldName . $ruleDescriptor[2] . $ruleDescriptor[1];
                    if ($ruleDescriptor[3] != '') {
                        $having .= ' AND ' . $fieldName . $ruleDescriptor[3] . $ruleDescriptor[4];
                    }
                    $having .= ')';
                    return $ruleDescriptor[0] == 'NOT' ? 'NOT(' . $having . ')' : $having;
                }
            }
        }
    }

    /**
     * Extract the comma separated list of order state ids in this list definition
     */
    private function extractOrderStateList()
    {
        // explode the rules
        $parts = explode('__', $this->dynamic_definition);
        $ruleDescriptors = array();
        foreach ($parts as $rule) {
            // is this an operator ?
            if ($rule == 'AND' || $rule == 'OR') {
                continue;
            } else    // or a rule ?
            {
                $subparts = explode('=>', $rule);
                $ruleName = $subparts[0];
                if ($ruleName != 'c_o_order_state') {
                    continue;
                }

                // here the rule is the one we are looking for
                // return the state id comma separated list
                // Descriptor could be like : NONE|5,1,2
                $descriptorParts = explode('|', $subparts[1]);
                return $descriptorParts[1];
            }
        }

        return false;
    }

    /**
     * Build the number of orders field (see sql below)
     */
    private function getOrderNumberSelectField($includeAlias)
    {
        /*
        (SELECT count(*) FROM `ps_customer` c1
            INNER JOIN `ps_orders` o ON c1.`id_customer`=o.`id_customer`
            WHERE
            c1.`id_customer`=c.`id_customer` AND
             1=1  AND (select oh.`id_order_state` from `ps_orders` o1
                                    left join `ps_order_history` oh on o1.`id_order`=oh.`id_order`
                                    where o1.`id_order`=o.`id_order`
                                    order by oh.`date_add` desc limit 0, 1) IN (3,17,4,5)) as Order_Number
                */
        $exp_dynamic = explode('__', $this->dynamic_definition);
        $where_sql = array_map(function ($row) {
            $exp_row = explode('=>', $row);
            $ruleD = explode('|', $row);
            if ($exp_row[0] == 'c_o_order_date') {
                return $this->getSqlDateRule('o.`date_add`', $ruleD);
            } elseif ($exp_row[0] == 'c_o_order_state') {
                return $this->getStatOrderRules('c_o_order_state', $ruleD);
            }
        }, $exp_dynamic);
        $where_sql = array_filter($where_sql);
        if (!empty($where_sql)) {
            $where_sql = 'AND ' . implode('AND ', $where_sql);
        } else {
            $where_sql = '';
        }
        if ($this->_orderStateSql) {
            return '(SELECT count(*) FROM `' . _DB_PREFIX_ . 'customer` c1
                INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON c1.`id_customer`=o.`id_customer`
                WHERE
                c1.`id_customer`=c.`id_customer` AND ' . $this->_orderStateSql . $where_sql . ')' . ($includeAlias ? ' as Order_Number' : '');
        } else {
            ExportCustomerTools::addLog('WARNING - number of orders field cannot be created because no order state filter was defined');
            return null;
        }
    }

    /**
     * Build the total amount of orders field (see sql below)
     */
    private function getOrderTotalAmountSelectField($includeAlias)
    {
        /*
        (SELECT sum(`total_paid_real`) FROM `ps_customer` c1
            INNER JOIN `ps_orders` o ON c1.`id_customer`=o.`id_customer`
            WHERE
            c1.`id_customer`=c.`id_customer` AND
             1=1  AND (select oh.`id_order_state` from `ps_orders` o1
                                    left join `ps_order_history` oh on o1.`id_order`=oh.`id_order`
                                    where o1.`id_order`=o.`id_order`
                                    order by oh.`date_add` desc limit 0, 1) IN (3,17,4,5)) as Order_Total_Amount
        */
        if ($this->_orderStateSql) {
            return '(SELECT sum(`total_paid_real`) FROM `' . _DB_PREFIX_ . 'customer` c1
                INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON c1.`id_customer`=o.`id_customer`
                WHERE
                c1.`id_customer`=c.`id_customer` AND ' . $this->_orderStateSql . ')' . ($includeAlias ? ' as Order_Total_Amount' : '');
        } else {
            ExportCustomerTools::addLog('WARNING - total amount of orders field cannot be created because no order state filter was defined');
            return null;
        }
    }

    public function getShopName($id)
    {
        $return = "";
        if (!empty($id)) {
            $shop = Shop::getShop($id);
            $return = $shop["name"];
        }
        return $return;
    }

}
