<?php

    $id_lang = 2;
    $id_commission = (int) Tools::getValue('gr_id', 0);

    if (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'inserted')
    {
        $id_partner = (int) Tools::getValue('id_partner', 0);

        $id_shop = 0;
        if (!empty($id_partner))
        {
            $partner = new SCAffPartner((int) $id_partner);
            $customer = new Customer((int) $partner->customer_id);

            if (!empty($customer->id_shop))
            {
                $id_shop = $customer->id_shop;
            }
        }

        $commission = new SCAffCommission();
        $commission->customer_id = 0;
        $commission->id_partner = (int) $id_partner;
        $commission->order_id = 0;
        $commission->date_add = date('Y-m-d');
        $commission->active = 1;
        $commission->status = 'waiting';
        $commission->price = 0.0;
        if (SCMS)
        {
            $commission->id_shop = $id_shop;
        }
        $commission->save();

        $newId = $commission->id;
        $action = 'insert';
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'updated')
    {
        $fields = array('id_partner', 'id_shop', 'status', 'price', 'order_id', 'hidden', 'date_add');
        $todo = array();
        foreach ($fields as $field)
        {
            if (isset($_POST[$field]))
            {
                $val = Tools::getValue($field);
                $todo[] = $field."='".psql(html_entity_decode($val))."'";

                if ($field == 'order_id')
                {
                    $order = new Order($val);
                    if (!empty($order->id_customer))
                    {
                        $todo[] = "customer_id=" .(int) $order->id_customer . "";
                    }
                }
            }
        }
        if (count($todo))
        {
            $sql = 'UPDATE '._DB_PREFIX_.'scaff_commission SET '.join(' , ', $todo).", active='1' WHERE id_commission=".(int) $id_commission;
            Db::getInstance()->Execute($sql);
        }
        $newId = Tools::getValue('gr_id');
        $action = 'update';
    }
    elseif (isset($_POST['!nativeeditor_status']) && trim($_POST['!nativeeditor_status']) == 'deleted')
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'scaff_commission WHERE id_commission='.(int) $id_commission;
        Db::getInstance()->Execute($sql);

        $newId = Tools::getValue('gr_id');
        $action = 'delete';
    }

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
    echo "<action type='".$action."' sid='".Tools::getValue('gr_id')."' tid='".$newId."'/>";
    echo $debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>' : '';
    echo $debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>' : '';
    echo $debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>' : '';
    echo '</data>';
