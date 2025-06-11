<?php

class combinationsEmptyFieldSegment extends SegmentCustom
{
    public $name = 'Products (Combinations) : Empty field';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $search_fields = array();
        if (!empty($values['search_fields']))
        {
            $search_fields = explode('-', $values['search_fields']);
        }

        $html_options = array(
            'reference' => _l('Reference'),
            'supplier_reference' => _l('Supplier reference'),
            'ean13' => _l('EAN13'),
            'upc' => _l('UPC'),
        );
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
        {
            $html_options['isbn'] = _l('ISBN');
        }
        if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
        {
            $html_options['mpn'] = _l('MPN');
        }

        $html = '<strong>'._l('Search in?').'</strong><br/>
        <select id="search_fields" style="width: 100%; height: 10em;" multiple="multiple">';
        foreach ($html_options as $field_id => $field_name)
        {
            $html .= '<option value="'.$field_id.'" '.(in_array($field_id, $search_fields) ? 'selected' : '').'>'.$field_name.'</option>';
        }
        $html .= '</select>
        <input type="hidden" name="search_fields" value="'.$values['search_fields'].'" />
        
        <br/><br/>
        <strong>'._l('Display products').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Active and nonactive').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Active only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonactive only').'</option>
        </select>
                    
        <script>
        $(document).ready(function(){
            $("#search_fields").change(function(){
                let fields = $("#search_fields").val().join("-");
                $("input[name=search_fields]").val(fields);
            });
        });
        </script>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';
        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['search_fields']))
            {
                $search_in = $for_lang = array();
                $contain_combination_field = null;
                $tmps = explode('-', $auto_params['search_fields']);
                foreach ($tmps as $field)
                {
                    if (!empty($field) && in_array($field, array('reference', 'supplier_reference', 'ean13', 'upc')))
                    {
                        switch ($field) {
                            case 'reference':
                            case 'ean13':
                            case 'upc':
                                $contain_combination_field['base'] = '(LOWER(pa.'.pSQL($field).') IS NULL OR LOWER(pa.'.pSQL($field).') = "")';
                                break;
                            case 'supplier_reference':
                                if (version_compare(_PS_VERSION_, '1.5.0.2', '>='))
                                {
                                    $contain_combination_field['supplier'] = '(LOWER(ps.product_supplier_reference) IS NULL OR LOWER(ps.product_supplier_reference) = ""))';
                                }
                                break;
                        }
                    }
                }

                if (!empty($contain_combination_field))
                {
                    if (isset($contain_combination_field['base']))
                    {
                        $search_in[] = 'p.id_product IN (SELECT DISTINCT(pa.id_product) FROM '._DB_PREFIX_.'product_attribute pa WHERE 1 AND ('.implode(' OR ', $contain_combination_field).'))';
                    }
                    if (isset($contain_combination_field['supplier']))
                    {
                        $search_in[] = 'p.id_product IN (SELECT DISTINCT(ps.id_product) FROM '._DB_PREFIX_.'product_supplier ps WHERE 1 AND ps.id_product_attribute > 0 AND '.$contain_combination_field['supplier'].')';
                    }
                }

                if (!empty($search_in))
                {
                    $search_in = implode(' OR ', $search_in);
                    if (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all')
                    {
                        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' ( ( '.$search_in.") AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."')";
                    }
                    else
                    {
                        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' ( '.$search_in.') ';
                    }
                }
            }
        }

        return $where;
    }
}
