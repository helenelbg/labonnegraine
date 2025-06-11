<?php
    require 'application_top.php';

    if ( isset($_POST['id_pda']) && !empty($_POST['id_pda']) )
    {
		$req_o = 'SELECT id_operateur FROM ps_LogiGraine_pda_operateur WHERE id_pda = "'.$_POST['id_pda'].'";';
		$resu_o = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_o);
		$operateur = $resu_o[0]['id_operateur'];

        $req = 'DELETE FROM ps_LogiGraine_pda_order WHERE id_order IN ("'.implode('","', $_POST['orders']).'");';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
        
		$multi_test = false;
		$array_cmd = array();
		foreach($_POST['orders'] as $ordTmp)
		{
			if ( strpos($ordTmp, '_') > 0 )
			{
				$multi_test = true;
				$expl_multiTmp = explode('_', $ordTmp);
				foreach($expl_multiTmp as $multiTmp)
				{
					$array_cmd[] = $multiTmp;
				}
			}
		}

		if ( $multi_test == true )
		{
			$req2 = 'DELETE FROM ps_LogiGraine_controle WHERE id_order IN ('.implode(',', $array_cmd).');';
			Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2);

			$req2d = 'DELETE FROM ps_LogiGraine_controle_produit_ordre WHERE id_order IN ('.implode(',', $array_cmd).');';
			Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2d);
		}
		else 
		{
			$req2 = 'DELETE FROM ps_LogiGraine_controle WHERE id_order IN ('.implode(',', $_POST['orders']).');';
			Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2);

			$req2d = 'DELETE FROM ps_LogiGraine_controle_produit_ordre WHERE id_order IN ('.implode(',', $_POST['orders']).');';
			Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2d);
		}
        
        if ( $_POST['id_pda'] > 0 )
        {
            $rows = '';
            $rows2 = '';
			$exeOK = true;
            foreach($_POST['orders'] as $ordEC)
            {
				$caisseLV = 292;
                if ( !empty($rows) )
                {
                    $rows .= ',';
                }
                $rows .= '('.$_POST['id_pda'].', "'.$ordEC.'")';
				if ( strpos($ordEC, '_') > 0 )
				{
					$expl_multi = explode('_', $ordEC);
					foreach($expl_multi as $multi)
					{
						// Vérification que la commande est bien en statut en cours paiement accepté
						$req_v = 'SELECT current_state FROM ps_orders WHERE id_order = "'.$multi.'";';
						$resu_v = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_v);
						if ( $resu_v[0]['current_state'] != 2 )
						{
							$exeOK = false;
						}
						if ( !empty($rows2) )
						{
							$rows2 .= ',';
						}
						$rows2 .= '('.$multi.', '.$operateur.', '.$caisseLV.', "0000-00-00 00:00:00", "0000-00-00 00:00:00", 0, "", 0)';
						$caisseLV++;
					}
				}
				else 
				{
					// Vérification que la commande est bien en statut en cours paiement accepté
					$req_v = 'SELECT current_state FROM ps_orders WHERE id_order = "'.$ordEC.'";';
					$resu_v = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_v);
					if ( $resu_v[0]['current_state'] != 2 )
					{
						$exeOK = false;
					}
					if ( !empty($rows2) )
					{
						$rows2 .= ',';
					}
					$rows2 .= '('.$ordEC.', '.$operateur.', '.$caisseLV.', "0000-00-00 00:00:00", "0000-00-00 00:00:00", 0, "", 0)';
					$caisseLV++;
				}
            }

			if ( $exeOK == true )
			{
            $req = 'INSERT IGNORE INTO ps_LogiGraine_pda_order
                    (id_pda,id_order)
                    VALUES
                    '.$rows.';';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);

            $req2 = 'INSERT IGNORE INTO ps_LogiGraine_controle
                    (id_order, id_operateur, id_caisse, date_debut, date_fin, valide, zone, transport)
                    VALUES
                    '.$rows2.';';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2);

			foreach($_POST['orders'] as $ordEC2)
            {
				$expl_multi = explode('_', $ordEC2);
				/*$optimizer = new WarehouseTopologyOptimizer();
				$liste_produits = $optimizer->optimizeOrders($expl_multi);*/

					$liste_produits = CommandeLV::getGroups($expl_multi, true);

				/*error_log(print_r($expl_multi, true));
				error_log(print_r($liste_produits, true));
				error_log('ICI');*/
				$rows3 = '';
				$locationEC = '';
				$ordreEC = 0;
				foreach($liste_produits as $ordreProduits)
				{

					foreach($ordreProduits['sequence'] as $seq)
					{
						foreach($seq['items'] as $prod)
						{
							if ( !empty($rows3) )
							{
								$rows3 .= ',';
							}
							if ( $locationEC != $prod['location'])
							{
								$ordreEC++;
								$locationEC = $prod['location'];
							}
							$rows3 .= '('.$prod['order_id'].', "'.$prod['location'].'", "'.$prod['ean'].'", '.$prod['quantity'].', '.$ordreEC.')';
						}
					}
					if ( !empty($rows3) )
					{
					$req3 = 'INSERT IGNORE INTO ps_LogiGraine_controle_produit_ordre
					(id_order, location, ean, quantity, ordre)
					VALUES
					'.$rows3.';';
						//error_log($req3);
            		Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req3);
					}
				}
			}
        }
        }

        // Etiquettes EPSON
        $tab_order_attr = array();

		foreach ($_POST['orders'] as $comm) 
		{
			$commande = new Order($comm);
			$products_commande = $commande->getProductsDetail();
			foreach ($products_commande as $produit) 
			{
				$ref_prod_tab = explode('-', $produit['product_reference']);
				if(
					(substr($produit['product_reference'], 0, 3) == '0-0')
					|| (substr($produit['product_reference'], 0, 3) == '0-1')
					|| (substr($produit['product_reference'], 0, 3) == '0-2')
					|| (substr($produit['product_reference'], 0, 1) == '8')
				)
				{
					$tab_order_attr[] = $comm.'_'.$produit['product_id'].'_'.$produit['product_attribute_id'].'_'.$produit['product_quantity'];
				}
			}
		}

		if(count($tab_order_attr) > 0 )
		{
			$liste_comm_prod = implode('-', $tab_order_attr);
		} 
		else 
		{
			$liste_comm_prod = "";
		}

		$nb_etiq_tmp = explode('-', $liste_comm_prod);
		$nb_etiq = 0;
		foreach($nb_etiq_tmp as $tmpp)
		{
			$expltmp = explode('_', $tmpp);
			if(isset($expltmp[3])){
				$nb_etiq += $expltmp[3];
			}
		}
		// Limite à 30 commandes car il y a des erreurs lorsqu'il y a trop de commandes	
		if ( $nb_etiq > 30 )
		{
			$retour = 'https://www.labonnegraine.com/admin123/liens_etiq.php?zone='.$_POST['zone'];
		}
		else if ( !empty($liste_comm_prod) )
		{
			$retour = 'http://localhost/etiquettes/index_prod_test.php?l='.$liste_comm_prod;
		}
        echo $retour;
    }
?>