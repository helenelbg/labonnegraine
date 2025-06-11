<?php

class customersWithoutOrdersSinceXDaysSegment extends SegmentCustom
{
    public $name = 'Customers without order since X days';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>'._l('Nb days:').'</strong><br/>
        <input type="text" id="x_days" name="x_days" style="width: 100%;" value="'.(!empty($values['x_days']) ? $values['x_days'] : '').'" />';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['x_days']) && is_numeric($auto_params['x_days']))
            {
                $sql = 'SELECT c.id_customer, MAX(o.date_add) AS last_order
                    FROM '._DB_PREFIX_.'customer c
                    INNER JOIN '._DB_PREFIX_.'orders o ON (c.id_customer=o.id_customer AND o.valid=1)
                    GROUP BY c.id_customer
                    HAVING last_order < (SELECT DATE_ADD("'.date('Y-m-d 00:00:00').'", INTERVAL -'.(int)$auto_params['x_days'].' DAY));';

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
            if (!empty($auto_params['x_days']) && is_numeric($auto_params['x_days']))
            {
                $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' (SELECT MAX(o2.date_add) AS last_order
                            FROM '._DB_PREFIX_."orders o2
                            WHERE o2.id_customer = c.id_customer AND o2.valid=1
                            GROUP BY o2.id_customer
                            ) < (SELECT DATE_ADD('".date('Y-m-d 00:00:00')."', INTERVAL -".(int)$auto_params['x_days'].' DAY))';
            }
        }

        return $where;
    }
}
