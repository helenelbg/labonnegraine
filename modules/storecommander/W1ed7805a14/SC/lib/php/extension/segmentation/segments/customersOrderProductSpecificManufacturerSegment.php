<?php

class customersOrderProductSpecificManufacturerSegment extends SegmentCustom
{
    public $name = 'Customers who ordered product of manufacturer ...';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $html = '<strong>'._l('Manufacturer:').'</strong><br/>
        <select id="id_manufacturer" name="id_manufacturer" style="width: 100%;">
            <option value="">--</option>';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $rows = Manufacturer::getManufacturers(false, $params['id_lang'], false);
        foreach ($rows as $row)
        {
            $html .= '<option value="'.$row['id_manufacturer'].'" '.($row['id_manufacturer'] == $values['id_manufacturer'] ? 'selected' : '').'>'.$row['name'].'</option>';
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
            if (!empty($auto_params['id_manufacturer']))
            {
                $sql = 'SELECT DISTINCT(c.id_customer)
                FROM '._DB_PREFIX_.'customer c
                    INNER JOIN '._DB_PREFIX_.'orders o ON (c.id_customer = o.id_customer)
                        INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            INNER JOIN '._DB_PREFIX_."product p ON (p.id_product = od.product_id AND p.id_manufacturer=".(int)$auto_params['id_manufacturer'].")";
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
            if (!empty($auto_params['id_manufacturer']))
            {
                if (!empty($params['is_customer']))
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' c.id_customer IN (SELECT DISTINCT(cu.id_customer)
                                                        FROM '._DB_PREFIX_.'customer cu
                                                            INNER JOIN '._DB_PREFIX_.'orders o ON (cu.id_customer = o.id_customer)
                                                                INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                                                                    INNER JOIN '._DB_PREFIX_."product p ON (p.id_product = od.product_id AND p.id_manufacturer=".(int)$auto_params['id_manufacturer'].")
                                                    )";
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' a.id_customer IN (SELECT DISTINCT(cu.id_customer)
                                                        FROM '._DB_PREFIX_.'customer cu
                                                            INNER JOIN '._DB_PREFIX_.'orders o ON (cu.id_customer = o.id_customer)
                                                                INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                                                                    INNER JOIN '._DB_PREFIX_."product p ON (p.id_product = od.product_id AND p.id_manufacturer=".(int)$auto_params['id_manufacturer'].")
                                                    )";
                }
            }
        }

        return $where;
    }
}
