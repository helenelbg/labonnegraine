<?php

class customersWithoutOrdersBetween2DatesSegment extends SegmentCustom
{
    public $name = 'Customers without order between 2 dates';
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
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['start_date']) && !empty($auto_params['end_date']))
            {
                $sql = 'SELECT c.id_customer, COUNT(o.id_order) AS nb_orders
                    FROM '._DB_PREFIX_.'customer c
                    LEFT JOIN '._DB_PREFIX_.'orders o ON (c.id_customer=o.id_customer 
                        AND o.valid=1
                        AND "'.pSQL($auto_params['start_date']).' 00:00:00" <= o.date_add
                        AND o.date_add <= "'.pSQL($auto_params['end_date']).' 00:00:00" )
                    GROUP BY c.id_customer
                    HAVING nb_orders = 0;';

                $res = Db::getInstance()->ExecuteS($sql);
                //echo $sql;
                foreach ($res as $row)
                {
                    $type = _l('Customer');
                    $element = new Customer($row['id_customer']);
                    $name = $element->firstname.' '.$element->lastname;
                    $infos = $element->email;
                    $array[] = array($type, $name, $infos, 'id' => 'customer_'.$row['id_customer'], 'id_display' => $row['id_customer']);
                }
            }
        }

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['start_date']) && !empty($auto_params['end_date']))
            {
                $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' (SELECT COUNT(o2.id_order) AS nb_orders
                            FROM '._DB_PREFIX_."orders o2
                            WHERE o2.id_customer = c.id_customer 
                                AND o2.valid=1
                                AND '".pSQL($auto_params['start_date'])." 00:00:00' <= o2.date_add
                                AND o2.date_add <= '".pSQL($auto_params['end_date'])." 00:00:00'
                            GROUP BY o2.id_customer
                            ) IS NULL ";
            }
        }

        return $where;
    }
}
