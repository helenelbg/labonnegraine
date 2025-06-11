<?php

class ExportCustomerFields
{
    /**
     * Manage list af available field aliases for customer tables / newsletter table
     * Store these aliases into DB and use it in the admin form
     *
     * From export, give the list of field aliases to export and get the true field list for customers / newsletter.
     * Useful to deal with default values if fields not available.
     *
     * Can also be used to display result list in admin form (call function with a system field aliases list)
     *
     * function getCustomerSqlField($aliases)
     * {
     * ...
     * }
     * function getNewsletterSqlField($aliases)
     * {
     * if (not $alias in getCustomerAvailableSqlFields) then
     * append ("'default_value' as alias")
     * }
     */

    /**
     * Return the SQL select clause for the input aliases
     * @param : family = could be either 'c' for customers or 'n' for newsletters subscriptions
     * @param : aliases = array of string
     * @param : id_lang
     */
    public static function getSqlSelectFields($family, $aliases, $id_lang)
    {
        $sql = '';

        // some fields should be replaced by some more complex expression to produce a human readable value
        // Example : id_gender whould be replaced by H F H/F (it depends on active language)
        /*
            CASE c.`id_gender`
                WHEN "1" THEN "M"
                WHEN "2" THEN "F"
                ELSE "M/F" END
        */
        // WARNING !!! Keep each expression on a single line (important for the replacement functions in aggregation group by clause preparation)
        /*$sqlExpression = array(
                                'c.`id_gender`'=>array(	1=>'CASE c.`id_gender` WHEN "1" THEN "M" WHEN "2" THEN "F" ELSE "M/F" END',
                                                        2=>'CASE c.`id_gender` WHEN "1" THEN "H" WHEN "2" THEN "F" ELSE "H/F" END')
                                );*/

        $languages = Language::getLanguages(true, false);
        $sqlExpression = array();
        foreach ($languages as $lang) {
            $id_language = (int)$lang['id_lang'];
            $sqlExpression['c.`id_gender`'][$id_language] = '(SELECT gdl.name FROM ' . _DB_PREFIX_ . 'gender_lang gdl WHERE gdl.id_lang = ' . (int)$id_language . ' AND c.`id_gender` = gdl.`id_gender`)';
        }

        // if any field to list
        if (count($aliases) > 0) {
            foreach ($aliases as $alias) {
                // get the sql field
                $sqlField = self::getField($family, $alias, $id_lang);
                if ($sqlField == 'SKIP') {
                    if (!empty($sql)) {
                        $sql .= ',';
                    }
                    $sql .= '{' . $alias . '}' . "\n";
                    continue;
                }
                // get sql field expression if some replacement is defined
                if (array_key_exists($sqlField, $sqlExpression)) {
                    if (isset($sqlExpression[$sqlField][$id_lang])) {
                        $fieldExpression = $sqlExpression[$sqlField][$id_lang];
                    } else {
                        $fieldExpression = $sqlExpression[$sqlField][1];
                    }    // use english as default
                } else {
                    $fieldExpression = $sqlField;
                }
                // skip special fields
                if (Tools::substr($fieldExpression, 0, 7) === 'SPECIAL') {
                    if (!empty($sql)) {
                        $sql .= ',';
                    }
                    $sql .= '"" As ' . $alias . "\n";
                    continue;
                }

                // then build sql for this field
                if (!empty($sql)) {
                    $sql .= ',';
                }
                $sql .= $fieldExpression . ' As ' . $alias . "\n";
            }
            // remove trailing comma
            $sql = Tools::substr($sql, 0, Tools::strlen($sql) - 1);
        }

        return $sql;
    }

    /**
     * Get the true field from customer tables to include in a select clause to get the input alias
     */
    public static function getSqlAliases($id_lang)
    {
        // Each row :
        // 'Alias'=>array('c'=>'sql_field','n'=>'sql_field')
        // if sql_field is empty, means to get default value instead
        $return = array(
            'Id_Customer' => array('c' => 'c.`id_customer`', 'n' => ''),
            'IdCustomer' => array('c' => 'c.`id_customer`', 'n' => ''),
            'Gender' => array('c' => 'c.`id_gender`', 'n' => ''),
            'First_name' => array('c' => 'c.`firstname`', 'n' => ''),
            'Last_name' => array('c' => 'c.`lastname`', 'n' => ''),
            'Birthday_date' => array('c' => 'c.`birthday`', 'n' => ''),
            'Email' => array('c' => 'c.`email`', 'n' => 'n.`email`'),
            'Newsletter' => array('c' => 'c.`newsletter`', 'n' => '1'),
            'Optin' => array('c' => 'c.`optin`', 'n' => ''),
            'Website' => array('c' => 'c.`website`', 'n' => ''),
            'Note' => array('c' => 'c.`note`', 'n' => ''),
            'Company' => array('c' => 'c.`company`', 'n' => ''),
            'Siret' => array('c' => 'c.`siret`', 'n' => ''),
            'APE' => array('c' => 'c.`ape`', 'n' => ''),
            'Active' => array('c' => 'c.`active`', 'n' => ''),
            'Deleted' => array('c' => 'c.`deleted`', 'n' => ''),
            'CusLang' => array('c' => '(SELECT UPPER(cusl.iso_code) FROM ' . _DB_PREFIX_ . 'lang cusl WHERE cusl.id_lang=c.`id_lang`)', 'n' => ''),
            'Date_add' => array('c' => 'c.`date_add`', 'n' => 'n.`newsletter_date_add`'),
            'IdAddress' => array('c' => 'a.`id_address`', 'n' => ''),
            'Address_Company' => array('c' => 'a.`company`', 'n' => ''),
            'Address_Title' => array('c' => 'a.`alias`', 'n' => ''),
            'Address_First_name' => array('c' => 'a.`firstname`', 'n' => ''),
            'Address_Last_name' => array('c' => 'a.`lastname`', 'n' => ''),
            'Address1' => array('c' => 'a.`address1`', 'n' => ''),
            'Address2' => array('c' => 'a.`address2`', 'n' => ''),
            'Postcode' => array('c' => 'a.`postcode`', 'n' => ''),
            'City' => array('c' => 'a.`city`', 'n' => ''),
            'Address_other' => array('c' => 'a.`other`', 'n' => ''),
            'Phone' => array('c' => 'a.`phone`', 'n' => ''),
            'Phone_mobile' => array('c' => 'a.`phone_mobile`', 'n' => ''),
            'Vat_number' => array('c' => 'a.`vat_number`', 'n' => ''),
            'DNI' => array('c' => 'a.`dni`', 'n' => ''),
            'CustomerGroup' => array('c' => '(SELECT GROUP_CONCAT( gl2.name SEPARATOR "," ) FROM `' . _DB_PREFIX_ . 'customer_group` cg2 INNER JOIN `' . _DB_PREFIX_ . 'group_lang` gl2 ON (cg2.`id_group`=gl2.`id_group` AND gl2.`id_lang`=' . (int)$id_lang . ') WHERE cg2.id_customer = c.id_customer )', 'n' => ''),
            'CustomerIdGroup' => array('c' => '(SELECT GROUP_CONCAT( cg2.id_group SEPARATOR "," ) FROM `' . _DB_PREFIX_ . 'customer_group` cg2 WHERE cg2.id_customer = c.id_customer )', 'n' => ''),
            'CustomerIdGroupDefault' => array('c' => 'c.`id_default_group`', 'n' => ''),
            'CustomerGroupDefault' => array('c' => 'gl_default.`name`', 'n' => ''),
            'Country' => array('c' => 'cl.`name`', 'n' => ''),
            'Country_iso_code' => array('c' => 'cn.`iso_code`', 'n' => ''),
            'State' => array('c' => 'st.`name`', 'n' => ''),
            'State_iso_code' => array('c' => 'st.`iso_code`', 'n' => ''),
            'Order_Number' => array('c' => 'SKIP', 'n' => 'SKIP'),
            'Order_Total_Amount' => array('c' => 'SKIP', 'n' => 'SKIP'),
            'SPECIAL_LAST_ORDER_DATE' => array('c' => 'SPECIAL_LAST_ORDER_DATE', 'n' => 'SPECIAL_LAST_ORDER_DATE'),
            'SPECIAL_LAST_ORDER_AMOUNT' => array('c' => 'SPECIAL_LAST_ORDER_AMOUNT', 'n' => 'SPECIAL_LAST_ORDER_AMOUNT'),
            'Shop_Id' => array('c' => 'c.`id_shop`', 'n' => ''),
        );

        return $return;
    }

    /**
     * Get the true field from customer tables to include in a select clause to get the input alias
     */
    public static function getField($family, $alias, $id_lang)
    {
        // get all aliases definitions
        $sqlAlias = self::getSqlAliases($id_lang);
        // return field if alias defined for current tables
        $field = (isset($sqlAlias[$alias][$family]) ? $sqlAlias[$alias][$family] : null);
        if ($field != '') {
            return $field;
        } else    // else get the default value
        {
            return "'" . self::getDefaultValues($alias, ExportCustomer::getRightIdLang($id_lang)) . "'";
        }
    }

    /**
     * Get the translation of all fields that could be exported
     */
    public static function getFieldsTranslation($key, $id_lang)
    {
        $translations = array(
            'Id_Customer' => array(1 => 'id_customer', 2 => 'id_customer'),
            'IdCustomer' => array(1 => 'id_customer', 2 => 'id_customer'),
            'Gender' => array(1 => 'Gender', 2 => 'Genre'),
            'First_name' => array(1 => 'First name', 2 => 'Prénom'),
            'Last_name' => array(1 => 'Last name', 2 => 'Nom'),
            'Birthday_date' => array(1 => 'Birthday', 2 => 'Date de naissance'),
            'Email' => array(1 => 'Email', 2 => 'Email'),
            'Newsletter' => array(1 => 'Newsletter', 2 => 'Newsletter'),
            'Company' => array(1 => 'Company', 2 => 'Société'),
            'Siret' => array(1 => 'Siret', 2 => 'Siret'),
            'APE' => array(1 => 'APE', 2 => 'APE'),
            'Optin' => array(1 => 'Opt-in (Partners offers)', 2 => 'Opt-in (Offres partenaires)'),
            'Website' => array(1 => 'Website', 2 => 'Website'),
            'Note' => array(1 => 'Private note', 2 => 'Note privée'),
            'Active' => array(1 => 'Active', 2 => 'Actif'),
            'CusLang' => array(1 => 'Lang', 2 => 'Langue'),
            'Deleted' => array(1 => 'Deleted', 2 => 'Supprimé'),
            'Date_add' => array(1 => 'Date of account creation', 2 => 'Date de création du compte'),
            'IdAddress' => array(1 => 'id_address', 2 => 'id_address'),
            'Address_Company' => array(1 => 'Address - company', 2 => 'Adresse - société'),
            'Address_Title' => array(1 => 'Address - title', 2 => 'Adresse - titre'),
            'Address_First_name' => array(1 => 'Address - firstname', 2 => 'Adresse - prénom'),
            'Address_Last_name' => array(1 => 'Address - lastname', 2 => 'Adresse - nom'),
            'Address1' => array(1 => 'Address - address 1', 2 => 'Adresse - adresse'),
            'Address2' => array(1 => 'Address - address 2', 2 => 'Adresse - adresse comp.'),
            'Postcode' => array(1 => 'Address - postcode', 2 => 'Adresse - code postal'),
            'City' => array(1 => 'Address - city', 2 => 'Adresse - ville'),
            'Address_other' => array(1 => 'Address - other', 2 => 'Adresse - autre'),
            'Phone' => array(1 => 'Address - phone', 2 => 'Adresse - téléphone'),
            'Phone_mobile' => array(1 => 'Address - phone mobile', 2 => 'Adresse - téléphone portable'),
            'Vat_number' => array(1 => 'Address - Vat number', 2 => 'Adresse - N°TVA'),
            'DNI' => array(1 => 'Address - DNI / NIF / NIE', 2 => 'Adresse - DNI / NIF / NIE'),
            'CustomerGroup' => array(1 => 'Groups', 2 => 'Groupes'),
            'CustomerIdGroup' => array(1 => 'id_group', 2 => 'id_group'),
            'CustomerIdGroupDefault' => array(1 => 'Default id_group', 2 => 'id_group par défaut'),
            'CustomerGroupDefault' => array(1 => 'Default group', 2 => 'Groupe par défaut'),
            'Country' => array(1 => 'Address - country', 2 => 'Adresse - pays'),
            'Country_iso_code' => array(1 => 'Address - country ISO code', 2 => 'Adresse - pays code ISO'),
            'State' => array(1 => 'Address - state', 2 => 'Adresse - état/département'),
            'State_iso_code' => array(1 => 'Address - state ISO code', 2 => 'Adresse - état code ISO'),
            'Order_Number' => array(1 => 'Number of orders', 2 => 'Nombre de commandes'),
            'Order_Total_Amount' => array(1 => 'Total orders amount', 2 => 'Montant total des commandes'),
            'SPECIAL_LAST_ORDER_DATE' => array(1 => 'Last order date', 2 => 'Date de la dernière commande'),
            'SPECIAL_LAST_ORDER_AMOUNT' => array(1 => 'Last order amount', 2 => 'Montant de la dernière commande'),
            'Shop_Id' => array(1 => 'Shop id', 2 => 'Id shop'),
        );

        SC_Ext::readCustomExportCustomerConfigXML('translation', $translations);

//		return array_key_exists($key, $translations) ? $translations[$key][$id_lang] : '';
        if (array_key_exists($key, $translations)) {
            if (isset($translations[$key][$id_lang])) {
                return $translations[$key][$id_lang];
            } else {
                return $translations[$key][1];
            }    // use english as default
        } else {
            return '';
        }
    }

    /**
     * Default values could be used for missing datas
     */
    public static function getDefaultValues($key, $id_lang)
    {
        $default = array(
            'Id_Customer' => array(1 => '0', 2 => '0'),
            'IdCustomer' => array(1 => '0', 2 => '0'),
            'Gender' => array(1 => 'M/F', 2 => 'H/F'),
            'First_name' => array(1 => '', 2 => ''),
            'Last_name' => array(1 => '', 2 => ''),
            'Birthday_date' => array(1 => '', 2 => ''),
            'Company' => array(1 => '', 2 => ''),
            'Email' => array(1 => '', 2 => ''),
            'Newsletter' => array(1 => '0', 2 => '0'),
            'Optin' => array(1 => '0', 2 => '0'),
            'Website' => array(1 => '', 2 => ''),
            'Note' => array(1 => '', 2 => ''),
            'Active' => array(1 => '', 2 => ''),
            'Deleted' => array(1 => '', 2 => ''),
            'CusLang' => array(1 => '', 2 => ''),
            'Date_add' => array(1 => '', 2 => ''),
            'IdAddress' => array(1 => '', 2 => ''),
            'Siret' => array(1 => '', 2 => ''),
            'APE' => array(1 => '', 2 => ''),
            'Address_Company' => array(1 => '', 2 => ''),
            'Address_Title' => array(1 => '', 2 => ''),
            'Address_First_name' => array(1 => '', 2 => ''),
            'Address_Last_name' => array(1 => '', 2 => ''),
            'Address1' => array(1 => '', 2 => ''),
            'Address2' => array(1 => '', 2 => ''),
            'Postcode' => array(1 => '', 2 => ''),
            'City' => array(1 => '', 2 => ''),
            'Address_other' => array(1 => '', 2 => ''),
            'Phone' => array(1 => '', 2 => ''),
            'Phone_mobile' => array(1 => '', 2 => ''),
            'Vat_number' => array(1 => '', 2 => ''),
            'DNI' => array(1 => '', 2 => ''),
            'CustomerGroup' => array(1 => '', 2 => ''),
            'CustomerIdGroup' => array(1 => '', 2 => ''),
            'CustomerIdGroupDefault' => array(1 => '', 2 => ''),
            'CustomerGroupDefault' => array(1 => '', 2 => ''),
            'Country' => array(1 => '', 2 => ''),
            'Country_iso_code' => array(1 => '', 2 => ''),
            'State' => array(1 => '', 2 => ''),
            'State_iso_code' => array(1 => '', 2 => ''),
            'Order_Number' => array(1 => '0', 2 => '0'),
            'Order_Total_Amount' => array(1 => '0', 2 => '0'),
            'SPECIAL_LAST_ORDER_DATE' => array(1 => '0', 2 => '0'),
            'SPECIAL_LAST_ORDER_AMOUNT' => array(1 => '0', 2 => '0'),
        );

        if (array_key_exists($key, $default)) {
            if (isset($default[$key][$id_lang])) {
                return $default[$key][$id_lang];
            } else {
                return $default[$key][1];
            }    // use english as default
        } else {
            return '';
        }
    }

    /**
     * Check if in aliases, there is at least on alias on the input type (address, group or order)
     */
    public static function isFieldOfThisType($type, $aliases)
    {
        $fields = array(
            'Id_Customer' => array('customer'),
            'IdCustomer' => array('customer'),
            'Gender' => array('customer'),
            'First_name' => array('customer'),
            'Last_name' => array('customer'),
            'Birthday_date' => array('customer'),
            'Newsletter' => array('customer'),
            'Company' => array('customer'),
            'Optin' => array('customer'),
            'Website' => array('customer'),
            'Note' => array('customer'),
            'Active' => array('customer'),
            'Deleted' => array('customer'),
            'CusLang' => array('customer'),
            'Siret' => array('customer'),
            'APE' => array('customer'),
            'Email' => array('customer', 'newsletter'),
            'Date_add' => array('customer', 'newsletter'),
            'IdAddress' => array('address'),
            'Address_Company' => array('address'),
            'Address_Title' => array('address'),
            'Address_First_name' => array('address'),
            'Address_Last_name' => array('address'),
            'Address1' => array('address'),
            'Address2' => array('address'),
            'Postcode' => array('address'),
            'City' => array('address'),
            'Address_other' => array('address'),
            'Phone' => array('address'),
            'Phone_mobile' => array('address'),
            'Vat_number' => array('address'),
            'DNI' => array('address'),
            'Country' => array('address'),
            'Country_iso_code' => array('address'),
            'State' => array('address'),
            'State_iso_code' => array('address'),
            'CustomerGroup' => array('other'),
            //'CustomerGroup'=>array('group'),
            'CustomerIdGroup' => array('other'),
            'CustomerIdGroupDefault' => array('customer'),
            'CustomerGroupDefault' => array('group_default'),
            'Order_Number' => array('order'),
            'Order_Total_Amount' => array('order'),
            'SPECIAL_LAST_ORDER_DATE' => array('SPECIAL'),
            'SPECIAL_LAST_ORDER_AMOUNT' => array('SPECIAL'),
            'Shop_Id' => array('customer'),
        );

        foreach ($aliases as $alias) {
            if(!isset($fields[$alias]))
            {
                continue;
            }
            foreach ($fields[$alias] as $fieldType) {
                if ($fieldType == $type) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Is this field an aggregation field ?
     */
    public static function isAggregationField($alias)
    {
        $aggregationFields = array(
            'Date_add',    // used to aggregate customer list display (before export)
            'IdAddress',
            'Address_Company',
            'Address_Title',
            'Address_First_name',
            'Address_Last_name',
            'Address1',
            'Address2',
            'Postcode',
            'City',
            'Address_other',
            'Phone',
            'Phone_mobile',
            'Vat_number',
            'DNI',
            'Country',
            'Country_iso_code',
            'State',
            'State_iso_code',
            'CustomerGroup',
            'CustomerIdGroup'
        );

        return in_array($alias, $aggregationFields);
    }

    /**
     * To know if anyone of these aliases is an aggregator field ?
     */
    public static function needAggregation($aliases)
    {
        foreach ($aliases as $alias) {
            if (self::isAggregationField($alias)) {
                return true;
            }
        }
        return false;
    }

    /**
     * With an input string like
     * c.`id_customer`,c.`firstname`,c.`lastname`,c.`email`,count(*), c.`date_add`
     * remove all aggregation fields
     */
    public static function removeAggregationFields($sqlFieldList)
    {
        // TODO : to be improved
        $sqlFieldList = str_replace('count(*)', '', $sqlFieldList);
        $sqlFieldList = str_replace('sum(`total_paid_real`)', '', $sqlFieldList);
        // remove spaces
        $sqlFieldList = str_replace(' ', '', $sqlFieldList);
        // remove double comma
        $sqlFieldList = str_replace(',,', ',', $sqlFieldList);
        $sqlFieldList = str_replace(',,', ',', $sqlFieldList);
        // remove comma at the beginning of the string
        $sqlFieldList = preg_replace('#^,#', '', $sqlFieldList);
        // remove trailing comma
        $sqlFieldList = preg_replace('#,$#', '', $sqlFieldList);
        return $sqlFieldList;
    }
}
