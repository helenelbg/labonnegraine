<?php

class productsWithDuplicatedReferencesSegment extends SegmentCustom
{
    public $name = 'Products with duplicated references';
    public $liste_hooks = array('segmentAutoSqlQuery', 'segmentAutoSqlQueryGrid'); //array("segmentAutoConfig");

    public function _executeHook_segmentAutoSqlQueryGrid($name, $params = array())
    {
        $array = array();

        $sql = 'SELECT DISTINCT(p1.id_product)
        FROM '._DB_PREFIX_.'product p1, '._DB_PREFIX_."product p2
        WHERE
            p1.id_product!=p2.id_product
            AND p1.reference = p2.reference
            AND (p1.reference IS NOT NULL AND p1.reference!='')
            AND (p2.reference IS NOT NULL AND p2.reference!='')";
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

        return $array;
    }

    public function _executeHook_segmentAutoSqlQuery($name, $params = array())
    {
        $where = ' '.(empty($params['no_operator']) ? 'AND' : '').' p.id_product IN (
                    SELECT DISTINCT(p1.id_product)
                    FROM '._DB_PREFIX_.'product p1, '._DB_PREFIX_."product p2
                    WHERE
                        p1.id_product!=p2.id_product
                        AND p1.reference = p2.reference
                        AND (p1.reference IS NOT NULL AND p1.reference!='')
                        AND (p2.reference IS NOT NULL AND p2.reference!='')
                )";

        return $where;
    }
}
