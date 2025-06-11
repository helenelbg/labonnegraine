<?php

class productsWithDuplicateMetaTitleAndOrDescriptionSegment extends SegmentCustom
{
    public $name = 'Products with duplicate meta title and or meta description';

    public $liste_hooks = [
        'segmentAutoConfig',
        'segmentAutoSqlQuery',
        'segmentAutoSqlQueryGrid',
    ];

    public function _executeHook_segmentAutoConfig($name, $params = [])
    {
        $values = [];
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $search_fields = array();
        if (!empty($values['search_fields']))
        {
            $search_fields = explode('-', $values['search_fields']);
        }

        return '
        <strong>'._l('Search in?').'</strong><br/>
        <select id="search_fields" style="width: 100%; height: 5em;" multiple="multiple">
            <option value="meta_title" '.(in_array('meta_title', $search_fields) ? 'selected' : '').'>'._l('meta_title').'</option>
            <option value="meta_description" '.(in_array('meta_description', $search_fields) ? 'selected' : '').'>'._l('meta_description').'</option>
        </select>
        <input type="hidden" name="search_fields" value="'.(isset($values['search_fields']) ? $values['search_fields'] : '').'" />
                    
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
                let fields = $(this).val()
                if(fields !== null)
                {
                    fields = fields.join("-");
                }
                $("input[name=search_fields]").val(fields);
            });
        });
        </script>';
    }

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = [])
    {
        $data_products = [];

        $sqlQuery = $this->getMainDbQuery($params);
        if(!$sqlQuery)
        {
            return $data_products;
        }

        $res = Db::getInstance()->executeS($sqlQuery);

        if(!$res)
        {
            return $data_products;
        }

        foreach ($res as $row)
        {
            $type = _l('Product');
            $element = new Product($row['id_product'], SCMS, $params['id_lang']);
            $name = $element->name;
            $infos = $element->reference;
            $data_products[] = [$type, $name, $infos, 'id' => 'product_'.$row['id_product'], 'id_display' => $row['id_product']];
        }
        return $data_products;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = [])
    {
        $where = 'FALSE';
        $operator = (empty($params['no_operator']) ? 'AND' : '');

        $sqlQuery = $this->getMainDbQuery($params);
        if($sqlQuery)
        {
            $where = '(p.id_product IN ('.$sqlQuery->build().'))';
        }
        return ' '.$operator.' '.$where;
    }

    /**
     * @param $segmentParams
     * @return DbQuery|false
     */
    protected function getMainDbQuery($segmentParams = [])
    {
        if (empty($segmentParams['auto_params']))
        {
            return false;
        }

        $auto_params = unserialize($segmentParams['auto_params']);

        if (empty($auto_params['search_fields']))
        {
            return false;
        }

        $searchFieldSelection = explode('-', $auto_params['search_fields']);
        if (empty($searchFieldSelection))
        {
            return false;
        }

        $whereCondition = [];
        foreach($searchFieldSelection as $field)
        {
            ## subquery
            $itemQuery = new DbQuery();
            $itemQuery->select('DISTINCT(pl.`'.bqSQL($field).'`)')
                        ->from('product_lang','pl')
                        ->where('pl.`'.bqSQL($field).'` IS NOT NULL AND pl.`'.bqSQL($field).'` <> ""')
                        ->groupBy('pl.`'.bqSQL($field).'`')
                        ->having('count(*) > 1')
            ;
            $whereCondition[] = 'plang.`'.bqSQL($field).'` IN ('.$itemQuery->build().')';
        }

        $whereCondition = '('.implode(' OR ', $whereCondition).')';

        if (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all')
        {
            $whereCondition .= ' AND p.active = '.(int) ($auto_params['active_pdt'] == 'active');
        }

        $productsQuery = new DbQuery();
        $productsQuery
            ->select('DISTINCT (plang.id_product)')
            ->from('product_lang','plang')
            ->leftJoin('product', 'p', '(p.id_product = plang.id_product)')
            ->where($whereCondition)
        ;

        return $productsQuery;
    }
}