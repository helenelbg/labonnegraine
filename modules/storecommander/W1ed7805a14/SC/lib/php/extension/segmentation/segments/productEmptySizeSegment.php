<?php

class productEmptySizeSegment extends SegmentCustom
{
    public $name = 'Products with an empty size';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $empty_size = explode(',', $values['empty_size']);
        $html = '<strong>'._l('Check which dimensions are empty:').'</strong><br/>
        <select name="empty_size" style="width: 100%;height: 52px;" multiple="multiple">
            <option value="width" '.(in_array('width', $empty_size) ? 'selected' : '').'>'._l('Width').'</option>
            <option value="height" '.(in_array('height', $empty_size) ? 'selected' : '').'>'._l('Height').'</option>
            <option value="depth" '.(in_array('depth', $empty_size) ? 'selected' : '').'>'._l('Depth').'</option>';
        $html .= '</select>
                    
        <br/><br/>
        <strong>'._l('Display products').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Active and nonactive').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Active only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonactive only').'</option>
        </select>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['empty_size']))
            {
                $empty_size = explode(',', $auto_params['empty_size']);
                $condition = '';
                foreach ($empty_size as $size)
                {
                    if (!empty($condition))
                    {
                        $condition .= ' OR ';
                    }
                    $condition .= ' (`'.bqSQL($size)."`='' OR `".bqSQL($size)."`='0') ";
                }

                $sql = 'SELECT id_product
                        FROM '._DB_PREFIX_.'product WHERE '.$condition.' '.
                    (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '');

                $res = Db::getInstance()->ExecuteS($sql);
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
            if (!empty($auto_params['empty_size']))
            {
                $empty_size = explode(',', $auto_params['empty_size']);
                $condition = '';
                foreach ($empty_size as $size)
                {
                    if (!empty($condition))
                    {
                        $condition .= ' OR ';
                    }
                    $condition .= ' (p.`'.bqSQL($size)."`='' OR p.`".bqSQL($size)."`='0') ";
                }

                $where = (empty($params['no_operator']) ? 'AND' : '').' ( '.$condition.
                    (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').' ) ';
            }
        }

        return $where;
    }
}
