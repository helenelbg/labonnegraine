<footer class="footer">
    <div class="container">
        <?php 
            if (isset($_SESSION['nomPda']) && !empty($_SESSION['nomPda']) )
            {
                echo '<p class="text-muted left">'.$_SESSION['nomPda'].'</p>';
                if ( isset($statsAAtraiter) )
                {
                    $explodeStats = explode('_', $statsAAtraiter);
                    $nb_session = $explodeStats[0];
                    $nb_cmd = $explodeStats[1];
                    $suffixe_session = ' session';
                    if ( $nb_session > 1 )
                    {
                        $suffixe_session = ' sessions';
                    }
                    $suffixe_cmd = ' commande';
                    if ( $nb_cmd > 1 )
                    {
                        $suffixe_cmd = ' commandes';
                    }
                    echo '<p class="text-muted right">'.$nb_session.$suffixe_session.' / '.$nb_cmd.$suffixe_cmd.'</p>';
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
<div class="modal fade succes" id="myModalSucces" role="dialog" data-keyboard="false" data-backdrop="static">
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