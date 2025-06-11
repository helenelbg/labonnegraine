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
            /*echo '>> '.$id_order;
            echo '<pre>';
            print_r($id_order);
            echo '</pre>';*/
            if ( !is_array($id_order) && $id_order > 0 )
            {
                $req = new DbQuery();
                $req->select('od.product_id, od.product_attribute_id, od.product_name, od.product_quantity, od.product_quantity_refunded, product_ean13, product_reference, pa.default_on, pl.name');
                $req->from('order_detail', 'od');
                $req->leftJoin('product_attribute', 'pa', 'od.product_attribute_id = pa.id_product_attribute');
                $req->leftJoin('product_lang', 'pl', 'od.product_id = pl.id_product');
                $req->where('od.id_order = "'.$id_order.'"');
                $req->where('pl.id_lang = "1"');
                $req->where('od.product_id <> "3063" AND od.product_id <> "3128" AND od.product_id <> "1850" AND od.product_id <> "1851" AND od.product_id <> "2638" AND od.product_id <> "1849"');//Carte cadeau et box
                $req->orderBy('od.`product_reference` ASC');
                
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                $resultat = array();
                foreach($resu as $rangee)
                {
                    $rangee['quantity_final'] = $rangee['product_quantity'] - $rangee['product_quantity_refunded'];
                    $tmp = explode('(', $rangee['product_name']);
                    /*$rangee['product_name_1'] = $tmp[0];
                    $rangee['product_name_2'] = str_replace(')', '',$tmp[1]);*/
                    $rangee['product_name_1'] = $rangee['name'];
                    $rangee['product_name_2'] = str_replace($rangee['name'], '',$rangee['product_name']);
                    $rangee['product_name_2'] = str_replace('(', '',$rangee['product_name_2']);
                    $rangee['product_name_2'] = str_replace(')', '',$rangee['product_name_2']);
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
                if ( $zone == -4 )
                {
                    $cmds1 = Order::getOrderIdsByStatusByZone(2, 0, $zone); // Paiement accepté
                    $cmds2 = Order::getOrderIdsByStatusByZone(3, 0, $zone); // Préparation en cours
                    $cmds3 = Order::getOrderIdsByStatusByZone(18, 0, $zone); // Nous partons arracher votre rosier !
                    //$cmds3 = Order::getOrderIdsByStatusByZone(43, 0, $zone); // Préparation en cours - Picking OK
                    //$cmds4 = Order::getOrderIdsByStatusByZone(20, 0, $zone); // Préparation en cours - Etiquette OK
                    //$cmds5 = Order::getOrderIdsByStatusByZone(4, 0, $zone); // En cours de livraison
                    //$cmds6 = Order::getOrderIdsByStatusByZone(42, 0, $zone); // Colis disponible au point de retrait Click and Collect
                    //$cmds_final = array_merge($cmds1, $cmds2, $cmds3);
                    $cmds_final = array_merge($cmds1, $cmds2, $cmds3);
                }
                else 
                {
                    $cmds1 = Order::getOrderIdsByStatusByZone(2, 0, $zone); // Paiement accepté
                    $cmds2 = Order::getOrderIdsByStatusByZone(3, 0, $zone); // Préparation en cours
                    //$cmds3 = Order::getOrderIdsByStatusByZone(43, 0, $zone); // Préparation en cours - Picking OK
                    //$cmds4 = Order::getOrderIdsByStatusByZone(20, 0, $zone); // Préparation en cours - Etiquette OK
                    //$cmds5 = Order::getOrderIdsByStatusByZone(4, 0, $zone); // En cours de livraison
                    //$cmds6 = Order::getOrderIdsByStatusByZone(42, 0, $zone); // Colis disponible au point de retrait Click and Collect
                    //$cmds_final = array_merge($cmds1, $cmds2, $cmds3);
                    $cmds_final = array_merge($cmds1, $cmds2);
                }
                
                return $cmds_final;
            }
            return false;
        }

        public static function getOrdersByZoneDEV($zone = 0)
        {
            if ( $zone <> 0 )
            {
                if ( $zone == -4 )
                {
                    $cmds1 = Order::getOrderIdsByStatusByZone(2, 0, $zone); // Paiement accepté
                    $cmds2 = Order::getOrderIdsByStatusByZone(3, 0, $zone); // Préparation en cours
                    $cmds3 = Order::getOrderIdsByStatusByZone(18, 0, $zone); // Nous partons arracher votre rosier !
                    //$cmds3 = Order::getOrderIdsByStatusByZone(43, 0, $zone); // Préparation en cours - Picking OK
                    //$cmds4 = Order::getOrderIdsByStatusByZone(20, 0, $zone); // Préparation en cours - Etiquette OK
                    //$cmds5 = Order::getOrderIdsByStatusByZone(4, 0, $zone); // En cours de livraison
                    //$cmds6 = Order::getOrderIdsByStatusByZone(42, 0, $zone); // Colis disponible au point de retrait Click and Collect
                    //$cmds_final = array_merge($cmds1, $cmds2, $cmds3);
                    $cmds_final = array_merge($cmds1, $cmds2, $cmds3);
                }
                else 
                {
                    $cmds1 = Order::getOrderIdsByStatusByZone(2, 0, $zone); // Paiement accepté
                    $cmds2 = Order::getOrderIdsByStatusByZone(3, 0, $zone); // Préparation en cours
                    $cmds3 = Order::getOrderIdsByStatusByZone(43, 0, $zone); // Préparation en cours - Picking OK
                    $cmds4 = Order::getOrderIdsByStatusByZone(20, 0, $zone); // Préparation en cours - Etiquette OK
                    //$cmds5 = Order::getOrderIdsByStatusByZone(4, 0, $zone); // En cours de livraison
                    //$cmds6 = Order::getOrderIdsByStatusByZone(42, 0, $zone); // Colis disponible au point de retrait Click and Collect
                    //$cmds_final = array_merge($cmds1, $cmds2, $cmds3);
                    $cmds_final = array_merge($cmds1, $cmds2, $cmds3, $cmds4);
                }
                
                return $cmds_final;
            }
            return false;
        }

        public static function getOrdersByDeuxZones($zone1 = 0, $zone2 = 0)
        {
            if ( $zone1 <> 0 && $zone2 <> 0 )
            {
                $cmds1 = Order::getOrderIdsByStatusByDeuxZones(2, 0, $zone1, $zone2); // Paiement accepté
                $cmds2 = Order::getOrderIdsByStatusByDeuxZones(3, 0, $zone1, $zone2); // Préparation en cours
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
            $req->select('lgp.id_pda, lgp.nom_pda, lgo.prenom_operateur, lgo.nom_operateur');
            $req->from('LogiGraine_pda', 'lgp');
            $req->leftJoin('LogiGraine_pda_operateur', 'lgpo', 'lgp.id_pda = lgpo.id_pda');
            $req->leftJoin('LogiGraine_operateur', 'lgo', 'lgpo.id_operateur = lgo.id_operateur');
            if ( $id_pda <> 0 )
            {
                $req->where('lgp.id_pda = "'.$id_pda.'"');
            } 
            //$req->orderBy('lgp.`nom_pda` ASC');
            $req->orderBy('lgo.`prenom_operateur` ASC');
            
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

        public static function getGroups($orders, $return_single = false)
        {
            /*echo '<pre>';
            print_r($orders);
            echo '</pre>';*/
            foreach($orders as $cmdEC)
            {
                $listeArt = Commande::getProductsByOrder($cmdEC);
                $articles = array();
                foreach($listeArt as $artEC)
                {
                    $req_emp = 'SELECT etagere_plan FROM ps_LogiGraine_plan WHERE (REPLACE(debut_plan,"-","") * 1) <= "'.str_replace('-', '', $artEC['product_reference']).'" AND (REPLACE(fin_plan,"-","") * 1) >= "'.str_replace('-', '', $artEC['product_reference']).'" LIMIT 0,1;';
                    //echo $req_emp.'<br />';
                    $resu_emp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_emp);
                    if ( !isset($resu_emp[0]['etagere_plan']) )
                    {
                        $resu_emp[0]['etagere_plan'] = '-';
                    }
                    $articles[] = array($artEC['product_ean13'], $artEC['quantity_final'], $resu_emp[0]['etagere_plan']);
                }
                $commandes[$cmdEC] = $articles;
            }

            if ( isset($commandes) && is_array($commandes) )
            {
                $optimizer = new WarehouseTopologyOptimizer();
                $retour = $optimizer->optimizeOrders($commandes);

                if ( $return_single == true )
                {
                    return $retour;
                }

                // Créer une table temporaire
                $req_ttmp = 'CREATE TEMPORARY TABLE temp_groupes_commandes LIKE ps_LogiGraine_groupes_commandes;';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_ttmp);

                $rowsins = '';

                foreach($retour as $r)
                {
                    $groupeEC = '';
                    foreach($r['orders'] as $idEC => $o)
                    {
                        if ( !empty($groupeEC) )
                        {
                            $groupeEC .= '_';
                        }
                        $groupeEC .= $idEC;
                    }
                    //echo '<pre style="background:pink">';
                    //print_r($r['orders']);
                    //echo '</pre>';
                    /*echo '<pre style="background:yellow">';
                    //print_r($r['trolley_layout']);
                    print_r($r);
                    echo '</pre><hr />';*/     
                    if ( !empty($rowsins) )
                    {
                        $rowsins .= ',';
                    }
                    $rowsins .= '(NOW(), "disponible", "0000-00-00 00:00:00", "'.$groupeEC.'")';                                 
                }

                if ( !empty($rowsins) )
                {
                    // Insérer les nouveaux groupes dans la table temporaire
                    $req_ins = 'INSERT INTO temp_groupes_commandes 
                    (date_creation, statut, date_prise, id_order)
                    VALUES '.$rowsins.';';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_ins);  

                    // Transaction avec verrou minimal
                    $req_start1 = 'START TRANSACTION;';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_start1);
                    $req_start2 = 'LOCK TABLES ps_LogiGraine_groupes_commandes WRITE;';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_start2);

                    // Supprimer les anciens groupes disponibles
                    $req_del = 'DELETE FROM ps_LogiGraine_groupes_commandes WHERE statut = "disponible";';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_del);

                    // Copier les données de la table temporaire
                    $req_comp = 'INSERT INTO ps_LogiGraine_groupes_commandes 
                    SELECT * FROM temp_groupes_commandes;';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_comp);

                    $req_commit1 = 'UNLOCK TABLES;';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_commit1);
                    $req_commit2 = 'COMMIT;';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_commit2);
                }
                
                // Supprimer la table temporaire
                $req_drop = 'DROP TEMPORARY TABLE temp_groupes_commandes;';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_drop);

                $configurations = $optimizer->analyzeTrolleyConfigurations($commandes);
                /*echo '<pre>';
                print_r($configurations);
                echo '</pre>';*/
                return $retour;
            }
            else 
            {
                return array();
            }
        }
    }

    class WarehouseTopologyOptimizer {
        private $shelves = [
            'A' => ['A01', 'A02', 'A03', 'A04', 'A05', 'A06', 'A07', 'A08', 'A09', 'A10', 'A11'],
            'B' => ['B01', 'B02', 'B03', 'B04', 'B05', 'B06', 'B07', 'B08', 'B09'],
            'C' => ['C01', 'C02', 'C03', 'C04', 'C05', 'C06', 'C07', 'C08', 'C09'],
            'D' => ['D01', 'D02', 'D03', 'D04', 'D05', 'D06', 'D07', 'D08'],
            'E' => ['E01', 'E02', 'E03', 'E04', 'E05', 'E06', 'E07', 'E08'],
            'F' => ['F01', 'F02', 'F03', 'F04', 'F05', 'F06', 'F07', 'F08'],            
            'G' => ['G08', 'G07', 'G06', 'G05', 'G04', 'G03', 'G02', 'G01'],
            'H' => ['H01'],
            'I' => ['I01', 'I02', 'I03', 'I04', 'I05'],
            'J' => ['J01', 'J02', 'J03', 'J04', 'J05'],
            'K' => ['K01', 'K02', 'K03', 'K04', 'K05'],
            'L' => ['L01', 'L02', 'L03', 'L04', 'L05'],
            'O' => ['O01'],
            'P' => ['P01'],
            'Q' => ['Q01'],
            'R' => ['R01'],
            'S' => ['S01'],
            'T' => ['T01'],
        ];
    
        private $facingRacks = [
            'A' => 'B',
            'B' => 'A',
            'C' => 'D',
            'D' => 'C',
            'E' => 'F',
            'F' => 'E',
            'J' => 'K'
        ];
    
        private $backToBackRacks = [
            'B' => 'C',
            'C' => 'B',
            'D' => 'E',
            'E' => 'D',
            'F' => 'G',
            'G' => 'F',
            'I' => 'J',
            'J' => 'I',
            'K' => 'L',
            'L' => 'K'
        ];
    
        private $pickingAisles = [
            ['A', 'B'],    // Allée 1
            ['C', 'D'],    // Allée 2
            ['E', 'F'],    // Allée 3
            ['G'],         // Allée 4
            ['H'],         // Allée 5
            ['I'],         // Allée 6
            ['J', 'K'],    // Allée 7
            ['L'],         // Allée 8
            ['O'],         // Allée 9
            ['P'],         // Allée 10
            ['Q'],         // Allée 11
            ['R'],         // Allée 12
            ['S'],         // Allée 13
            ['T']          // Allée 14
        ];
    
        private $shelfPairs = [
            // Allée 1 : A et B face à face
            'A01' => 'B01', 'B01' => 'A01',
            'A02' => 'B02', 'B02' => 'A02',
            'A03' => 'B03', 'B03' => 'A03',
            'A04' => 'B04', 'B04' => 'A04',
            'A05' => 'B05', 'B05' => 'A05',
            'A06' => 'B06', 'B06' => 'A06',
            'A07' => 'B07', 'B07' => 'A07',
            'A08' => 'B08', 'B08' => 'A08',
            'A09' => 'B09', 'B09' => 'A09',
            'A10' => null,  
            'A11' => null,  
    
            // Allée 2 : C et D face à face
            'C01' => 'D01', 'D01' => 'C01',
            'C02' => 'D02', 'D02' => 'C02',
            'C03' => 'D03', 'D03' => 'C03',
            'C04' => 'D04', 'D04' => 'C04',
            'C05' => 'D05', 'D05' => 'C05',
            'C06' => 'D06', 'D06' => 'C06',
            'C07' => 'D07', 'D07' => 'C07',
            'C08' => 'D08', 'D08' => 'C08',
            'C09' => null,
    
            // Allée 3 : E et F face à face
            'E01' => 'F01', 'F01' => 'E01',
            'E02' => 'F02', 'F02' => 'E02',
            'E03' => 'F03', 'F03' => 'E03',
            'E04' => 'F04', 'F04' => 'E04',
            'E05' => 'F05', 'F05' => 'E05',
            'E06' => 'F06', 'F06' => 'E06',
            'E07' => 'F07', 'F07' => 'E07',
            'E08' => 'F08', 'F08' => 'E08',
    
            // Allée 4 : Rayon G (pas de vis-à-vis)
            'G08' => null,
            'G07' => null,
            'G06' => null,
            'G05' => null,
            'G04' => null,
            'G03' => null,
            'G02' => null,
            'G01' => null,
    
            // Allée 5 : Rayon H (pas de vis-à-vis)
            'H01' => null,
    
            // Allée 6 : Rayon I (pas de vis-à-vis)
            'I01' => null,
            'I02' => null,
            'I03' => null,
            'I04' => null,
            'I05' => null,
    
            // Allée 7 : J et K face à face
            'J01' => 'K01', 'K01' => 'J01',
            'J02' => 'K02', 'K02' => 'J02',
            'J03' => 'K03', 'K03' => 'J03',
            'J04' => 'K04', 'K04' => 'J04',
            'J05' => 'K05', 'K05' => 'J05',

            // Allée 8 : Rayon L (pas de vis-à-vis)
            'L01' => null,
            'L02' => null,
            'L03' => null,
            'L04' => null,
            'L05' => null,

            // Allée 9 : Rayon H (pas de vis-à-vis)
            'O01' => null,

            // Allée 10 : Rayon H (pas de vis-à-vis)
            'P01' => null,

            // Allée 11 : Rayon H (pas de vis-à-vis)
            'Q01' => null,

            // Allée 12 : Rayon H (pas de vis-à-vis)
            'R01' => null,

            // Allée 13 : Rayon H (pas de vis-à-vis)
            'S01' => null,

            // Allée 14 : Rayon H (pas de vis-à-vis)
            'T01' => null,
        ];

        private const TROLLEY_ETAGES = 3;
        private const CAISSE_ROUGE = 'rouge';
        private const CAISSE_NOIRE = 'noire';
        private const CAPACITE_ROUGE = 3;
        private const CAPACITE_NOIRE = 1;
    
        /**
         * Point d'entrée principal pour optimiser les commandes
         */
        /*public function optimizeOrders(array $orders): array {
            try {
                // Analyser les commandes
                $orderVectors = $this->createOrderVectors($orders);
                
                // Regrouper les commandes
                $groups = $this->groupOrders($orderVectors);
                
                // Optimiser chaque groupe
                return $this->optimizeGroups($groups, $orders);
            } catch (Exception $e) {
                error_log("Erreur lors de l'optimisation : " . $e->getMessage());
                throw $e;
            }
        }*/
        public function optimizeOrders(array $orders): array {
            try {
                // Créer des groupes optimisés pour les chariots
                $groups = $this->optimizeGroupsForTrolleys($orders);
                
                // Optimiser chaque groupe
                $optimizedGroups = [];
                foreach ($groups as $group) {
                    $groupOrders = array_intersect_key($orders, array_flip($group));
                    $pickingPath = $this->calculateOptimalPath($groupOrders);
                    $sequence = $this->generatePickingSequence($groupOrders, $pickingPath);
                    
                    // Ajouter la configuration du chariot
                    $trolleyLayout = $this->calculateTrolleyLayout($group);
                    
                    $optimizedGroups[] = [
                        'orders' => $groupOrders,
                        'picking_path' => $pickingPath,
                        'sequence' => $this->formatPickingInstructions($sequence),
                        'trolley_layout' => $trolleyLayout
                    ];
                }

                // Avant le return, ajoutez ce bloc de tri
                usort($optimizedGroups, function($a, $b) {
                    $minIdA = min(array_keys($a['orders']));
                    $minIdB = min(array_keys($b['orders']));
                    return $minIdA - $minIdB;
                });
                
                return $optimizedGroups;
            } catch (Exception $e) {
                error_log("Erreur lors de l'optimisation : " . $e->getMessage());
                throw $e;
            }
        }
    
        /**
         * Crée des vecteurs de caractéristiques pour chaque commande
         */
        private function createOrderVectors(array $orders): array {
            $vectors = [];
            foreach ($orders as $orderId => $items) {
                $vector = [];
                foreach ($this->pickingAisles as $aisleIndex => $aisleRacks) {
                    $vector["aisle_$aisleIndex"] = 0;
                    foreach ($items as $item) {
                        $rack = substr($item[2], 0, 1);
                        if (in_array($rack, $aisleRacks)) {
                            $vector["aisle_$aisleIndex"]++;
                        }
                    }
                }
                $vectors[$orderId] = $vector;
            }
            return $vectors;
        }
    
        /**
         * Regroupe les commandes en utilisant K-means
         */
        private function groupOrders(array $vectors): array {
            if (empty($vectors)) {
                return [];
            }
    
            // Récupérer les types de caisses
            $orderTypes = [];
            foreach (array_keys($vectors) as $orderId) {
                $caisseN = Caisse::getTailleCaisseByCommande($orderId);
                if ( $caisseN == 100 )
                {
                    $orderTypes[$orderId] = self::CAISSE_ROUGE;
                }
                else 
                {
                    $orderTypes[$orderId] = self::CAISSE_NOIRE;
                }
                //$orderTypes[$orderId] = Caisse::getTailleCaisseByCommande($orderId);
            }
        
            // Premier tri par proximité
            $sortedOrders = $this->sortByProximity($vectors);
            
            // Créer des groupes en respectant les limites
            $groups = [];
            $currentGroup = [];
            $currentBlackCount = 0;
            $currentRedCount = 0;
        
            foreach ($sortedOrders as $orderId => $vector) {
                $type = $orderTypes[$orderId];
                $canAdd = false;
        
                if ($type === 'noire') {
                    // Une caisse noire nécessite un étage complet
                    $canAdd = $currentBlackCount < 3 && ($currentBlackCount + $currentRedCount/3) < 3;
                } else {
                    // Une caisse rouge peut être ajoutée si on ne dépasse pas 9 moins le nombre d'étages pris par les noires
                    $availableRedSlots = 9 - ($currentBlackCount * 3);
                    $canAdd = $currentRedCount < $availableRedSlots;
                }
        
                if (!$canAdd) {
                    if (!empty($currentGroup)) {
                        $groups[] = $currentGroup;
                    }
                    $currentGroup = [];
                    $currentBlackCount = 0;
                    $currentRedCount = 0;
                }
        
                $currentGroup[] = $orderId;
                if ($type === 'noire') {
                    $currentBlackCount++;
                } else {
                    $currentRedCount++;
                }
            }
        
            if (!empty($currentGroup)) {
                $groups[] = $currentGroup;
            }
        
            return $groups;
        }

        /**
     * Crée des groupes basés sur la proximité géographique
     */
    private function createProximityGroups(array $vectors): array {
        $maxGroupSize = 12; // Taille maximum avant optimisation chariot
        $groups = [];
        $currentGroup = [];
        
        // Trier les commandes par similarité de zones
        $sortedVectors = $this->sortByProximity($vectors);
        
        foreach ($sortedVectors as $orderId => $vector) {
            if (count($currentGroup) >= $maxGroupSize) {
                $groups[] = $currentGroup;
                $currentGroup = [];
            }
            $currentGroup[] = $orderId;
        }
        
        if (!empty($currentGroup)) {
            $groups[] = $currentGroup;
        }
        
        return $groups;
    }

    /**
     * Trie les commandes par proximité géographique
     */
    private function sortByProximity(array $vectors): array {
        $sorted = [];
        $remaining = $vectors;
        
        // Commencer par une commande au hasard
        $currentOrderId = array_key_first($remaining);
        $currentVector = $remaining[$currentOrderId];
        $sorted[$currentOrderId] = $currentVector;
        unset($remaining[$currentOrderId]);
        
        // Ajouter les commandes les plus proches une par une
        while (!empty($remaining)) {
            $minDistance = PHP_FLOAT_MAX;
            $closestId = null;
            
            foreach ($remaining as $orderId => $vector) {
                $distance = $this->calculateDistance($currentVector, $vector);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $closestId = $orderId;
                }
            }
            
            $currentOrderId = $closestId;
            $currentVector = $remaining[$closestId];
            $sorted[$closestId] = $currentVector;
            unset($remaining[$closestId]);
        }
        
        return $sorted;
    }

      /**
     * Optimise un groupe pour la répartition sur les chariots
     */
    private function optimizeForTrolley(array $orderIds, array $orderTypes): array {
        $trolleyGroups = [];
        $currentGroup = [
            'red' => [],    // caisses rouges
            'black' => []   // caisses noires
        ];
        
        foreach ($orderIds as $orderId) {
            $type = $orderTypes[$orderId];
            
            if ($type === 'noire') {
                if (count($currentGroup['black']) >= 3) {
                    // Chariot plein pour les noires, créer nouveau groupe
                    $trolleyGroups[] = $this->finalizeTrolleyGroup($currentGroup);
                    $currentGroup = ['red' => [], 'black' => []];
                }
                $currentGroup['black'][] = $orderId;
            } else {
                $redCapacityLeft = 9 - (count($currentGroup['black']) * 3);
                if (count($currentGroup['red']) >= $redCapacityLeft) {
                    // Plus de place pour les rouges, créer nouveau groupe
                    $trolleyGroups[] = $this->finalizeTrolleyGroup($currentGroup);
                    $currentGroup = ['red' => [], 'black' => []];
                }
                $currentGroup['red'][] = $orderId;
            }
                }
                
        // Ajouter le dernier groupe s'il n'est pas vide
        if (!empty($currentGroup['red']) || !empty($currentGroup['black'])) {
            $trolleyGroups[] = $this->finalizeTrolleyGroup($currentGroup);
        }
        
        return $trolleyGroups;
    }

    /**
     * Finalise un groupe pour le chariot en respectant les contraintes
     */
    private function finalizeTrolleyGroup(array $group): array {
        $finalGroup = [];
        
        // D'abord les caisses noires (en bas)
        foreach ($group['black'] as $orderId) {
            $finalGroup[] = $orderId;
        }
        
        // Puis les caisses rouges (en haut)
        foreach ($group['red'] as $orderId) {
            $finalGroup[] = $orderId;
        }
        
        return $finalGroup;
    }

         /**
     * Divise un groupe en sous-groupes respectant la capacité du chariot
     */
    private function splitGroupByCapacity(array $group, array $orderTypes): array {
        $subGroups = [];
        $currentGroup = [];
        $currentEtages = 0;
        $currentRouges = 0;

        foreach ($group as $orderId) {
            $type = $orderTypes[$orderId];
            $newEtages = $currentEtages;
            $newRouges = $currentRouges;

            if ($type === self::CAISSE_NOIRE) {
                $newEtages++;
            } else {
                $newRouges++;
                $newEtages = ceil($newRouges / self::CAPACITE_ROUGE) + 
                            ($currentEtages - ceil($currentRouges / self::CAPACITE_ROUGE));
            }

            // Si on dépasse 3 étages, créer un nouveau groupe
            if ($newEtages > self::TROLLEY_ETAGES) {
                if (!empty($currentGroup)) {
                    $subGroups[] = $currentGroup;
                }
                $currentGroup = [$orderId];
                $currentEtages = ($type === self::CAISSE_NOIRE) ? 1 : 1;
                $currentRouges = ($type === self::CAISSE_ROUGE) ? 1 : 0;
            } else {
                $currentGroup[] = $orderId;
                $currentEtages = $newEtages;
                $currentRouges = $newRouges;
            }
        }

        if (!empty($currentGroup)) {
            $subGroups[] = $currentGroup;
        }

        return $subGroups;
    }

    /**
     * Met à jour la capacité du groupe
     */
    private function updateGroupCapacity(array &$capacity, string $type): void {
        if ($type === self::CAISSE_NOIRE) {
            $capacity['etages']--;
            $capacity['noires']++;
        } else {
            $capacity['rouges']++;
            $capacity['etages'] = self::TROLLEY_ETAGES - 
                ceil($capacity['rouges'] / self::CAPACITE_ROUGE) - 
                $capacity['noires'];
        }
    }

     /**
     * Vérifie si une commande peut être ajoutée au groupe actuel
     */
    private function canAddToCurrentGroup(array $capacity, string $type): bool {
        if ($type === self::CAISSE_NOIRE) {
            return $capacity['etages'] > 0 && $capacity['noires'] < self::TROLLEY_ETAGES;
        } else {
            $etagesNecessaires = ceil(($capacity['rouges'] + 1) / self::CAPACITE_ROUGE);
            return $etagesNecessaires + $capacity['noires'] <= self::TROLLEY_ETAGES;
        }
    }

        /**
     * Calcule le nombre maximum de commandes par chariot
     */
    private function calculateMaxOrdersPerTrolley(array $orderTypes): int {
        $maxNoires = self::TROLLEY_ETAGES;
        $maxRouges = self::TROLLEY_ETAGES * self::CAPACITE_ROUGE;
        return max($maxNoires, $maxRouges);
    }

    /**
     * Trouve le meilleur groupe en respectant la capacité
     */
    private function findBestGroupWithCapacity(array $vector, array $centroids, array $groups, string $type): ?int {
        $minDistance = PHP_FLOAT_MAX;
        $bestGroup = null;

        foreach ($centroids as $groupId => $centroid) {
            if (!$this->hasCapacityForOrder($groups[$groupId], $type)) {
                continue;
            }

            $distance = $this->calculateDistance($vector, $centroid);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $bestGroup = $groupId;
            }
        }

        return $bestGroup;
    }

 /**
     * Vérifie si un groupe a la capacité pour une nouvelle commande
     */
    private function hasCapacityForOrder(array $group, string $type): bool {
        if (empty($group)) return true;

        $etagesUtilises = 0;
        $rougesCount = 0;

        foreach ($group as $orderId) {
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            if ( $caisseN == 100 )
            {
                $orderTypes = self::CAISSE_ROUGE;
            }
            else 
            {
                $orderTypes = self::CAISSE_NOIRE;
            }
            //$orderType = Caisse::getTailleCaisseByCommande($orderId);
            if ($orderType === self::CAISSE_NOIRE) {
                $etagesUtilises++;
            } else {
                $rougesCount++;
            }
        }

        if ($type === self::CAISSE_NOIRE) {
            return $etagesUtilises < self::TROLLEY_ETAGES;
        } else {
            $etagesRougesNecessaires = ceil(($rougesCount + 1) / self::CAPACITE_ROUGE);
            return $etagesRougesNecessaires + $etagesUtilises <= self::TROLLEY_ETAGES;
        }
    }

        /**
         * Calcule la taille maximale d'un groupe basé sur la capacité du chariot
         */
        private function calculateMaxGroupSize(array $orderTypes): int {
            $maxCapacity = self::TROLLEY_ETAGES * self::CAPACITE_ROUGE;
            $noires = $rouges = 0;
            foreach ($orderTypes as $type) {
                if ($type === self::CAISSE_NOIRE) {
                    $noires++;
                } else {
                    $rouges++;
                }
            }
            
            return min(
                self::TROLLEY_ETAGES, // Max caisses noires
                self::TROLLEY_ETAGES * self::CAPACITE_ROUGE // Max caisses rouges
            );
        }

         /**
         * Divise un groupe selon les contraintes de chariot
         */
        private function splitGroupByTrolleyConstraints(array $group, array $orderTypes): array {
            $subgroups = [];
            $currentSubgroup = [];
            $currentCapacity = [
                'etages' => self::TROLLEY_ETAGES,
                'rouges' => 0,
                'noires' => 0
            ];

            // Trier le groupe pour avoir les commandes similaires ensemble
            $sortedGroup = $this->sortGroupByProximity($group);

            foreach ($sortedGroup as $orderId) {
                $type = $orderTypes[$orderId];
                $canAdd = $this->canAddToCapacity($currentCapacity, $type);

                if (!$canAdd) {
                    if (!empty($currentSubgroup)) {
                        $subgroups[] = $currentSubgroup;
                    }
                    $currentSubgroup = [];
                    $currentCapacity = [
                        'etages' => self::TROLLEY_ETAGES,
                        'rouges' => 0,
                        'noires' => 0
                    ];
                    }

                $currentSubgroup[] = $orderId;
                $this->updateCapacity($currentCapacity, $type);
            }

            if (!empty($currentSubgroup)) {
                $subgroups[] = $currentSubgroup;
            }

            return $subgroups;
        }

        /**
         * Vérifie si une commande peut être ajoutée à la capacité actuelle
         */
        private function canAddToCapacity(array $capacity, string $type): bool {
            if ($type === self::CAISSE_NOIRE) {
                return $capacity['etages'] >= 1;
            }
            
            $etagesNecessaires = ceil(($capacity['rouges'] + 1) / self::CAPACITE_ROUGE);
            return $etagesNecessaires <= $capacity['etages'];
        }

        /**
         * Met à jour la capacité après l'ajout d'une commande
         */
        private function updateCapacity(array &$capacity, string $type): void {
            if ($type === self::CAISSE_NOIRE) {
                $capacity['etages']--;
                $capacity['noires']++;
            } else {
                $capacity['rouges']++;
                $capacity['etages'] = self::TROLLEY_ETAGES - 
                    ceil($capacity['rouges'] / self::CAPACITE_ROUGE) - 
                    $capacity['noires'];
            }
        }

        /**
         * Trie les commandes d'un groupe par proximité
         */
        private function sortGroupByProximity(array $group): array {
            // Créer une matrice de distances entre les commandes
            $distances = [];
            foreach ($group as $i => $order1) {
                foreach ($group as $j => $order2) {
                    if ($i !== $j) {
                        $distances[$order1][$order2] = $this->calculateOrderDistance($order1, $order2);
                    }
                }
            }

            // Algorithme glouton pour ordonner les commandes
            $sorted = [];
            $current = array_shift($group);
            $sorted[] = $current;
            $remaining = $group;

            while (!empty($remaining)) {
                $closest = null;
                $minDistance = PHP_FLOAT_MAX;

                foreach ($remaining as $index => $order) {
                    $distance = $distances[$current][$order] ?? PHP_FLOAT_MAX;
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $closest = $index;
        }
                }

                $current = $remaining[$closest];
                $sorted[] = $current;
                unset($remaining[$closest]);
            }

            return $sorted;
        }

    
        /**
         * Initialise les centroids pour le clustering
         */
        private function initializeCentroids(array $vectors, int $k): array {
            if (empty($vectors)) {
                throw new RuntimeException("Aucun vecteur fourni pour l'initialisation des centroids");
            }
    
            $centroids = [];
            $vectorKeys = array_keys($vectors);
            $firstVector = reset($vectors);
            
            $k = min($k, count($vectors));
            $k = max(1, $k);
            
            for ($i = 0; $i < $k; $i++) {
                if (isset($vectorKeys[$i])) {
                    $centroids[] = $vectors[$vectorKeys[$i]];
                } else {
                    $newCentroid = $firstVector;
                    array_walk($newCentroid, function(&$value) {
                        $value += rand(-1, 1);
                    });
                    $centroids[] = $newCentroid;
                }
            }
    
            return $centroids;
        }
    
        /**
         * Trouve le meilleur groupe pour un vecteur
         */
        private function findBestGroup(array $vector, array $centroids): int {
            if (empty($centroids)) {
                return 0;
            }
    
            $minDistance = PHP_FLOAT_MAX;
            $bestGroup = 0;
            
            foreach ($centroids as $groupId => $centroid) {
                if (!is_array($centroid) || empty($centroid)) {
                    continue;
                }
                
                try {
                    $distance = $this->calculateDistance($vector, $centroid);
                    if ($distance < $minDistance) {
                        $minDistance = $distance;
                        $bestGroup = $groupId;
                    }
                } catch (Exception $e) {
                    continue;
                }
            }
            
            return $bestGroup;
        }
    
        /**
         * Calcule la distance entre deux vecteurs
         */
        private function calculateDistance(array $vector1, array $vector2): float {
            if (empty($vector2)) {
                throw new InvalidArgumentException("Le second vecteur ne peut pas être vide");
            }
    
            $allKeys = array_unique(array_merge(array_keys($vector1), array_keys($vector2)));
            $sum = 0;
    
            foreach ($allKeys as $key) {
                $v1 = $vector1[$key] ?? 0;
                $v2 = $vector2[$key] ?? 0;
                $sum += pow($v1 - $v2, 2);
            }
    
            return sqrt($sum);
        }
    
        /**
         * Calcule le centroid d'un groupe de vecteurs
         */
        private function calculateCentroid(array $vectors): array {
            if (empty($vectors)) {
                return [];
            }
    
            $centroid = [];
            $numVectors = count($vectors);
            
            $keys = [];
            foreach ($vectors as $vector) {
                $keys = array_unique(array_merge($keys, array_keys($vector)));
            }
            
            foreach ($keys as $key) {
                $sum = 0;
                foreach ($vectors as $vector) {
                    $sum += $vector[$key] ?? 0;
                }
                $centroid[$key] = $sum / $numVectors;
            }
            
            return $centroid;
        }
    
        /**
         * Équilibre les groupes pour avoir environ 3 commandes par groupe
         */
        private function balanceGroups(array $groups): array {
            $balanced = [];
            $currentGroup = [];
            $count = 0;
            
            foreach ($groups as $group) {
                foreach ($group as $orderId) {
                    $currentGroup[] = $orderId;
                    $count++;
                    
                    if ($count >= 3) {
                        $balanced[] = $currentGroup;
                        $currentGroup = [];
                        $count = 0;
                    }
                }
            }
            
            if (!empty($currentGroup)) {
                $balanced[] = $currentGroup;
            }
            
            return $balanced;
        }
    
        /**
         * Optimise chaque groupe de commandes
         */
        private function optimizeGroups(array $groups, array $orders): array {
            $optimizedGroups = [];
            
            foreach ($groups as $group) {
                if (empty($group)) continue;
                
                // Diviser le groupe si nécessaire pour respecter la limite de 3 étages
                $trolleyGroups = $this->splitGroupByTrolleyCapacity($group);
                
                foreach ($trolleyGroups as $trolleyGroup) {
                    $groupOrders = array_intersect_key($orders, array_flip($trolleyGroup));
                $pickingPath = $this->calculateOptimalPath($groupOrders);
                $sequence = $this->generatePickingSequence($groupOrders, $pickingPath);
                
                $optimizedGroups[] = [
                    'orders' => $groupOrders,
                    'picking_path' => $pickingPath,
                        'sequence' => $this->formatPickingInstructions($sequence),
                        'trolley_layout' => $this->createStrictTrolleyLayout($trolleyGroup)
                ];
                }
            }
            
            return $optimizedGroups;
        }

        /**
     * Crée un layout de chariot strictement limité à 3 étages
     */
    private function createStrictTrolleyLayout(array $orderIds): array {
        $blackOrders = [];
        $redOrders = [];
        
        // Séparer les commandes par type
        foreach ($orderIds as $orderId) {
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            if ( $caisseN == 100 )
            {
                $orderTypesTmp = self::CAISSE_ROUGE;
            }
            else 
            {
                $orderTypesTmp = self::CAISSE_NOIRE;
            }
            if ($orderTypesTmp === 'noire') {
                $blackOrders[] = $orderId;
            } else {
                $redOrders[] = $orderId;
            }
        }
    
        $layout = [];
        $etage = 1;
    
        // Placer d'abord les caisses rouges en haut (max 3 par étage)
        for ($i = 0; $i < count($redOrders); $i += 3) {
            $etageOrders = array_slice($redOrders, $i, 3);
            if (!empty($etageOrders)) {
                $layout[] = [
                    'etage' => $etage++,
                    'type' => 'rouge',
                    'commandes' => $etageOrders
                ];
            }
        }
    
        // Puis les caisses noires (1 par étage)
        foreach ($blackOrders as $orderId) {
            $layout[] = [
                'etage' => $etage++,
                'type' => 'noire',
                'commandes' => [$orderId]
            ];
        }
    
        // Vérifier qu'on ne dépasse pas 3 étages
        if (count($layout) > 3) {
            throw new RuntimeException("Le groupe dépasse la capacité d'un chariot");
        }
    
        return $layout;
    }

        /**
     * Divise un groupe en respectant la capacité maximale d'un chariot
     */
    private function splitGroupByTrolleyCapacity(array $orderIds): array {
        $groups = [];
        $currentGroup = [];
        $currentBlackCount = 0;
        $currentRedCount = 0;
        
        foreach ($orderIds as $orderId) {
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            if ( $caisseN == 100 )
            {
                $type = self::CAISSE_ROUGE;
            }
            else 
            {
                $type = self::CAISSE_NOIRE;
            }
            //$type = Caisse::getTailleCaisseByCommande($orderId);
            
            if ($type === 'noire') {
                if ($currentBlackCount >= 3) {
                    // Chariot plein, commencer un nouveau groupe
                    $groups[] = $currentGroup;
                    $currentGroup = [];
                    $currentBlackCount = 0;
                    $currentRedCount = 0;
                }
                $currentBlackCount++;
            } else {
                // Calculer combien d'étages sont disponibles pour les rouges
                $availableRedSlots = (3 - $currentBlackCount) * 3;
                if ($currentRedCount >= $availableRedSlots) {
                    // Plus de place pour les rouges, commencer un nouveau groupe
                    $groups[] = $currentGroup;
                    $currentGroup = [];
                    $currentBlackCount = 0;
                    $currentRedCount = 0;
                }
                $currentRedCount++;
            }
            
            $currentGroup[] = $orderId;
        }
        
        if (!empty($currentGroup)) {
            $groups[] = $currentGroup;
        }
        
        return $groups;
    }

        /**
     * Crée le layout d'un chariot en respectant la limite de 3 étages
     */
    private function createTrolleyLayout(array $orderIds): array {
        $layout = [];
        $orderTypes = [];
        $currentEtage = 1;
        $rougesOnEtage = 0;

        // Trier les commandes : noires d'abord
        $blackOrders = [];
        $redOrders = [];
        
        foreach ($orderIds as $orderId) {
            
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            if ( $caisseN == 100 )
            {
                $type = self::CAISSE_ROUGE;
            }
            else 
            {
                $type = self::CAISSE_NOIRE;
            }
            //$type = Caisse::getTailleCaisseByCommande($orderId);
            if ($type === 'noire') {
                $blackOrders[] = $orderId;
            } else {
                $redOrders[] = $orderId;
            }
        }

        // Placer les caisses noires en bas
        for ($etage = 3; $etage > 0 && !empty($blackOrders); $etage--) {
            $orderId = array_shift($blackOrders);
            $layout[] = [
                'etage' => $etage,
                'type' => 'noire',
                'commandes' => [$orderId]
            ];
        }

        // Si plus de caisses noires et encore des étages disponibles, placer les rouges
        $etagesToUse = min(3 - count($layout), ceil(count($redOrders) / 3));
        $currentRedIndex = 0;

        for ($i = 0; $i < $etagesToUse; $i++) {
            $rougesSurEtage = [];
            for ($j = 0; $j < 3 && $currentRedIndex < count($redOrders); $j++) {
                $rougesSurEtage[] = $redOrders[$currentRedIndex++];
            }
            
            if (!empty($rougesSurEtage)) {
                $layout[] = [
                    'etage' => count($layout) + 1,
                    'type' => 'rouge',
                    'commandes' => $rougesSurEtage
                ];
            }
        }

        // Trier le layout par étage
        usort($layout, function($a, $b) {
            return $a['etage'] - $b['etage'];
        });

        return $layout;
    }

    /**
     * Divise un groupe en plusieurs chariots si nécessaire
     */
    private function splitIntoTrolleys(array $orderIds): array {
        $trolleys = [];
        $currentTrolley = [];
        $blackCount = 0;
        $redCount = 0;

        foreach ($orderIds as $orderId) {
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            if ( $caisseN == 100 )
            {
                $type = self::CAISSE_ROUGE;
            }
            else 
            {
                $type = self::CAISSE_NOIRE;
            }
            //$type = Caisse::getTailleCaisseByCommande($orderId);
            
            if ($type === 'noire') {
                if ($blackCount >= 3) {
                    // Chariot plein, on en commence un nouveau
                    $trolleys[] = $currentTrolley;
                    $currentTrolley = [];
                    $blackCount = 0;
                    $redCount = 0;
                }
                $blackCount++;
            } else {
                if ($redCount >= (9 - ($blackCount * 3))) {
                    // Plus de place pour les rouges, nouveau chariot
                    $trolleys[] = $currentTrolley;
                    $currentTrolley = [];
                    $blackCount = 0;
                    $redCount = 0;
                }
                $redCount++;
            }
            
            $currentTrolley[] = $orderId;
        }

        if (!empty($currentTrolley)) {
            $trolleys[] = $currentTrolley;
        }

        return $trolleys;
        }
    
        /**
         * Calcule le chemin optimal pour un groupe de commandes
         */
        private function calculateOptimalPath(array $orders): array {
            $aisleVisits = [];
            
            foreach ($this->pickingAisles as $aisleIndex => $aisleRacks) {
                foreach ($orders as $items) {
                    foreach ($items as $item) {
                        $rack = substr($item[2], 0, 1);
                        if (in_array($rack, $aisleRacks)) {
                            $aisleVisits[$aisleIndex] = true;
                            break 2;
                        }
                    }
                }
            }
            
            return $this->optimizeAisleSequence(array_keys($aisleVisits));
        }
    
        /**
         * Optimise l'ordre des allées à visiter
         */
        private function optimizeAisleSequence(array $aisleIndices): array {
            if (count($aisleIndices) <= 2) {
                return $aisleIndices;
            }
    
            $sequence = [$aisleIndices[0]];
            $remaining = array_slice($aisleIndices, 1);
            
            while (!empty($remaining)) {
                $lastAisle = end($sequence);
                $bestNextAisle = null;
                $bestDistance = PHP_FLOAT_MAX;
                
                foreach ($remaining as $index => $aisle) {
                    $distance = abs($lastAisle - $aisle);
                    if ($distance < $bestDistance) {
                        $bestDistance = $distance;
                        $bestNextAisle = $index;
                    }
                }
                
                $sequence[] = $remaining[$bestNextAisle];
                unset($remaining[$bestNextAisle]);
                $remaining = array_values($remaining);
            }
            
            return $sequence;
        }

        /**
     * Génère la séquence de picking détaillée
     */
    private function generatePickingSequence(array $orders, array $path): array {
        $sequence = [];
        
        foreach ($path as $aisleIndex) {
            $aisleRacks = $this->pickingAisles[$aisleIndex];
            $aisleItems = [];
            
            foreach ($orders as $orderId => $items) {
                foreach ($items as $item) {
                    $rack = substr($item[2], 0, 1);
                    if (in_array($rack, $aisleRacks)) {
                        $aisleItems[] = [
                            'order_id' => $orderId,
                            'location' => $item[2],
                            'ean' => $item[0],
                            'quantity' => $item[1]
                        ];
                    }
                }
            }
            
            if (!empty($aisleItems)) {
                $sequence[] = [
                    'aisle' => $aisleIndex,
                    'items' => $this->optimizeAisleItemsByPairs($aisleItems, $aisleRacks, $aisleIndex)
                ];
            }
        }
        
        return $sequence;
    }
/**
     * Optimise l'ordre des items dans une allée en tenant compte du sens de déplacement
     */
    private function optimizeAisleItemsByPairs(array $items, array $racks, int $aisleIndex): array {
        // Définir le sens de parcours pour chaque allée
        $reverseAisle = false;
        
        // Allées C-D et E-F sont inversées (pair)
        if ($aisleIndex % 2 === 1 && $aisleIndex <= 2) {
            $reverseAisle = true;
        }
        // Allée J-K (index 6) est inversée
        elseif ($aisleIndex === 6) {
            $reverseAisle = true;
        }
        
        // Pour les allées à rayon unique (G, H, I, L)
        if (count($racks) === 1) {
            return $this->optimizeSingleRackAisle($items, $aisleIndex);
        }

        // Pour les allées à double rayons
        $rackGroups = [];
        foreach ($items as $item) {
            $location = $item['location'];
            $rackNumber = substr($location, -2); // Extraire le numéro de l'étagère (01, 02, etc.)
            $rackLetter = substr($location, 0, 1); // Extraire la lettre du rayon
            
            if (!isset($rackGroups[$rackNumber])) {
                $rackGroups[$rackNumber] = [
                    'first' => [],  // Premier rayon de l'allée
                    'second' => []  // Second rayon de l'allée
                ];
            }
            
            // Déterminer si c'est le premier ou le second rayon de l'allée
            $isFirstRack = array_search($rackLetter, $racks) === 0;
            if ($isFirstRack) {
                $rackGroups[$rackNumber]['first'][] = $item;
            } else {
                $rackGroups[$rackNumber]['second'][] = $item;
            }
        }

        // Trier les numéros d'étagères selon le sens de parcours
        if ($reverseAisle) {
            krsort($rackGroups);
        } else {
            ksort($rackGroups);
        }

        // Construire la séquence finale
        $optimizedSequence = [];
        foreach ($rackGroups as $rackNumber => $rackGroup) {
            if ($reverseAisle) {
                // Pour les allées inversées (C-D, E-F, J-K)
                foreach ($rackGroup['second'] as $item) {
                    $optimizedSequence[] = $item;
                }
                foreach ($rackGroup['first'] as $item) {
                    $optimizedSequence[] = $item;
                }
            } else {
                // Pour les allées normales (A-B)
                foreach ($rackGroup['first'] as $item) {
                    $optimizedSequence[] = $item;
                }
                foreach ($rackGroup['second'] as $item) {
                    $optimizedSequence[] = $item;
                }
            }
        }
        
        return $optimizedSequence;
    }

    /**
     * Optimise l'ordre des items pour une allée à rayon unique
     */
    /*private function optimizeSingleRackAisle(array $items, int $aisleIndex): array {
        // Trier les items par emplacement
        usort($items, function($a, $b) use ($aisleIndex) {
            $locA = substr($a['location'], -2);
            $locB = substr($b['location'], -2);
            
            // Allée I et L : ordre croissant (01 vers 05)
            if ($aisleIndex === 5 || $aisleIndex === 7) {
                return strcmp($locA, $locB);
            }
            // Autres allées uniques (G, H) : ordre standard
            else {
                return strcmp($locA, $locB);
            }
        });
        
        return $items;
    }*/
    private function optimizeSingleRackAisle(array $items, int $aisleIndex): array {
        // Trier les items par emplacement
        usort($items, function($a, $b) use ($aisleIndex) {
            $locA = substr($a['location'], -2);
            $locB = substr($b['location'], -2);
            
            // Allée G : ordre décroissant (G08 vers G01)
            if ($aisleIndex === 3) { // Supposant que G est l'index 3 dans pickingAisles
                return strcmp($locB, $locA);
            }
            
            // Allée I et L : ordre croissant (01 vers 05)
            if ($aisleIndex === 5 || $aisleIndex === 7) {
                return strcmp($locA, $locB);
            }
            
            // Autres allées uniques (H) : ordre standard
            return strcmp($locA, $locB);
        });
        
        return $items;
    }

    /**
     * Formate les instructions de picking pour une meilleure lisibilité
     */
    private function formatPickingInstructions(array $sequence): array {
        $instructions = [];
        
        foreach ($sequence as $aisleData) {
            $aisle = $aisleData['aisle'];
            $items = $aisleData['items'];
            $currentPair = null;
            $pairItems = [];
            
            foreach ($items as $item) {
                $location = $item['location'];
                $baseLocation = $this->getBaseLocation($location);
                
                if ($currentPair !== $baseLocation) {
                    if (!empty($pairItems)) {
                        $instructions[] = [
                            'aisle' => $aisle,
                            'shelf_pair' => $currentPair,
                            'items' => $pairItems,
                            'aisle_name' => implode('-', $this->pickingAisles[$aisle])
                        ];
                        $pairItems = [];
                    }
                    $currentPair = $baseLocation;
                }
                
                $pairItems[] = $item;
            }
            
            // Ajouter la dernière paire
            if (!empty($pairItems)) {
                $instructions[] = [
                    'aisle' => $aisle,
                    'shelf_pair' => $currentPair,
                    'items' => $pairItems,
                    'aisle_name' => implode('-', $this->pickingAisles[$aisle])
                ];
            }
        }
        
        return $instructions;
    }

    /**
     * Obtient l'emplacement de base pour une paire d'étagères
     */
    private function getBaseLocation(string $location): string {
        $paired = $this->shelfPairs[$location] ?? null;
        return $paired ? min($location, $paired) : $location;
    }

    /**
     * Optimise les groupes en fonction des capacités des chariots
     */
    private function optimizeGroupsForTrolleys(array $orders): array {
        // Créer des vecteurs de caractéristiques basés sur les emplacements
        $orderVectors = $this->createOrderLocationVectors($orders);
        
        // Préparer les types de caisses
        $orderTypes = [];
        foreach (array_keys($orders) as $orderId) {
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            $orderTypes[$orderId] = $caisseN == 100 ? self::CAISSE_ROUGE : self::CAISSE_NOIRE;
        }
        
        // Nombre maximal de commandes par chariot
        $groups = [];
        $currentGroup = [];
        $currentCapacity = [
            'etages' => self::TROLLEY_ETAGES,
            'rouges' => 0,
            'noires' => 0
        ];
    
        // Trier les commandes par proximité
        $sortedOrders = $this->sortOrdersByProximity($orderVectors);
    
        foreach ($sortedOrders as $orderId => $vector) {
            $type = $orderTypes[$orderId];
            $canAdd = $this->canAddToCurrentGroup($currentCapacity, $type);
    
            if (!$canAdd) {
                // Groupe plein, on en commence un nouveau
                if (!empty($currentGroup)) {
                    $groups[] = $currentGroup;
                }
                $currentGroup = [];
                $currentCapacity = [
                    'etages' => self::TROLLEY_ETAGES,
                    'rouges' => 0,
                    'noires' => 0
                ];
            }
    
            $currentGroup[] = $orderId;
            $this->updateGroupCapacity($currentCapacity, $type);
        }
    
        // Ajouter le dernier groupe s'il n'est pas vide
        if (!empty($currentGroup)) {
            $groups[] = $currentGroup;
        }
    
        return $groups;
    }

    private function sortOrdersByProximity(array $orderVectors): array {
        $sorted = [];
        $remaining = $orderVectors;
        
        // Commencer par une commande au hasard
        $currentOrderId = array_key_first($remaining);
        $currentVector = $remaining[$currentOrderId];
        $sorted[$currentOrderId] = $currentVector;
        unset($remaining[$currentOrderId]);
        
        // Ajouter les commandes les plus proches une par une
        while (!empty($remaining)) {
            $minDistance = PHP_FLOAT_MAX;
            $closestId = null;
            
            foreach ($remaining as $orderId => $vector) {
                $distance = $this->calculateDistance($currentVector, $vector);
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $closestId = $orderId;
                }
            }
            
            $currentOrderId = $closestId;
            $currentVector = $remaining[$closestId];
            $sorted[$closestId] = $currentVector;
            unset($remaining[$closestId]);
        }
        
        return $sorted;
    }
    

    private function centroidsConverged(array $oldCentroids, array $newCentroids, float $threshold = 0.01): bool {
        foreach ($oldCentroids as $index => $oldCentroid) {
            $distance = $this->calculateDistance($oldCentroid, $newCentroids[$index]);
            if ($distance > $threshold) {
                return false;
            }
        }
        return true;
    }

    private function findBestGroupWithCapacityConstraint(
        array $vector, 
        array $centroids, 
        array &$groups, 
        string $orderType
    ): int {
        $minDistance = PHP_FLOAT_MAX;
        $bestGroup = 0;
        
        foreach ($centroids as $groupIndex => $centroid) {
            // Vérifier la capacité du groupe
            if (!$this->checkGroupCapacity($groups[$groupIndex], $orderType)) {
                continue;
            }
            
            $distance = $this->calculateDistance($vector, $centroid);
            
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $bestGroup = $groupIndex;
            }
        }
        
        return $bestGroup;
    }

    private function checkGroupCapacity(array $groupOrders, string $orderType): bool {
        $blackCount = 0;
        $redCount = 0;
        
        foreach ($groupOrders as $orderId) {
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            $type = $caisseN == 100 ? 'rouge' : 'noire';
            
            if ($type === 'noire') {
                $blackCount++;
            } else {
                $redCount++;
            }
        }
        
        // Vérifier les contraintes de capacité
        if ($orderType === 'noire') {
            return $blackCount < self::TROLLEY_ETAGES;
        } else {
            $availableRedSlots = (self::TROLLEY_ETAGES - $blackCount) * self::CAPACITE_ROUGE;
            return $redCount < $availableRedSlots;
        }
    }

    private function createOrderLocationVectors(array $orders): array {
        $vectors = [];
        
        foreach ($orders as $orderId => $items) {
            $locationVector = array_fill_keys(array_keys($this->pickingAisles), 0);
            
            foreach ($items as $item) {
                $rack = substr($item[2], 0, 1);
                
                // Trouver l'allée correspondante
                foreach ($this->pickingAisles as $aisleIndex => $aisleRacks) {
                    if (in_array($rack, $aisleRacks)) {
                        $locationVector[$aisleIndex]++;
                        break;
                    }
                }
            }
            
            $vectors[$orderId] = $locationVector;
        }
        
        return $vectors;
    }

    /**
     * Analyse et retourne les statistiques des configurations de chariots
     */
    public function analyzeTrolleyConfigurations(array $orders): array {
        $groups = $this->optimizeOrders($orders);
        $configurations = [];
        $totalTrolleys = count($groups);
        
        foreach ($groups as $group) {
            $config = $this->analyzeTrolleyGroup($group);
            $key = $config['signature'];
            
            if (!isset($configurations[$key])) {
                $configurations[$key] = [
                    'pattern' => $config['pattern'],
                    'count' => 0,
                    'percentage' => 0
                ];
            }
            $configurations[$key]['count']++;
        }
        
        // Calculer les pourcentages
        foreach ($configurations as &$config) {
            $config['percentage'] = round(($config['count'] / $totalTrolleys) * 100, 2);
        }

        uasort($configurations, function($a, $b) {
            return $b['percentage'] - $a['percentage'];
        });
        
        return [
            'total_trolleys' => $totalTrolleys,
            'configurations' => $configurations
        ];
    }

     /**
     * Analyse la configuration d'un groupe de chariot
     */
    private function analyzeTrolleyGroup(array $group): array {
        $pattern = [];
        $blackCount = 0;
        $redCount = 0;
        
        foreach ($group['orders'] as $orderId => $item) {
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            if ( $caisseN == 100 )
            {
                $type = self::CAISSE_ROUGE;
            }
            else 
            {
                $type = self::CAISSE_NOIRE;
            }
            //$type = Caisse::getTailleCaisseByCommande($orderId);
            if ($type === 'noire') {
                $blackCount++;
            } else {
                $redCount++;
            }
        }
        
        // Construire le pattern du chariot
        for ($i = 0; $i < 3; $i++) {
            if ($blackCount > 0) {
                $pattern[] = '1 noire';
                $blackCount--;
            } else if ($redCount > 0) {
                $count = min(3, $redCount);
                $pattern[] = $count . ' rouge' . ($count > 1 ? 's' : '');
                $redCount -= $count;
            }
        }
        
        return [
            'signature' => implode('-', $pattern),
            'pattern' => $pattern
        ];
    }

    /**
     * Vérifie si une commande peut être ajoutée à un chariot
     */
    private function canAddToTrolley(array $trolley, string $typeCaisse): bool {
        if ($trolley['capacite'] <= 0) {
            return false;
        }

        if ($typeCaisse === self::CAISSE_NOIRE) {
            return $trolley['capacite'] >= 1;
        }

        // Pour les caisses rouges
        foreach ($trolley['etages'] as $etage) {
            if ($etage['type'] === self::CAISSE_ROUGE && count($etage['commandes']) < self::CAPACITE_ROUGE) {
                return true;
            }
        }

        return $trolley['capacite'] >= 1;
    }

     /**
     * Ajoute une commande à un chariot
     */
    private function addToTrolley(array &$trolley, string $orderId, string $typeCaisse): void {
        if (!isset($trolley['etages'])) {
            $trolley['etages'] = [];
        }

        // Compter les étages actuels
        $etagesUtilises = count($trolley['etages']);
        $etagesDisponibles = self::TROLLEY_ETAGES - $etagesUtilises;
        echo '$etagesDisponibles : '.self::TROLLEY_ETAGES .' - '. $etagesUtilises.'<br />';
        if ($etagesDisponibles < 0) {
            echo 'ERREUR : '.$orderId.'<br />';
            //throw new RuntimeException("Plus d'étages disponibles sur le chariot");
        }

        if ($typeCaisse === self::CAISSE_ROUGE) {
            // Chercher un étage rouge non complet
            $etageRouge = null;
            foreach ($trolley['etages'] as &$etage) {
                if ($etage['type'] === self::CAISSE_ROUGE && 
                    count($etage['commandes']) < self::CAPACITE_ROUGE) {
                    $etageRouge = &$etage;
                    break;
                }
            }

            // Si pas d'étage rouge trouvé, en créer un nouveau en haut
            if ($etageRouge === null) {
                array_unshift($trolley['etages'], [
                    'type' => self::CAISSE_ROUGE,
                    'commandes' => [$orderId]
                ]);
            } else {
                $etageRouge['commandes'][] = $orderId;
            }
        } else {
            // Ajouter la caisse noire à la fin
            $trolley['etages'][] = [
                'type' => self::CAISSE_NOIRE,
                'commandes' => [$orderId]
            ];
        }

        // Mettre à jour la capacité
        $trolley['capacite'] = self::TROLLEY_ETAGES - count($trolley['etages']);
    }

    /**
     * Ajoute un chariot à la liste des configurations
     */
    private function addTrolleyConfiguration(array &$configurations, array $trolley): void {
        $key = $this->getConfigurationKey($trolley);
        
        if (!isset($configurations[$key])) {
            $configurations[$key] = [];
        }
        
        $configurations[$key][] = $trolley;
    }

    /**
     * Génère une clé unique pour une configuration de chariot
     */
    private function getConfigurationKey(array $trolley): string {
        $key = '';
        foreach ($trolley['etages'] as $etage) {
            $key .= $etage['type'] . count($etage['commandes']) . '_';
        }
        return rtrim($key, '_');
    }

    /**
     * Génère une description lisible d'une configuration
     */
    private function getConfigurationDescription(array $trolley): string {
        $desc = "Configuration du chariot:\n";
        foreach ($trolley['etages'] as $index => $etage) {
            $desc .= sprintf(
                "Étage %d: %d %s%s\n",
                $index + 1,
                count($etage['commandes']),
                $etage['type'] === self::CAISSE_NOIRE ? "caisse noire" : "caisses rouges",
                count($etage['commandes']) > 1 ? "s" : ""
            );
        }
        return rtrim($desc, "\n");
    }

     /**
     * Calcule la disposition des commandes sur un chariot
     */
    private function calculateTrolleyLayout(array $orderIds): array {
        // Séparer les commandes par type de caisse
        $blackOrders = [];
        $redOrders = [];
        
        foreach ($orderIds as $orderId) {
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            if ($caisseN == 100) {
                $redOrders[] = $orderId;
            } else {
                $blackOrders[] = $orderId;
            }
        }
    
        // Initialiser le layout
        $layout = [];
    
        // Placer les caisses noires (max 3)
        foreach (array_slice($blackOrders, 0, 3) as $index => $orderId) {
            $layout[] = [
                'etage' => $index + 1,
                'type' => 'noire',
                'commandes' => [$orderId]
            ];
        }
    
        // Placer les caisses rouges
        $redOrdersRemaining = $redOrders;
        $etageRouge = count($layout) + 1;
    
        while (!empty($redOrdersRemaining) && $etageRouge <= 3) {
            $caissesSurEtage = array_splice($redOrdersRemaining, 0, 3);
            $layout[] = [
                'etage' => $etageRouge,
                'type' => 'rouge',
                'commandes' => $caissesSurEtage
            ];
            $etageRouge++;
        }
    
        return $layout;
    }

    /**
     * Place les caisses rouges sur les étages supérieurs du chariot
     */
    private function organizeTrolleyLayout(array $orderIds): array {
        // Séparer les commandes par type de caisse
        $ordersByType = [
            self::CAISSE_NOIRE => [],
            self::CAISSE_ROUGE => []
        ];
        
        foreach ($orderIds as $orderId) {
            $caisseN = Caisse::getTailleCaisseByCommande($orderId);
            if ( $caisseN == 100 )
            {
                $type = self::CAISSE_ROUGE;
            }
            else 
            {
                $type = self::CAISSE_NOIRE;
            }
            //$type = Caisse::getTailleCaisseByCommande($orderId);
            $ordersByType[$type][] = $orderId;
        }

        // Vérifier si ce groupe de commandes peut tenir sur un seul chariot
        $etagesNoiresNecessaires = count($ordersByType[self::CAISSE_NOIRE]);
        $etagesRougesNecessaires = ceil(count($ordersByType[self::CAISSE_ROUGE]) / self::CAPACITE_ROUGE);
        $totalEtagesNecessaires = $etagesNoiresNecessaires + $etagesRougesNecessaires;

        if ($totalEtagesNecessaires > self::TROLLEY_ETAGES) {
            throw new RuntimeException("Ce groupe nécessite plus de 3 étages (" . $totalEtagesNecessaires . "). Il doit être divisé.");
        }

        // Initialiser le layout avec exactement 3 étages
        $layout = array_fill(0, self::TROLLEY_ETAGES, [
            'type' => null,
            'commandes' => []
        ]);

        // Placer les caisses rouges en haut
        $currentEtage = 0;
        $rougesRestantes = $ordersByType[self::CAISSE_ROUGE];
        
        while (!empty($rougesRestantes) && $currentEtage < (self::TROLLEY_ETAGES - $etagesNoiresNecessaires)) {
            $caissesSurEtage = array_splice($rougesRestantes, 0, self::CAPACITE_ROUGE);
            $layout[$currentEtage] = [
                'type' => self::CAISSE_ROUGE,
                'commandes' => $caissesSurEtage
            ];
            $currentEtage++;
        }

        // Placer les caisses noires en bas
        $etageNoire = self::TROLLEY_ETAGES - 1;
        foreach ($ordersByType[self::CAISSE_NOIRE] as $orderId) {
            if ($etageNoire >= $currentEtage) {
                $layout[$etageNoire] = [
                    'type' => self::CAISSE_NOIRE,
                    'commandes' => [$orderId]
                ];
                $etageNoire--;
            }
        }

        // Nettoyer les étages vides et ajouter les numéros d'étages
        $finalLayout = [];
        for ($i = 0; $i < self::TROLLEY_ETAGES; $i++) {
            if ($layout[$i]['type'] !== null) {
                $finalLayout[] = [
                    'etage' => $i + 1,
                    'type' => $layout[$i]['type'],
                    'commandes' => $layout[$i]['commandes']
                ];
            }
        }

        return $finalLayout;
    }
}
?>