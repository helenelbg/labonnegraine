<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(1, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Lecture';
    include('../top.php'); 
    include('../header.php'); 
?>
    <div class="container">
        <form class="form-lecture" method="POST">
            <div class="form-check">
                <input type="radio" class="form-check-input" id="radio1" name="typeLecture" value="box" checked>Caisse
                <label class="form-check-label" for="radio1"></label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input" id="radio2" name="typeLecture" value="order">Commande
                <label class="form-check-label" for="radio2"></label>
            </div>
            <div class="form-check">
                <input type="radio" class="form-check-input" id="radio3" name="typeLecture" value="product">Produit
                <label class="form-check-label" for="radio3"></label>
            </div>
            <?php
                echo '<div class="mireLecture">';
                echo '';
                echo '<input type="text" id="codeLecture" name="codeLecture" class="form-control" placeholder="Scannez le code-barre" required autofocus>';
                echo '<button class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>';
                echo '</div>';
                echo '</form>';

                if ( isset($_POST['typeLecture']) && !empty($_POST['typeLecture']) )
                {
                    if ( $_POST['typeLecture'] == 'box' )
                    {
                        $commandeAssociee = Caisse::getCommandeByCode($_POST['codeLecture']);
                        if ( $commandeAssociee === false )
                        {
                            ?>
                            <script type="text/javascript">
                                $( document ).ready(function() {
                                    $('#myModalErreur .modal-title').html('Erreur');
                                    $('#myModalErreur .modal-body').html("Aucune commande associée à cette caisse");
                                    $('#myModalErreur').modal('show');

                                    var audioElement = document.createElement("audio");
                                    audioElement.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                                    audioElement.setAttribute("autoplay:false", "autoplay");
                                    audioElement.play();
                                });
                            </script>
                            <?php
                        }
                        else 
                        {
                            $produits = Commande::getProductsByOrder($commandeAssociee);
                            print_r($produits);
                        }
                    }
                    elseif ( $_POST['typeLecture'] == 'order' )
                    {
                        $produits = Commande::getProductsByOrder($_POST['codeLecture']);
                        if ( count($produits) == 0 || $produits === false )
                        {
                            ?>
                            <script type="text/javascript">
                                $( document ).ready(function() {
                                    $('#myModalErreur .modal-title').html('Erreur');
                                    $('#myModalErreur .modal-body').html("Commande introuvable");
                                    $('#myModalErreur').modal('show');

                                    var audioElement2 = document.createElement("audio");
                                    audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                                    audioElement2.setAttribute("autoplay:false", "autoplay");
                                    audioElement2.play();
                                });
                            </script>
                            <?php
                        }
                        else 
                        {
                            print_r($produits);
                        }
                    }
                }
            ?>
    </div>
    <?php include('../footer.php'); ?>
  </body>
</html>