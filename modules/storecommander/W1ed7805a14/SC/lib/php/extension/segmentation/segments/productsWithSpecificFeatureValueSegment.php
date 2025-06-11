<?php

class productsWithSpecificFeatureValueSegment extends SegmentCustom
{
    public $name = 'Products with a specific feature value';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $html = '<strong>'._l('Feature:').'</strong><br/>
        <select id="id_feature" name="id_feature" style="width: 100%;">
            <option value="">--</option>';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $rows = Feature::getFeatures($params['id_lang']);
        foreach ($rows as $row)
        {
            $html .= '<option value="'.$row['id_feature'].'" '.($row['id_feature'] == $values['id_feature'] ? 'selected' : '').'>'.$row['name'].'</option>';
        }
        $html .= '</select>
        <br/><br/>        
        <strong>'._l('Value:').'</strong><br/>
        <select id="id_feature_value" name="id_feature_value" style="width: 100%;"></select>
                    
        <br/><br/>
        <strong>'._l('Display products').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Active and nonactive').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Active only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonactive only').'</option>
        </select>
                
        <script>
        $(document).ready(function(){
            $("#id_feature").change(function(){
                var id = $(this).val();
                $.post("index.php?ajax=1&act=all_win-segmentation_gate&id_lang='.$params['id_lang'].'",{"segment":"productsWithSpecificFeatureValueSegment", "function":"_getValuesForIdFeature", "params": {"id_feature":id}},function(data){
                    $("#id_feature_value").html(data);
                });
            });';

        if (!empty($values['id_feature_value']) && !empty($values['id_feature_value']))
        {
            $html .= '$.post("index.php?ajax=1&act=all_win-segmentation_gate&id_lang='.$params['id_lang'].'",{"segment":"productsWithSpecificFeatureValueSegment", "function":"_getValuesForIdFeature", "params": {"id_feature":"'.(int) $values['id_feature'].'"}},function(data){
                    $("#id_feature_value").html(data);
                    $("#id_feature_value").val('.(int) $values['id_feature_value'].');
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
            if (!empty($auto_params['id_feature_value']))
            {
                if (substr($auto_params['id_feature_value'], 0, 6) == 'empty_')
                {
                    $id_feature = str_replace('empty_', '', $auto_params['id_feature_value']);

                    $sql = 'SELECT id_product
                    FROM '._DB_PREFIX_.'product
                    WHERE id_product NOT IN (
                        SELECT DISTINCT(id_product)
                        FROM '._DB_PREFIX_."feature_product
                        WHERE id_feature=".(int)$id_feature."
                    ) ";
                }
                else
                {
                    $sql = 'SELECT DISTINCT(id_product)
                    FROM '._DB_PREFIX_."feature_product
                    WHERE id_feature_value=".(int)$auto_params['id_feature_value'];
                }
                $res = Db::getInstance()->ExecuteS($sql);
                //echo $sql;die();
                foreach ($res as $row)
                {
                    $type = _l('Product');
                    if (SCMS)
                    {
                        $element = new Product($row['id_product'], true);
                    }
                    else
                    {
                        $element = new Product($row['id_product']);
                    }
                    if (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all')
                    {
                        if ($auto_params['active_pdt'] == 'active' && $element->active != 1)
                        {
                            continue;
                        }
                        elseif ($auto_params['active_pdt'] == 'nonactive' && $element->active != 0)
                        {
                            continue;
                        }
                    }
                    $name = $element->name[$params['id_lang']];
                    $infos = $element->reference;
                    $array[] = array($type, $name, $infos, 'id' => 'product_'.$row['id_product'], 'id_display' => $row['id_product']);
                }
            }
            elseif (!empty($auto_params['id_feature']))
            {
                $sql = 'SELECT DISTINCT(id_product)
                FROM '._DB_PREFIX_."feature_product
                WHERE id_feature=".(int)$auto_params['id_feature'];
                $res = Db::getInstance()->ExecuteS($sql);
                //echo $sql;die();
                foreach ($res as $row)
                {
                    $type = _l('Product');
                    if (SCMS)
                    {
                        $element = new Product($row['id_product'], true);
                    }
                    else
                    {
                        $element = new Product($row['id_product']);
                    }
                    if (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all')
                    {
                        if ($auto_params['active_pdt'] == 'active' && $element->ative != 1)
                        {
                            continue;
                        }
                        elseif ($auto_params['active_pdt'] == 'nonactive' && $element->ative != 0)
                        {
                            continue;
                        }
                    }
                    $name = $element->name[$params['id_lang']];
                    $infos = $element->reference;
                    $array[] = array($type, $name, $infos, 'id' => 'product_'.$row['id_product'], 'id_display' => $row['id_product']);
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
            if (!empty($auto_params['id_feature_value']))
            {
                if (substr($auto_params['id_feature_value'], 0, 6) == 'empty_')
                {
                    $id_feature = str_replace('empty_', '', $auto_params['id_feature_value']);
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' ( p.id_product NOT IN (
                                SELECT DISTINCT(id_product)
                                FROM '._DB_PREFIX_."feature_product
                                WHERE id_feature=".(int)$id_feature."
                        ) ".
                        (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').' ) ';
                }
                else
                {
                    $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' ( p.id_product IN (SELECT DISTINCT(id_product)
                                                FROM '._DB_PREFIX_."feature_product
                                                WHERE id_feature_value=".(int)$auto_params['id_feature_value']."
                                                    ) ".
                    (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').' ) ';
                }
            }
            elseif (!empty($auto_params['id_feature']))
            {
                $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' ( p.id_product IN (SELECT DISTINCT(id_product)
                                                FROM '._DB_PREFIX_."feature_product
                                                WHERE id_feature=".(int)$auto_params['id_feature']."
                                                    ) ".
                    (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').' ) ';
            }
        }

        return $where;
    }

    public static function _getValuesForIdFeature($params = array())
    {
        $html = '<option value="">--</option>';
        if (!empty($params['id_feature']) && !empty($params['id_feature']))
        {
            $html = '<option value="empty_'.$params['id_feature'].'">-- '._l('Empty').' --</option>';
            $feature_values = FeatureValue::getFeatureValuesWithLang($params['id_lang'], $params['id_feature']);
            foreach ($feature_values as $feature_value)
            {
                $html .= '<option value="'.$feature_value['id_feature_value'].'">'.$feature_value['value'].'</option>';
            }
        }

        return $html;
    }
}
