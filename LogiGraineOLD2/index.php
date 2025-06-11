<?php
    require 'application_top.php';
    $title = 'Connexion';
    include('top.php'); 
    include('header.php'); 
?>
    <div class="container">
      <form class="form-signin" method="POST">
        <h2 class="form-signin-heading">Connexion</h2>
        <input type="password" id="codeOperateur" name="codeOperateur" class="form-control" placeholder="Scannez votre badge" required autofocus>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>
      </form>
    </div>
    <?php include('footer.php'); ?>
  </body>
</html>