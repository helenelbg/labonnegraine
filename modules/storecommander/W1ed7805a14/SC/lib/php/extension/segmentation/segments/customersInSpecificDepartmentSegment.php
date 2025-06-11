<?php

class customersInSpecificDepartmentSegment extends SegmentCustom
{
    public $name = 'Customers located in a particular state (country)';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $html = '<strong>'._l('Country:').'</strong><br/>
        <select id="id_country" name="id_country" style="width: 100%;">
            <option value="">--</option>';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $rows = Country::getCountries($params['id_lang'], false);
        foreach ($rows as $row)
        {
            $html .= '<option value="'.$row['id_country'].'" '.($row['id_country'] == $values['id_country'] ? 'selected' : '').'>'.$row['name'].'</option>';
        }
        $html .= '</select>
        <br/><br/>        
        <strong>'._l('State')._l(':').'</strong><br/>
        <select id="id_state" name="id_state" style="width: 100%;">
            <option value="">--</option>
        </select>
                
        <script>
        $(document).ready(function(){
            $("#id_country").change(function(){
                var id = $(this).val();
                $.post("index.php?ajax=1&act=all_win-segmentation_gate&id_lang='.$params['id_lang'].'",{"segment":"customersInSpecificDepartmentSegment", "function":"_getAttributesForIdCountry", "params": {"id_country":id}},function(data){
                    $("#id_state").html(data);
                });
            });';

        if (!empty($values['id_country']) && !empty($values['id_state']))
        {
            $html .= '$.post("index.php?ajax=1&act=all_win-segmentation_gate&id_lang='.$params['id_lang'].'",{"segment":"customersInSpecificDepartmentSegment", "function":"_getAttributesForIdCountry", "params": {"id_country":"'.(int) $values['id_country'].'"}},function(data){
                    $("#id_state").html(data);
                    $("#id_state").val('.(int) $values['id_state'].');
                });';
        }

        $html .= '
        });
        </script>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['id_state']))
            {
                $sql = 'SELECT DISTINCT(c.id_customer)
                FROM '._DB_PREFIX_.'customer c
                    INNER JOIN '._DB_PREFIX_."address a ON (c.id_customer = a.id_customer AND a.id_state='".(int)$auto_params['id_state']."')
                WHERE a.deleted=0";
                $res = Db::getInstance()->ExecuteS($sql);
                foreach ($res as $row)
                {
                    $type = _l('Customer');
                    $element = new Customer($row['id_customer']);
                    $name = $element->firstname.' '.$element->lastname;
                    $infos = $element->email;
                    $array[] = array($type, $name, $infos, 'id' => 'customer_'.$row['id_customer'], 'id_display' => $row['id_customer']);
                }
            }
            elseif (!empty($auto_params['id_country']))
            {
                $sql = 'SELECT DISTINCT(c.id_customer)
                FROM '._DB_PREFIX_.'customer c
                    INNER JOIN '._DB_PREFIX_."address a ON (c.id_customer = a.id_customer AND a.id_country=".(int)$auto_params['id_country'].")
                WHERE a.deleted=0";
                $res = Db::getInstance()->ExecuteS($sql);
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

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['id_state']))
            {
                if (!empty($params['is_customer']))
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' c.id_customer IN (SELECT DISTINCT(cu.id_customer)
                                                    FROM '._DB_PREFIX_.'customer cu
                                                        INNER JOIN '._DB_PREFIX_."address a ON (cu.id_customer = a.id_customer AND a.id_state=".(int)$auto_params['id_state'].")
                                                    WHERE a.deleted=0
                                                    )";
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '')." a.id_state=".(int)$auto_params['id_state'];
                }
            }
            elseif (!empty($auto_params['id_country']))
            {
                if (!empty($params['is_customer']))
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' c.id_customer IN (SELECT DISTINCT(cu.id_customer)
                                                    FROM '._DB_PREFIX_.'customer cu
                                                        INNER JOIN '._DB_PREFIX_."address a ON (cu.id_customer = a.id_customer AND a.id_country=".(int)$auto_params['id_country'].")
                                                    WHERE a.deleted=0
                                                    )";
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '')." a.id_country=".(int)$auto_params['id_country'];
                }
            }
        }

        return $where;
    }

    public static function _getAttributesForIdCountry($params = array())
    {
        $html = '<option value="">--</option>';

        if (!empty($params['id_country']) && !empty($params['id_lang']))
        {
            $rows = State::getStatesByIdCountry($params['id_country']);
            foreach ($rows as $row)
            {
                $html .= '<option value="'.$row['id_state'].'">'.$row['name'].'</option>';
            }
        }

        return $html;
    }
}
