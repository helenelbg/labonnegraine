<?php

class operatorSegment extends SegmentCustom
{
    public $name = 'Group segments';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $list_segments = '';
        $list_segments_js = '';
        $num = 1;
        if (!empty($values['id_segment']))
        {
            $sql = 'SELECT *
                    FROM '._DB_PREFIX_.'sc_segment
                    WHERE id_segment IN ('.pInSQL($values['id_segment']).')
                    ORDER BY name';
            $res = Db::getInstance()->ExecuteS($sql);
            if (!empty($res))
            {
                foreach ($res as $row)
                {
                    $list_segments .= '<span>- <input type="hidden" name="id_segment[]" value="'.$row['id_segment'].'" /> '.$row['name'].' <img src="lib/img/delete.gif" onclick="javascript: $(this).parent().remove();" title="'._l('Delete').'" style="cursor: pointer;" /><br/></span>';
                    $list_segments_js .= "\n".'$("#choose_segment").val('.$row['id_segment'].');addSeg();';
                    ++$num;
                }
            }
        }

        $html = '<strong>'._l('Operator:').'</strong><br/>
        <select id="operator" name="operator" style="width: 100%;">
            <option value="AND" '.(!empty($values['operator']) && $values['operator'] == 'AND' ? 'selected' : '').'>'._l('Intersection').'</option>
            <option value="OR" '.(!empty($values['operator']) && $values['operator'] == 'OR' ? 'selected' : '').'>'._l('Union').'</option>';
        $html .= '</select><br/><br/>';

        $html .= '<strong>'._l('Add segment:').'</strong><br/>
        <select id="choose_segment" style="width: 60%;float: left;">
            <option value="">--</option>';

        $html .= $this->getLevelFromDBcustom(0);

        $html .= '</select> <input type="button" id="add_segment" value="'._l('Add').'" style="width: 38%;float: right;" />
        <br/><br/>
        <fieldset id="list_segments">
            <legend>'._l('List of Segments to use:').'</legend>
            '/*.$list_segments*/.'
        </fieldset>

        <script>
            var num_segment = '.$num.'*1;
            $("#add_segment").on("click", function(){
                addSeg();
            });
            function addSeg()
            {
                var id = $("#choose_segment").val();
                var name = $( "#choose_segment option:selected" ).attr("name");
                if(id!="")
                {
                    $("#list_segments").append("<span>- <input type=\"hidden\" name=\"id_segment[]\" value=\""+id+"\" /> "+name+" <img src=\"lib/img/delete.gif\" onclick=\"javascript: $(this).parent().remove();\" title=\"'._l('Delete').'\" style=\"cursor: pointer;\" /><br/></span>");
                    num_segment++;
                 }
            }
            '.$list_segments_js.'
            $("#choose_segment").val("");
        </script>';

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
                $operator = (!empty($auto_params['operator']) ? $auto_params['operator'] : 'AND');
                $id_segments = explode(',', $auto_params['id_segment']);
                $final_rows = array();
                $segment_rows = array();
                foreach ($id_segments as $id_segment)
                {
                    $segment = new ScSegment($id_segment);
                    if ($segment->type == 'manual')
                    {
                        $sql = 'SELECT *
                        FROM '._DB_PREFIX_."sc_segment_element
                        WHERE id_segment =".(int)$id_segment."
                        ORDER BY type_element, id_element";
                        $res_segment = Db::getInstance()->ExecuteS($sql);
                        foreach ($res_segment as $row)
                        {
                            if ($row['type_element'] == 'product')
                            {
                                $type = _l('Product');
                                if (SCMS)
                                {
                                    $element = new Product($row['id_element'], true);
                                }
                                else
                                {
                                    $element = new Product($row['id_element']);
                                }
                                $id = $row['id_element'];
                                $name = $element->name[$id_lang];
                                $infos = $element->reference;
                            }
                            elseif ($row['type_element'] == 'customer')
                            {
                                $type = _l('Customer');
                                $id = $row['id_element'];
                                $element = new Customer($row['id_element']);
                                $name = $element->firstname.' '.$element->lastname;
                                $infos = $element->email;
                            }
                            elseif ($row['type_element'] == 'order')
                            {
                                $type = _l('Order');
                                $id = $row['id_element'];
                                $element = new Order($row['id_element']);
                                $name = $element->reference;
                                $infos = _l('Order placed ').$element->date_add;
                            }
                            elseif ($row['type_element'] == 'customer_service')
                            {
                                $type = _l('Customer service');
                                $id = $row['id_customer_thread'];
                                $element = new CustomerThread($row['id_customer_thread']);
                                $customer = new Customer($element->id_customer);
                                $name = _l('Discussion with ').$customer->firstname.' '.$customer->lastname;
                                $infos = $element->date_add.' / '._l('Customer').' #'.$element->id_customer.' '.$customer->email;
                            }
                            $array = array($type, $name, $infos, 'id' => $row['type_element'].'_'.$id, 'id_display' => $id);
                            if ($operator == 'AND')
                            {
                                $segment_rows[$id_segment][$row['type_element'].'_'.$id] = $array;
                            }
                            elseif ($operator == 'OR')
                            {
                                $final_rows[] = $array;
                            }
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
                                if ($operator == 'AND')
                                {
                                    $segment_rows[$id_segment][$row['id']] = $row;
                                }
                                elseif ($operator == 'OR')
                                {
                                    $final_rows[] = $row;
                                }
                            }
                        }
                    }
                }
                if ($operator == 'AND')
                {
                    $ar = array();
                    foreach ($segment_rows as $a)
                    {
                        if (empty($ar))
                        {
                            $ar = $a;
                        }
                        else
                        {
                            $tmp = array();
                            foreach ($a as $id => $r)
                            {
                                if (!empty($ar[$id]))
                                {
                                    $tmp[$id] = $r;
                                }
                            }
                            $ar = $tmp;
                        }
                    }
                    $array = array_values($ar);
                }
                elseif ($operator == 'OR')
                {
                    $array = $final_rows;
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
                $operator = (!empty($auto_params['operator']) ? $auto_params['operator'] : 'AND');
                $where = ' AND ( ';
                $id_segments = explode(',', $auto_params['id_segment']);
                $i = 0;
                foreach ($id_segments as $id_segment)
                {
                    $segment = new ScSegment($id_segment);
                    if ($segment->type == 'manual')
                    {
                        if (!empty($params['access']))
                        {
                            if ($params['access'] == 'catalog')
                            {
                                if ($i > 0)
                                {
                                    $where .= ' '.$operator.' ';
                                }
                                $where .= 'p.id_product IN (SELECT id_element FROM '._DB_PREFIX_."sc_segment_element WHERE type_element='product' AND id_segment=".(int)$id_segment.")";
                                ++$i;
                            }
                            elseif ($params['access'] == 'orders')
                            {
                                if ($i > 0)
                                {
                                    $where .= ' '.$operator.' ';
                                }
                                $where .= ' o.id_order IN (SELECT id_element FROM '._DB_PREFIX_."sc_segment_element WHERE type_element='order' AND id_segment=".(int)$id_segment.")";
                                ++$i;
                            }
                            elseif ($params['access'] == 'customers')
                            {
                                if ($i > 0)
                                {
                                    $where .= ' '.$operator.' ';
                                }
                                $where .= ' c.id_customer IN (SELECT id_element FROM '._DB_PREFIX_."sc_segment_element WHERE type_element='customer' AND id_segment=".(int)$id_segment.")";
                                ++$i;
                            }
                            elseif ($params['access'] == 'customer_service')
                            {
                                if ($i > 0)
                                {
                                    $where .= ' '.$operator.' ';
                                }
                                $where .= ' AND ct.id_customer_thread IN (SELECT id_element FROM '._DB_PREFIX_."sc_segment_element WHERE type_element='customer_service' AND id_segment=".(int)$id_segment.")";
                                ++$i;
                            }
                        }
                    }
                    elseif ($segment->type == 'auto')
                    {
                        $new_params = $params;
                        $new_params['id_segment'] = $id_segment;
                        $new_params['no_operator'] = true;
                        $where_segment = SegmentHook::hookByIdSegment('segmentAutoSqlQuery', $segment, $new_params);
                        if (!empty($where_segment))
                        {
                            if ($i > 0)
                            {
                                $where .= ' '.$operator.' ';
                            }
                            else
                            {
                                $tmp = trim($where_segment, '');
                                $pos = strpos($tmp, 'AND');
                                if ($pos <= 6)
                                {
                                    $where_segment = substr($where_segment, ($pos + 2));
                                }
                            }
                            $where .= $where_segment;
                            ++$i;
                        }
                    }
                }
                $where .= ') ';
            }
        }

        return $where;
    }

    public function getLevelFromDBcustom($parent_id, $values = array(), $niveau = 0, $prefix = '')
    {
        $html = '';
        $sql = 'SELECT *
                    FROM '._DB_PREFIX_."sc_segment
                    WHERE id_parent = ".(int)$parent_id."
                    ORDER BY name";

        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            if ($niveau > 0)
            {
                $name = '|- '.$row['name'];
            }
            else
            {
                $name = $row['name'];
            }
            for ($i = 1; $i <= $niveau; ++$i)
            {
                $name = '&nbsp;&nbsp;&nbsp;'.$name;
            }

            $html .= '<option value="'.$row['id_segment'].'" name="'.$prefix.'<strong>'.$row['name'].'</strong>'.'" '.($row['id_segment'] == $values['id_segment'] ? 'selected' : '').'>'.$name.'</option>';
            $html .= $this->getLevelFromDBcustom($row['id_segment'], $values, ($niveau + 1), $prefix.$row['name'].' > ');
        }

        return $html;
    }
}
