<?php
    session_start();

    include(dirname(__FILE__).'/../config/config.inc.php');
    require dirname(__FILE__).'/../init.php';

    include(dirname(__FILE__).'/classes/ClassOperateur.php');
    include(dirname(__FILE__).'/classes/ClassLBGModule.php');
    include(dirname(__FILE__).'/classes/ClassCommande.php');
    include(dirname(__FILE__).'/classes/ClassControle.php');
    include(dirname(__FILE__).'/classes/ClassControleProduit.php');
    include(dirname(__FILE__).'/classes/ClassCaisse.php');
    include(dirname(__FILE__).'/classes/ClassProduit.php');
    include(dirname(__FILE__).'/classes/ClassBoite.php');

    if ( isset($_GET['codePda']) && !empty($_GET['codePda']) )
    {
        $req_pda = new DbQuery();

        $req_pda->select('lgp.id_pda, lgp.nom_pda');
        $req_pda->from('LogiGraine_pda', 'lgp');
        $req_pda->where('lgp.code_pda = "'.$_GET['codePda'].'"');
        $resu_pda = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_pda);

        if ( isset($resu_pda[0]['id_pda']) && !empty($resu_pda[0]['id_pda']) )
        {
            $_SESSION['pda'] = $resu_pda[0]['id_pda'];
            $_SESSION['codePda'] = $_GET['codePda'];
            $_SESSION['nomPda'] = $resu_pda[0]['nom_pda'];
            
            header('Location: /LogiGraine/index.php');
        }
    }
    
    if ( ( !isset($_SESSION['pda']) || empty($_SESSION['pda']) ) && ( $_SERVER['SCRIPT_NAME'] != '/LogiGraine/cron_preparation.php' ) && ( $_SERVER['SCRIPT_NAME'] != '/LogiGraine/cron_sessions.php' ) && ( $_SERVER['SCRIPT_NAME'] != '/LogiGraine/calcul_reassort_pdt_cmd.php' ) && ( $_SERVER['SCRIPT_NAME'] != '/LogiGraine/calcul_reassort_pdt.php' ) )
    {
        die('<h1>Erreur PDA non reconnu</h1>');
    }

    /*if ( isset($_POST['codeOperateur']) && !empty($_POST['codeOperateur']) )
    {
        $req_connect = new DbQuery();

        $req_connect->select('lgo.id_operateur');
        $req_connect->from('LogiGraine_operateur', 'lgo');
        $req_connect->where('lgo.code_operateur = "'.$_POST['codeOperateur'].'"');
        $resu_connect = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_connect);

        if ( isset($resu_connect[0]['id_operateur']) && !empty($resu_connect[0]['id_operateur']) )
        {
            $_SESSION['operateur'] = $resu_connect[0]['id_operateur'];

            header('Location: /LogiGraine/accueil.php');
        }
    }*/
    if ( !isset($_SESSION['operateur']) || empty($_SESSION['operateur']) )
    {
        $req_connect = new DbQuery();
        $req_connect->select('lgpo.id_operateur');
        $req_connect->from('LogiGraine_pda_operateur', 'lgpo');
        $req_connect->where('lgpo.id_pda = "'.$_SESSION['pda'].'"');
        $resu_connect = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_connect);

        if ( isset($resu_connect[0]['id_operateur']) && !empty($resu_connect[0]['id_operateur']) )
        {
            $_SESSION['operateur'] = $resu_connect[0]['id_operateur'];

            header('Location: /LogiGraine/accueil.php');
        }
    }
    
    if ( isset($_GET['logoutLG']) )
    {
        $pdaEC = $_SESSION['codePda'];
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
        //session_regenerate_id(true);
        header('Location: /LogiGraine/index.php?codePda='.$pdaEC);
    }

    if ( isset($_SESSION['operateur']) && !empty($_SESSION['operateur']) )
    {
        if ( $_SERVER['SCRIPT_NAME'] == '/LogiGraine/index.php' )
        {
            header('Location: /LogiGraine/accueil.php');
        }
        $operateur = new Operateur($_SESSION['operateur']);
    }
    else 
    {
        if ( ( substr($_SERVER['SCRIPT_NAME'], 0, 21) != '/LogiGraine/index.php' ) && ( $_SERVER['SCRIPT_NAME'] != '/LogiGraine/calcul_reassort_pdt_cmd.php' ) && ( $_SERVER['SCRIPT_NAME'] != '/LogiGraine/cron_preparation.php' ) && ( $_SERVER['SCRIPT_NAME'] != '/LogiGraine/cron_sessions.php' ) && ( $_SERVER['SCRIPT_NAME'] != '/LogiGraine/calcul_reassort_pdt.php' ) )
        {
            die('<h1>Accès non autorisé</h1>');
        }
    }

    $statsAAtraiter = Commande::getATraiter();
?>