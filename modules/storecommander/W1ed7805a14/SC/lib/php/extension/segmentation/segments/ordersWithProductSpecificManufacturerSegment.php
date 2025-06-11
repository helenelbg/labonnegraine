<?php

class ordersWithProductSpecificManufacturerSegment extends SegmentCustom
{
    public $name = 'Orders with products of manufacturer ...';
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
                $sql = 'SELECT DISTINCT(o.id_order)
                FROM '._DB_PREFIX_.'orders o
                        INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            INNER JOIN '._DB_PREFIX_."product p ON (p.id_product = od.product_id AND p.id_manufacturer=".(int)$auto_params['id_manufacturer'].")";
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
            if (!empty($auto_params['id_manufacturer']))
            {
                if (!empty($params['is_order']))
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' o.id_order IN (SELECT DISTINCT(o.id_order)
                                                        FROM '._DB_PREFIX_.'orders o
                                                                INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                                                                    INNER JOIN '._DB_PREFIX_."product p ON (p.id_product = od.product_id AND p.id_manufacturer=".(int)$auto_params['id_manufacturer'].")
                                                    )";
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' od.id_order IN (SELECT DISTINCT(o.id_order)
                                                        FROM '._DB_PREFIX_.'orders o
                                                                INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                                                                    INNER JOIN '._DB_PREFIX_."product p ON (p.id_product = od.product_id AND p.id_manufacturer=".(int)$auto_params['id_manufacturer'].")
                                                    )";
                }
            }
        }

        return $where;
    }
}
