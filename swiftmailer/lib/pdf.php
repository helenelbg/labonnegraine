<?
require_once('html2fpdf.php');
// activate Output-Buffer:
ob_start();

include ('../connexion/aool_connexion.php');

$req_echantillon = 'SELECT * FROM echantillons WHERE id_echantillon = "'.$id.'";';
$resu_echantillon = mysql_query ( $req_echantillon, $connexion );
//echo $req_echantillon;
$rangee_echantillon = mysql_fetch_array ( $resu_echantillon );

$req_cuvee = 'SELECT * FROM cuvees WHERE id_cuvee = "'.$rangee_echantillon['id_cuvee'].'";';
$resu_cuvee = mysql_query ( $req_cuvee, $connexion );
$rangee_cuvee = mysql_fetch_array ( $resu_cuvee );

$req_client = 'SELECT * FROM viticulteurs WHERE id_viticulteur = "'.$rangee_cuvee['id_viticulteur'].'";';
$resu_client = mysql_query ( $req_client, $connexion );
$rangee_client = mysql_fetch_array ( $resu_client );

echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 WIDTH=710>';
echo '<TR>';
echo '<TD VALIGN=top ALIGN=center WIDTH=300><img src="http://www.oeno-labo.com/images/logo_AOOL_fond_blanc_200_300.jpg" WIDTH=300></TD>';
echo '<TD>&nbsp;</TD>';
echo '<TD>Oeno-Labo<BR>4, rue du stade<BR>49260 Le Puy Notre Dame<BR>Tel : 02.41.52.42.45<br />Fax : 02.41.52.66.41<br />http://www.oeno-labo.com/aool_client</TD>';
echo '</TR>';
echo '</TABLE>';

echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=1 CLASS=bord WIDTH=710>';
echo '<TR>';
echo '<TD>&nbsp;</TD>';
echo '</TR>';
echo '</TABLE>';
echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 CLASS=bord WIDTH=710>';
echo '<TR>';
echo '<TD>&nbsp;</TD>';
echo '</TR>';
echo '</TABLE>';
echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 WIDTH=710>';
echo '<TR><TD>&nbsp;</TD></TR>';
echo '<TR>';
echo '<TD VALIGN=top ALIGN=center WIDTH=300><B><FONT SIZE=5>RAPPORT D\'ANALYSE</FONT></B></TD>';
echo '<TD>&nbsp;</TD>';
echo '<TD>'.$rangee_client['societe_viticulteur'].'<BR>'.$rangee_client['adresse1_viticulteur'].'<BR>'.$rangee_client['adresse2_viticulteur'].'<BR>'.$rangee_client['cp_viticulteur'].' '.$rangee_client['ville_viticulteur'].'</TD>';
echo '</TR>';
$date = explode ( "-", $rangee_echantillon['date_lancement_echantillon'] );
$jour_r = substr ( $rangee_echantillon['date_lancement_echantillon'], 6, 2 );
$mois_r = substr ( $rangee_echantillon['date_lancement_echantillon'], 4, 2 );
$annee_r = substr ( $rangee_echantillon['date_lancement_echantillon'], 0, 4 );

$mois['01'] = 'Janvier';
$mois['02'] = 'FÈvrier';
$mois['03'] = 'Mars';
$mois['04'] = 'Avril';
$mois['05'] = 'Mai';
$mois['06'] = 'Juin';
$mois['07'] = 'Juillet';
$mois['08'] = 'Ao˚t';
$mois['09'] = 'Septembre';
$mois['10'] = 'Octobre';
$mois['11'] = 'Novembre';
$mois['12'] = 'DÈcembre';

$mois_bis[1] = 'Janvier';
$mois_bis[2] = 'FÈvrier';
$mois_bis[3] = 'Mars';
$mois_bis[4] = 'Avril';
$mois_bis[5] = 'Mai';
$mois_bis[6] = 'Juin';
$mois_bis[7] = 'Juillet';
$mois_bis[8] = 'Ao˚t';
$mois_bis[9] = 'Septembre';
$mois_bis[10] = 'Octobre';
$mois_bis[11] = 'Novembre';
$mois_bis[12] = 'DÈcembre';

echo '<TR><TD>DÈbut des analyses : '.$jour_r.' '.$mois[$mois_r].' '.$annee_r.'</TD></TR>';
echo '</TABLE>';

/*echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 CLASS=bord WIDTH=710>';
echo '<TR>';
echo '<TD>&nbsp;</TD>';
echo '</TR>';
echo '</TABLE>';
echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 CLASS=bord WIDTH=710>';
echo '<TR>';
echo '<TD>&nbsp;</TD>';
echo '</TR>';
echo '</TABLE>';*/
echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 CLASS=bord WIDTH=710>';
echo '<TR>';
echo '<TD>&nbsp;</TD>';
echo '</TR>';
echo '</TABLE>';

echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=1 WIDTH=500 ALIGN=center>';
	echo '<TR><TD WIDTH=25%><B>Echantillon(s)</B></TD><TD WIDTH=60% ALIGN=center><B>'.$rangee_echantillon['reference_echantillon'].'</B></TD></TR>';
	echo '<TR><TD WIDTH=25%><B>CuvÈe(s)</B></TD><TD WIDTH=60% ALIGN=center>'.$rangee_cuvee['appellation_cuvee'].'</TD></TR>';

$req_ordre = 'SELECT * FROM tests ORDER BY ordre ASC;';
$resu_ordre = mysql_query ( $req_ordre, $connexion );
while ( $rangee_ordre = mysql_fetch_array ( $resu_ordre ) )
{
	$liste_analyse = explode (";", $rangee_echantillon['tests_echantillon']);
	reset($liste_analyse);
	while (list($cle, $valeur) = each($liste_analyse))
	{	
		if ( $valeur == $rangee_ordre['id_test'] )
		{
			if ( $liste_analyse2 == '' )
			{
				$liste_analyse2	= $valeur;
			}
			else
			{
				$liste_analyse2 .= ';'.$valeur;
			}
		}
	}
}

$liste_analyse3 = explode (";", $liste_analyse2);
reset($liste_analyse3 );
while (list($cle2, $valeur2) = each($liste_analyse3))
{
	$req_t = 'SELECT * FROM tests WHERE id_test="'.$valeur2.'";';
	$resu_t = mysql_query ( $req_t, $connexion );
	$rangee_t = mysql_fetch_array ( $resu_t );
        
        if ( ( ( $valeur2 == 2 ) && ( $rangee_echantillon['glu_fruc'] > 2 ) ) || ( $valeur2 == 3 ) )
       	{
      		if ( $affiche == '1' )
      		{
     			$affiche = 'ok';
      		}
      		if ( $affiche == '' )
      		{
     			$affiche = '1';
      		}
                                                                        		
       	}
        if ( ( $valeur2 == 15 ) || ( $valeur2 == 11 ) || ( $valeur2 == 6 ) )
        {
          if ( $ok == '' )
          {
              if ( $rangee_echantillon['ntu_difference'] != '' )
			  {
				  $rangee_echantillon['ntu_difference'] .= ' NTU';
			  }
			  echo '<TR><TD>ProtÈÔnes</TD><TD>'.htmlentities($rangee_echantillon['ntu_difference']).'</TD></TR>';
              $ok = 'ok';
              //echo '<TR><TD>ProtÈÔnes</TD><TD>'.$ok.'</TD></TR>';
          }
       	}
		else if ( $valeur2 == 34 )
       	{

		}
       	else
       	{
	    if ( $rangee_echantillon[$rangee_t['correspondance']] == '0' )
            {
              $rangee_echantillon[$rangee_t['correspondance']] = 'O';
            }
            echo '<TR><TD VALIGN=TOP>'.$rangee_t['libelle_test'];
	    if ( $rangee_t['unite'] != '' )
	    {
	    	if ( ( $rangee_t['unite'] == '% VOL' ) || ( $rangee_t['unite'] == 'kg de CO2 / cm≤ ‡ 20∞C' ) || ( $rangee_t['unite'] == 'aprËs hydrolyse en g/L' ) )
	    	{
				echo '<br />('.$rangee_t['unite'].')';
			}
			else
			{
				echo ' ('.$rangee_t['unite'].')';
			}
	    }
	    echo '</TD><TD>'.htmlentities($rangee_echantillon[$rangee_t['correspondance']]).'</TD></TR>';

            if ( $affiche == 'ok' )
	    {
               echo '<TR><TD VALIGN=TOP>Alcool en puissance<br />(% VOL)</TD><TD>'.htmlentities($rangee_echantillon['alcool_puissance']).'</TD></TR>';
               echo '<TR><TD VALIGN=TOP>Alcool total<br />(% VOL)</TD><TD>'.htmlentities($rangee_echantillon['alcool_total']).'</TD></TR>';
               $affiche = 3;
           }
        }
}

echo '</TABLE>';
echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 CLASS=bord WIDTH=710>';
echo '<TR>';
echo '<TD>&nbsp;</TD>';
echo '</TR>';
echo '</TABLE>';
echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 WIDTH=710>';
echo '<TR><TD><U><B>Conseil client :</B></U>  '.htmlentities($rangee_echantillon['conseils_echantillon']);
if ( in_array('3', $liste_analyse3) )
{
	if ( $rangee_echantillon['alcool'] < 9 )
	{
		echo '<br />Alcool acquis trop faible pour une bonne exactitude des rÈsultats.';
	}
}
echo '</TD></TR>';
echo '</TABLE>';

$jour_now = date('d');
$mois_now = date('n');
$annee_now = date('Y');
$date_now = $jour_now.' '.$mois_bis[$mois_now].' '.$annee_now;
echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 CLASS=bord WIDTH=710>';
echo '<TR>';
echo '<TD>&nbsp;</TD>';
echo '</TR>';
echo '</TABLE>';
echo '<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 WIDTH=710>';
echo '<TR><TD>&nbsp;</TD><TD align=center><BR>&nbsp;<BR>Fait le '.$date_now.'</TD></TR>';
echo '<TR><TD>&nbsp;</TD><TD align=center><BR>&nbsp;<BR>Au Puy Notre Dame</TD></TR>';
echo '<TR>';
echo '<TD>&nbsp;</TD>';
echo '</TR>';
echo '</TABLE>';

// Output-Buffer in variable:
$html=ob_get_contents();
// delete Output-Buffer
ob_end_clean();
$pdf = new HTML2FPDF();
$pdf->DisplayPreferences('HideWindowUI');
$pdf->AddPage();
$pdf->WriteHTML($html);
			
			$conn_id = ftp_connect('www.oeno-labo.com',21);
			$login_result = ftp_login($conn_id, 'aool', 'Kjb58p2' );
			//echo $conn_id . ' ' . $user . ' ' . $password;
			// Ouverture du rÈpertoire en Ècriture via une commande FTP
			$permission = decoct(0777) ;
			$chmod_cmd = 'CHMOD '.$permission.' /web/aool_client/pdf' ;
			$chmod = ftp_site( $conn_id, $chmod_cmd);
			
			$mois_now_2 = date(m);
			$date_now_2 = $annee_now.$mois_now_2.$jour_now;
			//echo $date_now_2;
	
        		$nom_viti = strtr($rangee_client['societe_viticulteur'], "¿¡¬√ƒ≈‡·‚„‰Â“”‘’÷ÿÚÛÙıˆ¯»… ÀËÈÍÎ«ÁÃÕŒœÏÌÓÔŸ⁄€‹˘˙˚¸ˇ—Ò '\\*?/\"<>|&!","AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn------------" ) ;

                        $pdf->Output('../../aool_client/pdf/'.$nom_viti.'_'.$rangee_client['id_viticulteur'].'_'.$rangee_echantillon['id_echantillon'].'_'.$date_now_2.'.pdf','F');
			
			// Ouverture du rÈpertoire en Ècriture via une commande FTP
			$permission = decoct(0755) ;
			$chmod_cmd = 'CHMOD '.$permission.' /web/aool_client/pdf' ;
			$chmod = ftp_site( $conn_id, $chmod_cmd);
			// Fermeture de la connexion FTP
			ftp_close($conn_id);
			
			$req_date = 'UPDATE echantillons SET date_rapport = "'.$annee_now.$mois_now_2.$jour_now.'" WHERE id_echantillon = "'.$rangee_echantillon['id_echantillon'].'";';
			$resu_date = mysql_query ( $req_date, $connexion );
			
			$req_delete = 'DELETE FROM rapports WHERE nom_rapport = "'.$nom_viti.'_'.$rangee_client['id_viticulteur'].'_'.$rangee_echantillon['id_echantillon'].'_'.$date_now_2.'.pdf";';
			$resu_delete = mysql_query ( $req_delete, $connexion );
			
                        $req_delete2 = 'DELETE FROM rapports_viti WHERE nom_rapport = "'.$nom_viti.'_'.$rangee_client['id_viticulteur'].'_'.$rangee_echantillon['id_echantillon'].'_'.$date_now_2.'.pdf";';
			$resu_delete2 = mysql_query ( $req_delete2, $connexion );

              $req_contacts = 'SELECT * FROM contacts WHERE id_viticulteur = "'.$rangee_client['id_viticulteur'].'";';
              $resu_contacts = mysql_query ( $req_contacts, $connexion );
              while ( $rangee_contacts = mysql_fetch_array ( $resu_contacts ) )
              {
                    // Envoi du message

                    // Initialisation des variables
                    $destinataire = "";
                    $message = "";
                    $entetes = "";
                    $expediteur = "";
                    // destinataire
                    $destinataire = $rangee_contacts['nom_contact'] . " " . $rangee_contacts['prenom_contact'] . " <".$rangee_contacts['email_contact'].">";
                    $expediteur = "contact@oeno-labo.com";
                    $mail_erreur = "contact@oeno-labo.com";
                    // sujet
                    $sujet = 'Nouveau Rapport';

                    // message
                    $message .= "<TABLE WIDTH='640' ALIGN=center><TR><TD><CENTER><A HREF='http://www.oeno-labo.com/aool_client/login.php'><IMG src='http://www.oeno-labo.com/aool_client/images/bandeau_haut.jpg' BORDER=0></A>";
                    $message .= '<BR><BR><B>Un nouveau rapport est disponible. <BR><BR>Rendez-vous sur <A HREF="http://www.oeno-labo.com/aool_client/login.php"><B>votre espace client Oeno-Labo</B></A><BR><BR>Cordialement,<BR>Oeno-Labo</B><BR><BR>';
                    $message .= "<A HREF='http://www.oeno-labo.com/aool_client/login.php'><IMG src='http://www.oeno-labo.com/aool_client/images/bandeau_bas.jpg' BORDER=0></A></CENTER></TD></TR></TABLE>";

                    // D'autres en-tÍtes : errors, From cc's, bcc's, etc
                    $entetes .= "From: Oeno-Labo <".$expediteur.">\n";
                    $entetes .= "Reply-To: ".$expediteur."\n";
                    $entetes .= "X-Sender: <".$expediteur.">\n";
                    $entetes .= "X-Mailer: PHP\n";                               // maileur
                    $entetes .= "X-Priority: 1\n";                               //  Message urgent!
                    $entetes .= "Return-Path: <".$mail_erreur.">\n";             // Re-chemin de retour pour les erreurs
                    $entetes .= "Content-Type: text/html; charset=iso-8859-1\n"; // Type MIME

                    require_once '../../lib/swift_required.php';

        	//Create the Transport
        	$transport = Swift_SmtpTransport::newInstance('smtp.orange.fr', 587)
        	  //->setUsername('oeno-labo@orange.fr')
        	  ->setUsername('oenolabo@orange.fr')
        	  //->setPassword('hkefxh4')
        	  ->setPassword('epbghvk')
        	  ;

        	//Create the Mailer using your created Transport
        	$mailer = Swift_Mailer::newInstance($transport);

                //Create the Mailer using your created Transport
        	$mailer2 = Swift_Mailer::newInstance($transport);
             
			if ( $rangee_contacts['email_contact'] != '' )
                {
        	//Create a message
        	$messageEnvoi = Swift_Message::newInstance('Nouveau Rapport')
        	  ->setFrom(array($expediteur => 'Oeno Labo'))
        	  ->setTo(array($rangee_contacts['email_contact'] => $rangee_contacts['nom_contact'] . ' ' . $rangee_contacts['prenom_contact']))
        	  ->setBody($message, "text/html")
        	  ;
				}
                  $req_oeno = 'SELECT * FROM oenologues WHERE id_oenologue = "'.$rangee_client['id_oenologue'].'";';
                    $resu_oeno = mysql_query ( $req_oeno, $connexion );
                    $rangee_oeno = mysql_fetch_array ( $resu_oeno );


                if ( $rangee_oeno['email_oenologue'] != '' )
                {
        	  //Create a message
              	  $messageEnvoiOeno = Swift_Message::newInstance('Nouveau Rapport')
        	    ->setFrom(array($expediteur => 'Oeno Labo'))
        	    ->setTo(array($rangee_oeno['email_oenologue'] => 'Oenologue'))
        	    ->setBody($message, "text/html")
        	    ;
   	        }

                if ( $rangee_contacts['email_contact'] != '' )
                {
      	               //Send the message
      	               $result = $mailer->send($messageEnvoi);

                       //$retour_mail = mail($destinataire, $sujet, $message, $entetes);
		}

                    if ( $rangee_oeno['email_oenologue'] != '' )
                    {
                       //Send the message
      	               $result2 = $mailer2->send($messageEnvoiOeno);

                       //$retour_mail2 = mail($rangee_oeno['email_oenologue'], $sujet, $message, $entetes);
                    }
              }
              
              // INSERTION DU RAPPORT DANS LA BASE DE DONNEES DANS LA TABLE PDF ET PDF_ECH
              // RÈcupÈration de l'id max
              $req_v = 'SELECT * FROM pdf WHERE chemin_pdf = "'.$nom_viti.'_'.$rangee_client['id_viticulteur'].'_'.$rangee_echantillon['id_echantillon'].'_'.$date_now_2.'.pdf";';
              $resu_v = mysql_query ( $req_v, $connexion );
              $rangee_v = mysql_fetch_array ( $resu_v );

              if ( $rangee_v['id_pdf'] == '' )
              {
		              $req_max = 'SELECT max(id_pdf) AS max_id FROM pdf;';
		              $resu_max = mysql_query ( $req_max, $connexion );
		              $rangee_max = mysql_fetch_array ( $resu_max );
		
		              $req_pdf = 'INSERT INTO pdf (id_pdf, id_viticulteur, chemin_pdf, date_creation) VALUES ("'.($rangee_max['max_id']+1).'", "'.$rangee_client['id_viticulteur'].'", "'.$nom_viti.'_'.$rangee_client['id_viticulteur'].'_'.$rangee_echantillon['id_echantillon'].'_'.$date_now_2.'.pdf", "'.$date_now_2.'");';
		              $resu_pdf = mysql_query ( $req_pdf, $connexion );
		              
		              $req_pdfech = 'INSERT INTO pdf_ech (id_pdf, id_ech) VALUES ("'.($rangee_max['max_id']+1).'", "'.$rangee_echantillon['id_echantillon'].'");';
					  $resu_pdfech = mysql_query ( $req_pdfech, $connexion );
			  }
?>
<script language="javascript" type="text/javascript">
<!--
        <?php
             if ( $rangee_client['ouverture'] == 1 )
             {
               ?>
        window.open("http://www.oeno-labo.com/aool_client/pdf/<?php echo $nom_viti.'_'.$rangee_client['id_viticulteur'].'_'.$rangee_echantillon['id_echantillon'].'_'.$date_now_2.'.pdf';?>","popup","width=800, height=700");
                <?php
             }
             ?>   
        window.location.replace("http://www.oeno-labo.com/aool/detail_echantillon.php?page=echantillons&id=<?php echo $rangee_echantillon['id_echantillon']; ?>");
-->
</script>