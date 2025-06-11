<?php

//  Récapitulatif des reliquats mois par mois + alerte mail cron au 1er du mois sur ce qui est encore en reliquat sur tous les mois précédents

if ( $_GET['token'] != 'hdf6dfdfs6ddgs' )
{
    die;
}

include_once '../config/config.inc.php';
include_once '../config/settings.inc.php';
include_once '../init.php';
include_once 'util.php';



try {
	$bdd = new PDO("mysql:host=" . _DB_SERVER_ . ";dbname=" . _DB_NAME_, _DB_USER_, _DB_PASSWD_);
} catch (exeption $ex) {
	die("probleme connexion serveur" . $ex->getMessage());
}

$now = date('Y-m-d');

$sql = 'SELECT fd.id_cmd, s.name as nom_fournisseur, fd.id_produit, p.reference, pl.name, fd.qte, fd.unite, fd.date_reliquat FROM cmd_fournisseur_detail fd
LEFT JOIN ps_product p ON fd.id_produit = p.id_product
LEFT JOIN ps_product_lang pl ON fd.id_produit = pl.id_product
LEFT JOIN cmd_fournisseur cf ON (cf.id_cmd = fd.id_cmd)
LEFT JOIN ps_supplier s ON (cf.id_fournisseur = s.id_supplier)
WHERE pl.id_lang = 1 AND fd.id_etat = 4 AND date_reliquat < "'.$now.'"
ORDER BY fd.date_reliquat DESC';
$res = Db::getInstance()->ExecuteS($sql);

$str = '<table class="table_reliquat" style="border-collapse: collapse;">
	<thead>
		<tr>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;" >Commande</th>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">Réference</th>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">Nom</th>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">Quantité</th>
			<th style="border: 1px solid #7A7A7A; padding:2px 10px;">Date reliquat</th>
		</tr>
	</thead>
	<tbody class="table_reliquat_tbody">';

$old_date_display = "";

foreach($res as $r){
	
	$reliquat_datetime = new DateTime($r['date_reliquat']);
	$date_display = $reliquat_datetime->format('m / Y');
	if($old_date_display != $date_display){
		$old_date_display = $date_display;
		$str .= '<tr>';
		$str .= '<td colspan=5 style="border: 1px solid #7A7A7A; padding:20px 10px; text-align: center; font-size: 18px;">'.$date_display.'</td>';
		$str .= '</tr>';
	}
	$str .= '<tr>';
	$str .= '<td style="border: 1px solid #7A7A7A; padding:2px 10px;">'.$r['id_cmd'].' ('.$r['nom_fournisseur'].')</td>';
	$str .= '<td style="border: 1px solid #7A7A7A; padding:2px 10px;">'.$r['reference'].'</td>';
	$str .= '<td style="border: 1px solid #7A7A7A; padding:2px 10px;">'.$r['name'].'</td>';
	$str .= '<td style="border: 1px solid #7A7A7A; padding:2px 10px;">'.$r['qte'].' '.$r['unite'].'</td>';
	$str .= '<td style="border: 1px solid #7A7A7A; padding:2px 10px;">'.$date_display.'</td>';
	$str .= '</tr>';
}	

$str .= '</tbody>
</table>';

echo $str;




// envoi un mail 
		
$sujet = 'La Bonne Graine : mail reliquat';

$message = $str;

$email_to = '';
//$email_to = 'dorian@berry-web.com';
$email_to = 'stephane.dipalma@gmail.com';

$message .= '<a href="https://dev.labonnegraine.com/lbgest/reliquat.php?token=hdf6dfdfs6ddgs">Pour accéder au module conditionnement, rubrique "reliquat", cliquez ici.</a>';

if($_SERVER['HTTP_HOST'] == "dev.labonnegraine.com"){
	//$email_to = 'dorian@berry-web.com';
	$message .= "<br>Ceci est un email de test";
}

if($email_to){
	require_once '../Swift/swift_required.php';
	$transport = Swift_SmtpTransport::newInstance('in-v3.mailjet.com',587)
	->setUsername('34b10e378c3e0fa97459c5c143f5ec58')
	->setPassword('548160ec9d9e64da604c578c68636f08');
	Swift_Preferences::getInstance()->setCharset('utf-8');
	$mailer = Swift_Mailer::newInstance($transport);
	$messageEnvoi = Swift_Message::newInstance($sujet)
	->setFrom('info@labonnegraine.com') 
	->setTo($email_to)
	->setBody($message, "text/html");
	$result = $mailer->send($messageEnvoi);
	echo '<br>mail envoyé à '.$email_to;
}



?>

