<?php
require 'application_top.php';

// Exemple de commandes avec les articles et leurs emplacements

$commandes[] = 
    array('commande_id' => 1, 'articles' => array(
        array('article_id' => 1, 'quantite' => 2, 'emplacement' => 'G11'),
        array('article_id' => 2, 'quantite' => 1, 'emplacement' => 'G10')
    ));
$listeCmd = Commande::getOrdersByZone(1);
foreach($listeCmd as $cmdEC)
{
    $listeArt = Commande::getProductsByOrder($cmdEC);
    $articles = array();
    foreach($listeArt as $artEC)
    {
        $req_emp = 'SELECT etagere_plan FROM ps_LogiGraine_plan WHERE REPLACE(debut_plan,"-","") <= "'.str_replace('-', '', $artEC['product_reference']).'" AND REPLACE(fin_plan,"-","") >= "'.str_replace('-', '', $artEC['product_reference']).'" LIMIT 0,1;';
        //echo $req_emp.'<br />';
        $resu_emp = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_emp);

        $articles[] = array('article_id' => $artEC['product_id'], 'quantite' => $artEC['quantity_final'], 'emplacement' => $resu_emp[0]['etagere_plan']);
    }
    $commandes[] = array('commande_id' => $cmdEC, 'articles' => $articles);
}

$commandes[] = 
    array('commande_id' => 2, 'articles' => array(
        array('article_id' => 1, 'quantite' => 2, 'emplacement' => 'G11'),
        array('article_id' => 2, 'quantite' => 1, 'emplacement' => 'G10')
    ));
    $commandes[] = 
        array('commande_id' => 3, 'articles' => array(
            array('article_id' => 1, 'quantite' => 2, 'emplacement' => 'G11'),
            array('article_id' => 2, 'quantite' => 1, 'emplacement' => 'G10')
        ));
        $commandes[] = 
            array('commande_id' => 4, 'articles' => array(
                array('article_id' => 1, 'quantite' => 2, 'emplacement' => 'G22'),
                array('article_id' => 2, 'quantite' => 1, 'emplacement' => 'G22')
            ));

/*echo '<pre>';
print_r($commandes);
echo '</pre>';

die;*/

/*$commandes = [
    ['commande_id' => 1, 'articles' => [
        ['article_id' => 1, 'quantite' => 2, 'emplacement' => 'A1'],
        ['article_id' => 2, 'quantite' => 1, 'emplacement' => 'B1']
    ]],
    ['commande_id' => 2, 'articles' => [
        ['article_id' => 3, 'quantite' => 3, 'emplacement' => 'A2'],
        ['article_id' => 4, 'quantite' => 1, 'emplacement' => 'B2']
    ]],
    ['commande_id' => 3, 'articles' => [
        ['article_id' => 1, 'quantite' => 1, 'emplacement' => 'A1'],
        ['article_id' => 3, 'quantite' => 2, 'emplacement' => 'A3']
    ]],
    ['commande_id' => 4, 'articles' => [
        ['article_id' => 2, 'quantite' => 1, 'emplacement' => 'B1'],
        ['article_id' => 4, 'quantite' => 2, 'emplacement' => 'B2']
    ]],
    ['commande_id' => 5, 'articles' => [
        ['article_id' => 1, 'quantite' => 4, 'emplacement' => 'A1'],
        ['article_id' => 2, 'quantite' => 2, 'emplacement' => 'B1']
    ]],
];*/

// Fonction pour déterminer le rayon à partir de l'emplacement
function obtenirRayon($emplacement) {
    $rayons = [
        'A' => 1,
        'B' => 2,
        'C' => 3,
        'D' => 4,
        'E' => 5,
        'F' => 6,
        'G' => 7
    ];

    $lettreRayon = substr($emplacement, 0, 1); // La première lettre de l'emplacement (A, B, C, etc.)
    return isset($rayons[$lettreRayon]) ? $rayons[$lettreRayon] : null;
}

// Fonction pour vérifier si deux rayons sont face à face
function sontRayonsFaces($rayon1, $rayon2) {
    $rayonsFaceAFace = [
        [1, 2], // A et B sont face à face
        [3, 4], // C et D sont face à face
        [5, 6]  // E et F sont face à face
    ];

    return in_array([$rayon1, $rayon2], $rayonsFaceAFace) || in_array([$rayon2, $rayon1], $rayonsFaceAFace);
}

// Fonction pour déterminer si deux étagères sont proches
function sontEtageresProches($emplacement1, $emplacement2) {
    // Extrait les lettres et les numéros des étagères
    $rayon1 = substr($emplacement1, 0, 1);
    $rayon2 = substr($emplacement2, 0, 1);
    $num1 = (int)substr($emplacement1, 1);
    $num2 = (int)substr($emplacement2, 1);

    // Si les étagères sont dans le même rayon, elles sont proches
    if ($rayon1 === $rayon2) {
        return abs($num1 - $num2) <= 1;  // Deux étagères consécutives dans le même rayon
    }

    // Si les étagères sont dans des rayons face à face, elles sont également proches
    if (sontRayonsFaces(obtenirRayon($emplacement1), obtenirRayon($emplacement2))) {
        return abs($num1 - $num2) <= 1;  // Deux étagères adjacentes dans des rayons face à face
    }

    return false;
}

// Fonction pour regrouper les commandes par proximité d'étagères
function regrouperCommandesParProximite($commandes) {
    $groupes = [];

    foreach ($commandes as $commande) {
        $ajoute = false;

        // Vérifier si la commande peut être ajoutée à un groupe existant
        foreach ($groupes as &$groupe) {
            $etageresGroupe = [];

            // Extraire les étagères des articles déjà dans ce groupe
            foreach ($groupe as $commandeExistante) {
                foreach ($commandeExistante['articles'] as $articleExist) {
                    $etageresGroupe[] = $articleExist['emplacement'];
                }
            }

            // Vérifier si les articles de la commande appartiennent à des étagères proches
            $etageresCommande = [];
            foreach ($commande['articles'] as $article) {
                $etageresCommande[] = $article['emplacement'];
            }

            // Vérification de la proximité : vérifier que les étagères sont proches
            $proximite = false;
            foreach ($etageresCommande as $etagereCommande) {
                foreach ($etageresGroupe as $etagereGroupe) {
                    // Si les étagères sont proches, on les regroupe
                    if (sontEtageresProches($etagereCommande, $etagereGroupe)) {
                        $proximite = true;
                        break 2; // Les étagères sont proches
                    }
                }
            }

            // Si la commande est proche, on l'ajoute au groupe
            if ($proximite) {
                $groupe[] = $commande;
                $ajoute = true;
                break;
            }
        }

        // Si aucune proximité n'a été trouvée, créer un nouveau groupe
        if (!$ajoute) {
            $groupes[] = [$commande];
        }
    }

    return $groupes;
}

// Diviser les groupes en sous-groupes de 4
function diviserEnGroupesDe4($groupes) {
    $groupesFinales = [];
    foreach ($groupes as $groupe) {
        $chunks = array_chunk($groupe, 4); // Divise chaque groupe en sous-groupes de 4
        foreach ($chunks as $chunk) {
            $groupesFinales[] = $chunk;
        }
    }
    return $groupesFinales;
}

// Regrouper les commandes par proximité
$groupesRegroupes = regrouperCommandesParProximite($commandes);

// Diviser les groupes en sous-groupes de 4
$groupesFinales = diviserEnGroupesDe4($groupesRegroupes);

// Affichage des groupes finaux
echo "Groupes de picking optimisés :<br />";
foreach ($groupesFinales as $index => $groupe) {
    echo "<br />Groupe " . ($index + 1) . ":<br />";
    foreach ($groupe as $commande) {
        echo "- Commande ID: {$commande['commande_id']}<br />";
        foreach ($commande['articles'] as $article) {
            echo "&nbsp;&nbsp;- Article ID: {$article['article_id']} - Quantité: {$article['quantite']} - Emplacement: {$article['emplacement']}<br />";
        }
    }
}
?>