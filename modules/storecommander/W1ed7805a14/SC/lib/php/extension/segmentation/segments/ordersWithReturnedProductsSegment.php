<?php

class ordersWithReturnedProductsSegment extends SegmentCustom
{
    public $name = 'Orders with returned products in the last X months';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<strong>'._l('Number of months:').'</strong><br/>
        <input type="text" id="nb_month" name="nb_month" value="'.(!empty($values['nb_month']) ? (int) $values['nb_month'] : '').'" style="width: 100%;" />';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['nb_month']) && is_numeric($auto_params['nb_month']))
            {
                $sql = 'SELECT DISTINCT(o.id_order)
                FROM '._DB_PREFIX_.'orders o
                    INNER JOIN '._DB_PREFIX_."order_detail od ON (o.id_order = od.id_order)
                WHERE o.date_add >= (SELECT DATE_ADD('".date('Y-m')."-01 00:00:00', INTERVAL -".(int)$auto_params['nb_month'].' MONTH))
                    AND od.product_quantity_return > 0';
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
            if (!empty($auto_params['nb_month']) && is_numeric($auto_params['nb_month']))
            {
                if (!empty($params['is_order']))
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' o.id_order IN (SELECT DISTINCT(o.id_order)
                                                    FROM '._DB_PREFIX_.'orders o
                                                        INNER JOIN '._DB_PREFIX_."order_detail od ON (o.id_order = od.id_order)
                                                    WHERE o.date_add >= (SELECT DATE_ADD('".date('Y-m')."-01 00:00:00', INTERVAL -".(int)$auto_params['nb_month'].' MONTH))
                                                        AND od.product_quantity_return > 0
                                                    )';
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' od.id_order IN (SELECT DISTINCT(o.id_order)
                                                    FROM '._DB_PREFIX_.'orders o
                                                        INNER JOIN '._DB_PREFIX_."order_detail od ON (o.id_order = od.id_order)
                                                    WHERE o.date_add >= (SELECT DATE_ADD('".date('Y-m')."-01 00:00:00', INTERVAL -".(int)$auto_params['nb_month'].' MONTH))
                                                        AND od.product_quantity_return > 0
                                                    )';
                }
            }
        }

        return $where;
    }
}
