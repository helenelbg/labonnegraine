<?php
  echo '<div class="modal" style="max-width: max-content;">';

  echo '<table width="100%" class="center table_display" id="ligne_ajout_test_'. $ref_fourn_inv['id_inventaire_lots'] .'">
    <tbody>
      <tr class="border_claire">
        <th></th>
        <th colspan="2">Etape 1</th>
        <th colspan="2">Etape 2</th>
        <th colspan="2">Etape 3</th>
        <th></th>
      </tr>
      <tr class="border_claire">
        <th>Date d&eacute;but test</th>
        <th colspan="0.5">Date</th>
        <th>Resultat</th>
        <th>Date</th>
        <th>Resultat</th>
        <th>Date</th>
        <th>Resultat</th>

      </tr>
      <tr class="border_claire">
        <th><input placeholder="Date d&eacute;but de semis" type="text" class="datepickerLot date_debut_test" name="date_debut_test" /></th>
        <td><input type="hidden" name="id_lot" id="id_lot" value="'. $ref_fourn_inv['id_inventaire_lots'] .'"><input type="text" class="datepickerLot date_etape_1" name="date_etape_1" /></td>
        <td><input type="text" name="resultat_etape_1" id="resultat_etape_1"/></td>
        <td><input type="text" class="datepickerLot date_etape_2" name="date_etape_2" ></td>
        <td><input type="text" name="resultat_etape_2" id="resultat_etape_2"/></td>
        <td><input type="text" class="datepickerLot date_etape_3" name="date_etape_3" /></td>
        <td><input type="text" name="resultat_etape_3" id="resultat_etape_3"/></td>

      </tr>
      <tr>
        <th colspan="9"><textarea placeholder="Mettre un commentaire" name="commentaire_test" id="commentaire_test" style="width: 100%;height: 75px;"></textarea></th>
      </tr>
      <tr>
        <td colspan="9"><button class="ajout_test" id="ajout_test_'. $ref_fourn_inv['id_inventaire_lots'] .'"><i class="process-icon-new" style="font-size:14px; width: auto; height: auto; display:inline-block; color: initial;"></i> Ajouter le test</button></td>
      </tr>
    </tbody>
  </table>';

  echo '</div>';
 ?>
