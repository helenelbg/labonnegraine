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

$html .= '<div>
      <script type="text/javascript">
        $(function() {
         $(".datepickerLot").datepicker({
          dateFormat:"yy-mm-dd",
          prevText:"",
          nextText:""});
        });
      </script>
      <form name="formInventaire" action="" method="post">
       <table class="table" border="0" cellspacing="0" cellspacing="0" width="100%">
         <thead>
          <tr>
           <th style="text-align:left">Référence</th>
           <th style="text-align:left">Nom</th>
           <th style="text-align:left">Date Dernier Inv.</th>
           <th></th>
           <th style="display:none;">Stock Théor.</th>
           <th style="text-align:left">Stock Site</th>
           <th style="text-align:left">Alerte (en g.)</th>
         </tr>
       </thead><tbody>';
       $total_theorique_valeur = 0;

       $products = Db::getInstance()->ExecuteS('
      SELECT p.`id_product`, p.reference, pl.`name`
      FROM `' . _DB_PREFIX_ . 'product` p
      LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`
      WHERE pl.`id_lang` = 1 AND p.id_product = "'.$_GET['idp'].'"
      AND p.active=1 ORDER BY pl.`name`');

      /*$products = Db::getInstance()->ExecuteS('
      SELECT p.`id_product`, p.reference, pl.`name`, (p.quantity + IFNULL((SELECT SUM(pa.quantity) FROM ' . _DB_PREFIX_ . 'product_attribute pa WHERE pa.id_product = p.id_product GROUP BY pa.id_product), 0)) as quantity
      FROM `' . _DB_PREFIX_ . 'product` p
      LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`
      WHERE pl.`id_lang` = 2 AND p.id_product = "'.$_GET['idp'].'"
      AND p.active=1 ORDER BY pl.`name`');*/ 

       foreach ($products AS $product)
       {


        $id_stock_presta_p = StockAvailable::getStockAvailableIdByProductId($product['id_product']);
        $stockAvailableProduct = new StockAvailable($id_stock_presta_p);
        $ale = Db::getInstance()->ExecuteS('SELECT valeur FROM ps_alerte WHERE id_product = "' . $product['id_product'] . '" ORDER BY id DESC LIMIT 0,1;');
        $html .= '<tr><td>' . $product['reference'] . '</td><td colspan="3"><font style="color: #000000">' . $product['name'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a style="color:blue" href="javascript:afficher_lot_b(' . $product['id_product'] . ')">Informations lots</a></font></td><td>'.$stockAvailableProduct->quantity.'</td><td>' . @$ale[0]['valeur'] . '</td></tr>';
        $html .= '<tr id="info_lots_product_' . $product['id_product'] . '" class="detail_lot info_lots_product_' . $product['id_product'] . '" style="display: none;"><td colspan="7">
        <table id="lot_num_' . $ref_fourn_inv['id_inventaire_lots'] . '" class="lot_inv table_detail">
          <thead>
            <th>Fournisseur</th>
            <th>N&deg; lot origine</th>
            <th>Date appro</th>
            <th width="100px">Graines / Grammes</th>
            <th>Quantité appro</th>
            <th>N&deg; lot LBG</th>
            <th>Origine test Frns / LBG</th>
            <th>Date fin du test de germinaton</th>
            <th>Pourcentage germination</th>
            <th colspan="3">Commentaires</th>
            <th></th>
          </thead>
          <tbody>';
            $refs_fournisseur_inventaires = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire_lots WHERE id_product = "' . $product['id_product'] . '" ORDER BY date_approvisionnement DESC;');
            foreach ($refs_fournisseur_inventaires as $ref_fourn_inv)
            {
                /**** dernier test ***/
                $result_origine_test_lot = Db::getInstance()->ExecuteS('SELECT commentaire, origine_test, date_etape_1,resultat_etape_1,date_etape_2,resultat_etape_2,date_etape_3,resultat_etape_3 FROM AW_test_lots WHERE id_lot = "' . $ref_fourn_inv['id_inventaire_lots'] . '" ORDER BY id DESC LIMIT 1');
                    if($result_origine_test_lot[0]['date_etape_3']!='0000-00-00')
                            $date_fin_test=$result_origine_test_lot[0]['date_etape_3'];
                    elseif($result_origine_test_lot[0]['date_etape_2']!='0000-00-00')
                            $date_fin_test=$result_origine_test_lot[0]['date_etape_2'];
                    elseif($result_origine_test_lot[0]['date_etape_1']!='0000-00-00')
                             $date_fin_test=$result_origine_test_lot[0]['date_etape_1'];
                    else
                            $date_fin_test="";
                    if($result_origine_test_lot[0]['resultat_etape_3']!=0)
                            $resultat_test=$result_origine_test_lot[0]['resultat_etape_3'];
                    elseif($result_origine_test_lot[0]['resultat_etape_2']!=0)
                            $resultat_test=$result_origine_test_lot[0]['resultat_etape_2'];
                    elseif($result_origine_test_lot[0]['resultat_etape_1']!=0)
                             $resultat_test=$result_origine_test_lot[0]['resultat_etape_1'];
                    else
                            $resultat_test="0";

                if($result_origine_test_lot[0]['origine_test']!="")$origine_test_lot=$result_origine_test_lot[0]['origine_test'];
                else $origine_test_lot="";
                $commentaire_test_lot= $result_origine_test_lot[0]['commentaire'];
                if(count($result_origine_test_lot)==0)
                {
                    $resultat_test=$ref_fourn_inv['percent_germination'];
                    $date_fin_test= $date_germ;
                    $commentaire_test_lot=$ref_fourn_inv['commentaire'];
                }
                if(count($result_origine_test_lot)>0)
                    $origine_test_lot=$result_origine_test_lot[0]["origine_test"];
                else  $origine_test_lot="Frns";
              $date_appro = $ref_fourn_inv['date_approvisionnement'];
              $array_date_appro = explode('-', $date_appro);
              $date_appro = @$array_date_appro[2] . "/" . @$array_date_appro[1] . "/" . @$array_date_appro[0];

              $date_germ = $ref_fourn_inv['date_test_germination'];
              $array_date_germ = explode('-', $date_germ);
              $date_germ = @$array_date_germ[2] . "/" . @$array_date_germ[1] . "/" . @$array_date_germ[0];
              $qty_lot = $ref_fourn_inv['quantite'];
              $graine_gramme = $ref_fourn_inv['graine_gramme'];

              $html .='
              <tr>
                <td id="ref_fourn_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $ref_fourn_inv['fournisseur'] . '</td>
                <td align="left" id="num_lot_org_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $ref_fourn_inv['numero_lot_origine'] . '</td>
                <td align="left" id="date_appro_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $date_appro . '</td>
                <td align="left" id="graine_gramme_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $graine_gramme . '</td>
                <td align="left" id="quantite_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $qty_lot . '</td>
                <td id="num_lot_LBG_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $ref_fourn_inv['numero_lot_LBG'] . '</td>
                <td id="remplir">'.$origine_test_lot.'</td>
                <td align="left" id="date_germ_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $date_fin_test . '</td>
                <td align="left" id="germ_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $resultat_test . '%</td>
                <td colspan="3" align="left" id="commentaire_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $commentaire_test_lot . '</td>';

                $test_lots = Db::getInstance()->ExecuteS('SELECT * FROM AW_test_lots WHERE id_lot = "' . $ref_fourn_inv['id_inventaire_lots'] . '"');
                $html .='
                <td>
                  <div class="conteneur_lb" id="conteneur_lb_'. $ref_fourn_inv['id_inventaire_lots'] .'">
                    <div class="lightbox" style="text-align:center">
                      <h2 class="margin0" style="text-align: center;padding: 10px;border-bottom: 1px solid black;">Liste des tests - '.$product['name'].' - Lot n&deg;'.$ref_fourn_inv['numero_lot_LBG'].'</h2>
                      <table width="900px" class="center">
                        <thead>
                          <tr>
                            <th colspan="1" style="width:90px;">Date fin<br />Germination</th>
                            <th colspan="1" style="width:90px;">%<br />Germination</th>
                            <th colspan="8" style="width:700px;">Commentaire</th>
                          </tr>
                        </thead>
                        <tbody>';

                          foreach ($test_lots as $test_lot)
                          {
                            if (!empty($test_lot['resultat_etape_3'])) {
                              $pourc_germ = $test_lot['resultat_etape_3'];
                            }
                            elseif (!empty($test_lot['resultat_etape_2'])) {
                              $pourc_germ = $test_lot['resultat_etape_2'];
                            }
                            else{
                              $pourc_germ = $test_lot['resultat_etape_1'];
                            }
                          if($test_lot['date_etape_3']!='0000-00-00')
                            $date_fin_test=$test_lot['date_etape_3'];
                          elseif($test_lot['date_etape_2']!='0000-00-00')
                            $date_fin_test=$test_lot['date_etape_2'];
                          elseif($test_lot['date_etape_1']!='0000-00-00')
                             $date_fin_test=$test_lot['date_etape_1'];
                          else
                            $date_fin_test="";

                            $html .='
                            <tr class="border_top_fonce click_display">
                              <th>'.$date_fin_test.'</th>
                              <th class="center">'.$pourc_germ.'</th>
                              <th colspan="8">'.$test_lot['commentaire'].'</th>
                            </tr>
                            <tr class="display_'.$test_lot['id'].' border_claire">
                              <th></th>
                              <th></th>
                              <th></th>
                              <th colspan="2">Etape 1</th>
                              <th colspan="2">Etape 2</th>
                              <th colspan="2">Etape 3</th>
                            </tr>
                            <tr class="display_'.$test_lot['id'].' border_claire">
                            <th></th>
                            <th></th><th>Date d&eacute;but test</th>
                              <th>Date</th>
                              <th>Resultat</th>
                              <th>Date</th>
                              <th>Resultat</th>
                              <th>Date</th>
                              <th>Resultat</th>
                              <th>Origine test</th>
                            </tr>
                            <tr class="display_'.$test_lot['id'].' border_claire">
                            <th></th>
                            <th></th><th>'.$test_lot['date_debut_semis'].'</th>
                              <td>'.$test_lot['date_etape_1'].'</td>
                              <td>'.$test_lot['resultat_etape_1'].'</td>
                              <td>'.$test_lot['date_etape_2'].'</td>
                              <td>'.$test_lot['resultat_etape_2'].'</td>
                              <td>'.$test_lot['date_etape_3'].'</td>
                              <td>
                                '.$test_lot['resultat_etape_3'].'
                              </td>
                               <td>
                                '.$test_lot['origine_test'].'
                              </td>
                            </tr>';

                          }

                          $html .='
                        </tbody>
                      </table>
                    </div>
                  </div>
                </td>
                <td style="width:110px"><button type="button" onclick="javascript:voir_test(' . $ref_fourn_inv['id_inventaire_lots'] . ')"><i class="fas fa-search-plus"></i></button></td>
              </tr>';
            }


            $date_courante = date('Y-m-d');

            $html .='
          </tbody>
        </table>';


                        /*$html .='<a id="lot_produit' . $product['id_product'] . '"></a><b><u><i>Ajouter un suivi de lot: </i></u></b><br /><table><tr><td align="right"><u>Fournisseur :</u></td><td align="left"><input type="text" id="fournis' . $product['id_product'] . '" value="" /><td align="right"><u>N&deg; lot d\'origine :</u></td>
                        <td align="left"><input type="text" id="lot_org' . $product['id_product'] . '" value="" /></td></tr><tr><td align="right"><u>Date approvisionnement :</u></td><td align="left"><input type="text" class="datepickerLot" id="date_appro' . $product['id_product'] . '" value="' . $date_courante . '" /></td><td align="right"><u>Quantite r&eacute;assort (en g.) :</u></td><td align="left"><input type="text" id="quantite' . $product['id_product'] . '" value="" /></tr>
                        <tr><td align="right"><u>N&deg;lot LBG :</u></td><td align="left"><input type="text" id="lot_LBG' . $product['id_product'] . '" value="" /></td><td align="right"><u>Date test germination :</u></td><td align="left"><input type="text" class="datepickerLot" id="date_germ' . $product['id_product'] . '" value="' . $date_courante . '" /></td></tr>
                        <tr><td align="right"><u>Pourcentage germination :</u></td><td align="left"><input type="text" id="pourcent_germ' . $product['id_product'] . '" value="" /></td></tr>
                        <tr><td align="right"><u>Commentaire :</u></td><td colspan="3" align="left"><textarea rows="3" cols="80" id="comm' . $product['id_product'] . '"></textarea></td></tr>';
                        $html .= '<table><center><input type="button" id="button_' . $product['id_product'] . '" value="Ajouter ce lot" onclick="submit_form_lot(' . $product['id_product'] . ')"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" id="button_reset_' . $product['id_product'] . '" value="Remettre &agrave; z&eacute;ro" onclick="reset_lot_add(' . $product['id_product'] . ')"/></center>';
                        $html .= '</table><hr class="hr_lots" />';
                        $html .= '</td></tr>';*/
                        $poids_theorique = 0;
                        $rangee_attrib = Db::getInstance()->ExecuteS('SELECT * FROM ps_product_attribute WHERE id_product = "' . $product['id_product'] . '";');
                        $nb_graine = 0;
                        foreach ($rangee_attrib AS $attrib)
                        {
                          $aux_dec = array();
                          $qt_commandee = 0;
                          $stock_theorique = 0;
                          $rangee_comb = Db::getInstance()->ExecuteS('SELECT * FROM ps_product_attribute_combination WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '";');
                          foreach ($rangee_comb AS $comb)
                          {
                        //echo 'SELECT * FROM ps_attribute_lang WHERE id_attribute = "'.$comb['id_attribute'].'" AND id_lang = 1;';
                            $dec = Db::getInstance()->ExecuteS('SELECT name FROM ps_attribute_lang WHERE id_attribute = "' . $comb['id_attribute'] . '" AND id_lang = 1 LIMIT 0,1;');
                            $aux_dec[] = $dec[0]['name'];
                          }
                          sort($aux_dec);
                          $libelle_dec = implode(' - ', $aux_dec);

                          $inv = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');
                          if (!empty($inv[0]['date']))
                          {
                            $jour_inv = substr($inv[0]['date'], 6, 2);
                            $mois_inv = substr($inv[0]['date'], 4, 2);
                            $annee_inv = substr($inv[0]['date'], 0, 4);
                            $heure_inv = substr($inv[0]['date'], 8, 2);
                            $minutes_inv = substr($inv[0]['date'], 10, 2);
                        //$der_inv = ' <font color="#808080">('.$jour_inv.'/'.$mois_inv.'/'.$annee_inv.' '.$heure_inv.':'.$minutes_inv.' = '.$inv[0]['valeur'].')</font>';
                            //$der_inv = '<td>' . $jour_inv . '/' . $mois_inv . '/' . $annee_inv . '</td><td>' . $inv[0]['valeur'] . '</td>';
                            $der_inv = '<td>' . $jour_inv . '/' . $mois_inv . '/' . $annee_inv . '</td><td></td>';
                            $str_inv =  $jour_inv . '/' . $mois_inv . '/' . $annee_inv . ';'.$inv[0]['valeur'];
                          }
                          else
                          {
                            $der_inv = '<td>&nbsp;</td><td>&nbsp;</td>';
                          }
                          if ($der_inv != '<td>&nbsp;</td><td>&nbsp;</td>')
                          {
                        // Somme des quantit�s command�es depuis de dernier inventaire
                        //$commandes = Db::getInstance()->ExecuteS('SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "'.$product['id_product'].'" AND pod.product_attribute_id = "'.$attrib['id_product_attribute'].'" AND po.date_add > "'.$annee_inv.'-'.$mois_inv.'-'.$jour_inv.' '.$heure_inv.':'.$minutes_inv.'";');
                    /*        $commandes = Db::getInstance()->ExecuteS('SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "' . $product['id_product'] . '" AND pod.product_attribute_id = "' . $attrib['id_product_attribute'] . '" AND po.date_add > "' . $annee_inv . '-' . $mois_inv . '-' . $jour_inv . ' ' . $heure_inv . ':' . $minutes_inv . '" AND (SELECT logable FROM ps_order_state WHERE id_order_state LIKE (SELECT id_order_state FROM ps_order_history WHERE id_order = po.id_order ORDER BY date_add DESC LIMIT 0,1)) LIKE 1;');*/


                        //mail('aurelien@anjouweb.com', 'test statsstocksinventaire.php','SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "'.$product['id_product'].'" AND pod.product_attribute_id = "'.$attrib['id_product_attribute'].'" AND po.date_add > "'.$annee_inv.'-'.$mois_inv.'-'.$jour_inv.' '.$heure_inv.':'.$minutes_inv.'";');
              /*              foreach ($commandes AS $commande)
                            {
                              $qt_commandee += $commande['product_quantity'];
                            }
                            $stock_theorique = $inv[0]['valeur'] - $qt_commandee;*/
                            //if ( $product['id_product'] == 100 ) error_log('valeur : '.$inv[0]['valeur']);
                            //if ( $product['id_product'] == 100 ) error_log('qt_commandee : '.$qt_commandee);
                            //if ( $product['id_product'] == 100 ) error_log('stock_theorique : '.$stock_theorique);


                            $qtec = Db::getInstance()->ExecuteS('SELECT * FROM ps_stock_available WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "' . $attrib['id_product_attribute'] . '";');

                            $poids_theorique += $qtec[0]['quantity'] * $attrib['weight'];
                            $stock_theorique = $qtec[0]['quantity'];
                          }
                          $id_stock_presta = StockAvailable::getStockAvailableIdByProductId($product['id_product'], $attrib['id_product_attribute']);
                          $stockAvailable = new StockAvailable($id_stock_presta);

                         // print_r($reference);
                          $html .= '<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;' . $libelle_dec . '</td>' . $der_inv . '<td style="display:none;">' . $stock_theorique . '</td><td>'.$stockAvailable->quantity.'</td></tr>';
                          //$html .= '<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;' . $libelle_dec . '</td>' . $der_inv . '<td>' . $stock_theorique . '</td><td><input type="text" name="' . $product['id_product'] . '#' . $attrib['id_product_attribute'] . '" style="width:50px;" /></td><td>'.$stockAvailable->quantity.'</td><td>&nbsp;</td></tr>';



                          $nb_graine_sach = substr($libelle_dec, 0, strpos($libelle_dec, ' '));
                          $nb_graine_prod = (int)$nb_graine_sach * $stock_theorique;
                          $nb_graine = $nb_graine_prod + $nb_graine;
                        }
                        $qt_reassort = 0;
                        $stock_theorique_tamp = 0;
                        $inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "0" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');
                //SELECT max(date), SUM(valeur) as valeur FROM ps_inventaire WHERE id_product_attribute = "0" AND id_product = "198" GROUP BY SUBSTR(date,0,8) ORDER BY date DESC LIMIT 0,1
                //$inv_tamp = Db::getInstance()->ExecuteS('SELECT max(date) as date, SUM(valeur) as valeur FROM ps_inventaire WHERE id_product_attribute = "0" AND id_product = "'.$product['id_product'].'" GROUP BY SUBSTR(date,0,8) ORDER BY date DESC LIMIT 0,1;');

                        $total_site_tamp = $inv_tamp[0]['valeur'];
                        if (!empty($inv_tamp[0]['date']))
                        {
                          $jour_inv_tamp = substr($inv_tamp[0]['date'], 6, 2);
                          $mois_inv_tamp = substr($inv_tamp[0]['date'], 4, 2);
                          $annee_inv_tamp = substr($inv_tamp[0]['date'], 0, 4);
                          $heure_inv_tamp = substr($inv_tamp[0]['date'], 8, 2);
                          $minutes_inv_tamp = substr($inv_tamp[0]['date'], 10, 2);
                    //$der_inv = ' <font color="#808080">('.$jour_inv.'/'.$mois_inv.'/'.$annee_inv.' '.$heure_inv.':'.$minutes_inv.' = '.$inv[0]['valeur'].')</font>';
                          //$der_inv_tamp = '<td>' . $jour_inv_tamp . '/' . $mois_inv_tamp . '/' . $annee_inv_tamp . '</td><td>' . $inv_tamp[0]['valeur'] . '</td>';
                          $der_inv_tamp = '<td>' . $jour_inv_tamp . '/' . $mois_inv_tamp . '/' . $annee_inv_tamp . '</td><td></td>';
                          $str_der_inv_tamp = $jour_inv_tamp . '/' . $mois_inv_tamp . '/' . $annee_inv_tamp . ';' . $inv_tamp[0]['valeur'];
                        }
                        else
                        {
                          $der_inv_tamp = '<td>&nbsp;</td><td>&nbsp;</td>';
                        }
                        if ($der_inv_tamp != '<td>&nbsp;</td><td>&nbsp;</td>')
                        {
                    // Somme des quantit�s command�es depuis de dernier inventaire
                          $reassorts = Db::getInstance()->ExecuteS('SELECT * FROM ps_reassort WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "0" AND date > "' . $annee_inv_tamp . $mois_inv_tamp . $jour_inv_tamp . $heure_inv_tamp . $minutes_inv_tamp . '";');
                          foreach ($reassorts AS $reassort)
                          {
                            $qt_reassort += $reassort['valeur'];
                          }
                          $stock_theorique_tamp = $inv_tamp[0]['valeur'] + $qt_reassort;
                        }

                        $html .= '<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;Stock tampon</td>' . $der_inv_tamp .'<td style="display:none"></td><td>'.$total_site_tamp.'</td></tr>';
                        if ($graine_gramme == "graine") {
                          $stock_total = $stock_theorique_tamp + $nb_graine;
                        }
                        else{
                          $stock_total =1000* $poids_theorique + $stock_theorique_tamp;
                        }
                        $html .= '<tr><td></td><td colspan=3 style="text-align:right">&nbsp;&nbsp;&nbsp;&nbsp;Stock Total : </td><td>' . $stock_total .'</td>';
                //<td><input type="text" name="'.$product['id_product'].'#reassort" value="" style="width:50px;"/></td>
                        $html .= '</tr>';
                        $product_complet_use = new Product($product['id_product']);
                        $prix_achat = floatval($product_complet_use->wholesale_price) / 1000;
                        if ($graine_gramme == "graine") {
                          $prix_stock_theorique = Tools::ps_round(floatval($prix_achat)*($stock_theorique + $stock_theorique_tamp + $nb_graine));
                        }
                        else{
                          $prix_stock_theorique = Tools::ps_round(floatval($prix_achat) * floatval(($poids_theorique * 1000) + $stock_theorique_tamp), 2);
                        }
                        if ($prix_stock_theorique < 0)
                        {
                          $prix_stock_theorique = 0;
                        }
                        $total_theorique_valeur += $prix_stock_theorique;
                        $html .= '<tr class="border_bottom_inventaire"><td>&nbsp;</td><td colspan=3 style="text-align:right">&nbsp;&nbsp;&nbsp;&nbsp;Valeur : </td><td>' . $prix_stock_theorique . ' &euro;</td></tr>';
                      }
                      $html .= '</tbody></table><input type="hidden" name="maj" value="ok" /></form></div>';

$html .= '<script>
function afficher_lot_b(numero_lot) {
	if ($(".info_lots_product_"+numero_lot).is(":visible")){
		$(".info_lots_product_"+numero_lot).css("display","none");
	}else{
		$(".info_lots_product_"+numero_lot).css("display","table-row");
	}
}
</script>';		  
echo '<div class="modal" style="max-width:100%">';
echo $html;
echo '</div>';
?>
