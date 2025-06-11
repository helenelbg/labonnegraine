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
                  echo '<input type="text" class="etape" name="et_1_'.$prodEC['id_product'].'" id="et_1_'.$prodEC['id_product'].'" />';
                  echo '<input type="checkbox" name="term_'.$prodEC['id_product'].'" id="term_'.$prodEC['id_product'].'" /> Terminé&nbsp;&nbsp;';
                  echo '<img src="/img/valider.png" id="val1_'.$prodEC['id_product'].'" style="width:20px;vertical-align: middle;cursor:pointer;display:none;" onclick="majEtape(1,'.$prodEC['id_product'].','.$prodEC['id_lot'].', $(\'#et_1_'.$prodEC['id_product'].'\').val(), $(\'#term_'.$prodEC['id_product'].'\').prop(\'checked\'));" />';
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
                  echo '<input type="text" class="etape" name="et_2_'.$prodEC['id_product'].'" id="et_2_'.$prodEC['id_product'].'" />';
                  echo '<input type="checkbox" name="term_'.$prodEC['id_product'].'" id="term_'.$prodEC['id_product'].'" /> Terminé&nbsp;&nbsp;';
                  echo '<img src="/img/valider.png" id="val2_'.$prodEC['id_product'].'" style="width:20px;vertical-align: middle;cursor:pointer;display:none;" onclick="majEtape(2,'.$prodEC['id_product'].','.$prodEC['id_lot'].', $(\'#et_2_'.$prodEC['id_product'].'\').val(), $(\'#term_'.$prodEC['id_product'].'\').prop(\'checked\'));" />';
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
                  echo '<input type="text" class="etape" name="et_3_'.$prodEC['id_product'].'" id="et_3_'.$prodEC['id_product'].'" />';
                  echo '&nbsp;&nbsp;';
                  echo '<img src="/img/valider.png" id="val3_'.$prodEC['id_product'].'" style="width:20px;vertical-align: middle;cursor:pointer;display:none;" onclick="majEtape(3,'.$prodEC['id_product'].','.$prodEC['id_lot'].', $(\'#et_3_'.$prodEC['id_product'].'\').val(), \'true\');" />';
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
?>
