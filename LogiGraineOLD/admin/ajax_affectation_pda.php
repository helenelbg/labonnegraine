<?php
    require 'application_top.php';

    if ( isset($_POST['id_pda']) && !empty($_POST['id_pda']) )
    {
        $req = 'DELETE FROM ps_LogiGraine_pda_order WHERE id_order IN ('.implode(',', $_POST['orders']).');';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);
        
        $req2 = 'DELETE FROM ps_LogiGraine_controle WHERE id_order IN ('.implode(',', $_POST['orders']).');';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2);
        
        if ( $_POST['id_pda'] > 0 )
        {
            $rows = '';
            $rows2 = '';
            foreach($_POST['orders'] as $ordEC)
            {
                if ( !empty($rows) )
                {
                    $rows .= ',';
                    $rows2 .= ',';
                }
                $rows .= '('.$_POST['id_pda'].', '.$ordEC.')';
                $rows2 .= '('.$ordEC.', 0, 0, "0000-00-00 00:00:00", "0000-00-00 00:00:00", 0, "", 0)';
            }
            $req = 'INSERT IGNORE INTO ps_LogiGraine_pda_order
                    (id_pda,id_order)
                    VALUES
                    '.$rows.';';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req);

            $req2 = 'INSERT IGNORE INTO ps_LogiGraine_controle
                    (id_order, id_operateur, id_caisse, date_debut, date_fin, valide, zone, transport)
                    VALUES
                    '.$rows2.';';
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2);
        }
    }
?>