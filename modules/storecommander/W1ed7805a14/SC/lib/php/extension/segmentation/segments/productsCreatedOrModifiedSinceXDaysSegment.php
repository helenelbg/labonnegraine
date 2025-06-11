<?php

class productsCreatedOrModifiedSinceXDaysSegment extends SegmentCustom
{
    public $name = 'Products created or modified since X days';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = _l('Products').' <select name="dateToQuery" style="width: 40%">
            <option value="date_add" '.(!empty($values['dateToQuery']) && $values['dateToQuery'] == 'date_add' ? 'selected' : '').'>'. _l('Create date:') .'</option>
            <option value="date_upd" '.(!empty($values['dateToQuery']) && $values['dateToQuery'] == 'date_upd' ? 'selected' : '').'>'. _l('Modification date:') .'</option>
        </select>
        <select name="operator" style="width: 15%">
            <option value="supp" '.(!empty($values['operator']) && $values['operator'] == 'supp' ? 'selected' : '').'>></option>
            <option value="inf" '.(!empty($values['operator']) && $values['operator'] == 'inf' ? 'selected' : '').'><</option>
        </select>
        <br><br><strong>'._l('Nb days:').'</strong><br/>
        <input type="text" id="x_days" name="x_days" style="width: 40%;" value="'.(!empty($values['x_days']) ? $values['x_days'] : '').'" />';

        $html .= '<br><br><strong>'._l('Display products').'</strong><br/>
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
            $data_products = array();
            $auto_params = unserialize($params['auto_params']);

            $operation = '';
            if (!empty($auto_params['x_days']))
            {
                if ($auto_params['operator'] == 'supp')
                {
                    $operation = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.' : 'p.').$auto_params['dateToQuery'] .' > DATE_ADD(CURDATE(), INTERVAL -'.(int) $auto_params['x_days'].' DAY) ';
                }
                elseif ($auto_params['operator'] == 'inf')
                {
                    $operation = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps.' : 'p.').$auto_params['dateToQuery'] .' < DATE_ADD(CURDATE(), INTERVAL -'.(int) $auto_params['x_days'].' DAY) ';
                }
            }

            if (!empty($operation))
            {
                $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'ps.' : 'p.';

                $sql = 'SELECT DISTINCT(p.id_product)
                        FROM '._DB_PREFIX_.'product p
                        '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop ='.(int) SCI::getSelectedShop().') ' : '').'
                            WHERE '. pSQL($operation) .
                            ((!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all') ? " AND ".pSQL($alias)."active='".(($auto_params['active_pdt'] == 'active') ? '1' : '0'."'") : '').'
                        ORDER BY p.id_product';
                $res = Db::getInstance()->ExecuteS($sql);
                foreach ($res as $row)
                {
                    $type = _l('Product');
                    if (SCMS) {
                        $element = new Product($row['id_product'], true);
                    }
                    else
                    {
                        $element = new Product($row['id_product']);
                    }
                    $name = $element->name[$params['id_lang']];
                    //$infos = $element->reference;
                    $infos = $element->{$auto_params['dateToQuery']};
                    $data_products[] = array($type, $name, $infos, 'id' => 'product_'.$row['id_product'], 'id_display' => $row['id_product']);
                }
            }
        }
        return $data_products;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';

        $auto_params = unserialize($params['auto_params']);

        $operation = '';
        if (!empty($auto_params['x_days']))
        {
            if ($auto_params['operator'] == 'supp')
            {
                $operation = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps_seg.' : 'p_seg.').$auto_params['dateToQuery'] .' > DATE_ADD(CURDATE(), INTERVAL -'.(int) $auto_params['x_days'].' DAY) ';
            }
            elseif ($auto_params['operator'] == 'inf')
            {
                $operation = (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'ps_seg.' : 'p_seg.').$auto_params['dateToQuery'] .' < DATE_ADD(CURDATE(), INTERVAL -'.(int) $auto_params['x_days'].' DAY) ';
            }
            if (!empty($operation))
            {
                $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'ps_seg.' : 'p_seg.';

                $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' p.id_product IN (SELECT DISTINCT(p_seg.id_product)
                FROM '._DB_PREFIX_.'product p_seg
                '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN '._DB_PREFIX_.'product_shop ps_seg ON (p_seg.id_product = ps_seg.id_product AND ps_seg.id_shop ='.(int) SCI::getSelectedShop().') ' : '').'
                WHERE '. pSQL($operation).
                    ((!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all') ? " AND ".pSQL($alias)."active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').')';
            }
        }
        return $where;
    }
}
