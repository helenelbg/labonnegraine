<?php

class ordersBetween2DatesSegment extends SegmentCustom
{
    public $name = 'Orders between 2 dates';
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
                $sql = 'SELECT o.id_order
                FROM '._DB_PREFIX_."orders o
                WHERE o.valid=1
                        AND '".pSQL($auto_params['start_date'])." 00:00:00' <= o.date_add
                        AND o.date_add <= '".pSQL($auto_params['end_date'])." 00:00:00'";
                $res = Db::getInstance()->ExecuteS($sql);
                //echo $sql;die();
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
            if (!empty($auto_params['start_date']) && !empty($auto_params['end_date']))
            {
                $where = ' '.(empty($params['no_operator']) ? 'AND' : '')." ( o.valid=1
                        AND '".pSQL($auto_params['start_date'])." 00:00:00' <= o.date_add
                        AND o.date_add <= '".pSQL($auto_params['end_date'])." 00:00:00' ) ";
            }
        }

        return $where;
    }
}
