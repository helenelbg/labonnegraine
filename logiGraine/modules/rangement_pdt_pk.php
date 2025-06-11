<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(7, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Rangement pommes de terre picking';
    include('../top.php'); 
    include('../header.php'); 
?>
    <div class="container">
        <?php 
            if ( !isset($_POST['codeLecture']) || empty($_POST['codeLecture']) )
            {
        ?>
        <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/accueil.php'"></i> <?php echo $title; ?></h2>
        <form class="form-rangement" method="POST">
            <?php
                echo '<div class="mireLecture">';
                echo '<input type="text" id="codeLecture" name="codeLecture" class="form-control" placeholder="Scannez l\'emplacement" required autofocus>';
                echo '</div>';
                echo '</form>';
                }
                if ( isset($_POST['codeLecture']) && !empty($_POST['codeLecture']) )
                {
                    ?>
                    <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/modules/rangement_pdt_pk.php'"></i> <?php echo $title; ?></h2>
                    <?php
                    $emplacement = $_POST['codeLecture'];
                    echo '<h3>'.$emplacement.'</h3>';                   
                    echo '<input type="hidden" id="emplacement" value="'.$emplacement.'" />';
                    echo '<div class="mireEan">';                        
                        echo '<form name="addEan" id="addEan">';
                        echo '<input type="text" id="codeLectureEan" name="codeLectureEan" class="form-control" placeholder="Scannez le produit" required autofocus>';
                        echo '</form><br />';
                        echo '<div id="rangementEC"></div>';
                    echo '</div>';
                }
            ?>
    </div>
    <script type="text/javascript">
        $( document ).ready(function() {
            getStock($('#emplacement').val());
        });
        $("#addEan").submit(function(event) {
            event.preventDefault();
            
            $('#myModalQte').modal('show');
            $('#myModalQte .btnVal').attr('onclick', "stockerPdt('"+$('#emplacement').val()+"', '"+$('#codeLectureEan').val()+"');");    
            setTimeout(function(){ $('#myModalQte #qte').val(''); $('#myModalQte #qte').focus(); }, 500); 
        });
        function getStock(emplacement)
        {
            $.ajax({
                method: "POST",
                url: "/LogiGraine/ajax_rangement_pdt_pk_get.php?",
                data: {emplacement: emplacement},
                success :function(data) {  
                    //if (data != '')  
                    //{
                        $('#rangementEC').html(data);       
                        $( ".fa-trash" ).on( "click", function() {
                            $.ajax({
                                method: "POST",
                                url: "/LogiGraine/ajax_rangement_pdt_pk_remove.php?",
                                data: {id: $(this).attr('idR'), id_product_attribute: $(this).attr('idPA')},
                                success :function(data) {  
                                    getStock($('#emplacement').val());  
                                }
                            });
                        } );         
                    //} 
                }
            });
        }
        function stockerPdt(emplacement, ean)
        {
            $.ajax({
                method: "POST",
                url: "/LogiGraine/ajax_rangement_pdt_pk.php?",
                data: {code_ean: ean, emplacement: emplacement, qte: $('#qte').val()},
                success :function(data) {  
                    if (data != '')  
                    {
                        getStock(data);     
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