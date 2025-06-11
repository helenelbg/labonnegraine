<?php

class productsManualSearchSegment extends SegmentCustom
{
    public $name = 'Products: manual expression search';
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
                    $search_in = '';
                    $tmps = explode('-', $auto_params['search_fields']);
                    foreach ($tmps as $tmp)
                    {
                        if (!empty($tmp))
                        {
                            if (!empty($search_in))
                            {
                                $search_in .= ' OR ';
                            }
                            $search_in .= 'LOWER('.pSQL($tmp).") LIKE ('%".pSQL(strtolower($search))."%') ";
                        }
                    }
                    if (!empty($search_in))
                    {
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
                WHERE auto_file = 'productsManualSearchSegment'";
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
