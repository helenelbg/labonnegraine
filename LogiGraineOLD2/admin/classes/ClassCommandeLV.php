<?php
    class CommandeLV
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
                //echo $req->build().'<br />';
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

        public static function getGroups($orders)
        {
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
                $optimizer = new WarehouseTopologyOptimizerLV();
                return $optimizer->optimizeOrders($commandes);
            }
            else 
            {
                return array();
            }
        }
    }

    class WarehouseTopologyOptimizerLV {
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
            'L' => ['L01', 'L02', 'L03', 'L04', 'L05']
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
            ['L']          // Allée 8
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
            'L05' => null
        ];
    
        /**
         * Point d'entrée principal pour optimiser les commandes
         */
        public function optimizeOrders(array $orders): array {
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
    
            $k = max(1, ceil(count($vectors) / 18));
            $maxIterations = 100;
            $iteration = 0;
            
            // Initialiser les centroids
            $centroids = $this->initializeCentroids($vectors, $k);
            $groups = [];
            
            do {
                $changed = false;
                $newGroups = array_fill(0, $k, []);
                
                // Assigner chaque commande au meilleur groupe
                foreach ($vectors as $orderId => $vector) {
                    $bestGroup = $this->findBestGroup($vector, $centroids);
                    $newGroups[$bestGroup][] = $orderId;
                }
                
                // Mettre à jour les centroids
                foreach ($newGroups as $groupId => $groupOrders) {
                    if (empty($groupOrders)) continue;
                    
                    $groupVectors = array_intersect_key($vectors, array_flip($groupOrders));
                    $newCentroid = $this->calculateCentroid($groupVectors);
                    
                    if (!isset($centroids[$groupId]) || $newCentroid !== $centroids[$groupId]) {
                        $centroids[$groupId] = $newCentroid;
                        $changed = true;
                    }
                }
                
                $groups = $newGroups;
                $iteration++;
                
            } while ($changed && $iteration < $maxIterations);
            
            return $this->balanceGroups($groups);
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
         * Équilibre les groupes pour avoir environ 18 commandes par groupe
         */
        private function balanceGroups(array $groups): array {
            $balanced = [];
            $currentGroup = [];
            $count = 0;
            
            foreach ($groups as $group) {
                foreach ($group as $orderId) {
                    $currentGroup[] = $orderId;
                    $count++;
                    
                    if ($count >= 18) {
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
                
                $groupOrders = array_intersect_key($orders, array_flip($group));
                $pickingPath = $this->calculateOptimalPath($groupOrders);
                $sequence = $this->generatePickingSequence($groupOrders, $pickingPath);
                
                $optimizedGroups[] = [
                    'orders' => $groupOrders,
                    'picking_path' => $pickingPath,
                    'sequence' => $this->formatPickingInstructions($sequence)
                ];
            }
            
            return $optimizedGroups;
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
}
?>