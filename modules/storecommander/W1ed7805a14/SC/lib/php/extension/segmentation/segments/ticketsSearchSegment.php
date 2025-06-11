<?php

class ticketsSearchSegment extends SegmentCustom
{
    public $name = 'Tickets: expression search';
    public $liste_hooks = array(
            'segmentAutoConfig',
            'segmentAutoSqlQuery',
            'segmentAutoSqlQueryGrid',
        );

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $search_fields = array();
        if (!empty($values['search_fields']))
        {
            $search_fields = explode('-', $values['search_fields']);
        }

        $html = '<strong>'._l('What term do you want to look for?').'</strong><br/>
        <input type="text" name="search_words" value="'.((!empty($values['search_words'])) ? $values['search_words'] : '').'" style="width: 100%;" />';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['search_words']))
            {
                $search = $auto_params['search_words'];

                $sql = 'SELECT DISTINCT(id_customer_thread)
                FROM '._DB_PREFIX_."customer_message
                WHERE LOWER(message) LIKE '%".pSQL(strtolower($search))."%'";
                $res = Db::getInstance()->ExecuteS($sql);
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
            if (!empty($auto_params['search_words']))
            {
                $search = $auto_params['search_words'];
                $where = ' '.(empty($params['no_operator']) ? 'AND' : '')." ( LOWER(cm.message) LIKE ('%".pSQL(strtolower($search))."%') ) ";
            }
        }

        return $where;
    }
}
