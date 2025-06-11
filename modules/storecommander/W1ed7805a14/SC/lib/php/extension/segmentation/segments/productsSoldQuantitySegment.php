<?php

class productsSoldQuantitySegment extends SegmentCustom
{
    public $name = 'Products according to sold quantity';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = _l('Sold quantity').' <select name="operator" style="width: 15%">
            <option value="supp" '.(!empty($values['operator']) && $values['operator'] == 'supp' ? 'selected' : '').'>></option>
            <option value="inf" '.(!empty($values['operator']) && $values['operator'] == 'inf' ? 'selected' : '').'><</option>
            <option value="equal" '.(!empty($values['operator']) && $values['operator'] == 'equal' ? 'selected' : '').'>=</option>
            <option value="suppequal" '.(!empty($values['operator']) && $values['operator'] == 'suppequal' ? 'selected' : '').'>>=</option>
            <option value="infequal" '.(!empty($values['operator']) && $values['operator'] == 'infequal' ? 'selected' : '').'><=</option>
        </select>
        <input type="text" id="x_operator" name="x_operator" style="width: 20%;" value="'.(!empty($values['x_operator']) ? $values['x_operator'] : '').'" />
        <br/><strong>'._l('OR').'</strong><br/>
        '._l('Sold quantity between').' <input type="text" id="between_a" name="between_a" style="width: 20%;" value="'.(!empty($values['between_a']) ? $values['between_a'] : '').'" /> 
        '._l('and').' <input type="text" id="between_b" name="between_b" style="width: 20%;" value="'.(!empty($values['between_b']) ? $values['between_b'] : '').'" /> ';

        $html .= '<br/><br/><strong>'._l('Display products').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Active and nonactive').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Active only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonactive only').'</option>
        </select>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
        }

        $operation = '';
        if (!empty($auto_params['x_operator']))
        {
            if ($auto_params['operator'] == 'supp')
            {
                $operation = " > '".(int) $auto_params['x_operator']."' ";
            }
            elseif ($auto_params['operator'] == 'inf')
            {
                $operation = " < '".(int) $auto_params['x_operator']."' ";
            }
            elseif ($auto_params['operator'] == 'equal')
            {
                $operation = " = '".(int) $auto_params['x_operator']."' ";
            }
            elseif ($auto_params['operator'] == 'suppequal')
            {
                $operation = " >= '".(int) $auto_params['x_operator']."' ";
            }
            elseif ($auto_params['operator'] == 'infequal')
            {
                $operation = " <= '".(int) $auto_params['x_operator']."' ";
            }
        }
        elseif (!empty($auto_params['between_a']) && !empty($auto_params['between_b']))
        {
            $operation = " BETWEEN '".(int) $auto_params['between_a']."' AND '".(int) $auto_params['between_b']."'";
        }
        if (!empty($operation))
        {
            $sql = 'SELECT p.id_product
            FROM '._DB_PREFIX_.'product p
            WHERE 
            (SELECT SUM(od_seg_psq.product_quantity)
                            FROM '._DB_PREFIX_.'order_detail od_seg_psq
                                INNER JOIN '._DB_PREFIX_.'orders o_seg_psq ON (o_seg_psq.id_order=od_seg_psq.id_order)
                            WHERE od_seg_psq.product_id = p.id_product AND o_seg_psq.valid=1
                            GROUP BY od_seg_psq.product_id
                            ) '.$operation.' '.
                (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '');
            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row)
            {
                $type = _l('Product');
                if (SCMS)
                {
                    $element = new Product($row['id_product'], true);
                }
                else
                {
                    $element = new Product($row['id_product']);
                }
                $name = $element->name[$params['id_lang']];
                $infos = $element->reference;
                $array[] = array($type, $name, $infos, 'id' => 'product_'.$row['id_product'], 'id_display' => $row['id_product']);
            }
        }

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
        }

        $operation = '';
        if (!empty($auto_params['x_operator']))
        {
            if ($auto_params['operator'] == 'supp')
            {
                $operation = " > '".(int) $auto_params['x_operator']."' ";
            }
            elseif ($auto_params['operator'] == 'inf')
            {
                $operation = " < '".(int) $auto_params['x_operator']."' ";
            }
            elseif ($auto_params['operator'] == 'equal')
            {
                $operation = " = '".(int) $auto_params['x_operator']."' ";
            }
            elseif ($auto_params['operator'] == 'suppequal')
            {
                $operation = " >= '".(int) $auto_params['x_operator']."' ";
            }
            elseif ($auto_params['operator'] == 'infequal')
            {
                $operation = " <= '".(int) $auto_params['x_operator']."' ";
            }
        }
        elseif (!empty($auto_params['between_a']) && !empty($auto_params['between_b']))
        {
            $operation = " BETWEEN '".(int) $auto_params['between_a']."' AND '".(int) $auto_params['between_b']."'";
        }
        if (!empty($operation))
        {
            $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' ( (SELECT SUM(od_seg_psq.product_quantity)
                            FROM '._DB_PREFIX_.'order_detail od_seg_psq
                                INNER JOIN '._DB_PREFIX_.'orders o_seg_psq ON (o_seg_psq.id_order=od_seg_psq.id_order)
                            WHERE od_seg_psq.product_id = p.id_product AND o_seg_psq.valid=1
                            GROUP BY od_seg_psq.product_id
                            ) '.$operation.
                (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').' ) ';
        }
        else
        {
            $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' 0 ';
        }

        return $where;
    }
}
