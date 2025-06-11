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
                <li><a href="#tabs-2">Mixtes</a></li>
            </ul>
            <div id="tabs-1">
                <?php 
                    foreach (CommandeLV::getPdas() as $pda)
                    {
                        //$orderByPda[$pda['id_pda']] = array('nom' => $pda['nom_pda'], 'commandes' => Commande::getCommandesByPda($pda['id_pda'], 1));
                        $orderByPda[$pda['id_pda']] = array('nom' => $pda['prenom_operateur'].' '.$pda['nom_operateur'], 'commandes' => CommandeLV::getCommandesByPda($pda['id_pda'], 1));
                    }

                    $zones = CommandeLV::getZones();
                    foreach($zones as $zone)
                    {
                        //if ( $zone['id_zone'] == 1 || $zone['id_zone'] == 2 || $zone['id_zone'] == 3 || $zone['id_zone'] == 5 )
                        if ( $zone['id_zone'] == -2 )
                        {
                            $cmds = CommandeLV::getOrdersByZone($zone['id_zone']);

                            $tab_en_cours_multi = array();
                            $req_EC = 'SELECT id_order FROM ps_LogiGraine_controle WHERE valide = 0;';
                            $resu_EC = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_EC);
                            foreach($resu_EC as $cmdEC)
                            {
                                if(($keyEC = array_search($cmdEC['id_order'], $cmds)) !== false) {
                                    $tab_en_cours_multi[] = $cmds[$keyEC];
                                    unset($cmds[$keyEC]);
                                    //$arr = array_values($messages);
                                }
                            }

                            $groups = CommandeLV::getGroups($cmds);

                            $groupsCmdTmp = array();
                            foreach($groups as $group)
                            {
                                $oListeTmp = '';
                                foreach($group['orders'] as $okTmp => $oTmp)
                                {
                                    if ( !empty($oListeTmp) )
                                    {
                                        $oListeTmp .= '_';
                                    }
                                    $oListeTmp .= $okTmp;
                                }
                                $groupsCmdTmp[] = $oListeTmp;
                            }

                            echo '<fieldset style="border: 2px solid '.$zone['couleur_zone'].'">';
                            echo '<div class="zone" style="background-color:'.$zone['couleur_zone'].';">'.$zone['libelle_zone'].'</div>';

                            $traite = array();

                            foreach($orderByPda as $id_pda => $pda)
                            {
                                $cpt_ck = 0;
                                foreach($pda['commandes'] as $cmd)
                                {
                                    $explode_multi = explode('_', $cmd);
                                    if ( in_array($explode_multi[0], $tab_en_cours_multi) )
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
                            foreach($groups as $group)
                            {
                                $oListe = '';
                                foreach($group['orders'] as $okTmp => $oTmp)
                                {
                                    if ( !empty($oListe) )
                                    {
                                        $oListe .= '_';
                                    }
                                    $oListe .= $okTmp;
                                }
                                if ( !in_array($oListe, $traite) )
                                {
                                    if ( $cpt_ck == 0 )
                                    {
                                        echo '<div class="pda"><span class="sur_rouge">Commandes non attribuées</span></div>';
                                        echo '<div class="btn-group" role="group" aria-label="...">
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone['id_zone'].'-\', 20);">20 groupes</button>
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone['id_zone'].'-\', 0);">Toutes</button>
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone['id_zone'].'-\', -1);">Aucune</button>
                                            </div>';
                                        echo '<select name="pdaCible-0-'.$zone['id_zone'].'" selector="0-'.$zone['id_zone'].'-" zone="'.$zone['id_zone'].'">';
                                        echo '<option value="">Choisir le PDA...</option>';
                                        foreach($orderByPda as $idEC => $pdaEC)
                                        {
                                            echo '<option value="'.$idEC.'">'.$pdaEC['nom'].'</option>';
                                        }
                                        echo '</select>';
                                        echo '<div>';
                                    }
                                    $cpt_ck++;
                                    echo '<label for="checkbox-0-'.$zone['id_zone'].'-'.$cpt_ck.'">'.$oListe.'</label>';
                                    echo '<input type="checkbox" value="'.$oListe.'" name="checkbox-0-'.$zone['id_zone'].'-'.$cpt_ck.'" id="checkbox-0-'.$zone['id_zone'].'-'.$cpt_ck.'">';
                                }
                            }
                            if ( $cpt_ck > 0 )
                            {
                                echo '</div>';
                            }
                            echo '</fieldset>';
                        }
                        /*
                        elseif ( $zone['id_zone'] != -1 && $zone['id_zone'] != 1 & $zone['id_zone'] != 2 && $zone['id_zone'] != 3 && $zone['id_zone'] != 4 && $zone['id_zone'] != 5 && $zone['id_zone'] != -4 )
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
                                        echo '<select name="pdaCible-0-'.$zone['id_zone'].'" selector="0-'.$zone['id_zone'].'-" zone="'.$zone['id_zone'].'">';
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
                        }*/
                    }
                ?>
            </div>
            <div id="tabs-2">
                <?php 
                    // DUO
                    /*$zonesDuo = array(
                        array(1,2,'Graines et Bulbes potagers'),
                        array(1,3,'Graines et Chambre 2'),
                        array(1,5,'Graines et Accessoires'),
                        array(2,3,'Bulbes potagers et Chambre 2'),
                        array(2,5,'Bulbes potagers et Accessoires'),
                        array(3,5,'Chambre 2 et Accessoires'),
                    );
                    $duoEC = array();
                    foreach($zonesDuo as $zone)
                    {
                        if ( ($zone[0] == 1 && $zone[1] == 2) || ($zone[0] == 1 && $zone[1] == 3) || ($zone[0] == 2 && $zone[1] == 3) || ($zone[0] == 1 && $zone[1] == 5) || ($zone[0] == 2 && $zone[1] == 5) || ($zone[0] == 3 && $zone[1] == 5) )
                        //if ( false )
                        {
                            // Cmd groupés
                            $cmds = Commande::getOrdersByDeuxZones($zone[0], $zone[1]);
                            $tab_en_cours_multi = array();
                            $req_EC = 'SELECT id_order FROM ps_LogiGraine_controle WHERE valide = 0;';
                            $resu_EC = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_EC);
                            foreach($resu_EC as $cmdEC)
                            {
                                if(($keyEC = array_search($cmdEC['id_order'], $cmds)) !== false) {
                                    $tab_en_cours_multi[] = $cmds[$keyEC];
                                    unset($cmds[$keyEC]);
                                    //$arr = array_values($messages);
                                }
                            }
                            $groups = Commande::getGroups($cmds);

                            $groupsCmdTmp = array();
                            foreach($groups as $group)
                            {
                                $oListeTmp = '';
                                foreach($group['orders'] as $okTmp => $oTmp)
                                {
                                    if ( !empty($oListeTmp) )
                                    {
                                        $oListeTmp .= '_';
                                    }
                                    $oListeTmp .= $okTmp;
                                }
                                $groupsCmdTmp[] = $oListeTmp;
                            }

                            echo '<fieldset style="border: 2px solid #e91e63">';
                            echo '<div class="zone" style="background-color:#e91e63;">'.$zone[2].'</div>';

                            $traite = array();

                            foreach($orderByPda as $id_pda => $pda)
                            {
                                $cpt_ck = 0;
                                foreach($pda['commandes'] as $cmd)
                                {
                                    $explode_multi = explode('_', $cmd);
                                    if ( in_array($explode_multi[0], $tab_en_cours_multi) )
                                    {
                                        if ( $cpt_ck == 0 )
                                        {
                                            echo '<div class="pda">'.$pda['nom'].'</div>';
                                            echo '<div class="btn-group" role="group" aria-label="...">
                                                    <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-\', 20);">20 commandes</button>
                                                    <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-\', 0);">Toutes</button>
                                                    <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-\', -1);">Aucune</button>
                                                </div>';
                                            echo '<select name="pdaCible-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'" selector="'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-">';
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
                                        echo '<label for="'.$valide.'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'" etat="'.$valide.'">'.$cmd.'</label>';
                                        echo '<input type="checkbox"'.$etat.' value="'.$cmd.'" name="'.$valide.'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'" id="'.$valide.'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'">';
                                    }
                                }
                            }

                            $cpt_ck = 0;
                            foreach($groups as $group)
                            {
                                $oListe = '';
                                foreach($group['orders'] as $okTmp => $oTmp)
                                {
                                    if ( !empty($oListe) )
                                    {
                                        $oListe .= '_';
                                    }
                                    $oListe .= $okTmp;
                                }
                                if ( !in_array($oListe, $traite) )
                                {
                                    if ( $cpt_ck == 0 )
                                    {
                                        echo '<div class="pda"><span class="sur_rouge">Commandes non attribuées</span></div>';
                                        echo '<div class="btn-group" role="group" aria-label="...">
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone[0].'_'.$zone[1].'-\', 20);">20 commandes</button>
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone[0].'_'.$zone[1].'-\', 0);">Toutes</button>
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone[0].'_'.$zone[1].'-\', -1);">Aucune</button>
                                            </div>';
                                        echo '<select name="pdaCible-0-'.$zone[0].'_'.$zone[1].'" selector="0-'.$zone[0].'_'.$zone[1].'-" zone="'.$zone[0].'_'.$zone[1].'">';
                                        echo '<option value="">Choisir le PDA...</option>';
                                        foreach($orderByPda as $idEC => $pdaEC)
                                        {
                                            echo '<option value="'.$idEC.'">'.$pdaEC['nom'].'</option>';
                                        }
                                        echo '</select>';
                                        echo '<div>';
                                    }
                                    $cpt_ck++;
                                    echo '<label for="checkbox-0-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'">'.$oListe.'</label>';
                                    echo '<input type="checkbox" value="'.$oListe.'" name="checkbox-0-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'" id="checkbox-0-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'">';
                                }
                            }
                            if ( $cpt_ck > 0 )
                            {
                                echo '</div>';
                            }
                            echo '</fieldset>';
                        }
                        else 
                        {
                            $cmds = Commande::getOrdersByDeuxZones($zone[0], $zone[1]);
                            echo '<fieldset style="border: 2px solid #e91e63">';
                            echo '<div class="zone" style="background-color:#e91e63;">'.$zone[2].'</div>';

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
                                                    <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-\', 20);">20 commandes</button>
                                                    <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-\', 0);">Toutes</button>
                                                    <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-\', -1);">Aucune</button>
                                                </div>';
                                            echo '<select name="pdaCible-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'" selector="'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-">';
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
                                        echo '<label for="'.$valide.'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'" etat="'.$valide.'">'.$cmd.'</label>';
                                        echo '<input type="checkbox"'.$etat.' value="'.$cmd.'" name="'.$valide.'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'" id="'.$valide.'checkbox-'.$id_pda.'-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'">';
                                    }
                                }
                            }

                            $cpt_ck = 0;
                            foreach($cmds as $cmd)
                            {
                                $duoEC[] = $cmd;
                                if ( !in_array($cmd, $traite) )
                                {
                                    if ( $cpt_ck == 0 )
                                    {
                                        echo '<div class="pda"><span class="sur_rouge">Commandes non attribuées</span></div>';
                                        echo '<div class="btn-group" role="group" aria-label="...">
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone[0].'_'.$zone[1].'-\', 20);">20 commandes</button>
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone[0].'_'.$zone[1].'-\', 0);">Toutes</button>
                                                <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone[0].'_'.$zone[1].'-\', -1);">Aucune</button>
                                            </div>';
                                        echo '<select name="pdaCible-0-'.$zone[0].'_'.$zone[1].'" selector="0-'.$zone[0].'_'.$zone[1].'-" zone="'.$zone[0].'_'.$zone[1].'">';
                                        echo '<option value="">Choisir le PDA...</option>';
                                        foreach($orderByPda as $idEC => $pdaEC)
                                        {
                                            echo '<option value="'.$idEC.'">'.$pdaEC['nom'].'</option>';
                                        }
                                        echo '</select>';
                                        echo '<div>';
                                    }
                                    $cpt_ck++;
                                    echo '<label for="checkbox-0-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'">'.$cmd.'</label>';
                                    echo '<input type="checkbox" value="'.$cmd.'" name="checkbox-0-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'" id="checkbox-0-'.$zone[0].'_'.$zone[1].'-'.$cpt_ck.'">';
                                }
                            }
                            if ( $cpt_ck > 0 )
                            {
                                echo '</div>';
                            }
                            echo '</fieldset>';
                        }
                    }*/

                    // MIXTES
                    /*$zones = Commande::getZones(-1);
                    foreach($zones as $zone)
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
                                if ( in_array($cmd, $cmds) && !in_array($cmd, $duoEC) )
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
                            if ( !in_array($cmd, $traite) && !in_array($cmd, $duoEC)  )
                            {
                                if ( $cpt_ck == 0 )
                                {
                                    echo '<div class="pda"><span class="sur_rouge">Commandes non attribuées</span></div>';
                                    echo '<div class="btn-group" role="group" aria-label="...">
                                            <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone['id_zone'].'-\', 20);">20 commandes</button>
                                            <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone['id_zone'].'-\', 0);">Toutes</button>
                                            <button type="button" class="btn btn-default" onclick="checkOrder(\'checkbox-0-'.$zone['id_zone'].'-\', -1);">Aucune</button>
                                        </div>';
                                    echo '<select name="pdaCible-0-'.$zone['id_zone'].'" selector="0-'.$zone['id_zone'].'-" zone="'.$zone['id_zone'].'">';
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
                    }*/
                ?>
            </div>
        </div>
    </div>
    <?php include('../footer.php'); ?>
  </body>
</html>