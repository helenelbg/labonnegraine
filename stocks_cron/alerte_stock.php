<?php

	// exit car Stéphane de LBG m'a dit qu'on ne s'en sert plus
	exit;

	include('../config/config.inc.php');
	include('../init.php');
	$message_erreur = "";
    $db = Db::getInstance();
 	$products = $db->ExecuteS("SELECT DISTINCT ps_alerte.id_product, ps_alerte.valeur FROM ps_alerte, ps_product_lang WHERE ps_product_lang.id_product = ps_alerte.id_product AND ps_product_lang.id_lang = 2 ORDER BY ps_product_lang.name;");
 	foreach($products as $product)
 	{
		 $pas_inventaire = 0;
		 $query_test_active_product = "SELECT count(*) as cpt FROM ps_product WHERE active = 1 AND id_product=".$product['id_product'].";";
         $test_active = Db::getInstance()->ExecuteS($query_test_active_product);
         if($test_active[0]['cpt'] >="1")
         {
			 //Récupération du stock tampon
			 $inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "0" AND id_product = "'.$product['id_product'].'" ORDER BY date DESC LIMIT 0,1;');
			 if(isset($inv_tamp['valeur']))
			 {
			    $stock_tampon =  $inv_tamp['valeur'];
			 }
			 else
	            $stock_tampon="";
	
	         //Récupération des attributs
			 $rangee_attrib = Db::getInstance()->ExecuteS('SELECT * FROM ps_product_attribute WHERE id_product = "'.$product['id_product'].'";');
			 $poids_theorique = 0;
				 foreach ($rangee_attrib AS $attrib)
				 {
		                    $aux_dec = array();
		                    $qt_commandee = 0;
		                    $stock_theorique = 0;
		                    $rangee_comb = Db::getInstance()->ExecuteS('SELECT * FROM ps_product_attribute_combination WHERE id_product_attribute = "'.$attrib['id_product_attribute'].'";');
		                    foreach ($rangee_comb AS $comb)
			        		{
		                            //echo 'SELECT * FROM ps_attribute_lang WHERE id_attribute = "'.$comb['id_attribute'].'" AND id_lang = 2;';
		                            $dec = Db::getInstance()->ExecuteS('SELECT name FROM ps_attribute_lang WHERE id_attribute = "'.$comb['id_attribute'].'" AND id_lang = 2 LIMIT 0,1;');
		                            $aux_dec[] = $dec[0]['name'];
		                    }
		                    sort($aux_dec);
		                    $libelle_dec = implode(' - ', $aux_dec);
		                    $inv = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "'.$attrib['id_product_attribute'].'" AND id_product = "'.$product['id_product'].'" ORDER BY date DESC LIMIT 0,1;');
                                    /*echo "<pre>";
                                    print_r($inv);
                                    echo "</pre>";*/
		                    if ( !empty($inv[0]['date']) )
		                    {
		                       $jour_inv = substr($inv[0]['date'], 6, 2);
		                       $mois_inv = substr($inv[0]['date'], 4, 2);
		                       $annee_inv = substr($inv[0]['date'], 0, 4);
		                       $heure_inv = substr($inv[0]['date'], 8, 2);
		                       $minutes_inv = substr($inv[0]['date'], 10, 2);
		                    }
	
		                    // Somme des quantités commandées depuis de dernier inventaire
		                    $commandes = Db::getInstance()->ExecuteS('SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.valid = 1 AND po.id_order = pod.id_order AND pod.product_id = "'.$product['id_product'].'" AND pod.product_attribute_id = "'.$attrib['id_product_attribute'].'" AND po.date_add > "'.$annee_inv.'-'.$mois_inv.'-'.$jour_inv.' '.$heure_inv.':'.$minutes_inv.'";');
                                    //echo 'SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.valid = 1 AND po.id_order = pod.id_order AND pod.product_id = "'.$product['id_product'].'" AND pod.product_attribute_id = "'.$attrib['id_product_attribute'].'" AND po.date_add > "'.$annee_inv.'-'.$mois_inv.'-'.$jour_inv.' '.$heure_inv.':'.$minutes_inv.'";';
		                    foreach ($commandes AS $commande)
		    			    {
		                        $qt_commandee += $commande['product_quantity'];
		                    }
							if(isset( $inv[0]['valeur']))
							{
		                        $stock_theorique = $inv[0]['valeur'] - $qt_commandee;
		                        $poids_theorique += $stock_theorique * $attrib['weight'];
                                        //echo $poids_theorique ."+= " . $stock_theorique."*".$attrib['weight']."<br />";
		     				}
		     				else
		     				{
								$pas_inventaire ++;
							}
		   			}
		   			$qt_reassort = 0;
	                $stock_theorique_tamp = 0;
	                $inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "0" AND id_product = "'.$product['id_product'].'" ORDER BY date DESC LIMIT 0,1;');
	                    if ( !empty($inv_tamp[0]['date']) )
	                    {
	                       $jour_inv_tamp = substr($inv_tamp[0]['date'], 6, 2);
	                       $mois_inv_tamp = substr($inv_tamp[0]['date'], 4, 2);
	                       $annee_inv_tamp = substr($inv_tamp[0]['date'], 0, 4);
	                       $heure_inv_tamp = substr($inv_tamp[0]['date'], 8, 2);
	                       $minutes_inv_tamp = substr($inv_tamp[0]['date'], 10, 2);
	                    }
                            /*echo "<pre>";
                            print_r($inv_tamp);
                            echo "</pre>";*/
	                    // Somme des quantités commandées depuis de dernier inventaire
	                    $reassorts = Db::getInstance()->ExecuteS('SELECT * FROM ps_reassort WHERE id_product = "'.$product['id_product'].'" AND id_product_attribute = "0" AND date > "'.$annee_inv_tamp.$mois_inv_tamp.$jour_inv_tamp.$heure_inv_tamp.$minutes_inv_tamp.'";');
	                     foreach ($reassorts AS $reassort)
	    			     {
	                        $qt_reassort += $reassort['valeur'];
	                     }
                             
	                     $stock_theorique_tamp = $inv_tamp[0]['valeur'] + $qt_reassort;
                               
						 $stock_theorique_theo = (($poids_theorique*1000)+$stock_theorique_tamp);
                                                
			             if(floatval($stock_theorique_theo)<=floatval($product['valeur']) && $pas_inventaire!=count($rangee_attrib))
			             {
						 	$query = 'SELECT name FROM ps_product_lang WHERE id_product = '.$product['id_product'].' AND id_lang=2;';
						 	//echo $query;
						 	$rangee_nom_produit =  Db::getInstance()->ExecuteS($query);
			                $message_erreur .=$rangee_nom_produit[0]['name']." => ".$stock_theorique_theo."gr. en stock<br />";
			             }
	   		}
	 }  		
	//Envoi du mail récapitulatif
	$donnees = array('{message_erreur}'  => $message_erreur );
	
	$email = 'info@labonnegraine.com';

	/*echo "<pre>";
	print_r($donnees);
	echo "</pre>";*/
	Mail::Send(2, 'stocks', 'Alerte stock' ,$donnees , $email);
	
	echo 'ok';
?>