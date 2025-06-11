<?php

class ExportCustomerMappingForm
{
    public $customerFields;
    public $addressFields;
    public $orderFields;
    public $miscellaneousFields;

    public function __construct($id_lang)
    {
        $this->id_lang = (int)$id_lang;
        $this->initCustomerFields();
        $this->initAddressFields();
        $this->initOrderFields();
        $this->initMiscellaneousFields();
    }

    public function getAllFieldsLabelforJs()
    {
        $sourceList = array(
            'customerFields' => $this->customerFields,
            'addressFields' => $this->addressFields,
            'orderFields' => $this->orderFields,
            'miscellaneousFields' => $this->miscellaneousFields
        );

        $labels = array();
        foreach ($sourceList as $fieldList) {
            foreach ($fieldList as $key => $description) {
                $labels[$key] = $description;
            }
        }

        return json_encode($labels);
    }


    public function getFieldArrayforJsSelectField($type = 'customerFields', $json = false)
    {
        if (!isset($this->{$type})) {
            return array();
        }

        asort($this->{$type});

        $tmp = array();
        foreach ($this->{$type} as $key => $description) {
            $tmp[] = [
                'text' => $description,
                'value' => $type.'|'.$key
            ];
        }

        if ($json) {
            return json_encode($tmp);
        }

        return $tmp;
    }

    private function initCustomerFields()
    {
        $this->customerFields = array(
            'IdCustomer' => _l('Unique ID'),
            'Gender' => _l('Gender'),
            'First_name' => _l('First name'),
            'Last_name' => _l('Last name'),
            'Birthday_date' => _l('Birthday date'),
            'Email' => _l('Email'),
            'Company' => _l('Company'),
            'Newsletter' => _l('Accepts newsletter ?'),
            'Optin' => _l('Accepts partners advertising ?'),
            'Website' => _l('Website'),
            'Note' => _l('Private note'),
            'Active' => _l('Is active'),
            'Deleted' => _l('Is deleted'),
            'Date_add' => _l('Date added'),
            'CusLang' => _l('Lang'),
            'Siret' => _l('Siret'),
            'APE' => _l('APE'),
        );
        if (SCMS) {
            $this->customerFields['Shop_Id'] = _l('Shop Id');
        }

        SC_Ext::readCustomExportCustomerConfigXML('customer', $this->customerFields);

        $this->translateOptions($this->customerFields);
    }

    private function initAddressFields()
    {
        $this->addressFields = array(
            'IdAddress' => _l('Address ID'),
            'Address_Company' => _l('Company (address)'),
            'Address_Title' => _l('Address Title'),
            'Address_First_name' => _l('Address First name'),
            'Address_Last_name' => _l('Address Last name'),
            'Address1' => _l('Address - part 1'),
            'Address2' => _l('Address - part 2'),
            'Address_other' => _l('Other address'),
            'Postcode' => _l('Postcode'),
            'City' => _l('City'),
            'State' => _l('State'),
            'State_iso_code' => _l('State ISO code'),
            'Country' => _l('Country'),
            'Country_iso_code' => _l('Country ISO code'),
            'Phone' => _l('Phone'),
            'Phone_mobile' => _l('Mobile phone'),
            'Vat_number' => _l('Vat number'),
            'DNI' => _l('DNI / NIF / NIE')
        );

        SC_Ext::readCustomExportCustomerConfigXML('address', $this->addressFields);

        $this->translateOptions($this->addressFields);
    }

    private function initOrderFields()
    {
        $this->orderFields = array(
            'Order_Number' => _l('Number of orders'),
            'Order_Total_Amount' => _l('Total amount of orders'),
            'SPECIAL_LAST_ORDER_DATE' => _l('Last order date'),
            'SPECIAL_LAST_ORDER_AMOUNT' => _l('Last order amount')
        );

        SC_Ext::readCustomExportCustomerConfigXML('order', $this->orderFields);

        $this->translateOptions($this->orderFields);
    }

    private function initMiscellaneousFields()
    {
        $this->miscellaneousFields = array(
            'CustomerGroup' => _l('Group name'),
            'CustomerIdGroup' => _l('Group ID'),
            'CustomerGroupDefault' => _l('Default group'),
            'CustomerIdGroupDefault' => _l('Default Group id')
        );

        SC_Ext::readCustomExportCustomerConfigXML('miscellaneous', $this->miscellaneousFields);

        $this->translateOptions($this->miscellaneousFields);
    }

    /**
     * @param $fieldArray
     * @return void
     */
    private function translateOptions(&$fieldArray)
    {
        $rightIdLang = ExportCustomer::getRightIdLang($this->id_lang);

        foreach($fieldArray as $key => $val)
        {
            $fieldArray[$key] = ExportCustomerFields::getFieldsTranslation($key,$rightIdLang);
        }
    }
}