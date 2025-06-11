<?php

class customersWithProductsInCartBetween2DatesSegment extends SegmentCustom
{
    public $name = 'Customers with at least one product in cart between 2 dates';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>'._l('Start date (format: 2020-05-15):').'</strong><br/>
        <input type="text" id="start_date" name="start_date" style="width: 100%;" value="'.(!empty($values['start_date']) ? $values['start_date'] : '').'" />';

        $html .= '<strong>'._l('End date (format: 2020-05-15):').'</strong><br/>
        <input type="text" id="end_date" name="end_date" style="width: 100%;" value="'.(!empty($values['end_date']) ? $values['end_date'] : '').'" />';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $data_customers = array();
        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['start_date']) && !empty($auto_params['end_date']))
            {
                $sql = 'SELECT c.id_customer
                    FROM '._DB_PREFIX_.'customer c
                    INNER JOIN '._DB_PREFIX_.'cart ca ON (c.id_customer=ca.id_customer 
                        AND ca.date_add BETWEEN "'.pSQL($auto_params['start_date']).' 00:00:00" AND "'.pSQL($auto_params['end_date']).' 00:00:00" 
                        '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'AND ca.id_shop = '.(int) SCI::getSelectedShop().' ' : '').')
                    INNER JOIN '._DB_PREFIX_.'cart_product cap ON (cap.id_cart=ca.id_cart)
                    WHERE  cap.quantity != 0 AND NOT EXISTS (SELECT 1 FROM '._DB_PREFIX_.'orders o 
                                WHERE o.`id_cart` = ca.`id_cart`
                                AND o.`id_customer`=ca.id_customer)
                    GROUP BY c.id_customer';
                $res = Db::getInstance()->ExecuteS($sql);
                foreach ($res as $row)
                {
                    $type = _l('Customer');
                    $element = new Customer($row['id_customer']);
                    $name = $element->firstname.' '.$element->lastname;
                    $infos = $element->email;
                    $data_customers[] = array($type, $name, $infos, 'id' => 'customer_'.$row['id_customer'], 'id_display' => $row['id_customer']);
                }
            }
        }

        return $data_customers;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['start_date']) && !empty($auto_params['end_date']))
            {
                $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' (
                    SELECT ca2.id_customer FROM '._DB_PREFIX_.'cart ca2
                    INNER JOIN '._DB_PREFIX_.'cart_product cap2 ON (cap2.id_cart=ca2.id_cart)
                    WHERE  (c.id_customer=ca2.id_customer
                        AND ca2.date_add BETWEEN "'.pSQL($auto_params['start_date']).' 00:00:00" AND "'.pSQL($auto_params['end_date']).' 00:00:00"
                           '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'AND ca2.id_shop = '.(int) SCI::getSelectedShop().' ' : '').')
                        AND cap2.quantity != 0 
                        AND NOT EXISTS (SELECT 1 FROM '._DB_PREFIX_.'orders o2
                                WHERE o2.`id_cart` = ca2.`id_cart`
                                AND o2.`id_customer`=ca2.id_customer)
                    GROUP BY ca2.id_customer
                            ) IS NOT NULL ';
            }
        }
        return $where;
    }
}
