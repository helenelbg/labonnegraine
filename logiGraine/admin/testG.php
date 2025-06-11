<?php
require 'application_top.php';
$expl_multi = array(243597,243602,243607);
$liste_produits = Commande::getGroups($expl_multi);

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
                    echo $req3;
					//error_log($req3);
            		//Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req3);
					}
				}