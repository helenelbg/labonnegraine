<?php
    class Commande
    {
        public $id;
        public $id_pda;

        public function __construct()
        {
            
        }

        public static function getCommandesByPda($id_pda = 0, $today = 0)
        {
            if ( $id_pda > 0 )
            {
                $req = new DbQuery();
                $req->select('lgrm.id_order');
                $req->from('LogiGraine_pda_order', 'lgrm');
                $req->leftJoin('LogiGraine_controle', 'lgc', 'lgrm.`id_order` = lgc.`id_order`');
                $req->where('lgrm.id_pda = "'.$id_pda.'"');
                if ( $today == 1 )
                {
                    $req->where('((lgc.valide = 0) OR (lgc.valide = 1 AND lgc.date_fin LIKE "'.date('Y-m-d').' %"))');
                }
                else
                {
                    $req->where('lgc.valide = 0');
                }
                $req->orderBy('lgrm.`id_order` ASC');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                $resultat = array();
                foreach($resu as $rangee)
                {
                    $resultat[] = $rangee['id_order'];
                }
                return $resultat;
            }
            return false;
        }

        public static function getProductsByOrder($id_order = 0)
        {
            if ( $id_order > 0 )
            {
                $req = new DbQuery();
                $req->select('od.product_id, od.product_attribute_id, od.product_name, od.product_quantity, od.product_quantity_refunded, product_ean13, product_reference, pa.default_on');
                $req->from('order_detail', 'od');
                $req->leftJoin('product_attribute', 'pa', 'od.product_attribute_id = pa.id_product_attribute');
                $req->where('od.id_order = "'.$id_order.'"');
                $req->where('od.product_id <> "3128" AND od.product_id <> "1850" AND od.product_id <> "1851" AND od.product_id <> "2638" AND od.product_id <> "1849"');//Carte cadeau et box
                $req->orderBy('od.`product_reference` ASC');
                
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                $resultat = array();
                foreach($resu as $rangee)
                {
                    $rangee['quantity_final'] = $rangee['product_quantity'] - $rangee['product_quantity_refunded'];
                    $tmp = explode('(', $rangee['product_name']);
                    $rangee['product_name_1'] = $tmp[0];
                    $rangee['product_name_2'] = str_replace(')', '',$tmp[1]);
                    $resultat[] = $rangee;
                }
                return $resultat;
            }
            return false;
        }

        public static function getOrdersByZone($zone = 0)
        {
            if ( $zone <> 0 )
            {
                $cmds1 = Order::getOrderIdsByStatusByZone(2, 0, $zone); // Paiement accepté
                $cmds2 = Order::getOrderIdsByStatusByZone(3, 0, $zone); // Préparation en cours
                //$cmds3 = Order::getOrderIdsByStatusByZone(43, 0, $zone); // Préparation en cours - Picking OK
                //$cmds4 = Order::getOrderIdsByStatusByZone(20, 0, $zone); // Préparation en cours - Etiquette OK
                //$cmds5 = Order::getOrderIdsByStatusByZone(4, 0, $zone); // En cours de livraison
                //$cmds6 = Order::getOrderIdsByStatusByZone(42, 0, $zone); // Colis disponible au point de retrait Click and Collect
                //$cmds_final = array_merge($cmds1, $cmds2, $cmds3);
                $cmds_final = array_merge($cmds1, $cmds2);
                
                return $cmds_final;
            }
            return false;
        }

        public static function getZones($id_zone = 0)
        {
            $req = new DbQuery();
            $req->select('lgz.id_zone, lgz.libelle_zone, lgz.couleur_zone');
            $req->from('LogiGraine_admin_zone', 'lgz');
            if ( $id_zone <> 0 )
            {
                $req->where('lgz.id_zone = "'.$id_zone.'"');
            } 
            $req->orderBy('lgz.`ordre_zone` ASC');
            
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
            return $resu;
        }

        public static function getPdas($id_pda = 0)
        {
            $req = new DbQuery();
            $req->select('lgp.id_pda, lgp.nom_pda');
            $req->from('LogiGraine_pda', 'lgp');
            if ( $id_pda <> 0 )
            {
                $req->where('lgp.id_pda = "'.$id_pda.'"');
            } 
            $req->orderBy('lgp.`nom_pda` ASC');
            
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
            return $resu;
        }

        public static function getZoneByOrder($id_order = 0)
        {
            if ( $id_order > 0 )
            {
                $zone1 = Order::getOrderIdsByStatusByZone(0, 0, 1, $id_order);
                $zone2 = Order::getOrderIdsByStatusByZone(0, 0, 2, $id_order);
                $zone3 = Order::getOrderIdsByStatusByZone(0, 0, 3, $id_order);
                $zone4 = Order::getOrderIdsByStatusByZone(0, 0, 4, $id_order);
                $zone5 = Order::getOrderIdsByStatusByZone(0, 0, 5, $id_order);
                $zonelv = Order::getOrderIdsByStatusByZone(0, 0, -2, $id_order);
                $zoner = Order::getOrderIdsByStatusByZone(0, 0, -4, $id_order);
                $zonem = Order::getOrderIdsByStatusByZone(0, 0, -1, $id_order);

                if (in_array($id_order, $zone1))
                {
                    return 'Graines';
                }
                if (in_array($id_order, $zone2))
                {
                    return 'Bulbes potagers';
                }
                if (in_array($id_order, $zone3))
                {
                    return 'Chambre 2';
                }
                if (in_array($id_order, $zone4))
                {
                    return 'Extérieur';
                }
                if (in_array($id_order, $zone5))
                {
                    return 'Accessoires';
                }
                if (in_array($id_order, $zonelv))
                {
                    return 'Lettres vertes';
                }
                if (in_array($id_order, $zoner))
                {
                    return 'Rosiers';
                }
                if (in_array($id_order, $zonem))
                {
                    return 'Mixtes';
                }
                return 'Inconnu';
            }
            return false;
        }
    }
?>