<?php

class productsWithTooLowSellingPriceSegment extends SegmentCustom
{
    public $name = 'Products with a too low selling price';
    public $liste_hooks = array('segmentAutoConfig', 'segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid');

    public function _executeHook_segmentAutoConfig($name, $params = array())
    {
        $values = array();
        if (!empty($params['values']))
        {
            $values = unserialize($params['values']);
        }

        $html = '<p>'._l("Minimum threshold (percentage) between the sale price and the purchase price").": </p>".
            '<input type="text" id="x_operator" name="x_operator" style="width: 20%;" value="'.(!empty($values['x_operator']) ? $values['x_operator'] : '').'" />'.' %'.
            "<br/><br/>"._l("The segment will display products whose selling price is lower than the purchase price")." + ".(empty($values['x_operator']) ? "X" : $values['x_operator'])." %.".
            '<br/><br/><strong>'._l('Display products').'</strong><br/>
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
        }

        if (!empty($auto_params['x_operator']))
        {
            $operation = " < " . (float)$auto_params['x_operator'];

            $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'ps.' : 'p.';

            $sql = 'SELECT p.id_product, ' . $alias . 'price AS pv, ' . $alias . 'wholesale_price AS pa, (' . $alias . 'price - ' . $alias . 'wholesale_price) / (' . $alias . 'wholesale_price / 100) AS margin
                FROM ps_product p ' .
                ((version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'INNER JOIN ps_product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = ' . SCI::getSelectedShop() . ')' : '') .
                ' WHERE (' . $alias . 'price - ' . $alias . 'wholesale_price) / (' . $alias . 'wholesale_price / 100) ' . pSQL($operation) . ' ' .
                (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND '.$alias.'active='" . ($auto_params['active_pdt'] == 'active' ? '1' : '0') . "'" : '');

            $res = Db::getInstance()->ExecuteS($sql);
            foreach ($res as $row) {
                $type = _l('Product');
                if (SCMS) {
                    $element = new Product($row['id_product'], true);
                } else {
                    $element = new Product($row['id_product']);
                }
                $name = $element->name[$params['id_lang']];
                $infos = $element->reference;
                $array[] = array($type, $name, $infos, 'id' => 'product_' . $row['id_product'], 'id_display' => $row['id_product']);
            }
        }

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
        }

        if (!empty($auto_params['x_operator']))
        {
            $operation = " < ".(float) $auto_params['x_operator'];
            $alias = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) ? 'prs.' : 'p.';
            $where = ' '.(empty($params['no_operator']) ? 'AND ' : '').'
            ('.$alias.'price - '.$alias.'wholesale_price) / ('.$alias.'wholesale_price / 100) '. pSQL($operation).' '.
                (!empty($auto_params['active_pdt']) && $auto_params['active_pdt'] != 'all' ? " AND '.$alias.'active='".($auto_params['active_pdt'] == 'active' ? '1' : '0')."'" : '');
        }
        return $where;
    }
}
