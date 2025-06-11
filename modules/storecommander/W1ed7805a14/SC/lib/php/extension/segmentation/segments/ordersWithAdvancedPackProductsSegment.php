<?php

class ordersWithAdvancedPackProductsSegment extends SegmentCustom
{
    public $name = 'Orders with products from Advanced Pack';
    public $liste_hooks = array('segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();
        $sql = 'SELECT DISTINCT(o.id_order)
                FROM '._DB_PREFIX_.'orders o
                RIGHT JOIN '._DB_PREFIX_.'pm_advancedpack_cart_products apcp ON (apcp.id_order = o.id_order)';
        $res = Db::getInstance()->ExecuteS($sql);
        if (!empty($res))
        {
            foreach ($res as $row)
            {
                $type = _l('Order');
                $element = new Order($row['id_order']);
                $name = $element->reference;
                $infos = _l('Order placed ').$element->date_add;
                $array[] = array($type, $name, $infos, 'id' => 'order_'.$row['id_order'], 'id_display' => $row['id_order']);
            }
        }

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        return ' '.(empty($params['no_operator']) ? 'AND' : '').' o.id_order IN (SELECT DISTINCT(o.id_order)
                FROM '._DB_PREFIX_.'orders o
                RIGHT JOIN '._DB_PREFIX_.'pm_advancedpack_cart_products apcp ON (apcp.id_order = o.id_order))';
    }
}
