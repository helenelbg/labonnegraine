<?php

// Ce script vérifie si des tables MySQL sont bloquées (LOCK)
// Par Dorian BERRY-WEB, février 2022

// Procédure : 
// - modifier les variables de configuration, notamment les accès BDD
// - ajouter la library lib/swift_lib/
// - ajouter la cron dans la crontab, exemple toutes les 10 minutes : */10 * * * * curl http://www.artdubarbier.com/crons/cron_blocage_tables.php?token=dosmgjapcnrlgjsot

//require_once '../Swift/swift_required.php';

// vérification du token
if(!isset($_GET['token'])){
	exit;
}

if($_GET['token'] != "dosmgjapcnrlgjsot"){
	exit;
} 

// configuration, valeurs modifiables
require('../config/config.inc.php');

$mail_to = array('guillaume@anjouweb.com','contact@anjouweb.com');
$bdd_host = _DB_SERVER_;
$bdd_name = _DB_NAME_;
$bdd_user = _DB_USER_;
$bdd_pass = _DB_PASSWD_;
$mail_host = 'in.mailjet.com';
$mail_port = 587;
$mail_username = '34b10e378c3e0fa97459c5c143f5ec58';
$mail_password = '548160ec9d9e64da604c578c68636f08';
$compteur_pour_envoi_mail = 5; // mail auto
$compteur_pour_deblocage_auto = 10; // repair automatique
$compteur_pour_envoi_mail_regulier = 15; // mail tous les multiples de X détections de lock

// valeurs à ne pas modifier
$charset = 'utf-8';
$now = date('Y-m-d H:i:s');
$nom_table_array = array();
$send_mail = false;

// connexion bdd
try {
	
	$bdd = new PDO('mysql:host='.$bdd_host.';dbname='.$bdd_name,$bdd_user,$bdd_pass);

	echo "connexion ok <br><br>";
	
} catch (PDOException $e) {
    die("Erreur !: " . $e->getMessage());
}

// create table aw_blocage_tables_log
$sql = "CREATE TABLE IF NOT EXISTS aw_blocage_tables_log (
	id INT NOT NULL AUTO_INCREMENT, 
	nom_table TEXT, 
	date_blocage DATETIME,
	PRIMARY KEY (id) 
)";
$query = $bdd->prepare($sql);
$query->execute([]);

// create table aw_blocage_tables_compteur
$sql = "CREATE TABLE IF NOT EXISTS aw_blocage_tables_compteur (
	id INT NOT NULL AUTO_INCREMENT, 
	nom_table TEXT, 
	compteur INT,
	PRIMARY KEY (id) 
)";
$query = $bdd->prepare($sql);
$query->execute([]);

// check si blocage
$sql = "SHOW OPEN TABLES WHERE In_use > 0";
$query = $bdd->prepare($sql);
$query->execute([]);
$res = $query->fetchAll();
if(count($res)){
	$sujet = "Erreur BDD sur le serveur ".$_SERVER['HTTP_HOST'];
	$message = "Erreur BDD sur le serveur ".$_SERVER['HTTP_HOST']."<br>";
	$message .= "Nom de la BDD : ".$bdd_name."<br>";
	$message .= "Tables bloquées : <br>";
	foreach($res as $r){
		$nom_table = $r['Table'];
		$nom_table_array[] = $nom_table;
		
		
		// insert
		$sql = "INSERT INTO aw_blocage_tables_log (nom_table, date_blocage)
			VALUES (?,?)
		";
		$query = $bdd->prepare($sql);
		$query->execute([$nom_table,$now]);
		
		// insert / update
		$sql = "SELECT compteur FROM aw_blocage_tables_compteur 
		WHERE nom_table = ?";
		$query = $bdd->prepare($sql);
		$query->execute([$nom_table]);
		$res2 = $query->fetchAll();
		if(count($res2)){
			$compteur = $res2[0]['compteur'];
			$compteur++;
			
			// envoi de mail
			if($compteur == $compteur_pour_envoi_mail ){
				$limite = $compteur - 1;
				$sql =  "SELECT date_blocage FROM aw_blocage_tables_log 
				WHERE nom_table = '".$nom_table."' 
				ORDER BY date_blocage DESC 
				LIMIT ".$limite.",1;";
				$query = $bdd->prepare($sql);
				$query->execute([]);
				$res3 = $query->fetchAll();
				$dateheure = "";
				
				if(count($res3)){
					$dateheure = $res3[0]['date_blocage'];
					$datetime = new DateTime($dateheure);
					$dateheure = $datetime->format('d/m/Y H:i:s');
				}
		
				$message .= $nom_table . " - " . $compteur . " - depuis le " .$dateheure . "<br>";
				$send_mail = true;
			}
			
			// repair automatique + envoi de mail
			if($compteur >= $compteur_pour_deblocage_auto ){
				$limite = $compteur - 1;
				$sql =  "SELECT date_blocage FROM aw_blocage_tables_log 
				WHERE nom_table = '".$nom_table."' 
				ORDER BY date_blocage DESC 
				LIMIT ".$limite.",1;";
				$query = $bdd->prepare($sql);
				$query->execute([]);
				$res3 = $query->fetchAll();
				$dateheure = "";
				
				if(count($res3)){
					$dateheure = $res3[0]['date_blocage'];
					$datetime = new DateTime($dateheure);
					$dateheure = $datetime->format('d/m/Y H:i:s');
				}
				
				$sql = "REPAIR TABLE ?";
				$query = $bdd->prepare($sql);
				$query->execute([$nom_table]);
				
				$message .= $nom_table . " - " . $compteur . " - depuis le " .$dateheure . "<br>";
				$send_mail = true;
			}
			
			// mail tous les multiples de X détections de lock
			if($compteur % $compteur_pour_envoi_mail_regulier == 0){			
				$limite = $compteur - 1;
				$sql =  "SELECT date_blocage FROM aw_blocage_tables_log 
				WHERE nom_table = '".$nom_table."' 
				ORDER BY date_blocage DESC 
				LIMIT ".$limite.",1;";
				$query = $bdd->prepare($sql);
				$query->execute([]);
				$res3 = $query->fetchAll();
				$dateheure = "";
				
				if(count($res3)){
					$dateheure = $res3[0]['date_blocage'];
					$datetime = new DateTime($dateheure);
					$dateheure = $datetime->format('d/m/Y H:i:s');
				}
				
				$message .= $nom_table . " - " . $compteur . " - depuis le " .$dateheure . "<br>";
				$send_mail = true;
			}
			
			// update table
			$sql = "UPDATE aw_blocage_tables_compteur SET compteur = compteur + 1
			WHERE nom_table = ?
			";
			$query = $bdd->prepare($sql);
			$query->execute([$nom_table]);
		}else{
			$sql = "INSERT INTO aw_blocage_tables_compteur (nom_table, compteur)
			VALUES (?,?)
			";
			$compteur = 1;
			$query = $bdd->prepare($sql);
			$query->execute([$nom_table,$compteur]);
		}

	}
	$message .= "<br><br>Remarque : L'analyse se fait toutes les minutes. Ce mail a été envoyé car il y a eu plus de ".$compteur_pour_envoi_mail." locks consécutifs sur la même table. Au delà de ".$compteur_pour_deblocage_auto." locks, un REPAIR de la table en question est déclenché (suivi d'un mail de confirmation). Si une table reste encore lockée, un mail est envoyé toutes les ".$compteur_pour_envoi_mail_regulier." minutes.";
}



if($send_mail){
	send_mail_swift($mail_to, $sujet, $message, $charset, $mail_host, $mail_port, $mail_username, $mail_password );
}

// remise à zéro des compteurs
$where_clause = "";
foreach($nom_table_array as $n){
	if(!$where_clause){
		$where_clause = " WHERE nom_table <> ?";
	}else{
		$where_clause .= " AND nom_table <> ?";
	}
}
$sql = "UPDATE aw_blocage_tables_compteur SET compteur = 0";
$sql .= $where_clause;
$query = $bdd->prepare($sql);
$query->execute($nom_table_array);


function send_mail_swift($to_array, $sujet, $message, $charset, $mail_host, $mail_port, $mail_username, $mail_password ){
	
	$email_from = "contact@labonnegraine.com";

	$entetes = '';
    $entetes .= "From: " . $email_from  . " <" . $email_from  . ">\n";
    $entetes .= "Reply-To: " . $email_from  . "\n";
    $entetes .= "X-Sender: <" . $email_from  . ">\n";
    $entetes .= "X-Mailer: PHP\n";
    $entetes .= "X-Priority: \n";
    $entetes .= "Return-Path: <" . $email_from  . ">\n";
    $entetes .= "Content-Type: text/html; charset=" . $charset . "\n";
    $entetes .='MIME-Version: 1.0';
    $entetes .='Content-Transfer-Encoding: quoted-printable';

    $transport = Swift_SmtpTransport::newInstance($mail_host, $mail_port)
            ->setUsername($mail_username)
            ->setPassword($mail_password);

    Swift_Preferences::getInstance()->setCharset($charset);
    $mailer = Swift_Mailer::newInstance($transport);

    try
	{
	// envoi du mail
	$messageEnvoi = Swift_Message::newInstance($sujet)
			->setFrom(array($email_from  => $email_from ))
			->setTo($to_array)
			->setBody($message, "text/html");
	$mailer->send($messageEnvoi);
	}
	catch (Swift_TransportException $e){
		
	}

}
