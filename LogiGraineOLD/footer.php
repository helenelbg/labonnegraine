<footer class="footer">
    <div class="container">
        <?php 
            if (isset($_SESSION['nomPda']) && !empty($_SESSION['nomPda']) )
            {
                echo '<p class="text-muted left">'.$_SESSION['nomPda'].'</p>';
                if ( isset($liste_commandes) )
                {
                    $nb_cmd = count($liste_commandes);
                    $suffixe = ' commande restante';
                    if ( $nb_cmd > 1 )
                    {
                        $suffixe = ' commandes restantes';
                    }
                    echo '<p class="text-muted right">'.count($liste_commandes).$suffixe.'</p>';
                }
            }
        ?>
    </div>
</footer>
<div class="modal fade erreur" id="myModalErreur" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>This is a small modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btnFermer" data-dismiss="modal" style="padding: 20px;">Fermer</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade succes" id="myModalSucces" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>This is a small modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default lien" data-dismiss="modal" style="padding: 20px;">Valider</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade info" id="myModalInfo" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>This is a small modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="padding: 20px;">Fermer</button>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="/LogiGraine/dist/js/bootstrap.min.js"></script>