<?php

class ordersBetween2DatesWithTimeSegment extends SegmentCustom
{
    public $name = 'Orders between 2 dates with time';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>'._l('Start date :').'</strong><br/>
        <input type="date" id="start_date" name="start_date" style="width: 50%;" value="'.(!empty($values['start_date']) ? $values['start_date'] : '').'" />
        <input type="time" id="start_time" name="start_time" style="width: 50%;" step="1" value="'.(!empty($values['start_time']) ? $values['start_time'] : '').'" /><br/>';
        $html .= '<strong>'._l('End date :').'</strong><br/>
        <input type="date" id="end_date" name="end_date" style="width: 50%;" value="'.(!empty($values['end_date']) ? $values['end_date'] : '').'" />
        <input type="time" id="end_time" name="end_time" style="width: 50%;" step="1" value="'.(!empty($values['end_time']) ? $values['end_time'] : '').'" />';

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
                $dateFrom = $auto_params['start_date'];
                $dateTo = $auto_params['end_date'];

                $timeFrom = (!empty($auto_params['start_time'])) ? $auto_params['start_time'] : '00:00:00';
                $timeTo = (!empty($auto_params['end_time'])) ? $auto_params['end_time'] : '23:59:59';

                try
                {
                    $from = new DateTime($dateFrom.' '.$timeFrom);
                    $to = new DateTime($dateTo.' '.$timeTo);
                }
                catch (Exception $e)
                {
                    $from = '';
                    $to = '';
                }

                if (!empty($from) && !empty($to))
                {
                    $sql = 'SELECT `id_order`
                        FROM `'._DB_PREFIX_.'orders`
                        WHERE `date_add` BETWEEN "'.pSQL($from->format('Y-m-d H:i:s')).'" AND "'.pSQL($to->format('Y-m-d H:i:s')).'"';

                    $res = Db::getInstance()->ExecuteS($sql);
                }
                if (isset($res) && !empty($res))
                {
                    foreach ($res as $row)
                    {
                        $type = _l('Orders');
                        $element = new Order($row['id_order']);
                        $name = $element->reference;
                        $infos = _l('Order placed ').$element->date_add;
                        $array[] = array($type, $name, $infos, 'id' => 'order_'.$row['id_order'], 'id_display' => $row['id_order']);
                    }
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
                $dateFrom = $auto_params['start_date'];
                $dateTo = $auto_params['end_date'];

                $timeFrom = (!empty($auto_params['start_time'])) ? $auto_params['start_time'] : '00:00:00';
                $timeTo = (!empty($auto_params['end_time'])) ? $auto_params['end_time'] : '23:59:59';

                try
                {
                    $from = new DateTime($dateFrom.' '.$timeFrom);
                    $to = new DateTime($dateTo.' '.$timeTo);
                }
                catch (Exception $e)
                {
                    $from = '';
                    $to = '';
                }

                if (!empty($from) && !empty($to))
                {
                    if (!empty($params['is_order']))
                    {
                        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' o.`date_add` BETWEEN "'.pSQL($from->format('Y-m-d H:i:s')).'" AND "'.pSQL($to->format('Y-m-d H:i:s')).'"';
                    }
                    else
                    {
                        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' od.id_order IN (SELECT `id_order`
                        FROM `'._DB_PREFIX_.'orders`
                        WHERE `date_add` BETWEEN "'.pSQL($from->format('Y-m-d H:i:s')).'" AND "'.pSQL($to->format('Y-m-d H:i:s')).'")';
                    }
                }
                else
                {
                    $where = ' FALSE ';
                }
            }
        }

        return $where;
    }
}
