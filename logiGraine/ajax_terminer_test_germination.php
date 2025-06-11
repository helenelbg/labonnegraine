<?php
require 'application_top.php';

if (isset($_POST['id_test']) && !empty($_POST['id_test'])) {
    $id_test = (int)$_POST['id_test'];
    $commentaire = isset($_POST['commentaire']) ? addslashes($_POST['commentaire']) : '';
    
    // Récupérer les informations du test
    $sql_test = 'SELECT tl.*, il.id_product, il.numero_lot_LBG 
                 FROM AW_test_lots tl 
                 LEFT JOIN ps_inventaire_lots il ON tl.id_lot = il.id_inventaire_lots 
                 WHERE tl.id = "'.$id_test.'"';
    
    $test_info = Db::getInstance()->executeS($sql_test);
    
    if (!empty($test_info)) {
        $test = $test_info[0];
        
        // Calculer le pourcentage final (moyenne des 3 étapes)
        $total_etapes = 0;
        $nb_etapes = 0;
        
        if ($test['resultat_etape_1'] > 0) {
            $total_etapes = $test['resultat_etape_1'];
            $nb_etapes++;
        }
        if ($test['resultat_etape_2'] > 0) {
            $total_etapes = $test['resultat_etape_2'];
            $nb_etapes++;
        }
        if ($test['resultat_etape_3'] > 0) {
            $total_etapes = $test['resultat_etape_3'];
            $nb_etapes++;
        }
        
        $pourcentage_final = $nb_etapes > 0 ? $total_etapes : 0;
        
        // Mettre à jour le test comme terminé
        $sql_update = 'UPDATE AW_test_lots 
                       SET date_fin_test = NOW(), 
                           pourcentage_germ = "'.$pourcentage_final.'",
                           commentaire = "'.$commentaire.'" 
                       WHERE id = "'.$id_test.'"';
        
        if (Db::getInstance()->execute($sql_update)) {
            // Mettre à jour la table germination
            $sql_germination = 'UPDATE germination 
                               SET date_germination = NOW(), 
                                   germination = "'.$pourcentage_final.'",
                                   id_test = "'.$id_test.'" 
                               WHERE id_product = "'.$test['id_product'].'" 
                               AND lot_germination = "'.$test['numero_lot_LBG'].'"';
            
            Db::getInstance()->execute($sql_germination);
            
            // Libérer le code-barres pour réutilisation (optionnel)
            // Le code-barres reste associé au test terminé pour traçabilité
            // mais peut maintenant être réutilisé pour un nouveau test
            
            echo 'ok';
        } else {
            echo 'error_update';
        }
    } else {
        echo 'test_not_found';
    }
} else {
    echo 'missing_params';
}
?>