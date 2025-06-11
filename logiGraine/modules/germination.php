<?php
    require '../application_top.php';

    /** TEST SI MODULE AUTORISE POUR OPERATEUR **/
    $testAutorisation = LBGModule::testModuleByOperateur(12, $operateur->id);
    if ( !isset($testAutorisation[0]->id) || empty($testAutorisation[0]->id) )
    {
        header('Location: /LogiGraine/accueil.php');
    }

    if ( isset($_GET['cat']) )
    {
        $_SESSION['catEC'] = $_GET['cat'];
    }
    elseif ( isset($_SESSION['catEC']) )
    {
        $_GET['cat'] = $_SESSION['catEC'];
    }
    if ( isset($_GET['search']) )
    {
        $_SESSION['searchEC'] = $_GET['search'];
    }
    elseif ( isset($_SESSION['searchEC']) )
    {
        $_GET['search'] = $_SESSION['searchEC'];
    }

    $title = 'Germination';
    include('../top.php');
    include('../header.php');

    // Gestion du scan de code-barres
    if (isset($_POST['codeScan']) && !empty($_POST['codeScan'])) {
        $code_scanne = $_POST['codeScan'];
        
        // Rechercher le test par code-barres
        $sql_scan = 'SELECT tl.*, il.numero_lot_LBG, pl.name, p.reference, il.id_product
                     FROM AW_test_lots tl 
                     LEFT JOIN ps_inventaire_lots il ON (tl.id_lot = il.id_inventaire_lots) 
                     LEFT JOIN ps_product_lang pl ON (il.id_product = pl.id_product AND pl.id_lang = 1) 
                     LEFT JOIN ps_product p ON (pl.id_product = p.id_product) 
                     WHERE tl.code_barre = "'.$code_scanne.'" AND tl.origine_test = "LBG" AND tl.date_fin_test = "0000-00-00"';
        
        $test_trouve = Db::getInstance()->executeS($sql_scan);
        
        if (!empty($test_trouve)) {
            $test_data = $test_trouve[0];
            // Stocker les données en session pour la modal
            $_SESSION['test_scan_data'] = $test_data;
            $show_modal_scan = true;
        } else {
            $error_scan = "Aucun test en cours trouvé pour ce code-barres";
        }
    }

    // Gestion des actions POST
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'nouveau_test':
                if (!empty($_POST['id_lot'])) {
                    $code_barre = !empty($_POST['code_barre']) ? $_POST['code_barre'] : null;
                    
                    // Vérifier si le code-barres est déjà utilisé par un test en cours
                    if ($code_barre) {
                        $check_code = 'SELECT id FROM AW_test_lots 
                                      WHERE code_barre = "'.$code_barre.'" 
                                      AND origine_test = "LBG" 
                                      AND date_fin_test = "0000-00-00"';
                        $existing = Db::getInstance()->executeS($check_code);
                        
                        if (!empty($existing)) {
                            header('Location: /LogiGraine/modules/germination.php?tab=encours&error=code_utilise');
                            exit;
                        }
                    }
                    
                    $req_insert = 'INSERT INTO AW_test_lots (id_lot, nom, date_debut_semis, origine_test, pourcentage_germ, code_barre) 
                                   VALUES ("'.$_POST['id_lot'].'", "Test LBG", NOW(), "LBG", 0, '.($code_barre ? '"'.$code_barre.'"' : 'NULL').')';
                    Db::getInstance()->execute($req_insert);
                    header('Location: /LogiGraine/modules/germination.php?tab=encours');
                }
                break;
            
            case 'pas_de_test':
                if (!empty($_POST['id_product']) && !empty($_POST['lot_germination'])) {
                    // Marquer comme "pas de test nécessaire" dans la table germination
                    $req_update = 'UPDATE germination SET date_germination = NOW(), germination = -1 
                                   WHERE id_product = "'.$_POST['id_product'].'" AND lot_germination = "'.$_POST['lot_germination'].'"';
                    Db::getInstance()->execute($req_update);
                    header('Location: /LogiGraine/modules/germination.php');
                }
                break;
                
            case 'lancer_test':
                if (!empty($_POST['id_product']) && !empty($_POST['lot_germination'])) {
                    // Récupérer l'ID du lot correspondant
                    $req_lot = 'SELECT id_inventaire_lots, graine_gramme FROM ps_inventaire_lots 
                               WHERE id_product = "'.$_POST['id_product'].'" AND numero_lot_LBG = "'.$_POST['lot_germination'].'"';
                    $result_lot = Db::getInstance()->executeS($req_lot);
                    
                    if (!empty($result_lot)) {
                        $id_lot = $result_lot[0]['id_inventaire_lots'];
                        $code_barre = !empty($_POST['code_barre_lot']) ? $_POST['code_barre_lot'] : null;
                        
                        // Vérifier si le code-barres est déjà utilisé par un test en cours
                        if ($code_barre) {
                            $check_code = 'SELECT id FROM AW_test_lots 
                                          WHERE code_barre = "'.$code_barre.'" 
                                          AND origine_test = "LBG" 
                                          AND date_fin_test = "0000-00-00"';
                            $existing = Db::getInstance()->executeS($check_code);
                            
                            if (!empty($existing)) {
                                header('Location: /LogiGraine/modules/germination.php?error=code_utilise');
                                exit;
                            }
                        }
                        
                        $req_insert = 'INSERT INTO AW_test_lots (id_lot, nom, date_debut_semis, origine_test, pourcentage_germ, code_barre) 
                                       VALUES ("'.$id_lot.'", "Test LBG", NOW(), "LBG", 0, '.($code_barre ? '"'.$code_barre.'"' : 'NULL').')';
                        Db::getInstance()->execute($req_insert);

                        $nb_deduire = 50;
                        if ( $result_lot[0]['graine_gramme'] == 'gramme' )
                        {
                            $req_st = 'SELECT gg.value FROM ps_inventaire_lots il LEFT JOIN ps_feature_product fp ON il.id_product = fp.id_product LEFT JOIN ps_feature_value_lang fvl ON fp.id_feature_value = fvl.id_feature_value LEFT JOIN convert_gg gg ON fvl.value = gg.text WHERE il.id_inventaire_lots = "'.$id_lot.'" AND fp.id_feature = 17;';
                            $resu_st = Db::getInstance()->executeS($req_st);

                            $nb_deduire = ceil(50/$resu_st[0]['value']);
                        }
                        
                        $req_tampon = 'SELECT s.valeur, il.id_product, s.id FROM ps_inventaire_lots il LEFT JOIN ps_inventaire s ON il.id_product = s.id_product
                                          WHERE s.id_product_attribute = 0 AND il.id_inventaire_lots = "'.$id_lot.'" ORDER BY date DESC LIMIT 0,1;';
                        $resu_tampon = Db::getInstance()->executeS($req_tampon);

                        if ( $resu_tampon[0]['valeur'] > 0 )
                        {
                            $req_maj_tampon = 'UPDATE ps_inventaire SET valeur = valeur - '.$nb_deduire.' WHERE id = "'.$resu_tampon[0]['id'].'";';
                            Db::getInstance()->execute($req_maj_tampon);
                        }
                        //error_log('STOCK TAMPON ('.$id_lot.') : '.$resu_st[0]['valeur']);

                        header('Location: /LogiGraine/modules/germination.php?tab=encours');
                    }
                }
                break;
        }
    }

    // Gestion des onglets
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'sans';
?>

<div class="container">
    <h2><i class="fa-solid fa-arrow-left" aria-hidden="true" onclick="document.location.href='/LogiGraine/accueil.php'"></i> <?php echo $title; ?></h2>
    
<!-- Zone de filtrage par catégories -->
<div class="panel panel-default" style="margin-bottom: 20px;">
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                    <select class="form-control" id="filterCategorie" onchange="appliquerFiltreCategorie()">
                        <option value="000">Toutes les catégories</option>
                        <?php
                        // Récupérer les catégories principales de produits
                        $sql_categories = 'SELECT DISTINCT c.id_category, cl.name 
                                         FROM ps_category c
                                         INNER JOIN ps_category_lang cl ON c.id_category = cl.id_category
                                         INNER JOIN ps_category_product cp ON c.id_category = cp.id_category
                                         WHERE cl.id_lang = 1 
                                         AND c.active = 1 
                                         AND c.id_parent > 2
                                         AND c.id_category IN (
                                             SELECT DISTINCT g.id_categorie 
                                             FROM germination g
                                         )
                                         ORDER BY cl.name';
                        
                        $categories = Db::getInstance()->executeS($sql_categories);
                        
                        foreach ($categories as $cat) {
                            $selected = (isset($_GET['cat']) && $_GET['cat'] == $cat['id_category']) ? 'selected' : '';
                            echo '<option value="'.$cat['id_category'].'" '.$selected.'>'.$cat['name'].'</option>';
                        }
                        ?>
                    </select>
            </div>
            <div class="col-xs-12 col-sm-6">
                <div class="form-group">
                    <input type="text" class="form-control" id="filterRecherche" 
                           placeholder="Rechercher par référence ou nom" 
                           value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>"
                           onkeyup="appliquerFiltreRecherche()">
                </div>
                    </div>
        </div>
        <?php /*<div class="row">
            <div class="col-xs-12">
                <button class="btn btn-default btn-sm" onclick="resetFiltres()">
                    <i class="fa-solid fa-times"></i> Réinitialiser les filtres
                </button>
                <span class="label label-info pull-right" id="compteurResultats" style="margin-top: 5px;">
                    <!-- Le compteur sera mis à jour par JS -->
                </span>
            </div>
        </div>*/ ?>

<?php
// Modification des requêtes SQL pour inclure le filtre par catégorie
$where_categorie = '';
if (isset($_GET['cat']) && !empty($_GET['cat']) && ($_GET['cat'] != "000")) {
    $where_categorie = ' AND g.id_categorie = "'.(int)$_GET['cat'].'" ';
}

$where_recherche = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = pSQL($_GET['search']);
    $where_recherche = ' AND (g.reference LIKE "%'.$search.'%" OR g.nom LIKE "%'.$search.'%") ';
}

?>


    <!-- Zone de scan -->
            <form method="POST" class="form-scan">
                <div class="input-group">
                    <input type="text" name="codeScan" id="codeScan" class="form-control input-lg" 
                           placeholder="Scannez le code-barres du test" autofocus required>
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </span>
                </div>
            </form>
            <?php if (isset($error_scan)): ?>
                <div class="alert alert-warning" style="margin-top: 10px;">
                    <i class="fa-solid fa-exclamation-triangle"></i> <?php echo $error_scan; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error']) && $_GET['error'] == 'code_utilise'): ?>
                <div class="alert alert-danger" style="margin-top: 10px;">
                    <i class="fa-solid fa-exclamation-triangle"></i> Ce code-barres est déjà utilisé par un test en cours
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Navigation par onglets -->
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-xs-12">
            <div class="btn-group btn-group-justified" role="group">
                <a href="?tab=sans" class="btn btn-sm <?php echo $active_tab == 'sans' ? 'btn-primary' : 'btn-default'; ?>">
                    <i class="fa-solid fa-exclamation-circle"></i><br>
                    <span class="hidden-xs">Lots sans germination</span>
                    <span class="visible-xs-inline">Sans test</span>
                </a>
                <a href="?tab=arefaire" class="btn btn-sm <?php echo $active_tab == 'arefaire' ? 'btn-primary' : 'btn-default'; ?>">
                    <i class="fa-solid fa-clock"></i><br>
                    <span class="hidden-xs">Tests anciens</span>
                    <span class="visible-xs-inline">Anciens</span>
                </a>
                <a href="?tab=encours" class="btn btn-sm <?php echo $active_tab == 'encours' ? 'btn-primary' : 'btn-default'; ?>">
                    <i class="fa-solid fa-flask"></i><br>
                    <span class="hidden-xs">Tests en cours</span>
                    <span class="visible-xs-inline">En cours</span>
                </a>
            </div>
        </div>
    </div>

    <?php if ($active_tab == 'sans'): ?>
        <!-- Onglet: Lots sans germination -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Lots sans test de germination LBG</h3>
            </div>
            <div class="panel-body">
                <?php
                $sql = 'SELECT g.*, gn.officielle, gn.minimum, gn.optimum 
                FROM germination g 
                LEFT JOIN germination_normes gn ON (g.id_categorie = gn.id_categorie) 
                WHERE g.date_germination = "0000-00-00" 
                '.$where_categorie.'
                AND CONCAT(g.id_product,"-",g.lot_germination) NOT IN (
                    SELECT CONCAT(il.id_product,"-",il.numero_lot_LBG) 
                    FROM AW_test_lots tl 
                    LEFT JOIN ps_inventaire_lots il ON (tl.id_lot = il.id_inventaire_lots) 
                    WHERE tl.origine_test = "LBG" AND tl.date_fin_test = "0000-00-00"
                )
                ORDER BY g.priorite ASC, g.nom ASC';
                /*
                '.$where_categorie.'
                '.$where_recherche.'*/
        
        $produits_sans_test = Db::getInstance()->executeS($sql);
                
                if (empty($produits_sans_test)) {
                    echo '<div class="text-center"><p class="lead">Aucun lot sans test de germination</p></div>';
                } else {
                    // Affichage mobile-first avec cards
                    echo '<div class="hidden-xs">';
                    // Version desktop (tableau)
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped table-condensed">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Référence</th>';
                    echo '<th>Nom</th>';
                    echo '<th>Lot</th>';
                    echo '<th>Off.</th>';
                    echo '<th>Min</th>';
                    echo '<th>Opt</th>';
                    echo '<th>Actions</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    foreach ($produits_sans_test as $produit) {
                        echo '<tr>';
                        echo '<td><strong>'.$produit['reference'].'</strong></td>';
                        echo '<td>'.$produit['nom'].'</td>';
                        echo '<td><span class="label label-default">'.substr($produit['lot_germination'], -4, 4).'</span></td>';
                        echo '<td>'.$produit['officielle'].'%</td>';
                        echo '<td>'.$produit['minimum'].'%</td>';
                        echo '<td>'.$produit['optimum'].'%</td>';
                        echo '<td>';
                        echo '<div class="btn-group btn-group-xs">';
                        echo '<button class="btn btn-warning" onclick="pasDeTest('.$produit['id_product'].', \''.$produit['lot_germination'].'\')">Non</button>';
                        echo '<button class="btn btn-success" onclick="lancerTest('.$produit['id_product'].', \''.$produit['lot_germination'].'\')">Test</button>';
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table></div></div>';
                    
                    // Version mobile (cards)
                    echo '<div class="visible-xs-block">';
                    foreach ($produits_sans_test as $produit) {
                        echo '<div class="panel panel-default product-card" style="margin-bottom: 15px;">';
                        echo '<div class="panel-body">';
                        echo '<div class="row">';
                        echo '<div class="col-xs-8">';
                        echo '<h4 class="panel-title" style="margin-top: 0;">'.$produit['reference'].'</h4>';
                        echo '<p class="text-muted">'.substr($produit['nom'], 0, 400).'</p>';
                        echo '<span class="label label-info">Lot: '.($produit['lot_germination']).'</span> ';
                        echo '<span class="label label-default">'.$produit['minimum'].'%-'.$produit['optimum'].'%</span>';
                        echo '</div>';
                        echo '<div class="col-xs-4 text-right">';
                        echo '<div class="btn-group-vertical" style="width: 100%;">';
                        echo '<button class="btn btn-warning btn-sm" onclick="pasDeTest('.$produit['id_product'].', \''.$produit['lot_germination'].'\')" style="margin-bottom: 5px;">Pas de test</button>';
                        echo '<button class="btn btn-success btn-sm" onclick="lancerTest('.$produit['id_product'].', \''.$produit['lot_germination'].'\')">Lancer test</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>

    <?php elseif ($active_tab == 'arefaire'): ?>
        <!-- Onglet: Tests anciens à refaire -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Lots avec germination supérieure à 8 mois</h3>
            </div>
            <div class="panel-body">
                <?php
                $date_limite = date('Y-m-d', strtotime('-8 months'));
                
                $sql = 'SELECT g.*, gn.officielle, gn.minimum, gn.optimum 
            FROM germination g 
            LEFT JOIN germination_normes gn ON (g.id_categorie = gn.id_categorie) 
            WHERE g.date_germination <> "0000-00-00" 
            '.$where_categorie.'
            AND g.date_germination < "'.$date_limite.'" 
            AND g.id_product NOT IN (
                SELECT il.id_product 
                FROM AW_test_lots tl 
                LEFT JOIN ps_inventaire_lots il ON (tl.id_lot = il.id_inventaire_lots) 
                WHERE tl.origine_test = "LBG" AND tl.date_fin_test = "0000-00-00"
            )
            ORDER BY g.priorite ASC, g.nom ASC';
           /* '.$where_categorie.'
            '.$where_recherche.'*/
    
    $produits_anciens = Db::getInstance()->executeS($sql);
                
                if (empty($produits_anciens)) {
                    echo '<div class="text-center"><p class="lead">Aucun test ancien à refaire</p></div>';
                } else {
                    // Version desktop
                    echo '<div class="hidden-xs">';
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-striped table-condensed">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Référence</th>';
                    echo '<th>Nom</th>';
                    echo '<th>Lot</th>';
                    echo '<th>Off.</th>';
                    echo '<th>Min</th>';
                    echo '<th>Opt</th>';
                    echo '<th>Germination</th>';
                    echo '<th>Actions</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    
                    foreach ($produits_anciens as $produit) {
                        $median = ($produit['optimum'] + $produit['minimum']) / 2;
                        $couleur_germ = '#fff';
                        
                        if ($produit['germination'] >= $produit['optimum']) {
                            $couleur_germ = '#1aaf64'; // VERT
                        } elseif ($produit['germination'] >= $median) {
                            $couleur_germ = '#FF8000'; // ORANGE
                        } elseif ($produit['germination'] > 0) {
                            $couleur_germ = '#FE2E2E'; // ROUGE
                        }
                        
                        echo '<tr>';
                        echo '<td><strong>'.$produit['reference'].'</strong></td>';
                        echo '<td>'.substr($produit['nom'], 0, 400).'</td>';
                        echo '<td><span class="label label-default">'.substr($produit['lot_germination'], -4, 4).'</span></td>';
                        echo '<td>'.$produit['officielle'].'%</td>';
                        echo '<td>'.$produit['minimum'].'%</td>';
                        echo '<td>'.$produit['optimum'].'%</td>';
                        echo '<td><span class="label" style="background-color:'.$couleur_germ.'; color:#000;">'.$produit['germination'].'%</span></td>';
                        echo '<td>';
                        echo '<div class="btn-group btn-group-xs">';
                        echo '<button class="btn btn-warning" onclick="pasDeTest('.$produit['id_product'].', \''.$produit['lot_germination'].'\')">Non</button>';
                        echo '<button class="btn btn-success" onclick="lancerTest('.$produit['id_product'].', \''.$produit['lot_germination'].'\')">Test</button>';
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table></div></div>';
                    
                    // Version mobile (cards)
                    echo '<div class="visible-xs-block">';
                    foreach ($produits_anciens as $produit) {
                        $median = ($produit['optimum'] + $produit['minimum']) / 2;
                        $couleur_germ = '#f5f5f5';
                        $couleur_text = '#666';
                        
                        if ($produit['germination'] >= $produit['optimum']) {
                            $couleur_germ = '#1aaf64'; // VERT
                            $couleur_text = '#000';
                        } elseif ($produit['germination'] >= $median) {
                            $couleur_germ = '#FF8000'; // ORANGE  
                            $couleur_text = '#000';
                        } elseif ($produit['germination'] > 0) {
                            $couleur_germ = '#FE2E2E'; // ROUGE
                            $couleur_text = '#fff';
                        }
                        
                        echo '<div class="panel panel-default product-card" style="margin-bottom: 15px;">';
                        echo '<div class="panel-body">';
                        echo '<div class="row">';
                        echo '<div class="col-xs-8">';
                        echo '<h4 class="panel-title" style="margin-top: 0;">'.$produit['reference'].'</h4>';
                        echo '<p class="text-muted">'.substr($produit['nom'], 0, 400).'</p>';
                        echo '<div style="margin-bottom: 8px;">';
                        echo '<span class="label label-info">Lot: '.($produit['lot_germination']).'</span> ';
                        echo '<span class="label label-default">'.$produit['minimum'].'%-'.$produit['optimum'].'%</span>';
                        echo '</div>';
                        echo '<div class="well well-sm" style="margin-bottom: 0; padding: 5px; background-color: '.$couleur_germ.'; color: '.$couleur_text.';">';
                        echo '<strong>Germination actuelle: '.$produit['germination'].'%</strong>';
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="col-xs-4 text-right">';
                        echo '<div class="btn-group-vertical" style="width: 100%;">';
                        echo '<button class="btn btn-warning btn-sm" onclick="pasDeTest('.$produit['id_product'].', \''.$produit['lot_germination'].'\')" style="margin-bottom: 5px;">Pas de test</button>';
                        echo '<button class="btn btn-success btn-sm" onclick="lancerTest('.$produit['id_product'].', \''.$produit['lot_germination'].'\')">Lancer test</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>      
        <?php elseif ($active_tab == 'encours'): ?>
<!-- Onglet: Tests en cours -->
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Tests de germination en cours
            <button class="btn btn-sm btn-primary pull-right visible-xs" onclick="ajouterTest()">
                <i class="fa-solid fa-plus"></i>
            </button>
        </h3>
        <div class="pull-right hidden-xs">
            <button class="btn btn-sm btn-primary" onclick="ajouterTest()">
                <i class="fa-solid fa-plus"></i> Ajouter un test
            </button>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <?php
        $sql = 'SELECT tl.*, il.numero_lot_LBG, pl.name, p.reference, il.id_product 
        FROM AW_test_lots tl 
        LEFT JOIN ps_inventaire_lots il ON (tl.id_lot = il.id_inventaire_lots) 
        LEFT JOIN ps_product_lang pl ON (il.id_product = pl.id_product AND pl.id_lang = 1) 
        LEFT JOIN ps_product p ON (pl.id_product = p.id_product) ';

/*if (!empty($where_categorie) || !empty($where_recherche)) {
    $sql .= 'LEFT JOIN germination g ON (il.id_product = g.id_product AND il.numero_lot_LBG = g.lot_germination) ';
}*/

$sql .= 'WHERE tl.origine_test = "LBG" AND tl.date_fin_test = "0000-00-00" ';

if (!empty($where_categorie)) {
    //$sql .= str_replace('g.id_categorie', 'g.id_categorie', $where_categorie);
}
if (!empty($where_recherche)) {
    //$sql .= str_replace(['g.reference', 'g.nom'], ['p.reference', 'pl.name'], $where_recherche);
}

$sql .= 'ORDER BY tl.date_etape_1 ASC, tl.date_etape_2 ASC, tl.date_etape_3 ASC';
//error_log($sql );
$tests_encours = Db::getInstance()->executeS($sql);
        
        if (empty($tests_encours)) {
            echo '<div class="text-center"><p class="lead">Aucun test en cours</p></div>';
        } else {
            // Version desktop
            echo '<div class="hidden-xs">';
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-condensed">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Code</th>';
            echo '<th>Réf</th>';
            echo '<th>Nom</th>';
            echo '<th>Lot</th>';
            echo '<th>Début</th>';
            echo '<th>Ét.1</th>';
            echo '<th>Ét.2</th>';
            echo '<th>Ét.3</th>';
            echo '<th>Actions</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($tests_encours as $test) {
                $highlight_class = '';
                if (isset($_GET['focus']) && $_GET['focus'] == $test['id']) {
                    $highlight_class = 'class="warning"';
                }
                
                echo '<tr id="test_'.$test['id'].'" '.$highlight_class.'>';
                
                // Code-barres avec possibilité d'attribution
                echo '<td>';
                if (empty($test['code_barre'])) {
                    echo '<input type="text" class="form-control input-sm" id="code_'.$test['id'].'" placeholder="Code" style="width:80px;">';
                    echo '<button class="btn btn-xs btn-primary" onclick="attribuerCode('.$test['id'].')" style="margin-top:2px;">OK</button>';
                } else {
                    echo '<span class="label label-info" style="cursor: pointer;" onclick="ouvrirModalSaisie('.$test['id'].')">'.$test['code_barre'].'</span>';
                }
                echo '</td>';
                
                echo '<td><strong>'.$test['reference'].'</strong></td>';
                echo '<td>'.substr($test['name'], 0, 400).'</td>';
                echo '<td><span class="label label-default">'.($test['numero_lot_LBG']).'</span></td>';
                echo '<td>'.date('d/m', strtotime($test['date_debut_semis'])).'</td>';
                
                // Étapes - Remplacer les inputs par des boutons ou des labels
                echo '<td>';
                if ($test['date_etape_1'] == '0000-00-00') {
                    echo '<button class="btn btn-xs btn-warning" onclick="ouvrirModalSaisie('.$test['id'].')">Saisir</button>';
                } else {
                    echo '<span class="label label-success">'.$test['resultat_etape_1'].'%</span>';
                }
                echo '</td>';
                
                echo '<td>';
                if ($test['date_etape_1'] != '0000-00-00' && $test['date_etape_2'] == '0000-00-00') {
                    echo '<button class="btn btn-xs btn-warning" onclick="ouvrirModalSaisie('.$test['id'].')">Saisir</button>';
                } elseif ($test['date_etape_2'] != '0000-00-00') {
                    echo '<span class="label label-success">'.$test['resultat_etape_2'].'%</span>';
                } else {
                    echo '<span class="text-muted">-</span>';
                }
                echo '</td>';
                
                echo '<td>';
                if ($test['date_etape_2'] != '0000-00-00' && $test['date_etape_3'] == '0000-00-00') {
                    echo '<button class="btn btn-xs btn-warning" onclick="ouvrirModalSaisie('.$test['id'].')">Saisir</button>';
                } elseif ($test['date_etape_3'] != '0000-00-00') {
                    echo '<span class="label label-success">'.$test['resultat_etape_3'].'%</span>';
                } else {
                    echo '<span class="text-muted">-</span>';
                }
                echo '</td>';
                
                echo '<td>';
                echo '<div class="btn-group btn-group-xs">';
                echo '<button class="btn btn-danger" onclick="supprimerTest('.$test['id'].')"><i class="fa-solid fa-trash"></i></button>';
                if (!empty($test['code_barre'])) {
                    echo '<button class="btn btn-info" onclick="imprimerEtiquette('.$test['id'].')"><i class="fa-solid fa-print"></i></button>';
                }
                if ($test['date_etape_3'] != '0000-00-00') {
                    echo '<button class="btn btn-primary" onclick="terminerTest('.$test['id'].')">Fin</button>';
                }
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table></div></div>';
            
            // Version mobile (cards)
            echo '<div class="visible-xs-block">';
            foreach ($tests_encours as $test) {
                $highlight_class = '';
                if (isset($_GET['focus']) && $_GET['focus'] == $test['id']) {
                    $highlight_class = 'panel-warning';
                } else {
                    $highlight_class = 'panel-default';
                }
                
                echo '<div class="panel '.$highlight_class.' test-card" id="test_mobile_'.$test['id'].'" style="margin-bottom: 15px;">';
                echo '<div class="panel-body">';
                
                // En-tête du test
                echo '<div class="row" style="margin-bottom: 10px;">';
                echo '<div class="col-xs-8">';
                echo '<h4 style="margin-top: 0;"><strong>'.$test['reference'].'</strong></h4>';
                echo '<p class="text-muted" style="margin-bottom: 5px;">'.substr($test['name'], 0, 400).'</p>';
                echo '<span class="label label-default">Lot: '.($test['numero_lot_LBG']).'</span> ';
                echo '<span class="label label-info">'.date('d/m/Y', strtotime($test['date_debut_semis'])).'</span>';
                echo '</div>';
                echo '<div class="col-xs-4 text-right">';
                
                // Code-barres mobile
                if (empty($test['code_barre'])) {
                    echo '<input type="text" class="form-control input-sm" id="code_mobile_'.$test['id'].'" placeholder="Code-barres" style="margin-bottom: 5px;">';
                    echo '<button class="btn btn-primary btn-xs btn-block" onclick="attribuerCodeMobile('.$test['id'].')">Attribuer</button>';
                } else {
                    echo '<div class="well well-sm text-center" style="margin-bottom: 5px; padding: 5px; cursor: pointer;" onclick="ouvrirModalSaisie('.$test['id'].')">';
                    echo '<small>Code-barres</small><br>';
                    echo '<strong>'.$test['code_barre'].'</strong>';
                    echo '</div>';
                }
                echo '</div>';
                echo '</div>';
                
                // Étapes mobile - Remplacer les inputs par des boutons
                echo '<div class="row">';
                echo '<div class="col-xs-12">';
                echo '<h5>Étapes de germination:</h5>';
                
                // Étape 1
                echo '<div class="form-group">';
                echo '<label class="control-label">Étape 1:</label>';
                if ($test['date_etape_1'] == '0000-00-00') {
                    echo '<button class="btn btn-warning btn-block" onclick="ouvrirModalSaisie('.$test['id'].')">Saisir l\'étape 1</button>';
                } else {
                    echo '<p class="form-control-static">';
                    echo '<span class="label label-success">'.$test['resultat_etape_1'].'%</span> ';
                    echo '<small class="text-muted">('.date('d/m/Y', strtotime($test['date_etape_1'])).')</small>';
                    echo '</p>';
                }
                echo '</div>';
                
                // Étape 2
                if ($test['date_etape_1'] != '0000-00-00') {
                    echo '<div class="form-group">';
                    echo '<label class="control-label">Étape 2:</label>';
                    if ($test['date_etape_2'] == '0000-00-00') {
                        echo '<button class="btn btn-warning btn-block" onclick="ouvrirModalSaisie('.$test['id'].')">Saisir l\'étape 2</button>';
                    } else {
                        echo '<p class="form-control-static">';
                        echo '<span class="label label-success">'.$test['resultat_etape_2'].'%</span> ';
                        echo '<small class="text-muted">('.date('d/m/Y', strtotime($test['date_etape_2'])).')</small>';
                        echo '</p>';
                    }
                    echo '</div>';
                }
                
                // Étape 3
                if ($test['date_etape_2'] != '0000-00-00') {
                    echo '<div class="form-group">';
                    echo '<label class="control-label">Étape 3:</label>';
                    if ($test['date_etape_3'] == '0000-00-00') {
                        echo '<button class="btn btn-warning btn-block" onclick="ouvrirModalSaisie('.$test['id'].')">Saisir l\'étape 3</button>';
                    } else {
                        echo '<p class="form-control-static">';
                        echo '<span class="label label-success">'.$test['resultat_etape_3'].'%</span> ';
                        echo '<small class="text-muted">('.date('d/m/Y', strtotime($test['date_etape_3'])).')</small>';
                        echo '</p>';
                    }
                    echo '</div>';
                }
                
                // Actions mobile
                echo '<div class="btn-group btn-group-justified" style="margin-top: 15px;">';
                echo '<div class="btn-group">';
                echo '<button class="btn btn-danger btn-sm" onclick="supprimerTest('.$test['id'].')"><i class="fa-solid fa-trash"></i> Supprimer</button>';
                echo '</div>';
                /*if (!empty($test['code_barre'])) {
                    echo '<div class="btn-group">';
                    echo '<button class="btn btn-info btn-sm" onclick="imprimerEtiquette('.$test['id'].')"><i class="fa-solid fa-print"></i> Imprimer</button>';
                    echo '</div>';
                }*/
                if ($test['date_etape_3'] != '0000-00-00') {
                    echo '<div class="btn-group">';
                    echo '<button class="btn btn-primary btn-sm" onclick="terminerTest('.$test['id'].')"><i class="fa-solid fa-check"></i> Terminer</button>';
                    echo '</div>';
                }
                echo '</div>';
                
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
    </div>
</div>
</div>
</div>
<?php endif; ?>
</div>

<!-- Modal pour ajouter un test - Optimisé mobile -->
<div class="modal fade" id="modalAjoutTest" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Ajouter un test de germination</h4>
            </div>
            <div class="modal-body">
                <form id="formAjoutTest">
                    <div class="form-group">
                        <label>Produit :</label>
                        <input type="text" class="form-control input-lg" id="inputEan" placeholder="Scanner le code-barres du sachet">
                        <select class="form-control input-lg" id="selectProduit" onchange="chargerLots()">
                            <option value="">-- Sélectionner un produit --</option>
                            <?php
                            $sql_produits = 'SELECT p.id_product, p.reference, pl.name 
                            FROM ps_product p 
                            INNER JOIN ps_product_lang pl ON p.id_product = pl.id_product 
                            WHERE pl.id_lang = 1 AND p.active = 1 AND p.id_product IN (SELECT cp.id_product FROM ps_category_product cp WHERE cp.id_category IN (18,233,92))
                            ORDER BY p.reference';
                            $produits = Db::getInstance()->executeS($sql_produits);
                            
                            foreach ($produits as $produit) {
                                echo '<option value="'.$produit['id_product'].'">'.$produit['reference'].' - '.$produit['name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Lot :</label>
                        <select class="form-control input-lg" id="selectLot">
                            <option value="">-- Sélectionner d'abord un produit --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Code-barres (optionnel) :</label>
                        <input type="text" class="form-control input-lg" id="inputCodeBarre" placeholder="Scanner le code-barres à associer">
                        <small class="text-muted">Laissez vide pour créer le test sans code-barres</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary btn-lg" onclick="confirmerAjoutTest()">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de saisie rapide après scan -->
<?php if (isset($show_modal_scan) && $show_modal_scan && isset($_SESSION['test_scan_data'])): ?>
<div class="modal fade" id="modalScanRapide" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white">
                    <i class="fa-solid fa-seedling"></i> Test de Germination
                    <button type="button" class="close text-white" onclick="fermerModalScan()" style="float: right;">
                        <span>&times;</span>
                    </button>
                </h4>
            </div>
            <div class="modal-body">
                <?php 
                $test = $_SESSION['test_scan_data'];
                unset($_SESSION['test_scan_data']); // Nettoyer la session
                ?>
                
                <!-- Informations du test -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-xs-12">
                        <div class="panel panel-info">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6">
                                        <h4 style="margin-top: 0;">
                                            <strong><?php echo $test['reference']; ?></strong>
                                        </h4>
                                        <p><?php echo $test['name']; ?></p>
                                        <span class="label label-primary">Lot: <?php echo ($test['numero_lot_LBG']); ?></span>
                                        <span class="label label-info"><?php echo $test['code_barre']; ?></span>
                                    </div>
                                    <div class="col-xs-6 col-sm-6 text-right">
                                        <p><strong>Début:</strong> <?php echo date('d/m/Y', strtotime($test['date_debut_semis'])); ?></p>
                                        <p><strong>Statut:</strong> 
                                            <?php
                                            if ($test['date_etape_3'] != '0000-00-00') {
                                                echo '<span class="label label-warning">Prêt à finaliser</span>';
                                            } elseif ($test['date_etape_2'] != '0000-00-00') {
                                                echo '<span class="label label-info">Étape 3 en attente</span>';
                                            } elseif ($test['date_etape_1'] != '0000-00-00') {
                                                echo '<span class="label label-info">Étape 2 en attente</span>';
                                            } else {
                                                echo '<span class="label label-info">Étape 1 en attente</span>';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Saisie des étapes -->
                <div class="row">
                    <div class="col-xs-12">
                        <form id="formSaisieRapide">
                            <input type="hidden" id="test_id_scan" value="<?php echo $test['id']; ?>">
                            
                            <!-- Étape 1 -->
                            <div class="form-group">
                                <label class="control-label">
                                    <i class="fa-solid fa-seedling"></i> Étape 1
                                </label>
                                <?php if ($test['date_etape_1'] == '0000-00-00'): ?>
                                    <div class="input-group input-group-lg">
                                        <input type="number" class="form-control" id="etape1_scan" 
                                               placeholder="Nombre de graines germées" min="0" max="50" autofocus>
                                    </div>
                                    <div class="btn-group btn-group-justified" style="margin-top: 10px;">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success btn-lg" onclick="validerEtapeScan(1)">
                                                <i class="fa-solid fa-check"></i> Valider
                                            </button>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-lg" onclick="validerEtFinaliserScan(1)">
                                                <i class="fa-solid fa-check-double"></i> Valider et Finaliser
                                            </button>
                                        </div>
                                    </div>
                                    <small class="help-block">Saisissez le nombre de graines germées à l'étape 1</small>
                                <?php else: ?>
                                    <div class="well">
                                        <i class="fa-solid fa-check-circle text-success"></i>
                                        <strong><?php echo $test['resultat_etape_1']; ?>%</strong> 
                                        <small class="text-muted">(<?php echo date('d/m/Y', strtotime($test['date_etape_1'])); ?>)</small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Étape 2 -->
                            <?php if ($test['date_etape_1'] != '0000-00-00'): ?>
                            <div class="form-group">
                                <label class="control-label">
                                    <i class="fa-solid fa-seedling"></i> Étape 2
                                </label>
                                <?php if ($test['date_etape_2'] == '0000-00-00'): ?>
                                    <div class="input-group input-group-lg">
                                        <input type="number" class="form-control" id="etape2_scan" 
                                               placeholder="Nombre de graines germées" min="0" max="50">
                                    </div>
                                    <div class="btn-group btn-group-justified" style="margin-top: 10px;">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success btn-lg" onclick="validerEtapeScan(2)">
                                                <i class="fa-solid fa-check"></i> Valider
                                            </button>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-lg" onclick="validerEtFinaliserScan(2)">
                                                <i class="fa-solid fa-check-double"></i> Valider et Finaliser
                                            </button>
                                        </div>
                                    </div>
                                    <small class="help-block">Saisissez le nombre de graines germées à l'étape 2</small>
                                <?php else: ?>
                                    <div class="well">
                                        <i class="fa-solid fa-check-circle text-success"></i>
                                        <strong><?php echo $test['resultat_etape_2']; ?>%</strong> 
                                        <small class="text-muted">(<?php echo date('d/m/Y', strtotime($test['date_etape_2'])); ?>)</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Étape 3 -->
                            <?php if ($test['date_etape_2'] != '0000-00-00'): ?>
                            <div class="form-group">
                                <label class="control-label">
                                    <i class="fa-solid fa-seedling"></i> Étape 3
                                </label>
                                <?php if ($test['date_etape_3'] == '0000-00-00'): ?>
                                    <div class="input-group input-group-lg">
                                        <input type="number" class="form-control" id="etape3_scan" 
                                               placeholder="Nombre de graines germées" min="0" max="50">
                                    </div>
                                    <div class="btn-group btn-group-justified" style="margin-top: 10px;">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-success btn-lg" onclick="validerEtapeScan(3)">
                                                <i class="fa-solid fa-check"></i> Valider
                                            </button>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary btn-lg" onclick="validerEtFinaliserScan(3)">
                                                <i class="fa-solid fa-check-double"></i> Valider et Finaliser
                                            </button>
                                        </div>
                                    </div>
                                    <small class="help-block">Saisissez le nombre final de graines germées</small>
                                <?php else: ?>
                                    <div class="well">
                                        <i class="fa-solid fa-check-circle text-success"></i>
                                        <strong><?php echo $test['resultat_etape_3']; ?>%</strong> 
                                        <small class="text-muted">(<?php echo date('d/m/Y', strtotime($test['date_etape_3'])); ?>)</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Zone de finalisation si toutes les étapes sont terminées -->
                            <?php if ($test['date_etape_3'] != '0000-00-00'): ?>
                            <div class="alert alert-success">
                                <h4><i class="fa-solid fa-trophy"></i> Test complet !</h4>
                                <p>Toutes les étapes ont été complétées. Vous pouvez finaliser ce test maintenant.</p>
                                <div class="form-group">
                                    <label>Commentaire (optionnel):</label>
                                    <textarea class="form-control" id="commentaire_scan" rows="3" 
                                              placeholder="Observations particulières..."></textarea>
                                </div>
                                <button type="button" class="btn btn-primary btn-lg btn-block" onclick="terminerTestScanComplet()">
                                    <i class="fa-solid fa-check-double"></i> Finaliser le test
                                </button>
                            </div>
                            <?php endif; ?>

                        </form>
                        
                            <!-- Zone de commentaire pour finalisation directe -->
                            <div class="panel panel-default" id="panelCommentaire" style="display: none;">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa-solid fa-comment"></i> Finalisation du test
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Commentaire (optionnel):</label>
                                        <textarea class="form-control" id="commentaire_finalisation" rows="3" 
                                                  placeholder="Raison de la finalisation, observations..."></textarea>
                                        <small class="help-block">
                                            Expliquez pourquoi vous finalisez le test à cette étape (conditions défavorables, résultat suffisant, etc.)
                                        </small>
                                    </div>
                                    <div class="btn-group btn-group-justified">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default" onclick="annulerFinalisation()">
                                                <i class="fa-solid fa-times"></i> Annuler
                                            </button>
                                        </div>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary" id="btnConfirmerFinalisation">
                                                <i class="fa-solid fa-check-double"></i> Confirmer la finalisation
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-xs-6">
                        <button type="button" class="btn btn-default btn-block" onclick="fermerModalScan()">
                            <i class="fa-solid fa-times"></i> Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script type="text/javascript">
// Auto-focus sur le champ de scan au chargement
$(document).ready(function() {
    $('#codeScan').focus();
    
    // Highlight du test focalisé
    <?php if (isset($_GET['focus'])): ?>
        setTimeout(function() {
            $('#test_<?php echo $_GET['focus']; ?>').addClass('highlight-test');
            $('#test_mobile_<?php echo $_GET['focus']; ?>').addClass('highlight-test');
            
            // Scroll vers le test sur mobile ou desktop
            var target = $('.visible-xs-block').is(':visible') ? 
                $('#test_mobile_<?php echo $_GET['focus']; ?>') : 
                $('#test_<?php echo $_GET['focus']; ?>');
                
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        }, 500);
    <?php endif; ?>
    
    // Optimisation mobile pour les modals
    if (/Mobi|Android/i.test(navigator.userAgent)) {
        $('.modal').on('shown.bs.modal', function() {
            $('body').addClass('modal-open-mobile');
        }).on('hidden.bs.modal', function() {
            $('body').removeClass('modal-open-mobile');
        });
    }
    
    // Amélioration de l'UX pour les boutons tactiles
    $('.btn').on('touchstart', function() {
        $(this).addClass('btn-touched');
    }).on('touchend', function() {
        var self = this;
        setTimeout(function() {
            $(self).removeClass('btn-touched');
        }, 150);
    });
});

// Auto-submit du formulaire de scan après saisie
$('#codeScan').on('input', function() {
    var code = $(this).val();
    if (code.length >= 8) { // Longueur minimale d'un code-barres de test
        // Délai pour permettre la fin de scan
        setTimeout(function() {
            if ($('#codeScan').val().length >= 8) {
                $('#codeScan').closest('form').submit();
            }
        }, 500);
    }
});

// Afficher la modal si elle doit être affichée
<?php if (isset($show_modal_scan) && $show_modal_scan): ?>
$(document).ready(function() {
    $('#modalScanRapide').modal('show');
});
<?php endif; ?>

// Prévenir le zoom sur iOS lors du focus sur les inputs
if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
    $('input[type="text"], input[type="number"], select').attr('style', 'font-size: 16px !important;');
}

// Fonctions pour la modal de scan rapide
function fermerModalScan() {
    $('#modalScanRapide').modal('hide');
    // Vider le champ de scan pour un prochain scan
    $('#codeScan').val('').focus();
}

// Fonction pour valider une étape sans finaliser
function validerEtapeScan(etape) {
    var valeur = $('#etape' + etape + '_scan').val();
    var test_id = $('#test_id_scan').val();
    
    if (!valeur || valeur < 0 || valeur > 50) {
        // Animation d'erreur
        var input = $('#etape' + etape + '_scan');
        input.addClass('form-control-error').focus();
        setTimeout(function() {
            input.removeClass('form-control-error');
        }, 1000);
        
        if (navigator.vibrate) {
            navigator.vibrate(200);
        }
        alert('Veuillez saisir une valeur entre 0 et 50');
        return;
    }
    
    // Désactiver le bouton pendant la requête
    var btn = $('button[onclick="validerEtapeScan(' + etape + ')"]');
    var originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Validation...');
    
    $.ajax({
        url: '/LogiGraine/ajax_valider_etape_germination.php',
        method: 'POST',
        data: { 
            etape: etape, 
            id_test: test_id, 
            valeur: valeur 
        },
        success: function(data) {
            if (data === 'ok') {
                // Animation de succès
                var input = $('#etape' + etape + '_scan');
                input.addClass('form-control-success');
                
                // Feedback tactile
                if (navigator.vibrate) {
                    navigator.vibrate([100, 50, 100]);
                }
                
                // Toast de confirmation
                var toast = $('<div class="alert alert-success toast-notification">' +
                            '<i class="fa-solid fa-check"></i> Étape ' + etape + ' validée avec succès !' +
                            '</div>');
                $('body').append(toast);
                setTimeout(function() {
                    toast.fadeOut(function() { $(this).remove(); });
                }, 2000);
                
                // Recharger la modal avec les nouvelles données
                setTimeout(function() {
                    window.location.href = '/LogiGraine/modules/germination.php?tab=encours&refresh=1';
                }, 1500);
            } else {
                btn.prop('disabled', false).html(originalText);
                alert('Erreur lors de la validation');
            }
        },
        error: function() {
            btn.prop('disabled', false).html(originalText);
            alert('Erreur de connexion');
        }
    });
}

// Fonction pour valider une étape ET finaliser le test
function validerEtFinaliserScan(etape) {
    console.log('validerEtFinaliserScan appelée pour étape:', etape);
    
    var valeur = $('#etape' + etape + '_scan').val();
    var test_id = $('#test_id_scan').val();
    
    console.log('Valeur:', valeur, 'Test ID:', test_id);
    
    if (!valeur || valeur < 0 || valeur > 50) {
        // Animation d'erreur
        var input = $('#etape' + etape + '_scan');
        input.addClass('form-control-error').focus();
        setTimeout(function() {
            input.removeClass('form-control-error');
        }, 1000);
        
        if (navigator.vibrate) {
            navigator.vibrate(200);
        }
        alert('Veuillez saisir une valeur entre 0 et 50');
        return;
    }
    
    // Masquer le formulaire principal et afficher la zone de commentaire
    $('#formSaisieRapide').slideUp(300);
    
    setTimeout(function() {
        $('#panelCommentaire').slideDown(300);
    }, 300);
    
    // Préparer les données pour la finalisation
    window.etapeAValider = etape;
    window.valeurAValider = valeur;
    window.testIdAValider = test_id;
    
    console.log('Données préparées:', {
        etape: window.etapeAValider,
        valeur: window.valeurAValider,
        testId: window.testIdAValider
    });
    
    // Focus sur le commentaire
    setTimeout(function() {
        $('#commentaire_finalisation').focus();
    }, 700);
    
    // Attacher l'événement au bouton de confirmation
    $('#btnConfirmerFinalisation').off('click').on('click', function() {
        console.log('Bouton confirmer cliqué');
        confirmerFinalisationAvecEtape();
    });
}

// Fonction pour annuler la finalisation et revenir au formulaire
function annulerFinalisation() {
    console.log('Annulation de la finalisation');
    
    $('#panelCommentaire').slideUp(300);
    
    setTimeout(function() {
        $('#formSaisieRapide').slideDown(300);
    }, 300);
    
    // Nettoyer les variables temporaires
    window.etapeAValider = null;
    window.valeurAValider = null;
    window.testIdAValider = null;
    
    // Vider le commentaire
    $('#commentaire_finalisation').val('');
}

// Fonction pour confirmer la finalisation avec validation d'étape
function confirmerFinalisationAvecEtape() {
    console.log('confirmerFinalisationAvecEtape appelée');
    console.log('Données disponibles:', {
        etape: window.etapeAValider,
        valeur: window.valeurAValider,
        testId: window.testIdAValider
    });
    
    if (!window.etapeAValider || !window.valeurAValider || !window.testIdAValider) {
        console.error('Données manquantes');
        alert('Erreur: données manquantes pour la finalisation');
        return;
    }
    
    var commentaire = $('#commentaire_finalisation').val();
    var btn = $('#btnConfirmerFinalisation');
    
    console.log('Commentaire:', commentaire);
    
    // Désactiver le bouton
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Finalisation...');
    
    // D'abord valider l'étape, puis finaliser le test
    console.log('Envoi requête validation étape...');
    $.ajax({
        url: '/LogiGraine/ajax_valider_etape_germination.php',
        method: 'POST',
        data: { 
            etape: window.etapeAValider, 
            id_test: window.testIdAValider, 
            valeur: window.valeurAValider 
        },
        success: function(data) {
            console.log('Réponse validation étape:', data);
            if (data === 'ok') {
                // Étape validée, maintenant finaliser le test
                console.log('Envoi requête finalisation test...');
                $.ajax({
                    url: '/LogiGraine/ajax_terminer_test_germination.php',
                    method: 'POST',
                    data: { 
                        id_test: window.testIdAValider,
                        commentaire: commentaire
                    },
                    success: function(data2) {
                        console.log('Réponse finalisation test:', data2);
                        if (data2 === 'ok') {
                            // Succès complet
                            if (navigator.vibrate) {
                                navigator.vibrate([200, 100, 200, 100, 200]);
                            }
                            
                            // Animation de succès sur la modal
                            $('.modal-content').addClass('border-success');
                            $('.modal-header').removeClass('bg-primary').addClass('bg-success');
                            
                            // Toast de confirmation
                            var toast = $('<div class="alert alert-success toast-notification">' +
                                        '<i class="fa-solid fa-trophy"></i> Test finalisé à l\'étape ' + window.etapeAValider + ' !' +
                                        '</div>');
                            $('body').append(toast);
                            
                            setTimeout(function() {
                                $('#modalScanRapide').modal('hide');
                                
                                setTimeout(function() {
                                    toast.fadeOut(function() { $(this).remove(); });
                                    // Retourner à l'onglet approprié
                                    window.location.href = '/LogiGraine/modules/germination.php?tab=sans';
                                }, 2000);
                            }, 1000);
                        } else {
                            console.error('Erreur finalisation test:', data2);
                            btn.prop('disabled', false).html('<i class="fa-solid fa-check-double"></i> Confirmer la finalisation');
                            alert('Erreur lors de la finalisation du test: ' + data2);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX finalisation:', {xhr: xhr, status: status, error: error});
                        btn.prop('disabled', false).html('<i class="fa-solid fa-check-double"></i> Confirmer la finalisation');
                        alert('Erreur de connexion lors de la finalisation');
                    }
                });
            } else {
                console.error('Erreur validation étape:', data);
                btn.prop('disabled', false).html('<i class="fa-solid fa-check-double"></i> Confirmer la finalisation');
                alert('Erreur lors de la validation de l\'étape: ' + data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX validation:', {xhr: xhr, status: status, error: error});
            btn.prop('disabled', false).html('<i class="fa-solid fa-check-double"></i> Confirmer la finalisation');
            alert('Erreur de connexion lors de la validation');
        }
    });
}

// Fonction pour finaliser un test déjà complet (toutes étapes terminées)
function terminerTestScanComplet() {
    var test_id = $('#test_id_scan').val();
    var commentaire = $('#commentaire_scan').val();
    
    if (confirm('Êtes-vous sûr de vouloir finaliser ce test ?')) {
        // Désactiver le bouton
        var btn = $('button[onclick="terminerTestScanComplet()"]');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Finalisation en cours...');
        
        $.ajax({
            url: '/LogiGraine/ajax_terminer_test_germination.php',
            method: 'POST',
            data: { 
                id_test: test_id,
                commentaire: commentaire
            },
            success: function(data) {
                if (data === 'ok') {
                    // Feedback de succès
                    if (navigator.vibrate) {
                        navigator.vibrate([200, 100, 200, 100, 200]);
                    }
                    
                    // Animation de succès
                    $('.modal-content').addClass('border-success');
                    $('.modal-header').removeClass('bg-primary').addClass('bg-success');
                    
                    setTimeout(function() {
                        $('#modalScanRapide').modal('hide');
                        
                        // Toast de confirmation
                        var toast = $('<div class="alert alert-success toast-notification">' +
                                    '<i class="fa-solid fa-check-circle"></i> Test finalisé avec succès !' +
                                    '</div>');
                        $('body').append(toast);
                        
                        setTimeout(function() {
                            toast.fadeOut(function() { $(this).remove(); });
                            // Retourner à l'onglet approprié
                            window.location.href = '/LogiGraine/modules/germination.php?tab=sans';
                        }, 2000);
                    }, 1000);
                } else {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-check-double"></i> Finaliser le test');
                    alert('Erreur lors de la finalisation du test');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="fa-solid fa-check-double"></i> Finaliser le test');
                alert('Erreur de connexion');
            }
        });
    }
}

function terminerTestScan() {
    var valeur = $('#etape' + etape + '_scan').val();
    var test_id = $('#test_id_scan').val();
    
    if (!valeur || valeur < 0 || valeur > 50) {
        // Animation d'erreur
        var input = $('#etape' + etape + '_scan');
        input.addClass('form-control-error').focus();
        setTimeout(function() {
            input.removeClass('form-control-error');
        }, 1000);
        
        if (navigator.vibrate) {
            navigator.vibrate(200);
        }
        alert('Veuillez saisir une valeur entre 0 et 50');
        return;
    }
    
    // Désactiver le bouton pendant la requête
    var btn = $('button[onclick="validerEtapeScan(' + etape + ')"]');
    var originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Validation...');
    
    $.ajax({
        url: '/LogiGraine/ajax_valider_etape_germination.php',
        method: 'POST',
        data: { 
            etape: etape, 
            id_test: test_id, 
            valeur: valeur 
        },
        success: function(data) {
            if (data === 'ok') {
                // Animation de succès
                var input = $('#etape' + etape + '_scan');
                input.addClass('form-control-success');
                
                // Feedback tactile
                if (navigator.vibrate) {
                    navigator.vibrate([100, 50, 100]);
                }
                
                // Recharger la modal avec les nouvelles données
                setTimeout(function() {
                    // Recharger la page pour mettre à jour la modal
                    window.location.href = '/LogiGraine/modules/germination.php?tab=encours&refresh=1';
                }, 1000);
            } else {
                btn.prop('disabled', false).html(originalText);
                alert('Erreur lors de la validation');
            }
        },
        error: function() {
            btn.prop('disabled', false).html(originalText);
            alert('Erreur de connexion');
        }
    });
}

function terminerTestScan() {
    // Cette fonction est maintenant remplacée par terminerTestScanComplet()
    // Gardée pour compatibilité
    terminerTestScanComplet();
}

function imprimerEtiquetteScan() {
    var test_id = $('#test_id_scan').val();
    
    $.ajax({
        url: '/LogiGraine/ajax_generer_etiquette_test.php',
        method: 'POST',
        data: { id_test: test_id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (typeof writeToSelectedPrinter === 'function') {
                    writeToSelectedPrinter(response.zpl_data);
                    
                    // Feedback visuel
                    if (navigator.vibrate) {
                        navigator.vibrate([100, 50, 100]);
                    }
                    
                    var toast = $('<div class="alert alert-info toast-notification">' +
                                '<i class="fa-solid fa-print"></i> Étiquette envoyée à l\'imprimante' +
                                '</div>');
                    $('body').append(toast);
                    setTimeout(function() {
                        toast.fadeOut(function() { $(this).remove(); });
                    }, 3000);
                } else {
                    alert('Étiquette générée:\nCode: ' + response.code_barre + 
                          '\nProduit: ' + response.reference + ' - ' + response.nom);
                }
            } else {
                alert('Erreur: ' + response.error);
            }
        },
        error: function() {
            alert('Erreur lors de la génération de l\'étiquette');
        }
    });
}

function attribuerCodeMobile(id_test) {
    var code_barre = $('#code_mobile_' + id_test).val();
    if (code_barre) {
        $.ajax({
            url: '/LogiGraine/ajax_attribuer_code_test.php',
            method: 'POST',
            data: { 
                id_test: id_test,
                code_barre: code_barre
            },
            success: function(data) {
                if (data === 'ok') {
                    // Animation de succès avant reload
                    $('#test_mobile_' + id_test).addClass('panel-success');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else if (data === 'code_utilise') {
                    // Vibration si disponible
                    if (navigator.vibrate) {
                        navigator.vibrate(200);
                    }
                    alert('Ce code-barres est déjà utilisé par un autre test en cours');
                    $('#code_mobile_' + id_test).focus();
                } else {
                    alert('Erreur lors de l\'attribution du code-barres');
                }
            },
            error: function() {
                alert('Erreur de connexion');
            }
        });
    } else {
        // Focus avec animation
        $('#code_mobile_' + id_test).addClass('form-control-error').focus();
        setTimeout(function() {
            $('#code_mobile_' + id_test).removeClass('form-control-error');
        }, 1000);
        alert('Veuillez saisir un code-barres');
    }
}

function validerEtapeMobile(etape, id_test) {
    var valeur = $('#etape' + etape + '_mobile_' + id_test).val();
    if (valeur && valeur >= 0 && valeur <= 50) {
        // Désactiver le bouton pendant la requête
        var btn = $('button[onclick="validerEtapeMobile(' + etape + ', ' + id_test + ')"]');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Validation...');
        
        $.ajax({
            url: '/LogiGraine/ajax_valider_etape_germination.php',
            method: 'POST',
            data: { 
                etape: etape, 
                id_test: id_test, 
                valeur: valeur 
            },
            success: function(data) {
                if (data === 'ok') {
                    // Animation de succès
                    $('#test_mobile_' + id_test).addClass('panel-success');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    btn.prop('disabled', false).html('Valider');
                    alert('Erreur lors de la validation');
                }
            },
            error: function() {
                btn.prop('disabled', false).html('Valider');
                alert('Erreur de connexion');
            }
        });
    } else {
        // Animation d'erreur
        var input = $('#etape' + etape + '_mobile_' + id_test);
        input.addClass('form-control-error').focus();
        setTimeout(function() {
            input.removeClass('form-control-error');
        }, 1000);
        
        if (navigator.vibrate) {
            navigator.vibrate(200);
        }
        alert('Veuillez saisir une valeur entre 0 et 50');
    }
}

function attribuerCode(id_test) {
    var code_barre = $('#code_' + id_test).val();
    if (code_barre) {
        $.ajax({
            url: '/LogiGraine/ajax_attribuer_code_test.php',
            method: 'POST',
            data: { 
                id_test: id_test,
                code_barre: code_barre
            },
            success: function(data) {
                if (data === 'ok') {
                    location.reload();
                } else if (data === 'code_utilise') {
                    alert('Ce code-barres est déjà utilisé par un autre test en cours');
                    $('#code_' + id_test).focus();
                } else {
                    alert('Erreur lors de l\'attribution du code-barres');
                }
            }
        });
    } else {
        alert('Veuillez saisir un code-barres');
        $('#code_' + id_test).focus();
    }
}

function pasDeTest(id_product, lot_germination) {
    if (confirm('Confirmer que ce lot ne nécessite pas de test de germination ?')) {
        // Indicateur de chargement
        var btn = $('button[onclick="pasDeTest(' + id_product + ', \'' + lot_germination + '\')"]');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        var form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        var actionInput = document.createElement('input');
        actionInput.name = 'action';
        actionInput.value = 'pas_de_test';
        form.appendChild(actionInput);
        
        var idInput = document.createElement('input');
        idInput.name = 'id_product';
        idInput.value = id_product;
        form.appendChild(idInput);
        
        var lotInput = document.createElement('input');
        lotInput.name = 'lot_germination';
        lotInput.value = lot_germination;
        form.appendChild(lotInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function lancerTest(id_product, lot_germination) {
    // Interface mobile pour saisie du code-barres
    var code_barre = prompt('Scannez ou saisissez le code-barres pour ce test (optionnel):');
    
    if (code_barre !== null) { // null si annulé, chaîne vide si rien saisi
        var form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        var actionInput = document.createElement('input');
        actionInput.name = 'action';
        actionInput.value = 'lancer_test';
        form.appendChild(actionInput);
        
        var idInput = document.createElement('input');
        idInput.name = 'id_product';
        idInput.value = id_product;
        form.appendChild(idInput);
        
        var lotInput = document.createElement('input');
        lotInput.name = 'lot_germination';
        lotInput.value = lot_germination;
        form.appendChild(lotInput);
        
        if (code_barre) {
            var codeInput = document.createElement('input');
            codeInput.name = 'code_barre_lot';
            codeInput.value = code_barre;
            form.appendChild(codeInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

function ajouterTest() {
    $('#modalAjoutTest').modal('show');
    // Focus sur le premier champ après ouverture du modal
    setTimeout(function() {
        $('#selectProduit').focus();
    }, 500);
}

function chargerLots() {
    var id_product = $('#selectProduit').val();
    if (id_product) {
        $('#selectLot').html('<option value="">Chargement...</option>');
        $.ajax({
            url: '/LogiGraine/ajax_get_lots_germination.php',
            method: 'POST',
            data: { id_product: id_product },
            success: function(data) {
                $('#selectLot').html(data);
            },
            error: function() {
                $('#selectLot').html('<option value="">Erreur de chargement</option>');
            }
        });
    } else {
        $('#selectLot').html('<option value="">-- Sélectionner d\'abord un produit --</option>');
    }
}

$( document ).ready(function() {
document.getElementById('inputCodeBarre').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
    }
});

document.getElementById('inputEan').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        $.ajax({
            url: '/LogiGraine/ajax_get_product_germination.php',
            method: 'POST',
            data: { ean: $('#inputEan').val() },
            success: function(data) {
                $("#selectProduit").val(data).change();
                chargerLots();
            },
        });
    }
});
});

function confirmerAjoutTest() {
    var id_lot = $('#selectLot').val();
    var code_barre = $('#inputCodeBarre').val();
    
    if (id_lot) {
        // Désactiver le bouton pendant la création
        var btn = $('button[onclick="confirmerAjoutTest()"]');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Création...');
        
        var form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        var actionInput = document.createElement('input');
        actionInput.name = 'action';
        actionInput.value = 'nouveau_test';
        form.appendChild(actionInput);
        
        var lotInput = document.createElement('input');
        lotInput.name = 'id_lot';
        lotInput.value = id_lot;
        form.appendChild(lotInput);
        
        if (code_barre) {
            var codeInput = document.createElement('input');
            codeInput.name = 'code_barre';
            codeInput.value = code_barre;
            form.appendChild(codeInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    } else {
        alert('Veuillez sélectionner un lot');
        $('#selectLot').focus();
    }
}

function validerEtape(etape, id_test) {
    var valeur = $('#etape' + etape + '_' + id_test).val();
    if (valeur && valeur >= 0 && valeur <= 50) {
        $.ajax({
            url: '/LogiGraine/ajax_valider_etape_germination.php',
            method: 'POST',
            data: { 
                etape: etape, 
                id_test: id_test, 
                valeur: valeur 
            },
            success: function(data) {
                if (data === 'ok') {
                    location.reload();
                } else {
                    alert('Erreur lors de la validation');
                }
            }
        });
    } else {
        alert('Veuillez saisir une valeur entre 0 et 50');
        $('#etape' + etape + '_' + id_test).focus();
    }
}

function supprimerTest(id_test) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce test ?')) {
        $.ajax({
            url: '/LogiGraine/ajax_supprimer_test_germination.php',
            method: 'POST',
            data: { id_test: id_test },
            success: function(data) {
                if (data === 'ok') {
                    // Animation de suppression
                    $('#test_' + id_test + ', #test_mobile_' + id_test).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Erreur lors de la suppression');
                }
            }
        });
    }
}

function imprimerEtiquette(id_test) {
    $.ajax({
        url: '/LogiGraine/ajax_generer_etiquette_test.php',
        method: 'POST',
        data: { id_test: id_test },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Si imprimante Zebra disponible, imprimer directement
                if (typeof writeToSelectedPrinter === 'function') {
                    writeToSelectedPrinter(response.zpl_data);
                    
                    // Feedback visuel sur mobile
                    if (/Mobi|Android/i.test(navigator.userAgent)) {
                        if (navigator.vibrate) {
                            navigator.vibrate([100, 50, 100]);
                        }
                        
                        // Toast-like notification
                        var toast = $('<div class="alert alert-success" style="position:fixed;top:20px;right:20px;z-index:9999;">Étiquette envoyée à l\'imprimante</div>');
                        $('body').append(toast);
                        setTimeout(function() {
                            toast.fadeOut(function() { $(this).remove(); });
                        }, 3000);
                    } else {
                        alert('Étiquette envoyée à l\'imprimante');
                    }
                } else {
                    // Affichage des informations si pas d'imprimante
                    var info = 'Étiquette générée:\n' + 
                              'Code-barres: ' + response.code_barre + '\n' +
                              'Produit: ' + response.reference + ' - ' + response.nom + '\n' +
                              'Lot: ' + response.lot;
                    alert(info);
                }
            } else {
                alert('Erreur: ' + response.error);
            }
        },
        error: function() {
            alert('Erreur lors de la génération de l\'étiquette');
        }
    });
}

function terminerTest(id_test) {
    var commentaire = prompt('Commentaire (optionnel) :');
    if (commentaire !== null) {
        $.ajax({
            url: '/LogiGraine/ajax_terminer_test_germination.php',
            method: 'POST',
            data: { 
                id_test: id_test,
                commentaire: commentaire
            },
            success: function(data) {
                if (data === 'ok') {
                    // Feedback sur mobile
                    if (/Mobi|Android/i.test(navigator.userAgent) && navigator.vibrate) {
                        navigator.vibrate([200, 100, 200]);
                    }
                    
                    alert('Test terminé avec succès');
                    location.reload();
                } else {
                    alert('Erreur lors de la finalisation du test');
                }
            }
        });
    }
}

// Fonction pour ouvrir la modal de saisie depuis la vue "Tests en cours"
function ouvrirModalSaisie(id_test) {
    // Désactiver temporairement le focus sur le scan
    $('#codeScan').blur();
    
    // Charger les données du test via AJAX
    $.ajax({
        url: '/LogiGraine/ajax_get_test_germination.php',
        method: 'POST',
        data: { id_test: id_test },
        dataType: 'json',
        success: function(test) {
            if (test.error) {
                alert('Erreur: ' + test.error);
                return;
            }        
            
            // Créer le contenu de la modal
            var modalHtml = `
            <div class="modal fade" id="modalSaisieManuelle" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h4 class="modal-title text-white">
                                <i class="fa-solid fa-seedling"></i> Test de Germination
                                <button type="button" class="close text-white" onclick="fermerModalSaisie()" style="float: right;">
                                    <span>&times;</span>
                                </button>
                            </h4>
                        </div>
                        <div class="modal-body">
                            <!-- Informations du test -->
                            <div class="row" style="margin-bottom: 20px;">
                                <div class="col-xs-12">
                                    <div class="panel panel-info">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6">
                                                    <h4 style="margin-top: 0;">
                                                        <strong>${test.reference}</strong>
                                                    </h4>
                                                    <p>${test.name}</p>
                                                    <span class="label label-primary">Lot: ${test.numero_lot_LBG}</span>
                                                    ${test.code_barre ? '<span class="label label-info">' + test.code_barre + '</span>' : ''}
                                                </div>
                                                <div class="col-xs-6 col-sm-6 text-right">
                                                    <p><strong>Début:</strong> ${formatDate(test.date_debut_semis)}</p>
                                                    <p><strong>Statut:</strong> ${getStatutLabel(test)}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            
                            <!-- Saisie des étapes -->
                            <div class="row">
                                <div class="col-xs-12">
                                    <form id="formSaisieManuelle">
                                        <input type="hidden" id="test_id_modal" value="${test.id}">
                                        
                                        ${genererHTMLEtapes(test)}
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="row">                                
                                <div class="${test.code_barre ? 'col-xs-6' : 'col-xs-12'}">
                                    <button type="button" class="btn btn-default btn-block" onclick="fermerModalSaisie()">
                                        <i class="fa-solid fa-times"></i> Fermer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
            
            // Supprimer toute modal existante
            $('#modalSaisieManuelle').remove();
            
            // Ajouter la nouvelle modal
            $('body').append(modalHtml);
            
            // Afficher la modal
            $('#modalSaisieManuelle').modal('show');
            
            // Focus sur le premier champ disponible
            $('#modalSaisieManuelle').on('shown.bs.modal', function() {
                $('#modalSaisieManuelle input[type="number"]:first').focus();
            });
        },
        error: function() {
            alert('Erreur lors du chargement des données du test');
        }
    });
}

// Fonctions utilitaires
function formatDate(dateString) {
    var date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function getStatutLabel(test) {
    if (test.date_etape_3 != '0000-00-00') {
        return '<span class="label label-warning">Prêt à finaliser</span>';
    } else if (test.date_etape_2 != '0000-00-00') {
        return '<span class="label label-info">Étape 3 en attente</span>';
    } else if (test.date_etape_1 != '0000-00-00') {
        return '<span class="label label-info">Étape 2 en attente</span>';
    } else {
        return '<span class="label label-info">Étape 1 en attente</span>';
    }
}

function genererHTMLEtapes(test) {
    var html = '';
    
    // Étape 1
    html += '<div class="form-group">';
    html += '<label class="control-label"><i class="fa-solid fa-seedling"></i> Étape 1</label>';
    if (test.date_etape_1 == '0000-00-00') {
        html += '<div class="input-group input-group-lg">';
        html += '<input type="number" class="form-control" id="etape1_modal" placeholder="Nombre de graines germées" min="0" max="50">';
        html += '</div>';
        html += '<div class="btn-group btn-group-justified" style="margin-top: 10px;">';
        html += '<div class="btn-group">';
        html += '<button type="button" class="btn btn-success btn-lg" onclick="validerEtapeModal(1)"><i class="fa-solid fa-check"></i> Valider</button>';
        html += '</div>';
        html += '<div class="btn-group">';
        html += '<button type="button" class="btn btn-primary btn-lg" onclick="validerEtFinaliserModal(1)"><i class="fa-solid fa-check-double"></i> Valider et Finaliser</button>';
        html += '</div>';
        html += '</div>';
    } else {
        html += '<div class="well">';
        html += '<i class="fa-solid fa-check-circle text-success"></i> ';
        html += '<strong>' + test.resultat_etape_1 + '%</strong> ';
        html += '<small class="text-muted">(' + formatDate(test.date_etape_1) + ')</small>';
        html += '</div>';
    }
    html += '</div>';
    
    // Étape 2
    if (test.date_etape_1 != '0000-00-00') {
        html += '<div class="form-group">';
        html += '<label class="control-label"><i class="fa-solid fa-seedling"></i> Étape 2</label>';
        if (test.date_etape_2 == '0000-00-00') {
            html += '<div class="input-group input-group-lg">';
            html += '<input type="number" class="form-control" id="etape2_modal" placeholder="Nombre de graines germées" min="0" max="50">';
            html += '</div>';
            html += '<div class="btn-group btn-group-justified" style="margin-top: 10px;">';
            html += '<div class="btn-group">';
            html += '<button type="button" class="btn btn-success btn-lg" onclick="validerEtapeModal(2)"><i class="fa-solid fa-check"></i> Valider</button>';
            html += '</div>';
            html += '<div class="btn-group">';
            html += '<button type="button" class="btn btn-primary btn-lg" onclick="validerEtFinaliserModal(2)"><i class="fa-solid fa-check-double"></i> Valider et Finaliser</button>';
            html += '</div>';
            html += '</div>';
        } else {
            html += '<div class="well">';
            html += '<i class="fa-solid fa-check-circle text-success"></i> ';
            html += '<strong>' + test.resultat_etape_2 + '%</strong> ';
            html += '<small class="text-muted">(' + formatDate(test.date_etape_2) + ')</small>';
            html += '</div>';
        }
        html += '</div>';
    }
    
    // Étape 3
    if (test.date_etape_2 != '0000-00-00') {
        html += '<div class="form-group">';
        html += '<label class="control-label"><i class="fa-solid fa-seedling"></i> Étape 3</label>';
        if (test.date_etape_3 == '0000-00-00') {
            html += '<div class="input-group input-group-lg">';
            html += '<input type="number" class="form-control" id="etape3_modal" placeholder="Nombre de graines germées" min="0" max="50">';
            html += '</div>';
            html += '<div class="btn-group btn-group-justified" style="margin-top: 10px;">';
            html += '<div class="btn-group">';
            html += '<button type="button" class="btn btn-success btn-lg" onclick="validerEtapeModal(3)"><i class="fa-solid fa-check"></i> Valider</button>';
            html += '</div>';
            html += '<div class="btn-group">';
            html += '<button type="button" class="btn btn-primary btn-lg" onclick="validerEtFinaliserModal(3)"><i class="fa-solid fa-check-double"></i> Valider et Finaliser</button>';
            html += '</div>';
            html += '</div>';
        } else {
            html += '<div class="well">';
            html += '<i class="fa-solid fa-check-circle text-success"></i> ';
            html += '<strong>' + test.resultat_etape_3 + '%</strong> ';
            html += '<small class="text-muted">(' + formatDate(test.date_etape_3) + ')</small>';
            html += '</div>';
        }
        html += '</div>';
    }
    
    // Zone de finalisation si toutes les étapes sont terminées
    if (test.date_etape_3 != '0000-00-00') {
        html += '<div class="alert alert-success">';
        html += '<h4><i class="fa-solid fa-trophy"></i> Test complet !</h4>';
        html += '<p>Toutes les étapes ont été complétées. Vous pouvez finaliser ce test maintenant.</p>';
        html += '<div class="form-group">';
        html += '<label>Commentaire (optionnel):</label>';
        html += '<textarea class="form-control" id="commentaire_modal" rows="3" placeholder="Observations particulières..."></textarea>';
        html += '</div>';
        html += '<button type="button" class="btn btn-primary btn-lg btn-block" onclick="terminerTestModal()"><i class="fa-solid fa-check-double"></i> Finaliser le test</button>';
        html += '</div>';
    }
    
    // Zone de commentaire pour finalisation directe (cachée par défaut)
    html += '<div class="panel panel-default" id="panelCommentaireModal" style="display: none;">';
    html += '<div class="panel-heading"><h4 class="panel-title"><i class="fa-solid fa-comment"></i> Finalisation du test</h4></div>';
    html += '<div class="panel-body">';
    html += '<div class="form-group">';
    html += '<label>Commentaire (optionnel):</label>';
    html += '<textarea class="form-control" id="commentaire_finalisation_modal" rows="3" placeholder="Raison de la finalisation, observations..."></textarea>';
    html += '</div>';
    html += '<div class="btn-group btn-group-justified">';
    html += '<div class="btn-group"><button type="button" class="btn btn-default" onclick="annulerFinalisationModal()"><i class="fa-solid fa-times"></i> Annuler</button></div>';
    html += '<div class="btn-group"><button type="button" class="btn btn-primary" onclick="confirmerFinalisationModal()"><i class="fa-solid fa-check-double"></i> Confirmer la finalisation</button></div>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    
    return html;
}

// Fonctions pour la modal manuelle
function fermerModalSaisie() {
    $('#modalSaisieManuelle').modal('hide');
    // Remettre le focus sur le scan
    setTimeout(function() {
        $('#codeScan').focus();
    }, 500);
}

function validerEtapeModal(etape) {
    var valeur = $('#etape' + etape + '_modal').val();
    var test_id = $('#test_id_modal').val();
    
    if (!valeur || valeur < 0 || valeur > 50) {
        $('#etape' + etape + '_modal').addClass('form-control-error').focus();
        setTimeout(function() {
            $('#etape' + etape + '_modal').removeClass('form-control-error');
        }, 1000);
        alert('Veuillez saisir une valeur entre 0 et 50');
        return;
    }
    
    $.ajax({
        url: '/LogiGraine/ajax_valider_etape_germination.php',
        method: 'POST',
        data: { 
            etape: etape, 
            id_test: test_id, 
            valeur: valeur 
        },
        success: function(data) {
            if (data === 'ok') {
                $('#modalSaisieManuelle').modal('hide');
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            } else {
                alert('Erreur lors de la validation');
            }
        }
    });
}

function validerEtFinaliserModal(etape) {
    var valeur = $('#etape' + etape + '_modal').val();
    
    if (!valeur || valeur < 0 || valeur > 50) {
        $('#etape' + etape + '_modal').addClass('form-control-error').focus();
        setTimeout(function() {
            $('#etape' + etape + '_modal').removeClass('form-control-error');
        }, 1000);
        alert('Veuillez saisir une valeur entre 0 et 50');
        return;
    }
    
    // Stocker les données
    window.etapeModalAValider = etape;
    window.valeurModalAValider = valeur;
    window.testIdModalAValider = $('#test_id_modal').val();
    
    // Afficher la zone de commentaire
    $('#formSaisieManuelle').slideUp(300);
    setTimeout(function() {
        $('#panelCommentaireModal').slideDown(300);
        $('#commentaire_finalisation_modal').focus();
    }, 300);
}

function annulerFinalisationModal() {
    $('#panelCommentaireModal').slideUp(300);
    setTimeout(function() {
        $('#formSaisieManuelle').slideDown(300);
    }, 300);
    
    // Nettoyer les variables
    window.etapeModalAValider = null;
    window.valeurModalAValider = null;
    window.testIdModalAValider = null;
    
    $('#commentaire_finalisation_modal').val('');
}

function confirmerFinalisationModal() {
    if (!window.etapeModalAValider || !window.valeurModalAValider || !window.testIdModalAValider) {
        alert('Erreur: données manquantes pour la finalisation');
        return;
    }
    
    var commentaire = $('#commentaire_finalisation_modal').val();
    var btn = $('button[onclick="confirmerFinalisationModal()"]');
    
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Finalisation...');
    
    // D'abord valider l'étape
    $.ajax({
        url: '/LogiGraine/ajax_valider_etape_germination.php',
        method: 'POST',
        data: { 
            etape: window.etapeModalAValider, 
            id_test: window.testIdModalAValider, 
            valeur: window.valeurModalAValider 
        },
        success: function(data) {
            if (data === 'ok') {
                // Puis finaliser le test
                $.ajax({
                    url: '/LogiGraine/ajax_terminer_test_germination.php',
                    method: 'POST',
                    data: { 
                        id_test: window.testIdModalAValider,
                        commentaire: commentaire
                    },
                    success: function(data2) {
                        if (data2 === 'ok') {
                            $('#modalSaisieManuelle').modal('hide');
                            
                            var toast = $('<div class="alert alert-success toast-notification">' +
                                        '<i class="fa-solid fa-trophy"></i> Test finalisé avec succès !' +
                                        '</div>');
                            $('body').append(toast);
                            
                            setTimeout(function() {
                                toast.fadeOut(function() { $(this).remove(); });
                                window.location.href = '/LogiGraine/modules/germination.php?tab=sans';
                            }, 2000);
                        } else {
                            btn.prop('disabled', false).html('<i class="fa-solid fa-check-double"></i> Confirmer la finalisation');
                            alert('Erreur lors de la finalisation du test');
                        }
                    }
                });
            } else {
                btn.prop('disabled', false).html('<i class="fa-solid fa-check-double"></i> Confirmer la finalisation');
                alert('Erreur lors de la validation de l\'étape');
            }
        }
    });
}

function terminerTestModal() {
    var test_id = $('#test_id_modal').val();
    var commentaire = $('#commentaire_modal').val();
    
    if (confirm('Êtes-vous sûr de vouloir finaliser ce test ?')) {
        var btn = $('button[onclick="terminerTestModal()"]');
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Finalisation...');
        
        $.ajax({
            url: '/LogiGraine/ajax_terminer_test_germination.php',
            method: 'POST',
            data: { 
                id_test: test_id,
                commentaire: commentaire
            },
            success: function(data) {
                if (data === 'ok') {
                    $('#modalSaisieManuelle').modal('hide');
                    
                    var toast = $('<div class="alert alert-success toast-notification">' +
                                '<i class="fa-solid fa-check-circle"></i> Test finalisé avec succès !' +
                                '</div>');
                    $('body').append(toast);
                    
                    setTimeout(function() {
                        toast.fadeOut(function() { $(this).remove(); });
                        window.location.href = '/LogiGraine/modules/germination.php?tab=sans';
                    }, 2000);
                } else {
                    btn.prop('disabled', false).html('<i class="fa-solid fa-check-double"></i> Finaliser le test');
                    alert('Erreur lors de la finalisation du test');
                }
            }
        });
    }
}

function imprimerEtiquetteModal(id_test) {
    imprimerEtiquette(id_test);
}

// Fonctions pour les filtres
function appliquerFiltreCategorie() {
    var categorie = $('#filterCategorie').val();
    var search = $('#filterRecherche').val();
    var tab = getActiveTab();
    
    var url = window.location.pathname + '?tab=' + tab;
    
    if (categorie) {
        url += '&cat=' + categorie;
    }
    
    if (search) {
        url += '&search=' + encodeURIComponent(search);
    }
    
    window.location.href = url;
}

function appliquerFiltreRecherche() {
    // Délai pour éviter trop de requêtes pendant la frappe
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(function() {
        var categorie = $('#filterCategorie').val();
        var search = $('#filterRecherche').val();
        var tab = getActiveTab();

        $.ajax({            
            url: '/LogiGraine/ajax_session_germination.php',
            method: 'POST',
            data: { search: search },
            success: function (response) {
                //optional handling of the response
            }
        });
        
        // Filtrage côté client pour une réactivité immédiate
        if (search.length >= 2) {
            filtrerTableauClient(search);
        } else if (search.length === 0) {
            afficherTousLesElements();
        }
        
        // Mise à jour de l'URL sans recharger la page
        var newUrl = window.location.pathname + '?tab=' + tab;
        if (categorie) {
            newUrl += '&cat=' + categorie;
        }
        if (search) {
            newUrl += '&search=' + encodeURIComponent(search);
        }
        
        window.history.replaceState({}, '', newUrl);
    }, 300);
}

function filtrerTableauClient(search) {
    var searchLower = search.toLowerCase();
    var countVisible = 0;
    
    // Pour les tableaux desktop
    $('table tbody tr').each(function() {
        var reference = $(this).find('td:first').text().toLowerCase();
        var nom = $(this).find('td:nth-child(2)').text().toLowerCase();
        var visible = reference.includes(searchLower) || nom.includes(searchLower);
        
        $(this).toggle(visible);
        if (visible) countVisible++;
    });
    
    // Pour les cards mobile
    $('.product-card, .test-card').each(function() {
        var reference = $(this).find('h4').text().toLowerCase();
        var nom = $(this).find('p').text().toLowerCase();
        var visible = reference.includes(searchLower) || nom.includes(searchLower);
        
        $(this).toggle(visible);
        if (visible && $('.visible-xs-block').is(':visible')) countVisible++;
    });
    
    updateCompteur(countVisible);
}

function afficherTousLesElements() {
    $('table tbody tr').show();
    $('.product-card, .test-card').show();
    
    var count = $('.visible-xs-block').is(':visible') ? 
        $('.product-card:visible, .test-card:visible').length : 
        $('table tbody tr:visible').length;
    
    updateCompteur(count);
}

function updateCompteur(count) {
    var total = $('.visible-xs-block').is(':visible') ? 
        $('.product-card, .test-card').length : 
        $('table tbody tr').length;
    
    $('#compteurResultats').text(count + ' / ' + total + ' résultats');
}

function resetFiltres() {
    $('#filterCategorie').val('');
    $('#filterRecherche').val('');
    var tab = getActiveTab();
    window.location.href = window.location.pathname + '?tab=' + tab;
}

function getActiveTab() {
    var urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('tab') || 'sans';
}

// Au chargement de la page, initialiser le compteur
$(document).ready(function() {
    var count = $('.visible-xs-block').is(':visible') ? 
        $('.product-card:visible, .test-card:visible').length : 
        $('table tbody tr:visible').length;
    
    updateCompteur(count);
    
    // Si une recherche est présente dans l'URL, appliquer le filtre
    var urlParams = new URLSearchParams(window.location.search);
    var search = urlParams.get('search');
    if (search) {
        filtrerTableauClient(search);
    }
});

// Ajouter la recherche rapide avec les touches du clavier
$('#filterRecherche').on('keypress', function(e) {
    if (e.which === 13) { // Touche Entrée
        e.preventDefault();
        appliquerFiltreCategorie();
    }
});

// Fonction pour exporter les résultats filtrés
function exporterResultats() {
    var categorie = $('#filterCategorie').val();
    var search = $('#filterRecherche').val();
    var tab = getActiveTab();
    
    var url = '/LogiGraine/ajax_export_germination.php?tab=' + tab;
    
    if (categorie) {
        url += '&cat=' + categorie;
    }
    
    if (search) {
        url += '&search=' + encodeURIComponent(search);
    }
    
    window.open(url, '_blank');
}

</script>

<!-- Intégration imprimante Zebra -->
<script type="text/javascript" src="../BrowserPrint-3.1.250.min.js"></script>
<script type="text/javascript">
var selected_device;
var devices = [];

function setup_printer() {
    BrowserPrint.getDefaultDevice("printer", function(device) {
        selected_device = device;
        devices.push(device);
        
        BrowserPrint.getLocalDevices(function(device_list){
            for(var i = 0; i < device_list.length; i++) {
                var device = device_list[i];
                if(!selected_device || device.uid != selected_device.uid) {
                    devices.push(device);
                }
            }
        }, function(){
            console.log("Aucune imprimante trouvée");
        },"printer");
    }, function(error){
        console.log('Imprimante non détectée');
    });
}

function writeToSelectedPrinter(dataToWrite) {
    if (selected_device) {
        selected_device.send(dataToWrite, undefined, function(errorMessage){
            console.log("Erreur impression: " + errorMessage);
        });
    }
}

// Initialiser les imprimantes au chargement
window.onload = function() {
    setup_printer();
    appliquerFiltreRecherche();
};
</script>

<style>
.highlight-test {
    background-color: #fcf8e3 !important;
    border: 2px solid #faebcc !important;
    animation: pulse 2s ease-in-out;
}

@keyframes pulse {
    0% { background-color: #fcf8e3; }
    50% { background-color: #fff3cd; }
    100% { background-color: #fcf8e3; }
}

.panel-info .panel-heading {
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.label-info {
    font-family: 'Courier New', monospace;
    font-size: 11px;
}
/* Styles pour la zone de filtres */
.panel-default .panel-heading {
    background-color: #f5f5f5;
    border-color: #ddd;
}

#filterCategorie, #filterRecherche {
    font-size: 14px;
}

/* Highlight des résultats de recherche */
.highlight-search {
    background-color: #fff3cd !important;
}

/* Animation de filtrage */
table tbody tr, .product-card, .test-card {
    transition: opacity 0.3s ease;
}

table tbody tr[style*="display: none"], 
.product-card[style*="display: none"], 
.test-card[style*="display: none"] {
    opacity: 0;
}

/* Compteur de résultats */
#compteurResultats {
    font-size: 14px;
    padding: 5px 10px;
}

/* Responsive pour les filtres */
@media (max-width: 767px) {
    .panel-body .row {
        margin-bottom: 10px;
    }
    
    .form-group {
        margin-bottom: 10px;
    }
    
    #compteurResultats {
        display: block;
        margin-top: 10px;
        float: none !important;
        text-align: center;
    }
}

/* Bouton reset */
.btn-default.btn-sm {
    background-color: #f5f5f5;
    border-color: #ccc;
}

.btn-default.btn-sm:hover {
    background-color: #e6e6e6;
    border-color: #adadad;
}

/* Amélioration de la lisibilité */
.form-control:focus {
    border-color: #66afe9;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* Message quand aucun résultat */
.no-results {
    text-align: center;
    padding: 20px;
    color: #666;
    font-style: italic;
}
</style>

<?php include('../footer.php'); ?>
</body>
</html>