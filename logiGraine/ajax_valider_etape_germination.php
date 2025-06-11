<?php
require 'application_top.php';

if (isset($_POST['etape']) && isset($_POST['id_test']) && isset($_POST['valeur'])) {
    $etape = (int)$_POST['etape'];
    $id_test = (int)$_POST['id_test'];
    $valeur = (int)$_POST['valeur'];
    
    if ($etape >= 1 && $etape <= 3 && $valeur >= 0 && $valeur <= 50) {
        
        $sql = '';
        switch ($etape) {
            case 1:
                $sql = 'UPDATE AW_test_lots 
                       SET date_etape_1 = NOW(), resultat_etape_1 = "'.($valeur*2).'" 
                       WHERE id = "'.$id_test.'"';
                break;
            case 2:
                $sql = 'UPDATE AW_test_lots 
                       SET date_etape_2 = NOW(), resultat_etape_2 = "'.($valeur*2).'" 
                       WHERE id = "'.$id_test.'"';
                break;
            case 3:
                $sql = 'UPDATE AW_test_lots 
                       SET date_etape_3 = NOW(), resultat_etape_3 = "'.($valeur*2).'" 
                       WHERE id = "'.$id_test.'"';
                break;
        }
        
        if ($sql && Db::getInstance()->execute($sql)) {
            echo 'ok';
        } else {
            echo 'error';
        }
    } else {
        echo 'invalid_params';
    }
} else {
    echo 'missing_params';
}
?>