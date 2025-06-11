<?php

class productsSearchSegment extends SegmentCustom
{
    public $name = 'Products: expression search';
    public $liste_hooks = array(
            'segmentAutoConfig',
            'segmentAutoSqlQuery',
            'segmentAutoSqlQueryGrid',
        );

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

        $html = '<strong>'._l('What term do you want to look for?').'</strong><br/>
        <input type="text" name="search_words" value="'.((!empty($values['search_words'])) ? $values['search_words'] : '').'" style="width: 100%;" />
        <br/><br/>
        <strong>'._l('Search in?').'</strong><br/>
        <select id="search_fields" style="width: 100%; height: 10em;" multiple="multiple">
            <option value="name" '.(in_array('name', $search_fields) ? 'selected' : '').'>'._l('Name').'</option>
            <option value="description" '.(in_array('description', $search_fields) ? 'selected' : '').'>'._l('Description').'</option>
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

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['search_fields']) && !empty($auto_params['search_words']))
            {
                $search = $auto_params['search_words'];
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
                        $where = ' AND ( ( '.$search_in.") AND p.active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."')";
                    }
                    else
                    {
                        $where = ' AND ( '.$search_in.') ';
                    }

                    $sql = 'SELECT DISTINCT(p.id_product)
                    FROM '._DB_PREFIX_.'product p
                        INNER JOIN '._DB_PREFIX_."product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang=".(int)$params['id_lang'].")
                    WHERE 1=1".$where;
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
        }

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';
        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
            if (!empty($auto_params['search_fields']) && !empty($auto_params['search_words']))
            {
                $search = $auto_params['search_words'];
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

        return $where;
    }
}
