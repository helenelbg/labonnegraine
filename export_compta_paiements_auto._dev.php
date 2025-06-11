<?php
define('PS_ADMIN_DIR', getcwd());
include(PS_ADMIN_DIR . '/config/config.inc.php');
/* Getting cookie or logout */
require_once(dirname(__FILE__) . '/init.php');
include_once(_PS_SWIFT_DIR_.'Swift.php');
include_once(_PS_SWIFT_DIR_.'Swift/Connection/SMTP.php');
include_once(_PS_SWIFT_DIR_.'Swift/Connection/NativeMail.php');
include_once(_PS_SWIFT_DIR_.'Swift/Plugin/Decorator.php');


//$configuration = Configuration::getMultiple(array('PS_SHOP_EMAIL', 'PS_MAIL_METHOD', 'PS_MAIL_SERVER', 'PS_MAIL_USER', 'PS_MAIL_PASSWD', 'PS_SHOP_NAME', 'PS_MAIL_SMTP_ENCRYPTION', 'PS_MAIL_SMTP_PORT', 'PS_MAIL_METHOD', 'PS_MAIL_TYPE'));
$avant_m = date('m', strtotime('-1 month'));
$avant_y = date('Y', strtotime('-1 month'));
$mois = array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"); 
$avant_month = $mois[intval($avant_m)];
//$txt_message = utf8_encode("Bonjour,<br /><br />Veuillez trouver ci-joint les exports comptables du mois précédent (".$avant_month." ".$avant_y.")."."<br /><br />Cordialement,<br /><br /><br /><i>[Message envoyé automatiquement via le site Internet]</i>");


/*$connection = new Swift_Connection_SMTP($configuration['PS_MAIL_SERVER'], $configuration['PS_MAIL_SMTP_PORT'], ($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'ssl') ? Swift_Connection_SMTP::ENC_SSL :(($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'tls') ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_OFF));
$connection->setUsername($configuration['PS_MAIL_USER']);
$connection->setPassword($configuration['PS_MAIL_PASSWD']);
$connection->setTimeout(20);
$message = new Swift_Message('['.Configuration::get('PS_SHOP_NAME').'] Exports comptables '.$avant_month." ".$avant_y);
$message->headers->setEncoding('Q');
$swift = new Swift($connection,'');*/
$lastday=strftime("%d/%m/%Y",mktime(0,0,0,$avant_m+1,0,$avant_y));
$firstday=strftime("%d/%m/%Y",mktime(0,0,0,$avant_m,1,$avant_y));

$lastday_str = str_replace("/", "-", $lastday);
$firstday_str = str_replace("/", "-", $firstday);
echo "lastday = ".$lastday;
echo "<br>";
echo "firstday = ".$firstday;

//$message->attach(new Swift_Message_Part($txt_message, 'text/html', '8bit', 'utf-8'));

$result_file_1 = file_get_contents("http://dev.labonnegraine.com/export_compta_dev.php?date_debut=".$firstday."&date_fin=".$lastday);
//$message->attach(new Swift_Message_Attachment($result_file_1, 'export_compta_du_'.$firstday_str.'_au_'.$lastday_str.'.csv', 'text/csv'));

/*$result_file_2 = file_get_contents("http://dev.labonnegraine.com/export_paiements.php?date_debut=".$firstday."&date_fin=".$lastday);
$message->attach(new Swift_Message_Attachment($result_file_2, 'export_paiements_du_'.$firstday_str.'_au_'.$lastday_str.'.csv', 'text/csv'));


//$send = $swift->send($message, new Swift_Address('stephane.dipalma@gmail.com', 'Stephane DI PALMA'), new Swift_Address('info@labonnegraine.com', 'Stephane DI PALMA'));
//$send = $swift->send($message, new Swift_Address('aurelien@anjouweb.com', 'Aurelien'), new Swift_Address('info@labonnegraine.com', 'Stephane DI PALMA'));

////$send = $swift->send($message, new Swift_Address('nbuffard@asartis.com', 'Nathalie BUFFARD'), new Swift_Address('info@labonnegraine.com', 'Stephane DI PALMA'));
//$send = $swift->send($message, new Swift_Address('fbodin@asartis.com', 'Fabrice BODIN'), new Swift_Address('info@labonnegraine.com', 'Stephane DI PALMA'));

$send = $swift->send($message, new Swift_Address('guillaume@anjouweb.com', 'Guillaume ANJOU WEB'), new Swift_Address('info@labonnegraine.com', 'Stephane DI PALMA'));
$swift->disconnect();
*/

?>