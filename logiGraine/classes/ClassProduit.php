<?php
    class Produit
    {
        public $id;
        public $reference;
        public $nom;
        public $nom_limit;
        public $declinaison;
        public $id_declinaison;

        public function __construct($ean = 0, $id_product = 0, $id_product_attribute = 0)
        {
            if ( $ean > 0 )
            {
                $req = new DbQuery();
                $req->select('p.id_product, pa.id_product_attribute, p.reference, pl.name, al.name as decli');
                $req->from('product_attribute', 'pa');
                $req->leftJoin('product_lang', 'pl', 'pa.id_product = pl.id_product AND pl.id_lang = 1');
                $req->leftJoin('product', 'p', 'pa.id_product = p.id_product');
                $req->leftJoin('product_attribute_combination', 'pac', 'pa.id_product_attribute = pac.id_product_attribute');
                $req->leftJoin('attribute', 'a', 'pac.id_attribute = a.id_attribute');
                $req->leftJoin('attribute_lang', 'al', 'a.id_attribute = al.id_attribute AND al.id_lang = 1');
                $req->where('pa.ean13 LIKE "'.$ean.'%"');
                $req->where('a.id_attribute_group = 6');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_product']) && !empty($resu[0]['id_product']) )
                {
                    $this->id = $resu[0]['id_product'];
                    $this->reference = $resu[0]['reference'];
                    $this->nom = $resu[0]['name'];
                    if ( strlen($resu[0]['name']) > 30 )
                    {
                        $this->nom_limit = substr($resu[0]['name'], 0, 30).'...';
                    }
                    else
                    {
                        $this->nom_limit = $resu[0]['name'];
                    }
                    $this->declinaison = $resu[0]['decli'];
                    $this->id_declinaison = $resu[0]['id_product_attribute'];
                }
                else 
                {
                    $req = new DbQuery();
                    $req->select('p.id_product, pa.id_product_attribute, p.reference, pl.name, al.name as decli');
                    $req->from('product_attribute', 'pa');
                    $req->leftJoin('product_lang', 'pl', 'pa.id_product = pl.id_product AND pl.id_lang = 1');
                    $req->leftJoin('product', 'p', 'pa.id_product = p.id_product');
                    $req->leftJoin('product_attribute_combination', 'pac', 'pa.id_product_attribute = pac.id_product_attribute');
                    $req->leftJoin('attribute', 'a', 'pac.id_attribute = a.id_attribute');
                    $req->leftJoin('attribute_lang', 'al', 'a.id_attribute = al.id_attribute AND al.id_lang = 1');
                    $req->where('pa.ean13 LIKE "'.$ean.'%"');
                    $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                    if ( isset($resu[0]['id_product']) && !empty($resu[0]['id_product']) )
                    {
                        $this->id = $resu[0]['id_product'];
                        $this->reference = $resu[0]['reference'];
                        $this->nom = $resu[0]['name'];
                        if ( strlen($resu[0]['name']) > 30 )
                        {
                            $this->nom_limit = substr($resu[0]['name'], 0, 30).'...';
                        }
                        else
                        {
                            $this->nom_limit = $resu[0]['name'];
                        }
                        $this->declinaison = $resu[0]['decli'];
                        $this->id_declinaison = $resu[0]['id_product_attribute'];
                    }
                }
            }
            else if ( $id_product > 0 && $id_product_attribute > 0 )
            {
                $req = new DbQuery();
                $req->select('p.id_product, pa.id_product_attribute, p.reference, pl.name, al.name as decli');
                $req->from('product_attribute', 'pa');
                $req->leftJoin('product_lang', 'pl', 'pa.id_product = pl.id_product AND pl.id_lang = 1');
                $req->leftJoin('product', 'p', 'pa.id_product = p.id_product');
                $req->leftJoin('product_attribute_combination', 'pac', 'pa.id_product_attribute = pac.id_product_attribute');
                $req->leftJoin('attribute', 'a', 'pac.id_attribute = a.id_attribute');
                $req->leftJoin('attribute_lang', 'al', 'a.id_attribute = al.id_attribute AND al.id_lang = 1');
                $req->where('pa.id_product = "'.$id_product.'"');
                $req->where('pa.id_product_attribute = "'.$id_product_attribute.'"');
                $req->where('a.id_attribute_group = 6');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_product']) && !empty($resu[0]['id_product']) )
                {
                    $this->id = $resu[0]['id_product'];
                    $this->reference = $resu[0]['reference'];
                    $this->nom = $resu[0]['name'];
                    if ( strlen($resu[0]['name']) > 30 )
                    {
                        $this->nom_limit = substr($resu[0]['name'], 0, 30).'...';
                    }
                    else
                    {
                        $this->nom_limit = $resu[0]['name'];
                    }
                    $this->declinaison = $resu[0]['decli'];
                    $this->id_declinaison = $resu[0]['id_product_attribute'];
                }
            }
        }

        public function getEmplacements()
        {
            $req = new DbQuery();
            $req->select('lgb.id_boite, lgb.code_boite, lgb.id_product, lgb.reserve, lgbd.id_product_attribute, SUM(lgbd.quantity) AS qte, COUNT(*) AS nb, lgrb.emplacement');
            $req->from('LogiGraine_boite', 'lgb');
            $req->leftJoin('LogiGraine_boite_decli', 'lgbd', 'lgb.id_boite = lgbd.id_boite');
            $req->leftJoin('LogiGraine_rangement_boite', 'lgrb', 'lgb.id_boite = lgrb.id_boite');
            $req->where('lgb.id_product = "'.$this->id.'"');
            $req->where('lgrb.emplacement <> "facing"');
            $req->groupBy('lgb.id_product');
            $req->orderBy('lgrb.emplacement ASC');
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

            $emplacements = array();
            foreach ( $resu as $emplacement )
            {
                $emplacements[] = array('id_boite' => $emplacement['id_boite'], 'code_boite' => $emplacement['code_boite'], 'id_product' => $emplacement['id_product'], 'reserve' => $emplacement['reserve'], 'quantity' => $emplacement['qte'], 'emplacement' => $emplacement['emplacement'], 'nb_boite' => $emplacement['nb']);
            }

            return $emplacements;
        }

        public static function getReassortPdt()
        {
            $req = new DbQuery();
            $req->select('lgpr.id_operateur, lgpr.id_product, lgpr.id_product_attribute, lgpr.emplacement, lgpr.quantity, lgpr.prepare');
            $req->from('LogiGraine_rangement_pdt_reassort', 'lgpr');
            $req->leftJoin('LogiGraine_rangement_pdt_reserve', 'lgpr2', 'lgpr.emplacement = lgpr2.emplacement');
            $req->where('lgpr.termine = "0"');
            $req->orderBy('lgpr2.ordre ASC, lgpr.id_product ASC, lgpr.id_product_attribute ASC');
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

            return $resu;
        }

        public function autresDeclis()
        {
            $req = new DbQuery();
            $req->select('p.id_product, pa.id_product_attribute, p.reference, pl.name, al.name as decli, pa.ean13');
            $req->from('product_attribute', 'pa');
            $req->leftJoin('product_lang', 'pl', 'pa.id_product = pl.id_product AND pl.id_lang = 1');
            $req->leftJoin('product', 'p', 'pa.id_product = p.id_product');
            $req->leftJoin('product_attribute_combination', 'pac', 'pa.id_product_attribute = pac.id_product_attribute');
            $req->leftJoin('attribute', 'a', 'pac.id_attribute = a.id_attribute');
            $req->leftJoin('attribute_lang', 'al', 'a.id_attribute = al.id_attribute AND al.id_lang = 1');
            $req->where('pa.id_product = "'.$this->id.'%"');
            $req->where('pa.id_product_attribute <> "'.$this->id_declinaison.'"');
            $req->where('a.id_attribute_group = 6');
            $req->orderBy('a.position', 'ASC');
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

            $autres = array();
            foreach ( $resu as $autre )
            {
                $autres[] = array('declinaison' => $autre['decli'], 'id_declinaison' => $autre['id_product_attribute'], 'ean' => $autre['ean13']);
            }

            return $autres;
        }
    }
?>
