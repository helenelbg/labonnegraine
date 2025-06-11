<?php

class ordersWithoutOrderStateSegment extends SegmentCustom
{
    public $name = 'Orders without order state';
    public $liste_hooks = array('segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();
        $sql = 'SELECT DISTINCT(id_order)
                FROM '._DB_PREFIX_.'orders 
                WHERE current_state IS NULL OR current_state = 0';
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
        return ' '.(empty($params['no_operator']) ? 'AND' : '').' o.id_order IN (SELECT DISTINCT(id_order)
                FROM '._DB_PREFIX_.'orders 
                WHERE current_state IS NULL OR current_state = 0)';
    }
}
