<?php
    require 'application_top.php';
    $title = 'Admin Connexion';
    include('top.php'); 
    include('header.php'); 
?>
    <div class="container">
      <form class="form-signin" method="POST">
        <h2 class="form-signin-heading">Admin Connexion</h2>
        <input type="text" id="emailAdmin" name="emailAdmin" class="form-control" placeholder="Email" required autofocus><br />
        <input type="password" id="pswAdmin" name="pswAdmin" class="form-control" placeholder="Mot de passe" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Valider</button>
      </form>
    </div>
    <?php include('footer.php'); ?>
  </body>
</html>