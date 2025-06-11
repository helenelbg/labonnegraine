<?php

class customersWithAtLeastXOrdersSegment extends SegmentCustom
{
    public $name = 'Customers according to number of orders';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = _l('Number of orders').' <select name="operator" style="width: 15%">
            <option value="suppequal" '.(!empty($values['operator']) && $values['operator'] == 'suppequal' ? 'selected' : '').'>>=</option>
            <option value="infequal" '.(!empty($values['operator']) && $values['operator'] == 'infequal' ? 'selected' : '').'><=</option>
            <option value="supp" '.(!empty($values['operator']) && $values['operator'] == 'supp' ? 'selected' : '').'>></option>
            <option value="inf" '.(!empty($values['operator']) && $values['operator'] == 'inf' ? 'selected' : '').'><</option>
            <option value="equal" '.(!empty($values['operator']) && $values['operator'] == 'equal' ? 'selected' : '').'>=</option>
        </select>
        <input type="text" id="x_orders" name="x_orders" style="width: 20%;" value="'.(!empty($values['x_orders']) ? $values['x_orders'] : '').'" />
        <br/><strong>'._l('OR').'</strong><br/>
        '._l('Number of orders between').' <input type="text" id="between_a" name="between_a" style="width: 20%;" value="'.(!empty($values['between_a']) ? $values['between_a'] : '').'" /> 
        '._l('and').' <input type="text" id="between_b" name="between_b" style="width: 20%;" value="'.(!empty($values['between_b']) ? $values['between_b'] : '').'" /> ';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);

            $operation = '';
            if (!empty($auto_params['x_orders']))
            {
                if ($auto_params['operator'] == 'supp')
                {
                    $operation = " > '".(int) $auto_params['x_orders']."' ";
                }
                elseif ($auto_params['operator'] == 'inf')
                {
                    $operation = " < '".(int) $auto_params['x_orders']."' ";
                }
                elseif ($auto_params['operator'] == 'equal')
                {
                    $operation = " = '".(int) $auto_params['x_orders']."' ";
                }
                elseif ($auto_params['operator'] == 'suppequal')
                {
                    $operation = " >= '".(int) $auto_params['x_orders']."' ";
                }
                elseif ($auto_params['operator'] == 'infequal')
                {
                    $operation = " <= '".(int) $auto_params['x_orders']."' ";
                }
                else
                {
                    $operation = " >= '".(int) $auto_params['x_orders']."' ";
                }
            }
            elseif (!empty($auto_params['between_a']) && !empty($auto_params['between_b']))
            {
                $operation = " BETWEEN '".(int) $auto_params['between_a']."' AND '".(int) $auto_params['between_b']."'";
            }
            if (!empty($operation))
            {
                $sql = 'SELECT c.id_customer, COUNT(o.id_order) AS nb_orders
                    FROM '._DB_PREFIX_.'customer c
                    INNER JOIN '._DB_PREFIX_.'orders o ON (c.id_customer=o.id_customer AND o.valid=1)
                    GROUP BY c.id_customer
                    HAVING nb_orders '.$operation;

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
            $operation = '';
            if (!empty($auto_params['x_orders']))
            {
                if ($auto_params['operator'] == 'supp')
                {
                    $operation = " > '".(int) $auto_params['x_orders']."' ";
                }
                elseif ($auto_params['operator'] == 'inf')
                {
                    $operation = " < '".(int) $auto_params['x_orders']."' ";
                }
                elseif ($auto_params['operator'] == 'equal')
                {
                    $operation = " = '".(int) $auto_params['x_orders']."' ";
                }
                elseif ($auto_params['operator'] == 'suppequal')
                {
                    $operation = " >= '".(int) $auto_params['x_orders']."' ";
                }
                elseif ($auto_params['operator'] == 'infequal')
                {
                    $operation = " <= '".(int) $auto_params['x_orders']."' ";
                }
                else
                {
                    $operation = " >= '".(int) $auto_params['x_orders']."' ";
                }
            }
            elseif (!empty($auto_params['between_a']) && !empty($auto_params['between_b']))
            {
                $operation = " BETWEEN '".(int) $auto_params['between_a']."' AND '".(int) $auto_params['between_b']."'";
            }
            if (!empty($operation))
            {
                $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' (SELECT COUNT(o2.id_order) AS nb_orders
                            FROM '._DB_PREFIX_.'orders o2
                            WHERE o2.id_customer = c.id_customer AND o2.valid=1
                            GROUP BY o2.id_customer
                            ) '.$operation;
            }
        }

        return $where;
    }
}
