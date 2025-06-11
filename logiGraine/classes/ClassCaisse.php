<?php
    class Caisse
    {
        public $id;
        public $taille;
        public $code;

        public function __construct($id_caisse = 0)
        {
            if ( $id_caisse > 0 )
            {
                $req = new DbQuery();
                $req->select('lgc.id_caisse, lgc.taille_caisse, lgc.code_caisse');
                $req->from('LogiGraine_caisse', 'lgc');
                $req->where('lgc.id_caisse = "'.$id_caisse.'"');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_caisse']) && !empty($resu[0]['id_caisse']) )
                {
                    $this->id = $resu[0]['id_caisse'];
                    $this->taille = $resu[0]['taille_caisse'];
                    $this->code = $resu[0]['code_caisse'];
                }
            }
        }

        public static function getTailleCaisseByCommande($id_order = 0)
        {
            //$taille_caisse = array(100,200,400);
            $taille_caisse = array(100,400);
            if ( $id_order > 0 )
            {
                $liste_zone = array(
                    1 => array(
                        '0-', '1-', '2-', '3-', '4-', '20-000'
                    ),
                    2 => array(
                        '5-'
                    ),
                    3 => array(
                        '6-', '7-', '8-'
                    ),
                    4 => array(
                        '9-', '10-', '11-', '13-'
                    ),
                    5 => array(
                        '14-', '15-', 'M-', 'PACK45'
                    )
                );
                $unite_zone = array(
                    1 => 1,
                    2 => 10,
                    3 => 10,
                    4 => 10,
                    5 => 10
                );

                $cmdEC = Commande::getProductsByOrder($id_order);
                $tailleCmd = 0;
                $force = 0;
                foreach($cmdEC as $prodEC)
                {
                    $tmpProd = new Product($prodEC['product_id']);
                    $cpt = 0;
                    $position = 0;

                    foreach($tmpProd->getAttributesGroups(1) as $tmpAttr)
                    {
                        $cpt++;
                        if ( $tmpAttr['id_attribute_group'] == 6 && $tmpAttr['id_product_attribute'] == $prodEC['product_attribute_id'] )
                        {
                            $position = $cpt;
                        }
                    }
                    $prefixe = explode('-', $prodEC['product_reference'])[0].'-';
                    $zoneEC = 0;
                    foreach($liste_zone as $idz => $z)
                    {
                        if ( in_array($prefixe, $z) )
                        {
                            $zoneEC = $idz;
                        }
                    }

                    if ( $zoneEC == 4 ) // Extérieur
                    {
                        $req = new DbQuery();
                        $req->select('fp.id_feature, fp.id_feature_value');
                        $req->from('feature_product', 'fp');
                        $req->where('fp.id_product = "'.$prodEC['product_id'].'" AND id_feature = 19');
                        $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                        if ( isset($resu[0]['id_feature']) && !empty($resu[0]['id_feature']) )
                        {
                            if ( $resu[0]['id_feature_value'] == 2266 ) // 7 cm
                            {
                                $tailleCmd += 12 * $prodEC['product_quantity'];
                            }
                            elseif ( $resu[0]['id_feature_value'] == 2267 || $resu[0]['id_feature_value'] == 2280 ) // 9 cm et 8 cm
                            {
                                $tailleCmd += 16 * $prodEC['product_quantity'];
                            }
                            elseif ( $resu[0]['id_feature_value'] == 2281 ) // 2L
                            {
                                $tailleCmd += 50 * $prodEC['product_quantity'];
                            }
                            elseif ( $resu[0]['id_feature_value'] == 6080 ) // Racines nues
                            {
                                $tailleCmd += 50 * $prodEC['product_quantity'];
                            }
                        }
                        else
                        {
                            // on a pas la taille du produit, on force la caisse verte
                            $tailleCmd += 200;
                        }
                    }
                    elseif ( $zoneEC == 5 ) // Accessoires
                    {
                        $req = new DbQuery();
                        $req->select('lga.ref, lga.unite, lga.forcer');
                        $req->from('LogiGraine_accessoires', 'lga');
                        $req->where('lga.ref = "'.$prodEC['product_reference'].'"');
                        $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                        if ( isset($resu[0]['ref']) && !empty($resu[0]['ref']) )
                        {
                            $tailleCmd += $resu[0]['unite'] * $prodEC['product_quantity'];
                            if ( $resu[0]['forcer'] > $force )
                            {
                                $force = $resu[0]['forcer'];
                            }
                        }
                        else
                        {
                            // on a pas la taille de l'accessoire, on force la caisse noire
                            $tailleCmd += 400;
                        }
                    }
                    else
                    {
                        // OK Asperges 100 (7-600 > 7-999)
                        // OK Rosiers 400 (0-001 > 0-299)
                        // OK 2-900 > 3-999 5
                        // OK Fraisiers en racines nues 7-500 > 7-599 10
                        // Pdt 1.5kg 100 (6-001 > 6-999)

                        $refEC = (int)((str_replace('-', '', $prodEC['product_reference'])));
                        if ( $refEC >= 7600 && $refEC <= 7999 )
                        {
                            $unite_zone[$zoneEC] = 100;
                        }
                        elseif ( $refEC >= 1 && $refEC <= 299 )
                        {
                            $unite_zone[$zoneEC] = 400;
                        }
                        elseif ( $refEC >= 2900 && $refEC <= 3999 )
                        {
                            $unite_zone[$zoneEC] = 5;
                        }
                        elseif ( $refEC >= 7500 && $refEC <= 7599 )
                        {
                            $unite_zone[$zoneEC] = 10;
                        }
                        elseif ( !isset($unite_zone[$zoneEC]) )
                        {
                            $unite_zone[$zoneEC] = 0;
                        }
                        $taille_prod = $unite_zone[$zoneEC] * $prodEC['product_quantity'];
                        for ($i = 1; $i < $position; $i++)
                        {
                            $taille_prod = $taille_prod * 4;
                        }
                        $tailleCmd += $taille_prod;
                    }
                }
                if ( !isset($force))
                {
                    $force = 0;
                }
                if ( $force > $tailleCmd )
                {
                    return $force;
                }

                foreach($taille_caisse as $tc)
                {
                    if ( $tailleCmd < $tc )
                    {
                        return $tc;
                    }
                }
                return 400;
            }
        }

        public static function getCommandeByCode($code_caisse = 0)
        {
            if ( $code_caisse > 0 )
            {
                $req = new DbQuery();
                $req->select('lgc.id_order');
                $req->from('LogiGraine_controle', 'lgc');
                $req->leftJoin('LogiGraine_caisse', 'lgc2', 'lgc.id_caisse = lgc2.id_caisse');
                $req->where('lgc2.code_caisse = "'.$code_caisse.'"');
                $req->where('lgc.valide = "1"');
                $req->where('lgc.transport = "0"');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_order']) && !empty($resu[0]['id_order']) )
                {
                    return $resu[0]['id_order'];
                }
                return false;
            }
        }

        public static function getDerniereCommandeByCode($code_caisse = 0)
        {
            if ( $code_caisse > 0 )
            {
                $req = new DbQuery();
                $req->select('lgc.id_order, lgc.date_fin');
                $req->from('LogiGraine_controle', 'lgc');
                $req->leftJoin('LogiGraine_caisse', 'lgc2', 'lgc.id_caisse = lgc2.id_caisse');
                $req->where('lgc2.code_caisse = "'.$code_caisse.'"');
                $req->where('lgc.valide = "1"');
                $req->where('lgc.transport = "1"');
                $req->orderBy('lgc.`date_debut` DESC');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_order']) && !empty($resu[0]['id_order']) )
                {
                    return $resu[0]['id_order'].'#'.$resu[0]['date_fin'];
                }
                return false;
            }
        }

        public static function checkTailleCaisseByCode($code = 0, $taille = 0)
        {
            if ( $code > 0 && $taille > 0 )
            {
                $req = new DbQuery();
                $req->select('lgc.id_caisse, lgc.taille_caisse, lgc.code_caisse');
                $req->from('LogiGraine_caisse', 'lgc');
                $req->where('lgc.code_caisse = "'.$code.'"');
                $req->where('lgc.taille_caisse = "'.$taille.'"');

                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_caisse']) && !empty($resu[0]['id_caisse']) )
                {
                    return $resu[0]['id_caisse'];
                }
            }
            return false;
        }
    }
?>
