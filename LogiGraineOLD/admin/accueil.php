<?php
    require 'application_top.php';
    $title = 'Admin Tableau de bord';
    include('top.php'); 
    include('header.php'); 
?>
    <div class="container">
      <h2 class="heading align-center">Admin Tableau de bord</h2>
      <div class="row">
        <?php
            foreach(LBGModuleAdmin::getModules() as $LOGI_module)
            {
                echo '<a href="modules/'.$LOGI_module->script.'"><div class="col-xs-6 col-md-6 module">';
                echo '<div class="module_picto"><i class="fa-solid fa-'.$LOGI_module->picto.'"></i></div>';
                echo '<div class="module_nom">'.$LOGI_module->nom.'</div>';
                echo '</div></a>';
            }
        ?>
      </div>
    </div>
    <?php include('footer.php'); ?>
  </body>
</html>