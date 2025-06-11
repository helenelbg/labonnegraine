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


        echo '<html lang="en">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>La Bonne Graine - Besoins en conditionnement</title>
          <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
          <link rel="stylesheet" href="/resources/demos/style.css">
          <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
          <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
          <script src="https://unpkg.com/sticky-table-headers"></script>
          <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
          <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
          <script>
          $( function() {
            $( "#tabs" ).tabs();
            $("table").stickyTableHeaders();
          } );

          $(document).ready(function () {

            $(\'input\').keypress(function (e) {

                var charCode = (e.which) ? e.which : event.keyCode

                if (String.fromCharCode(charCode).match(/[^0-9]/g))
                    return false;

            });

            $(\'input\').keyup(function (e) {
                var split = $(this).attr(\'id\').split(\'_\');
                if ( $(\'#tamp_\'+split[1]).val() == \'\' || $(\'#prod_\'+split[1]).val() == \'\' )
                {
                    console.log(\'vide\');
                    $(\'#val_tamp_\'+split[1]).css(\'display\', \'none\');
                }
                else
                {
                    console.log(\'ok\');
                    $(\'#val_tamp_\'+split[1]).css(\'display\', \'inline-block\');
                }
            });
        });

          function majProduit(id_product, id_product_attribute, sachet, tampon)
          {
            $.ajax({
                method: "POST",
                url: "majProduit.php?token=hdf6dfdfs6ddgs",
                data: { id_product: id_product, id_product_attribute: id_product_attribute, sachet: sachet, tampon: tampon }
              })
                .done(function( msg ) {
                    $(\'#tamp_\'+id_product_attribute).val(\'\');
                    $(\'#prod_\'+id_product_attribute).val(\'\');
                    $(\'#val_tamp_\'+id_product_attribute).css(\'display\', \'none\');
                    $(\'#tr_\'+id_product_attribute).css(\'display\', \'none\');
                });
          }
          </script>
          <style>
          .ui-widget {
            font-family: \'Open Sans\', sans-serif !important;
          }
          </style>
        </head>
        <body style="font-family: \'Open Sans\', sans-serif !important;">';

          echo '<h1 style="text-align: center;"><img src="/img/logo135.png" style="vertical-align: middle;" />&nbsp;Conditionnement</h1>';
		  echo '<div style="margin-bottom:15px"><a href="parametres.php?token='.$_GET['token'].'">Paramètres</a></div>';

        echo '<div id="tabs">
        <ul>';
        $tab_decli = array();
        $requete = $bdd->prepare('SELECT DISTINCT(ordre) FROM operationnel ORDER BY ordre ASC;');
        $requete->execute() or die(print_r($requete->errorInfo()));

        while (($ligne = $requete->fetch()))
        {
            if ( $ligne['ordre'] == 1 )
            {
                $affiche = "Déclinaison par défaut";
            }
            else
            {
                $affiche = $ligne['ordre']."ème déclinaison";
            }
          echo '<li><a href="#tab'.$ligne['ordre'].'">'.$affiche.'</a></li>';
          $tab_decli[] = $ligne['ordre'];
        }
        echo '</ul>';
        foreach($tab_decli as $liste)
        {
        echo '<div id="tab'.str_replace(' ','', $liste).'">';
        //echo 'SELECT * FROM operationnel WHERE declinaison = "'.$liste.'" ORDER BY nb_quantite_restant ASC, couvert_besoin ASC;';
        echo '<table class="table" style="border: 0; cellspacing: 0;" width="100%">
        <thead style="background-color:#fff">
            <tr>
                <th style="text-align:left;">
                    <span class="title_box  active">Réference</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Nom</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Déclinaison</span>
                </th>
                <th style="text-align:left;width: 75px;">
                    <span class="title_box  active">Nb de sachets restants</span>
                </th>
                <th style="text-align:left;width: 100px;">
                    <span class="title_box  active">Nb de sachets à produire</span>
                </th>
                <th style="text-align:left;width: 110px;">
                    <span class="title_box  active">Qté produite</span>
                </th>
                <th style="text-align:left;width: 110px;">
                    <span class="title_box  active">Nouveau stock tampon</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Stock tampon</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Germination</span>
                </th>
                <th style="text-align:left;width: 90px;">
                    <span class="title_box  active">% du besoin couvert</span>
                </th>
            </tr>
        </thead>
        <tbody>';
		
		// $requete = $bdd->prepare('SELECT * FROM operationnel WHERE ordre = "'.$liste.'" ORDER BY priorite ASC, nb_quantite_restant ASC, nb_sachet_a_produire DESC;');
	   
		/* DEBUT fix tri par germination, Dorian Berry Web */
		
		$requete = $bdd->prepare('
		SELECT DISTINCT id_operationnel, o.* FROM operationnel o
		INNER JOIN (SELECT id_product, MAX(date_approvisionnement) as max_date
		FROM ps_inventaire_lots il
		GROUP BY id_product)a ON a.id_product = o.id_product
		INNER JOIN ps_inventaire_lots il ON a.id_product = il.id_product
		INNER JOIN AW_test_lots tl ON il.id_inventaire_lots = tl.id_lot
		WHERE ordre = "'.$liste.'"
		AND a.max_date = il.date_approvisionnement
		AND tl.origine_test <> "Frns"
		ORDER BY priorite ASC, nb_quantite_restant ASC, nb_sachet_a_produire DESC
	   ');
	   
		/* FIN fix tri par germination, Dorian Berry Web */

        $requete->execute() or die(print_r($requete->errorInfo()));

        while (($prodEC = $requete->fetch()))
        {
			
            if($prodEC['couvert_besoin'] >= 100){
                $couleur = '#fff';
            }elseif ($prodEC['couvert_besoin'] >= 75) {
                $couleur = '#1aaf64';
            }elseif ($prodEC['couvert_besoin'] >= 50) {
                $couleur = '#FFFF00';
            }elseif ($prodEC['couvert_besoin'] >= 25) {
                $couleur = '#FF8000';
            }else{
                $couleur = '#FE2E2E';
            }

            $debut = strtotime($prodEC['date_germination']);
            $fin = strtotime(date('Y-m-d'));
            $dif = ceil(abs($fin - $debut) / 86400);

            if ( $dif <= 90 )
            {
                $couleurG = '#1aaf64';
            }
            elseif ( $dif <= 180 )
            {
                $couleurG = '#FFFF00';
            }
            elseif ( $dif <= 365 )
            {
                $couleurG = '#FF8000';
            }
            elseif ( $dif > 365 )
            {
                $couleurG = '#FE2E2E';
            }
            $afficheG = $prodEC['germination'].'%';
            if ( $prodEC['germination'] == 0 && $prodEC['date_germination'] == '0000-00-00' )
            {
                $couleurG = '#fff';
                $afficheG = '--';
            }
            $finalafficheG = $prodEC['lot_germination'].' | <span style="background:'.$couleurG.'; color:#000;">'.$afficheG.'</span>';

            if ( !empty($prodEC['lot_germination2']) )
            {
                $finalafficheG .= '<br />'.$prodEC['lot_germination2'].' | ';
                $debut2 = strtotime($prodEC['date_germination2']);
                $fin2 = strtotime(date('Y-m-d'));
                $dif2 = ceil(abs($fin2 - $debut2) / 86400);

                if ( $dif2 <= 90 )
                {
                    $couleurG2 = '#1aaf64';
                }
                elseif ( $dif2 <= 180 )
                {
                    $couleurG2 = '#FFFF00';
                }
                elseif ( $dif2 <= 365 )
                {
                    $couleurG2 = '#FF8000';
                }
                elseif ( $dif2 > 365 )
                {
                    $couleurG2 = '#FE2E2E';
                }
                $afficheG2 = $prodEC['germination2'].'%';
                if ( $prodEC['germination2'] == 0 && $prodEC['date_germination2'] == '0000-00-00' )
                {
                    $couleurG2 = '#fff';
                    $afficheG2 = '--';
                }
                $finalafficheG .= '<span style="background:'.$couleurG2.'; color:#000;">'.$afficheG2.'</span>';
            }

            if ( !empty($prodEC['lot_germination3']) )
            {
                $finalafficheG .= '<br />'.$prodEC['lot_germination3'].' | ';
                $debut3 = strtotime($prodEC['date_germination3']);
                $fin3 = strtotime(date('Y-m-d'));
                $dif3 = ceil(abs($fin3 - $debut3) / 86400);

                if ( $dif3 <= 90 )
                {
                    $couleurG3 = '#1aaf64';
                }
                elseif ( $dif3 <= 180 )
                {
                    $couleurG3 = '#FFFF00';
                }
                elseif ( $dif3 <= 365 )
                {
                    $couleurG3 = '#FF8000';
                }
                elseif ( $dif3 > 365 )
                {
                    $couleurG3 = '#FE2E2E';
                }
                $couleurG3 = $prodEC['germination3'].'%';
                if ( $prodEC['germination3'] == 0 && $prodEC['date_germination3'] == '0000-00-00' )
                {
                    $couleurG3 = '#fff';
                    $afficheG3 = '--';
                }
                $finalafficheG .= '<span style="background:'.$couleurG3.'; color:#000;">'.$afficheG3.'</span>';
            }
            $divfinalafficheG = '';
            if ( !empty($prodEC['lot_germination2']) )
            {
                $divfinalafficheG = '<div style="float:right;font-weight:bold; cursor:pointer;"><span id="p'.$prodEC['id_product'].'_'.$prodEC['id_product_attribute'].'" onclick="$(\'#g'.$prodEC['id_product'].'_'.$prodEC['id_product_attribute'].'\').css(\'display\', \'block\');$(\'#m'.$prodEC['id_product'].'_'.$prodEC['id_product_attribute'].'\').css(\'display\', \'block\');$(\'#p'.$prodEC['id_product'].'_'.$prodEC['id_product_attribute'].'\').css(\'display\', \'none\');">+</span><span id="m'.$prodEC['id_product'].'_'.$prodEC['id_product_attribute'].'" onclick="$(\'#g'.$prodEC['id_product'].'_'.$prodEC['id_product_attribute'].'\').css(\'display\', \'none\');$(\'#m'.$prodEC['id_product'].'_'.$prodEC['id_product_attribute'].'\').css(\'display\', \'none\');$(\'#p'.$prodEC['id_product'].'_'.$prodEC['id_product_attribute'].'\').css(\'display\', \'block\');" style="display:none;">-</span></div><div id="g'.$prodEC['id_product'].'_'.$prodEC['id_product_attribute'].'" style="display:none; background-color:#fff">'.$finalafficheG.'</div>';
            }

            echo '
                    <tr id="tr_'.$prodEC['id_product_attribute'].'">
                        <td style="border-bottom: 1px solid grey;">
                            '.$prodEC['reference'].'
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            '.$prodEC['nom'].'
                        </td>
                        <td style="border-bottom: 1px solid grey;text-align:right;">
                            '.$prodEC['declinaison'].' g
                        </td>
                        <td style="border-bottom: 1px solid grey;text-align:right;">'.$prodEC['nb_quantite_restant'].'</td>
                        <td style="border-bottom: 1px solid grey;text-align:right;">'.$prodEC['nb_sachet_a_produire'].' ('.floor($prodEC['stock_theorique_tamp']/$prodEC['declinaison']).')</td>
                        <td style="border-bottom: 1px solid grey;"><input type="text" class="majQte" style="width: 75px;" id="prod_'.$prodEC['id_product_attribute'].'" /></td>
                        <td style="border-bottom: 1px solid grey;"><input type="text" class="majTamp" style="width: 75px;" id="tamp_'.$prodEC['id_product_attribute'].'" />&nbsp;<img src="/img/valider.png" id="val_tamp_'.$prodEC['id_product_attribute'].'" style="width:20px;vertical-align: middle;cursor:pointer;display:none;" onclick="majProduit('.$prodEC['id_product'].', '.$prodEC['id_product_attribute'].', $(\'#prod_'.$prodEC['id_product_attribute'].'\').val(), $(\'#tamp_'.$prodEC['id_product_attribute'].'\').val());" /></td>
                        <td style="border-bottom: 1px solid grey;text-align:right;">'.$prodEC['stock_theorique_tamp'].' g</td>
                        <td style="border-bottom: 1px solid grey;background:'.$couleurG.'; color:#000;">'.$afficheG.$divfinalafficheG.'</td>
                        <td style="border-bottom: 1px solid grey;background:'.$couleur.'; color:#000;">'.$prodEC['couvert_besoin'].' %</td>
                    </tr>';
        }
        echo '</tbody></table></div>';
        }
      echo '</div>';


      echo '</body>
      </html>';
