<?php
include("../config/config.inc.php");

/*$cookie = new Cookie('psAdmin', '', (int)Configuration::get('PS_COOKIE_LIFETIME_BO')); 
$emp_id = '';
if (isset($cookie->id_employee) && $cookie->id_employee) {
	$emp_id = $cookie->id_employee;
}
if ( $emp_id == 18 )
{
	die();
}*/
$html = '';

/*$id_commandes_attentes = Order::getOrderIdsByStatus(2, 50);

$tab_order_attr = array();

foreach ($id_commandes_attentes as $comm) 
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

$cmds_attentes = implode('-',$id_commandes_attentes);
$id_commandes_attentes_mandat = Order::getOrderIdsByStatus(12);
if(strlen($cmds_attentes)> 0 && !empty($id_commandes_attentes_mandat))
{
	$cmds_attentes .= '-';
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

$cmds_attentes .= implode('-',$id_commandes_attentes_mandat);
$cntCA = explode('-', $cmds_attentes);
	
// Limite à 30 commandes car il y a des erreurs lorsqu'il y a trop de commandes	
if ( $nb_etiq > 30 )
{
	$html = '<a id="page-header-desc-order-imprimer" class="toolbar_btn btn btn-primary" href="/admin123/test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes.'" title="Impression des BLs ('.count($cntCA).')" onclick="window.open(\'https://dev.labonnegraine.com/admin123/liens_etiq.php\'), print_bls(\''.$cmds_attentes.'\')"><i class="process-icon-print "></i><span>Impression des BLs ('.count($cntCA).')</span></a>';
}
else 
{
	$html = '<a id="page-header-desc-order-imprimer" class="toolbar_btn btn btn-primary" href="/admin123/test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes.'" title="Impression des BLs ('.count($cntCA).')" onclick="print_bls(\''.$cmds_attentes.'\'), window.open(\'http://localhost/etiquettes/index_prod_test.php?l='.$liste_comm_prod.'\')"><i class="process-icon-print "></i><span>Impression des BLs ('.count($cntCA).')</span></a>';
}*/

/* Début BL par zone 1 à 5 */

$zone_color = array(1=>"#2196f3", 2=>"#673AB7", 3=>"#795548", 4=>"#f44336", 5=>"#ffc107");
for ($izone = 1; $izone <= 5; $izone++)
{
	$cmds_attentes_zone = '';
	$id_commandes_attentes_zone = Order::getOrderIdsByStatusByZone(2, 50, $izone);
	$cmds_attentes_zone = implode('-',$id_commandes_attentes_zone);

	$id_commandes_attentes_mandat = Order::getOrderIdsByStatusByZone(12, 50, $izone);
	if(strlen($cmds_attentes_zone)> 0 && !empty($id_commandes_attentes_mandat))
	{
		$cmds_attentes_zone .= '-';
	}
	$cmds_attentes_zone .= implode('-',$id_commandes_attentes_mandat);

	if ( count($id_commandes_attentes_zone) > 0 )
	{
		$tab_order_attr = array();

		foreach ($id_commandes_attentes_zone as $comm) 
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
			$html .= '<a id="page-header-desc-order-imprimerZ'.$izone.'" class="toolbar_btn btn btn-primary" style="background-color:'.$zone_color[$izone].';border-color:'.$zone_color[$izone].';" href="/admin123/test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes_zone.'" title="Impression des BLs Z'.$izone.' ('.count($id_commandes_attentes_zone).')" onclick="window.open(\'https://dev.labonnegraine.com/admin123/liens_etiq.php?zone='.$izone.'\'), print_bls(\''.$cmds_attentes_zone.'\')"><i class="process-icon-print "></i><span>BLs Z'.$izone.' ('.count($id_commandes_attentes_zone).')</span></a>';
		}
		else
		{
			$html .= '<a id="page-header-desc-order-imprimerZ'.$izone.'" class="toolbar_btn btn btn-primary" style="background-color:'.$zone_color[$izone].';border-color:'.$zone_color[$izone].';" href="/admin123/test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes_zone.'" title="Impression des BLs Z'.$izone.' ('.count($id_commandes_attentes_zone).')" onclick="print_bls(\''.$cmds_attentes_zone.'\'), window.open(\'http://localhost/etiquettes/index_prod_test.php?l='.$liste_comm_prod.'\')"><i class="process-icon-print "></i><span>BLs Z'.$izone.' ('.count($id_commandes_attentes_zone).')</span></a>';
		}
	}
}

/* Fin BL par zone 1 à 5 */

/* Début BL Lettre verte */

$cmds_attentes_zonelv = '';
$id_commandes_attentes_zonelv = Order::getOrderIdsByStatusByZone(2, 50, -2);
$cmds_attentes_zonelv = implode('-',$id_commandes_attentes_zonelv);

$id_commandes_attentes_mandat = Order::getOrderIdsByStatusByZone(12, 50, -2);
if(strlen($cmds_attentes_zonelv)> 0 && !empty($id_commandes_attentes_mandat))
{
	$cmds_attentes_zonelv .= '-';
}
$cmds_attentes_zonelv .= implode('-',$id_commandes_attentes_mandat);

if ( count($id_commandes_attentes_zonelv) > 0 )
{
	$tab_order_attr = array();

	foreach ($id_commandes_attentes_zonelv as $comm) 
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
		$html .= '<a id="page-header-desc-order-imprimerZlv" class="toolbar_btn btn btn-primary" style="background-color:#4caf50;border-color:#4caf50;" href="/admin123/test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes_zonelv.'" title="Impression des BLs Z'.$izone.' ('.count($id_commandes_attentes_zonelv).')" onclick="window.open(\'https://dev.labonnegraine.com/admin123/liens_etiq.php?zone=-2\'), print_bls(\''.$cmds_attentes_zonelv.'\')"><i class="process-icon-print "></i><span>BLs Lettres vertes ('.count($id_commandes_attentes_zonelv).')</span></a>';
	}
	else
	{	
		$html .= '<a id="page-header-desc-order-imprimerZlv" class="toolbar_btn btn btn-primary" style="background-color:#4caf50;border-color:#4caf50;" href="/admin123/test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes_zonelv.'" title="Impression des BLs Z Mixte ('.count($id_commandes_attentes_zonelv).')" onclick="print_bls(\''.$cmds_attentes_zonelv.'\'), window.open(\'http://localhost/etiquettes/index_prod_test.php?l='.$liste_comm_prod.'\')"><i class="process-icon-print "></i><span>BLs Lettres vertes ('.count($id_commandes_attentes_zonelv).')</span></a>';
	}
}
/* Fin BL Lettre verte */

/* Début BL Rosiers mixte */
$cmds_attentes_zonerm = '';
$id_commandes_attentes_zonerm = Order::getOrderIdsByStatusByZone(2, 50, -4);
$cmds_attentes_zonerm = implode('-',$id_commandes_attentes_zonerm);

$id_commandes_attentes_mandat = Order::getOrderIdsByStatusByZone(12, 50, -4);
if(strlen($cmds_attentes_zonerm)> 0 && !empty($id_commandes_attentes_mandat))
{
	$cmds_attentes_zonerm .= '-';
}
$cmds_attentes_zonerm .= implode('-',$id_commandes_attentes_mandat);

if ( count($id_commandes_attentes_zonerm) > 0 )
{
	$tab_order_attr = array();

	foreach ($id_commandes_attentes_zonerm as $comm) 
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
		$html .= '<a id="page-header-desc-order-imprimerZrm" class="toolbar_btn btn btn-primary" style="background-color:#fd8fb5;border-color:#fd8fb5;" href="/admin123/test_etiquettes_rosiers.php?deliveryslipsadmin='.$cmds_attentes_zonerm.'" title="Impression des BLs Z'.$izone.' ('.count($id_commandes_attentes_zonerm).')" onclick="window.open(\'https://dev.labonnegraine.com/admin123/liens_etiq.php?zone=-2\'), print_bls(\''.$cmds_attentes_zonerm.'\')"><i class="process-icon-print "></i><span>BLs Rosiers ('.count($id_commandes_attentes_zonerm).')</span></a>';
	}
	else
	{	
		$html .= '<a id="page-header-desc-order-imprimerZrm" class="toolbar_btn btn btn-primary" style="background-color:#fd8fb5;border-color:#fd8fb5;" href="/admin123/test_etiquettes_rosiers.php?deliveryslipsadmin='.$cmds_attentes_zonerm.'" title="Impression des BLs Z Mixte ('.count($id_commandes_attentes_zonerm).')" onclick="print_bls(\''.$cmds_attentes_zonerm.'\'), window.open(\'http://localhost/etiquettes/index_prod_test.php?l='.$liste_comm_prod.'\')"><i class="process-icon-print "></i><span>BLs Rosiers ('.count($id_commandes_attentes_zonerm).')</span></a>';
	}
}
/* Fin BL Rosiers mixte */

/* Début BL Z Mixte */

$cmds_attentes_zonem = '';
$id_commandes_attentes_zonem = Order::getOrderIdsByStatusByZone(2, 50, -1);
$cmds_attentes_zonem = implode('-',$id_commandes_attentes_zonem);

$id_commandes_attentes_mandat = Order::getOrderIdsByStatusByZone(12, 50, -1);
if(strlen($cmds_attentes_zonem)> 0 && !empty($id_commandes_attentes_mandat))
{
	$cmds_attentes_zonem .= '-';
}
$cmds_attentes_zonem .= implode('-',$id_commandes_attentes_mandat);

if ( count($id_commandes_attentes_zonem) > 0 )
{
	$tab_order_attr = array();

	foreach ($id_commandes_attentes_zonem as $comm) 
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
		$html .= '<a id="page-header-desc-order-imprimerZm" class="toolbar_btn btn btn-primary" style="background-color:#e91e63;border-color:#e91e63;" href="/admin123/test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes_zonem.'" title="Impression des BLs Z'.$izone.' ('.count($id_commandes_attentes_zonem).')" onclick="window.open(\'https://dev.labonnegraine.com/admin123/liens_etiq.php?zone=-1\'), print_bls(\''.$cmds_attentes_zonem.'\')"><i class="process-icon-print "></i><span>BLs Z Mixte ('.count($id_commandes_attentes_zonem).')</span></a>';
	}
	else
	{	
		$html .= '<a id="page-header-desc-order-imprimerZm" class="toolbar_btn btn btn-primary" style="background-color:#e91e63;border-color:#e91e63;" href="/admin123/test_etiquettes.php?deliveryslipsadmin='.$cmds_attentes_zonem.'" title="Impression des BLs Z Mixte ('.count($id_commandes_attentes_zonem).')" onclick="print_bls(\''.$cmds_attentes_zonem.'\'), window.open(\'http://localhost/etiquettes/index_prod_test.php?l='.$liste_comm_prod.'\')"><i class="process-icon-print "></i><span>BLs Z Mixte ('.count($id_commandes_attentes_zonem).')</span></a>';
	}
}
/* Fin BL Z Mixte */

echo $html;
