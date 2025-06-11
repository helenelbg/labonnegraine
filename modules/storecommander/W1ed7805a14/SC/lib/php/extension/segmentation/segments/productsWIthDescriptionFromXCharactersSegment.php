<?php

class productsWIthDescriptionFromXCharactersSegment extends SegmentCustom
{
    public $name = 'Products with a description of less, more or equal to X characters';
    public $liste_hooks = ['segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid'];

    private $operators = [
        'equal' => '=',
        'supp' => '>',
        'inf' => '<',
    ];

    public function _executeHook_segmentAutoConfig($name, $params = [])
    {
        $values = [];
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '
        <select name="operator" style="width: 15%">';
        foreach($this->operators as $key => $value) {
            $html .= "\t".'<option value="'.$key.'" '.(!empty($values['operator']) && $values['operator'] == $key  ? 'selected' : '').'>'.$value.'</option>';
        }
        $html .= '
        </select>
        <strong>'._l('Nb characters')._l(':').'</strong>
        <input type="number" id="x_characters" name="x_characters" style="width: 30%;" value="'.(!empty($values['x_characters']) ? $values['x_characters'] : '').'" />
        
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

        $productIdList = $this->getMainDbQuery($params);
        if(!$productIdList)
        {
            return $data_products;
        }

        foreach ($productIdList as $id_product)
        {
            $type = _l('Product');
            $element = new Product($id_product, SCMS, $params['id_lang']);
            $name = $element->name;
            $infos = $element->reference;
            $data_products[] = [$type, $name, $infos, 'id' => 'product_'.$id_product, 'id_display' => $id_product];
        }
        return $data_products;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = [])
    {
        $where = 'FALSE';
        $operator = (empty($params['no_operator']) ? 'AND' : '');

        $productIdList = $this->getMainDbQuery($params);
        if($productIdList)
        {
            $where = '(p.id_product IN ('.implode(',', $productIdList).'))';
        }
        return ' '.$operator.' '.$where;
    }

    /**
     * @param $segmentParams
     * @return array
     * @throws PrestaShopException
     */
    protected function getMainDbQuery($segmentParams = [])
    {
        if (empty($segmentParams['auto_params']))
        {
            return [];
        }

        $auto_params = unserialize($segmentParams['auto_params']);

        if (
            (empty($auto_params['x_characters']) || !is_numeric($auto_params['x_characters']))
            ||
            (empty($auto_params['operator'])) || !in_array($auto_params['operator'], array_keys($this->operators)))
        {
            return [];
        }

        $operator = $auto_params['operator'];
        $nbCharacters = (int)$auto_params['x_characters'];


        $lang_id = (int) Configuration::get('PS_LANG_DEFAULT');
        if(isset($auto_params['lang_id']))
        {
            $lang_id = (int) $auto_params['lang_id'];
        }

        $whereCondition = [
            'plang.`id_lang` = '.(int)$lang_id,
            'plang.`id_shop` = '.(int)SCI::getSelectedShop(),
            'plang.`description` <> ""',
            'plang.`description` IS NOT NULL'
        ];

        $whereCondition = '('.implode(' AND ', $whereCondition).')';

        if (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all')
        {
            $whereCondition .= ' AND p.active = '.(int) ($auto_params['active_pdt'] == 'active');
        }

        $productsQuery = new DbQuery();
        $productsQuery
            ->select('DISTINCT plang.id_product, description')
            ->from('product_lang','plang')
            ->leftJoin('product', 'p', '(p.id_product = plang.id_product)')
            ->where($whereCondition)
        ;

        $res = Db::getInstance()->executeS($productsQuery);
        $listId = [];
        if ($res){
            foreach($res as $row)
            {
                if($this->needInsertId($operator, $nbCharacters, $row['description']))
                {
                    $listId[] = (int)$row['id_product'];
                }
            }
        }

        return $listId;
    }


    /**
     * @param string $operator
     * @param int $nbCharacters
     * @param string $data
     * @return bool
     */
    private function needInsertId($operator, $nbCharacters, $data)
    {
        $data = strip_tags($data);
        $dataLength = strlen(trim($data));
        switch ($operator)
        {
            case 'equal':
                return $dataLength == $nbCharacters;
            case 'supp':
                return $dataLength > $nbCharacters;
            case 'inf':
            default:
                return $dataLength < $nbCharacters;
        }
    }
}