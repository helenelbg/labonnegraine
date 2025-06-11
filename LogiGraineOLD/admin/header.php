<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header" style="width: 100%;">
            <div class="logo-header"><a href="/LogiGraine/admin/accueil.php"><img src="/LogiGraine/logo.png" /></a></div>
            <?php            
                if ( isset($admin) && !empty($admin) )
                {
                    echo '<div class="admin_logout"><a href="?logoutLG"><i class="fa-solid fa-right-from-bracket"></i></a></div>';
                    echo '<div class="admin_name">' . $admin->prenom . ' ' . $admin->nom . '</div>';
                }
            ?>
        </div>
    </div>
</nav>