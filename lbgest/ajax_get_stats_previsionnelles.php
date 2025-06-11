<?php

if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}
include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';
include_once 'util.php';

try {
       $bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
       die("probleme connexion serveur" . $ex->getMessage());
}

if(!isset($_GET['idp'])){
	die;
}

$id_product = (int)$_GET['idp'];
$id_product = pSQL($id_product);

$croissance = 0;
$res = Db::getInstance()->ExecuteS('SELECT croissance FROM conditionnement WHERE id = 1;');
foreach ($res as $r){
  $croissance = floatval($r['croissance']);
  $croissance = pSQL($croissance);
  break;
}

$html='
<table class="table-stats-previsionnelles" style="border: 0; cellspacing: 0;">
	<thead>
		<tr>
			<th>
				<span class="title_box  active">Conditionnement</span>
			</th>
			<th>
				<span class="title_box  active">Nb sachets vendus</span>
			</th>
			<th>
				<span class="title_box  active">Nb de sachets restants</span>
			</th>
			<th>
				<span class="title_box  active">Nb de sachets à produire</span>
			</th>
			<th>
				<span class="title_box  active">Stock tampon</span>
			</th>
			<th>
				<span class="title_box  active">% du besoin couvert</span>
			</th>
		</tr>
	</thead>
	<tbody>';


		
		$declinaisons = get_declinaison($id_product);

		foreach ($declinaisons as $dec_prod) {
			if($dec_prod['name'] != 'Non traitée' && $dec_prod['name'] != 'BIO'){
				$tab_dec[$dec_prod['id_product']][$dec_prod['id_product_attribute']] = $dec_prod['name'];
			}
			/*echo '<pre>';
print_r($dec_prod);
echo '</pre>';*/
			//$aa = get_quantity($dec_prod['id_product'], $dec_prod['id_product_attribute']);
			//$tab_qty[$dec_prod['id_product']][$dec_prod['id_product_attribute']] = $aa;
			$tab_qty[$dec_prod['id_product']][$dec_prod['id_product_attribute']] = $dec_prod['qte'];

		}
	

		if($croissance > 1){

			if(is_array($tab_dec[$id_product])){
				foreach ($tab_dec[$id_product] as $key => $value) {
					
					$nb_sachet_vendu = getNb_achat($id_product, $key);
					//$nb_quantite_restant = $tab_qty[$id_product][$key]['valeur'];
					$nb_quantite_restant = $tab_qty[$id_product][$key];

					$inv = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "' . $key . '" AND id_product = "' . $id_product . '" ORDER BY date DESC LIMIT 0,1;');


						$jour_inv = substr($inv[0]['date'], 6, 2);
						$mois_inv = substr($inv[0]['date'], 4, 2);
						$annee_inv = substr($inv[0]['date'], 0, 4);
						$heure_inv = substr($inv[0]['date'], 8, 2);
						$minutes_inv = substr($inv[0]['date'], 10, 2);


					$commandes = Db::getInstance()->ExecuteS('SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "' . $id_product . '" AND pod.product_attribute_id = "' . $key . '" AND po.date_add > "' . $annee_inv . '-' . $mois_inv . '-' . $jour_inv . ' ' . $heure_inv . ':' . $minutes_inv . '" AND (SELECT logable FROM ps_order_state WHERE id_order_state LIKE (SELECT id_order_state FROM ps_order_history WHERE id_order = po.id_order ORDER BY date_add DESC LIMIT 0,1)) LIKE 1;');

					foreach ($commandes AS $commande)
					{
					  //$nb_quantite_restant -= $commande['product_quantity'];
					}
												if ( $nb_quantite_restant < 0 )
												{
													$nb_quantite_restant = 0;
												}

					$nb_sachet_a_produire = ($nb_sachet_vendu*( ($croissance/100) + 1 ) )-$nb_quantite_restant;
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
					if($couvert_besoin >= 100){
						$couleur = '#fff';
					}elseif ($couvert_besoin >= 75) {
						$couleur = '#1aaf64';
					}elseif ($couvert_besoin >= 50) {
						$couleur = '#FFFF00';
					}elseif ($couvert_besoin >= 25) {
						$couleur = '#FF8000';
					}else{
						$couleur = '#FE2E2E';
					}

					$qt_reassort = 0;
					$stock_theorique_tamp = 0;
					$inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "0" AND id_product = "' . $id_product . '" ORDER BY date DESC LIMIT 0,1;');

					if (!empty($inv_tamp[0]['date']))
					{
						$jour_inv_tamp = substr($inv_tamp[0]['date'], 6, 2);
						$mois_inv_tamp = substr($inv_tamp[0]['date'], 4, 2);
						$annee_inv_tamp = substr($inv_tamp[0]['date'], 0, 4);
						$heure_inv_tamp = substr($inv_tamp[0]['date'], 8, 2);
						$minutes_inv_tamp = substr($inv_tamp[0]['date'], 10, 2);

						$der_inv_tamp = true;
					}
					else
					{
						$der_inv_tamp = false;
					}

					if ($der_inv_tamp == true)
					{
						// Somme des quantités commandées depuis le dernier inventaire
						$reassorts = Db::getInstance()->ExecuteS('SELECT * FROM ps_reassort WHERE id_product = "' . $id_product . '" AND id_product_attribute = "0" AND date > "' . $annee_inv_tamp . $mois_inv_tamp . $jour_inv_tamp . $heure_inv_tamp . $minutes_inv_tamp . '";');

						foreach ($reassorts AS $reassort)
						{
							$qt_reassort += $reassort['valeur'];
						}
						$stock_theorique_tamp = $inv_tamp[0]['valeur'] + $qt_reassort;
					}

	

					$html .= '
					<tr>
						<td>'.$value.'</td>
						<td>'.$nb_sachet_vendu.'</td>
						<td>'.$nb_quantite_restant.'</td>
						<td>'.$nb_sachet_a_produire.'</td>
						<td>'.$stock_theorique_tamp.'</td>
						<td style="background:'.$couleur.'; color:#000;">'.$couvert_besoin.' %</td>
					</tr>';
					
				}
			}
		}


		$html .= '
	</tbody>
</table>';

echo $html;   

function get_quantity($id_product, $id_attr){
	$sql = 'SELECT *  FROM `ps_inventaire` WHERE `id_product` = "'.$id_product.'" AND `id_product_attribute` = "'.$id_attr.'" ORDER BY date DESC ';

	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	return $res[0];
}

function getNb_achat($id_product, $id_prod_attr){

	$now = date("Y-m-d H:i:s");
	$d1a = new DateTime($now);
	$d1a->sub(new DateInterval('P1Y')); // moins un an
	$moins = $d1a->format('Y-m-d H:i:s');

	$sql = 'SELECT product_quantity FROM `ps_order_detail` AS od
	LEFT JOIN `ps_orders` AS o ON od.id_order = o.id_order
	WHERE od.product_id = '.$id_product.' AND od.product_attribute_id = '.$id_prod_attr.' AND o.invoice_date > "'.$moins.'" AND o.invoice_date < "'.$now.'" ';

	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	$nb = 0;

	foreach ($res as $value) {
		$nb = $nb + $value['product_quantity'];
	}

	return $nb;
}
	
?>
