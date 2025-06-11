<?php
    require 'application_top.php';

    $req_d = 'DELETE FROM ps_LogiGraine_rangement_pdt_reassort WHERE termine = 0;';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_d);

    $besoins = array();

    $req = 'SELECT * FROM ps_LogiGraine_rangement_pdt_pk;';
    foreach ( Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req) as $rangee )
    {
        //$prodEC = new Produit($rangee['ean']);
        if ( $rangee['quantity'] <= ceil($rangee['max']/3) )
        {
            $besoins[$rangee['ean']] = $rangee['max'] - $rangee['quantity'];
        }
    }
    //$besoins['3597363023229'] = 2;

    echo '<pre>';
    print_r($besoins);
    echo '</pre>';

    // Fonction pour récupérer les stocks par produit
    function getStockByEAN($ean) {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT rp.* FROM ps_LogiGraine_rangement_pdt rp LEFT JOIN ps_LogiGraine_rangement_pdt_reserve rpr ON rp.emplacement = rpr.emplacement WHERE rp.ean = "'.$ean.'" ORDER BY rpr.ordre ASC;');
    }

    // Fonction pour trouver les emplacements optimisés pour une liste de produits
    function findEmplacementsOptimises($demandes) {
        $result = [];

        // Crée un tableau pour les palettes disponibles par produit
        $palettesParProduit = [];

        // Récupère les emplacements pour chaque produit
        foreach ($demandes as $eanEC => $demande) {
            $ean13 = $eanEC;
            $quantiteDemandee = $demande;

            // Récupère les emplacements pour cet EAN
            $emplacements = getStockByEAN($ean13);

            $palettesParProduit[$ean13] = [];
            foreach ($emplacements as $emplacement) {
                $empEC = substr($emplacement['emplacement'], 0, 4);
                $palettesParProduit[$ean13][] = [
                    'emplacement' => $empEC,
                    'quantite' => $emplacement['quantity']
                ];
            }
        }

        // Trouver les palettes communes entre plusieurs produits
        $palettesCommunes = findPalettesCommunes($palettesParProduit);

        // Processus de sélection des palettes et de satisfaction de la demande
        foreach ($demandes as $eanEC => $demande) {
            $ean13 = $eanEC;
            $quantiteDemandee = $demande;

            // Liste des emplacements utilisés pour ce produit
            $palettesUtilisees = [];
            $quantiteRestante = $quantiteDemandee;

            // Étape 1 : Prendre le maximum de produits sur les palettes communes
            $quantiteRestante = takeFromCommonPalettes($ean13, $palettesCommunes, $quantiteRestante, $palettesUtilisees);

            // Si la demande n'a pas été entièrement satisfaite par les palettes communes, on passe aux palettes spécifiques
            if ($quantiteRestante > 0) {
                $quantiteRestante = takeFromSpecificPalettes($ean13, $quantiteRestante, $palettesParProduit[$ean13], $palettesUtilisees);
            }

            // Si la demande n'a pas pu être complètement satisfaite, on affiche un message
            /*if ($quantiteRestante > 0) {
                $result[] = [
                    'ean13' => $ean13,
                    'message' => "Pas assez de stock pour satisfaire la demande de $quantiteDemandee unités."
                ];
            } else {*/
                // Enregistrer les emplacements utilisés pour cet EAN
                $result[] = [
                    'ean13' => $ean13,
                    'palettes' => $palettesUtilisees
                ];
            //}
        }

        return $result;
    }

    // Fonction pour récupérer la quantité d'un produit sur une palette
    function getQuantiteInPalette($ean13, $emplacement) {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT SUM(quantity) as quantiteEC FROM ps_LogiGraine_rangement_pdt WHERE ean = "'.$ean13.'" AND emplacement LIKE "'.$emplacement.'%";');
        return $result ? $result[0]['quantiteEC'] : 0;
    }

    // Fonction pour prendre les produits à partir des palettes communes
    function takeFromCommonPalettes($ean13, $palettesCommunes, $quantiteRestante, &$palettesUtilisees) {
        foreach ($palettesCommunes as $emplacement) {
            if ($quantiteRestante <= 0) break; // Si la demande est déjà satisfaite, on arrête

            $quantiteDisponible = getQuantiteInPalette($ean13, $emplacement);
            $quantiteAPrendre = min($quantiteRestante, $quantiteDisponible);
            $quantiteRestante -= $quantiteAPrendre;

            if ( $quantiteAPrendre > 0 )
            {
                // Ajouter l'emplacement à la liste des emplacements utilisés
                $palettesUtilisees[] = [
                    'emplacement' => $emplacement,
                    'quantite' => $quantiteAPrendre
                ];
            }
        }

        return $quantiteRestante;
    }

    // Fonction pour prendre les produits à partir des palettes spécifiques
    function takeFromSpecificPalettes($ean13, $quantiteRestante, $palettes, &$palettesUtilisees) {
        foreach ($palettes as $palette) {
            if ($quantiteRestante <= 0) break; // Si la demande est déjà satisfaite, on arrête

            $quantiteDisponible = $palette['quantite'];
            $quantiteAPrendre = min($quantiteRestante, $quantiteDisponible);
            $quantiteRestante -= $quantiteAPrendre;

            if ( $quantiteAPrendre > 0 )
            {
                // Ajouter l'emplacement à la liste des emplacements utilisés
                $palettesUtilisees[] = [
                    'emplacement' => $palette['emplacement'],
                    'quantite' => $quantiteAPrendre
                ];
            }
        }

        return $quantiteRestante;
    }

    // Fonction pour trouver les palettes communes entre plusieurs produits
    function findPalettesCommunes($palettesParProduit) {
        // Créer une liste de toutes les palettes
        $toutesLesPalettes = [];
        foreach ($palettesParProduit as $ean => $palettes) {
            foreach ($palettes as $palette) {
                $toutesLesPalettes[$palette['emplacement']][] = $ean;
            }
        }

        // Trouver les palettes communes
        $palettesCommunes = [];
        foreach ($toutesLesPalettes as $emplacement => $eanList) {
            // Si cette palette contient des produits différents (donc commune à plusieurs produits)
            if (count(array_unique($eanList)) > 1) {
                $palettesCommunes[] = $emplacement;
            }
        }

        return $palettesCommunes;
    }

    // Exécution de la fonction optimisée
    $resultat = findEmplacementsOptimises($besoins);

    // Affichage des résultats
    foreach ($resultat as $item) {
        if (isset($item['message'])) {
            echo $item['message'] . PHP_EOL;
        } else {
            echo "EAN: " . $item['ean13'] . "\n";
            echo "Emplacements utilisés :\n";
            foreach ($item['palettes'] as $palette) {
                $prodEC = new Produit($item['ean13']);

                $req_v = 'SELECT * FROM ps_LogiGraine_rangement_pdt_reassort WHERE id_product = "'.$prodEC->id.'" AND id_product_attribute = "'.$prodEC->id_declinaison.'" AND emplacement = "'.$palette['emplacement'].'" AND termine = 0;';
                $resu_v = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_v);

                if ( isset($resu_v[0]['id_product']) && !empty($resu_v[0]['id_product']) )
                {
                    $req_r = 'UPDATE ps_LogiGraine_rangement_pdt_reassort SET quantity = quantity + '.$palette['quantite'].' WHERE id_product = "'.$prodEC->id.'" AND id_product_attribute = "'.$prodEC->id_declinaison.'" AND emplacement = "'.$palette['emplacement'].'" AND termine = 0;';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_r);
                }
                else 
                {
                    $req_r = 'INSERT INTO ps_LogiGraine_rangement_pdt_reassort SET id_product = "'.$prodEC->id.'", id_product_attribute = "'.$prodEC->id_declinaison.'", emplacement = "'.$palette['emplacement'].'", quantity = "'.$palette['quantite'].'", termine = 0;';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_r);
                }
                echo "- Emplacement: " . $palette['emplacement'] . ", Quantité: " . $palette['quantite'] . "<br />";
            }
        }
    }
?>
