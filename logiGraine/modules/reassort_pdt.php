<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(8, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Réassort pommes de terre';
    include('../top.php');
    include('../header.php');
?>
    <div class="container">
        <?php 
            $reassortEC = Produit::getReassortPdt();
        ?>
        <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/accueil.php'"></i> <?php echo $title . ' ('.count($reassortEC).')'; ?></h2>
        <?php

            if ( isset($_POST['codeLectureEan']) && !empty($_POST['codeLectureEan']) )
            {
                $prodScan = new Produit($_POST['codeLectureEan']);
                if ( $reassortEC[0]['id_product'] == $prodScan->id && $reassortEC[0]['id_product_attribute'] == $prodScan->id_declinaison )
                {
                    $req_u = 'UPDATE ps_LogiGraine_rangement_pdt_reassort SET prepare = prepare + 1 WHERE id_product = "'.$reassortEC[0]['id_product'].'" AND id_product_attribute = "'.$reassortEC[0]['id_product_attribute'].'" AND emplacement = "'.$reassortEC[0]['emplacement'].'";';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_u);
                    $reassortEC = Produit::getReassortPdt();
                }
                else 
                {
                    ?>
                    <script type="text/javascript">
                        $( document ).ready(function() {
                            $('#myModalErreur .modal-title').html('Erreur');
                            $('#myModalErreur .modal-body').html("Mauvais produit scanné");
                            $('#myModalErreur').modal('show');
                            $('#myModalErreur .btnFermer').attr('onclick', "$('#codeLectureEan').focus();");

                            var audioElement2 = document.createElement("audio");
                            audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                            audioElement2.setAttribute("autoplay:false", "autoplay");
                            audioElement2.play();
                        });
                    </script>
                    <?php
                }
            }

            if ( isset($_POST['codeLecturePk']) && !empty($_POST['codeLecturePk']) )
            {
                $req_pk = 'SELECT * FROM ps_LogiGraine_rangement_pdt_pk WHERE id_product = "'.$reassortEC[0]['id_product'].'" AND id_product_attribute = "'.$reassortEC[0]['id_product_attribute'].'";';
                $resu_pk = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_pk);

                if ( $_POST['codeLecturePk'] == $resu_pk[0]['emplacement'] )
                {
                    $req_u = 'UPDATE ps_LogiGraine_rangement_pdt_reassort SET termine = 1, id_operateur = "'.$operateur->id.'" WHERE id_product = "'.$reassortEC[0]['id_product'].'" AND id_product_attribute = "'.$reassortEC[0]['id_product_attribute'].'" AND emplacement = "'.$reassortEC[0]['emplacement'].'";';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_u);

                    $req_u2 = 'UPDATE ps_LogiGraine_rangement_pdt_pk SET quantity = quantity + '.$reassortEC[0]['quantity'].' WHERE id_product = "'.$reassortEC[0]['id_product'].'" AND id_product_attribute = "'.$reassortEC[0]['id_product_attribute'].'";';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_u2);

                    $req_ds = 'SELECT * FROM ps_LogiGraine_rangement_pdt WHERE id_product = "'.$reassortEC[0]['id_product'].'" AND id_product_attribute = "'.$reassortEC[0]['id_product_attribute'].'" AND emplacement LIKE "'.$reassortEC[0]['emplacement'].'%";';
                    $auxQt = $reassortEC[0]['quantity'];
                    foreach ( Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req_ds) as $rangee_ds )
                    {
                        if ( $rangee_ds['quantity'] >= $auxQt )
                        {
                            $req_u3 = 'UPDATE ps_LogiGraine_rangement_pdt SET quantity = quantity - '.$auxQt.' WHERE id_rangement_pdt = "'.$rangee_ds['id_rangement_pdt'].'";';
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_u3);
                            $auxQt = 0;
                        }
                        else 
                        {
                            $auxQt = $auxQt - $rangee_ds['quantity'];
                            $req_u3 = 'UPDATE ps_LogiGraine_rangement_pdt SET quantity = 0 WHERE id_rangement_pdt = "'.$rangee_ds['id_rangement_pdt'].'";';
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_u3);
                        }
                    }
                    $reassortEC = Produit::getReassortPdt();

                    ?>
                        <script type="text/javascript">
                            $( document ).ready(function() {
                                $('#myModalSucces .lien').attr('onclick', 'document.location.href=\'/LogiGraine/modules/reassort_pdt.php\'');
                                $('#myModalSucces .modal-title').html('Réassort ok');
                                $('#myModalSucces .modal-body').html('Vous allez passer à la suite');
                                $('#myModalSucces').modal('show'); 
                            });
                        </script>
                    <?php
                }
                else 
                {
                    ?>
                    <script type="text/javascript">
                        $( document ).ready(function() {
                            $('#myModalErreur .modal-title').html('Erreur');
                            $('#myModalErreur .modal-body').html("Mauvais emplacement picking scanné");
                            $('#myModalErreur').modal('show');
                            $('#myModalErreur .btnFermer').attr('onclick', "$('#codeLecturePk').focus();");

                            var audioElement2 = document.createElement("audio");
                            audioElement2.setAttribute("src", "https://www.labonnegraine.com/modules/controlecommande/erreur5.mp3");
                            audioElement2.setAttribute("autoplay:false", "autoplay");
                            audioElement2.play();
                        });
                    </script>
                    <?php
                }
            }            

            if ( isset($reassortEC[0]['id_product']) && $reassortEC[0]['prepare'] < $reassortEC[0]['quantity'] )
            {
                echo '<h3>Palette '.str_replace('P', '', $reassortEC[0]['emplacement']).'</h3>';
                $prodEC = new Produit(0, $reassortEC[0]['id_product'], $reassortEC[0]['id_product_attribute']);
                
                echo '<h4>'.$prodEC->reference.'</h4>';
                echo '<h4>'.$prodEC->nom.'</h4>';
                echo '<h4>'.$prodEC->declinaison.'</h4>';

                echo '<h3>'.$reassortEC[0]['prepare'].'/'.$reassortEC[0]['quantity'].'</h3>';

                echo '<div class="mireEan">';
                    echo '<form name="addEan" id="addEan" method="POST">';
                    echo '<input type="text" id="codeLectureEan" name="codeLectureEan" class="form-control" placeholder="Scannez le produit" required autofocus>';
                    echo '</form><br />';
                echo '</div>';
            }
            elseif ( isset($reassortEC[0]['id_product']) && $reassortEC[0]['prepare'] == $reassortEC[0]['quantity'] )
            {
                echo '<h3>Palette '.str_replace('P', '', $reassortEC[0]['emplacement']).'</h3>';
                $prodEC = new Produit(0, $reassortEC[0]['id_product'], $reassortEC[0]['id_product_attribute']);
                
                echo '<h4>'.$prodEC->reference.'</h4>';
                echo '<h4>'.$prodEC->nom.'</h4>';
                echo '<h4>'.$prodEC->declinaison.'</h4>';

                echo '<h3>'.$reassortEC[0]['prepare'].'/'.$reassortEC[0]['quantity'].'</h3>';

                echo '<div class="mirePk">';
                    echo '<form name="addPk" id="addPk" method="POST">';
                    echo '<input type="text" id="codeLecturePk" name="codeLecturePk" class="form-control" placeholder="Scannez l\'emplacement picking" required autofocus>';
                    echo '</form><br />';
                echo '</div>';
            }
            else 
            {
                echo '<h4>Pas de mouvement à effectuer</h4>';
            }
        ?>
    </div>
    <script type="text/javascript">
        $( document ).ready(function() {
            $( ".fa-trash" ).on( "click", function() {
                $.ajax({
                    method: "POST",
                    url: "/LogiGraine/ajax_rangement_pdt_remove.php?",
                    data: {id: $(this).attr('idR'), id_product_attribute: $(this).attr('idPA')},
                    success :function(data) {
                        $('.ligne'+data).remove();
                    },
                    error: function(xhr){
                        alert("Erreur");
                    }
                });
            } );
        });        
    </script>
    <div class="modal fade qte" id="myModalQte" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Quantité stockée</h4>
                </div>
                <div class="modal-body">
                    <input type="number" id="qte" name="qte" value="" required autofocus />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btnVal" data-dismiss="modal" style="padding: 20px;">Valider</button>
                </div>
            </div>
        </div>
    </div>
    <?php include('../footer.php'); ?>
  </body>
</html>