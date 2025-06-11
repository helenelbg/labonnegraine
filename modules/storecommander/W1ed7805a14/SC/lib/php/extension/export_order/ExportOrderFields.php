<?php

class ExportOrderFields
{
    /**
        Manage list af available field aliases for order tables
        Store these aliases into DB and use it in the admin form

        Can also be used to display result list in admin form (call function with a system field aliases list)
     */
    public static function getFieldsDefinition($id_lang = 1)
    {
        /**
         *	ps_orders fields history :.
         *
         * Changes in 1.1 :
         *		`Order_Invoice_Number` changed to include the invoice number format
         */
        $fieldsInfos = array(
                        'Order_Id' => array('sqlField' => 'o.`id_order`', 'fieldType' => 'order', 'translations' => array(1 => 'Order id', 2 => 'Id de commande')),
                        'Order_Reference' => array('sqlField' => 'o.`reference`', 'fieldType' => 'order', 'translations' => array(1 => 'Order reference', 2 => 'Référence de commande')),
                        // 'Order_Invoice_Number'=>array('sqlField'=>'o.`invoice_number`', 'fieldType'=>'order', 'translations'=>array(1=>'Invoice number',2=>'N° facture')),
                        // 'Order_Invoice_Number'=>array('sqlField'=>'CONCAT("'.Configuration::get('PS_INVOICE_PREFIX', $id_lang).'",  LPAD(o.`invoice_number`,6,"0"))', 'fieldType'=>'order', 'translations'=>array(1=>'Invoice number',2=>'N° facture')),
                        'Order_Invoice_Number' => array('sqlField' => 'IF(LENGTH(CONCAT("'.Configuration::get('PS_INVOICE_PREFIX', $id_lang).'", o.`invoice_number`))<8, CONCAT("'.Configuration::get('PS_INVOICE_PREFIX', $id_lang).'", LPAD(o.`invoice_number`,8-LENGTH("'.Configuration::get('PS_INVOICE_PREFIX', $id_lang).'"),"0")), CONCAT("'.Configuration::get('PS_INVOICE_PREFIX', $id_lang).'", o.`invoice_number`))',
                        'fieldType' => 'order', 'translations' => array(1 => 'Invoice number', 2 => 'N° facture')),
                        'Order_Module' => array('sqlField' => 'o.`payment`', 'fieldType' => 'order', 'translations' => array(1 => 'Payment', 2 => 'Paiement')),
                        'Order_Date_Add' => array('sqlField' => 'o.`date_add`', 'fieldType' => 'order', 'translations' => array(1 => 'Order date', 2 => 'Date de la commande')),
                        'Order_Shipping_Number' => array('sqlField' => 'o.`shipping_number`', 'fieldType' => 'order', 'translations' => array(1 => 'Shipping number', 2 => 'N° d\'expédition')),
                        'Order_Last_History_Date' => array('sqlField' => 'oh1.`date_add`', 'fieldType' => 'last_order_history', 'translations' => array(1 => 'Status update', 2 => 'MAJ état de commande')),
                        'Order_Invoice_Date' => array('sqlField' => 'o.`invoice_date`', 'fieldType' => 'order', 'translations' => array(1 => 'Invoice date/hour', 2 => 'Date/Heure de facturation')),
                        'Order_Invoice_Date_without_time' => array('sqlField' => 'DATE_FORMAT(o.`invoice_date`, "%Y-%m-%d")', 'fieldType' => 'order', 'translations' => array(1 => 'Invoice date', 2 => 'Date de facturation')),
                        'Order_Delivery_Date' => array('sqlField' => 'o.`delivery_date`', 'fieldType' => 'order', 'translations' => array(1 => 'Delivery date', 2 => 'Date de livraison')),
                        'Order_Gift' => array('sqlField' => 'o.`gift`', 'fieldType' => 'order', 'translations' => array(1 => 'Gift', 2 => 'Cadeau')),
                        'Order_Gift_Message' => array('sqlField' => 'o.`gift_message`', 'fieldType' => 'order', 'translations' => array(1 => 'Gift - message', 2 => 'Cadeau - message')),
                        'Order_Weight' => array('sqlField' => '(select sum(`product_weight`*`product_quantity`) from `'._DB_PREFIX_.'order_detail` where `id_order`=o.`id_order`)', 'fieldType' => 'order', 'translations' => array(1 => 'Total weight', 2 => 'Poids total')),
                        'Cart_id' => array('sqlField' => 'o.`id_cart`', 'fieldType' => 'order', 'translations' => array(1 => 'Cart id', 2 => 'Id panier')),
                        'Order_PAYMENT_TRANSACTION_ID' => array('sqlField' => '(SELECT op.transaction_id FROM '._DB_PREFIX_.'order_payment op WHERE o.`reference`=op.`order_reference` LIMIT 1)', 'fieldType' => 'order', 'translations' => array(1 => 'Transaction Id', 2 => 'Id de transaction')),

                        'Order_State' => array('sqlField' => '(SELECT oh_sl.name FROM `'._DB_PREFIX_.'order_history` oh INNER JOIN `'._DB_PREFIX_.'order_state_lang` oh_sl ON (oh.id_order_state=oh_sl.id_order_state AND oh_sl.id_lang="'.(int) $id_lang.'") WHERE oh.`id_order`=o.`id_order` ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC LIMIT 1)', 'fieldType' => 'order', 'translations' => array(1 => 'Order status', 2 => 'Etat de la commande')),

                        'Order_Discount_Name' => array('sqlField' => '(SELECT GROUP_CONCAT(o_dis.`name`) FROM `'._DB_PREFIX_.'order_discount` o_dis WHERE o_dis.`id_order`=o.`id_order`)', 'fieldType' => 'order', 'translations' => array(1 => 'Discount name', 2 => 'Nom du code de réduction')),
                        'Order_Discount_Codes' => array('sqlField' => 'UPPER((SELECT GROUP_CONCAT(o_dis.`name`)) FROM `'._DB_PREFIX_.'order_discount` o_dis WHERE o_dis.`id_order`=o.`id_order`)', 'fieldType' => 'order', 'translations' => array(1 => 'Discount codes', 2 => 'Codes de réduction')),

                        'Order_Total_Paid_Real' => array('sqlField' => 'o.`total_paid_real`', 'fieldType' => 'order', 'translations' => array(1 => 'Total paid real', 2 => 'Total réel payé'), 'total' => 'order', 'price' => 1),
                        'Order_Total_Discount' => array('sqlField' => 'o.`total_discounts`', 'fieldType' => 'order', 'translations' => array(1 => 'Total discount', 2 => 'Total des réductions'), 'total' => 'order', 'price' => 1),
                        'Order_Total_Paid' => array('sqlField' => 'o.`total_paid`', 'fieldType' => 'order', 'translations' => array(1 => 'Total paid', 2 => 'Total payé'), 'total' => 'order', 'price' => 1),
                        'Order_Total_Products_ET' => array('sqlField' => 'o.`total_products`', 'fieldType' => 'order', 'translations' => array(1 => 'Total products ET', 2 => 'Total des produits HT'), 'total' => 'order', 'price' => 1),
                        'Order_Total_Products_ET_Shipping' => array('sqlField' => '(o.`total_products`+o.`total_shipping`)', 'fieldType' => 'order', 'translations' => array(1 => 'Total products ET + Shipping', 2 => 'Total des produits HT + Transport'), 'total' => 'order', 'price' => 1),
                        // in Prestashop prior to 1.3.0.1, the total_products_wt fields didn't exist
                        'Order_Total_Products_IT' => array('sqlField' => 'o.`total_products_wt`', 'fieldType' => 'order', 'translations' => array(1 => 'Total products IT', 2 => 'Total des produits TTC'), 'total' => 'order', 'price' => 1),
                        'Order_Total_Shipping' => array('sqlField' => 'o.`total_shipping`', 'fieldType' => 'order', 'translations' => array(1 => 'Total shipping', 2 => 'Total transport'), 'total' => 'order', 'price' => 1),
                        'Order_Total_Shipping_ET' => array('sqlField' => '(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100)))', 'fieldType' => 'order', 'translations' => array(1 => 'Total shipping ET', 2 => 'Total transport HT'), 'total' => 'order', 'price' => 1),
                        'Order_Total_Wrapping' => array('sqlField' => 'o.`total_wrapping`', 'fieldType' => 'order', 'translations' => array(1 => 'Total wrapping', 2 => 'Total emballage'), 'total' => 'order', 'price' => 1),

                        'Order_Customer_Message' => array('sqlField' => '(SELECT message FROM `'._DB_PREFIX_.'message` msg WHERE msg.`id_order` = o.id_order AND msg.`id_customer` = o.id_customer ORDER BY msg.date_add LIMIT 1)', 'fieldType' => 'order', 'translations' => array(1 => 'Customer message', 2 => 'Message du client')),

                        'Order_Detail_Product_Id' => array('sqlField' => 'od.`product_id`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product id', 2 => 'Détail commande - id produit')),
                        'Order_Detail_Product_Attribute_Id' => array('sqlField' => 'od.`product_attribute_id`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product attribute id', 2 => 'Détail commande - id déclinaison produit')),
                        'Order_Detail_Product_Name' => array('sqlField' => 'od.`product_name`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product name', 2 => 'Détail commande - nom produit')),
                        'Order_Detail_Product_Qte' => array('sqlField' => 'od.`product_quantity`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product quantity', 2 => 'Détail commande - qte produit')),
                        'Order_Detail_Product_Wholesale_Price' => array('sqlField' => 'odp.`wholesale_price`', 'fieldType' => 'orderDetailProduct', 'translations' => array(1 => 'Order detail - Wholesale price', 2 => 'Détail commande - prix d\'achat')),
                        'Order_Detail_Product_Price_U_HT' => array('sqlField' => 'od.`product_price`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit price excl. tax', 2 => 'Détail commande - prix unitaire HT'), 'total' => 'orderDetail', 'price' => 1),
                        'Order_Detail_Product_Price_HT' => array('sqlField' => '(od.`product_price`*od.`product_quantity`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total product excl. tax', 2 => 'Détail commande - prix total HT'), 'total' => 'orderDetail', 'price' => 1),
                        'Order_Detail_Product_Price_U_TTC' => array('sqlField' => '((od.`product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` )', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit price incl. tax', 2 => 'Détail commande - prix unitaire TTC'), 'total' => 'orderDetail', 'price' => 1),
                        'Order_Detail_Product_Price_TTC' => array('sqlField' => '(((od.`product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total product incl. tax', 2 => 'Détail commande - prix total TTC'), 'total' => 'orderDetail', 'price' => 1),
                        'Order_Detail_Product_Tax_amount_U' => array('sqlField' => '(od.`product_price` * (od.`tax_rate`/100) )', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit tax amount', 2 => 'Détail commande - montant unitaires des taxes'), 'total' => 'orderDetail', 'price' => 1),
                        'Order_Detail_Product_Tax_amount' => array('sqlField' => '((od.`product_price` * (od.`tax_rate`/100)) * od.`product_quantity`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total tax amount', 2 => 'Détail commande - montant total des taxes'), 'total' => 'orderDetail', 'price' => 1),
                        'Order_Detail_Product_ISBN' => array('sqlField' => 'od.`product_isbn`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - ISBN', 2 => 'Détail commande - ISBN')),
                        // New 1.4
                        'Order_Detail_Product_EAN13' => array('sqlField' => 'od.`product_ean13`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - EAN13', 2 => 'Détail commande - EAN13')),
                        // New 1.4 - added in Prestashop 1.4.0.2
                        'Order_Detail_Product_UPC' => array('sqlField' => 'od.`product_upc`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - UPC', 2 => 'Détail commande - UPC')),
                        'Order_Detail_Product_Reference' => array('sqlField' => 'od.`product_reference`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product reference', 2 => 'Détail commande - référence produit')),
                        // New 1.4
                        'Order_Detail_Product_Supplier_Reference' => array('sqlField' => 'od.`product_supplier_reference`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product supplier reference', 2 => 'Détail commande - référence fournisseur produit')),
                        'Order_Detail_Product_Weight' => array('sqlField' => 'od.`product_weight`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product weight', 2 => 'Détail commande - poids produit')),
                        'Order_Detail_Product_Tax_Name' => array('sqlField' => 'od.`tax_name`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product tax', 2 => 'Détail commande - TVA produit')),
                        'Order_Detail_Product_Tax_rate' => array('sqlField' => 'od.`tax_rate`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product tax rate', 2 => 'Détail commande - taux TVA produit')),
                        // New 1.4
                        'Order_Detail_Product_Ecotax' => array('sqlField' => 'od.`ecotax`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product ecotax', 2 => 'Détail commande - Ecotaxe produit'), 'price' => 1),
                        // New 1.4 - added in Prestashop 1.3.5.0
                        'Order_Detail_Product_Ecotax_rate' => array('sqlField' => 'od.`ecotax_tax_rate`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - product ecotax rate', 2 => 'Détail commande - taux ecotaxe produit'), 'price' => 1),
                        'Order_Detail_Product_Download_Nb' => array('sqlField' => 'od.`download_nb`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - download number', 2 => 'Détail commande - nombre téléchargements')),

                        // New 1.4 - not available in order_detail table, but in product ! Could be null if product is no more in the catalog
//						'Order_Detail_Product_Location'=>array('sqlField'=>'odp.`location`', 'fieldType'=>'orderDetailProduct', 'translations'=>array(1=>'Order detail - product location',2=>'Détail commande - Emplacement (entrepôt) produit')),
                        'Order_Detail_Product_Location' => array('sqlField' => 'COALESCE(odpa.`location`, odp.`location`)', 'fieldType' => 'orderDetailProduct', 'translations' => array(1 => 'Order detail - product location', 2 => 'Détail commande - Emplacement')),
                        // New 1.4 - not available in order_detail table, but in category ! Could be null if product is no more in the catalog
                        'Order_Detail_Product_Category_Default' => array('sqlField' => 'odpcl.`name`', 'fieldType' => 'orderDetailCategory', 'translations' => array(1 => 'Order detail - product default category', 2 => 'Détail commande - catégorie par défaut produit')),
                        // New 1.4 - not available in order_detail table, but in supplier ! Could be null if product is no more in the catalog
                        'Order_Detail_Product_Supplier_Name' => array('sqlField' => 'odps.`name`', 'fieldType' => 'orderDetailSupplier', 'translations' => array(1 => 'Order detail - product supplier', 2 => 'Détail commande - fournisseur produit')),
                        // New 1.4 - not available in order_detail table, but in manufacturer ! Could be null if product is no more in the catalog
                        'Order_Detail_Product_Manufacturer_Name' => array('sqlField' => 'odpm.`name`', 'fieldType' => 'orderDetailManufacturer', 'translations' => array(1 => 'Order detail - product manufacturer', 2 => 'Détail commande - marque produit')),
                        'Order_Detail_Product_Actual_Quantity' => array('sqlField' => 'od.product_id as temp_product_id, 0 as temp_id_warehouse, od.product_attribute_id as temp_product_attribute_id, 0', 'fieldType' => 'orderDetailProduct', 'translations' => array(1 => 'Order detail - current quantity', 2 => 'Détail commande - quantité actuelle')),

                        'Carrier' => array('sqlField' => 'ca.`name`', 'fieldType' => 'carrier', 'translations' => array(1 => 'Carrier', 2 => 'Transporteur')),

                        'Currency_Name' => array('sqlField' => 'cu.`name`', 'fieldType' => 'currency', 'translations' => array(1 => 'Currency', 2 => 'Devise')),
                        'Currency_Iso_Code' => array('sqlField' => 'cu.`iso_code`', 'fieldType' => 'currency', 'translations' => array(1 => 'Currency iso_code', 2 => 'Devise iso_code')),

                        'Id_Customer' => array('sqlField' => 'c.`id_customer`', 'fieldType' => 'customer', 'translations' => array(1 => 'Id customer', 2 => 'Id client')),
                        'Gender' => array('sqlField' => 'c.`id_gender`', 'fieldType' => 'customer', 'translations' => array(1 => 'Gender', 2 => 'Genre')),
                        'First_name' => array('sqlField' => 'c.`firstname`', 'fieldType' => 'customer', 'translations' => array(1 => 'First name', 2 => 'Prénom')),
                        'Last_name' => array('sqlField' => 'c.`lastname`', 'fieldType' => 'customer', 'translations' => array(1 => 'Last name', 2 => 'Nom')),
                        'Birthday_date' => array('sqlField' => 'c.`birthday`', 'fieldType' => 'customer', 'translations' => array(1 => 'Birthday date', 2 => 'Date anniversaire')),
                        'Email' => array('sqlField' => 'c.`email`', 'fieldType' => 'customer', 'translations' => array(1 => 'Email', 2 => 'Email')),
                        'Nb_Orders' => array('sqlField' => ' (SELECT COUNT(sub_o.id_order) FROM '._DB_PREFIX_.'orders sub_o WHERE sub_o.id_customer=c.`id_customer` AND sub_o.current_state IN (SELECT id_order_state FROM '._DB_PREFIX_.'order_state WHERE logable=1)) ', 'fieldType' => 'customer', 'translations' => array(1 => 'Nb orders', 2 => 'Nb. commandes')),
                        'New_Customer' => array('sqlField' => ' (IF((SELECT so.id_order FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer=c.`id_customer` AND so.id_order < o.id_order LIMIT 1) > 0, 0, 1)) ', 'fieldType' => 'customer', 'translations' => array(1 => 'New customer', 2 => 'Nouveau client')),

                        'Address_Delivery_Company' => array('sqlField' => 'ad.`company`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Company', 2 => 'Société')),
                        'Address_Delivery_First_name' => array('sqlField' => 'ad.`firstname`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : First name', 2 => 'Adresse livraison : Prénom')),
                        'Address_Delivery_Last_name' => array('sqlField' => 'ad.`lastname`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Last name', 2 => 'Adresse livraison : Nom')),
                        'Address_Delivery_Address1' => array('sqlField' => 'ad.`address1`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Address 1', 2 => 'Adresse livraison : Adresse 1')),
                        'Address_Delivery_Address2' => array('sqlField' => 'ad.`address2`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Address 2', 2 => 'Adresse livraison : Adresse 2')),
                        'Address_Delivery_Address_other' => array('sqlField' => 'ad.`other`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Address other', 2 => 'Adresse livraison : Adresse autre')),
                        'Address_Delivery_Postcode' => array('sqlField' => 'ad.`postcode`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Postcode', 2 => 'Adresse livraison : Code postal')),
                        'Address_Delivery_City' => array('sqlField' => 'ad.`city`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : City', 2 => 'Adresse livraison : Ville')),
                        'Address_Delivery_State' => array('sqlField' => 'std.`name`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : State', 2 => 'Adresse livraison : Etat')),
                        'Address_Delivery_State_iso_code' => array('sqlField' => 'std.`iso_code`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Iso_code state', 2 => 'Adresse livraison : Iso_code état')),
                        'Address_Delivery_Country' => array('sqlField' => 'cld.`name`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Country', 2 => 'Adresse livraison : Pays')),
                        'Address_Delivery_Country_iso_code' => array('sqlField' => 'cnd.`iso_code`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Iso_code country', 2 => 'Adresse livraison : Pays iso_code')),
                        'Address_Delivery_Phone' => array('sqlField' => 'ad.`phone`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Phone', 2 => 'Adresse livraison : Téléphone')),
                        'Address_Delivery_Phone_mobile' => array('sqlField' => 'ad.`phone_mobile`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : Phone mobile', 2 => 'Adresse livraison : Téléphone mobile')),
                        'Address_Delivery_vat_number' => array('sqlField' => 'ad.`vat_number`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Address delivery : customer VAT num.', 2 => 'Adresse livraison : n° TVA client')),

                        'Address_Invoice_Company' => array('sqlField' => 'ai.`company`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Company', 2 => 'Société')),
                        'Address_Invoice_First_name' => array('sqlField' => 'ai.`firstname`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : First name', 2 => 'Adresse facturation : Prénom')),
                        'Address_Invoice_Last_name' => array('sqlField' => 'ai.`lastname`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : Last name', 2 => 'Adresse facturation : Nom')),
                        'Address_Invoice_Address1' => array('sqlField' => 'ai.`address1`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : Address 1', 2 => 'Adresse facturation : Adresse 1')),
                        'Address_Invoice_Address2' => array('sqlField' => 'ai.`address2`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : Address 2', 2 => 'Adresse facturation : Adresse 2')),
                        'Address_Invoice_Address_other' => array('sqlField' => 'ai.`other`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : Address other', 2 => 'Adresse facturation : Adresse autre')),
                        'Address_Invoice_Postcode' => array('sqlField' => 'ai.`postcode`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : Postcode', 2 => 'Adresse facturation : Code postal')),
                        'Address_Invoice_City' => array('sqlField' => 'ai.`city`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : City', 2 => 'Adresse facturation : Ville')),
                        'Address_Invoice_State' => array('sqlField' => 'sti.`name`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : State', 2 => 'Adresse facturation : Etat')),
                        'Address_Invoice_State_iso_code' => array('sqlField' => 'sti.`iso_code`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : Iso_code state', 2 => 'Adresse facturation : Iso_code état')),
                        'Address_Invoice_Country' => array('sqlField' => 'cli.`name`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : country', 2 => 'Adresse facturation : Pays')),
                        'Address_Invoice_Country_iso_code' => array('sqlField' => 'cni.`iso_code`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : Iso_code country', 2 => 'Adresse facturation : Pays iso_code')),
                        'Address_Invoice_Phone' => array('sqlField' => 'ai.`phone`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : Phone', 2 => 'Adresse facturation : Téléphone')),
                        'Address_Invoice_Phone_mobile' => array('sqlField' => 'ai.`phone_mobile`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : Phone mobile', 2 => 'Adresse facturation : Téléphone mobile')),
                        'Address_Invoice_vat_number' => array('sqlField' => 'ai.`vat_number`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Address invoice : customer VAT num.', 2 => 'Adresse facturation : n° TVA client')),

                        'CustomerGroup' => array('sqlField' => 'cgl.`name`', 'fieldType' => 'group', 'translations' => array(1 => 'Group', 2 => 'Groupe')),

                        'Calculated_Taxes_breakdown' => array('sqlField' => 'od.`tax_rate` as tb_tax_name,o.`carrier_tax_rate` as tb_carrier_tax_rate,(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100))) as tb_total_shipping_tax_excl,(o.`total_shipping`-(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100)))) as tb_total_shipping_tax_amount, ((((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`) - (od.`sc_qc_product_price`*od.`product_quantity`)) AS Calculated_Taxes_Amount_breakdown,(od.`product_price`*od.`product_quantity`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Taxes breakdown', 2 => 'Ventilation TVA'), 'total' => 'orderDetail', 'price' => 1),
                        'Calculated_Taxes_breakdown_by_Country' => array('sqlField' => 'od.`tax_rate` as tbc_tax_name,cld.`name` as tbc_country_name,o.`carrier_tax_rate` as tbc_carrier_tax_rate,(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100))) as tbc_total_shipping_tax_excl,(o.`total_shipping`-(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100)))) as tbc_total_shipping_tax_amount, ((((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`) - (od.`sc_qc_product_price`*od.`product_quantity`)) AS Calculated_Taxes_breakdown_by_Country_Amount,(od.`product_price`*od.`product_quantity`)', 'fieldType' => 'orderDetailDelivery', 'translations' => array(1 => 'Taxes breakdown by country', 2 => 'Ventilation TVA par pays'), 'total' => 'orderDetail', 'price' => 1),
                        'Total_Excl_Taxes_By_Taxes_breakdown' => array('sqlField' => 'o.`id_order`', 'fieldType' => 'order', 'translations' => array(1 => 'VAT breakdown by order', 2 => 'Ventilation TVA par commande'), 'total' => 'order', 'price' => 1),
                        'Total_Excl_Taxes_By_Taxes_breakdown_by_Country' => array('sqlField' => 'cld.`name` as ttbc_country_name,o.`id_order`', 'fieldType' => 'orderDelivery', 'translations' => array(1 => 'VAT breakdown by order by country', 2 => 'Ventilation TVA par commande par pays'), 'total' => 'order', 'price' => 1),
                        'Calculated_Taxes_breakdown_for_FR' => array('sqlField' => 'od.`tax_rate` as fr_tax_name,cld.`name` as fr_country_name,cnd.`iso_code` as fr_country_iso,o.`carrier_tax_rate` as fr_carrier_tax_rate,(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100))) as fr_total_shipping_tax_excl,(o.`total_shipping`-(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100)))) as fr_total_shipping_tax_amount, ((((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`) - (od.`sc_qc_product_price`*od.`product_quantity`)) AS Calculated_Taxes_breakdown_for_FR_Amount,(od.`product_price`*od.`product_quantity`)', 'fieldType' => 'orderDetailDelivery', 'translations' => array(1 => 'VAT breakdown for E.U countries', 2 => 'Ventilation mini-guichet T.V.A. U.E.'), 'total' => 'orderDetail', 'price' => 1),
                        'Calculated_Payment_breakdown' => array('sqlField' => 'o.`payment` as pb_payment,o.`total_products`', 'fieldType' => 'order', 'translations' => array(1 => 'Payment method breakdown', 2 => 'Ventilation Moyens de paiement'), 'total' => 'order', 'price' => 1),
                        'Calculated_Payment_TTC_breakdown' => array('sqlField' => 'o.`payment` as pbttc_payment,o.`total_paid_real`', 'fieldType' => 'order', 'translations' => array(1 => 'Payment IT breakdown', 2 => 'Ventilation TTC Moyens de paiement'), 'total' => 'order', 'price' => 1),
                        'Calculated_Invoice_country_breakdown' => array('sqlField' => 'cli.`name` as icb_country,o.`total_products`', 'fieldType' => 'address_invoice', 'translations' => array(1 => 'Invoice countries breakdown', 2 => 'Ventilation des pays de facturation'), 'total' => 'order', 'price' => 1),
                        'Calculated_Delivery_country_breakdown' => array('sqlField' => 'cld.`name` as dcb_country,o.`total_products`', 'fieldType' => 'address_delivery', 'translations' => array(1 => 'Shipping countries breakdown', 2 => 'Ventilation des pays de livraison'), 'total' => 'order', 'price' => 1),
                        // 'Calculated_Slip'=>array('sqlField'=>'GROUP_CONCAT(os.`id_order_slip`) as id_slips, (SUM(osd.`product_quantity`*osd_pd.`product_price`) + SUM(o.`total_shipping`*os.`shipping_cost`))', 'fieldType'=>'order_slip', 'translations'=>array(1=>'Slips amount',2=>'Montant des avoirs'), 'total'=>"order"),
                        'Calculated_Slip' => array('sqlField' => '(SELECT COUNT(os.id_order_slip) FROM `'._DB_PREFIX_.'order_slip` os WHERE o.`id_order`=os.`id_order`) as id_slips, "" as Calculated_Slip_TTC, "" as Calculated_Slip_Shipping, "" as Calculated_Slip_Shipping_TTC, 0', 'fieldType' => 'order_slip', 'translations' => array(1 => 'Total products slips excl. tax', 2 => 'Total produits des avoirs HT'), 'total' => 'order', 'price' => 1),
                            'id_slips' => array('translations' => array(1 => 'Slips N°', 2 => 'Avoirs N°')),
                            'Calculated_Slip_TTC' => array('translations' => array(1 => 'Slips amount with taxes', 2 => 'Montant des avoirs TTC'), 'total' => 'order', 'price' => 1),
                            'Calculated_Slip_Shipping' => array('translations' => array(1 => 'Slips - Total shipping', 2 => 'Avoirs - Total Frais de port HT'), 'total' => 'order', 'price' => 1),
                            'Calculated_Slip_Shipping_TTC' => array('translations' => array(1 => 'Slips - Total shipping with taxes', 2 => 'Avoirs - Total Frais de port TTC'), 'total' => 'order', 'price' => 1),
                        'Order_Delivery_Number' => array('sqlField' => 'o.`delivery_number`', 'fieldType' => 'order', 'translations' => array(1 => 'Delivery number', 2 => 'N° de bon de livraison')),
                        'Order_Number_Products' => array('sqlField' => '(SELECT SUM(`product_quantity`-`product_quantity_refunded`) AS total from `'._DB_PREFIX_.'order_detail` WHERE `id_order`=o.`id_order`)', 'fieldType' => 'order', 'translations' => array(1 => 'Order detail - Number of products', 2 => 'Détail commande - Nombre de produits')),
                        );

        // some more fields only used to display list preview in web
        $fieldsInfos['Order_Display_Total'] = array('sqlField' => 'CONCAT(o.`total_paid_real`, \' \', cu.`iso_code`)', 'fieldType' => 'currency', 'translations' => array(1 => 'Total', 2 => 'Total'), 'price' => 1);
        $fieldsInfos['Order_Display_Customer'] = array('sqlField' => 'CONCAT(c.`firstname`, \' \', c.`lastname`)', 'fieldType' => 'customer', 'translations' => array(1 => 'Customer', 2 => 'Client'));

        if (ExportOrderTools::isPs14x() || ExportOrderTools::isPs13x())
        {
            $fieldsInfos['Order_Detail_Product_Price_U_HT'] = array('sqlField' => 'od.`sc_qc_product_price`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit price without taxes', 2 => 'Détail commande - prix unitaire HT'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Price_HT'] = array('sqlField' => '(od.`sc_qc_product_price`*od.`product_quantity`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total product excl. tax', 2 => 'Détail commande - prix total HT'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Price_U_TTC'] = array('sqlField' => '((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` )', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit price with taxes', 2 => 'Détail commande - prix unitaire TTC'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Price_TTC'] = array('sqlField' => '(((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total product incl. tax', 2 => 'Détail commande - prix total TTC'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Tax_amount_U'] = array('sqlField' => '(od.`sc_qc_product_price` * (od.`tax_rate`/100) )', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit taxes amount', 2 => 'Détail commande - montant unitaires des taxes'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Tax_amount'] = array('sqlField' => '((od.`sc_qc_product_price` * (od.`tax_rate`/100)) * od.`product_quantity`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total taxes amount', 2 => 'Détail commande - montant total des taxes'), 'total' => 'orderDetail', 'price' => 1);
        }
        if (ExportOrderTools::isNewerPs13x())
        {
            $fieldsInfos['Calculated_Taxes_breakdown'] = array('sqlField' => 'od.`tax_rate` as tb_tax_name,o.`carrier_tax_rate` as tb_carrier_tax_rate,(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100))) as tb_total_shipping_tax_excl,(o.`total_shipping`-(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100)))) as tb_total_shipping_tax_amount, o.`total_discounts` AS tb_total_discounts_tax_incl, (o.`total_discounts`/(1+(o.`carrier_tax_rate`/100))) AS tb_total_discounts_tax_excl, (o.`total_discounts`-(o.`total_discounts`/(1+(o.`carrier_tax_rate`/100)))) AS tb_total_discounts_tax_amount, o.`carrier_tax_rate` AS tb_total_discounts_tax_rate, ((((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`) - (od.`sc_qc_product_price`*od.`product_quantity`) ) AS Calculated_Taxes_Amount_breakdown,(od.`sc_qc_product_price`*od.`product_quantity`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'VAT breakdown', 2 => 'Ventilation TVA'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Calculated_Taxes_breakdown_by_Country'] = array('sqlField' => 'od.`tax_rate` as tbc_tax_name,cld.`name` as tbc_country_name,o.`carrier_tax_rate` as tbc_carrier_tax_rate,(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100))) as tbc_total_shipping_tax_excl,(o.`total_shipping`-(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100)))) as tbc_total_shipping_tax_amount, o.`total_discounts` AS tbc_total_discounts_tax_incl, (o.`total_discounts`/(1+(o.`carrier_tax_rate`/100))) AS tbc_total_discounts_tax_excl, (o.`total_discounts`-(o.`total_discounts`/(1+(o.`carrier_tax_rate`/100)))) AS tbc_total_discounts_tax_amount, o.`carrier_tax_rate` AS tbc_total_discounts_tax_rate, ((((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`) - (od.`sc_qc_product_price`*od.`product_quantity`) ) AS Calculated_Taxes_breakdown_by_Country_Amount,(od.`sc_qc_product_price`*od.`product_quantity`)', 'fieldType' => 'orderDetailDelivery', 'translations' => array(1 => 'VAT breakdown by country', 2 => 'Ventilation TVA par pays'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Calculated_Taxes_breakdown_for_FR'] = array('sqlField' => 'od.`tax_rate` as fr_tax_name,cld.`name` as fr_country_name,cnd.`iso_code` as fr_country_iso,o.`carrier_tax_rate` as fr_carrier_tax_rate,(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100))) as fr_total_shipping_tax_excl,(o.`total_shipping`-(o.`total_shipping`/ (1+(o.`carrier_tax_rate`/100)))) as fr_total_shipping_tax_amount, o.`total_discounts` AS fr_total_discounts_tax_incl, (o.`total_discounts`/(1+(o.`carrier_tax_rate`/100))) AS fr_total_discounts_tax_excl, (o.`total_discounts`-(o.`total_discounts`/(1+(o.`carrier_tax_rate`/100)))) AS fr_total_discounts_tax_amount, o.`carrier_tax_rate` AS fr_total_discounts_tax_rate, ((((od.`sc_qc_product_price` * (od.`tax_rate`/100+1) ) + od.`ecotax` ) * od.`product_quantity`) - (od.`sc_qc_product_price`*od.`product_quantity`) ) AS Calculated_Taxes_breakdown_for_FR_Amount,(od.`sc_qc_product_price`*od.`product_quantity`)', 'fieldType' => 'orderDetailDelivery', 'translations' => array(1 => 'Breakdown VAT for E.U. countries', 2 => 'Ventilation mini-guichet T.V.A. U.E.'), 'total' => 'orderDetail', 'price' => 1);
        }
        if (ExportOrderTools::isNewerPs15x())
        {
            $fieldsInfos['Order_Total_Shipping_ET'] = array('sqlField' => 'o.`total_shipping_tax_excl`', 'fieldType' => 'order', 'translations' => array(1 => 'Total shipping excl. tax', 2 => 'Total transport HT'), 'total' => 'order');
            $fieldsInfos['Order_Detail_Product_Price_U_HT'] = array('sqlField' => 'od.`unit_price_tax_excl`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit price without taxes', 2 => 'Détail commande - prix unitaire HT'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Price_HT'] = array('sqlField' => 'od.`total_price_tax_excl`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total product excl. tax', 2 => 'Détail commande - prix total HT'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Price_U_TTC'] = array('sqlField' => 'od.`unit_price_tax_incl`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit price with taxes', 2 => 'Détail commande - prix unitaire TTC'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Price_TTC'] = array('sqlField' => 'od.`total_price_tax_incl`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total product incl. tax', 2 => 'Détail commande - prix total TTC'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Tax_amount_U'] = array('sqlField' => '(od.`unit_price_tax_incl` - od.`unit_price_tax_excl`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit tax amount', 2 => 'Détail commande - montant unitaires des taxes'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Tax_amount'] = array('sqlField' => '(od.`total_price_tax_incl` - od.`total_price_tax_excl`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total tax amount', 2 => 'Détail commande - montant total des taxes'), 'total' => 'orderDetail', 'price' => 1);
            /*$fieldsInfos['Calculated_Taxes_breakdown'] = array('sqlField'=>'od.`tax_name` as tb_tax_name,o.`carrier_tax_rate` as tb_carrier_tax_rate,o.`total_shipping_tax_excl` as tb_total_shipping_tax_excl,o.`total_shipping_tax_incl`-o.`total_shipping_tax_excl` as tb_total_shipping_tax_amount, o.`total_discounts` AS tb_total_discounts_tax_incl, o.`total_discounts_tax_excl` AS tb_total_discounts_tax_excl, (o.`total_discounts`-o.`total_discounts_tax_excl`) AS tb_total_discounts_tax_amount, (((o.`total_discounts_tax_incl`/o.`total_discounts_tax_excl`)-1)*100) AS tb_total_discounts_tax_rate, (od.`total_price_tax_incl`-od.`total_price_tax_excl` - (o.`total_discounts` - (o.`total_discounts`/(1+(o.`carrier_tax_rate`/100))))) AS Calculated_Taxes_Amount_breakdown, od.`total_price_tax_excl`', 'fieldType'=>'orderDetail', 'translations'=>array(1=>'Taxes breakdown',2=>'Ventilation TVA'), 'total'=>"orderDetail", 'price'=>1);
            $fieldsInfos['Calculated_Taxes_breakdown_by_Country'] = array('sqlField'=>'od.`tax_name` as tbc_tax_name,cld.`name` as tbc_country_name,o.`carrier_tax_rate` as tbc_carrier_tax_rate,o.`total_shipping_tax_excl` as tbc_total_shipping_tax_excl,o.`total_shipping_tax_incl`-o.`total_shipping_tax_excl` as tbc_total_shipping_tax_amount, o.`total_discounts` AS tbc_total_discounts_tax_incl, o.`total_discounts_tax_excl` AS tbc_total_discounts_tax_excl, (o.`total_discounts`-o.`total_discounts_tax_excl`) AS tbc_total_discounts_tax_amount, (((o.`total_discounts_tax_incl`/o.`total_discounts_tax_excl`)-1)*100) AS tbc_total_discounts_tax_rate, (od.`total_price_tax_incl`-od.`total_price_tax_excl` - (o.`total_discounts` - (o.`total_discounts`/(1+(o.`carrier_tax_rate`/100))))) AS Calculated_Taxes_breakdown_by_Country_Amount, od.`total_price_tax_excl`', 'fieldType'=>'orderDetailDelivery', 'translations'=>array(1=>'Taxes breakdown by country',2=>'Ventilation TVA par pays'), 'total'=>"orderDetail", 'price'=>1);
            $fieldsInfos['Calculated_Taxes_breakdown_for_FR'] = array('sqlField'=>'od.`tax_name` as fr_tax_name,cld.`name` as fr_country_name,cnd.`iso_code` as fr_country_iso,o.`carrier_tax_rate` as fr_carrier_tax_rate,o.`total_shipping_tax_excl` as fr_total_shipping_tax_excl,o.`total_shipping_tax_incl`-o.`total_shipping_tax_excl` as fr_total_shipping_tax_amount, o.`total_discounts` AS fr_total_discounts_tax_incl, (o.`total_discounts`-o.`total_discounts_tax_excl`) AS fr_total_discounts_tax_amount, o.`total_discounts_tax_excl` AS fr_total_discounts_tax_excl, (((o.`total_discounts_tax_incl`/o.`total_discounts_tax_excl`)-1)*100) AS fr_total_discounts_tax_rate, (od.`total_price_tax_incl`-od.`total_price_tax_excl` - (o.`total_discounts` - (o.`total_discounts`/(1+(o.`carrier_tax_rate`/100))))) AS Calculated_Taxes_breakdown_for_FR_Amount, od.`total_price_tax_excl`', 'fieldType'=>'orderDetailDelivery', 'translations'=>array(1=>'Breakdown VAT for E.U. countries',2=>'Ventilation mini-guichet T.V.A. U.E.'), 'total'=>"orderDetail", 'price'=>1);*/
            $fieldsInfos['Order_Detail_Product_Wholesale_Price'] = array('sqlField' => 'odp_shop.`wholesale_price`', 'fieldType' => 'orderDetailProduct', 'translations' => array(1 => 'Order detail - Wholesale price', 2 => 'Détail commande - prix d\'achat'));
            $fieldsInfos['Order_Discount_Name'] = array('sqlField' => '(SELECT GROUP_CONCAT(o_dis.`name`) FROM `'._DB_PREFIX_.'order_cart_rule` o_dis WHERE o_dis.`id_order`=o.`id_order`)', 'fieldType' => 'order', 'translations' => array(1 => 'Discount name', 2 => 'Nom du code de réduction'));
            $fieldsInfos['Order_Discount_Codes'] = array('sqlField' => '(SELECT GROUP_CONCAT(cr.`code`) FROM `'._DB_PREFIX_.'order_cart_rule` o_dis LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON cr.id_cart_rule = o_dis.id_cart_rule WHERE o_dis.`id_order`=o.`id_order`)', 'fieldType' => 'order', 'translations' => array(1 => 'Discount codes', 2 => 'Codes de réduction'));
            $fieldsInfos['Order_Invoice_Number'] = array('sqlField' => '(SELECT GROUP_CONCAT(`number`) FROM `'._DB_PREFIX_.'order_invoice` oi WHERE oi.id_order=o.id_order)', 'fieldType' => 'order', 'translations' => array(1 => 'Invoice number', 2 => 'N° facture'));

            $fieldsInfos['Order_Detail_Product_Actual_Quantity'] = array('sqlField' => 'od.product_id as temp_product_id, od.id_warehouse as temp_id_warehouse, od.product_attribute_id as temp_product_attribute_id, 0', 'fieldType' => 'orderDetailProduct', 'translations' => array(1 => 'Order detail - actual quantity', 2 => 'Détail commande - quantité actuelle'));

            // NEW
            $fieldsInfos['Shop_Id'] = array('sqlField' => 'o.`id_shop`', 'fieldType' => 'order', 'translations' => array(1 => 'Shop id', 2 => 'Id de la Boutique'));
            $fieldsInfos['Order_Total_Discount_HT'] = array('sqlField' => 'o.`total_discounts_tax_excl`', 'fieldType' => 'order', 'translations' => array(1 => 'Total discount Tax excl.', 2 => 'Total des réductions HT'), 'total' => 'order', 'price' => 1);
            $fieldsInfos['Order_Total_Paid_HT'] = array('sqlField' => 'o.`total_paid_tax_excl`', 'fieldType' => 'order', 'translations' => array(1 => 'Total paid Tax excl.', 2 => 'Total payé HT'), 'total' => 'order', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Reduction_Percent'] = array('sqlField' => 'od.`reduction_percent`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - reduction percent', 2 => 'Détail commande - pourcentage de réduction'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Reduction_Amount'] = array('sqlField' => 'od.`reduction_amount`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - reduction amount', 2 => 'Détail commande - montant des réductions'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Reduction_Amount_HT'] = array('sqlField' => 'od.`reduction_amount_tax_excl`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - reduction percent Tax excl.', 2 => 'Détail commande - montant des réductions HT'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Total_Wrapping_ET'] = array('sqlField' => 'o.`total_wrapping_tax_excl`', 'fieldType' => 'order', 'translations' => array(1 => 'Total wrapping excl. tax', 2 => 'Total emballage HT'), 'total' => 'order', 'price' => 1);
        }
        if (ExportOrderTools::isNewerPs15x())
        {
            $fieldsInfos['Order_Detail_Product_Price_U_HT_No_Reduction'] = array('sqlField' => 'od.`original_product_price`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - unit price excl. tax (no reduction)', 2 => 'Détail commande - prix unitaire HT (sans réduction)'), 'total' => 'orderDetail', 'price' => 1);
            $fieldsInfos['Order_Detail_Product_Price_HT_No_Reduction'] = array('sqlField' => '(od.`original_product_price`*od.`product_quantity`)', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Order detail - total product excl. tax (no reduction)', 2 => 'Détail commande - prix total HT (sans réduction)'), 'total' => 'orderDetail', 'price' => 1);
        }
        if (ExportOrderTools::isNewerPs16_1_1x())
        {
            $fieldsInfos['Order_Detail_Original_Product_Wholesale_Price'] = array('sqlField' => 'od.`original_wholesale_price`', 'fieldType' => 'orderDetail', 'translations' => array(1 => 'Original wholesale price', 2 => 'Prix d\'achat original'));
            $fieldsInfos['Order_Total_Wholesale_Price'] = array('sqlField' => ' (SELECT SUM(otwp_od.`product_quantity`*otwp_od.`original_wholesale_price`) FROM `'._DB_PREFIX_.'order_detail` otwp_od WHERE otwp_od.`id_order` = o.`id_order`) ', 'fieldType' => 'order', 'translations' => array(1 => 'Total wholesale price', 2 => 'Total prix d\'achat'), 'total' => 'order', 'price' => 1);
            $fieldsInfos['Order_Total_Margin'] = array('sqlField' => ' (SELECT SUM(otwp_od.`product_quantity`*(otwp_od.`unit_price_tax_excl`-otwp_od.`original_wholesale_price`)) FROM `'._DB_PREFIX_.'order_detail` otwp_od WHERE otwp_od.`id_order` = o.`id_order`) ', 'fieldType' => 'order', 'translations' => array(1 => 'Total margin', 2 => 'Total Marge'), 'total' => 'order', 'price' => 1);
        }
        if(version_compare(_PS_VERSION_, '1.7.6', '>=')){
            $fieldsInfos['Currency_Name'] = array('sqlField' => 'cu_lang.`name`', 'fieldType' => 'currency', 'translations' => array(1 => 'Currency', 2 => 'Devise'));
        }
        if(version_compare(_PS_VERSION_, '1.7.8.0', '>=')){
            $fieldsInfos['Order_Note'] = array('sqlField' => 'o.`note`', 'fieldType' => 'order', 'translations' => array(1 => 'Order detail - Note', 2 => 'Détail commande - Note'));
        }

        $fieldsInfos['Order_Total_Taxes'] = array('sqlField' => ' (SELECT SUM(oi.total_paid_tax_incl - oi.total_paid_tax_excl) FROM `'._DB_PREFIX_.'order_invoice` oi WHERE oi.`id_order` = o.`id_order`) ', 'fieldType' => 'order', 'translations' => array(1 => 'Total taxes', 2 => 'Total taxes'), 'total' => 'order', 'price' => 1);

        if (ExportOrderTools::isSCAS())
        {
            $warehouses = Warehouse::getWarehouses();
            foreach ($warehouses as $warehouse)
            {
                $fieldsInfos['Order_Detail_Product_Location_'.$warehouse['id_warehouse']] = array('sqlField' => '(SELECT wpl.location FROM '._DB_PREFIX_.'warehouse_product_location wpl WHERE wpl.id_product = od.`product_id` AND  wpl.id_product_attribute = od.`product_attribute_id` AND  wpl.id_warehouse = "'.(int) $warehouse['id_warehouse'].'")', 'fieldType' => 'orderDetailProduct', 'translations' => array(1 => 'Order detail - product location', 2 => 'Détail commande - Emplacement'));
            }
        }

        if (ExportOrderTools::isNewerPs8x())
        {
            // todo order carrier / tracking number
            $fieldsInfos['Order_Shipping_Number'] = array('sqlField' => '(SELECT tracking_number FROM '._DB_PREFIX_.'order_carrier WHERE id_order = o.id_order AND id_carrier = o.id_carrier)', 'fieldType' => 'order', 'translations' => array(1 => 'Shipping number', 2 => 'N° d\'expédition'));
        }

        // add specific field infos (only for some)
        // some fields should be replaced by some more complex expression to produce a human readable value
        // Example : id_gender whould be replaced by H F H/F (it depends on active language)
        /*
            CASE c.`id_gender`
                WHEN "1" THEN "M"
                WHEN "2" THEN "F"
                ELSE "M/F" END
        */
        $fieldsInfos['Order_Module']['sqlFieldOverride'] = array(2 => 'o.`payment`',
                                                                    1 => 'o.`payment`');
        $fieldsInfos['Order_Gift']['sqlFieldOverride'] = array(2 => 'CASE o.`gift` WHEN "1" THEN "Gift" WHEN "0" THEN "Not a gift" END',
                                                                1 => 'CASE o.`gift` WHEN "1" THEN "Cadeau" WHEN "0" THEN "Pas un cadeau" END');
        $fieldsInfos['Gender']['sqlFieldOverride'] = array(2 => 'CASE c.`id_gender` WHEN "1" THEN "M" WHEN "2" THEN "F" ELSE "M/F" END',
                                                            1 => 'CASE c.`id_gender` WHEN "1" THEN "H" WHEN "2" THEN "F" ELSE "H/F" END');


        SC_Ext::readCustomExportOrderConfigXML('fieldDefinition', $fieldsInfos);

        return $fieldsInfos;
    }

    /**
     * Return the SQL select clause for the input aliases.
     *
     * @param : aliases = array of string
     * @param : forSelectClause = if true, then ok to apply the human expression in SQL fields
     * @param : id_lang
     */
    public static function getSqlFields($aliases, $forSelectClause, $id_lang)
    {
        $sql = '';

        // get fields infos
        $fieldsInfos = self::getFieldsDefinition($id_lang);

        // if any field to list
        if (count($aliases) > 0)
        {
            foreach ($aliases as $alias)
            {
                if(empty($fieldsInfos[$alias])){
                    continue;
                }
                // get sql field expression if some replacement is defined / else get the classic sqlField
                // $fieldExpression = ($forSelectClause && array_key_exists('sqlFieldOverride', $fieldsInfos[$alias])) ? $fieldsInfos[$alias]['sqlFieldOverride'][$id_lang] : $fieldsInfos[$alias]['sqlField'];
                if ($forSelectClause && array_key_exists('sqlFieldOverride', $fieldsInfos[$alias]))
                {
                    // check for language
                    if (isset($fieldsInfos[$alias]['sqlFieldOverride'][$id_lang]))
                    {
                        $fieldExpression = $fieldsInfos[$alias]['sqlFieldOverride'][$id_lang];
                    }
                    else
                    {
                        $fieldExpression = $fieldsInfos[$alias]['sqlFieldOverride'][1];
                    }	// use english as default
                }
                else
                {
                    $fieldExpression = $fieldsInfos[$alias]['sqlField'];
                }
                // then build sql for this field
                $sql .= $fieldExpression.' As '.$alias.',';
            }
            // remove trailing comma
            $sql = trim($sql, ','); // Tools::substr($sql, 0, Tools::strlen($sql)-1);
        }

        return $sql;
    }

    /**
     * Get the translation of all fields that could be exported.
     */
    public static function getFieldsTranslation($alias, $id_lang)
    {
        // get fields infos
        $fieldsInfos = self::getFieldsDefinition($id_lang);

        //		return array_key_exists($id_lang, $fieldsInfos[$alias]['translations']) ? $fieldsInfos[$alias]['translations'][$id_lang] : '';
        $return = $alias;
        if (!empty($fieldsInfos[$alias]['translations']))
        {
            if (array_key_exists($id_lang, $fieldsInfos[$alias]['translations']))
            {
                $return = $fieldsInfos[$alias]['translations'][$id_lang];
            }
            else
            {
                $return = $fieldsInfos[$alias]['translations'][1];
            }
        }

        return $return;	// use english as default
    }

    /**
     * Get the translation of all fields that could be exported.
     */
    public static function getFieldTotal($alias)
    {
        // get fields infos
        $fieldsInfos = self::getFieldsDefinition(1);

        $return = '';
        if (!empty($fieldsInfos[$alias]['total']))
        {
            $return = $fieldsInfos[$alias]['total'];
        }

        return $return;
    }

    /**
     * Get the translation of all fields that could be exported.
     */
    public static function getFieldPrice($alias)
    {
        // get fields infos
        $fieldsInfos = self::getFieldsDefinition(1);

        $return = false;
        if (!empty($fieldsInfos[$alias]['price']))
        {
            $return = true;
        }

        return $return;
    }

    /**
     * Check if in aliases, there is at least on alias on the input type (customer, address_delivery, group).
     */
    public static function isFieldOfThisType($type, $aliases)
    {
        // get fields infos
        $fieldsInfos = self::getFieldsDefinition();

        foreach ($aliases as $alias)
        {
            if (isset($fieldsInfos[$alias]) && $fieldsInfos[$alias]['fieldType'] === $type)
            {
                return true;
            }
        }

        return false;
    }
}
