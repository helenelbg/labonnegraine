<?php

class ticketsWithoutAnswerSinceXDaysSegment extends SegmentCustom
{
    public $name = 'Tickets: Open tickets without response since X days';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>'._l('Number of days:').'</strong><br/>
        <input type="text" id="nb_day" name="nb_day" value="'.(!empty($values['nb_day']) ? (int) $values['nb_day'] : '').'" style="width: 100%;" />';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['nb_day']) && is_numeric($auto_params['nb_day']))
            {
                $sql = 'SELECT ct.id_customer_thread
                FROM '._DB_PREFIX_."customer_thread ct
                WHERE ct.status = 'open'
                    AND (SELECT COUNT(cm.id_customer_message) FROM "._DB_PREFIX_."customer_message cm WHERE ct.id_customer_thread = cm.id_customer_thread) = 1
                    AND ct.id_contact = 0
                    AND ct.date_add <= (SELECT DATE_ADD('".date('Y-m-d')." 00:00:00', INTERVAL -".(int)$auto_params['nb_day'].' DAY))';
                $res = Db::getInstance()->ExecuteS($sql);
                //echo $sql;die();
                foreach ($res as $row)
                {
                    $type = _l('Customer service');
                    $element = new CustomerThread($row['id_customer_thread']);
                    $customer = new Customer($element->id_customer);
                    $name = _l('Discussion with ').$customer->firstname.' '.$customer->lastname;
                    $infos = $element->date_add.' / '._l('Customer').' #'.$element->id_customer.' '.$customer->email;
                    $array[] = array($type, $name, $infos, 'id' => 'customer_service_'.$row['id_customer_thread'], 'id_display' => $row['id_customer_thread']);
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
            if (!empty($auto_params['nb_day']) && is_numeric($auto_params['nb_day']))
            {
                $where = ' '.(empty($params['no_operator']) ? 'AND' : '')." ( ct.status = 'open'
                            AND (SELECT COUNT(cm.id_customer_message) FROM "._DB_PREFIX_."customer_message cm WHERE ct.id_customer_thread = cm.id_customer_thread) = 1
                            AND ct.id_contact = 0
                            AND ct.date_add <= (SELECT DATE_ADD('".date('Y-m-d')." 00:00:00', INTERVAL -".(int)$auto_params['nb_day'].' DAY)) ) ';
            }
        }

        return $where;
    }
}
