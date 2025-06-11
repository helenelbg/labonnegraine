<?php

class customersWithOrdersInSegmentSegment extends SegmentCustom
{
    public $name = 'Customers with orders in segment ...';
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
                    $sql = 'SELECT DISTINCT(c.id_customer)
                    FROM '._DB_PREFIX_.'customer c
                        INNER JOIN '._DB_PREFIX_.'orders o ON (c.id_customer = o.id_customer AND o.valid=1)
                            INNER JOIN '._DB_PREFIX_."sc_segment_element se ON (se.id_element = o.id_order AND se.type_element='order' AND se.id_segment=".(int)$auto_params['id_segment'].")";
                }
                elseif ($segment->type == 'auto')
                {
                    $ids = array();
                    $res_segment = SegmentHook::hookByIdSegment('segmentAutoSqlQueryGrid', $segment, array('id_lang' => $params['id_lang']));
                    if (is_array($res_segment) && !empty($res_segment))
                    {
                        foreach ($res_segment as $row)
                        {
                            if (strpos($row['id'], 'order_') !== false)
                            {
                                $exp = explode('_', $row['id']);
                                $ids[] = end($exp);
                            }
                        }

                        $sql = 'SELECT DISTINCT(c.id_customer)
                                    FROM '._DB_PREFIX_.'customer c
                                        INNER JOIN '._DB_PREFIX_.'orders o ON (c.id_customer = o.id_customer AND o.valid=1)
                                WHERE o.id_order IN ('.pInSQL(implode(',', $ids)).')';
                    }
                }
                if (!empty($sql))
                {
                    $res = Db::getInstance()->ExecuteS($sql);
                    //echo $sql;
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
                    if (!empty($params['is_customer']))
                    {
                        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' c.id_customer IN (SELECT DISTINCT(c.id_customer)
                                                        FROM '._DB_PREFIX_.'customer c
                                                            INNER JOIN '._DB_PREFIX_.'orders o ON (c.id_customer = o.id_customer AND o.valid=1)
                                                                INNER JOIN '._DB_PREFIX_."sc_segment_element se ON (se.id_element = o.id_order AND se.type_element='order' AND se.id_segment=".(int)$auto_params['id_segment'].")
                                                        )";
                    }
                    else
                    {
                        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' a.id_customer IN (SELECT DISTINCT(c.id_customer)
                                                        FROM '._DB_PREFIX_.'customer c
                                                            INNER JOIN '._DB_PREFIX_.'orders o ON (c.id_customer = o.id_customer AND o.valid=1)
                                                                INNER JOIN '._DB_PREFIX_."sc_segment_element se ON (se.id_element = o.id_order AND se.type_element='order' AND se.id_segment=".(int)$auto_params['id_segment'].")
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
                            if (strpos($row['id'], 'order_') !== false)
                            {
                                $exp = explode('_', $row['id']);
                                $ids[] = end($exp);
                            }
                        }
                        if (!empty($params['is_customer']) && !empty($ids))
                        {
                            $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' c.id_customer IN (SELECT DISTINCT(c.id_customer)
                                                        FROM '._DB_PREFIX_.'customer c
                                                            INNER JOIN '._DB_PREFIX_.'orders o ON (c.id_customer = o.id_customer AND o.valid=1)
                                                        WHERE o.id_order IN ('.pInSQL(implode(',', $ids)).')
                                                        )';
                        }
                        elseif (!empty($ids))
                        {
                            $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' a.id_customer IN (SELECT DISTINCT(c.id_customer)
                                                        FROM '._DB_PREFIX_.'customer c
                                                            INNER JOIN '._DB_PREFIX_.'orders o ON (c.id_customer = o.id_customer AND o.valid=1)
                                                        WHERE o.id_order IN ('.pInSQL(implode(',', $ids)).')
                                                        )';
                        }
                    }
                }
            }
        }

        return $where;
    }
}
