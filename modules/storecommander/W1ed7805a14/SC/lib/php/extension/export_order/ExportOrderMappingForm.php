<?php

class ExportOrderMappingForm
{
    public $orderFields;
    public $orderTotalFields;
    public $orderDetailFields;
    public $customerFields;
    public $addressDeliveryFields;
    public $addressInvoiceFields;
    public $calculatedFields;

    public function __construct($id_lang)
    {
        $this->id_lang = (int)$id_lang;
        $this->initOrderFields();
        $this->initOrderTotalFields();
        $this->initOrderDetailFields();
        $this->initCustomerFields();
        $this->initAddressDeliveryFields();
        $this->initAddressInvoiceFields();
        $this->initCalculatedFields();
    }

    public function getAllFieldsLabelforJs()
    {
        $sourceList = array(
            'orderFields'=>$this->orderFields,
            'orderTotalFields'=>$this->orderTotalFields,
            'orderDetailFields'=>$this->orderDetailFields,
            'customerFields'=>$this->customerFields,
            'addressDeliveryFields'=>$this->addressDeliveryFields,
            'addressInvoiceFields'=>$this->addressInvoiceFields,
            'calculatedFields'=>$this->calculatedFields,
        );
        $labels = array();
        foreach($sourceList as $sourceId => $fieldList)
        {
            foreach($fieldList as $key => $description)
            {
                $labels[Tools::substr($key, 3)] = $description;
            }
        }
        return json_encode($labels);
    }


    public function getFieldArrayforJsSelectField($type = 'orderFields',$json = false)
    {
        if(!isset($this->{$type}))
        {
            return array();
        }

        asort($this->{$type});

        $tmp = array();
        foreach($this->{$type} as $key => $description)
        {
            $tmp[] = [
                'text' => $description,
                'value' => $type.'|'.Tools::substr($key, 3)
            ];
        }

        if($json)
        {
            return json_encode($tmp);
        }

        return $tmp;
    }

    private function initOrderFields()
    {
        $this->orderFields = array(
            '01_Order_Id' => _l('id order'),
            '01_Order_Reference' => _l('Order reference'),
            '02_Order_Invoice_Number' => _l('Invoice number'),
            '03_Carrier' => _l('Carrier'),
            '04_Order_Shipping_Number' => _l('Shipping number'),
            '05_Order_Gift' => _l('Gift'),
            '05_Order_Gift_Message' => _l('Gift message'),
            '06_Order_Date_Add' => _l('Creation date'),
            '07_Order_Last_History_Date' => _l('Status update'),
            '07_Order_Invoice_Date' => _l('Invoice date/hour'),
            '07_Order_Invoice_Date_without_time' => _l('Invoice date'),
            '08_Order_Delivery_Date' => _l('Delivery Date'),
            '09_Order_Weight' => _l('Total weight'),
            '10_Order_State' => _l('Order status'),
            '12_Cart_id' => _l('Cart id'),
            '13_Order_Discount_Name' => _l('Discount name'),
            '14_Order_PAYMENT_TRANSACTION_ID' => _l('Id transaction'),
            '15_Order_Discount_Codes' => _l('Discount codes'),
            '16_Order_Delivery_Number' => _l('Delivery number'),
            '17_Order_Customer_Message' => _l('Customer message'),
            '18_Order_Number_Products' => _l('Number of products'),
        );

        if (SCMS) {
            $this->orderFields['11_Shop_Id'] = _l('Shop Id');
        }
        if(version_compare(_PS_VERSION_, '1.7.8.0', '>=')){
            $this->orderFields['19_Order_Note'] = _l('Note');
        }

        SC_Ext::readCustomExportOrderConfigXML('order', $this->orderFields);
    }

    private function initOrderTotalFields()
    {
        $this->orderTotalFields = array(
            '01_Order_Module' => _l('Payment method'),
            '02_Order_Total_Paid_Real' => _l('Total paid real'),
            '03_Order_Total_Discount' => _l('Total discount'),
            '04_Order_Total_Paid' => _l('Total paid'),
            '05_Order_Total_Products_ET' => _l('Total products excluding Tax'),
            '13_Order_Total_Products_ET_Shipping' => _l('Total products excluding Tax + Shipping'),
            '06_Order_Total_Products_IT' => _l('Total products incl. tax'),
            '07_Order_Total_Shipping' => _l('Total shipping'),
            '08_Order_Total_Shipping_ET' => _l('Total shipping excl. tax'),
            '09_Currency_Name' => _l('Currency name'),
            '10_Currency_Iso_Code' => _l('Currency iso_code'),
            '11_Order_Total_Wrapping' => _l('Total wrapping'),
            '12_Order_Total_Wholesale_Price' => _l('Total wholesale price'),
            '13_Order_Total_Margin' => _l('Total margin'),
            '03_Order_Total_Discount_HT' => _l('Total discount excl. tax'),
            '04_Order_Total_Paid_HT' => _l('Total paid excl. tax'),
            '12_Order_Total_Wrapping_ET' => _l('Total wrapping excl. tax'),
            '18_Order_Total_Taxes' => _l('Total taxes')
        );

        SC_Ext::readCustomExportOrderConfigXML('orderTotal', $this->orderTotalFields);
    }

    private function initOrderDetailFields()
    {
        $this->orderDetailFields = array(
            '01_Order_Detail_Product_Id' => _l('Product id'),
            '01_Order_Detail_Product_Attribute_Id' => _l('Product Attribute id'),
            '02_Order_Detail_Product_Name' => _l('Product name'),
            '03_Order_Detail_Product_Reference' => _l('Product reference'),
            '04_Order_Detail_Product_Qte' => _l('Product quantity'),
            '05_Order_Detail_Product_Price_U_HT' => _l('Unit price excl. tax'),
            '06_Order_Detail_Product_Price_HT' => _l('Total product excl. tax'),
            '07_Order_Detail_Product_Price_U_TTC' => _l('Unit price incl. tax'),
            '08_Order_Detail_Product_Price_TTC' => _l('Total product incl. tax'),
            '09_Order_Detail_Product_Tax_Name' => _l('Product tax name'),
            '10_Order_Detail_Product_Tax_rate' => _l('Product tax rate'),
            '11_Order_Detail_Product_Tax_amount_U' => _l('Unit tax amount'),
            '12_Order_Detail_Product_Tax_amount' => _l('Total tax amount'),
            '13_Order_Detail_Product_Ecotax' => _l('Product ecotax'),
            // 14_Order_Detail_Product_Ecotax_rate only for version 1.3.3 and newer
            '15_Order_Detail_Product_Weight' => _l('Product weight'),
            '16_Order_Detail_Product_Location' => _l('Location'),
            '17_Order_Detail_Product_Supplier_Reference' => _l('Product supplier reference'),
            '18_Order_Detail_Product_Supplier_Name' => _l('Product supplier'),
            '19_Order_Detail_Product_Manufacturer_Name' => _l('Product manufacturer'),
            '20_Order_Detail_Product_EAN13' => _l('Product EAN13'),
            '21_Order_Detail_Product_ISBN' => _l('Product ISBN'),
            // 21_Order_Detail_Product_UPC only for version 1.4.0.2 and newer
            '22_Order_Detail_Product_Category_Default' => _l('Product default category'),
            '23_Order_Detail_Product_Download_Nb' => _l('Product download number'),
            '24_Order_Detail_Product_Wholesale_Price' => _l('Wholesale price'),
            '25_Order_Detail_Product_Actual_Quantity' => _l('Current quantity'),
            '17_Order_Detail_Product_UPC' => _l('Product UPC'),
            '09_Order_Detail_Product_Ecotax_rate' => _l('Product ecotax rate'),
            '09_Order_Detail_Product_Price_U_HT_No_Reduction' => _l('Unit price excl. tax') . ' ' . _l('(no reduction)'),
            '09_Order_Detail_Product_Price_HT_No_Reduction' => _l('Total product excl. tax') . ' ' . _l('(no reduction)'),
            '12_Order_Detail_Product_Reduction_Percent' => _l('Product discount percentage'),
            '12_Order_Detail_Product_Reduction_Amount' => _l('Product discount amount'),
            '12_Order_Detail_Product_Reduction_Amount_HT' => _l('Product discount amount excl. tax'),
            '25_Order_Detail_Original_Product_Wholesale_Price' => _l('Original wholesale price'),
        );

        if (SCAS) {
            $warehouses = Warehouse::getWarehouses();
            foreach ($warehouses as $warehouse) {
                $this->orderDetailFields['16_Order_Detail_Product_Location_' . $warehouse["id_warehouse"]] = _l('Location') . " (" . $warehouse["name"] . ")";
            }
        }

        SC_Ext::readCustomExportOrderConfigXML('orderDetail', $this->orderDetailFields);
    }

    private function initCustomerFields()
    {
        $this->customerFields = array(
            '01_Id_Customer' => _l('Unique id'),
            '02_Gender' => _l('Gender'),
            '03_First_name' => _l('First name'),
            '04_Last_name' => _l('Last name'),
            '05_Birthday_date' => _l('Birthday date'),
            '06_Email' => _l('Email'),
            '07_CustomerGroup' => _l('Customer group'),
            '08_Nb_Orders' => _l('Nb orders'),
            '09_New_Customer' => _l('New customer')
        );

        SC_Ext::readCustomExportOrderConfigXML('customer', $this->customerFields);
    }

    private function initAddressDeliveryFields()
    {
        $this->addressDeliveryFields = array(
            '01_Address_Delivery_Company' => _l('Delivery : Company'),
            '02_Address_Delivery_First_name' => _l('Delivery : Address First name'),
            '03_Address_Delivery_Last_name' => _l('Delivery : Address Last name'),
            '05_Address_Delivery_Address1' => _l('Delivery : Address - part 1'),
            '06_Address_Delivery_Address2' => _l('Delivery : Address - part 2'),
            '07_Address_Delivery_Address_other' => _l('Delivery : Other address'),
            '08_Address_Delivery_Postcode' => _l('Delivery : Post code'),
            '09_Address_Delivery_City' => _l('Delivery : City'),
            '10_Address_Delivery_State' => _l('Delivery : State'),
            '11_Address_Delivery_State_iso_code' => _l('Delivery : State ISO code'),
            '12_Address_Delivery_Country' => _l('Delivery : Country'),
            '13_Address_Delivery_Country_iso_code' => _l('Delivery : Country ISO code'),
            '14_Address_Delivery_Phone' => _l('Delivery : Phone'),
            '15_Address_Delivery_Phone_mobile' => _l('Delivery : Mobile phone'),
            '16_Address_Delivery_vat_number' => _l('Delivery: Customer VAT number')
        );

        SC_Ext::readCustomExportOrderConfigXML('addressDelivery', $this->addressDeliveryFields);
    }

    private function initAddressInvoiceFields()
    {
        $this->addressInvoiceFields = array(
            '01_Address_Invoice_Company' => _l('Invoice : Company'),
            '02_Address_Invoice_First_name' => _l('Invoice : Address First name'),
            '03_Address_Invoice_Last_name' => _l('Invoice : Address Last name'),
            '05_Address_Invoice_Address1' => _l('Invoice : Address - part 1'),
            '06_Address_Invoice_Address2' => _l('Invoice : Address - part 2'),
            '07_Address_Invoice_Address_other' => _l('Invoice : Other address'),
            '08_Address_Invoice_Postcode' => _l('Invoice : Post code'),
            '09_Address_Invoice_City' => _l('Invoice : City'),
            '10_Address_Invoice_State' => _l('Invoice : State'),
            '11_Address_Invoice_State_iso_code' => _l('Invoice : State ISO code'),
            '12_Address_Invoice_Country' => _l('Invoice : Country'),
            '13_Address_Invoice_Country_iso_code' => _l('Invoice : Country ISO code'),
            '14_Address_Invoice_Phone' => _l('Invoice : Phone'),
            '15_Address_Invoice_Phone_mobile' => _l('Invoice : Mobile phone'),
            '16_Address_Invoice_vat_number' => _l('Invoice: Customer VAT number')
        );

        SC_Ext::readCustomExportOrderConfigXML('addressInvoice', $this->addressInvoiceFields);
    }

    private function initCalculatedFields()
    {
        $this->calculatedFields = array(
            '01_Calculated_Taxes_breakdown' => _l('VAT breakdown'),
            '02_Total_Excl_Taxes_By_Taxes_breakdown' => _l('VAT breakdown by order'),
            '03_Calculated_Payment_breakdown' => _l('Payment method breakdown'),
            '03_Calculated_Payment_TTC_breakdown' => _l('Payment IT breakdown'),
            '04_Calculated_Invoice_country_breakdown' => _l('Invoicing countries breakdown'),
            '05_Calculated_Delivery_country_breakdown' => _l('Shipping countries breakdown'),
            '06_Calculated_Slip' => _l('Credit notes'),
            '07_Calculated_Taxes_breakdown_by_Country' => _l('VAT breakdown by country'),
            '08_Total_Excl_Taxes_By_Taxes_breakdown_by_Country' => _l('VAT breakdown by order by country'),
            '09_Calculated_Taxes_breakdown_for_FR' => _l('VAT breakdown for EU countries')
        );

        SC_Ext::readCustomExportOrderConfigXML('calculated', $this->calculatedFields);
    }


}