<?php
    session_start();

    include(dirname(__FILE__).'/../../config/config.inc.php');
    require dirname(__FILE__).'/../../init.php';

    include(dirname(__FILE__).'/classes/ClassAdmin.php');
    include(dirname(__FILE__).'/classes/ClassLBGModuleAdmin.php');
    include(dirname(__FILE__).'/classes/ClassCommande.php');
    include(dirname(__FILE__).'/../classes/ClassControle.php');

    if ( isset($_POST['emailAdmin']) && !empty($_POST['emailAdmin']) && isset($_POST['pswAdmin']) && !empty($_POST['pswAdmin']) )
    {
        $req_connect = new DbQuery();

        $req_connect->select('lga.id_admin');
        $req_connect->from('LogiGraine_admin', 'lga');
        $req_connect->where('lga.email_admin = "'.$_POST['emailAdmin'].'"');
        $req_connect->where('lga.psw_admin = MD5("'.$_POST['pswAdmin'].'")');
        $resu_connect = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_connect);

        if ( isset($resu_connect[0]['id_admin']) && !empty($resu_connect[0]['id_admin']) )
        {
            $_SESSION['admin'] = $resu_connect[0]['id_admin'];

            header('Location: /LogiGraine/admin/accueil.php');
        }
    }
    
    if ( isset($_GET['logoutLG']) )
    {
        session_unset();
        session_destroy();
        session_write_close();
        setcookie(session_name(), '', 0, '/');
        //session_regenerate_id(true);
        header('Location: /LogiGraine/admin/index.php');
    }

    if ( isset($_SESSION['admin']) && !empty($_SESSION['admin']) )
    {
        if ( $_SERVER['SCRIPT_NAME'] == '/LogiGraine/admin/index.php' )
        {
            header('Location: /LogiGraine/admin/accueil.php');
        }
        $admin = new Admin($_SESSION['admin']);
    }
    else 
    {
        if ( substr($_SERVER['SCRIPT_NAME'], 0, 27) != '/LogiGraine/admin/index.php' )
        {
            header('Location: /LogiGraine/admin/index.php');
        }
    }
?>