<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <div class="logo-header"><a href="/LogiGraine/accueil.php"><img src="/LogiGraine//logo.png" /></a></div>
            <?php            
                if ( isset($operateur) && !empty($operateur) )
                {
                    //echo '<div class="operateur_logout"><a href="?logoutLG"><i class="fa-solid fa-right-from-bracket"></i></a></div>';
                    echo '<div class="operateur_name">' . $operateur->prenom . ' ' . $operateur->nom . '</div>';
                }
            ?>
        </div>
    </div>
</nav>