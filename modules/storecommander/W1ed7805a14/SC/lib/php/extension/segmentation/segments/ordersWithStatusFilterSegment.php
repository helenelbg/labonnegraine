<?php

class ordersWithStatusFilterSegment extends SegmentCustom
{
    public $name = 'Orders with status filter';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        global $id_lang;

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>'._l('Option filters').':</strong><br/>
        <div style="display: flex;align-items: center;"><input type="checkbox" name="use_filters" '.($values['use_filters'] == 1 ? 'checked=checked' : '').'>'._l('Use order filters').'</div>';

        $html .= '<strong>'._l('Searched status:').'</strong><br/>
        <select id="id_searched_status" name="id_searched_status" style="width: 100%;height:5em" multiple="multiple">
            <option value="">--</option>';

        $id_searched_status = explode(',', $values['id_searched_status']);
        $sql = 'SELECT DISTINCT os.id_order_state,os.name FROM '._DB_PREFIX_.'order_state_lang AS os, '._DB_PREFIX_.'order_history AS oh WHERE oh.id_order_state = os.id_order_state AND os.id_lang = '.(int)$id_lang.' ORDER BY os.name';
        $rows = Db::getInstance()->ExecuteS($sql);

        foreach ($rows as $row)
        {
            $html .= '<option value="'.$row['id_order_state'].'" '.(in_array($row['id_order_state'], $id_searched_status) ? 'selected' : '').'>'.$row['name'].'</option>';
        }

        $html .= '</select>';

        $html .= '<strong>'._l('Current status to include:').'</strong><br/>
        <select id="id_include_status" name="id_include_status" style="width: 100%;height:5em" multiple="multiple">
            <option value="">--</option>';
        $id_include_status = explode(',', $values['id_include_status']);
        $sql2 = 'SELECT DISTINCT os.id_order_state,os.name 
        FROM '._DB_PREFIX_.'order_state_lang AS os
        WHERE 
            os.id_lang = '.(int)$id_lang.' 
        ORDER BY os.name';

        $rows2 = Db::getInstance()->ExecuteS($sql2);

        foreach ($rows2 as $row2)
        {
            $html .= '<option value="'.$row2['id_order_state'].'" '.(in_array($row2['id_order_state'], $id_include_status) ? 'selected' : '').'>'.$row2['name'].'</option>';
        }
        $html .= '</select>';

        $html .= '<strong>'._l('Current status to exclude:').'</strong><br/>
    
        <select id="id_exclude_status" name="id_exclude_status" style="width: 100%;height:5em" multiple="multiple">
            <option value="">--</option>';
        $id_exclude_status = explode(',', $values['id_exclude_status']);
        foreach ($rows2 as $row2)
        {
            $html .= '<option value="'.$row2['id_order_state'].'" '.(in_array($row2['id_order_state'], $id_exclude_status) ? 'selected' : '').'>'.$row2['name'].'</option>';
        }
        $html .= '</select>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);

            if ((!empty($auto_params['id_searched_status'])) || (!empty($auto_params['id_include_status'])) || (!empty($auto_params['id_exclude_status'])))
            {
                $sql = 'SELECT DISTINCT o.id_order 
                FROM '._DB_PREFIX_.'orders AS o ';

                if (!empty($auto_params['id_searched_status']))
                {
                    $sql .= ' INNER JOIN '._DB_PREFIX_.'order_history AS oh ON oh.id_order = o.id_order  ';
                }

                $sql .= ' WHERE 1=1 ';

                if (!empty($auto_params['id_searched_status']))
                {
                    $sql .= ' AND oh.id_order_state IN ( '.pInSQL($auto_params['id_searched_status']).') ';
                }
                if (!empty($auto_params['id_include_status']))
                {
                    $sql .= ' AND o.current_state IN ('.pInSQL($auto_params['id_include_status']).') ';
                }
                if (!empty($auto_params['id_exclude_status']))
                {
                    $sql .= ' AND o.current_state NOT IN ('.pInSQL($auto_params['id_exclude_status']).') ';
                }

                $sql .= ' LIMIT 500';
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
            if ((!empty($auto_params['id_searched_status'])) || (!empty($auto_params['id_include_status'])) || (!empty($auto_params['id_exclude_status'])))
            {
                $sql = 'SELECT DISTINCT o.id_order 
                FROM '._DB_PREFIX_.'orders AS o ';

                if (!empty($auto_params['id_searched_status']))
                {
                    $sql .= ' INNER JOIN '._DB_PREFIX_.'order_history AS oh ON oh.id_order = o.id_order  ';
                }

                $sql .= ' WHERE 1=1 ';

                if (!empty($auto_params['id_searched_status']))
                {
                    $sql .= ' AND oh.id_order_state IN ( '.pInSQL($auto_params['id_searched_status']).') ';
                }
                if (!empty($auto_params['id_include_status']))
                {
                    $sql .= ' AND o.current_state IN ('.pInSQL($auto_params['id_include_status']).') ';
                }
                if (!empty($auto_params['id_exclude_status']))
                {
                    $sql .= ' AND o.current_state NOT IN ('.pInSQL($auto_params['id_exclude_status']).') ';
                }

                if (!empty($params['is_order']))
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' o.id_order IN ('.$sql.')';
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' od.id_order IN ('.$sql.')';
                }
            }
        }

        return $where;
    }
}
