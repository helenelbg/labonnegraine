<?php

class productWithManufacturerSegment extends SegmentCustom
{
    public $name = 'Products with a specific manufacturer';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $html = '<strong>'._l('Manufacturer:').'</strong><br/>';
        $html .= '<select id="id_manufacturer_multiselect" style="width: 100%; height: 10em;" multiple="multiple">';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $id_manufacturers = array();
        if (!empty($values['id_manufacturer']))
        {
            $id_manufacturers = array_filter(explode('-', $values['id_manufacturer']));
        }

        $sql = 'SELECT m.id_manufacturer, m.name FROM '._DB_PREFIX_.'manufacturer m ORDER BY m.name';
        $manufacturers = Db::getInstance()->ExecuteS($sql);
        foreach ($manufacturers as $manufacturer)
        {
            $html .= '<option value="'.$manufacturer['id_manufacturer'].'" '.(in_array($manufacturer['id_manufacturer'], $id_manufacturers) ? 'selected' : '').'>'.$manufacturer['name'].'</option>';
        }
        $html .= '</select>
        <input type="hidden" name="id_manufacturer" value="'.$values['id_manufacturer'].'" />

        <script>
        $(document).ready(function(){
            $("#id_manufacturer_multiselect").change(function(){
                var fields = "";
                $.each($("#id_manufacturer_multiselect option:selected"), function(num, element){
                    var val = $(element).val();
                    fields = fields + val + "-";
                });
                $("input[name=id_manufacturer]").val(fields);
            });
        });
        </script>
                    
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

            $array_id_manufacturers = array_filter(explode('-', $auto_params['id_manufacturer']));
            $ids = implode(',', $array_id_manufacturers);

            if (!empty($auto_params['id_manufacturer']))
            {
                $sql = 'SELECT id_product
                        FROM '._DB_PREFIX_.'product p WHERE id_manufacturer IN ('.pInSQL($ids).')'.
                        (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').
                        ' GROUP BY p.id_product ORDER BY p.id_product';

                $res = Db::getInstance()->ExecuteS($sql);
                if (!empty($res))
                {
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
                        $array[] = array($type, $name, $infos, 'id' => 'product_' . $row['id_product'], 'id_display' => $row['id_product']);
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
            if (!empty($auto_params['id_manufacturer']))
            {
                $array_id_manufacturers = array_filter(explode('-', $auto_params['id_manufacturer']));
                $ids = implode(',', $array_id_manufacturers);

                $where = (empty($params['no_operator']) ? 'AND' : '')." ( p.id_manufacturer IN (".pInSQL($ids).")".
                    (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').' ) ';
            }
        }

        return $where;
    }
}
