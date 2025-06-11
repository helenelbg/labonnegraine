<?php

class ordersWithProductsInSegmentSegment extends SegmentCustom
{
    public $name = 'Orders with products in segment ...';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $html = '<strong>'._l('Segment:').'</strong><br/>
        <select id="id_segment" name="id_segment" style="width: 100%;">
            <option value="">--</option>';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html .= $this->getLevelFromDB(0, $values);

        $html .= '</select>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['id_segment']))
            {
                $segment = new ScSegment($auto_params['id_segment']);
                if ($segment->type == 'manual')
                {
                    $sql = 'SELECT DISTINCT(o.id_order)
                    FROM '._DB_PREFIX_.'orders o
                            INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                                INNER JOIN '._DB_PREFIX_."sc_segment_element se ON (se.id_element = od.product_id AND se.type_element='product' AND se.id_segment=".(int)$auto_params['id_segment'].")";
                }
                elseif ($segment->type == 'auto')
                {
                    $ids = array();
                    $res_segment = SegmentHook::hookByIdSegment('segmentAutoSqlQueryGrid', $segment, array('id_lang' => $params['id_lang']));
                    if (is_array($res_segment) && !empty($res_segment))
                    {
                        foreach ($res_segment as $row)
                        {
                            if (strpos($row['id'], 'product_') !== false)
                            {
                                $exp = explode('_', $row['id']);
                                $ids[] = end($exp);
                            }
                        }

                        $sql = 'SELECT DISTINCT(o.id_order)
                            FROM '._DB_PREFIX_.'orders o
                                    INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                            WHERE od.product_id IN ('.pInSQL(implode(',', $ids)).')';
                    }
                }
                if (!empty($sql))
                {
                    $res = Db::getInstance()->ExecuteS($sql);
                    //echo $sql;
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
        }

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['id_segment']))
            {
                $segment = new ScSegment($auto_params['id_segment']);
                if ($segment->type == 'manual')
                {
                    if (!empty($params['is_order']))
                    {
                        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' o.id_order IN (SELECT DISTINCT(o.id_order)
                                                            FROM '._DB_PREFIX_.'orders o
                                                                INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                                                                    INNER JOIN '._DB_PREFIX_."sc_segment_element se ON (se.id_element = od.product_id AND se.type_element='product' AND se.id_segment=".(int)$auto_params['id_segment'].")
                                                        )";
                    }
                    else
                    {
                        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' od.id_order IN (SELECT DISTINCT(o.id_order)
                                                            FROM '._DB_PREFIX_.'orders o
                                                                INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                                                                    INNER JOIN '._DB_PREFIX_."sc_segment_element se ON (se.id_element = od.product_id AND se.type_element='product' AND se.id_segment=".(int)$auto_params['id_segment'].")
                                                        )";
                    }
                }
                elseif ($segment->type == 'auto')
                {
                    $ids = array();
                    $res_segment = SegmentHook::hookByIdSegment('segmentAutoSqlQueryGrid', $segment, array('id_lang' => $params['id_lang']));
                    if (is_array($res_segment) && !empty($res_segment))
                    {
                        foreach ($res_segment as $row)
                        {
                            if (strpos($row['id'], 'product_') !== false)
                            {
                                $exp = explode('_', $row['id']);
                                $ids[] = end($exp);
                            }
                        }
                        if (!empty($params['is_order']) && !empty($ids))
                        {
                            $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' o.id_order IN (SELECT DISTINCT(o.id_order)
                                                                FROM '._DB_PREFIX_.'orders o
                                                                    INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                                                            WHERE od.product_id IN ('.pInSQL(implode(',', $ids)).')
                                                            )';
                        }
                        elseif (!empty($ids))
                        {
                            $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' od.id_order IN (SELECT DISTINCT(o.id_order)
                                                                FROM '._DB_PREFIX_.'orders o
                                                                    INNER JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
                                                            WHERE od.product_id IN ('.pInSQL(implode(',', $ids)).')
                                                            )';
                        }
                    }
                }
            }
        }

        return $where;
    }
}
