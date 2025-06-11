<?php

class productsWithoutOrderSegment extends SegmentCustom
{
    public $name = 'Products without order';
    public $liste_hooks = array(
            'segmentAutoSqlQuery',
            'segmentAutoSqlQueryGrid',
        );

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $data_products = array();

        if (!empty($params['auto_params']))
        {
            $auto_params = unserialize($params['auto_params']);
        }

        $sql = 'SELECT p.id_product FROM `'._DB_PREFIX_.'product` p
            WHERE p.id_product NOT IN
            (SELECT distinct od.product_id FROM `'._DB_PREFIX_.'order_detail` od)';
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
            $data_products[] = array($type, $name, $infos, 'id' => 'product_'.$row['id_product'], 'id_display' => $row['id_product']);
        }
        return $data_products;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = '';
        if (!empty($params['auto_params'])) {
            $auto_params = unserialize($params['auto_params']);
        }

        $where=' '.(empty($params['no_operator']) ? 'AND' : '').' p.id_product NOT IN
            (SELECT distinct od.product_id FROM `'._DB_PREFIX_.'order_detail` od)';

        return $where;
    }
}
