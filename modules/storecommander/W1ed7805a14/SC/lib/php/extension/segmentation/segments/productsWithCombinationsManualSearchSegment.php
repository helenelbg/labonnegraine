<?php

class productsWithCombinationsManualSearchSegment extends SegmentCustom
{
    public $name = 'Products (and/or combinations): manual expression search';
    public $liste_hooks = array('segmentAutoConfig', 'productBeforeLoadXML', 'segmentAutoSqlQuery');

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

        $html = '<strong>'._l('Search in?').'</strong><br/>
        <select id="search_fields" style="width: 100%; height: 10em;" multiple="multiple">
            <option value="name" '.(in_array('name', $search_fields) ? 'selected' : '').'>'._l('Name').'</option>
            <option value="description" '.(in_array('description', $search_fields) ? 'selected' : '').'>'._l('Description').'</option>
            <option value="description_short" '.(in_array('description_short', $search_fields) ? 'selected' : '').'>'._l('Short description').'</option>
            <option value="reference" '.(in_array('reference', $search_fields) ? 'selected' : '').'>'._l('Reference').'</option>
            <option value="supplier_reference" '.(in_array('supplier_reference', $search_fields) ? 'selected' : '').'>'._l('Supplier reference').'</option>
            <option value="ean13" '.(in_array('ean13', $search_fields) ? 'selected' : '').'>'._l('EAN13').'</option>
            <option value="upc" '.(in_array('upc', $search_fields) ? 'selected' : '').'>'._l('UPC').'</option>
        </select>
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
                var fields = "";
                $.each($("#search_fields option:selected"), function(num, element){
                    var val = $(element).val();
                    fields = fields + val + "-";
                });
                $("input[name=search_fields]").val(fields);
            });
        });
        </script>';

        return $html;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';
        if (!empty($params['auto_params']) && !empty($params['segment_params_1']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['search_fields']))
            {
                $search = urldecode($params['segment_params_1']);
                if (!empty($search))
                {
                    $search_in = array();
                    $contain_combination_field = null;
                    $tmps = explode('-', $auto_params['search_fields']);
                    foreach ($tmps as $field)
                    {
                        if (!empty($field))
                        {
                            switch ($field) {
                                case 'reference':
                                case 'ean13':
                                case 'upc':
                                    $contain_combination_field['base'] = 'LOWER(pa.'.pSQL($field).") LIKE ('%".pSQL(strtolower($search))."%')";
                                    break;
                                case 'supplier_reference':
                                    if (version_compare(_PS_VERSION_, '1.5.0.2', '>='))
                                    {
                                        $contain_combination_field['supplier'] = "LOWER(ps.product_supplier_reference) LIKE ('%".pSQL(strtolower($search))."%')";
                                    }
                                    break;
                                default:
                                    $search_in[] = 'LOWER('.pSQL($field).") LIKE ('%".pSQL(strtolower($search))."%')";
                            }
                        }
                    }

                    if (!empty($contain_combination_field))
                    {
                        if (array_key_exists('base', $contain_combination_field))
                        {
                            $search_in[] = 'p.id_product IN (SELECT DISTINCT(pa.id_product) FROM '._DB_PREFIX_.'product_attribute pa WHERE 1 AND ('.implode(' OR ', $contain_combination_field).'))';
                        }
                        if (array_key_exists('supplier', $contain_combination_field))
                        {
                            $search_in[] = 'p.id_product IN (SELECT DISTINCT(ps.id_product) FROM '._DB_PREFIX_.'product_supplier ps WHERE 1 AND '.$contain_combination_field['supplier'].')';
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
        }

        return $where;
    }

    public function _executeHook_productBeforeLoadXML($name, $params = array())
    {
        $js = '';
        $ids = array();
        $sql = 'SELECT id_segment
                FROM '._DB_PREFIX_."sc_segment
                WHERE auto_file = 'productsWithCombinationsManualSearchSegment'";
        $res = Db::getInstance()->ExecuteS($sql);
        if (!empty($res))
        {
            foreach ($res as $row)
            {
                $ids[] = 'catselection=="seg_'.$row['id_segment'].'"';
            }
        }
        if (!empty($ids))
        {
            $js = '
            if('.implode(' || ', $ids).')
            {
                var search = prompt("'._l('What term do you want to look for?').'", "");
                if (search!=undefined && search!=null && search!="") {
                    params_supp = params_supp+"&segment_params_1="+encodeURIComponent(search);
                }
            }
            ';
        }

        return $js;
    }
}
