<?php
define('PS_ADMIN_DIR', getcwd());
include(PS_ADMIN_DIR . '/config/config.inc.php');
/* Getting cookie or logout */
require_once(dirname(__FILE__) . '/init.php');
require_once(dirname(__FILE__) . '/swiftmailer/vendor/autoload.php');


$configuration = Configuration::getMultiple(array('PS_SHOP_EMAIL', 'PS_MAIL_METHOD', 'PS_MAIL_SERVER', 'PS_MAIL_USER', 'PS_MAIL_PASSWD', 'PS_SHOP_NAME', 'PS_MAIL_SMTP_ENCRYPTION', 'PS_MAIL_SMTP_PORT', 'PS_MAIL_METHOD', 'PS_MAIL_TYPE'));
$avant_m = date('m', strtotime('-1 month'));
$avant_y = date('Y', strtotime('-1 month'));
//$avant_m = date('m');
//$avant_y = date('Y');
$mois = array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
$avant_month = $mois[intval($avant_m)];
$txt_message = "Bonjour,<br /><br />Veuillez trouver ci-joint les exports comptables du mois précédent (".$avant_month." ".$avant_y.")."."<br /><br />Cordialement,<br /><br /><br /><i>[Message envoyé automatiquement via le site Internet]</i>";


/*$connection = new Swift_Connection_SMTP($configuration['PS_MAIL_SERVER'], $configuration['PS_MAIL_SMTP_PORT'], ($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'ssl') ? Swift_Connection_SMTP::ENC_SSL :(($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'tls') ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_OFF));
$connection->setUsername($configuration['PS_MAIL_USER']);
$connection->setPassword($configuration['PS_MAIL_PASSWD']);*/


$connection =(new Swift_SmtpTransport($configuration['PS_MAIL_SERVER'],$configuration['PS_MAIL_SMTP_PORT']));
$connection->setUsername($configuration['PS_MAIL_USER']);
$connection->setPassword($configuration['PS_MAIL_PASSWD']);
                Swift_Preferences::getInstance()->setCharset('utf-8'); 

                //Create the Mailer using your created Transport
                $swift = new Swift_Mailer($connection);

$connection->setTimeout(20);
$message = new Swift_Message('['.Configuration::get('PS_SHOP_NAME').'] Exports comptables '.$avant_month." ".$avant_y);
//$message->headers->setEncoding('Q');
//$swift = new Swift($connection,'');
//$lastday=strftime("%d/%m/%Y",mktime(0,0,0,$avant_m,2,$avant_y));
$lastday=strftime("%d/%m/%Y",mktime(0,0,0,$avant_m+1,0,$avant_y));
$firstday=strftime("%d/%m/%Y",mktime(0,0,0,$avant_m,1,$avant_y));

// $lastday = "31/03/2021"; //Modif Andy - test
// $firstday = "01/03/2021"; //Modif Andy - test

$lastday_str = str_replace("/", "-", $lastday);
$firstday_str = str_replace("/", "-", $firstday);
$message->setBody($txt_message, 'text/html');

//$result_file_1 = file_get_contents("https://dev.labonnegraine.com/export_compta.php?date_debut=".$firstday."&date_fin=".$lastday);
//$message->attach(new Swift_Message_Attachment($result_file_1, 'export_compta_du_'.$firstday_str.'_au_'.$lastday_str.'.csv', 'text/csv'));
$_GET['date_debut'] = $firstday;
$_GET['date_fin'] = $lastday;
include('export_compta_new.php');
$file_name1 = "exports_compta/export_compta_du_".$firstday_str."_au_".$lastday_str.".csv";
echo $file_name1;
//error_log($file_name1);
$message->attach(Swift_Attachment::fromPath($file_name1));

//$result_file_2 = file_get_contents("https://dev.labonnegraine.com/export_paiements.php?date_debut=".$firstday."&date_fin=".$lastday);
//$message->attach(new Swift_Message_Attachment($result_file_2, 'export_paiements_du_'.$firstday_str.'_au_'.$lastday_str.'.csv', 'text/csv'));

$_GET['date_debut'] = $firstday;
$_GET['date_fin'] = $lastday;
include('export_paiements.php');
$file_name2 = "exports_compta/export_paiements_du_".$firstday_str."_au_".$lastday_str.".csv";

//error_log($file_name2);

$message->attach(Swift_Attachment::fromPath($file_name2, 'export_paiements_du_'.$firstday_str.'_au_'.$lastday_str.'.csv', 'text/csv'));


$_GET['date_debut'] = $firstday;
$_GET['date_fin'] = $lastday;
include('export_ue.php');
$file_name3 = "exports_compta/export_CA_UE_du_".$firstday_str."_au_".$lastday_str.".csv";

//error_log($file_name2);

$message->attach(Swift_Attachment::fromPath($file_name3, 'export_CA_UE_du_'.$firstday_str.'_au_'.$lastday_str.'.csv', 'text/csv'));


$message->setFrom("info@labonnegraine.com"); // email d'envoi
$message->setSender('info@labonnegraine.com'); // email d'envoi
$message->setTo(array('guillaume@anjouweb.com','sgirard@asartis.com'));

//$send = $swift->send($message, new Swift_Address('sgirard@asartis.com'), new Swift_Address('info@labonnegraine.com', 'Stephane DI PALMA'));
$send = $swift->send($message);
//$send = $swift->send($message, new Swift_Address('andy@anjouweb.com'), new Swift_Address('info@labonnegraine.com', 'Stephane DI PALMA'));


?>
