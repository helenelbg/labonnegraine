<?php

class ticketsWithIdEmployeeSegment extends SegmentCustom
{
    public $name = 'Tickets: Open tickets of employee X';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $html = '<strong>'._l('Employee:').'</strong><br/>
        <select id="id_employee" name="id_employee" style="width: 100%;">
            <option value="">--</option>';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $rows = Employee::getEmployees();
        foreach ($rows as $row)
        {
            $html .= '<option value="'.$row['id_employee'].'" '.($row['id_employee'] == $values['id_employee'] ? 'selected' : '').'>'.$row['firstname'].' '.$row['lastname'].'</option>';
        }
        $html .= '</select></select>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['id_employee']))
            {
                $sql = 'SELECT DISTINCT(ct.id_customer_thread)
                FROM '._DB_PREFIX_.'customer_thread ct
                    LEFT JOIN '._DB_PREFIX_."customer_message cm ON (ct.id_customer_thread = cm.id_customer_thread)
                WHERE ct.status = 'open'
                    AND (
                            ct.id_contact = ".(int)$auto_params['id_employee']."
                            OR
                            cm.id_employee = ".(int)$auto_params['id_employee']."
                        )";
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
            if (!empty($auto_params['id_employee']))
            {
                $where = ' '.(empty($params['no_operator']) ? 'AND' : '')." ( ct.status = 'open'
                        AND (
                            ct.id_contact = ".(int)$auto_params['id_employee']."
                            OR
                            cm.id_employee = ".(int)$auto_params['id_employee']."
                        )) ";
            }
        }

        return $where;
    }
}
