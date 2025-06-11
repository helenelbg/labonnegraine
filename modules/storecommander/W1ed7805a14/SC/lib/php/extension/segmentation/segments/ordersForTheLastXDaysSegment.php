<?php

class ordersForTheLastXDaysSegment extends SegmentCustom
{
    public $name = 'Orders for the last X days';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>'._l('Number of days:').'</strong><br/>';
        $html .= '<input type="number" min="0" step="1" id="x_days" name="x_days" style="width: 50%;" value="'.(!empty($values['x_days']) ? $values['x_days'] : '').'" />';
        $html .= '<br><br><strong>'._l('Display orders').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Valid and nonvalid').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Valid only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonvalid only').'</option>
        </select>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['x_days']))
            {
                $date_from = new DateTime();
                $date_from->modify('-'.(int) $auto_params['x_days'].' day');

                $sql = 'SELECT o.id_order
                        FROM `'._DB_PREFIX_."orders` o
                            WHERE o.date_add >= '".pSQL($date_from->format('Y-m-d 00:00:00'))."'".
                            ((!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all') ? ' AND o.valid='.($auto_params['active_pdt'] == 'active' ? '1' : '0') : '');
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
            if (!empty($auto_params['x_days']))
            {
                $date_from = new DateTime();
                $date_from->modify('-'.(int) $auto_params['x_days'].' day');

                $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' ( 
                         o.date_add >= "'.pSQL($date_from->format('Y-m-d 00:00:00')).'"'.
                        ((!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all') ? ' AND o.valid='.($auto_params['active_pdt'] == 'active' ? '1' : '0') : '').') ';
            }
        }

        return $where;
    }
}
