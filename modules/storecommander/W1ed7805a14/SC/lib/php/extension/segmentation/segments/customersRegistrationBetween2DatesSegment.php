<?php

class customersRegistrationBetween2DatesSegment extends SegmentCustom
{
    public $name = 'Customers registered between 2 dates';
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
                $from = new DateTime($auto_params['start_date']);
                $to = new DateTime($auto_params['end_date']);
                $sql = 'SELECT `id_customer`
                        FROM `'._DB_PREFIX_.'customer`
                        WHERE `date_add` BETWEEN "'.pSQL($from->format('Y-m-d')).'" AND "'.pSQL($to->format('Y-m-d')).'"';

                $res = Db::getInstance()->ExecuteS($sql);
                if (!empty($res))
                {
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
                $from = new DateTime($auto_params['start_date']);
                $to = new DateTime($auto_params['end_date']);
                if (!empty($params['is_customer']))
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' c.`date_add` BETWEEN "'.pSQL($from->format('Y-m-d')).'" AND "'.pSQL($to->format('Y-m-d')).'"';
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' a.id_customer IN (SELECT cu.`id_customer`
                                FROM `'._DB_PREFIX_.'customer` cu
                                WHERE cu.`date_add` BETWEEN "'.pSQL($from->format('Y-m-d')).'" AND "'.pSQL($to->format('Y-m-d')).'")';
                }
            }
        }

        return $where;
    }
}
