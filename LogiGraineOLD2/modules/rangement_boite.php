<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(4, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Rangement boîte';
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
                    <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/modules/rangement_boite.php'"></i> <?php echo $title; ?></h2>
                    <?php
                    $emplacement = $_POST['codeLecture'];
                    if ( substr($emplacement, 3, 1) == 'H' )
                    {
                        $hauteur = ' HAUT';
                    }
                    elseif ( substr($emplacement, 3, 1) == 'B' )
                    {
                        $hauteur = ' BAS';
                    }
                    $emplacement_affiche = substr($emplacement, 0, 1).'-'.substr($emplacement, 1, 2).$hauteur;
                    echo '<h3>'.$emplacement_affiche.'</h3>';                   
                    echo '<input type="hidden" id="emplacement" value="'.$emplacement.'" />';
                    echo '<div class="mireBoite">';                        
                        echo '<form name="addBoite" id="addBoite">';
                        echo '<input type="text" id="codeLectureBoite" name="codeLectureBoite" class="form-control" placeholder="Scannez la boîte" required autofocus>';
                        echo '</form>';
                        echo '<div id="rangementEC"></div>';
                        //echo '<button class="btn btn-lg btn-primary btn-block" value="Valider" onclick="">Valider</button>';
                    echo '</div>';
                }
            ?>
    </div>
    <script type="text/javascript">
        $("#addBoite").submit(function(event) {
            event.preventDefault();
            $.ajax({
                method: "POST",
                url: "/LogiGraine/ajax_rangement_boite.php?",
                data: {code_boite: $('#codeLectureBoite').val(), emplacement: $('#emplacement').val()},
                success :function(data) {  
                    if (data != '')  
                    {
                        $('#rangementEC').append(data+'<br />');                
                    }
                    else 
                    {
                        alert('Erreur');
                    }
                    $('#codeLectureBoite').val('');  
                    $('#codeLectureBoite').focus();  
                },
                error: function(xhr){
                    alert("Erreur, boîte déjà dans un emplacement");
                    $('#codeLectureBoite').val('');  
                    $('#codeLectureBoite').focus(); 
                }
            });
        });
    </script>
    <?php include('../footer.php'); ?>
  </body>
</html>