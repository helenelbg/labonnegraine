<?php
    class Controle
    {
        public $id;
        public $id_order;
        public $id_operateur;
        public $id_caisse;
        public $date_debut;
        public $date_fin;
        public $valide;
        public $zone;

        public function __construct($id_order = 0)
        {
            if ( $id_order > 0 )
            {
                $req = new DbQuery();
                $req->select('lgc.id_controle, lgc.id_order, lgc.id_operateur, lgc.id_caisse, lgc.date_debut, lgc.date_fin, lgc.valide, lgc.zone');
                $req->from('LogiGraine_controle', 'lgc');
                $req->where('lgc.id_order = "'.$id_order.'"');
                //echo $req->build();
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_controle']) && !empty($resu[0]['id_controle']) )
                {
                    $this->id = $resu[0]['id_controle'];
                    $this->id_order = $resu[0]['id_order'];
                    $this->id_operateur = $resu[0]['id_operateur'];
                    $this->id_caisse = $resu[0]['id_caisse'];
                    $this->date_debut = $resu[0]['date_debut'];
                    $this->date_fin = $resu[0]['date_fin'];
                    $this->valide = $resu[0]['valide'];
                    if ( empty($resu[0]['zone']) )
                    {
                        $this->zone = Commande::getZoneByOrder($resu[0]['id_order']);
                        $sqlUpd = 'UPDATE `' . _DB_PREFIX_ . 'LogiGraine_controle`
                        SET `zone` = "' . $this->zone . '"
                        WHERE  `id_controle` = ' . (int) $this->id;
                        Db::getInstance()->execute($sqlUpd);
                    }
                    else
                    {
                        $this->zone = $resu[0]['zone'];
                    }
                }
            }
        }

        public function scanCaisse($codeCaisse = '', $tailleCaisse = '')
        {
            if ( !empty($codeCaisse) && !empty($tailleCaisse) )
            {
                $req = new DbQuery();
                $req->select('lgc.id_caisse');
                $req->from('LogiGraine_caisse', 'lgc');
                $req->where('lgc.code_caisse = "'.$codeCaisse.'"');
                $req->where('lgc.taille_caisse = "'.$tailleCaisse.'"');
                $req->where('lgc.id_caisse NOT IN (SELECT tmp.id_caisse FROM ps_LogiGraine_controle tmp WHERE tmp.id_caisse = lgc.id_caisse AND tmp.transport = 0)');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_caisse']) && !empty($resu[0]['id_caisse']) )
                {
                    $sqlUpd = 'UPDATE `' . _DB_PREFIX_ . 'LogiGraine_controle`
                        SET `id_caisse` = "' . $resu[0]['id_caisse'] . '", date_debut = NOW(), date_fin = "0000-00-00 00:00:00", id_operateur = "'.$_SESSION['operateur'].'"
                        WHERE  `id_controle` = ' . (int) $this->id;
                    Db::getInstance()->execute($sqlUpd);
                    $this->id_caisse = $resu[0]['id_caisse'];

                    /*$history = new OrderHistory();
                    $history->id_order = $this->id_order;
                    $history->id_order_state = 3;
                    $history->add();
                    $history->changeIdOrderState(3, $this->id_order);*/

                    return true;
                }
                else
                {
                    $req = new DbQuery();
                    $req->select('lgc.id_order');
                    $req->from('LogiGraine_controle', 'lgc');
                    $req->leftJoin('LogiGraine_caisse', 'lgc2', 'lgc.id_caisse = lgc2.id_caisse');
                    $req->where('lgc2.code_caisse = "'.$codeCaisse.'"');
                    $req->where('lgc2.taille_caisse = "'.$tailleCaisse.'"');
                    $req->where('lgc.transport = "0"');
                    $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                    if ( isset($resu[0]['id_order']) && !empty($resu[0]['id_order']) )
                    {
                        return 'Cette caisse est encore associé à la commande '.$resu[0]['id_order'].'. <br />L\'étiquette transport ne doit pas être imprimée.';
                    }
                    return 'Erreur de caisse';
                }
            }
        }

        public function scanProduit($codeProduit = '')
        {
            function search_user_by_ean($ean, $array)
            {
                foreach ($array as $keys) {
                    foreach ($keys as $key => $_user_record) {
                        if ($_user_record == $ean) {
                            return $keys;
                        }
                    }
                }
                return false;
            }
            if ( !empty($codeProduit) )
            {
                /*$cmdEC = Commande::getProductsByOrder($this->id_order);
                if ( ($produitEC = search_user_by_ean($codeProduit, $cmdEC)) !== false )
                {*/
                    $reqGetP = 'SELECT * FROM ps_order_detail WHERE id_order = "'.$this->id_order.'" AND product_ean13 = "'.$codeProduit.'";';
                    $resuGetP = Db::getInstance()->executeS($reqGetP);

                    if ( !isset($resuGetP[0]['product_id']) )
                    {
                        $produitEC['quantity_final'] = 0;
                        $req_ppack = 'SELECT pa.id_product_item, pa.id_product_attribute_item, od.product_quantity, od.product_quantity_refunded, pa.quantity FROM ps_order_detail od LEFT JOIN ps_product p ON od.product_id = p.id_product LEFT JOIN ps_pack pa ON p.id_product = pa.id_product_pack WHERE od.id_order = "'.$this->id_order.'" AND p.product_type = "pack";';
                        $resu_ppack = Db::getInstance()->executeS($req_ppack);
                        foreach($resu_ppack as $rangee_ppack)
                        {
                            $req_pack = new DbQuery();
                            $req_pack->select('p.id_product, pa.id_product_attribute');
                            $req_pack->from('product', 'p');
                            $req_pack->leftJoin('product_attribute', 'pa', 'p.id_product = pa.id_product');
                            $req_pack->where('p.id_product = "'.$rangee_ppack['id_product_item'].'"');
                            $req_pack->where('pa.id_product_attribute = "'.$rangee_ppack['id_product_attribute_item'].'"');
                            $req_pack->where('pa.ean13 = "'.$codeProduit.'" OR p.ean13 = "'.$codeProduit.'"');
                            error_log($req_pack->build()); 
                            $resu_pack = Db::getInstance()->executeS($req_pack);
                            if ( isset($resu_pack[0]['id_product']) )
                            {
                                $produitEC['product_id'] = $rangee_ppack['id_product_item'];
                                $produitEC['product_attribute_id'] = $rangee_ppack['id_product_attribute_item'];
                                $produitEC['quantity_final'] += ($rangee_ppack['product_quantity']*$rangee_ppack['quantity']) - ($rangee_ppack['product_quantity_refunded']*$rangee_ppack['quantity']);
                            }
                        }                        
                    }
                    else 
                    {
                        $produitEC = $resuGetP[0];
                        $produitEC['quantity_final'] = $resuGetP[0]['product_quantity'] - $resuGetP[0]['product_quantity_refunded'];
                    }
                    $check = ControleProduit::check($this->id, $produitEC['product_id'], $produitEC['product_attribute_id']);
                    //error_log('check : '.$check);
                    if ( $check === false )
                    {
                        $sqlIns = 'INSERT INTO `' . _DB_PREFIX_ . 'LogiGraine_controle_produit`
                            SET `id_controle` = "' . $this->id . '", id_product = "'.$produitEC['product_id'].'", id_product_attribute = "'.$produitEC['product_attribute_id'].'", quantite_prepare = 1;';

                        //error_log($sqlIns);
                        Db::getInstance()->execute($sqlIns);

                        // Destock LogiGraine Pdt
                  			$reqLGG = 'UPDATE ps_LogiGraine_rangement_pdt_pk SET quantity = quantity - 1 WHERE id_product = "'.$produitEC['product_id'].'" AND id_product_attribute = "'.$produitEC['product_attribute_id'].'";';
                  			Db::getInstance()->execute($reqLGG);

                    }
                    else
                    {
                        if ( $produitEC['quantity_final'] == $check['quantite_prepare'] )
                        {
                            error_log('aaaa');
                            error_log(print_r($produitEC, true));
                            return 'Ce produit est déjà en nombre suffisant ('.$produitEC['quantity_final'].'/'.$check['quantite_prepare'].')';
                        }
                        else
                        {
                            $sqlUpd = 'UPDATE `' . _DB_PREFIX_ . 'LogiGraine_controle_produit`
                            SET `quantite_prepare` = `quantite_prepare` + 1
                            WHERE  `id_controle_produit` = "'.$check['id_controle_produit'].'";';
                            Db::getInstance()->execute($sqlUpd);

                            // maj picking pommes de terre
                            $sqlUpd2 = 'UPDATE `' . _DB_PREFIX_ . 'LogiGraine_rangement_pdt_pk`
                            SET `quantity` = `quantity` - 1
                            WHERE  id_product = "'.$produitEC['product_id'].'" AND id_product_attribute = "'.$produitEC['product_attribute_id'].'";';
                            Db::getInstance()->execute($sqlUpd2);
                        }
                    }
                    return true;
                /*}
                else
                {
                    return "Ce produit n'est pas dans la commande";
                }*/
            }
        }

        public static function scanProduitGroup($codeProduit = '', $orderEC = '')
        {
            function search_user_by_ean2($ean, $array)
            {
                foreach ($array as $keys) {
                    foreach ($keys as $key => $_user_record) {
                        if ($_user_record == $ean) {
                            return $keys;
                        }
                    }
                }
                return false;
            }
            if ( !empty($codeProduit) && !empty($orderEC) )
            {
                /*$cmdEC = Commande::getProductsByOrder($orderEC);
                if ( ($produitEC = search_user_by_ean2($codeProduit, $cmdEC)) !== false )
                {*/
                    /*if ( $codeProduit == '1234567891231' )
                    {
                        $reqGetP = 'SELECT * FROM ps_order_detail WHERE id_order = "'.$orderEC.'" AND product_id = "2154";';
                        $resuGetP = Db::getInstance()->executeS($reqGetP);
                        if ( count($resuGetP) == 1 )
                        {
                            $quantity_final = $resuGetP[0]['product_quantity'] - $resuGetP[0]['product_quantity_refunded'];
                            $produitEC = array('product_id' => 999999, 'product_attribute_id' => 0, 'quantity_final' => $quantity_final);
                        }
                    }
                    else 
                    {*/
                        $reqGetP = 'SELECT * FROM ps_order_detail WHERE id_order = "'.$orderEC.'" AND product_ean13 = "'.$codeProduit.'";';
                        $resuGetP = Db::getInstance()->executeS($reqGetP);
                        //$produitEC = $resuGetP[0];
                        //$produitEC['quantity_final'] = $produitEC['product_quantity'] - $produitEC['product_quantity_refunded'];
                    //}

                    if ( !isset($resuGetP[0]['product_id']) )
                    {
                        $produitEC['quantity_final'] = 0;
                        $req_ppack = 'SELECT pa.id_product_item, pa.id_product_attribute_item, od.product_quantity, od.product_quantity_refunded, pa.quantity FROM ps_order_detail od LEFT JOIN ps_product p ON od.product_id = p.id_product LEFT JOIN ps_pack pa ON p.id_product = pa.id_product_pack WHERE od.id_order = "'.$orderEC.'" AND p.product_type = "pack";';
                        $resu_ppack = Db::getInstance()->executeS($req_ppack);
                        foreach($resu_ppack as $rangee_ppack)
                        {
                            $req_pack = new DbQuery();
                            $req_pack->select('p.id_product, pa.id_product_attribute');
                            $req_pack->from('product', 'p');
                            $req_pack->leftJoin('product_attribute', 'pa', 'p.id_product = pa.id_product');
                            $req_pack->where('p.id_product = "'.$rangee_ppack['id_product_item'].'"');
                            $req_pack->where('pa.id_product_attribute = "'.$rangee_ppack['id_product_attribute_item'].'"');
                            $req_pack->where('pa.ean13 = "'.$codeProduit.'" OR p.ean13 = "'.$codeProduit.'"');
                            error_log($req_pack->build()); 
                            $resu_pack = Db::getInstance()->executeS($req_pack);
                            if ( isset($resu_pack[0]['id_product']) )
                            {
                                $produitEC['product_id'] = $rangee_ppack['id_product_item'];
                                $produitEC['product_attribute_id'] = $rangee_ppack['id_product_attribute_item'];
                                $produitEC['quantity_final'] += ($rangee_ppack['product_quantity']*$rangee_ppack['quantity']) - ($rangee_ppack['product_quantity_refunded']*$rangee_ppack['quantity']);
                            }
                        }                        
                    }
                    else 
                    {
                        $produitEC = $resuGetP[0];
                        $produitEC['quantity_final'] = $produitEC['product_quantity'] - $produitEC['product_quantity_refunded'];
                    }

                    //$check = ControleProduit::check($this->id, $produitEC['product_id'], $produitEC['product_attribute_id']);
                    $check = ControleProduit::checkGroup($orderEC, $produitEC['product_id'], $produitEC['product_attribute_id']);
                    //error_log('check : '.$check);
                    if ( $check === false )
                    {
                        $sqlsc = 'SELECT id_controle FROM `' . _DB_PREFIX_ . 'LogiGraine_controle`
                        WHERE  `id_order` = "'.$orderEC.'";';
                        $resu_sqlsc = Db::getInstance()->executeS($sqlsc);

                        $sqlIns = 'INSERT INTO `' . _DB_PREFIX_ . 'LogiGraine_controle_produit`
                            SET `id_controle` = "' . $resu_sqlsc[0]['id_controle'] . '", id_product = "'.$produitEC['product_id'].'", id_product_attribute = "'.$produitEC['product_attribute_id'].'", quantite_prepare = 1;';

                        //error_log($sqlIns);
                        Db::getInstance()->execute($sqlIns);

                        // Destock LogiGraine Pdt
                  			$reqLGG = 'UPDATE ps_LogiGraine_rangement_pdt_pk SET quantity = quantity - 1 WHERE id_product = "'.$produitEC['product_id'].'" AND id_product_attribute = "'.$produitEC['product_attribute_id'].'";';
                  			Db::getInstance()->execute($reqLGG);

                    }
                    else
                    {
                        if ( $produitEC['quantity_final'] == $check['quantite_prepare'] )
                        {
                            return 'Ce produit est déjà en nombre suffisant';
                        }
                        else
                        {
                            $sqlUpd = 'UPDATE `' . _DB_PREFIX_ . 'LogiGraine_controle_produit`
                            SET `quantite_prepare` = `quantite_prepare` + 1
                            WHERE  `id_controle_produit` = "'.$check['id_controle_produit'].'";';
                            Db::getInstance()->execute($sqlUpd);

                            // maj picking pommes de terre
                            $sqlUpd2 = 'UPDATE `' . _DB_PREFIX_ . 'LogiGraine_rangement_pdt_pk`
                            SET `quantity` = `quantity` - 1
                            WHERE  id_product = "'.$produitEC['product_id'].'" AND id_product_attribute = "'.$produitEC['product_attribute_id'].'";';
                            Db::getInstance()->execute($sqlUpd2);
                        }
                    }
                    return true;
                /*}
                else
                {
                    return "Ce produit n'est pas dans la commande";
                }*/
            }
        }

        public function getNbControleEC()
        {
            $req = new DbQuery();
            $req->select('SUM(lgcp.quantite_prepare) as nb');
            $req->from('LogiGraine_controle_produit', 'lgcp');
            $req->where('lgcp.id_controle = "'.$this->id.'"');
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
            if ( !isset($resu[0]['nb']) || empty($resu[0]['nb']) )
            {
                $resu[0]['nb'] = 0;
            }
            return $resu[0]['nb'];
        }

        public static function getNbControleECGroup($orders)
        {
            $req = new DbQuery();
            $req->select('SUM(lgcp.quantite_prepare) as nb');
            $req->from('LogiGraine_controle_produit', 'lgcp');
            $req->leftJoin('LogiGraine_controle', 'lgc', 'lgcp.id_controle = lgc.id_controle');
            $req->where('lgc.id_order IN ('.str_replace('_', ',', $orders).')');
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
            if ( !isset($resu[0]['nb']) || empty($resu[0]['nb']) )
            {
                $resu[0]['nb'] = 0;
            }
            return $resu[0]['nb'];
        }

        public function validate()
        {
            $sqlUpd = 'UPDATE `' . _DB_PREFIX_ . 'LogiGraine_controle`
            SET `valide` = 1, date_fin = NOW()
            WHERE  `id_controle` = "'.$this->id.'";';
            Db::getInstance()->execute($sqlUpd);
            $this->valide = 1;

            $history = new OrderHistory();
            $history->id_order = $this->id_order;
            $history->id_order_state = 43;
            $history->add();
            $history->changeIdOrderState(43, $this->id_order);

            return true;
        }

        public function validateLettreVerte()
        {
            $history = new OrderHistory();
            $history->id_order = $this->id_order;
            $history->id_order_state = 4;
            $history->add();
            $history->changeIdOrderState(4, $this->id_order);
        }
    }
?>
