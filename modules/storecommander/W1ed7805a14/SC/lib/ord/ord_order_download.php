<?php

// M4PDF Module compatibility
    $type = Tools::getValue('type', 0);
    $orders = explode(',', Tools::getValue('orders', ''));
    if (count($orders) && $orders[0] == '')
    {
        $orders = array();
    }

    function multipleInvoices($invoices)
    {
        $shop = new Shop((int) SCI::getSelectedShop());
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $sql_in = implode(',', $invoices);
            $order_invoice_list_temp = Db::getInstance()->executeS('
                SELECT oi.*
                FROM `'._DB_PREFIX_.'order_invoice` oi
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = oi.`id_order`)
                WHERE oi.id_order IN ('.pInSQL($sql_in).')
                ORDER BY oi.date_add ASC
            ');

            $order_invoice_list_byorder = array();
            foreach ($order_invoice_list_temp as $order_invoice)
            {
                $order_invoice_list_byorder[$order_invoice['id_order']] = $order_invoice;
            }
            $order_invoice_list = array();
            foreach ($invoices as $id)
            {
                if (array_key_exists($id, $order_invoice_list_byorder))
                {
                    $order_invoice_list[] = $order_invoice_list_byorder[$id];
                }
            }

            $order_invoice_collection = ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);
            if (!count($order_invoice_collection))
            {
                echo 'Nothing to download';
            }
            else
            {
                if (version_compare(_PS_VERSION_, '1.7.6.0', '>='))
                {
                    $symfony_container = PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance();
                    $pdf_generator = $symfony_container->get('prestashop.adapter.pdf.generator.invoice');
                    $pdf_generator->generatePDF($order_invoice_collection);
                }
                else
                {
                    if (SCI::getConfigurationValue('M4PDF_PDF_INVOICES') && _s('ORD_EXPORT_USE_M4PDF'))
                    {
                        require_once _PS_MODULE_DIR_.'m4pdf/m4pdf.php';
                        if (!class_exists('M4OrderDetail'))
                        {
                            require_once _PS_MODULE_DIR_.'m4pdf/M4OrderDetail.php';
                        }
                        $invoices = array();
                        foreach ($order_invoice_list as $r)
                        {
                            $invoices[] = $r['id_order'];
                        }
                        $_POST['submitFilterorder'] = 0;
                        $_POST['orderFilter_id_order'] = $invoices;
                        $object = new M4OrderDetail();
                        $object->generatePDF('invoices.pdf', 'D', SCI::getConfigurationValue('M4TPL_PDF_INVOICE', null, $shop->id_shop_group, $shop->id));
                    }
                    else
                    {
                        $pdf = new PDF($order_invoice_collection, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
                        $pdf->render();
                    }
                }
            }
        }
        else
        {
            if (SCI::getConfigurationValue('M4PDF_PDF_INVOICES') && _s('ORD_EXPORT_USE_M4PDF'))
            {
                require_once _PS_MODULE_DIR_.'m4pdf/m4pdf.php';
                if (!class_exists('M4OrderDetail'))
                {
                    require_once _PS_MODULE_DIR_.'m4pdf/M4OrderDetail.php';
                }
                $_POST['submitFilterorder'] = 0;
                $_POST['orderFilter_id_order'] = $invoices;
                $object = new M4OrderDetail();
                $object->generatePDF('invoices.pdf', 'D', SCI::getConfigurationValue('M4TPL_PDF_INVOICE', null, $shop->id_shop_group, $shop->id));
            }
            else
            {
                $pdf = new PDF('P', 'mm', 'A4');
                $res = array();
                foreach ($invoices as $id_order)
                {
                    $orderObj = new Order((int) $id_order);
                    if (Validate::isLoadedObject($orderObj))
                    {
                        PDF::invoice($orderObj, 'D', true, $pdf);
                        $res[] = $orderObj->invoice_number;
                    }
                }
                if (count($res))
                {
                    return $pdf->Output('invoices.pdf', 'D');
                }
            }
            echo 'Nothing to download';
        }
    }

    function multipleOrderSlips($orderSlips)
    {
        $pdf = new PDF('P', 'mm', 'A4');
        sort($orderSlips);
        foreach ($orderSlips as $id_order_slip)
        {
            $orderSlip = new OrderSlip((int) $id_order_slip);
            $order = new Order((int) $orderSlip->id_order);
            $order->products = OrderSlip::getOrdersSlipProducts($orderSlip->id, $order);
            if (Validate::isLoadedObject($orderSlip) and Validate::isLoadedObject($order))
            {
                PDF::invoice($order, 'D', true, $pdf, $orderSlip);
            }
        }

        return $pdf->Output('order_slips.pdf', 'D');
    }

    function multipleDelivery($orders)
    {
        $shop = new Shop((int) SCI::getSelectedShop());
        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
        {
            $sql_in = implode(',', $orders);
            $order_invoice_list_temp = Db::getInstance()->executeS('
                SELECT oi.*
                FROM `'._DB_PREFIX_.'order_invoice` oi
                LEFT JOIN `'._DB_PREFIX_.'orders` o ON (o.`id_order` = oi.`id_order`)
                WHERE oi.id_order IN ('.pInSQL($sql_in).')
                    '.((version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? ' AND oi.delivery_number IS NOT NULL AND oi.delivery_number>0 ' : '').'
                ORDER BY '.(_s('ORD_EXPORT_DELIVERY_SORT') == '2' ? 'oi.id_order' : 'oi.delivery_number').' ASC
            ');
//                ORDER BY oi.delivery_date ASC
//                ORDER BY oi.id_order ASC

            $order_invoice_list_byorder = array();
            foreach ($order_invoice_list_temp as $order_invoice)
            {
                $order_invoice_list_byorder[$order_invoice['id_order']] = $order_invoice;
            }
            $order_invoice_list = array();
            foreach ($orders as $id)
            {
                if (array_key_exists($id, $order_invoice_list_byorder))
                {
                    $order_invoice_list[] = $order_invoice_list_byorder[$id];
                }
            }

            $order_invoice_collection = ObjectModel::hydrateCollection('OrderInvoice', $order_invoice_list);

            if (!count($order_invoice_collection))
            {
                echo 'Nothing to download';
            }
            else
            {
                // M4PDF Module compatibility
                if (version_compare(_PS_VERSION_, '1.7.6.0', '>='))
                {
                    $context = Context::getContext();
                    $context->currency = Currency::getCurrencyInstance((int) Configuration::get('PS_CURRENCY_DEFAULT'));
                    $pdf = new PDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP, $context->smarty);
                    $pdf->render();
                }
                else
                {
                    if (SCI::getConfigurationValue('M4PDF_PDF_DELIVERYSLIPS') && _s('ORD_EXPORT_USE_M4PDF'))
                    {
                        require_once _PS_MODULE_DIR_.'m4pdf/m4pdf.php';
                        if (!class_exists('M4OrderDetail'))
                        {
                            require_once _PS_MODULE_DIR_.'m4pdf/M4OrderDetail.php';
                        }
                        $invoices = array();
                        foreach ($order_invoice_list as $r)
                        {
                            $invoices[] = $r['id_order'];
                        }
                        $_POST['submitFilterorder'] = 0;
                        $_POST['orderFilter_id_order'] = $invoices;
                        $object = new M4OrderDetail();
                        $object->generatePDF('delivery.pdf', 'D', SCI::getConfigurationValue('M4TPL_PDF_DELIVERYSLIPS', null, $shop->id_shop_group, $shop->id));
                    }
                    else
                    {
                        $pdf = new PDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP, Context::getContext()->smarty);
                        $pdf->render();
                    }
                }
            }
        }
        else
        {
            // M4PDF Module compatibility
            if (SCI::getConfigurationValue('M4PDF_PDF_DELIVERYSLIPS') && _s('ORD_EXPORT_USE_M4PDF'))
            {
                require_once _PS_MODULE_DIR_.'m4pdf/m4pdf.php';
                if (!class_exists('M4OrderDetail'))
                {
                    require_once _PS_MODULE_DIR_.'m4pdf/M4OrderDetail.php';
                }
                $_POST['submitFilterorder'] = 0;
                $_POST['orderFilter_id_order'] = $orders;
                $object = new M4OrderDetail();
                $object->generatePDF('delivery.pdf', 'D', SCI::getConfigurationValue('M4TPL_PDF_DELIVERYSLIPS', null, $shop->id_shop_group, $shop->id));
            }
            else
            {
                $pdf = new PDF('P', 'mm', 'A4');
                $res = array();
                sort($orders);
                foreach ($orders as $id_order)
                {
                    $orderObj = new Order((int) $id_order);
                    if (Validate::isLoadedObject($orderObj) && $orderObj->delivery_number > 0)
                    {
                        PDF::invoice($orderObj, 'D', true, $pdf, false, $orderObj->delivery_number);
                        $res[] = $orderObj->delivery_number;
                    }
                }
                if (count($res))
                {
                    return $pdf->Output('delivery.pdf', 'D');
                }
            }
            echo 'Nothing to download';
        }
    }

    switch ($type){
        case 'download_invoice':
            multipleInvoices($orders);
            break;
        case 'download_delivery':
            multipleDelivery($orders);
            break;
        case 'download_slips':
            multipleOrderSlips($orders);
            break;
    }
