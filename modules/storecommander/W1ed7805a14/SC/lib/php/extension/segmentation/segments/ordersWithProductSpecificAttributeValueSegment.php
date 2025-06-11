<?php

class ordersWithProductSpecificAttributeValueSegment extends SegmentCustom
{
    public $name = 'Orders with products specific attribute ...';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $html = '<strong>'._l('Attributes group:').'</strong><br/>
        <select id="id_group" name="id_group" style="width: 100%;">
            <option value="">--</option>';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $groups = AttributeGroup::getAttributesGroups($params['id_lang']);
        foreach ($groups as $group)
        {
            $html .= '<option value="'.$group['id_attribute_group'].'" '.($group['id_attribute_group'] == $values['id_group'] ? 'selected' : '').'>'.$group['name'].'</option>';
        }
        $html .= '</select>
        <br/><br/>        
        <strong>'._l('Attribute:').'</strong><br/>
        <select id="id_attribute" name="id_attribute" style="width: 100%;"></select>
                    
        <br/><br/>
        <strong>'._l('Display products').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Active and nonactive').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Active only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonactive only').'</option>
        </select>
                
        <script>
        $(document).ready(function(){
            $("#id_group").change(function(){
                var id = $(this).val();
                $.post("index.php?ajax=1&act=all_win-segmentation_gate&id_lang='.$params['id_lang'].'",{"segment":"ordersWithProductSpecificAttributeValueSegment", "function":"_getAttributesForIdGroup", "params": {"id_group":id}},function(data){
                    $("#id_attribute").html(data);
                });
            });';

        if (!empty($values['id_group']) && !empty($values['id_attribute']))
        {
            $html .= '$.post("index.php?ajax=1&act=all_win-segmentation_gate&id_lang='.$params['id_lang'].'",{"segment":"ordersWithProductSpecificAttributeValueSegment", "function":"_getAttributesForIdGroup", "params": {"id_group":"'.(int) $values['id_group'].'"}},function(data){
                    $("#id_attribute").html(data);
                    $("#id_attribute").val('.(int) $values['id_attribute'].');
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
            if (!empty($auto_params['id_attribute']))
            {
                $sql = 'SELECT DISTINCT(od.id_order)
                        FROM '._DB_PREFIX_.'order_detail od
                        INNER JOIN '._DB_PREFIX_.'product_shop ps ON (od.product_id = ps.id_product AND ps.id_shop ='.(int) SCI::getSelectedShop().')
                        INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = od.product_attribute_id)
                        INNER JOIN '._DB_PREFIX_."product_attribute_combination pac ON (pa.id_product_attribute = pac.id_product_attribute AND pac.id_attribute=".(int)$auto_params['id_attribute'].")".
                        (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " WHERE ps.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '');
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
            if (!empty($auto_params['id_attribute']))
            {
                if (!empty($params['is_order']))
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' o.id_order IN (SELECT DISTINCT(od.id_order)
                                                        FROM '._DB_PREFIX_.'order_detail od
                                                        INNER JOIN '._DB_PREFIX_.'product_shop ps ON (od.product_id = ps.id_product AND ps.id_shop ='.(int) SCI::getSelectedShop().')
                                                        INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = od.product_attribute_id)
                                                        INNER JOIN '._DB_PREFIX_."product_attribute_combination pac ON (pa.id_product_attribute = pac.id_product_attribute AND pac.id_attribute=".(int)$auto_params['id_attribute'].")".
                                                        (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " WHERE ps.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '')."
                                                    )";
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' od.id_order_detail IN (SELECT DISTINCT(od.id_order_detail)
                                                            FROM '._DB_PREFIX_.'order_detail od
                                                            INNER JOIN '._DB_PREFIX_.'product_shop ps ON (od.product_id = ps.id_product AND ps.id_shop ='.(int) SCI::getSelectedShop().')
                                                            INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = od.product_attribute_id)
                                                            INNER JOIN '._DB_PREFIX_."product_attribute_combination pac ON (pa.id_product_attribute = pac.id_product_attribute AND pac.id_attribute=".(int)$auto_params['id_attribute'].")".
                                                           (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " WHERE ps.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '')."
                                                    )";
                }
            }
        }

        return $where;
    }

    public static function _getAttributesForIdGroup($params = array())
    {
        $html = '<option value="">--</option>';

        if (!empty($params['id_group']) && !empty($params['id_lang']))
        {
            $attributes = AttributeGroup::getAttributes($params['id_lang'], $params['id_group']);
            foreach ($attributes as $attribute)
            {
                $html .= '<option value="'.$attribute['id_attribute'].'">'.$attribute['name'].'</option>';
            }
        }

        return $html;
    }
}
