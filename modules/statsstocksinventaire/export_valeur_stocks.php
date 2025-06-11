<?php

define('PS_ADMIN_DIR', getcwd()."/../../admin123");
include(PS_ADMIN_DIR . '/../config/config.inc.php');
/* Getting cookie or logout */
require_once(PS_ADMIN_DIR . '/../init.php');

//include('connect.php');
function valideChaine($chaineNonValide)
{
    $chaineNonValide = preg_replace('`\s+`', '', trim(utf8_decode($chaineNonValide)));
    $chaineNonValide = str_replace("'", "", $chaineNonValide);
    $chaineNonValide = preg_replace('`_+`', '', trim($chaineNonValide));
    $chaineValide = strtr($chaineNonValide, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn")
    ;
    return (strtolower($chaineValide));
}

$message_erreur = "";
$db = Db::getInstance();
$nom_category = "";
$query_depart = "";

if ((isset($_GET['id_category']) && $_GET['id_category'] != ""))
{
	$query_category_set = 'SELECT p.`id_product`
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON p.`id_product` = pl.`id_product`
		LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
		WHERE pl.`id_lang` = 1
		AND cp.id_category = ' . $_GET['id_category'] . '
		AND p.active=1 ORDER BY pl.`name`';
		
    $query_depart = "SELECT p.id_product, p.id_category_default FROM "._DB_PREFIX_."product p, "._DB_PREFIX_."product_lang pl WHERE p.id_product = pl.id_product AND pl.id_lang = 1 AND p.id_product IN (" . $query_category_set . ") ORDER BY id_category_default, name;";
    //Récupération nom catégorie
    $query_cat_name = "SELECT name FROM "._DB_PREFIX_."category_lang WHERE id_category=" . $_GET['id_category'] . " AND id_lang = 1";
    $name_array = $db->ExecuteS($query_cat_name);
    $nom_category = valideChaine($name_array[0]['name']);
}
else
{
    $query_depart = "SELECT p.id_product, p.id_category_default FROM "._DB_PREFIX_."product p, "._DB_PREFIX_."product_lang pl WHERE p.id_product = pl.id_product AND pl.id_lang = 1 ORDER BY id_category_default, name;";
}

$products = $db->ExecuteS($query_depart);
foreach ($products as $product)
{
    $pas_inventaire = 0;
    $query_test_active_product = "SELECT count(*) as cpt FROM "._DB_PREFIX_."product WHERE active = 1 AND id_product=" . $product['id_product'] . ";";
    $test_active = Db::getInstance()->ExecuteS($query_test_active_product);
    if ($test_active[0]['cpt'] >= "1")
    {
        //Récupération du stock tampon
        $inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'inventaire WHERE id_product_attribute = "0" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');
        if (isset($inv_tamp['valeur']))
        {
            $stock_tampon = $inv_tamp['valeur'];
        }
        else
            $stock_tampon = "";

        //Récupération des attributs
        $rangee_attrib = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_attribute WHERE id_product = "' . $product['id_product'] . '";');
        $poids_theorique = 0;
        foreach ($rangee_attrib AS $attrib)
        {
            $aux_dec = array();
            $qt_commandee = 0;
            $stock_theorique = 0;
            $rangee_comb = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'product_attribute_combination WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '";');
            foreach ($rangee_comb AS $comb)
            {
                $dec = Db::getInstance()->ExecuteS('SELECT name FROM '._DB_PREFIX_.'attribute_lang WHERE id_attribute = "' . $comb['id_attribute'] . '" AND id_lang = 1 LIMIT 0,1;');
                $aux_dec[] = $dec[0]['name'];
            }
            sort($aux_dec);
            $libelle_dec = implode(' - ', $aux_dec);
            $inv = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'inventaire WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');
            
			$jour_inv = "";
			$mois_inv = "";
			$annee_inv = "";
			$heure_inv = "";
			$minutes_inv = "";
			if (!empty($inv[0]['date']))
            {
                $jour_inv = substr($inv[0]['date'], 6, 2);
                $mois_inv = substr($inv[0]['date'], 4, 2);
                $annee_inv = substr($inv[0]['date'], 0, 4);
                $heure_inv = substr($inv[0]['date'], 8, 2);
                $minutes_inv = substr($inv[0]['date'], 10, 2);
            }

            // Somme des quantités commandées depuis de dernier inventaire
            $commandes = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'order_detail pod, '._DB_PREFIX_.'orders po WHERE po.id_order = pod.id_order AND pod.product_id = "' . $product['id_product'] . '" AND pod.product_attribute_id = "' . $attrib['id_product_attribute'] . '" AND po.date_add > "' . $annee_inv . '-' . $mois_inv . '-' . $jour_inv . ' ' . $heure_inv . ':' . $minutes_inv . '";');
            foreach ($commandes AS $commande)
            {
                $qt_commandee += $commande['product_quantity'];
            }
            if (isset($inv[0]['valeur']))
            {
                $stock_theorique = $inv[0]['valeur'] - $qt_commandee;
                $poids_theorique += $stock_theorique * $attrib['weight'];
            }
            else
            {
                $pas_inventaire ++;
            }
        }
        $qt_reassort = 0;
        $stock_theorique_tamp = 0;
        $inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'inventaire WHERE id_product_attribute = "0" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');
        $jour_inv_tamp = "";
        $mois_inv_tamp = "";
        $annee_inv_tamp = "";
        $heure_inv_tamp = "";
        $minutes_inv_tamp = "";
		if (!empty($inv_tamp[0]['date']))
        {
            $jour_inv_tamp = substr($inv_tamp[0]['date'], 6, 2);
            $mois_inv_tamp = substr($inv_tamp[0]['date'], 4, 2);
            $annee_inv_tamp = substr($inv_tamp[0]['date'], 0, 4);
            $heure_inv_tamp = substr($inv_tamp[0]['date'], 8, 2);
            $minutes_inv_tamp = substr($inv_tamp[0]['date'], 10, 2);
        }

        // Somme des quantités commandées depuis de dernier inventaire
        $reassorts = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'reassort WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "0" AND date > "' . $annee_inv_tamp . $mois_inv_tamp . $jour_inv_tamp . $heure_inv_tamp . $minutes_inv_tamp . '";');
        foreach ($reassorts AS $reassort)
        {
            $qt_reassort += $reassort['valeur'];
        }
        $stock_theorique_tamp = @$inv_tamp[0]['valeur'] + $qt_reassort;

        $stock_theorique_theo = (($poids_theorique * 1000) + $stock_theorique_tamp);

        //Récupération de la catégorie
        $query_category_name = "SELECT name FROM "._DB_PREFIX_."category_lang WHERE id_category = " . $product['id_category_default'] . " AND id_lang=1";
        $rangee_category_name = Db::getInstance()->ExecuteS($query_category_name);


        $product_complet_use = new Product($product['id_product']);
        $prix_achat = floatval($product_complet_use->wholesale_price) / 1000;
        $prix_stock_theorique = Tools::ps_round(floatval($prix_achat) * floatval(($poids_theorique * 1000) + $stock_theorique_tamp), 2);
        if ($prix_stock_theorique < 0)
        {
            $prix_stock_theorique = 0;
        }

        $query = 'SELECT name FROM '._DB_PREFIX_.'product_lang WHERE id_product = ' . $product['id_product'] . ' AND id_lang=1;';
        $rangee_nom_produit = Db::getInstance()->ExecuteS($query);
        $message_erreur .= utf8_decode($rangee_category_name[0]['name']) . ";" . utf8_decode($rangee_nom_produit[0]['name']) . ";" . utf8_decode($stock_theorique_theo) . ";" . ($prix_achat * 1000) . ";" . utf8_decode($prix_stock_theorique) . "\n";
    }
}
$file_name = "exportstocks_";
if ((isset($_GET['id_category']) && $_GET['id_category'] != ""))
{
    $file_name .= $nom_category . "_";
}
else
{
    $file_name .= "toutescategories_";
}
$file_name .= date('Y-m-d_H-i-s') . '.csv';

$message_erreur = "Catégorie du produit;Nom du produit;Stock Théorique en grammes;Prix achat au kilo en €uros;Valeur théorique du stock en €uros\n" . $message_erreur;
header('Content-Type: application/csv-tab-delimited-table');
header('Content-disposition: filename=' . $file_name);
echo $message_erreur;
exit;
?>