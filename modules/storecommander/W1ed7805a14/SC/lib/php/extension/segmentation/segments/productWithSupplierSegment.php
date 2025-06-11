<?php

class productWithSupplierSegment extends SegmentCustom
{
    public $name = 'Products with a specific supplier';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $html = '<strong>'._l('Supplier:').'</strong><br/>';
        $html .= '<select id="id_supplier_multiselect" style="width: 100%; height: 10em;" multiple="multiple">';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $id_suppliers = array();
        if (!empty($values['id_supplier']))
        {
            $id_suppliers = array_filter(explode('-', $values['id_supplier']));
        }

        $sql = 'SELECT t.id_supplier, t.name FROM '._DB_PREFIX_.'supplier t ORDER BY t.name';
        $suppliers = Db::getInstance()->ExecuteS($sql);
        foreach ($suppliers as $supplier)
        {
            $html .= '<option value="'.$supplier['id_supplier'].'" '.(in_array($supplier['id_supplier'], $id_suppliers) ? 'selected' : '').'>'.$supplier['name'].'</option>';
        }
        $html .= '</select>
        <input type="hidden" name="id_supplier" value="'.$values['id_supplier'].'" />
        
        <script>
        $(document).ready(function(){
            $("#id_supplier_multiselect").change(function(){
                var fields = "";
                $.each($("#id_supplier_multiselect option:selected"), function(num, element){
                    var val = $(element).val();
                    fields = fields + val + "-";
                });
                $("input[name=id_supplier]").val(fields);
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

            $array_id_suppliers = array_filter(explode('-', $auto_params['id_supplier']));
            $ids = implode(',', $array_id_suppliers);

            if (!empty($auto_params['id_supplier']))
            {
                $sql = 'SELECT p.id_product
                        FROM '._DB_PREFIX_.'product p 
                            INNER JOIN '._DB_PREFIX_."product_supplier psp ON (p.id_product=psp.id_product)
                        WHERE (p.id_supplier IN (".pInSQL($ids).") OR psp.id_supplier IN (".pInSQL($ids)."))".
                        (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').
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
            if (!empty($auto_params['id_supplier']))
            {
                $array_id_suppliers = array_filter(explode('-', $auto_params['id_supplier']));
                $ids = implode(',', $array_id_suppliers);

                $where = ' '.(empty($params['no_operator']) ? 'AND' : '')." ( (
                    p.id_supplier IN (".pInSQL($ids).")
                    OR
                    p.id_product IN (SELECT DISTINCT(psp_seg.id_product)
                        FROM "._DB_PREFIX_."product_supplier psp_seg 
                        WHERE psp_seg.id_product=p.id_product AND psp_seg.id_supplier IN (".pInSQL($ids)."))
                )".
                    (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').' ) ';
            }
        }

        return $where;
    }
}
