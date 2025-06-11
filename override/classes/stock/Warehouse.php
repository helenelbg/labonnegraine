<?php
class Warehouse extends WarehouseCore
{
    /**
     * For a given {product, product attribute} gets warehouse list.
     *
     * @param int $id_product ID of the product
     * @param int $id_product_attribute Optional, uses 0 if this product does not have attributes
     * @param int $id_shop Optional, ID of the shop. Uses the context shop id (@see Context::shop)
     *
     * @return array Warehouses (ID, reference/name concatenated)
     */
    public static function getProductWarehouseList($id_product, $id_product_attribute = 0, $id_shop = null)
    {        
        $query = new DbQuery();
        $query->select('acc.semaines');
        $query->from('product', 'p');
        $query->innerJoin('aw_custom_category', 'acc', 'p.id_category_default = acc.id_category');
        $query->where('p.id_product = ' . (int) $id_product);

        $rangee_s = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $retour_semaines = array();
        if ( isset($rangee_s[0]['semaines']) && !empty($rangee_s[0]['semaines']) )
        {
            foreach(explode(';', $rangee_s[0]['semaines']) as $semaine)
            {
                $retour_semaines[] = array('id_warehouse' => $semaine, 'name' => 'Semaine '.$semaine);
            }
        }
        if ( count($retour_semaines) == 0 )
        {
            $weekp1 = (date('W')+1);
            $weekp2 = (date('W')+2);
            
            $weekp1 = date("W", strtotime("now + 1 weeks"));
            $weekp2 = date("W", strtotime("now + 2 weeks"));

            $retour_semaines[] = array('id_warehouse' => 0, 'name' => 'Immédiat');
            $retour_semaines[] = array('id_warehouse' => $weekp1, 'name' => 'Semaine '.$weekp1);
            $retour_semaines[] = array('id_warehouse' => $weekp2, 'name' => 'Semaine '.$weekp2);
        }
        
        return $retour_semaines;
    }
    public static function getProductWarehouseListLBG($id_product, $id_product_attribute = 0, $id_shop = null, $id_cart = null)
    {        
        if ( $id_cart <> null )
        {     
            $query1 = new DbQuery();
            $query1->select('cd.semaine');
            $query1->from('product', 'p');
            $query1->innerJoin('custom_delivery', 'cd', 'cd.id_category = p.id_category_default');
            $query1->where('cd.id_cart = ' . (int) $id_cart);
            $query1->where('p.id_product = ' . (int) $id_product);

            //req_custom = 'SELECT cd.semaine FROM custom_delivery cd LEFT JOIN aw_custom_category acc ON cd.id_category = acc.id_category WHERE cd.id_cart = ' . (int) $id_cart . ' AND acc.id_product = '.(int) $id_product.';';
            //echo $req_custom.'<br />';
            $rangee_s1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query1);

            if ( isset($rangee_s1[0]['semaine']) )
            {
                $retour_semaines[] = array('id_warehouse' => $rangee_s1[0]['semaine'], 'name' => 'Semaine '.$rangee_s1[0]['semaine']);
                return $retour_semaines;
            }
        }

        $query = new DbQuery();
        $query->select('acc.semaines');
        $query->from('product', 'p');
        $query->innerJoin('aw_custom_category', 'acc', 'p.id_category_default = acc.id_category');
        $query->where('p.id_product = ' . (int) $id_product);

        $rangee_s = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $retour_semaines = array();
        if ( isset($rangee_s[0]['semaines']) && !empty($rangee_s[0]['semaines']) )
        {
            foreach(explode(';', $rangee_s[0]['semaines']) as $semaine)
            {
                $retour_semaines[] = array('id_warehouse' => $semaine, 'name' => 'Semaine '.$semaine);
            }
        }

        if ( count($retour_semaines) == 0 )
        {
            
            $query1 = new DbQuery();
            $query1->select('cd.semaine');
            $query1->from('product', 'p');
            $query1->innerJoin('custom_delivery', 'cd', 'cd.id_category = 0');
            $query1->where('cd.id_cart = ' . (int) $id_cart);
            $query1->where('p.id_product = ' . (int) $id_product);

            //req_custom = 'SELECT cd.semaine FROM custom_delivery cd LEFT JOIN aw_custom_category acc ON cd.id_category = acc.id_category WHERE cd.id_cart = ' . (int) $id_cart . ' AND acc.id_product = '.(int) $id_product.';';
            //echo $req_custom.'<br />';
            $rangee_s1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query1);

            if ( isset($rangee_s1[0]['semaine']) )
            {
                $retour_semaines[] = array('id_warehouse' => $rangee_s1[0]['semaine'], 'name' => 'Semaine '.$rangee_s1[0]['semaine']);
                return $retour_semaines;
            }
            else 
            {
                $weekp1 = (date('W')+1);
                $weekp2 = (date('W')+2);
                
                $weekp1 = date("W", strtotime("now + 1 weeks"));
                $weekp2 = date("W", strtotime("now + 2 weeks"));

                $retour_semaines[] = array('id_warehouse' => 0, 'name' => 'Immédiat');
                $retour_semaines[] = array('id_warehouse' => $weekp1, 'name' => 'Semaine '.$weekp1);
                $retour_semaines[] = array('id_warehouse' => $weekp2, 'name' => 'Semaine '.$weekp2);
            }
        }

        return $retour_semaines;
    }
}