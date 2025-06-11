<?php

if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}
include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';

try {
       $bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
       die("probleme connexion serveur" . $ex->getMessage());
}

$sql = $bdd->prepare('SELECT commentaire FROM AW_test_lots WHERE id_lot = '.$_POST['lot'].' AND origine_test = "LBG" AND date_fin_test = "0000-00-00";');
$sql->execute();
$com = $sql->fetch();

echo '<div class="modal">';

echo '<p>Commentaires</p><p><textarea id="com_'.$_POST['id_product'].'" style="width: 435px;height: 100px;">'.$com['commentaire'].'</textarea></p>';
echo '<p><input type="button" value="Valider" onclick="majEtapeFinal('.$_POST['etape'].', '.$_POST['id_product'].', \''.$_POST['lot'].'\', \''.$_POST['valeur'].'\', \''.$_POST['termine'].'\', $(\'#com_'.$_POST['id_product'].'\').val());" /></p>';

echo '</div>';
?>
