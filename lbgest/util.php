<?php

// Ce fichier contient plusieurs fonctions utiles

function get_unite_vente($id_product){
	$declinaisons = get_declinaison($id_product);
	$unite_vente = "";
	foreach ($declinaisons as $dec_prod) {
		if($dec_prod['name'] != 'Non traitée' && $dec_prod['name'] != 'BIO'){
			$dec_prod['name'] = str_replace('Par ', '', $dec_prod['name']);
			$exp = explode(' ', $dec_prod['name']);                   
			if ( strtolower($exp[1]) == 'graines' ){
			  $unite_vente = 'graine';
			  break;
			}
			else {
			  $unite_vente = 'gramme';
			  break;
			}
		}
	}
	return $unite_vente;
}

function get_declinaison($id_product){
	$sql = 'SELECT *, sa.quantity as qte, pa.weight as poids FROM `ps_product_attribute` AS pa
	LEFT JOIN `ps_product_attribute_combination` AS pac ON pac.id_product_attribute = pa.id_product_attribute
	LEFT JOIN ps_attribute a ON a.id_attribute = pac.id_attribute
	LEFT JOIN `ps_attribute_lang` AS al ON al.id_attribute = pac.id_attribute
	LEFT JOIN ps_stock_available sa ON sa.id_product_attribute = pa.id_product_attribute
	WHERE pa.id_product = '.pSQL($id_product).' AND al.id_lang = 1 ORDER BY pa.default_on DESC, a.position ASC';

	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

	return $res;
}

function conversion($id_product){
	// conversion graines/grammes
	$conversion = 0;
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
					break;
				}
			}
		}
	}
	return $conversion;
}

?>
