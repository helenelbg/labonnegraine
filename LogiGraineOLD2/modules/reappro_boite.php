<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(5, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    $title = 'Réappro boîte';
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
                echo '<input type="text" id="codeLecture" name="codeLecture" class="form-control" placeholder="Scannez la boîte" required autofocus>';
                echo '</div>';
                echo '</form>';
                }
                if ( isset($_POST['codeLecture']) && !empty($_POST['codeLecture']) )
                {         
                    $boiteEC = new Boite($_POST['codeLecture']);  
                    if ( isset($boiteEC->id) && !empty($boiteEC->id) )
                    {         
                        $boiteEC->moveToFacing();
                    }
                    header('Location: /LogiGraine/modules/reappro_boite.php?boite='.$_POST['codeLecture']);
                }
                if ( isset($_GET['boite']) && !empty($_GET['boite']) )
                { 
                    $boiteEC = new Boite($_GET['boite']);  
                    $prodEC = $boiteEC->getProduct();
                    
                    if ($prodEC !== false)
                    {
                        echo '<br />Boîte de <b>'.$prodEC.'</b> prête à mettre en facing';
                    }
                    else 
                    {
                        echo '<br />Boîte inconnue';
                    }
                }
            ?>
    </div>
    <?php include('../footer.php'); ?>
  </body>
</html>