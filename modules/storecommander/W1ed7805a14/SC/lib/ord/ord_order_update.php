<?php

    $id_order = (int) Tools::getValue('gr_id', 0);
    if (array_key_exists('id_order', $_POST) && Tools::getValue('id_order') && is_numeric(Tools::getValue('id_order')))
    {
        $id_order = (int) Tools::getValue('id_order');
    }
    $isStatus = 0;

    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        $fields = array('payment', 'status', 'shipping_number', 'invoice_number', 'delivery_number',
            'date_add', 'id_carrier', 'reference', 'conversion_rate',
            'recyclable', 'gift', 'gift_message', 'total_discounts',
            'total_discounts_tax_incl', 'total_discounts_tax_excl',
            'total_paid_tax_incl', 'total_paid_tax_excl', 'total_paid_real',
            'total_products', 'total_products_wt', 'total_shipping',
            'total_shipping_tax_incl', 'total_shipping_tax_excl',
            'carrier_tax_rate', 'total_wrapping', 'total_wrapping_tax_incl',
            'total_wrapping_tax_excl', 'invoice_date', 'delivery_date',
            'customer_note', );
        if (version_compare(_PS_VERSION_, '1.7.8.0', '>='))
        {
            $fields[] = 'note';
        }
        $fields_address_delivery = array('del_address1', 'del_address2', 'del_postcode', 'del_city',
            'del_id_country', 'del_id_state', );
        $fields_address_invoice = array('inv_address1', 'inv_address2', 'inv_postcode', 'inv_city', 'inv_id_country',
            'inv_id_state', );

        sc_ext::readCustomOrdersGridsConfigXML('updateSettings');
        sc_ext::readCustomOrdersGridsConfigXML('onBeforeUpdateSQL');
        $todo = array();
        $todo_payment = array();
        $todo_address_delivery = array();
        $todo_address_invoice = array();
        foreach ($fields as $field)
        {
            if (isset($_POST[$field]) && $field == 'payment')
            {
                $todo[] = "payment='".psql(Tools::getValue($field))."'";
                $todo_payment[] = "payment_method='".psql(Tools::getValue($field))."'";
                addToHistory('order', 'modification', $field, (int) $id_order, 0, _DB_PREFIX_.'orders', psql(Tools::getValue($field)));
                continue;
            }
            if (isset($_POST[$field]) && $field == 'customer_note')
            {
                $order = new Order($id_order);

                $customer = new Customer((int) $order->id_customer);
                $customer->note = Tools::getValue($field);
                $customer->save();
                unset($_POST['customer_note']);
            }
            if (isset($_POST[$field]) && $field == 'shipping_number')
            {
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    $id_order_carrier = Db::getInstance()->getValue('
                        SELECT `id_order_carrier`
                        FROM `'._DB_PREFIX_.'order_carrier`
                        WHERE `id_order` = '.(int) $id_order.'
                        ORDER BY id_order_carrier ASC');
                    Db::getInstance()->Execute('
                        UPDATE `'._DB_PREFIX_.'order_carrier`
                        SET tracking_number = \''.psql(html_entity_decode(Tools::getValue($field))).'\'
                        WHERE id_order_carrier = '.(int) $id_order_carrier);
                }

                if (Tools::getValue($field) && _s('ORD_ORDPROP_SEND_TRACKING_MAIL'))
                {
                    $order = new Order($id_order);
                    $customer = new Customer((int) $order->id_customer);
                    $carrier = new Carrier((int) $order->id_carrier, $order->id_lang);
                    if (Validate::isLoadedObject($customer) && Validate::isLoadedObject($carrier))
                    {
                        $templateVars = array(
                                '{followup}' => str_replace('@', Tools::getValue($field), $carrier->url),
                                '{firstname}' => $customer->firstname,
                                '{lastname}' => $customer->lastname,
                                '{id_order}' => $order->id,
                        );
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $templateVars['{order_name}'] = $order->getUniqReference();
                            if ((version_compare(_PS_VERSION_, '1.6.0.0', '<')) || (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCI::getConfigurationValue('PS_MAIL_METHOD') != 3))
                            {
                                if (version_compare(_PS_VERSION_, '1.6.1.6', '>='))
                                {
                                    if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                                    {
                                        $_POST['setShopContext'] = 's-'.(int) $order->id_shop;
                                        $context = Context::getContext();
                                        $context->currency = Currency::getCurrencyInstance((int) $order->id_currency);
                                    }
                                    @Mail::Send((int) $order->id_lang, 'in_transit', SCI::translateSubjectMail('Package in transit', (int) $order->id_lang), $templateVars,
                                        $customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
                                        _PS_MAIL_DIR_, true, (int) $order->id_shop);
                                }
                                else
                                {
                                    @SCI::SendMail((int) $order->id_lang, 'in_transit', SCI::translateSubjectMail('Package in transit', (int) $order->id_lang), $templateVars,
                                    $customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
                                    _PS_MAIL_DIR_, true, (int) $order->id_shop);
                                }
                            }
                        }
                        else
                        {
                            @Mail::Send((int) $order->id_lang, 'in_transit', SCI::translateSubjectMail('Package in transit', (int) $order->id_lang), $templateVars,
                                $customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
                                _PS_MAIL_DIR_, true);
                        }
                    }
                }
            }
            if (isset($_POST[$field]) && $field == 'invoice_date' && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $id_order_invoice = Db::getInstance()->getValue('
                        SELECT `id_order_invoice`
                        FROM `'._DB_PREFIX_.'order_invoice`
                        WHERE `id_order` = '.(int) $id_order.'
                        ORDER BY id_order_invoice ASC');
                Db::getInstance()->Execute('
                        UPDATE `'._DB_PREFIX_.'order_invoice`
                        SET date_add = \''.psql(html_entity_decode(Tools::getValue($field))).'\'
                        WHERE id_order_invoice = '.(int) $id_order_invoice);
            }
            if (isset($_POST[$field]) && $field == 'delivery_date' && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $id_order_invoice = Db::getInstance()->getValue('
                        SELECT `id_order_invoice`
                        FROM `'._DB_PREFIX_.'order_invoice`
                        WHERE `id_order` = '.(int) $id_order.'
                        ORDER BY id_order_invoice ASC');
                Db::getInstance()->Execute('
                        UPDATE `'._DB_PREFIX_.'order_invoice`
                        SET delivery_date = \''.psql(html_entity_decode(Tools::getValue($field))).'\'
                        WHERE id_order_invoice = '.(int) $id_order_invoice);
            }
            if (isset($_POST[$field]) && $field == 'id_carrier')
            {
                $order = new Order($id_order);
                if (Validate::isLoadedObject($order))
                {
                    if (!$order->hasBeenShipped())
                    {
                        $id_order_carrier = Db::getInstance()->getValue('
                                SELECT `id_order_carrier`
                                FROM `'._DB_PREFIX_.'order_carrier`
                                WHERE `id_order` = '.(int) $id_order);
                        if ($id_order_carrier)
                        {
                            $order_carrier = new OrderCarrier($id_order_carrier);
                            $order_carrier->id_carrier = (int) Tools::getValue($field);
                            $order_carrier->save();
                        }
                        $order->id_carrier = (int) Tools::getValue($field);
                        $order->save();
                    }
                }
            }
            if (isset($_POST[$field]) && $field == 'status')
            {
                $isStatus = 1;
                $order_state = new OrderState(Tools::getValue($field));
                if (Validate::isLoadedObject($order_state))
                {
                    $order = new Order($id_order);
                    $result = Db::getInstance()->getRow('
                        SELECT `id_order_state`
                        FROM `'._DB_PREFIX_.'order_history`
                        WHERE `id_order` = '.(int) $id_order.'
                        ORDER BY `date_add` DESC, `id_order_history` DESC');
                    $current_order_state = new OrderState((int) $result['id_order_state']);
                    if ($current_order_state->id != $order_state->id)
                    {
                        // Create new OrderHistory
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
                        $history->id_employee = (int) $sc_agent->id_employee;
                        $use_existings_payment = false;
                        if ((version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !$order->hasInvoice()) || (version_compare(_PS_VERSION_, '1.5.0.0', '<') && $order->invoice_number))
                        {
                            $use_existings_payment = true;
                        }

                        if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                        {
                            $_POST['setShopContext'] = 's-'.(int) $order->id_shop;
                            $context = Context::getContext();
                            $context->currency = Currency::getCurrencyInstance((int) $order->id_currency);
                        }
                        $history->changeIdOrderState((int) $order_state->id, $order->id, $use_existings_payment);

                        $carrier = new Carrier($order->id_carrier, $order->id_lang);
                        $templateVars = array();

                        $tracking_number = '';
                        if (version_compare(_PS_VERSION_, '8.0.0', '>='))
                        {
                            $tracking_number = Db::getInstance()->getValue('SELECT tracking_number
                                                                                FROM '._DB_PREFIX_.'order_carrier
                                                                                WHERE id_order = '.(int) $order->id);
                        }
                        else
                        {
                            $tracking_number = $order->shipping_number;
                        }

                        if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $tracking_number)
                        {
                            $templateVars = array('{followup}' => str_replace('@', $tracking_number, $carrier->url));
                        }

                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            $context = Context::getContext();
                            $context->shop->id = $order->id_shop;
                            $addWithemail = $history->addWithemail(true, $templateVars, $context);
                        }
                        else
                        {
                            $addWithemail = $history->addWithemail(true, $templateVars);
                        }

                        // Save all changes
                        if ($addWithemail)
                        {
                            // synchronizes quantities if needed..
                            if (SCAS)
                            {
                                foreach ($order->getProducts() as $product)
                                {
                                    if (StockAvailable::dependsOnStock($product['product_id']))
                                    {
                                        StockAvailable::synchronize($product['product_id'], (int) $product['id_shop']);
                                    }
                                }
                            }
                        }

                        addToHistory('order', 'modification', 'current_state', (int) $id_order, 0, _DB_PREFIX_.'orders', $order_state->name[$order->id_lang].' (id_order #'.(int) $id_order.')', $current_order_state->name[$order->id_lang]);
                    }
                }
                continue;
            }
            if (isset($_POST[$field]))
            {
                switch ($field){
                    case 'total_discounts_tax_incl':
                        $todo[] = "total_discounts='".psql(Tools::getValue($field))."'";
                        break;
                    case 'total_paid_tax_incl':
                        $todo[] = "total_paid='".psql(Tools::getValue($field))."'";
                        break;
                    case 'total_shipping_tax_incl':
                        $todo[] = "total_shipping='".psql(Tools::getValue($field))."'";
                        break;
                    case 'total_wrapping_tax_incl':
                        $todo[] = "total_wrapping='".psql(Tools::getValue($field))."'";
                        break;
                    case 'note':
                        $todo[] = "note='".psql(strip_tags(Tools::getValue($field)))."'";
                        break;
                    default:
                        $todo[] = $field."='".psql(Tools::getValue($field))."'";
                }
                addToHistory('order', 'modification', $field, (int) $id_order, 0, _DB_PREFIX_.'orders', psql(Tools::getValue($field)));
            }
        }
        foreach ($fields_address_delivery as $field)
        {
            if (isset($_POST[$field]))
            {
                $order = new Order($id_order);
                $sql = 'select '.substr($field, 4).' from '._DB_PREFIX_.'address   WHERE id_address='.(int) $order->id_address_delivery;
                $oValue = Db::getInstance()->getValue($sql);
                $del_oValue = $oValue;
                $todo_address_delivery[] = substr($field, 4)."='".psql(Tools::getValue($field))."'";
                addToHistory('order', 'modification', $field, (int) $id_order, 0, _DB_PREFIX_.'address', psql(Tools::getValue($field)), $del_oValue);
            }
        }
        foreach ($fields_address_invoice as $field)
        {
            if (isset($_POST[$field]))
            {
                $order = new Order($id_order);
                $sql = 'select '.substr($field, 4).' from '._DB_PREFIX_.'address   WHERE id_address='.(int) $order->id_address_invoice;
                $oValue = Db::getInstance()->getValue($sql);
                $inv_oValue = $oValue;
                $todo_address_invoice[] = substr($field, 4)."='".psql(Tools::getValue($field))."'";
                addToHistory('order', 'modification', $field, (int) $id_order, 0, _DB_PREFIX_.'address', psql(Tools::getValue($field)), $inv_oValue);
            }
        }
        if (count($todo))
        {
            $todo[] = 'date_upd=NOW()';
            $sql = 'UPDATE '._DB_PREFIX_.'orders SET '.join(' , ', $todo).' WHERE id_order='.(int) $id_order;
            Db::getInstance()->Execute($sql);
        }
        if (count($todo_payment))
        {
            $sql = 'SELECT id_order_payment FROM '._DB_PREFIX_.'order_invoice_payment  WHERE id_order='.(int) $id_order.' LIMIT 1';
            $id_order_payment = Db::getInstance()->ExecuteS($sql);

            if (!empty($id_order_payment[0]['id_order_payment']))
            {
                $sql = 'UPDATE '._DB_PREFIX_.'order_payment SET '.join(' , ', $todo_payment).' WHERE id_order_payment='.(int) $id_order_payment[0]['id_order_payment'];
                Db::getInstance()->Execute($sql);
            }
        }
        if (count($todo_address_delivery))
        {
            $order = new Order($id_order);
            $todo_address_delivery[] = 'date_upd=NOW()';
            $sql4 = 'UPDATE '._DB_PREFIX_.'address SET '.join(' , ', $todo_address_delivery).' WHERE id_address='.(int) $order->id_address_delivery;
            Db::getInstance()->Execute($sql4);
        }
        if (count($todo_address_invoice))
        {
            $order = new Order($id_order);
            $todo_address_invoice[] = 'date_upd=NOW()';
            $sql5 = 'UPDATE '._DB_PREFIX_.'address SET '.join(' , ', $todo_address_invoice).' WHERE id_address='.(int) $order->id_address_invoice;
            Db::getInstance()->Execute($sql5);
        }

        sc_ext::readCustomOrdersGridsConfigXML('onAfterUpdateSQL');
        $newId = Tools::getValue('gr_id');
        $action = 'update';
    }

    sc_ext::readCustomGridsConfigXML('extraVars');

    if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
    {
        header('Content-type: application/xhtml+xml');
    }
    else
    {
        header('Content-type: text/xml');
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo '<data>';
    echo "<action type='".$action."' sid='".Tools::getValue('gr_id')."' is_status='".$isStatus."' tid='".$newId."'/>";
    echo $debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>' : '';
    echo $debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>' : '';
    echo $debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>' : '';
    echo $debug && isset($sql4) ? '<sql><![CDATA['.$sql4.']]></sql>' : '';
    echo $debug && isset($sql5) ? '<sql><![CDATA['.$sql5.']]></sql>' : '';

echo '</data>';
