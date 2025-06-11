<?php
    require '../application_top.php';

    $title = 'Affectation des commandes';
    include('../top.php'); 
    include('../header.php'); 
?>
    <div class="container">
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Zone unique</a></li>
                <li><a href="#tabs-2">2 zones</a></li>
                <li><a href="#tabs-3">Mixtes</a></li>
            </ul>
            <div id="tabs-1">
                <?php 
                    foreach (Commande::getPdas() as $pda)
                    {
                        $orderByPda[$pda['id_pda']] = array('nom' => $pda['nom_pda'], 'commandes' => Commande::getCommandesByPda($pda['id_pda'], 1));
                    }

                    $zones = Commande::getZones();
                    foreach($zones as $zone)
                    {
                        if ( $zone['id_zone'] != -1 )
                        {
                            $cmds = Commande::getOrdersByZone($zone['id_zone']);
                            echo '<fieldset style="border: 2px solid '.$zone['couleur_zone'].'">';
                            echo '<div class="zone" style="background-color:'.$zone['couleur_zone'].';">'.$zone['libelle_zone'].'</div>';

                            $traite = array();

                            foreach($orderByPda as $id_pda => $pda)
                            {
                                $cpt_ck = 0;
                                foreach($pda['commandes'] as $cmd)
                                {
                                    if ( in_array($cmd, $cmds) )
                                    {
                                        if ( $cpt_ck == 0 )
                                        {
                                            echo '<div class="pda">'.$pda['nom'].'</div>';
                                            echo '<div class="btn-group" role="group" aria-label="...">
                                                    <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-'.$id_pda.'-'.$zone['id_zone'].'-\', 20);">20 commandes</button>
                                                    <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-'.$id_pda.'-'.$zone['id_zone'].'-\', 0);">Toutes</button>
                                                    <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-'.$id_pda.'-'.$zone['id_zone'].'-\', -1);">Aucune</button>
                                                </div>';
                                            echo '<select name="pdaCible-'.$id_pda.'-'.$zone['id_zone'].'" selector="'.$id_pda.'-'.$zone['id_zone'].'-">';
                                            echo '<option value="">Choisir le PDA...</option>';
                                            echo '<option value="-1">RAZ</option>';
                                            foreach($orderByPda as $idEC => $pdaEC)
                                            {
                                                echo '<option value="'.$idEC.'">'.$pdaEC['nom'].'</option>';
                                            }
                                            echo '</select>';
                                            echo '<div>';
                                        }
                                        $cpt_ck++;
                                        $traite[] = $cmd;
                                        $controleEC = new Controle($cmd);
                                        $etat = '';
                                        $valide = '';
                                        if ( $controleEC->date_debut != '0000-00-00 00:00:00' )
                                        {
                                            $etat = ' disabled="disabled"';
                                            $valide = 'encours';
                                        }
                                        if ( $controleEC->valide == '1' )
                                        {
                                            $valide = 'valide';
                                        }
                                        echo '<label for="'.$valide.'checkbox-'.$id_pda.'-'.$zone['id_zone'].'-'.$cpt_ck.'" etat="'.$valide.'">'.$cmd.'</label>';
                                        echo '<input type="checkbox"'.$etat.' value="'.$cmd.'" name="'.$valide.'checkbox-'.$id_pda.'-'.$zone['id_zone'].'-'.$cpt_ck.'" id="'.$valide.'checkbox-'.$id_pda.'-'.$zone['id_zone'].'-'.$cpt_ck.'">';
                                    }
                                }
                            }

                            $cpt_ck = 0;
                            foreach($cmds as $cmd)
                            {
                                if ( !in_array($cmd, $traite) )
                                {
                                    if ( $cpt_ck == 0 )
                                    {
                                        echo '<div class="pda"><span class="sur_rouge">Commandes non attribuées</span></div>';
                                        echo '<div class="btn-group" role="group" aria-label="...">
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone['id_zone'].'-\', 20);">20 commandes</button>
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone['id_zone'].'-\', 0);">Toutes</button>
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone['id_zone'].'-\', -1);">Aucune</button>
                                            </div>';
                                        echo '<select name="pdaCible-0-'.$zone['id_zone'].'" selector="0-'.$zone['id_zone'].'-">';
                                        echo '<option value="">Choisir le PDA...</option>';
                                        foreach($orderByPda as $idEC => $pdaEC)
                                        {
                                            echo '<option value="'.$idEC.'">'.$pdaEC['nom'].'</option>';
                                        }
                                        echo '</select>';
                                        echo '<div>';
                                    }
                                    $cpt_ck++;
                                    echo '<label for="checkbox-0-'.$zone['id_zone'].'-'.$cpt_ck.'">'.$cmd.'</label>';
                                    echo '<input type="checkbox" value="'.$cmd.'" name="checkbox-0-'.$zone['id_zone'].'-'.$cpt_ck.'" id="checkbox-0-'.$zone['id_zone'].'-'.$cpt_ck.'">';
                                }
                            }
                            if ( $cpt_ck > 0 )
                            {
                                echo '</div>';
                            }
                            echo '</fieldset>';
                        }
                    }
                ?>
            </div>
            <div id="tabs-2">
            </div>
            <div id="tabs-3">
                <?php 
                    /*$zones = Commande::getZones(-1);
                    foreach($zones as $zone)
                    {
                        $cpt_ck = 0;
                        $cmds = Commande::getOrdersByZone($zone['id_zone']);
                        echo '<fieldset style="border: 2px solid '.$zone['couleur_zone'].'">';
                        echo '<div class="zone" style="background-color:'.$zone['couleur_zone'].';">'.$zone['libelle_zone'].'</div>';
                        foreach($cmds as $cmd)
                        {
                            $cpt_ck++;
                            echo '<label for="checkbox-'.$zone['id_zone'].'-'.$cpt_ck.'">'.$cmd.'</label>';
                            echo '<input type="checkbox" name="checkbox-'.$zone['id_zone'].'-'.$cpt_ck.'" id="checkbox-'.$zone['id_zone'].'-'.$cpt_ck.'">';
                        }
                        echo '</fieldset>';
                    }*/
                ?>
            </div>
        </div>
    </div>
    <?php include('../footer.php'); ?>
  </body>
</html>