<?php 
require 'application_top.php';

$testEC = explode('_', $statsAAtraiter);

if ( $testEC[0] > 0 )
{
    $req_pda = 'SELECT id_pda FROM ps_LogiGraine_pda_operateur WHERE id_operateur = "'.$_POST['id_operateur'].'";';
    $resu_pda = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_pda);

    $req = 'CALL recuperer_groupe_commandes('.$resu_pda[0]['id_pda'].');';
    $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
//return true;
    $req_g = 'SELECT id_order FROM ps_LogiGraine_pda_order WHERE id_pda = "'.$resu_pda[0]['id_pda'].'" ORDER BY id_pda_order DESC LIMIT 0,1;';
    $resu_g = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_g);

    $explode_cmd = explode('_', $resu_g[0]['id_order']);

    $exeOK = true;
    foreach($explode_cmd as $multi)
    {
        // Vérification que la commande est bien en statut en cours paiement accepté
        $req_v = 'SELECT current_state FROM ps_orders WHERE id_order = "'.$multi.'";';
        $resu_v = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_v);
        if ( $resu_v[0]['current_state'] != 2 && $resu_v[0]['current_state'] != 47 )
        {
            $exeOK = false;
        }
    }

    if ( $exeOK == true )
    {
        foreach($explode_cmd as $cmd)
        {           
            $sqlUpd2 = 'UPDATE `' . _DB_PREFIX_ . 'orders`
            SET `current_state` = "46"
            WHERE  `id_order` = ' . $cmd;
            Db::getInstance()->execute($sqlUpd2);
        
            $sqlIns = 'INSERT INTO `' . _DB_PREFIX_ . 'order_history`
            SET `id_order` = "'.$cmd.'", id_order_state = "46", date_add = NOW();';
            Db::getInstance()->execute($sqlIns);

            $req_i = 'INSERT INTO ps_LogiGraine_controle SET id_order = '.$cmd.', id_operateur = '.$_POST['id_operateur'].';';
            $resu_i = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_i);
        }

        $expl_multi = explode('_', $resu_g[0]['id_order']);
        $liste_produits = Commande::getGroups($expl_multi, true);

        $rows3 = '';
        $locationEC = '';
        $ordreEC = 0;
        foreach($liste_produits as $ordreProduits)
        {
            foreach($ordreProduits['sequence'] as $seq)
            {
                foreach($seq['items'] as $prod)
                {
                    if ( !empty($rows3) )
                    {
                        $rows3 .= ',';
                    }
                    if ( $locationEC != $prod['location'])
                    {
                        $ordreEC++;
                        $locationEC = $prod['location'];
                    }
                    $rows3 .= '('.$prod['order_id'].', "'.$prod['location'].'", "'.$prod['ean'].'", '.$prod['quantity'].', '.$ordreEC.')';
                }
            }
            
            $req3 = 'INSERT IGNORE INTO ps_LogiGraine_controle_produit_ordre
            (id_order, location, ean, quantity, ordre)
            VALUES
            '.$rows3.';';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req3);
        }
    }
    else 
    {
        $req_d1 = 'DELETE FROM ps_LogiGraine_groupes_commandes WHERE id_order = "'.$resu_g[0]['id_order'].'";';
        $resu_d1 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_d1);

        $req_d = 'DELETE FROM ps_LogiGraine_pda_order WHERE id_order = "'.$resu_g[0]['id_order'].'";';
        $resu_d2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_d2);
    }
}