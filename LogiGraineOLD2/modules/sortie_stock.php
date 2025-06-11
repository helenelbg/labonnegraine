<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(9, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Sortie de stock';
    include('../top.php');
    include('../header.php');
?>
    <div class="container">
        <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/accueil.php'"></i> <?php echo $title; ?></h2>
        <?php
            /*if ( isset($_POST['codeLectureEan']) && !empty($_POST['codeLectureEan']) )
            {
                $prodScan = new Produit($_POST['codeLectureEan']);
                if ( isset($prodScan->id) && !empty($prodScan->id) && isset($prodScan->id_declinaison) && !empty($prodScan->id_declinaison) )
                {
                    //$req_u = 'UPDATE ps_LogiGraine_rangement_pdt_reassort SET prepare = prepare + 1 WHERE id_product = "'.$reassortEC[0]['id_product'].'" AND id_product_attribute = "'.$reassortEC[0]['id_product_attribute'].'" AND emplacement = "'.$reassortEC[0]['emplacement'].'";';
                    //Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_u);
                    //$reassortEC = Produit::getReassortPdt();

                    // MAJ DU STOCK
                }
                else 
                {
                    ?>
                    <script type="text/javascript">
                        $( document ).ready(function() {
                            $('#myModalErreur .modal-title').html('Erreur');
                            $('#myModalErreur .modal-body').html("Produit scanné inconnu");
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
            }*/          
            
            echo '<div class="mireEan">';
                echo '<form name="addEan" id="addEan" method="POST">';
                echo '<input type="text" id="codeLectureEan" name="codeLectureEan" class="form-control" placeholder="Scannez le produit" required autofocus>';
                echo '</form><br />';
            echo '</div>';

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

            echo '<select class="js-product-content">
                    <option value="0"> -- </option>
                    '.$product_list_str.'
                  </select>';
        ?>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".js-product-content").select2({dropdownAutoWidth : false, width: '100%'});
        });
        $("#addEan").submit(function(event) {
            event.preventDefault();
            
            $('#myModalQte').modal('show');
            $('#myModalQte .btnVal').attr('onclick', "sortieStock('"+$('#codeLectureEan').val()+"');");    
            setTimeout(function(){ $('#myModalQte #qte').val(''); $('#myModalQte #qte').focus(); }, 500); 
        });
        function sortieStock(ean)
        {
            $.ajax({
                method: "POST",
                url: "/LogiGraine/ajax_sortie_stock.php?",
                data: {code_ean: ean, qte: $('#qte').val()},
                success :function(data) {  
                    if (data != '')  
                    {
                        var explo = data.split('#');
                        $('#myModalSucces .lien').attr('onclick', 'document.location.href=\'/LogiGraine/modules/sortie_stock.php\'');
                        $('#myModalSucces .modal-title').html('Sortie de stock validée');
                        $('#myModalSucces .modal-body').html(explo[0] + ' x ' + explo[1]);
                        $('#myModalSucces').modal('show'); 
                    }
                    else 
                    {
                        alert('Erreur');
                    }
                    $('#codeLectureEan').val('');  
                    $('#codeLectureEan').focus();  
                },
                error: function(xhr){
                    alert("Erreur");
                    $('#codeLectureEan').val('');  
                    $('#codeLectureEan').focus(); 
                }
            });
        }
    </script>
    <div class="modal fade qte" id="myModalQte" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Quantité à sortir</h4>
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
    <link href="../select2/dist/css/select2.min.css" rel="stylesheet" />
    <script src="../select2/dist/js/select2.min.js"></script>
  </body>
</html>