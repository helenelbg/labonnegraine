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

$json = array();
$json['msg'] = '';

$erreur = array();

$sql = 'SELECT il.*, pp.reference, pl.name FROM ps_inventaire_lots il LEFT JOIN ps_product pp ON il.id_product = pp.id_product LEFT JOIN ps_product_lang pl ON il.id_product = pl.id_product AND pl.id_lang = 1 WHERE (date_approvisionnement LIKE "2023-%" OR date_approvisionnement LIKE "2024-%") AND il.id_product IN (SELECT p.id_product FROM ps_product p) order by pl.name ASC, il.id_inventaire_lots DESC';
$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
foreach($res as $rangee)
{
	$id_product = $rangee['id_product'];
	$declinaisons = get_declinaison($id_product);
	$unite_vente = "";
	foreach ($declinaisons as $dec_prod) {
		if($dec_prod['name'] != 'Non traitée' && $dec_prod['name'] != 'BIO'){
			$dec_prod['name'] = str_replace('Par ', '', $dec_prod['name']);
			$exp = explode(' ', $dec_prod['name']);                   
			if ( isset($exp[1]) && strtolower($exp[1]) == 'graines' ){
			  $unite_vente = 'graine';
              break;
			}
			else {
			  $unite_vente = 'gramme';
			}
		}
	}
    if ( $unite_vente != $rangee['graine_gramme'])
    {
        echo $rangee['reference'].';'.$rangee['name'].';'.$rangee['graine_gramme'].' > '.$unite_vente.'<br />';
        if ( $rangee['graine_gramme'] == 'gramme' && $unite_vente == 'graine' )
        {
            $sql1 = 'UPDATE ps_inventaire_lots SET graine_gramme = "'.$unite_vente.'" WHERE id_inventaire_lots = "'.$rangee['id_inventaire_lots'].'";';
	        $req1 = Db::getInstance()->Execute($sql1);
        }
        $erreur[$id_product] = $rangee['graine_gramme'].' > '.$unite_vente;
    }
}
echo '<pre>';
print_r($erreur);
echo '</pre>';

function get_declinaison($id_product){
	$sql = 'SELECT *, sa.quantity as qte, pa.weight as poids FROM `ps_product_attribute` AS pa
	LEFT JOIN `ps_product_attribute_combination` AS pac ON pac.id_product_attribute = pa.id_product_attribute
	LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
	LEFT JOIN `ps_attribute_lang` AS al ON al.id_attribute = pac.id_attribute
	LEFT JOIN ps_stock_available sa ON sa.id_product_attribute = pa.id_product_attribute
	WHERE pa.id_product = '.$id_product.' AND al.id_lang = 1 ORDER BY pa.default_on DESC, a.position ASC';

	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	return $res;
}

function conversion($id_product){
	// conversion graines/grammes
	$conversion = 1;
	$sql = 'SELECT value FROM ps_feature_value_lang v
	INNER JOIN ps_feature_product p ON p.id_feature_value = v.id_feature_value
	WHERE p.id_product = '.pSQL($id_product).'
	AND p.id_feature = 17
	AND v.id_lang = 1';
	$res = Db::getInstance()->executeS($sql);
	if(is_array($res)){
		if(count($res)){
			foreach($res as $r){
				if($r['value']){
					$conversion = $r['value'];
				}
			}
		}
	}
	return $conversion;
}



?>
