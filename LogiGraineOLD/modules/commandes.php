<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(2, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Scan commandes';
    include('../top.php'); 
    include('../header.php'); 
?>
    <div class="container">
        <?php
            if ( count($liste_commandes) > 0 )
            {                
                $controleEC = new Controle($liste_commandes[0]);
                
                if ( $controleEC->id_caisse == 0 )
                {
                    if ( isset($_POST['codeProduit']) && !empty($_POST['codeProduit']) )
                    {
                        unset($_POST['codeProduit']);
                    }
                }

                if ( isset($_POST['codeCaisse']) && !empty($_POST['codeCaisse']) )
                {
                    $tailleAPrendre = Caisse::getTailleCaisseByCommande($controleEC->id_order);
                    if ( ($retourCaisse = $controleEC->scanCaisse($_POST['codeCaisse'], $tailleAPrendre)) !== true )
                    {
                        ?>
                        <script type="text/javascript">
                            $( document ).ready(function() {
                                $('#myModalErreur .modal-title').html('Erreur');
                                $('#myModalErreur .modal-body').html("<?php echo $retourCaisse; ?>");
                                $('#myModalErreur').modal('show');
                                $('#myModalErreur .btnFermer').attr('onclick', "$('#codeCaisse').focus();");

                                var audioElement = document.createElement("audio");
                                audioElement.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                                audioElement.setAttribute("autoplay:false", "autoplay");
                                audioElement.play();
                            });
                        </script>
                        <?php
                    }
                }
                else if ( isset($_POST['codeProduit']) && !empty($_POST['codeProduit']) )
                {
                    if ( ($retourProduit = $controleEC->scanProduit($_POST['codeProduit'])) !== true )
                    {
                        ?>
                        <script type="text/javascript">
                            $( document ).ready(function() {
                                $('#myModalErreur .modal-title').html('Erreur');
                                $('#myModalErreur .modal-body').html("<?php echo $retourProduit; ?>");
                                $('#myModalErreur').modal('show');
                                $('#myModalErreur .btnFermer').attr('onclick', "$('#codeProduit').focus();");

                                var audioElement2 = document.createElement("audio");
                                audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                                audioElement2.setAttribute("autoplay:false", "autoplay");
                                audioElement2.play();
                            });
                        </script>
                        <?php
                    }
                }
                $caisseEC = new Caisse($controleEC->id_caisse);

                $produitsEC = Commande::getProductsByOrder($controleEC->id_order);
                $totalEC = 0;
                foreach($produitsEC as $produitEC)
                {
                    $totalEC += $produitEC['quantity_final'];
                }

                echo '<div class="row topCmd">';
                echo '<div class="col-xs-6 col-md-6 align-left"><i class="fa-solid fa-globe"></i>#'.$controleEC->id_order.'</div>';
                echo '<div class="col-xs-6 col-md-6 align-right"><span id="compteurProduits">'.$controleEC->getNbControleEC().'</span>/'.$totalEC.'<i class="fa-solid fa-cart-shopping"></i></div>';
                echo '<div class="col-xs-6 col-md-6 align-left"><i class="fa-solid fa-location-dot"></i>'.$controleEC->zone.'</div>';
                echo '<div class="col-xs-6 col-md-6 align-right">'.$caisseEC->code.'<i class="fa-solid fa-box-archive"></i></div>';
                echo '</div>';

                if ( $controleEC->id_caisse == 0 )
                {
                    $tailleAPrendre = Caisse::getTailleCaisseByCommande($controleEC->id_order);
                    if ( $tailleAPrendre == 100 )
                    {
                        $valeurTaille = 'petite';
                        $couleurTaille = 'rouge';
                    }
                    elseif ( $tailleAPrendre == 200 )
                    {
                        $valeurTaille = 'moyenne';
                        $couleurTaille = 'verte';
                    }
                    elseif ( $tailleAPrendre == 400 )
                    {
                        $valeurTaille = 'grande';
                        $couleurTaille = 'noire';
                    }
                    echo '<div class="mireCaisse">';
                    echo '<div class="choixCaisse sur_'.$couleurTaille.'">Prenez une '.$valeurTaille.' caisse '.$couleurTaille.'</div>';
                    echo '<form class="form-caisse" method="POST">';
                    echo '<input type="text" id="codeCaisse" onblur="focus();" name="codeCaisse" class="form-control" placeholder="Scannez la caisse" required autofocus>';
                    //echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>';
                    echo '</form>';
                    echo '</div>';
                }
                else
                {
                    $fin = 1;
                    foreach($produitsEC as $produitEC)
                    {
                        $quantite_scan = 0;
                        $cpEC = ControleProduit::check($controleEC->id, $produitEC['product_id'], $produitEC['product_attribute_id']);
                        if ( $cpEC !== false )
                        {
                            // il y a un contrôle de débuté ou de terminé sur ce produit
                            $quantite_scan = $cpEC['quantite_prepare'];
                        }
                        if ( $quantite_scan == $produitEC['quantity_final'] )
                        {
                            continue;
                        }
                        $fin = 0;
                        echo '<div class="mireProduit">';
                        echo '<div class="product_ref" lbg="'.$produitEC['product_ean13'].'">'.$produitEC['product_reference'].'</div>';
                        echo '<div class="product_name">'.$produitEC['product_name_1'].'</div>';
                        $class_decli = 'sur_rouge';
                        echo '<pre>';
                        print_r($produitEC);
                        echo '</pre>';
                        if ( $produitEC['default_on'] == 1 )
                        {
                            $class_decli = 'noir';
                        }
                        echo '<div class="product_name '.$class_decli.'">'.$produitEC['product_name_2'].'</div>';
                        $class_qt = 'noir';
                        if ( $produitEC['quantity_final'] > 1 )
                        {
                            $class_qt = 'sur_rouge';
                        }
                        echo '<div class="product_qt '.$class_qt.'">'.$quantite_scan.'/'.$produitEC['quantity_final'].'</div>';
                        echo '<form class="form-produit" method="POST">';
                        echo '<input type="text" id="codeProduit" onblur="focus();" name="codeProduit" class="form-control" placeholder="Scannez le produit" required autofocus>';
                        //echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>';
                        echo '</form>';
                        echo '</div>';
                        break;
                    }
                    if ( $fin == 1 )
                    {
                        $controleEC->validate();
                        ?>
                        <script type="text/javascript">
                            $( document ).ready(function() {
                                $('#myModalSucces .lien').attr('onclick', 'document.location.href=\'/LogiGraine/modules/commandes.php\'');
                                $('#myModalSucces .modal-title').html('Commande complète');
                                $('#myModalSucces .modal-body').html('Vous allez passer à la commande suivante');
                                $('#myModalSucces').modal('show');
                            });
                        </script>
                        <?php
                    }
                }
            }
            else 
            {
                echo '<h3 class="heading">Aucune commande à traiter</h3>';
            }
        ?>
    </div>
    <?php include('../footer.php'); ?>
  </body>
</html>