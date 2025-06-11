<?php

if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}
include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

try {
	$bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
	die("probleme connexion serveur" . $ex->getMessage());
}

$tableau_final = array();
$tableau_final_appro = array();
$tableau_final_germ = array();

// On récupère le coefficient de croissance depuis la BDD
$croissance = 20;
$res = Db::getInstance()->ExecuteS('SELECT croissance FROM conditionnement WHERE id = 1;');
foreach ($res as $r){
	$croissance = floatval($r['croissance']);
}
$res_appro = Db::getInstance()->ExecuteS('SELECT croissance FROM conditionnement WHERE id = 2;');
foreach ($res_appro as $r_appro){
	$croissance_appro = floatval($r['croissance']);
}

$date_moins = date('d/m').'/'.(date('Y')-1);
$date_plus = date('d/m/Y');
$tab_stockf = [];

foreach (getProducts2(1) as $product)
{
	$declinaisons = get_declinaison($product['id_product']);

	foreach ($declinaisons as $dec_prod) {
		if($dec_prod['name'] != 'Non traitée' && $dec_prod['name'] != 'BIO' && trim($dec_prod['name']) != 'plant'){
			$tab_dec[$dec_prod['id_product']][$dec_prod['id_product_attribute']] = $dec_prod['name'];
			$dec_prod['name'] = str_replace('Par ', '', $dec_prod['name']);
			$exp = explode(' ', $dec_prod['name']);
			$exp[0] = str_replace(',', '.', $exp[0]);
			if (isset($exp[1]) && strtolower($exp[1]) == 'kg' )
			{
				$tab_decqt[$dec_prod['id_product']][$dec_prod['id_product_attribute']] = $exp[0] * 1000;
			}
			else
			{
				if ( isset($exp[0]) )
				{
					$tab_decqt[$dec_prod['id_product']][$dec_prod['id_product_attribute']] = $exp[0];
				}
				else
				{
					$tab_decqt[$dec_prod['id_product']][$dec_prod['id_product_attribute']] = $exp;
				}
			}
			echo 'strtolower($exp[1]) : '.strtolower($exp[1]).'<br />';
			if (isset($exp[1]) && strtolower($exp[1]) == 'graines' )
			{
			  $tab_unite[$dec_prod['id_product']] = 'graines';
			}
			else {
			  $tab_unite[$dec_prod['id_product']] = 'g';
			}

			if ( $dec_prod['default_on'] == 1 )
			{
			  $tab_stockd[$dec_prod['id_product']] = $dec_prod['qte'];
			  $tab_stockdp[$dec_prod['id_product']] = $dec_prod['poids'];
			}
			if(!isset($tab_stockf[$dec_prod['id_product']]))
			{
				$tab_stockf[$dec_prod['id_product']] = 0;
			}
			
			if (isset($exp[1]) && strtolower($exp[1]) != 'graines' )
			{
			  $tab_stockf[$dec_prod['id_product']] += (int)$dec_prod['qte'] * (float)$dec_prod['poids'];
			}
			else
			{
			  $tab_stockf[$dec_prod['id_product']] += (int)$dec_prod['qte'] * (float)$tab_decqt[$dec_prod['id_product']][$dec_prod['id_product_attribute']];
			}
		}

		$aa = get_quantity($dec_prod['id_product'], $dec_prod['id_product_attribute']);
		$tab_qty[$dec_prod['id_product']][$dec_prod['id_product_attribute']] = $aa;

	}
}
echo '<pre>';
print_r($tab_qty);
echo '</pre>';
echo '<pre>';
print_r($tab_stockf);
echo '</pre>';
echo '<pre>';
print_r($tab_stockd);
echo '</pre>';
echo '<pre>';
print_r($tab_stockdp);
echo '</pre>';

foreach (getProducts2(1) as $product){

	$cpt = 0;

	$requeteG = $bdd->prepare('SELECT * FROM `ps_inventaire_lots` il LEFT JOIN AW_test_lots tl ON (il.id_inventaire_lots=tl.id_lot) WHERE il.id_product = '.$product['id_product'].' ORDER BY `il`.`id_inventaire_lots`  DESC, tl.date_fin_test DESC, tl.id DESC LIMIT 0, 1;');
	$requeteG->execute() or die(print_r($requeteG->errorInfo()));
	$ligneG = $requeteG->fetch();

	/*if(empty($ligneG)){
		continue;
	}
	
	if(!$ligneG['quantite']){
		$ligneG['quantite'] = 0;
	}*/

	if(is_array($tab_dec[$product['id_product']])){
		foreach ($tab_dec[$product['id_product']] as $key => $value) {
			


            /*if ( $product['reference'] == '1-369' )
            {
                echo 'DEBUT 2<br />';
            }*/
		
			$cpt++;

			$nb_sachet_vendu = getNb_achat($product['id_product'], $key);

			/*if ( $product['reference'] == '1-369' )
            {
                echo '$nb_sachet_vendu : '.$nb_sachet_vendu.'<br />';
            }*/


			/*if ( $product['reference'] == '1-369' )
                    {
                        echo 'nb_quantite_restant : '.$nb_quantite_restant.'<br />';
                    }*/

			
					
                    
			$id_stock_presta = StockAvailable::getStockAvailableIdByProductId($product['id_product'], $key);
			$stockAvailable = new StockAvailable($id_stock_presta);
			$nb_quantite_restant = $stockAvailable->quantity;

			if ( $nb_quantite_restant < 0 )
			{
				$nb_quantite_restant = 0;
			}

			$nb_sachet_a_produire = ($nb_sachet_vendu*( ($croissance/100) + 1 ) )-$nb_quantite_restant;
			$nb_sachet_a_produire_appro = ($nb_sachet_vendu*( ($croissance_appro/100) + 1 ) )-$nb_quantite_restant;

			$nb_sachet_a_produire = ceil($nb_sachet_a_produire);
			if($nb_sachet_a_produire < 0){
				$nb_sachet_a_produire = 0;
			}
			if($nb_sachet_vendu > 0){
				$couvert_besoin = ($nb_quantite_restant*100)/($nb_sachet_vendu*( ($croissance/100) + 1 ) );
			}else{
				$couvert_besoin = 100;
			}
			$couvert_besoin = round($couvert_besoin, 2);

			$qt_reassort = 0;
			$stock_theorique_tamp = 0;
			$inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "0" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');

			if (!empty($inv_tamp[0]['date']))
			{
				$jour_inv_tamp = substr($inv_tamp[0]['date'], 6, 2);
				$mois_inv_tamp = substr($inv_tamp[0]['date'], 4, 2);
				$annee_inv_tamp = substr($inv_tamp[0]['date'], 0, 4);
				$heure_inv_tamp = substr($inv_tamp[0]['date'], 8, 2);
				$minutes_inv_tamp = substr($inv_tamp[0]['date'], 10, 2);

				$der_inv_tamp = true;
			}
			else{
				$der_inv_tamp = false;
			}

			if ($der_inv_tamp == true){
				// Somme des quantités commandées depuis de dernier inventaire
				$reassorts = Db::getInstance()->ExecuteS('SELECT * FROM ps_reassort WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "0" AND date > "' . $annee_inv_tamp . $mois_inv_tamp . $jour_inv_tamp . $heure_inv_tamp . $minutes_inv_tamp . '";');

				foreach ($reassorts AS $reassort){
					$qt_reassort += $reassort['valeur'];
				}
				$stock_theorique_tamp = $inv_tamp[0]['valeur'] + $qt_reassort;
			}

			if ( isset($tab_decqt[$product['id_product']][$key])){
				$nb_stock = (int)$tab_decqt[$product['id_product']][$key];
				if ( $nb_stock > 0) {
					$paquet_tampon = floor($stock_theorique_tamp / $nb_stock);
					$besoin_annuel_30pc = floor(($croissance * $nb_sachet_a_produire) / 100);


					$poids_total = $stock_theorique_tamp + $tab_stockf[$product['id_product']];
					$quantite_en_stock = $poids_total;
					/*if ( $product['reference'] == '1-369' )
					{
						echo 'stock_theorique_tamp : '.$stock_theorique_tamp.'<br />';
						echo '$tab_stockf[$product[id_product]] : '.$tab_stockf[$product['id_product']].'<br />';
							echo 'quantite_en_stock : '.$quantite_en_stock.'<br />';
					}*/
					if($tab_unite[$product['id_product']] != 'gramme' && $tab_unite[$product['id_product']] != 'graines'){
						$poids_total = $stock_theorique_tamp + $tab_stockf[$product['id_product']] * 1000;
						$quantite_en_stock = $poids_total;
					}

					echo '$tab_unite[$product[id_product]] : '.$tab_unite[$product['id_product']].'<br />' ;

					echo '$stock_theorique_tamp : '.$stock_theorique_tamp.'<br />';
					echo '$tab_stockf[$product[id_product]] : '.$tab_stockf[$product['id_product']].'<br />';

					$value_float = preg_replace("/[^0-9.]/", "", $value); 

					/*if ( $product['reference'] == '1-369' )
          {
                  echo '$tab_unite[$product[id_product]] : '.$tab_unite[$product['id_product']].'<br />';
                          echo 'quantite_en_stock2 : '.$quantite_en_stock.'<br />';
						  echo 'declinaison = '.addslashes($value_float).'<br />';
          }*/

					$poids_min = $tab_stockf[$product['id_product']] * 10;
					$quantite_vendue_annee_n_moins_1 = $nb_sachet_vendu * $tab_decqt[$product['id_product']][$key];

					$tableau_final_appro[] = 'INSERT INTO appro SET id_product = "'.$product['id_product'].'", id_product_attribute = "'.$key.'", reference = "'.$product['reference'].'", nom = "'.addslashes($product['name']).'", declinaison = "'.addslashes($value_float).'", nb_sachet_a_produire = "'.$nb_sachet_a_produire_appro.'", stock_theorique_tamp = "'.$stock_theorique_tamp.'", quantite_en_stock = "'.$quantite_en_stock.'", quantite_vendue_annee_n_moins_1 = "'.$quantite_vendue_annee_n_moins_1.'";';


					$ligneG2 = '';
					$ligneG3 = '';
					// Si stock total > quantite dernier lot

					echo '$poids_total : '.$poids_total.'<br />';
					echo '$ligneG[quantite] : '.$ligneG['quantite'].'<br />';

					if ( $poids_total > @$ligneG['quantite'] ){
						if(!isset($ligneG['id_lot'])){
							$ligneG['id_lot'] = 0;
						}
						$requeteG2 = $bdd->prepare('SELECT * FROM `ps_inventaire_lots` il LEFT JOIN AW_test_lots tl ON (il.id_inventaire_lots=tl.id_lot) WHERE il.id_product = '.$product['id_product'].' AND tl.id_lot <> "'.$ligneG['id_lot'].'" ORDER BY `il`.`id_inventaire_lots`  DESC, tl.date_fin_test DESC LIMIT 0, 1;');
						$requeteG2->execute() or die(print_r($requeteG2->errorInfo()));
						$ligneG2 = $requeteG2->fetch();
						
						if(empty($ligneG2)){
							$ligneG2 = [];
							$ligneG2['id_lot'] = "";
							$ligneG2['quantite'] = 0;
							$ligneG2['origine_test'] = "";
							$ligneG2['numero_lot_LBG'] = "";
						}
						
						if(!isset($ligneG['quantite'])){
							$ligneG['quantite'] = 0;
						}
						if(!isset($ligneG2['quantite'])){
							$ligneG2['quantite'] = 0;
						}
						$qt_lot = $ligneG['quantite'] + $ligneG2['quantite'];
						if ( $poids_total > $qt_lot )
						{
							if(!isset($ligneG2['id_lot'])){
								$ligneG2['id_lot'] = 0;
							}
							$requeteG3 = $bdd->prepare('SELECT * FROM `ps_inventaire_lots` il LEFT JOIN AW_test_lots tl ON (il.id_inventaire_lots=tl.id_lot) WHERE il.id_product = '.$product['id_product'].' AND tl.id_lot <> "'.$ligneG['id_lot'].'" AND tl.id_lot <> "'.$ligneG2['id_lot'].'" ORDER BY `il`.`id_inventaire_lots`  DESC, tl.date_fin_test DESC LIMIT 0, 1;');

							$requeteG3->execute() or die(print_r($requeteG3->errorInfo()));
							$ligneG3 = $requeteG3->fetch();
						}
					}
					
					/*if(!$ligneG2){
						$ligneG2 = [];
						$ligneG2['id_lot'] = "";
						$ligneG2['quantite'] = 0;
						$ligneG2['origine_test'] = "";
						$ligneG2['numero_lot_LBG'] = "";
					}

					if(!$ligneG3){
						$ligneG3 = [];
						$ligneG3['id_lot'] = "";
						$ligneG3['origine_test'] = "";
						$ligneG3['numero_lot_LBG'] = "";
					}*/
					
					$date_germination = '0000-00-00';
					$resultat_germination = '0';
					$lot_germination = @$ligneG['numero_lot_LBG'];
					$id_test = 0;
					if ( @$ligneG['origine_test'] == 'LBG' ){
					  $id_test = $ligneG['id'];
					  $date_germination = $ligneG['date_fin_test'];
					  $resultat_germination = $ligneG['pourcentage_germ'];
					}
				  

					$date_germination2 = '0000-00-00';
					$resultat_germination2 = '0';
					$lot_germination2 = '';
					$id_test2 = 0;
					if ( !empty($ligneG2))
                    {
					if ( $ligneG2['origine_test'] == 'LBG' ){
						$id_test2 = $ligneG2['id'];
						$date_germination2 = $ligneG2['date_fin_test'];
						$resultat_germination2 = $ligneG2['pourcentage_germ'];
					}
					$lot_germination2 = $ligneG2['numero_lot_LBG'];
				}

					$date_germination3 = '0000-00-00';
					$resultat_germination3 = '0';
					$lot_germination3 = '';
					$id_test3 = 0;
					if ( !empty($ligneG3))
                    {
					if ( $ligneG3['origine_test'] == 'LBG' ){
						$id_test3 = $ligneG3['id'];
						$date_germination3 = $ligneG3['date_fin_test'];
						$resultat_germination3 = $ligneG3['pourcentage_germ'];
					}
					$lot_germination3 = $ligneG3['numero_lot_LBG'];
				}	   
				



						
					// On récupère nb_mois_germination depuis la BDD
					$nb_mois_germination = 8; // défaut = 8 mois
					$res = Db::getInstance()->ExecuteS('SELECT nb_mois_germination FROM conditionnement WHERE id = 1;');
					foreach ($res as $r){
						$nb_mois_germination = intval($r['nb_mois_germination']);
					}
	
					$date_germination_mini = date('Y-m-d', strtotime('-8 months')); // défaut = 8 mois
					if($nb_mois_germination){
						$date_germination_mini = date('Y-m-d', strtotime('-'.$nb_mois_germination.' months'));
					}
							
						//if ( !isset($tableau_final_germ[$product['id_product']]) && ($poids_total > $poids_min) )
						if ( !isset($tableau_final_germ[$product['id_product']]) )
						{
							$requete_cat = $bdd->prepare('SELECT * FROM `germination_normes` gn WHERE id_categorie IN (SELECT id_category FROM ps_category_product WHERE id_product = "'.$product['id_product'].'");');
							$requete_cat->execute() or die(print_r($requete_cat->errorInfo()));
							$cat = $requete_cat->fetch();
	
							if(empty($cat)){
								$cat['id_categorie'] = 0;
								$cat['minimum'] = 0;
								$cat['optimum'] = 0;
							}
							
							if ($poids_total > $poids_min || ($poids_total > 0 && $date_germination < $date_germination_mini) )
							{
								$tableau_final_germ[$product['id_product']][] = array('reference' => $product['reference'], 'nom' => $product['name'], 'lot_germination' => $lot_germination, 'id_test' => $id_test, 'germination' => $resultat_germination, 'date_germination' => $date_germination, 'id_cat' => $cat['id_categorie'], 'minimum' => $cat['minimum'], 'optimum' => $cat['optimum']);
							}
						
							if ( !empty($lot_germination2) && ($poids_total > $poids_min || ($poids_total > 0 && $date_germination2 < $date_germination_mini)) )
							{
							  $tableau_final_germ[$product['id_product']][] = array('reference' => $product['reference'], 'nom' => $product['name'], 'lot_germination' => $lot_germination2, 'id_test' => $id_test2, 'germination' => $resultat_germination2, 'date_germination' => $date_germination2, 'id_cat' => $cat['id_categorie'], 'minimum' => $cat['minimum'], 'optimum' => $cat['optimum']);
							}
	
							if ( !empty($lot_germination3) && ($poids_total > $poids_min || ($poids_total > 0 && $date_germination3 < $date_germination_mini)) )
							{
							  $tableau_final_germ[$product['id_product']][] = array('reference' => $product['reference'], 'nom' => $product['name'], 'lot_germination' => $lot_germination3, 'id_test' => $id_test3, 'germination' => $resultat_germination3, 'date_germination' => $date_germination3, 'id_cat' => $cat['id_categorie'], 'minimum' => $cat['minimum'], 'optimum' => $cat['optimum']);
							}
						}

					//$stockd = 0;
					/*if(isset($tab_stockd[$product['id_product']])){
						$stockd = $tab_stockd[$product['id_product']];
					}*/

					/*if ( $product['reference'] == '1-369' )
                        {
							echo '<hr>';
                            echo 'id_product : '.$product['id_product'].'<br />';
                            echo 'reference : '.$product['reference'].'<br />';
                            echo '$paquet_tampon : '.$paquet_tampon.'<br />';
                            echo '$besoin_annuel_30pc : '.$besoin_annuel_30pc.'<br />';
                            echo '$nb_sachet_a_produire : '.$nb_sachet_a_produire.'<br />';
                            echo 'stock : '.$tab_stockd[$product['id_product']].'<br />';
							
							echo '<hr>';
						}*/

					if(!isset($tab_stockd[$product['id_product']])){
						$tab_stockd[$product['id_product']] = 0;
					}
					if ( ( ($paquet_tampon > $besoin_annuel_30pc) && ( ($nb_sachet_a_produire > 50) || ($nb_sachet_a_produire > 0 && $paquet_tampon >= $nb_sachet_a_produire) ) ) || ( $tab_stockd[$product['id_product']] == 0 && $paquet_tampon > 0 ) )
					{
						/*if ( $product['reference'] == '1-369' )
						{
							echo 'DANS LE TABLEAU';
						}*/

						$tableau_final[$tab_decqt[$product['id_product']][$key]][] = array('id_product' => $product['id_product'], 'id_product_attribute' => $key, 'reference' => $product['reference'], 'nom' => $product['name'], 'declinaison' => $value, 'nb_sachet_vendu' => $nb_sachet_vendu, 'nb_quantite_restant' => $nb_quantite_restant, 'nb_sachet_a_produire' => $nb_sachet_a_produire, 'stock_theorique_tamp' => $stock_theorique_tamp, 'couvert_besoin' => $couvert_besoin, 'lot_germination' => $lot_germination, 'germination' => $resultat_germination, 'date_germination' => $date_germination, 'lot_germination2' => $lot_germination2, 'germination2' => $resultat_germination2, 'date_germination2' => $date_germination2, 'lot_germination3' => $lot_germination3, 'germination3' => $resultat_germination3, 'date_germination3' => $date_germination3, 'ordre' => $cpt);

					}
				}
		
			}
	   
		} // end foreach attribute

	}
}

echo '<pre>';
	print_r($tableau_final);
echo '</pre>';
echo '<pre>';
	print_r($tableau_final_germ);
echo '</pre>';


/*$requeteTA = $bdd->prepare('TRUNCATE TABLE `appro`;');
$requeteTA->execute() or die(print_r($requeteTA->errorInfo()));

$requeteTg = $bdd->prepare('TRUNCATE TABLE `germination`;');
$requeteTg->execute() or die(print_r($requeteTg->errorInfo()));

foreach($tableau_final_appro as $reqappro)
{
	$sqlAppro = $bdd->prepare($reqappro);
	$sqlAppro->execute() or die(print_r($sqlAppro->errorInfo()));
}

$requeteT = $bdd->prepare('TRUNCATE TABLE `operationnel`;');
$requeteT->execute() or die(print_r($requeteT->errorInfo()));

foreach($tableau_final as $decli => $liste) {
	foreach($liste as $prodEC) {

		if($prodEC['couvert_besoin'] >= 100){
			$priorite = 5;
		}elseif ($prodEC['couvert_besoin'] >= 75) {
			$priorite = 4;
		}elseif ($prodEC['couvert_besoin'] >= 50) {
			$priorite = 3;
		}elseif ($prodEC['couvert_besoin'] >= 25) {
			$priorite = 2;
		}else{
			$priorite = 1;
		}
		
		// Supprime les caractères non numériques. Par exemple, 2 g devient 2.
		$decli = preg_replace("/[^0-9.]/", "", $decli); 
	 
		$sql = $bdd->prepare('INSERT INTO operationnel SET id_product = "'.$prodEC['id_product'].'", id_product_attribute = "'.$prodEC['id_product_attribute'].'", reference = "'.$prodEC['reference'].'", nom = "'.addslashes($prodEC['nom']).'", declinaison = "'.addslashes($decli).'", unite = "'.$tab_unite[$prodEC['id_product']].'", nb_quantite_restant = "'.$prodEC['nb_quantite_restant'].'", nb_sachet_a_produire = "'.$prodEC['nb_sachet_a_produire'].'", stock_theorique_tamp = "'.$prodEC['stock_theorique_tamp'].'", couvert_besoin = "'.$prodEC['couvert_besoin'].'", lot_germination = "'.$prodEC['lot_germination'].'", germination = "'.$prodEC['germination'].'", date_germination = "'.$prodEC['date_germination'].'", lot_germination2 = "'.$prodEC['lot_germination2'].'", germination2 = "'.$prodEC['germination2'].'", date_germination2 = "'.$prodEC['date_germination2'].'", lot_germination3 = "'.$prodEC['lot_germination3'].'", germination3 = "'.$prodEC['germination3'].'", date_germination3 = "'.$prodEC['date_germination3'].'", ordre = "'.$prodEC['ordre'].'", priorite = "'.$priorite.'";');

		$sql->execute() or die(print_r($sql->errorInfo()));

	}
}

foreach($tableau_final_germ as $id => $prodECtmp)
{
  foreach($prodECtmp as $ind => $prodEC)
  {
	$median = ($prodEC['optimum'] + $prodEC['minimum']) / 2;

	if ( $prodEC['germination'] >= $prodEC['optimum'] ) // VERT
	{
		$priorite = '3';
	}
	elseif ( $prodEC['germination'] >= $median ) // ORANGE
	{
		$priorite = '2';
	}
	elseif ( $prodEC['germination'] < $median ) // ROUGE
	{
		$priorite = '1';
	}

	$sql = $bdd->prepare('INSERT INTO germination SET id_product = "'.$id.'", reference = "'.$prodEC['reference'].'", nom = "'.addslashes($prodEC['nom']).'", lot_germination = "'.$prodEC['lot_germination'].'", id_test = "'.$prodEC['id_test'].'", germination = "'.$prodEC['germination'].'", date_germination = "'.$prodEC['date_germination'].'", id_categorie = "'.$prodEC['id_cat'].'", priorite = "'.$priorite.'";');



	$sql->execute() or die(print_r($sql->errorInfo()));
  }
}*/



function getProducts2($id_lang){
	$sql = 'SELECT p.`id_product` , p.reference, pl.`name` , IFNULL( stock.quantity, 0 ) AS quantity
		FROM  `ps_product` p
		LEFT JOIN ps_stock_available stock ON ( stock.id_product = p.id_product
		AND stock.id_product_attribute =0
		AND stock.id_shop =1 )
		LEFT JOIN  `ps_product_lang` pl ON p.`id_product` = pl.`id_product`
		AND pl.id_shop =1
		INNER JOIN ps_product_shop product_shop ON ( product_shop.id_product = p.id_product
		AND product_shop.id_shop =1 )
		WHERE p.id_product = 887 AND p.active  = 1 AND p.visibility <> "none" AND pl.`id_lang` = '.(int)$id_lang.' AND (p.reference LIKE "0-%" OR p.reference LIKE "1-%" OR p.reference LIKE "2-%" OR p.reference LIKE "3-%" OR p.reference LIKE "4-%") AND p.reference NOT LIKE "0-9%"
		AND p.id_category_default NOT IN (129,135,132,131,133,134,213,299,338)
		ORDER BY  `pl`.`name` ASC ';

	return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
}

function get_declinaison($id_product){
	// ids 10512, 10513 = plants en précommande
	$sql = 'SELECT *, sa.quantity as qte, pa.weight as poids FROM `ps_product_attribute` AS pa
	LEFT JOIN `ps_product_attribute_combination` AS pac ON pac.id_product_attribute = pa.id_product_attribute
	LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
	LEFT JOIN `ps_attribute_lang` AS al ON al.id_attribute = pac.id_attribute
	LEFT JOIN ps_stock_available sa ON sa.id_product_attribute = pa.id_product_attribute
	WHERE pa.id_product = '.$id_product.' 
	AND a.id_attribute NOT IN (10512,10513)
	AND a.id_attribute_group IN (6)
	AND al.id_lang = 1 
	ORDER BY pa.default_on DESC, a.position ASC';

	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	return $res;
}

function getNb_achat($id_product, $id_prod_attr){

	$moins = '';
	if(strlen($moins) < 1){
		$date_m = strtotime("-1 year");
		$moins = date('Y-m-d', $date_m);
	}
	$plus = '';
	if(strlen($plus) < 1){
		$plus = date('Y-m-d');
	}

	$sql = 'SELECT product_quantity FROM `ps_order_detail` AS od
	LEFT JOIN `ps_orders` AS o ON od.id_order = o.id_order
	WHERE od.product_id = '.$id_product.' AND od.product_attribute_id = '.$id_prod_attr.' AND o.invoice_date > "'.$moins.'" AND o.invoice_date < "'.$plus.'" ';

	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	$nb = 0;

	foreach ($res as $value) {
		$nb = $nb + $value['product_quantity'];
	}

	return $nb;
}

function get_quantity($id_product, $id_attr){
	$sql = 'SELECT *  FROM `ps_inventaire` WHERE `id_product` = "'.$id_product.'" AND `id_product_attribute` = "'.$id_attr.'" ORDER BY date DESC ';

	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	
	if(is_array($res) && count($res)){
		return $res[0];
	}
	return 0;
}


