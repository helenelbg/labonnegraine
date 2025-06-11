<?php

class productsWithImageWithoutAltValueSegment extends SegmentCustom
{
    public $name = 'Products with images without legend (alt tag)';

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

        $html = '
        <strong>'._l('Display products').'</strong><br/>
        <select name="active_pdt" style="width: 100%">
            <option value="all" '.(empty($values['active_pdt']) || $values['active_pdt'] == 'all' ? 'selected' : '').'>'._l('Active and nonactive').'</option>
            <option value="active" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'active' ? 'selected' : '').'>'._l('Active only').'</option>
            <option value="nonactive" '.(!empty($values['active_pdt']) && $values['active_pdt'] == 'nonactive' ? 'selected' : '').'>'._l('Nonactive only').'</option>
        </select>
        
        <br/><br/>
        <strong>'._l('Lang:').'</strong><br/>
        <select name="lang_id" style="width: 100%">';
        foreach(Language::getLanguages(false) as $lang) {
            $html .= '<option value="'.(int)$lang['id_lang'].'" ' . (isset($values['lang_id']) && (int)$values['lang_id'] == (int)$lang['id_lang'] ? 'selected' : '') . '>' . $lang['name'] . '</option>';
        }
        $html .= '</select>';

        return $html;
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

        $lang_id = (int) Configuration::get('PS_LANG_DEFAULT');
        if(isset($auto_params['lang_id']))
        {
            $lang_id = (int) $auto_params['lang_id'];
        }

        $whereCondition = [
            'pil.`legend` = ""',
            'pil.`legend` IS NULL',
        ];

        $whereCondition = '('.implode(' OR ', $whereCondition).')';

        if (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all')
        {
            $whereCondition .= ' AND p.active = '.(int) ($auto_params['active_pdt'] == 'active');
        }

        $productsQuery = new DbQuery();
        $productsQuery
            ->select('DISTINCT (p.id_product)')
            ->from('product','p')
            ->rightJoin('image','pim', 'pim.id_product = p.id_product')
            ->leftJoin('image_lang','pil', 'pil.id_image = pim.id_image AND pil.`id_lang` = '.(int)$lang_id)
            ->where($whereCondition)
        ;

        return $productsQuery;
    }
}