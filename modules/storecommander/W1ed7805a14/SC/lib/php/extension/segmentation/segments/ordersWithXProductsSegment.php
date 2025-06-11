<?php

class ordersWithXProductsSegment extends SegmentCustom
{
    public $name = 'Orders with X products';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = _l('Number of products').' <select name="operator" style="width: 15%">
            <option value="suppequal" '.(!empty($values['operator']) && $values['operator'] == 'suppequal' ? 'selected' : '').'>>=</option>
            <option value="infequal" '.(!empty($values['operator']) && $values['operator'] == 'infequal' ? 'selected' : '').'><=</option>
            <option value="supp" '.(!empty($values['operator']) && $values['operator'] == 'supp' ? 'selected' : '').'>></option>
            <option value="inf" '.(!empty($values['operator']) && $values['operator'] == 'inf' ? 'selected' : '').'><</option>
            <option value="equal" '.(!empty($values['operator']) && $values['operator'] == 'equal' ? 'selected' : '').'>=</option>
        </select>
        <input type="text" id="x_products" name="x_products" style="width: 20%;" value="'.(!empty($values['x_products']) ? $values['x_products'] : '').'" />
        <br/><strong>'._l('OR').'</strong><br/>
        '._l('Number of products between').' <input type="text" id="between_a" name="between_a" style="width: 20%;" value="'.(!empty($values['between_a']) ? $values['between_a'] : '').'" /> 
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
            if (!empty($auto_params['x_products']))
            {
                if ($auto_params['operator'] == 'supp')
                {
                    $operation = " > '".(int) $auto_params['x_products']."' ";
                }
                elseif ($auto_params['operator'] == 'inf')
                {
                    $operation = " < '".(int) $auto_params['x_products']."' ";
                }
                elseif ($auto_params['operator'] == 'equal')
                {
                    $operation = " = '".(int) $auto_params['x_products']."' ";
                }
                elseif ($auto_params['operator'] == 'suppequal')
                {
                    $operation = " >= '".(int) $auto_params['x_products']."' ";
                }
                elseif ($auto_params['operator'] == 'infequal')
                {
                    $operation = " <= '".(int) $auto_params['x_products']."' ";
                }
                else
                {
                    $operation = " >= '".(int) $auto_params['x_products']."' ";
                }
            }
            elseif (!empty($auto_params['between_a']) && !empty($auto_params['between_b']))
            {
                $operation = " BETWEEN '".(int) $auto_params['between_a']."' AND '".(int) $auto_params['between_b']."'";
            }

            if (!empty($operation))
            {
                $sql = 'SELECT DISTINCT(o.id_order)
                FROM '._DB_PREFIX_.'orders o
                WHERE id_order IN (
                    SELECT od.id_order FROM '._DB_PREFIX_.'order_detail od GROUP BY od.id_order  
                    HAVING SUM(od.product_quantity) '.$operation.' )';
                $res = Db::getInstance()->ExecuteS($sql);
                foreach ($res as $row)
                {
                    $type = _l('Order');
                    $element = new Order($row['id_order']);
                    $name = $element->reference;
                    $infos = _l('Order placed ').$element->date_add;
                    $array[] = array($type, $name, $infos, 'id' => 'order_'.$row['id_order'], 'id_display' => $row['id_order']);
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
            if (!empty($auto_params['x_products']))
            {
                if ($auto_params['operator'] == 'supp')
                {
                    $operation = " > '".(int) $auto_params['x_products']."' ";
                }
                elseif ($auto_params['operator'] == 'inf')
                {
                    $operation = " < '".(int) $auto_params['x_products']."' ";
                }
                elseif ($auto_params['operator'] == 'equal')
                {
                    $operation = " = '".(int) $auto_params['x_products']."' ";
                }
                elseif ($auto_params['operator'] == 'suppequal')
                {
                    $operation = " >= '".(int) $auto_params['x_products']."' ";
                }
                elseif ($auto_params['operator'] == 'infequal')
                {
                    $operation = " <= '".(int) $auto_params['x_products']."' ";
                }
                else
                {
                    $operation = " >= '".(int) $auto_params['x_products']."' ";
                }
            }
            elseif (!empty($auto_params['between_a']) && !empty($auto_params['between_b']))
            {
                $operation = " BETWEEN '".(int) $auto_params['between_a']."' AND '".(int) $auto_params['between_b']."'";
            }

            if (!empty($operation))
            {
                if (!empty($params['is_order']))
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' o.id_order IN (SELECT DISTINCT(o.id_order)
                        FROM '._DB_PREFIX_.'orders o
                        WHERE id_order IN (
                            SELECT od.id_order FROM '._DB_PREFIX_.'order_detail od GROUP BY od.id_order  
                            HAVING SUM(od.product_quantity) '.$operation.' )
                        )';
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' od.id_order IN (SELECT DISTINCT(o.id_order)
                        FROM '._DB_PREFIX_.'orders o
                        WHERE id_order IN (
                            SELECT od.id_order FROM '._DB_PREFIX_.'order_detail od GROUP BY od.id_order  
                            HAVING SUM(od.product_quantity) '.$operation.' )
                        )';
                }
            }
        }

        return $where;
    }
}
