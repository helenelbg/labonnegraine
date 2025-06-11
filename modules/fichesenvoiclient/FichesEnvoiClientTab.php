<?php

//include_once(_PS_CLASS_DIR_ . 'AdminTab.php');
//include_once(PS_ADMIN_DIR . '/tabs/AdminAttributes.php');

class FichesEnvoiClientTab extends AdminController
{

    public $name = 'fichesenvoiclient';
    public $displayName = 'Envoi des fiches aux clients';

    public function display($token = NULL)
    {
        if(isset($_GET['add_fichier']))
        {
            $this->display_add_fichier();
        }
        elseif(isset($_GET['edit_fichier']))
        {
            $this->display_add_fichier($_GET['edit_fichier']);
        }
        elseif(isset($_GET['supprimer_fichier']))
        {
            $this->supprimer_fichier($_GET['supprimer_fichier']);
        }
        elseif(isset($_GET['add_page']))
        {
            $this->display_add_page();
        }
        elseif(isset($_GET['supprimer_association']))
        {
            $this->supprimer_association($_GET['supprimer_association']);
        }
        elseif(isset($_GET['edit_association']))
        {
             $this->edit_association($_GET['edit_association']); 
        }
        else
        {
            $this->display_liste_fichier();
        }
    }

    function display_add_fichier($id_fichier = "")
    {
        $currentIndex = '/admin123/index.php?controller=FichesEnvoiClientTab';
        $chemin_destination = dirname(__FILE__) . '/fiches/';
        if (isset($_POST['submit_fichier']))
        {
            $nom_fichier = date("YmdHis") . "_" . stripslashes(clear_nom_fichier($_FILES['file_fichier']['name']));
            if ((isset($_FILES['file_fichier']['tmp_name']) && ($_FILES['file_fichier']['error'] == UPLOAD_ERR_OK)))
            {
                if (move_uploaded_file($_FILES['file_fichier']['tmp_name'], $chemin_destination . $nom_fichier))
                {
                    if (isset($_POST['id_file']))
                    {
                        $query_recup = "SELECT * FROM " . _DB_PREFIX_ . "fichiers_infos WHERE id_fichier = '" . $_POST['id_file'] . "';";
                        $result = Db::getInstance()->ExecuteS($query_recup);
                        $result = $result[0];
                        unlink($chemin_destination . $result['fichier']);
                        $query = "UPDATE " . _DB_PREFIX_ . "fichiers_infos SET nom_fichier = '" . $_POST['nom_fichier'] . "', fichier='" . $nom_fichier . "', date_maj=NOW() WHERE id_fichier = '" . trim($_POST['id_file']) . "';";
                        if (Db::getInstance()->Execute($query))
                        {
                            echo '<script>document.location.href="' . $currentIndex . '&ok_fichier_update&token=' . $this->token . '";</script>';
                        }
                    }
                    else
                    {
                        $query = "INSERT INTO " . _DB_PREFIX_ . "fichiers_infos SET nom_fichier = '" . $_POST['nom_fichier'] . "', fichier='" . $nom_fichier . "', date_add=NOW(), date_maj=NOW();";
                        if (Db::getInstance()->Execute($query))
                        {
                            echo '<script>document.location.href="' . $currentIndex . '&ok_fichier&token=' . $this->token . '";</script>';
                        }
                    }
                }
            }
            else
            {
                if (isset($_POST['id_file']))
                {
                    $query = "UPDATE " . _DB_PREFIX_ . "fichiers_infos SET nom_fichier = '" . $_POST['nom_fichier'] . "', date_maj=NOW() WHERE id_fichier = '" . trim($_POST['id_file']) . "';";
                    if (Db::getInstance()->Execute($query))
                    {
                        echo '<script>document.location.href="' . $currentIndex . '&ok_fichier_update&token=' . $this->token . '";</script>';
                    }
                }
            }
        }
        echo "<form method='POST' name='add_file_fiche_envoi_client' enctype='multipart/form-data'>";

        if (trim($id_fichier) != "")
        {
            $fichier = fichier_details($id_fichier);
            echo "<label for='nom_fichier'>Nom du fichier : </label><input type='text' name='nom_fichier' id='nom_fichier' style='width : 300px' value=\"" . $fichier['nom_fichier'] . "\" /><br /><br />";

            echo "<input type='hidden' name='id_file' value='" . $fichier['id_fichier'] . "'/>";
            echo "<label for='file_fichier'>Nouveau fichier : </label><input type='file' name='file_fichier' id='file_fichier' />";
            echo "<br /><br /><center><input type='button' value='Retour' onclick='document.location.href=\"" . $currentIndex . "&token=" . $this->token . "\";'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='submit_fichier' id='submit_fichier' value='Modifier le fichier' /></center>";
        }
        else
        {
            echo "<label for='nom_fichier'>Nom du fichier : </label><input type='text' name='nom_fichier' id='nom_fichier' style='width : 300px' /><br /><br />";
            echo "<label for='file_fichier'>Fichier : </label><input type='file' name='file_fichier' id='file_fichier' />";
            echo "<br /><br /><center><input type='button' value='Retour' onclick='document.location.href=\"" . $currentIndex . "&token=" . $this->token . "\";'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' name='submit_fichier' id='submit_fichier' value='Ajouter le fichier' /></center>";
        }

        echo "</form>";
    }

    function display_liste_fichier()
    {
        $currentIndex = '/admin123/index.php?controller=FichesEnvoiClientTab';
        if (isset($_GET['ok_fichier']))
        {
            echo "<div style='border:1px solid green; padding:5px;text-align:center;color:green;font-weight:bold;font-style:italic;margin-bottom:15px;'>Fichier ajout&eacute; avec succ&egraves</div>";
        }
        if (isset($_GET['ok_fichier_update']))
        {
            echo "<div style='border:1px solid green; padding:5px;text-align:center;color:green;font-weight:bold;font-style:italic;margin-bottom:15px;'>Fichier mis &agrave; jour avec succ&egraves</div>";
        }
        if (isset($_GET['ok_categorie']))
        {
            echo "<div style='border:1px solid green; padding:5px;text-align:center;color:green;font-weight:bold;font-style:italic;margin-bottom:15px;'>Produits associ&eacute;s avec succ&egrave;s</div>";
        }
        if (isset($_GET['erreur_delete']))
        {
            echo "<div style='border:1px solid red; padding:5px;text-align:center;color:red;font-weight:bold;font-style:italic;margin-bottom:15px;'>Echec lors de la suppression du ficher</div>";
        }
        if (isset($_GET['ok_delete']))
        {
            echo "<div style='border:1px solid green; padding:5px;text-align:center;color:green;font-weight:bold;font-style:italic;margin-bottom:15px;'>Fichier supprim&eacute; avec succ&egrave;s</div>";
        }        
        if(isset($_POST['ok_delete_assoc']))
        {
             echo "<div style='border:1px solid green; padding:5px;text-align:center;color:green;font-weight:bold;font-style:italic;margin-bottom:15px;'>Association supprim&eacute;e avec succ&egrave;s</div>";
        }
        if (isset($_GET['erreur_delete_assoc']))
        {
            echo "<div style='border:1px solid red; padding:5px;text-align:center;color:red;font-weight:bold;font-style:italic;margin-bottom:15px;'>Echec lors de la suppression de l'association</div>";
        }
        if(isset($_GET['ok_categorie_modif']))
        {
           echo "<div style='border:1px solid green; padding:5px;text-align:center;color:green;font-weight:bold;font-style:italic;margin-bottom:15px;'>Association modifi&eacute;e avec succ&egrave;s</div>";
        }
       

       
        $fichiersss_assoc = getFichiersAssociees(); 
        if(count($fichiersss_assoc))
        {
            echo "<span style='font-size : 15px;'><b><i>Associations : </i></b></span><br /><br />";
            echo '<table border=1 cellpadding=4><tr><th>Nom du fichier associ&eacute;</th><th>Cat&eacute;gorie associ&eacute;e</th><th></th></tr>';
            foreach ($fichiersss_assoc as $fichier)
            {
                $categorie = new Category($fichier['id_categorie'], 1);
                echo "<tr><td>" . $fichier['nom_fichier'] . "</td><td>" . $categorie->name . "</td><td><a href='" . $currentIndex . "&edit_association=" . $fichier['id_categorie'] . "-".$fichier['id_fichier']."&token=" . $this->token . "&id_fichier=" . $fichier['id_fichier'] . "'><img src='../img/admin/edit.gif' alt='' title='Modifier' /></a>&nbsp;&nbsp;&nbsp;<a href='" . $currentIndex . "&supprimer_association=" . $fichier['id_categorie'] . "-".$fichier['id_fichier']."&token=" . $this->token . "&id_fichier=" . $fichier['id_fichier'] . "' onclick='if (confirm(\"Voulez-vous vraiment supprimer cette association ?\")) {return true;}else{return false;}'><img src='../img/admin/delete.gif' alt='' title='Supprimer' /></a></td></tr>"; 
            }
            echo '</table>';

            echo '<br /><hr /><br />';
        }
        echo "<span style='font-size : 15px;'><b><i>Fichiers : </i></b></span><br /><br />";
         echo '<a href="' . $currentIndex . '&add_fichier&token=' . $this->token . '"><img src="../img/admin/add.gif" border="0" /> Nouveau fichier</a><br /><br />';
        $fichierssss = getFichiers();
        if(count($fichierssss))
        {
            $chemin_destination = dirname(__FILE__) . '/fiches/';
            $chemin_array = explode('/web/', $chemin_destination);
            $chemin_destination = "/" . $chemin_array[1]; 
            echo '<table border=1 cellpadding=4><tr><th>Nom du fichier</th><th></th></tr>';
            foreach ($fichierssss as $fic)
            {
                echo "<tr><td><a target='_blank' href='" . $chemin_destination . $fic['fichier'] . "'>" . $fic['nom_fichier'] . "</a></td><td><a href='" . $currentIndex . "&edit_fichier=" . $fic['id_fichier'] . "&token=" . $this->token . "'><img src='../img/admin/edit.gif' alt='' title='Modifier' /></a>&nbsp;&nbsp;&nbsp;<a href='" . $currentIndex . "&supprimer_fichier=" . $fic['id_fichier'] . "&token=" . $this->token . "&id_fichier=" . $fic['id_fichier'] . "' onclick='if (confirm(\"Voulez-vous vraiment supprimer ce fichier ?\")) {return true;}else{return false;}'><img src='../img/admin/delete.gif' alt='' title='Supprimer' /></a>&nbsp;&nbsp;&nbsp;<a href='" . $currentIndex . "&add_page&token=" . $this->token . "&id_fichier=" . $fic['id_fichier'] . "'><u>Associer</u></a></td></tr>";
            }
            echo '</table>';
        }
    }

    function display_add_page()
    {
        global $currentIndex, $cookie;
        if (isset($_POST['ajouter_button']))
        {
            foreach ($_POST['produit_table'] as $product)
            {
                $query_insert = "INSERT INTO " . _DB_PREFIX_ . "fichiers_categories_produits SET id_fichier ='" . $_POST['id_fichier'] . "' , id_categorie='" . $_POST['id_category'] . "', id_produit='" . $product . "'";
                Db::getInstance()->Execute($query_insert);
            }
            echo '<script>document.location.href="' . $currentIndex . '&ok_categorie&token=' . $this->token . '";</script>';
        }
        $chemin_destination = dirname(__FILE__) . '/fiches/';
        $chemin_array = explode('/web/', $chemin_destination);
        $chemin_destination = "/" . $chemin_array[1];
        echo '<script type="text/javascript" src="../js/jquery/jquery-3.5.1.min.js"></script>';
        echo '<script type="text/javascript" src="../modules/' . $this->name . '/moduleFicheEnvoiClient.js"></script>';
        echo "<form name='categoriesForm' id='categoriesForm' method='POST'>";
        echo "<input type='hidden' name='id_fichier' value='" . $_GET['id_fichier'] . "' />";
        $fichier = fichier_details($_GET['id_fichier']);
        echo "Fichier : <u><a target='_blank' href='" . $chemin_destination . $fichier['fichier'] . "'>" . $fichier['nom_fichier'] . "</a></u><br /><br />";
        $categories = Category::getCategories(intval($cookie->id_lang), false);
        $id_category = intval(Tools::getValue('id_category'));
        echo 'Cat&eacute;gorie : <select name="id_category" id="id_category" onchange="submit_form_select_category_fiches();">
	            <option value="">Choisir...</option>';
        ob_start();
        $id_categorie_root = intval(Configuration::get('PS_ROOT_CATEGORY'));
        Category::recurseCategory($categories, $categories[0][$id_categorie_root], 1, $id_category);
        $buffer = ob_get_contents();
        ob_clean();
        ob_end_flush();
        echo $buffer;
       
        echo '</select>';
        if ($id_category != "" && intval($id_category) != 0)
        {
            $produits = $this->getProducts(1, $id_category, $this->get_ids());
            if (count($produits))
            {
                echo "<br /><br /><br /><input type='button' value='Tout cocher' onclick='check_all_product_fiches()' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Tout d&eacute;cocher' onclick='uncheck_all_product_fiches()' /><br /><br /><table>";
                foreach ($produits as $prod)
                {
                    echo "<tr><td><input class='check_all_product_fiches' type='checkbox' name='produit_table[]' id='" . $prod['id_product'] . "' value='" . $prod['id_product'] . "'></td><td style='white-space:nowrap'><label for='" . $prod['id_product'] . "' style='text-align:left'>&nbsp;&nbsp;&nbsp;" . $prod['name'] . "</label></td></tr>";
                }
                echo "</table>";
                echo "<br /><input type='button' value='Tout cocher' onclick='check_all_product_fiches()' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Tout d&eacute;cocher' onclick='uncheck_all_product_fiches()' />"; 
            }
        }
        echo "<br /><br /><center><input type='button' value='Retour' onclick='document.location.href=\"" . $currentIndex . "&token=" . $this->token . "\";'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' value='Ajouter' name='ajouter_button' /></center>";
        echo "</form>";
    }

    private function getProducts($id_lang, $id_categorie = '', $id_produits = array())
    {
        $queryyyyy = 'SELECT p.`id_product`, p.reference, pl.`name`, p.quantity as quantity
                    FROM `' . _DB_PREFIX_ . 'product` p
                    LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`
                    ' . (!empty($id_categorie) ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`' : '') . '
                    WHERE pl.`id_lang` = ' . intval($id_lang) . '
                    ' . (!empty($id_categorie) ? ' AND cp.id_category = ' . $id_categorie : '') . (!empty($id_produits) ? ' AND p.id_product NOT IN ( ' . implode(',', $id_produits) . ')' : '') . '
                   AND p.active=1 ORDER BY pl.`name`';
        return Db::getInstance()->ExecuteS($queryyyyy);
    }

    private function get_ids($current_cat = 0)
    {
        $query = "SELECT DISTINCT id_produit as id_produit FROM " . _DB_PREFIX_ . "fichiers_categories_produits";
        if(intval($current_cat) != 0)
        {
            $query .= " WHERE id_categorie != '".$current_cat."'";
        }
        $result = Db::getInstance()->ExecuteS($query);
        $array_ids = array();
        foreach ($result as $res)
        {
            $array_ids[] = $res['id_produit'];
        }
        return $array_ids;
    }
    
    public function supprimer_fichier($id_fichier)
    {
        $currentIndex = '/admin123/index.php?controller=FichesEnvoiClientTab';
        if(intval($id_fichier))
        {
            $chemin_destination = dirname(__FILE__) . '/fiches/';
            $fichier = fichier_details($id_fichier);
            unlink($chemin_destination . $fichier['fichier']);
            $query_delete1 = "DELETE FROM " . _DB_PREFIX_ . "fichiers_infos WHERE id_fichier = '".$id_fichier."';";
            if(Db::getInstance()->Execute($query_delete1))
            {
                $query_delete2 = "DELETE FROM " . _DB_PREFIX_ . "fichiers_categories_produits WHERE id_fichier = '".$id_fichier."';";
                if(Db::getInstance()->Execute($query_delete2))
                {
                     echo '<script>document.location.href="' . $currentIndex . '&ok_delete&token=' . $this->token . '";</script>';
                }
                else
                {
                    echo '<script>document.location.href="' . $currentIndex . '&erreur_delete&token=' . $this->token . '";</script>';
                }
            }
            else
            {
                echo '<script>document.location.href="' . $currentIndex . '&erreur_delete&token=' . $this->token . '";</script>';
            }
        }
    }
    
    public function supprimer_association($assoc)
    {
        $currentIndex = '/admin123/index.php?controller=FichesEnvoiClientTab';
        if(trim($assoc) != "" && trim($assoc) != "-")
        {
            $res = explode('-', $assoc);
            $id_fichier = $res[1];
            $id_categorie = $res[0];
            $query_delete = "DELETE FROM " . _DB_PREFIX_ . "fichiers_categories_produits WHERE id_fichier = '".$id_fichier."' AND id_categorie = '".$id_categorie."';";
        }
        if(Db::getInstance()->Execute($query_delete))
        {
             echo '<script>document.location.href="' . $currentIndex . '&ok_delete_assoc&token=' . $this->token . '";</script>';
        }
        else
        {
            echo '<script>document.location.href="' . $currentIndex . '&erreur_delete_assoc&token=' . $this->token . '";</script>';
        }
    }
    
    public function edit_association($assoc)
    {
        $currentIndex = '/admin123/index.php?controller=FichesEnvoiClientTab';
		echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>';
        echo '<script type="text/javascript" src="../modules/' . $this->name . '/moduleFicheEnvoiClient.js"></script>';
        if(trim($assoc) != "" && trim($assoc) != "-")
        {
            if (isset($_POST['modifier_button']))
            {
                $query_delete = "DELETE FROM " . _DB_PREFIX_ . "fichiers_categories_produits WHERE id_fichier = '".$_POST['id_fichier']."' AND id_categorie = '".$_POST['id_category']."';";
                if( Db::getInstance()->Execute($query_delete))
                {
                    foreach ($_POST['produit_table'] as $product)
                    {
                        $query_insert = "INSERT INTO " . _DB_PREFIX_ . "fichiers_categories_produits SET id_fichier ='" . $_POST['id_fichier'] . "' , id_categorie='" . $_POST['id_category'] . "', id_produit='" . $product . "'";
                        Db::getInstance()->Execute($query_insert);
                    }
                    echo '<script>document.location.href="' . $currentIndex . '&ok_categorie_modif&token=' . $this->token . '";</script>';
                }
            }
            
            
            
            
            $res = explode('-', $assoc);
            $id_fichier = $res[1];
            $id_category = $res[0];        
            
            $chemin_destination = dirname(__FILE__) . '/fiches/';
            $chemin_array = explode('/web/', $chemin_destination);
            $chemin_destination = "/" . $chemin_array[1];
           
            echo "<form name='categoriesForm' id='categoriesForm' method='POST'>";
            echo "<input type='hidden' name='id_fichier' value='" . $id_fichier . "' />";
            $fichier = fichier_details($id_fichier);
            echo "Fichier : <u><a target='_blank' href='" . $chemin_destination . $fichier['fichier'] . "'>" . $fichier['nom_fichier'] . "</a></u><br /><br />";
            
            echo "<input type='hidden' name='id_category' id='id_category' value='".$id_category."' />";
            $categ = new Category($id_category, 1);
            echo "Cat&eacute;gorie : ".$categ->name;
            $id_products = get_elements_fichier_categorie($id_fichier, $id_category);
            if ($id_category != "" && intval($id_category) != 0)           
            {
                
                $produits = $this->getProducts(1, $id_category,$this->get_ids($id_category));
                if (count($produits))
                {
                    echo "<br /><br /><br /><input type='button' value='Tout cocher' onclick='check_all_product_fiches()' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Tout d&eacute;cocher' onclick='uncheck_all_product_fiches()' /><br /><br /><table>";
                    foreach ($produits as $prod)
                    {
                        $checked = "";
                        if(in_array($prod['id_product'], $id_products))
                        {
                            $checked = " checked ";
                        }
                        echo "<tr><td><input class='check_all_product_fiches' type='checkbox' name='produit_table[]' id='" . $prod['id_product'] . "' value='" . $prod['id_product'] . "' ".$checked."></td><td style='white-space:nowrap'><label for='" . $prod['id_product'] . "' style='text-align:left'>&nbsp;&nbsp;&nbsp;" . $prod['name'] . "</label></td></tr>";
                    }
                    echo "</table><br /><input type='button' value='Tout cocher' onclick='check_all_product_fiches()' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Tout d&eacute;cocher' onclick='uncheck_all_product_fiches()' />";
                }
            }
            echo "<br /><br /><center><input type='button' value='Retour' onclick='document.location.href=\"" . $currentIndex . "&token=" . $this->token . "\";'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' value='Modifier l&apos;association' name='modifier_button' /></center>";
            echo "</form>";
        }
        else
        {
            echo '<script>document.location.href="' . $currentIndex . '&token=' . $this->token . '";</script>';
        }
    }

}

function getFichiersAssociees()
{
    $query = "SELECT DISTINCT fi.id_fichier, fi.nom_fichier, fi.fichier, fcp.id_categorie FROM " . _DB_PREFIX_ . "fichiers_infos fi, ". _DB_PREFIX_ . "fichiers_categories_produits fcp WHERE fcp.id_fichier = fi.id_fichier ORDER BY fi.nom_fichier;";
    return Db::getInstance()->ExecuteS($query);
}

function getFichiers()
{
    $query = "SELECT fi.id_fichier, fi.nom_fichier, fi.fichier FROM " . _DB_PREFIX_ . "fichiers_infos fi ORDER BY fi.nom_fichier;";
    return Db::getInstance()->ExecuteS($query);
}

function fichier_details($id_fichier)
{
    $query = "SELECT * FROM " . _DB_PREFIX_ . "fichiers_infos WHERE id_fichier = '" . $id_fichier . "';";

    $result = Db::getInstance()->ExecuteS($query);
    return $result[0];
}

function get_elements_fichier_categorie($id_fichier, $id_categorie)
{
    $query = "SELECT DISTINCT(id_produit) as id_produit FROM " . _DB_PREFIX_ . "fichiers_categories_produits WHERE id_fichier = '" . $id_fichier . "' AND id_categorie='".$id_categorie."';";
    $result = Db::getInstance()->ExecuteS($query);
    $return_array = array();
    foreach($result as $res)
    {
        $return_array[] = $res['id_produit'];
    }
    return $return_array;
}

function clear_nom_fichier($nom_fichier)
{
     $a = array("'",' ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
        'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
        'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
        'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', '?', '?', '?', '?', '?', '?', '?', '?', '?',
        '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
        '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
        '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
        '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '¼',
        '½', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '¦', '¨', '?', '?', '?', 
        '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', 
        '?', '¾', '?', '?', '?', '?', '´', '¸', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?',
        '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');

    $b = array("_", '_','A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O',
        'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c',
        'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
        'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
        'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
        'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
        'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o',
        'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
        's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W',
        'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
        'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');



    $nom_fichier = str_replace($a, $b, $nom_fichier);
    return $nom_fichier;
}

?>