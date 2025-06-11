<?php

class productsWithMarginInferiorToXPercentSegment extends SegmentCustom
{
    public $name = 'Products with margin inferior to X percent';
    public $liste_hooks = ['segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid'];

    public function _executeHook_segmentAutoConfig($name, $params = [])
    {
        $values = [];
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = _l('Sell price inc. tax').' = '._l('Wholesale price').' + '._l('Margin').' (%)'.'<br/>'.
            _l('Margin').' <select name="operator" style="width: 15%">
            <option value="supp" '.(!empty($values['operator']) && $values['operator'] == 'supp' ? 'selected' : '').'>></option>
            <option value="inf" '.(!empty($values['operator']) && $values['operator'] == 'inf' ? 'selected' : '').'><</option>
        </select>
        <input type="text" id="x_operator" name="x_operator" style="width: 20%;" value="'.(!empty($values['x_operator']) ? $values['x_operator'] : '').'" />'.'%'.'
        <br/><strong>'._l('OR').'</strong><br/>
        '._l('Margin between').' <input type="text" id="between_a" name="between_a" style="width: 20%;" value="'.(!empty($values['between_a']) ? $values['between_a'] : '').'" /> 
        '._l('and').' <input type="text" id="between_b" name="between_b" style="width: 20%;" value="'.(!empty($values['between_b']) ? $values['between_b'] : '').'" /> '.'%';

        $html .= '<br/><br/><strong>'._l('Display products').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Active and nonactive').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Active only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonactive only').'</option>
        </select>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = [])
    {
        $array = [];

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
        }

        $operation = '';
        if (!empty($auto_params['x_operator']))
        {
            if ($auto_params['operator'] == 'supp')
            {
                $operation = ' > '.(float) $auto_params['x_operator'];
            }
            elseif ($auto_params['operator'] == 'inf')
            {
                $operation = ' < '.(float) $auto_params['x_operator'];
            }
        }
        elseif (!empty($auto_params['between_a']) && !empty($auto_params['between_b']))
        {
            $operation = ' BETWEEN '.(float) $auto_params['between_a'].' AND '.(float) $auto_params['between_b'];
        }
        if (!empty($operation))
        {
            $sql = 'SELECT p.id_product, ps.price AS pv, ps.wholesale_price AS pa, (ps.price - ps.wholesale_price) / (ps.wholesale_price / 100) AS margin
                    FROM ps_product p 
                    INNER JOIN ps_product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.SCI::getSelectedShop().')
                    WHERE (ps.price - ps.wholesale_price) / (ps.wholesale_price / 100) '.pSQL($operation).' '.
            (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND ps.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '');

            $res = Db::getInstance()->executeS($sql);
            foreach ($res as $row)
            {
                $type = _l('Product');
                $element = new Product($row['id_product'], SCMS);
                $name = $element->name[$params['id_lang']];
                $infos = $element->reference;
                $array[] = [$type, $name, $infos, 'id' => 'product_'.$row['id_product'], 'id_display' => $row['id_product']];
            }
        }

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = [])
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
                $operation = ' > '.(float) $auto_params['x_operator'];
            }
            elseif ($auto_params['operator'] == 'inf')
            {
                $operation = ' < '.(float) $auto_params['x_operator'];
            }
        }
        elseif (!empty($auto_params['between_a']) && !empty($auto_params['between_b']))
        {
            $operation = ' BETWEEN '.(float) $auto_params['between_a'].' AND '.(float) $auto_params['between_b'];
        }

        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' 0 ';
        if (!empty($operation))
        {
            $where = ' '.(empty($params['no_operator']) ? 'AND ' : '').'
                (prs.price - prs.wholesale_price) / (prs.wholesale_price / 100) '.pSQL($operation).' '.
                (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND prs.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '');
        }

        return $where;
    }
}
