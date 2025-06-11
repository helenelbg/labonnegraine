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
		
		if (isset($_POST['maj_nb_mois_germination'])) {
			// maj croissance
			$nb_mois_germination = $_POST['nb_mois_germination'];
			$sql = 'UPDATE conditionnement SET nb_mois_germination="' . pSQL($nb_mois_germination) . '" WHERE id = 1;';
			$req = Db::getInstance()->Execute($sql);
		}

        echo '<html lang="en">
        <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <title>Germination - LBG</title>
          <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
          <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
          <script src="https://unpkg.com/sticky-table-headers"></script>
          <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
          <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
          <script src="https://kit.fontawesome.com/e35ed65262.js" crossorigin="anonymous"></script>
          <script src="/modules/statsstocksinventaire/modulestatsstocksinventaire.js"></script>
		  <link href="select2/dist/css/select2.min.css" rel="stylesheet" />
		  <script src="select2/dist/js/select2.min.js"></script>

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
            /*padding: 5px;*/
            /*background-color: white;*/
            margin: 5px;
            color: initial;
          }

          #ajout_test{

          }
          .table_detail th {
              font-weight: bold !important;
              font-size: 12px !important;
          }
		  
		  .modal_product {
			  display: none;
			  position: fixed;
			  z-index: 15;
			  left: 0;
			  top: 0;
			  width: 100%;
			  height: 100%;
			  overflow: auto;
			  background-color: rgb(0,0,0);
			  background-color: rgba(0,0,0,0.4);
			}
			
			.modal_product .select2 {
				width: 500px;
			}

			.modal_content {
			  background-color: #fefefe;
			  margin: 15% auto;
			  padding: 20px;
			  border: 1px solid #888;
			  width: 80%;
			  font-size: 18px;
			}

			.modal_close {
			  color: #aaa;
			  float: right;
			  font-size: 28px;
			  font-weight: bold;
			  cursor: pointer;
			  margin-top: -20px;
			  margin-right: 5px;
			}

			.modal_close:hover,
			.modal_close:focus {
			  color: black;
			  text-decoration: none;
			}
			
			.js-lots{
				display:none;
			}
			
          </style>


          <!-- jQuery Modal -->
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

          <script>
          function init_first()
          {
            $( "#tabs" ).tabs();
            $("table").stickyTableHeaders();

            $(\'.notest\').click(function (e) {
                var split = $(this).attr(\'id\').split(\'_\');
                var r = confirm("Confirmer ?");
				if (r == true) {
					majGerm(1, split[1], $(this));
				}else{
					$(this).prop("checked",false);
				}
            });
            $(\'.lancer\').click(function (e) {
                var split = $(this).attr(\'id\').split(\'_\');
                majGerm(2, split[1], $(this));
            });
          }

          function init()
          {
              $(\'input\').keypress(function (e) {

                  var charCode = (e.which) ? e.which : event.keyCode

                  if (String.fromCharCode(charCode).match(/[^0-9]/g))
                      return false;

              });

              $(\'input\').keyup(function (e) {
                  var split = $(this).attr(\'id\').split(\'_\');
				  var tr = $(this).closest(\'tr\');
                  if ( $(this).val() == \'\' )
                  {
					  $(\'.val1\',tr).css(\'display\', \'none\');
                  }
                  else
                  {
					  $(\'.val1\',tr).css(\'display\', \'inline-block\');
                  }
              });

              $(\'.manual-ajax-etat\').click(function(event) {
                event.preventDefault();
                this.blur(); // Manually remove focus from clicked link.
                $.get(this.href, function(html) {
                  $(html).appendTo(\'body\').modal({
                    closeExisting: true,clickClose: false
                  });
                  $(\'.close-modal\').click(function(event) {
                    console.log($(this).parent());
                    $(this).parent().remove();
                  });
                });
              });

              $(\'.manual-ajax\').click(function(event) {
    event.preventDefault();
    this.blur(); // Manually remove focus from clicked link.
    $.get(this.href, function(html) {
      $(html).appendTo(\'body\').modal({
        closeExisting: true,clickClose: false
      });

          $(function() {
           $(".datepickerLot").datepicker({
            dateFormat:"yy-mm-dd",
            prevText:"",
            nextText:""});
          });

    });
  });
          }



          $(document).ready(function () {

			init_first();
			init();
			
			var queryString = window.location.search;
			var urlParams = new URLSearchParams(queryString);
			var testencours = urlParams.get("testencours");
			if(testencours == "1"){
				$("#ui-id-3").click(); // affiche l onglet 3 par défaut
			}
						
			$(".js-product-content").select2({dropdownAutoWidth : true});
			
			$("body").on("click", ".js-ajout-test", function(e) {
				$(".modal_product").show();
			});
			
			$("body").on("click", ".modal_close", function(e) {
				$(".modal_product").hide();
			});
			
			$("body").on("click", ".js-submit-ajout-test", function(e) {
				var id_lot = $(".js-lots").val();
					$.ajax({
					method: "POST",
					url: "ajax_ajout_aw_lot.php?token=hdf6dfdfs6ddgs",
					data: { id_lot: id_lot }
				  })
                .done(function( msg ) {
				   if(msg == "ok"){
					   $(".modal_product").hide();
					   // recharge la page sur l onglet "Tests en cours"
					   window.location.href = window.location.href+"&testencours=1"
				   }
                });
			});
			
			$("body").on("change", ".js-product-content", function(e) {
				var id_product = $(this).val();
					$.ajax({
					method: "POST",
					url: "ajax_get_lots.php?token=hdf6dfdfs6ddgs",
					data: { id_product: id_product }
				  })
                .done(function( msg ) {
					var res = jQuery.parseJSON(msg);
					var str = "";
					
					 var arr = [];
				     for(var i in res){
					   arr.push(i);
				     }
					 
				     for (var i=arr.length-1; i>=0; i--) {
					    var name = arr[i];
						var key = res[name];
						str += \'<option value="\'+key+\'">\'+name+\'</option>\';
				     }

  
					$(".js-lots").html(str);
					$(".js-lots").show();
				   
                });
			});
			

        });
		
		
		

          function majGerm(status, id_product, elm)
          {
			var tr = elm.closest(\'tr\');
			var lot_germination = $(".lot_germination",tr).attr("data-lot-germination");
			console.log("majGerm");
			console.log(id_product);
			console.log(status);
			console.log(lot_germination);
            $.ajax({
                method: "POST",
                url: "majGerm.php?token=hdf6dfdfs6ddgs",
                data: { id_product: id_product, status: status, lot_germination: lot_germination }
              })
                .done(function( msg ) {
					$(elm).closest("tr").css(\'display\', \'none\');
                    $.ajax({
                        method: "POST",
                        url: "majOnglet.php?token=hdf6dfdfs6ddgs"
                      })
                        .done(function( msg ) {
                            $(\'#encours\').html(msg);
                            init();
                        });
                });
          }

          function suppr_test(id_test)
          {
            var r = confirm("Etes-vous sûr de vouloir supprimer ce test ?");
            if (r == true) {
            $.ajax({
                method: "POST",
                url: "suppr_test.php?token=hdf6dfdfs6ddgs",
                data: { id_test: id_test }
              })
                .done(function( msg ) {
                    $.ajax({
                        method: "POST",
                        url: "majOnglet.php?token=hdf6dfdfs6ddgs"
                      })
                        .done(function( msg ) {
                            $(\'#encours\').html(msg);
                            init();
                        });
                });
              }
          }

          function terminer_test(id_test)
          {
            var r = confirm("Etes-vous sûr de vouloir terminer ce test ?");
            if (r == true) {
            $.ajax({
                method: "POST",
                url: "terminer_test.php?token=hdf6dfdfs6ddgs",
                data: { id_test: id_test }
              })
                .done(function( msg ) {
                    $.ajax({
                        method: "POST",
                        url: "majOnglet.php?token=hdf6dfdfs6ddgs"
                      })
                        .done(function( msg ) {
                            $(\'#encours\').html(msg);
                            init();
                        });
                });
              }
          }

          function majEtape(etape, id_product, lot, valeur, termine)
          {
            $.ajax({
                method: "POST",
                url: "ajaxTermine.php?token=hdf6dfdfs6ddgs",
                data: { etape: etape, lot: lot, valeur: valeur, termine: termine, id_product: id_product }
              })
                .done(function( msg ) {
                      $(msg).appendTo(\'body\').modal({
                        closeExisting: true,clickClose: false
                      });

                      $(\'.close-modal\').click(function(event) {
                        console.log($(this).parent());
                        $(this).parent().remove();
                      });
                });
          }

          function majEtapeFinal(etape, id_product, lot, valeur, termine, commentaires)
          {
            $.ajax({
                method: "POST",
                url: "majEtape.php?token=hdf6dfdfs6ddgs",
                data: { etape: etape, lot: lot, valeur: valeur, termine: termine, commentaires: commentaires }
              })
                .done(function( msg ) {
                  $(".modal").remove();
                    $(".jquery-modal").remove();
					$("body").css("overflow","auto");

                    $(\'#tr_ec_\'+id_product).css(\'display\', \'none\');

                    $.ajax({
                        method: "POST",
                        url: "majOnglet.php?token=hdf6dfdfs6ddgs"
                      })
                        .done(function( msg ) {
                            $(\'#encours\').html(msg);
                            init();
                        });
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

		// On récupère nb_mois_germination depuis la BDD
		$nb_mois_germination = 8; // défaut = 8 mois
		$res = Db::getInstance()->ExecuteS('SELECT nb_mois_germination FROM conditionnement WHERE id = 1;');
		foreach ($res as $r){
			$nb_mois_germination = intval($r['nb_mois_germination']);
		}

		$date_germination = date('Y-m-d', strtotime('-8 months')); // défaut = 8 mois
		if($nb_mois_germination){
			$date_germination = date('Y-m-d', strtotime('-'.$nb_mois_germination.' months'));
		}
		
        echo '<h1 style="text-align: center;"><a href="approvisionnement.php?token='.$_GET['token'].'"><img src="/img/logo135.png" style="vertical-align: middle;" /></a>&nbsp;Germination</h1>';

		$url = 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		echo '<form action="'.$url.'" method="post" enctype="multipart/form-data"><div>
			<label for="nb_mois_germination">Nombre de mois germination</label>
			<input id="nb_mois_germination" type="text" name="nb_mois_germination" value="'.$nb_mois_germination.'">
			<input type="submit" name="maj_nb_mois_germination" value="Valider" />
		</div>
		</form>';
 
        echo '<div id="tabs">';
        echo '<ul>';
        echo '<li><a href="#sans">Lots sans germination LBG</a></li>';
        echo '<li><a href="#arefaire">Lots avec germination supérieure à '.$nb_mois_germination.' mois</a></li>';
        echo '<li><a href="#encours">Tests en cours</a></li>';
        echo '</ul>';

        echo '<div id="sans">';
        //echo 'SELECT * FROM operationnel WHERE declinaison = "'.$liste.'" ORDER BY nb_quantite_restant ASC, couvert_besoin ASC;';
        echo '<table class="table" style="border: 0; cellspacing: 0;" width="100%">
        <thead style="background-color:#fff">
            <tr id="headert">
                <th style="text-align:left;">
                    <span class="title_box  active">Réference</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Saisie</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Nom</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">N° lot</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Officielle</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Minimum LBG</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Optimum LBG</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Germination</span>
                </th>
            </tr>
        </thead>
        <tbody>';

        $sql = 'SELECT * FROM germination g LEFT JOIN germination_normes gn ON (g.id_categorie = gn.id_categorie) WHERE date_germination = "0000-00-00" AND CONCAT(id_product,"-",`lot_germination`) NOT IN (SELECT CONCAT(il.id_product,"-",il.numero_lot_LBG) FROM AW_test_lots tl LEFT JOIN ps_inventaire_lots il ON (tl.id_lot = il.id_inventaire_lots) LEFT JOIN ps_product_lang pl ON (il.id_product = pl.id_product AND id_lang = 1) LEFT JOIN ps_product p ON (pl.id_product = p.id_product) WHERE tl.origine_test = "LBG" AND tl.date_fin_test = "0000-00-00");';

    		$requete = $bdd->prepare($sql);

        $requete->execute() or die(print_r($requete->errorInfo()));

        while (($prodEC = $requete->fetch()))
        {
            $debut = strtotime($prodEC['date_germination']);
            $fin = strtotime(date('Y-m-d'));
            $dif = ceil(abs($fin - $debut) / 86400);

            $median = ($prodEC['optimum'] + $prodEC['minimum']) / 2;

            if ( $prodEC['germination'] >= $prodEC['optimum'] ) // VERT
            {
                $couleurG = '#1aaf64';
            }
            elseif ( $prodEC['germination'] >= $median ) // ORANGE
            {
                $couleurG = '#FF8000';
            }
            elseif ( $prodEC['germination'] < $median ) // ROUGE
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

                if ( $prodEC['germination2'] >= $prodEC['optimum'] ) // VERT
                {
                    $couleurG2 = '#1aaf64';
                }
                elseif ( $prodEC['germination2'] >= $median ) // ORANGE
                {
                    $couleurG2 = '#FF8000';
                }
                elseif ( $prodEC['germination2'] < $median ) // ROUGE
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

                if ( $prodEC['germination3'] >= $prodEC['optimum'] ) // VERT
                {
                    $couleurG3 = '#1aaf64';
                }
                elseif ( $prodEC['germination3'] >= $median ) // ORANGE
                {
                    $couleurG3 = '#FF8000';
                }
                elseif ( $prodEC['germination3'] < $median ) // ROUGE
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

            echo '<tr id="tr_'.$prodEC['id_product'].'" filtre_ref="'.$prodEC['reference'].'">
                        <td style="border-bottom: 1px solid grey;" class="ref">
                            '.$prodEC['reference'].'
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            <input type="checkbox" class="notest" name="notest_'.$prodEC['id_product'].'" id="notest_'.$prodEC['id_product'].'" /> <label for="notest_'.$prodEC['id_product'].'">Pas de test</label>&nbsp;|&nbsp;
                            <input type="checkbox" class="lancer" name="lancer_'.$prodEC['id_product'].'" id="lancer_'.$prodEC['id_product'].'" /> <label for="lancer_'.$prodEC['id_product'].'">Lancer le test</label>
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                        <a href="etat.php?token=hdf6dfdfs6ddgs&idp='.$prodEC['id_product'].'" class="manual-ajax-etat">'.$prodEC['nom'].'</a>
                        </td>
                        <td class="lot_germination" data-lot-germination="'.$prodEC['lot_germination'].'" style="border-bottom: 1px solid grey;">
                            '.substr($prodEC['lot_germination'], -4, 4).'
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            '.$prodEC['officielle'].'%
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            '.$prodEC['minimum'].'%
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            '.$prodEC['optimum'].'%
                        </td>
                        <td style="border-bottom: 1px solid grey;background:'.$couleurG.'; color:#000;">'.$afficheG.$divfinalafficheG.'</td>
                    </tr>';
        }
        echo '</tbody></table>';
        echo '</div>';



        echo '<div id="arefaire">';
        //echo 'SELECT * FROM operationnel WHERE declinaison = "'.$liste.'" ORDER BY nb_quantite_restant ASC, couvert_besoin ASC;';
        echo '<table class="table" style="border: 0; cellspacing: 0;" width="100%">
        <thead style="background-color:#fff">
            <tr id="headert">
                <th style="text-align:left;">
                    <span class="title_box  active">Réference</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Saisie</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Nom</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">N° lot</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Officielle</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Minimum LBG</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Optimum LBG</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Germination</span>
                </th>
            </tr>
        </thead>
        <tbody>';
		
        $sql = 'SELECT * FROM germination g LEFT JOIN germination_normes gn ON (g.id_categorie = gn.id_categorie) WHERE date_germination <> "0000-00-00" AND date_germination < "'.$date_germination.'" AND id_product NOT IN (SELECT il.id_product FROM AW_test_lots tl LEFT JOIN ps_inventaire_lots il ON (tl.id_lot = il.id_inventaire_lots) LEFT JOIN ps_product_lang pl ON (il.id_product = pl.id_product AND id_lang = 1) LEFT JOIN ps_product p ON (pl.id_product = p.id_product) WHERE tl.origine_test = "LBG" AND tl.date_fin_test = "0000-00-00") ORDER BY priorite ASC, nom ASC;';

    		$requete = $bdd->prepare($sql);

        $requete->execute() or die(print_r($requete->errorInfo()));

        while (($prodEC = $requete->fetch()))
        {
            $debut = strtotime($prodEC['date_germination']);
            $fin = strtotime(date('Y-m-d'));
            $dif = ceil(abs($fin - $debut) / 86400);

            $median = ($prodEC['optimum'] + $prodEC['minimum']) / 2;

            if ( $prodEC['germination'] >= $prodEC['optimum'] ) // VERT
            {
                $couleurG = '#1aaf64';
            }
            elseif ( $prodEC['germination'] >= $median ) // ORANGE
            {
                $couleurG = '#FF8000';
            }
            elseif ( $prodEC['germination'] < $median ) // ROUGE
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

                if ( $prodEC['germination2'] >= $prodEC['optimum'] ) // VERT
                {
                    $couleurG2 = '#1aaf64';
                }
                elseif ( $prodEC['germination2'] >= $median ) // ORANGE
                {
                    $couleurG2 = '#FF8000';
                }
                elseif ( $prodEC['germination2'] < $median ) // ROUGE
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

                if ( $prodEC['germination3'] >= $prodEC['optimum'] ) // VERT
                {
                    $couleurG3 = '#1aaf64';
                }
                elseif ( $prodEC['germination3'] >= $median ) // ORANGE
                {
                    $couleurG3 = '#FF8000';
                }
                elseif ( $prodEC['germination3'] < $median ) // ROUGE
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

            echo '<tr id="tr_'.$prodEC['id_product'].'" filtre_ref="'.$prodEC['reference'].'">
                        <td style="border-bottom: 1px solid grey;" class="ref">
                            '.$prodEC['reference'].'
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                        <input type="checkbox" class="notest" name="notest_'.$prodEC['id_product'].'" id="notest_'.$prodEC['id_product'].'" /> <label for="notest_'.$prodEC['id_product'].'">Pas de test</label>&nbsp;|&nbsp;
                        <input type="checkbox" class="lancer" name="lancer_'.$prodEC['id_product'].'" id="lancer_'.$prodEC['id_product'].'" /> <label for="lancer_'.$prodEC['id_product'].'">Lancer le test</label>
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                        <a href="etat.php?token=hdf6dfdfs6ddgs&idp='.$prodEC['id_product'].'" class="manual-ajax-etat">'.$prodEC['nom'].'</a>
                        </td>
                       <td class="lot_germination" data-lot-germination="'.$prodEC['lot_germination'].'" style="border-bottom: 1px solid grey;">
                            '.substr($prodEC['lot_germination'], -4, 4).'
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            '.$prodEC['officielle'].'%
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            '.$prodEC['minimum'].'%
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            '.$prodEC['optimum'].'%
                        </td>
                        <td style="border-bottom: 1px solid grey;background:'.$couleurG.'; color:#000;">'.$afficheG.$divfinalafficheG.'</td>
                    </tr>';
        }
        echo '</tbody></table>';
        echo '</div>';

        echo '<div id="encours">';
        echo '<table class="table" style="border: 0; cellspacing: 0;" width="100%">
        <thead style="background-color:#fff">
            <tr id="headert">
                <th style="text-align:left;">
                    <span class="title_box  active">Réference</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Nom</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">N° lot</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Date début</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Etape 1</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Etape 2</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active">Etape 3</span>
                </th>
                <th style="text-align:left;">
                    <span class="title_box  active"></span>
                </th>
                <th></th>
            </tr>
        </thead>
        <tbody>';

        $sql = 'SELECT * FROM AW_test_lots tl LEFT JOIN ps_inventaire_lots il ON (tl.id_lot = il.id_inventaire_lots) LEFT JOIN ps_product_lang pl ON (il.id_product = pl.id_product AND id_lang = 1) LEFT JOIN ps_product p ON (pl.id_product = p.id_product) WHERE tl.origine_test = "LBG" AND tl.date_fin_test = "0000-00-00" ORDER BY tl.date_etape_1 ASC, tl.date_etape_2 ASC, tl.date_etape_3 ASC;';

    		$requete = $bdd->prepare($sql);

        $requete->execute() or die(print_r($requete->errorInfo()));

        while (($prodEC = $requete->fetch()))
        {
            echo '<tr id="tr_ec_'.$prodEC['id_product'].'" filtre_ref="'.$prodEC['reference'].'">
                        <td style="border-bottom: 1px solid grey;" class="ref">
                            '.$prodEC['reference'].'
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            <a href="etat.php?token=hdf6dfdfs6ddgs&idp='.$prodEC['id_product'].'" class="manual-ajax-etat">'.$prodEC['name'].'</a>
                        </td>
                        <td class="lot_germination" data-lot-germination="'.$prodEC['numero_lot_LBG'].'" style="border-bottom: 1px solid grey;">
                            '.substr($prodEC['numero_lot_LBG'], -4, 4).'
                        </td>
                        <td style="border-bottom: 1px solid grey;">
                            '.substr($prodEC['date_debut_semis'], 8, 2).'/'.substr($prodEC['date_debut_semis'], 5, 2).'/'.substr($prodEC['date_debut_semis'], 0, 4).'
                        </td>
                        <td style="border-bottom: 1px solid grey;">';
                        $affiche_et = false;
                        if ( $prodEC['date_etape_1'] == '0000-00-00' )
                        {
                          echo '<input type="text" class="etape" name="et_1_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" id="et_1_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" />';
                          echo '<input type="checkbox" name="term_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" id="term_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" /> Terminé&nbsp;&nbsp;';
                          echo '<img src="/img/valider.png" id="val1_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" class="val1" style="width:20px;vertical-align: middle;cursor:pointer;display:none;" onclick="majEtape(1,'.$prodEC['id_product'].','.$prodEC['id_lot'].', $(\'#et_1_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'\').val(), $(\'#term_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'\').prop(\'checked\'));" />';
                          $affiche_et = true;
                        }
                        else
                        {
                          echo substr($prodEC['date_etape_1'], 8, 2).'/'.substr($prodEC['date_etape_1'], 5, 2).'/'.substr($prodEC['date_etape_1'], 0, 4).' = '.$prodEC['resultat_etape_1'].'%';
                        }
                        echo '</td>
                        <td style="border-bottom: 1px solid grey;">';
                        if ( $affiche_et == false && $prodEC['date_etape_2'] == '0000-00-00' )
                        {
                          echo '<input type="text" class="etape" name="et_2_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" id="et_2_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" />';
                          echo '<input type="checkbox" name="term_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" id="term_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" /> Terminé&nbsp;&nbsp;';
                          echo '<img src="/img/valider.png" id="val2_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" class="val1" style="width:20px;vertical-align: middle;cursor:pointer;display:none;" onclick="majEtape(2,'.$prodEC['id_product'].','.$prodEC['id_lot'].', $(\'#et_2_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'\').val(), $(\'#term_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'\').prop(\'checked\'));" />';
                          $affiche_et = true;
                        }
                        elseif ( $prodEC['date_etape_2'] != '0000-00-00' )
                        {
                          echo substr($prodEC['date_etape_2'], 8, 2).'/'.substr($prodEC['date_etape_2'], 5, 2).'/'.substr($prodEC['date_etape_2'], 0, 4).' = '.$prodEC['resultat_etape_2'].'%';
                        }
                        else {
                          echo '-';
                        }
                        echo '</td>
                        <td style="border-bottom: 1px solid grey;">';
                        if ( $affiche_et == false && $prodEC['date_etape_3'] == '0000-00-00' )
                        {
                          echo '<input type="text" class="etape" name="et_3_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" id="et_3_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" />';
                          echo '&nbsp;&nbsp;';
                          echo '<img src="/img/valider.png" id="val3_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'" class="val1" style="width:20px;vertical-align: middle;cursor:pointer;display:none;" onclick="majEtape(3,'.$prodEC['id_product'].','.$prodEC['id_lot'].', $(\'#et_3_'.$prodEC['id_product'].'_'.$prodEC['id_lot'].'\').val(), \'true\');" />';
                          $affiche_et = true;
                        }
                        elseif ( $prodEC['date_etape_3'] != '0000-00-00' )
                        {
                          echo substr($prodEC['date_etape_3'], 8, 2).'/'.substr($prodEC['date_etape_3'], 5, 2).'/'.substr($prodEC['date_etape_3'], 0, 4).' = '.$prodEC['resultat_etape_3'].'%';
                        }
                        else {
                          echo '-';
                        }
                        echo '</td>
                        <td><img src="/img/suppr.png" width="22px;" onclick="suppr_test('.$prodEC['id'].')" style="cursor:pointer;" /></td>
                        <td><img src="/img/valider.png" width="20px;" onclick="terminer_test('.$prodEC['id'].')" style="cursor:pointer;" /></td>
                    </tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
		
		echo '<div>
			<input type="button" class="js-ajout-test" value="Ajouter un test">
		</div>';
		
		// On récupère tous les produits 
		$sql = 'SELECT p.id_product, p.reference, pl.name FROM ps_product p
		INNER join ps_product_lang pl ON p.id_product =  pl.id_product
		WHERE pl.id_lang = 1
		ORDER BY pl.name';
		$product_list = Db::getInstance()->ExecuteS($sql);

		$product_list_str = "";
		foreach($product_list as $p){
			$product_list_str .= '<option value="'.$p['id_product'].'">'.$p['reference'].' '.$p['name'].'</option>';
		}

		echo '	<div class="modal_product" style="display:none;">
			<div class="modal_content">
				<span class="modal_close">&times;</span>
				<select class="js-product-content">
					<option value="0"> -- </option>
					'.$product_list_str.'
				</select>
				<br><br>
				
				<label>Lot</label>
				<select class="js-lots">

				</select>
			
				
				<br><br>
				<input type="button" class="js-submit-ajout-test" value="Ajouter">
			</div>
		</div>	';	


      echo '</body>
      </html>';
