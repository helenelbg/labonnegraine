<?php
    class ControleProduit
    {
        public $id;
        public $id_controle;
        public $id_product;
        public $id_product_attribute;
        public $quantite_prepare;

        public function __construct()
        {
            
        }

        public static function check($id_controle = '', $id_product = '', $id_product_attribute = '')
        {
            if ( $id_controle > 0 && $id_product > 0 )
            {
                $req = new DbQuery();
                $req->select('lgcp.id_controle_produit, lgcp.id_controle, lgcp.id_product, lgcp.id_product_attribute, lgcp.quantite_prepare');
                $req->from('LogiGraine_controle_produit', 'lgcp');
                $req->where('lgcp.id_controle = "'.$id_controle.'"');
                $req->where('lgcp.id_product = "'.$id_product.'"');
                $req->where('lgcp.id_product_attribute = "'.$id_product_attribute.'"');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_controle_produit']) && !empty($resu[0]['id_controle_produit']) )
                {
                    return $resu[0];
                }
                else 
                {
                    return false;
                }
            }
        }

        public static function checkGroup($id_order = '', $id_product = '', $id_product_attribute = '')
        {
            if ( $id_order > 0 && $id_product > 0 )
            {
                $req = new DbQuery();
                $req->select('lgcp.id_controle_produit, lgcp.id_controle, lgcp.id_product, lgcp.id_product_attribute, lgcp.quantite_prepare');
                $req->from('LogiGraine_controle_produit', 'lgcp');
                $req->leftJoin('LogiGraine_controle', 'lgc', 'lgcp.id_controle = lgc.id_controle');
                $req->where('lgc.id_order = "'.$id_order.'"');
                $req->where('lgcp.id_product = "'.$id_product.'"');
                $req->where('lgcp.id_product_attribute = "'.$id_product_attribute.'"');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_controle_produit']) && !empty($resu[0]['id_controle_produit']) )
                {
                    return $resu[0];
                }
                else 
                {
                    return false;
                }
            }
        }
    }
?>