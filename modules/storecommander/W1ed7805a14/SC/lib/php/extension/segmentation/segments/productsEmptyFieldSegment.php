<?php

class productsEmptyFieldSegment extends SegmentCustom
{
    public $name = 'Products : Empty field';
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

        $selected_langs = array();
        if (isset($values['selected_langs']) && !empty($values['selected_langs']))
        {
            $selected_langs = explode(',', $values['selected_langs']);
        }

        $html_options = array(
            'name' => _l('Name'),
            'description' => _l('Description'),
            'description_short' => _l('Short description'),
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
        <select id="search_fields" style="width: 100%; height: 5em;" multiple="multiple">';
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
        
        <br/><br/>';
        $html .= '<strong>'._l('Languages').'</strong><br/>
        <select id="selected_langs" style="width: 100%; height: 5em;" multiple="multiple">';
        $languages = Db::getInstance()->executeS('SELECT id_lang,name FROM '._DB_PREFIX_.'lang');
        $languages = array_column($languages, 'name', 'id_lang');
        foreach ($languages as $field_id => $field_name)
        {
            $html .= '<option value="'.$field_id.'" '.(in_array($field_id, $selected_langs) ? 'selected' : '').'>'.$field_name.'</option>';
        }
        $html .= '</select>
        <input type="hidden" name="selected_langs" value="'.$values['selected_langs'].'" />
                    
        <script>
        $(document).ready(function(){
            $("#search_fields").change(function(){
                let fields = $("#search_fields").val().join("-");
                $("input[name=search_fields]").val(fields);
            });
            $("#selected_langs").change(function(){
                let fields = $("#selected_langs").val().join(",");
                $("input[name=selected_langs]").val(fields);
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
                $tmps = explode('-', $auto_params['search_fields']);
                foreach ($tmps as $field)
                {
                    if (!empty($field) && in_array($field, array('name', 'description', 'description_short', 'reference', 'supplier_reference', 'ean13', 'upc', 'isbn', 'mpn')))
                    {
                        switch ($field) {
                            case 'reference':
                            case 'ean13':
                            case 'upc':
                            case 'isbn':
                            case 'mpn':
                                $search_in[] = '(LOWER(p.'.pSQL($field).') IS NULL OR LOWER(p.'.pSQL($field).') = "")';
                                break;
                            case 'supplier_reference':
                                if (version_compare(_PS_VERSION_, '1.5.0.2', '>='))
                                {
                                    $search_in[] = 'p.id_product IN (SELECT DISTINCT(ps.id_product) FROM '._DB_PREFIX_.'product_supplier ps WHERE 1 AND id_product_attribute = 0 AND (LOWER(ps.product_supplier_reference) IS NULL OR LOWER(ps.product_supplier_reference) = ""))';
                                }
                                break;
                            default: ## name, description_short, description
                                $for_lang[] = '(LOWER(pl.'.pSQL($field).') IS NULL OR LOWER(pl.'.pSQL($field).') = "")';
                        }
                    }
                }

                if (!empty($for_lang))
                {
                    $lang_condition = '';
                    if (!empty($auto_params['selected_langs']))
                    {
                        $lang_condition = ' AND pl.id_lang IN ('.pInSQL($auto_params['selected_langs']).')';
                    }
                    $search_in[] = 'p.id_product IN (SELECT DISTINCT(pl.id_product) FROM '._DB_PREFIX_.'product_lang pl WHERE 1 AND ('.implode(' OR ', $for_lang).')'.$lang_condition.')';
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
