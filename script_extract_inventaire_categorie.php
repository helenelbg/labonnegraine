<?php
include_once('config/config.inc.php');
include('init.php');
  $dbhost = "92.243.24.83";        // nom du serveur
  $usebdd = "lbg";        // nom de la base de donn�es
  $user = "lbg";          // nom de l'utilisateur de la base de donn�es
  $password = "bgtlR-2d";       // mot de passe de l'utilisateur de la base de donn�es

  if (! @$connexion = mysqli_connect($dbhost, $user, $password, $usebdd))
  {
    print("Impossible de se connecter au serveur " . $dbhost);
    print("<br>");
    exit;
  }
  // Selection de la BDD
  /*$categoryObj = new Category((int)$_GET['categorie']);
  if(!$categoryObj->id)
  {
      die('erreur categorie : '.$_GET['categorie']);
  }*/

  //mysql_select_db ($usebdd, $connexion) or die ("probleme de connexion base");

  $id_category_parent = $_GET['categorie'];
  $array_categories = array();

$explode = explode(',',$id_category_parent);
foreach($explode as $exp)
{
  $array_categories[] = $exp;
}

  //$array_categories[] = $id_category_parent;
  $query_parent = "SELECT id_category FROM ps_category WHERE id_parent IN (".$id_category_parent.") AND active=1";
  error_log($query_parent);
  $result_parent = mysqli_query($connexion, $query_parent);
  while($datas_parent = mysqli_fetch_array($result_parent))
  {
  	   //Parent de niveau 1
  	   $array_categories[] = $datas_parent['id_category'];
  	   //Niveau 2
  	   $query_niv2 = "SELECT id_category FROM ps_category WHERE id_parent='".$datas_parent['id_category']."' AND active=1";
  	   $result_parentniv2 = mysqli_query($connexion, $query_niv2);
  	   while($datas_parentniv2 = mysqli_fetch_array($result_parentniv2))
  	   {
	   	   //Parent de niveau 2
       	   $array_categories[] = $datas_parentniv2['id_category'];
	  	   //Niveau 3
	  	   $query_niv3 = "SELECT id_category FROM ps_category WHERE id_parent='".$datas_parentniv2['id_category']."' AND active=1";
	  	   $result_parentniv3 = mysqli_query($connexion, $query_niv3);
	  	   while($datas_parentniv3 = mysqli_fetch_array($result_parentniv3))
	  	   {
	       	   $array_categories[] = $datas_parentniv3['id_category'];

	       	    //Niveau 4
	  	  		$query_niv4 = "SELECT id_category FROM ps_category WHERE id_parent='".$datas_parentniv3['id_category']."' AND active=1";
	  	   		$result_parentniv3 = mysqli_query($connexion, $query_niv4);
	  	   		while($datas_parentniv4 = mysqli_fetch_array($result_parentniv4))
	  	   		{
	       	   		$array_categories[] = $datas_parentniv4['id_category'];
		   		}
		   }
	   }
  }

  header("Content-type: application/vnd.ms-excel;charset=utf-8");
  header("Content-disposition: attachment; filename=\"inventaire.csv\"");
  //R�cup�ration des produits
  $req_c = 'SELECT DISTINCT id_product FROM ps_category_product WHERE id_category IN ('.implode(",",$array_categories).')';
//  $req_p = 'SELECT p.id_product, pl.name as nom, p.location as emplacement, sa.quantity as stock_disponible FROM ps_product_attribute p, ps_product_lang pl, ps_stock_available sa WHERE p.id_product = pl.id_product AND pl.id_lang = 2 AND p.id_product_attribute = sa.id_product AND p.active=1 AND p.id_product IN('.$req_c.') ORDER BY pl.name';

  $req_p = 'SELECT * FROM ps_product_attribute  pa WHERE pa.id_product  IN('.$req_c.');';
  $result_p = mysqli_query($connexion, $req_p);
 // echo utf8_encode("reference;id produit;id declinaison;nom;emplacement;quantite;quantite disponible\n");
  echo utf8_encode("reference;id produit;id declinaison;nom;quantite\n");
  while($datas = mysqli_fetch_array($result_p))
  {
      $combination = new Combination($datas['id_product_attribute']);
      $product = new Product($combination->id_product);
      $attributes = $combination->getAttributesName(1);
      $total_attributes = count($attributes);
      $name = $product->name[1].' ';
      foreach($attributes as $key => $attribute)
      {
          $sep = ', ';
          if($key+1 == $total_attributes)
          {
              $sep = '';
          }
          $name .= $attribute['name'].$sep;
      }
      $id_stock_available =  StockAvailable::getStockAvailableIdByProductId($combination->id_product, $combination->id);
      $stockAvailable = new StockAvailable($id_stock_available);
     /*echo '<br><br>$combination <br> [ <br><pre>';
      print_r($combination);
      echo '</pre><br>]<br>';*/
//die;

      echo ($product->reference.';'
      . $combination->id_product .';'
      . $combination->id .';'
               . $name .';'
               //. $combination->location.';'
               //. $combination->quantity.';'
               . $stockAvailable->quantity
               . "\n");

//      echo utf8_decode($datas['id_product'].";".$datas['nom'].";".$datas['emplacement']."\n");
  }

  /*echo "<pre>";
  print_r($array_categories);
  echo "</pre>";*/




?>
