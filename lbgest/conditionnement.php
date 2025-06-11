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
          <title>Conditionnement - LBG</title>
          <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
          <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
          <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
          <script src="https://unpkg.com/sticky-table-headers"></script>
          <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
          <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
          <script src="https://kit.fontawesome.com/e35ed65262.js" crossorigin="anonymous"></script>
		  <link href="select2/dist/css/select2.min.css" rel="stylesheet" />
		  <script src="select2/dist/js/select2.min.js"></script>
		  <script src="/modules/statsstocksinventaire/modulestatsstocksinventaire.js"></script>
          <script>
          $( function() {
            $( "#tabs" ).tabs();
            $(".table").stickyTableHeaders();
          } );

          function search(ref)
          {
            if ( ref == 0 )
            {
              $(".table tr").show();
              $(".fa-search-plus").show();
              $(".fa-search-minus").hide();
            }
            else
            {
              $(".table tr").hide();
              $("[filtre_ref=\'"+ref+"\']").show();
              $(".table tr#headert").show();
              $(".fa-search-plus").hide();
              $(".fa-search-minus").show();
            }
          }

          $(document).ready(function () {
		
			$(".js-product-content").select2({dropdownAutoWidth : true});

			$("body").on("click", ".js-submit-maj-product", function(e) {
				var tr = $(this).closest("tr"); // on retrouve la ligne correspondante
				var id_product = $(tr).data("id-product");
				var id_product_attribute = $(tr).data("id-product-attribute");
				var sachet = $(".majQte",tr).val();
				var tampon = $(".majTamp",tr).val();
				majProduit(id_product, id_product_attribute, sachet, tampon);
			});
				
			$(".js-product-content").on("change", function() {
				var id_product = $(this).val();
				
				$.ajax({
					method: "GET",
					url: "ajax_get_product_details.php?token=hdf6dfdfs6ddgs&idp="+id_product
				})
				.done(function( msg ) {
					var maj = "";
					var res = jQuery.parseJSON(msg);
					var len = 1;
					for(var i=0; i<res.length; i++){
						var id_product_attribute = res[i]["id_product_attribute"];
						var name = res[i]["name"];
						if(name == "BIO" || name == "Non traitée"){
							continue;
						}
						maj += \'<tr id="tr_\'+id_product_attribute+\'" data-id-product="\'+id_product+\'" data-id-product-attribute="\'+id_product_attribute+\'"    >\';
						maj += \'<td style="border-bottom: 1px solid grey;text-align:right;">\'+name+\'</td>\';
						maj += \'<td style="border-bottom: 1px solid grey;text-align:right;"><input type="text" class="majQte" style="width: 75px;"/></td>\';
						maj += \'<td style="border-bottom: 1px solid grey;text-align:right;"><input type="text" class="majTamp" style="width: 75px;"/></td>\';
						maj += \'<td style="border-bottom: 1px solid grey;text-align:right;"><input class="js-submit-maj-product" type="button" value="Valider"></td>\';
						maj += \'</tr>\';
					}
					
					$(".js-maj-produit-bloc").show();
					$(".js-maj-produit").html(maj);	
				});
				
				$.ajax({
					method: "GET",
					url: "etat.php?token=hdf6dfdfs6ddgs&idp="+id_product
				})
				.done(function( msg ) {
					$(".js-etat-des-stocks").html(msg);
				});
				
				$.ajax({
					method: "GET",
					url: "ajax_get_stats_previsionnelles.php?token=hdf6dfdfs6ddgs&idp="+id_product
				})
				.done(function( msg ) {
					$(".js-stats-previsionnelles").html(msg);
				});
				
				
			});

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
		  
		  .table_detail th, .table_detail td{
            padding: 5px;
          }

          .table_detail th{
            font-size: 14px;
          }

          .table_detail, .table_detail thead, .table_detail tbody{
            width: 100%;
          }

          .table_detail{
            margin: 20px 0;
          }

          .conteneur_lb{
            position: fixed;
            display: flex;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 999999999999999;
            display: none;
          }

          .lightbox{
            min-height: 50%;
            width: 920px;
            margin: auto;
            background-color: white;
            overflow: scroll;
            max-height: 100%;
            max-width: 100%;
            z-index: 999999999999999;
          }

          .border_top_fonce td,.border_top_fonce th{
            border-top: 1px solid #666666 !important;
          }

          .border_bottom_top_fonce td,.border_bottom_top_fonce th{
            border-bottom: 1px solid #666666 !important;
            border-top: 1px solid #666666 !important;
          }

          td.border_bottom_fonce{
            border-bottom: 1px solid #666666 !important;
          }

          .margin0{
            margin: 0 !important;
          }

          .center, .center th, .center thead, .center td{
            text-align: center;
          }

          .display_none{
            display: none;
          }

          .click_display{
            cursor: pointer;
          }

          .border_claire td, .border_claire th{
            /*border: 1px solid #eaedef;*/
          }

          #ajout_test{
            cursor: pointer;
            margin: 5px;
            color: initial;
          }

          .table_detail th {
              font-weight: bold !important;
              font-size: 12px !important;
          }
		  
          .ui-widget {
            font-family: \'Open Sans\', sans-serif !important;
          }
		  
		  .js-maj-produit-bloc{
			display: none;  
			margin-top: 90px;
		  }
		  
		  .js-maj-produit-bloc td, .js-maj-produit-bloc th{
			padding: 8px 12px;
		  }
		  
		  .table-stats-previsionnelles td, .table-stats-previsionnelles th{
			padding: 8px 12px;
		  }
		  
          </style>
        </head>
        <body style="font-family: \'Open Sans\', sans-serif !important;">';

        echo '<h1 style="text-align: center;"><a href="conditionnement.php?token='.$_GET['token'].'"><img src="/img/logo135.png" style="vertical-align: middle;" /></a>&nbsp;Conditionnement</h1>';
		    echo '<div style="margin-bottom:15px"><a href="parametres.php?token='.$_GET['token'].'">Paramètres</a></div>';

        echo '<div id="tabs">';
        echo '<ul>';
        echo '<li><a href="#tabvictoria">Victoria</a></li>';
        echo '<li><a href="#tabcarmen">Carmen</a></li>';
        echo '<li><a href="#tabrechercher">Rechercher</a></li>';
        echo '</ul>';

		// Début - Dorian, BERRY-WEB, mars 2022
		// Si pas de ventilation pour un produit, le mettre par défaut dans Victoria
		update_ventilation($bdd);
		
		// Fin - Dorian, BERRY-WEB, mars 2022

        $tab_decli = array('1' => 'Victoria', '2' => 'Carmen');
        foreach($tab_decli as $key => $liste)
        {
        echo '<div id="tab'.str_replace(' ','', $liste).'">';
        //echo 'SELECT * FROM operationnel WHERE declinaison = "'.$liste.'" ORDER BY nb_quantite_restant ASC, couvert_besoin ASC;';
        echo '<table class="table" style="border: 0; cellspacing: 0;" width="100%">
        <thead style="background-color:#fff">
            <tr id="headert">
                <th style="text-align:left;">
                  <i class="fas fa-search-minus" onclick="search(\'0\');" style="display:none;cursor:pointer;"></i>
                </th>
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
		WHERE o.id_product_attribute IN (SELECT SUBSTRING_INDEX(id, "_", -1) FROM ventilation WHERE id LIKE CONCAT("'.$key.'_%_", o.id_product, "_", o.id_product_attribute))
		AND a.max_date = il.date_approvisionnement
		AND tl.origine_test <> "Frns"
		AND o.date_germination <> "0000-00-00"									  
		ORDER BY priorite ASC, nb_sachet_a_produire DESC, nb_quantite_restant ASC
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
				$afficheG3 = '';
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

            echo '<tr id="tr_'.$prodEC['id_product_attribute'].'" filtre_ref="'.$prodEC['reference'].'">
                        <td><i class="fas fa-search-plus" onclick="search(\''.$prodEC['reference'].'\');" style="cursor:pointer;"></i></td>
                        <td style="border-bottom: 1px solid grey;" class="ref">
                            '.$prodEC['reference'].'
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            '.$prodEC['nom'].'
                        </td>
                        <td style="border-bottom: 1px solid grey;text-align:right;">
                            '.$prodEC['declinaison'].' '.$prodEC['unite'].'
                        </td>
                        <td style="border-bottom: 1px solid grey;text-align:right;">'.$prodEC['nb_quantite_restant'].'</td>
                        <td style="border-bottom: 1px solid grey;text-align:right;">'.$prodEC['nb_sachet_a_produire'].' ('.floor($prodEC['stock_theorique_tamp']/$prodEC['declinaison']).')</td>
                        <td style="border-bottom: 1px solid grey;"><input type="text" class="majQte" style="width: 75px;" id="prod_'.$prodEC['id_product_attribute'].'" /></td>
                        <td style="border-bottom: 1px solid grey;"><input type="text" class="majTamp" style="width: 75px;" id="tamp_'.$prodEC['id_product_attribute'].'" />&nbsp;<img src="/img/valider.png" id="val_tamp_'.$prodEC['id_product_attribute'].'" style="width:20px;vertical-align: middle;cursor:pointer;display:none;" onclick="majProduit('.$prodEC['id_product'].', '.$prodEC['id_product_attribute'].', $(\'#prod_'.$prodEC['id_product_attribute'].'\').val(), $(\'#tamp_'.$prodEC['id_product_attribute'].'\').val());" /></td>
                        <td style="border-bottom: 1px solid grey;text-align:right;">'.$prodEC['stock_theorique_tamp'].' '.$prodEC['unite'].'</td>
                        <td style="border-bottom: 1px solid grey;background:'.$couleurG.'; color:#000;">'.$afficheG.$divfinalafficheG.'</td>
                        <td style="border-bottom: 1px solid grey;background:'.$couleur.'; color:#000;">'.$prodEC['couvert_besoin'].' %</td>
                    </tr>';
        }
        echo '</tbody></table></div>';
        }
		
		// On récupère tous les produits 
		$sql = 'SELECT p.id_product, p.reference, pl.name FROM ps_product p
		INNER join ps_product_lang pl ON p.id_product =  pl.id_product
		WHERE pl.id_lang = 1 AND p.active = 1 
		ORDER BY pl.name';
		$product_list = Db::getInstance()->ExecuteS($sql);
		
		$product_list_str = "";
		foreach($product_list as $p){
			$product_list_str .= '<option value="'.$p['id_product'].'">'.$p['reference'].' '.$p['name'].'</option>';
		}
		$now = date("Y-m-d H:i:s");
		$d1a = new DateTime($now);
		$d1a->sub(new DateInterval('P1Y')); 
		$d1b = $d1a->format('d/m/Y');

		echo '<div id="tabrechercher">'; // début - onlget Rechercher
			echo '<select class="js-product-content">
					<option value="0"> -- </option>
					'.$product_list_str.'
				</select>';
				
			echo '<div class="js-etat-des-stocks"></div>';
			
			echo '<div class="stats-previsionnelles">
				<h2>Stats Previsionnelles</h2>
				<div>Du '.$d1b.' au '.date('d/m/Y').'</div>
				<div class="js-stats-previsionnelles"></div>
			</div>';
			
			echo '<div class="js-maj-produit-bloc">
				<table>
					<thead>
						<tr>
							<th>Déclinaison</th>
							<th>Qté produite</th>
							<th>Nouveau stock tampon</th>
						</tr>
					</thead>
					<tbody class="js-maj-produit">
					</tbody>
				</table>
			</div>';
			
		echo '</div>'; // fin - onlget Rechercher
		
		 
      echo '</div>';

      echo '</body>
      </html>';

// Début - Dorian, BERRY-WEB, aout 2022
function update_ventilation($bdd){
		
	// On récupère la ventilation depuis la BDD
	$ventilation = array();
	$sql = 'SELECT id FROM ventilation;';
	$query = $bdd->prepare($sql);
	$query->execute();
	$ventilation = $query->fetchAll(\PDO::FETCH_GROUP);

	// On récupère les graines depuis la BDD
	// id conditionnement = 6

	$graines = array();

	// id catgories = 18, 78, 233 ( graines potagères, aromatiques, fleurs )
	$categories = array(18 => 'Graines potagères', 78 => 'Graines aromatiques', 233 => 'Fleurs', 92 => 'Mélange de fleurs');

	foreach ($categories as $key => $cat){

		// select toutes les sous-categories

		$sql = 'SELECT c.id_category, pa.id_product_attribute, pa.id_product, pl.name as nom, al.name as declinaison, cl.name as category_name
		FROM ps_product_attribute pa
		INNER JOIN ps_product p ON pa.id_product = p.id_product
		INNER JOIN ps_product_lang pl ON pl.id_product = p.id_product
		INNER JOIN ps_product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute
		INNER JOIN ps_attribute_lang al ON al.id_attribute = pac.id_attribute
		INNER JOIN ps_category c ON c.id_category = p.id_category_default
		INNER JOIN ps_category_lang cl ON cl.id_category = c.id_category
		INNER JOIN ps_attribute a ON a.id_attribute = al.id_attribute
		WHERE pl.id_lang = 1
		AND al.id_lang = 1
		AND cl.id_lang = 1
		AND p.active = 1
		AND a.id_attribute_group = 6';

		if($key == 78){
			$sql .= ' AND c.id_category = '.$key.' ORDER BY cl.name, pl.name, al.name';
		}else{
			$sql .= ' AND c.id_parent = '.$key.' ORDER BY cl.name, pl.name, al.name';
		}


		$query = $bdd->prepare($sql);
		$query->execute();
		$res = $query->fetchAll(\PDO::FETCH_GROUP);

		$g = array('name' => $cat, 'value' => $res);
		$graines[$key] = $g;


	}

	foreach($graines as $key1 => $g){
		
		// Pacourt des catégories
		foreach($g['value'] as $key2 => $c){
			
			// Formatte la liste, puis pacourt les produits
			$produits = array();
			foreach($c as $produit){
				$p = array();
				$key = $produit['id_product'];
				$p['nom'] = $produit['nom'];
				$p['id_product_attribute'] = $produit['id_product_attribute'];
				$p['declinaison'] = $produit['declinaison'];
				$produits[$key][] = $p;
			}
			foreach($produits as $key3 => $produit){

				// Pacourt les déclinaisons de produits
				foreach($produit as $declinaison){
					$key4 = $declinaison['id_product_attribute'];
					$checked1 = isset($ventilation['1_'.$key1.'_'.$key2.'_'.$key3.'_'.$key4]) ? 'checked="checked"' : '';
					$checked2 = isset($ventilation['2_'.$key1.'_'.$key2.'_'.$key3.'_'.$key4]) ? 'checked="checked"' : '';

					// Si pas de ventilation pour un produit, le mettre par défaut dans Victoria
					if(!$checked1  && !$checked2){
						$id_ventilation = '1_'.$key1.'_'.$key2.'_'.$key3.'_'.$key4;
						$sql = 'INSERT IGNORE INTO ventilation (id)
						VALUES ("'.pSQL($id_ventilation).'")';
						$req = Db::getInstance()->Execute($sql);
					}
				}
			}
		}
	}

}
// Fin - Dorian, BERRY-WEB, aout 2022

