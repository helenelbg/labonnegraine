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
            $taille_caisse = array(100,200,400);
            if ( $id_order > 0 )
            {
                $liste_zone = array(
                    1 => array(
                        '0-', '1-', '2-', '3-', '4-'
                    ),
                    2 => array(
                        '5-'
                    ),
                    3 => array(
                        '6-', '7-', '8-'
                    ),
                    4 => array(
                        '9-', '10-', '11-'
                    ),
                    5 => array(
                        '14-', '15-', 'M-'
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
                    if ( !isset($unite_zone[$zoneEC]) )
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