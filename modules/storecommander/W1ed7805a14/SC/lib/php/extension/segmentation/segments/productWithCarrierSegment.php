<?php

class productWithCarrierSegment extends SegmentCustom
{
    public $name = 'Products with a specific carrier';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $html = '<strong>'._l('Carrier').' : '.'</strong><br/>';
        $html .= '<select id="id_carrier" style="width: 100%; height: 10em;" multiple="multiple">';

        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $id_carriers = array();
        if (!empty($values['id_carriers']))
        {
            $id_carriers = array_filter(explode('-', $values['id_carriers']));
        }

        $sql = 'SELECT c.id_carrier,c.name
                    FROM `'._DB_PREFIX_.'carrier` c '.
                    (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN `'._DB_PREFIX_.'carrier_shop` cs ON (c.id_carrier = cs.id_carrier AND cs.id_shop ='.(int) SCI::getSelectedShop().') ' : '').
                    ' WHERE c.active=1 '.
                ' ORDER BY c.name';
        $carriers = Db::getInstance()->ExecuteS($sql);
        foreach ($carriers as $carrier)
        {
            $html .= '<option value="'.$carrier['id_carrier'].'" '.(in_array($carrier['id_carrier'], $id_carriers) ? 'selected' : '').'>'.$carrier['name'].'</option>';
        }
        $html .= '</select>
        <input type="hidden" name="id_carriers" value="'.$values['id_carriers'].'" />
        
        <script>
        $(document).ready(function(){
            $("#id_carrier").change(function(){
                var fields = "";
                $.each($("#id_carrier option:selected"), function(num, element){
                    var val = $(element).val();
                    fields = fields + val + "-";
                });
                $("input[name=id_carriers]").val(fields);
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

            $array_id_carriers = array_filter(explode('-', $auto_params['id_carriers']));
            $ids = implode(',', $array_id_carriers);

            if (!empty($auto_params['id_carriers']))
            {
                $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'ps.' : 'p.';

                $sql = 'SELECT p.id_product
                        FROM `'._DB_PREFIX_.'product` p '.
                            (version_compare(_PS_VERSION_, '1.5.0.0', '>=') ? 'INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop ='.(int) SCI::getSelectedShop().') ' : '').'
                            INNER JOIN `'._DB_PREFIX_.'product_carrier` pc ON (p.id_product=pc.id_product AND pc.id_shop='.(int) SCI::getSelectedShop().')'.'
                            INNER JOIN `'._DB_PREFIX_.'carrier` c ON (pc.id_carrier_reference=c.id_reference)
                        WHERE c.id_carrier IN ('.pInSQL($ids).')'.
                        (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? ' AND '.pSQL($alias).'active='.($auto_params['active_pdt'] == 'active' ? '1' : '0') : '').
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
                        $array[] = array($type, $name, $infos, 'id' => 'product_'.$row['id_product'], 'id_display' => $row['id_product']);
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
            if (!empty($auto_params['id_carriers']))
            {
                $array_id_carriers = array_filter(explode('-', $auto_params['id_carriers']));
                $ids = implode(',', $array_id_carriers);

                $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'p.' : 'prs.';

                $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' ( (
                    p.id_product IN (SELECT DISTINCT(pc_seg.id_product)
                        FROM `'._DB_PREFIX_.'product_carrier` pc_seg 
                        INNER JOIN `'._DB_PREFIX_.'carrier` c_seg ON (pc_seg.id_carrier_reference=c_seg.id_reference AND pc_seg.id_shop='.(int) SCI::getSelectedShop().')
                        WHERE c_seg.id_carrier IN ('.pInSQL($ids).'))
                )'.(!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND ".pSQL($alias)."active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '').' ) ';
            }
        }
        return $where;
    }
}
