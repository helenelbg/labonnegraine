<?php

require 'application_top.php';

// Exemple de commandes avec les articles et leurs emplacements

/*$orders = [
    'ORDER1' => [
        ['123456', 1, 'A01'],
        ['234567', 2, 'B02'],
        ['345678', 1, 'C01']
    ],
    'ORDER2' => [
        ['456789', 3, 'D01'],
        ['567890', 1, 'E02'],
        ['678901', 2, 'F03']
    ],
    // Ajoutez d'autres commandes...
];*/

$listeCmd = Commande::getOrdersByZoneDEV(1);
foreach($listeCmd as $cmdEC)
{
    $listeArt = Commande::getProductsByOrder($cmdEC);
    $articles = array();
    foreach($listeArt as $artEC)
    {
        $req_emp = 'SELECT etagere_plan FROM ps_LogiGraine_plan WHERE REPLACE(debut_plan,"-","") <= "'.str_replace('-', '', $artEC['product_reference']).'" AND REPLACE(fin_plan,"-","") >= "'.str_replace('-', '', $artEC['product_reference']).'" LIMIT 0,1;';
        //echo $req_emp.'<br />';
        $resu_emp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_emp);

        $articles[] = array($artEC['product_ean13'], $artEC['quantity_final'], $resu_emp[0]['etagere_plan']);
    }
    $commandes[$cmdEC] = $articles;
}

/*echo '<pre>';
    print_r($commandes);
echo '</pre>';*/


// Exemple d'utilisation
$optimizer = new WarehouseTopologyOptimizer();

$optimizedGroups = $optimizer->optimizeOrders($commandes);

echo '<pre style="background-color:yellow">';
    print_r($optimizedGroups);
echo '</pre>';