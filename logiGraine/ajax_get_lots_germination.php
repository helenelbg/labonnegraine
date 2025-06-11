<?php
require 'application_top.php';

if (isset($_POST['id_product']) && !empty($_POST['id_product'])) {
    $id_product = (int)$_POST['id_product'];
    
    $sql = 'SELECT id_inventaire_lots, numero_lot_LBG, date_approvisionnement 
            FROM ps_inventaire_lots 
            WHERE id_product = "'.$id_product.'" 
            ORDER BY date_approvisionnement DESC';
    
    $lots = Db::getInstance()->executeS($sql);
    
    $options = '<option value="">-- Sélectionner un lot --</option>';
    
    foreach ($lots as $lot) {
        $date_formatted = '';
        if ($lot['date_approvisionnement'] != '0000-00-00' && !empty($lot['date_approvisionnement'])) {
            $date_formatted = ' ('.date('d/m/Y', strtotime($lot['date_approvisionnement'])).')';
        }
        
        $options .= '<option value="'.$lot['id_inventaire_lots'].'">';
        $options .= $lot['numero_lot_LBG'].$date_formatted;
        $options .= '</option>';
    }
    
    echo $options;
} else {
    echo '<option value="">-- Erreur --</option>';
}
?>