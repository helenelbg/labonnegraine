<?php

class StatsStocksInventaire extends ModuleGraph
{

  private $_html = '';
  private $_query = '';
  private $_option = 0;
  private $_id_product = 0;

  function __construct()
  {
    $this->name = 'statsstocksinventaire';
    $this->tab = 'Stats';
    $this->version = 1.0;

    parent::__construct();

    $this->displayName = 'Etat des stocks / Inventaire';
    $this->description = '';
  }

  public function install()
  {
    return (parent::install() AND $this->registerHook('displayAdminStatsModules'));
  }

  public function getTotalBought($id_product)
  {
    $dateBetween = ModuleGraph::getDateBetween();
    $result = Db::getInstance()->getRow('
      SELECT SUM(od.`product_quantity`) AS total
      FROM `' . _DB_PREFIX_ . 'order_detail` od
      LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.`id_order` = od.`id_order`
      WHERE od.`product_id` = ' . intval($id_product) . '
      AND o.valid = 1
      AND o.`date_add` BETWEEN ' . $dateBetween . '');
    return isset($result['total']) ? $result['total'] : 0;
  }

  public function getTotalViewed($id_product)
  {
    $dateBetween = ModuleGraph::getDateBetween();
    $result = Db::getInstance()->getRow('
      SELECT SUM(pv.`counter`) AS total
      FROM `' . _DB_PREFIX_ . 'page_viewed` pv
      LEFT JOIN `' . _DB_PREFIX_ . 'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
      LEFT JOIN `' . _DB_PREFIX_ . 'page` p ON pv.`id_page` = p.`id_page`
      LEFT JOIN `' . _DB_PREFIX_ . 'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
      WHERE pt.`name` = \'product.php\'
      AND p.`id_object` = ' . intval($id_product) . '
      AND dr.`time_start` BETWEEN ' . $dateBetween . '
      AND dr.`time_end` BETWEEN ' . $dateBetween . '');
    return isset($result['total']) ? $result['total'] : 0;
  }

  private function getProducts($id_lang)
  {
    return Db::getInstance()->ExecuteS('
      SELECT p.`id_product`, p.reference, pl.`name`, (p.quantity + IFNULL((SELECT SUM(sa.quantity) 
	  FROM ' . _DB_PREFIX_ . 'stock_available sa 
	  WHERE sa.id_product = p.id_product GROUP BY sa.id_product), 0)) as quantity
      FROM `' . _DB_PREFIX_ . 'product` p
      LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`
      ' . (Tools::getValue('id_category') ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`' : '') . '
      WHERE pl.`id_lang` = ' . intval($id_lang) . '
      ' . (Tools::getValue('id_category') ? 'AND cp.id_category = ' . intval(Tools::getValue('id_category')) : '') . '
      AND p.active=1 ORDER BY p.`reference`');
  }

  private function getSales($id_product, $id_lang)
  {
    return Db::getInstance()->ExecuteS('
      SELECT o.date_add, o.id_order, o.id_customer, od.product_quantity, (od.product_price * od.product_quantity) as total, od.tax_name, od.product_name
      FROM `' . _DB_PREFIX_ . 'orders` o
      LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON o.id_order = od.id_order
      WHERE o.date_add BETWEEN ' . $this->getDate() . ' AND o.valid = 1
      AND od.product_id = ' . intval($id_product));
  }

  public function hookDisplayAdminStatsModules($params)
  {
    global $cookie, $currentIndex;

    if(!Tools::isSubmit('export_stock'))
    {
      echo "<style>.detail_lot{display : none}</style>";
    }
    if (isset($_POST['maj']) && $_POST['maj'] == 'ok' ) {
      reset($_POST);
	  
      //while (list($cle, $valeur) = each($_POST))
	  foreach($_POST as $cle => $valeur)
      {
        $exp = explode('#', $cle);
        if (!empty($exp[1]) && (!empty($valeur) || $valeur == '0'))
        {
          if (!empty($exp[1]) && ($exp[1] == 'reassort') && !empty($valeur))
          {
            $req_ins = Db::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'reassort (date, id_product, id_product_attribute, valeur) VALUES ("' . date('YmdHi') . '", "' . $exp[0] . '", "0", "' . $valeur . '");');
          }
          elseif (!empty($exp[1]) && ($exp[1] == 'alerte') && !empty($valeur))
          {
            //On regarde s'il y a d�ja une alerte
            $req_exist = Db::getInstance()->ExecuteS('SELECT count(*) as cpt FROM ' . _DB_PREFIX_ . 'alerte WHERE id_product=' . $exp[0] . ';');
            if ($req_exist[0]['cpt'] == 0)
            {
              $req_ins = Db::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'alerte (id_product, valeur) VALUES ("' . $exp[0] . '", "' . $valeur . '");');
            }
            else
            {
              $req_ins = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'alerte SET valeur="' . $valeur . '" WHERE id_product="' . $exp[0] . '";');
            }
          }
          else
          {
            $req_ins = Db::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'inventaire (date, id_product, id_product_attribute, valeur) VALUES ("' . date('YmdHis') . '", "' . $exp[0] . '", "' . $exp[1] . '", "' . $valeur . '");');
            $nb_quantite_restant = $valeur;
            $def = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product_attribute = "' . $exp[1] . '" AND id_product = "' . $exp[0] . '";');
            if ( $def[0]['default_on'] == 1 )
            {
              $nb_quantite_restant -= 3;
            }
            if ( $nb_quantite_restant < 0 )
            {
                $nb_quantite_restant = 0;
            }
            $sqlAppro = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'stock_available SET quantity = "'.$nb_quantite_restant.'" WHERE id_shop = 1 AND id_product = "'.$exp[0].'" AND id_product_attribute = "'.$exp[1].'";');
            //$sqlAppro = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_attribute SET quantity = "'.$nb_quantite_restant.'" WHERE id_product = "'.$exp[0].'" AND id_product_attribute = "'.$exp[1].'";');
            $totalQte = 0;
            $sync = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_shop = 1 AND id_product_attribute <> "0" AND id_product = "' . $exp[0] . '";');
            foreach ($sync as $one)
            {
              $totalQte += $one['quantity'];
            }
            $sqlAppro = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'stock_available SET quantity = "'.$totalQte.'" WHERE id_shop = 1 AND id_product = "'.$exp[0].'" AND id_product_attribute = "0";');
            $sqlAppro = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product SET quantity = "'.$totalQte.'" WHERE id_product = "'.$exp[0].'";');
          }
        }
      }

    }
    elseif(isset($_POST['test_lot']) && $_POST['test_lot'] == 'ok') {
      $id_product = trim($_POST['id_product']);
      $fournisseur = trim($_POST['fournis']);
      $num_lot_org = trim($_POST['lot_org']);
      $date_appro = trim($_POST['date_appro']);
      $num_lot_LBG = trim($_POST['lot_LBG']);
      $date_germ = trim($_POST['date_germ']);
      $percent_germ = trim($_POST['pourcent_germ']);
      $comm = nl2br(trim(addslashes ($_POST['comm'])));
      $quantite = trim($_POST['quantite']);
      $graine_gramme = trim($_POST['graine_gramme']);



      if (!isset($_POST['numero_lot']) || trim($_POST['numero_lot']) == "")
      {
        $query_insert_lot = "INSERT INTO  " . _DB_PREFIX_ . "inventaire_lots (id_product, fournisseur, numero_lot_origine, date_approvisionnement, numero_lot_LBG,date_test_germination, percent_germination , commentaire, quantite, graine_gramme) VALUES ('" . $id_product . "', '" . addslashes($fournisseur) . "','" . $num_lot_org . "', '" . $date_appro . "', '" . $num_lot_LBG . "', '" . $date_germ . "', '" . $percent_germ . "', '" . $comm . "', '" . $quantite . "', '" . $graine_gramme . "');";
        Db::getInstance()->Execute($query_insert_lot);
        $id_lot = Db::getInstance()->Insert_ID();
        $req_ins = Db::getInstance()->Execute('INSERT INTO ' . _DB_PREFIX_ . 'reassort (date, id_product, id_product_attribute, valeur, id_lot) VALUES ("' . date('YmdHi') . '", "' . $id_product . '", "0", "' . $quantite . '", "' . $id_lot . '");');

        /******** test fournisseur *******/
            if (isset($_POST['origine_test']) && !empty($_POST['origine_test'])) {
                    $origine_test = $_POST['origine_test'];
            }
            else{
                    $origine_test = 'Frns';
            }
                // champs vides pour test fournisseur
                $date_debut_test = '0000-00-00';
                $date_etape_2 = '0000-00-00';
                $resultat_etape_2 = '0';
                $date_etape_3 = '0000-00-00';
                $resultat_etape_3 = '0';


        $query_insert_test_frns = "INSERT INTO `AW_test_lots` (`id_lot`, `date_debut_semis`, `date_etape_1`, `resultat_etape_1`, `date_etape_2`, `resultat_etape_2`, `date_etape_3`, `resultat_etape_3`, `date_fin_test`, `commentaire`, `origine_test`) VALUES ('" . $id_lot . "','" . $date_debut_test . "', '" . $date_germ. "', '" . $percent_germ. "', '" . $date_etape_2 . "', '" . $resultat_etape_2 . "', '" . $date_etape_3 . "', '" . $resultat_etape_3 . "', '" . '0000-00-00' . "', '" . $comm . "', '" . $origine_test . "')";
        Db::getInstance()->Execute($query_insert_test_frns);
      }
      else
      {
        $query_insert_lot = "UPDATE " . _DB_PREFIX_ . "inventaire_lots SET fournisseur='" . $fournisseur . "', numero_lot_origine='" . $num_lot_org . "', date_approvisionnement='" . $date_appro . "', numero_lot_LBG='" . $num_lot_LBG . "',date_test_germination='" . $date_germ . "', percent_germination='" . $percent_germ . "' , commentaire='" . $comm . "', quantite='" . $quantite . "', graine_gramme='" . $graine_gramme . "' WHERE id_inventaire_lots='" . trim($_POST['numero_lot']) . "'";
        Db::getInstance()->Execute($query_insert_lot);



      }


    }
    elseif(isset($_POST['suppr_lot']) && $_POST['suppr_lot'] == 'ok' && !empty($_POST['id_lot'])) {
      $lot = trim($_POST['id_lot']);
      $query_suppr_lot = "DELETE FROM " . _DB_PREFIX_ . "inventaire_lots WHERE id_inventaire_lots = '" . $lot . "';";
      Db::getInstance()->Execute($query_suppr_lot);
    }

    $id_category = intval(Tools::getValue('id_category'));

    $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

    $this->_html =
            '<style>.non_modifiable: {border:none!important;}</style>'.
            '<fieldset class="width3"><legend><img src="../modules/' . $this->name . '/logo.gif" /> ' . $this->displayName . '</legend>';

    $this->_html .= '
    <script type="text/javascript" src="'._MODULE_DIR_. $this->name . '/modulestatsstocksinventaire.js"></script>
    <div style="text-align:right"><input type="button" class="button" value="Export valeurs th&eacute;orique de toutes les cat&eacute;gories" onclick="window.open(\''._MODULE_DIR_. $this->name . '/export_valeur_stocks.php\')" /></div><div class="clear space"></div>
    <div style="text-align:right">
      <form action="'.$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'" method="POST"><input type="submit" class="button" name="export_stock" value="Export des valeurs theorique et stocks Site" />
        <inpur type="hidden" name="id_category" value="'.Tools::getValue('id_category').'">
        </form>
      </div>
      <div class="clear space"></div>
      <label>Choisir une catégorie</label>
      <div class="margin-form">
        <form action="" method="post" id="categoriesForm" name="categoriesForm">
         <select name="id_category" id="id_category" onchange="submit_form_select_category();">
          <option value="">Choisir...</option>
          <option value="0">Toutes</option>';
        

        $id_categorie_root = intval(Configuration::get('PS_ROOT_CATEGORY'));
		$category = new Category($id_categorie_root, Context::getContext()->language->id);
		$categories = $category->recurseLiteCategTree(0);

		$this->displayCatOption($categories['children']);


          $this->_html .= '
        </select>
      </form>
    </div>';
    if ((isset($_POST['id_category']) &&  $_POST['id_category'] != '') || (isset($_GET['id_category']) && $_GET['id_category'] != ''))
    {
      $this->_html .= '<div style="text-align:right"><input type="button" class="button" value="Export valeurs th&eacute;orique de la cat&eacute;gorie courante"  onclick="window.open(\''._MODULE_DIR_. $this->name . '/export_valeur_stocks.php?id_category=' . $id_category . '\')"></div>';
    }
    $this->_html .= '<div class="clear space"></div>';



    if ((isset($_POST['id_category']) &&  $_POST['id_category'] != '') || (isset($_GET['id_category']) && $_GET['id_category'] != ''))
    {
      $str_export = "nom;Date Dernier Inventaire;Stock Dernier Inventaire;Stock Theorique;Stock Site\n";
      $this->_html .= '<div>
      <script type="text/javascript">
        $(function() {
         $(".datepickerLot").datepicker({
          dateFormat:"yy-mm-dd",
          prevText:"",
          nextText:""});
        });
      </script>
      <form name="formInventaire" action="" method="post">
       <table class="table" border="0" cellspacing="0" cellspacing="0" width="100%">
         <thead>
          <tr>
           <th>Référence</th>
           <th>Nom</th>
           <th>Date Dernier Inv.</th>
           <th></th>
           <th style="display:none;">Stock Théor.</th>
           <th>Stock Réel</th>
           <th>Stock Site</th>
           <th>Stock en attente</th>
           ';
            //<th>Réassort (en g.)</th>
           $this->_html .= '<th>Alerte (en g.)</th>

         </tr>
       </thead><tbody>';
       $total_theorique_valeur = 0;
       foreach ($this->getProducts($cookie->id_lang) AS $product)
       {


        $id_stock_presta_p = StockAvailable::getStockAvailableIdByProductId($product['id_product']);
        $stockAvailableProduct = new StockAvailable($id_stock_presta_p);

        /* suppression des stocks de plants */
        $rangee_attrib_tmp = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = "' . $product['id_product'] . '";');
        $nb_graine = 0;
        foreach ($rangee_attrib_tmp AS $attrib_tmp)
        {
          $rangee_comb_tmp = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute_combination WHERE id_product_attribute = "' . $attrib_tmp['id_product_attribute'] . '";');
          $traiter_tmp = false;
          foreach ($rangee_comb_tmp AS $comb_tmp)
          {
            $dec = Db::getInstance()->ExecuteS('SELECT name, id_attribute FROM ' . _DB_PREFIX_ . 'attribute_lang WHERE id_attribute = "' . $comb_tmp['id_attribute'] . '" AND id_lang = 1 LIMIT 0,1;');
            if ( $dec[0]['id_attribute'] == 10512 || $dec[0]['id_attribute'] == 10513 )
            {
              $traiter_tmp = true;
            }
          }
          if ( $traiter_tmp == false )
          {
            continue;
          }
          $id_stock_presta_tmp = StockAvailable::getStockAvailableIdByProductId($product['id_product'], $attrib_tmp['id_product_attribute']);
          $stockAvailable_tmp = new StockAvailable($id_stock_presta_tmp);

          $stockAvailableProduct->quantity -= $stockAvailable_tmp->quantity;
        }

        $ale = Db::getInstance()->ExecuteS('SELECT valeur FROM ' . _DB_PREFIX_ . 'alerte WHERE id_product = "' . $product['id_product'] . '" ORDER BY id DESC LIMIT 0,1;');
        
		// Produit
		$this->_html .= '<tr><td>' . $product['reference'] . '</td><td colspan="3"><font style="color: #000000">' . $product['name'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a style="color:blue" href="javascript:afficher_lot(' . $product['id_product'] . ')">Informations lots</a></font></td><td>&nbsp;</td><td>'.$stockAvailableProduct->quantity.'</td><td></td><td><input type="text" name="' . $product['id_product'] . '#alerte" value="' . @$ale[0]['valeur'] . '" style="width:50px;"/></td></tr>';
        
		$this->_html .= '<tr id="info_lots_product_' . $product['id_product'] . '" class="detail_lot"><td colspan="7">
        <table id="lot_num_" class="lot_inv table_detail">
          <thead>
            <th>Fournisseur</th>
            <th>N&deg; lot origine</th>
            <th>Date appro</th>
            <th width="100px">Graines / Grammes</th>
            <th>Quantité appro</th>
            <th>N&deg; lot LBG</th>
            <th>Origine test Frns / LBG</th>
            <th>Date fin du test de germinaton</th>
            <th>Pourcentage germination</th>
            <th colspan="3">Commentaires</th>
            <th></th>
          </thead>
          <tbody>';
            $refs_fournisseur_inventaires = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'inventaire_lots WHERE id_product = "' . $product['id_product'] . '" ORDER BY date_approvisionnement DESC;');
            foreach ($refs_fournisseur_inventaires as $ref_fourn_inv)
            {
                /**** dernier test ***/
                $result_origine_test_lot = Db::getInstance()->ExecuteS('SELECT commentaire, origine_test, date_etape_1,resultat_etape_1,date_etape_2,resultat_etape_2,date_etape_3,resultat_etape_3 FROM AW_test_lots WHERE id_lot = "' . $ref_fourn_inv['id_inventaire_lots'] . '" ORDER BY id DESC LIMIT 1');
				
				$date_fin_test="";
				$resultat_test="0";
				$origine_test_lot = "";
				$commentaire_test_lot = "";
        /*if ( $ref_fourn_inv == 3612)
        {*/
         /* echo $ref_fourn_inv['id_inventaire_lots'] .' : <pre>';
          print_r($result_origine_test_lot);
          echo '</pre><hr>';*/
        //}
				if(count($result_origine_test_lot)) {
                    if($result_origine_test_lot[0]['date_etape_3']!='0000-00-00')
                            $date_fin_test=substr($result_origine_test_lot[0]['date_etape_3'], 8, 2).'/'.substr($result_origine_test_lot[0]['date_etape_3'], 5, 2).'/'.substr($result_origine_test_lot[0]['date_etape_3'], 0, 4);
                    elseif($result_origine_test_lot[0]['date_etape_2']!='0000-00-00')
                            $date_fin_test=substr($result_origine_test_lot[0]['date_etape_2'], 8, 2).'/'.substr($result_origine_test_lot[0]['date_etape_2'], 5, 2).'/'.substr($result_origine_test_lot[0]['date_etape_2'], 0, 4);
                    elseif($result_origine_test_lot[0]['date_etape_1']!='0000-00-00')
                             $date_fin_test=substr($result_origine_test_lot[0]['date_etape_1'], 8, 2).'/'.substr($result_origine_test_lot[0]['date_etape_1'], 5, 2).'/'.substr($result_origine_test_lot[0]['date_etape_1'], 0, 4);
          
                    if($result_origine_test_lot[0]['resultat_etape_3']!=0)
                            $resultat_test=$result_origine_test_lot[0]['resultat_etape_3'];
                    elseif($result_origine_test_lot[0]['resultat_etape_2']!=0)
                            $resultat_test=$result_origine_test_lot[0]['resultat_etape_2'];
                    elseif($result_origine_test_lot[0]['resultat_etape_1']!=0)
                             $resultat_test=$result_origine_test_lot[0]['resultat_etape_1'];    

					if($result_origine_test_lot[0]['origine_test']!="") {
						$origine_test_lot=$result_origine_test_lot[0]['origine_test'];
					}
					
					$commentaire_test_lot = $result_origine_test_lot[0]['commentaire'];
							 
				}

				
                
                

                $date_germ = $ref_fourn_inv['date_test_germination'];
                $array_date_germ = explode('-', $date_germ);
                $date_germ = @$array_date_germ[2] . "/" . @$array_date_germ[1] . "/" . @$array_date_germ[0];

                if(count($result_origine_test_lot)==0)
                {
                    $resultat_test=$ref_fourn_inv['percent_germination'];
                    $date_fin_test= $date_germ;
                    $commentaire_test_lot=$ref_fourn_inv['commentaire'];
                }
                if(count($result_origine_test_lot)>0)
                    $origine_test_lot=$result_origine_test_lot[0]["origine_test"];
                else  $origine_test_lot="Frns";

              $date_appro = $ref_fourn_inv['date_approvisionnement'];
              $array_date_appro = explode('-', $date_appro);
              $date_appro = @$array_date_appro[2] . "/" . @$array_date_appro[1] . "/" . @$array_date_appro[0];


              $qty_lot = $ref_fourn_inv['quantite'];
              $graine_gramme = $ref_fourn_inv['graine_gramme'];

              $this->_html .='
              <tr>
                <td id="ref_fourn_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $ref_fourn_inv['fournisseur'] . '</td>
                <td align="left" id="num_lot_org_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $ref_fourn_inv['numero_lot_origine'] . '</td>
                <td align="left" id="date_appro_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $date_appro . '</td>
                <td align="left" id="graine_gramme_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $graine_gramme . '</td>
                <td align="left" id="quantite_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $qty_lot . '</td>
                <td id="num_lot_LBG_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $ref_fourn_inv['numero_lot_LBG'] . '</td>
                <td id="remplir">'.$origine_test_lot.'</td>
                <td align="left" id="date_germ_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $date_fin_test . '</td>
                <td align="left" id="germ_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $resultat_test . '%</td>
                <td colspan="3" align="left" id="commentaire_lot_' . $ref_fourn_inv['id_inventaire_lots'] . '">' . $commentaire_test_lot . '</td>';

                $test_lots = Db::getInstance()->ExecuteS('SELECT * FROM AW_test_lots WHERE id_lot = "' . $ref_fourn_inv['id_inventaire_lots'] . '"');
                $this->_html .='
                <td>
                  <div class="conteneur_lb" id="conteneur_lb_'. $ref_fourn_inv['id_inventaire_lots'] .'">
                    <div class="lightbox" style="text-align:center">
                      <h2 class="margin0" style="text-align: center;padding: 10px;border-bottom: 1px solid black;">Liste des tests - '.$product['name'].' - Lot n&deg;'.$ref_fourn_inv['numero_lot_LBG'].'</h2>
                      <table width="900px" class="center">
                        <thead>
                          <tr>
                            <th colspan="1" style="width:20px;"></th>
                            <th colspan="1" style="width:90px;">Date fin<br />Germination</th>
                            <th colspan="1" style="width:90px;">%<br />Germination</th>
                            <th colspan="5" style="width:700px;">Commentaire</th>
                          </tr>
                        </thead>
                        <tbody>';

						foreach ($test_lots as $test_lot) {
							if (!empty($test_lot['resultat_etape_3'])) {
								$pourc_germ = $test_lot['resultat_etape_3'];
                            }
							elseif (!empty($test_lot['resultat_etape_2'])) {
								$pourc_germ = $test_lot['resultat_etape_2'];
                            }
                            else{
								$pourc_germ = $test_lot['resultat_etape_1'];
                            }
							
							if($test_lot['resultat_etape_3']!=0)
								$date_fin_test=$test_lot['date_etape_3'];
							elseif($test_lot['resultat_etape_2']!=0)
								$date_fin_test=$test_lot['date_etape_2'];
							elseif($test_lot['resultat_etape_1']!=0)
								$date_fin_test=$test_lot['date_etape_1'];
							else
								$date_fin_test="";

                            $this->_html .='
                            <tr class="border_top_fonce click_display">
                              <input type="hidden" name="id_lot" id="id_lot_'.$test_lot['id'].'" value="'. $ref_fourn_inv['id_inventaire_lots'] .'">
                              <th class="icon_plus_moins plus" id="icon_plus_moins_'.$test_lot['id'].'" id-attr="'.$test_lot['id'].'"><span class="icon-plus"></span></th>
                              <th>'.$date_fin_test.'</th>
                              <th class="center">'.$pourc_germ.'</th>
                              <th colspan="5"><textarea id="commentaire_'.$test_lot['id'].'" type="text">'.$test_lot['commentaire'].'</textarea></th>
                            </tr>
                            <tr class="display_'.$test_lot['id'].' display_none  border_claire">
                              <th></th>
                              <th></th>
                              <th colspan="2">Etape 1</th>
                              <th colspan="2">Etape 2</th>
                              <th colspan="2">Etape 3</th>
                            </tr>
                            <tr class="display_'.$test_lot['id'].' display_none border_claire">
                              <th></th>
                              <th>Date d&eacute;but test</th>
                              <th>Date</th>
                              <th>Resultat</th>
                              <th>Date</th>
                              <th>Resultat</th>
                              <th>Date</th>
                              <th>Resultat</th>
                              <th>Origine test</th>
                            </tr>
                            <tr class="display_'.$test_lot['id'].' display_none border_claire">
                              <td></td>
                              <th><input id="date_debut_semis_'.$test_lot['id'].'" class="datepickerLot" type="text" value="'.$test_lot['date_debut_semis'].'"></th>
                              <td><input id="date_etape_1_'.$test_lot['id'].'" class="datepickerLot" type="text" value="'.$test_lot['date_etape_1'].'"></td>
                              <td><input id="resultat_etape_1_'.$test_lot['id'].'" type="text" value="'.$test_lot['resultat_etape_1'].'"></td>
                              <td><input id="date_etape_2_'.$test_lot['id'].'" class="datepickerLot" type="text" value="'.$test_lot['date_etape_2'].'"></td>
                              <td><input id="resultat_etape_2_'.$test_lot['id'].'" type="text" value="'.$test_lot['resultat_etape_2'].'"></td>
                              <td><input id="date_etape_3_'.$test_lot['id'].'" class="datepickerLot" type="text" value="'.$test_lot['date_etape_3'].'"></td>
                              <td>
                                <input id="resultat_etape_3_'.$test_lot['id'].'" type="text" value="'.$test_lot['resultat_etape_3'].'">
                              </td>
                               <td>
                                <input id="origine_test_'.$test_lot['id'].'" type="text" value="'.$test_lot['origine_test'].'">
                              </td>
                            </tr>
                            <tr class="display_'.$test_lot['id'].' display_none border_claire">
                              <td colspan="8"><button class="envoi_modif" id="'.$test_lot['id'].'"><i class="icon-pencil"></i> Modifier</button></td>
                            </tr>
                            <tr class="display_'.$test_lot['id'].' display_none border_claire">
                              <td colspan="8" style="background-color:grey;line-height: 2px;">&nbsp;</td>
                            </tr>';

                          }

                          $this->_html .='
                        </tbody>
                      </table>
                      <h2 class="margin0 titre_click_display" id="titre_click_display_'.$ref_fourn_inv['id_inventaire_lots'].'" style="padding:10px;padding-top:20px; cursor: pointer;">Cr&eacute;er un nouveau test de germination</h2>
                      <table width="100%" class="center table_display" style="display: none;"  id="ligne_ajout_test_'. $ref_fourn_inv['id_inventaire_lots'] .'">
                        <tbody>
                          <tr class="border_claire">
                            <th></th>
                            <th></th>
                            <th colspan="2">Etape 1</th>
                            <th colspan="2">Etape 2</th>
                            <th colspan="2">Etape 3</th>
                            <th></th>
                          </tr>
                          <tr class="border_claire">
                            <th></th>
                            <th>Date d&eacute;but test</th>
                            <th colspan="0.5">Date</th>
                            <th>Resultat</th>
                            <th>Date</th>
                            <th>Resultat</th>
                            <th>Date</th>
                            <th>Resultat</th>

                            <th>Origine test</th>
                          </tr>
                          <tr class="border_claire">
                            <th></th>
                            <th><input placeholder="Date d&eacute;but de semis" type="text" class="datepickerLot date_debut_test" name="date_debut_test" /></th>
                            <td><input type="hidden" name="id_lot" id="id_lot" value="'. $ref_fourn_inv['id_inventaire_lots'] .'"><input type="text" class="datepickerLot date_etape_1" name="date_etape_1" /></td>
                            <td><input type="text" name="resultat_etape_1" id="resultat_etape_1"/></td>
                            <td><input type="text" class="datepickerLot date_etape_2" name="date_etape_2" ></td>
                            <td><input type="text" name="resultat_etape_2" id="resultat_etape_2"/></td>
                            <td><input type="text" class="datepickerLot date_etape_3" name="date_etape_3" /></td>
                            <td><input type="text" name="resultat_etape_3" id="resultat_etape_3"/></td>

                            <td><input type="text" name="origine_test" id="origine_test" value=""/></td>
                          </tr>
                          <tr>
                            <th colspan="9"><textarea placeholder="Mettre un commentaire" name="commentaire_test" id="commentaire_test"></textarea></th>
                          </tr>
                          <tr>
                            <td colspan="9"><button class="ajout_test" id="ajout_test_'. $ref_fourn_inv['id_inventaire_lots'] .'"><i class="process-icon-new" style="font-size:14px; width: auto; height: auto; display:inline-block; color: initial;"></i> Ajouter le test</button></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </td>
                <td style="width:110px"><button type="button" onclick="javascript:voir_test(' . $ref_fourn_inv['id_inventaire_lots'] . ')"><i class="icon-search-plus"></i></button> <button type="button" onclick="javascript:supprimer_lot(' . $ref_fourn_inv['id_inventaire_lots'] . ')"><i class="icon-trash"></i></button> <button type="button" onclick="javascript:modifier_lot(' . $ref_fourn_inv['id_inventaire_lots'] . ', ' . $product['id_product'] . ')"><i class="icon-pencil"></i></button></td>
              </tr>';




            }


            $date_courante = date('Y-m-d');

            $this->_html .='
            <tr>
              <td align="left"><input placeholder="Fournisseur" type="text" id="fournis' . $product['id_product'] . '" value="" /></td>
              <td align="left"><input placeholder="N&deg; lot origine" type="text" id="lot_org' . $product['id_product'] . '" value="" /></td>
              <td align="left"><input placeholder="Date appro" type="text" class="datepickerLot" id="date_appro' . $product['id_product'] . '" value="' . $date_courante . '" /></td>
              <td>
                <input type="radio" name="graine_gramme' . $product['id_product'] . '" id="graine' . $product['id_product'] . '" value="graine" />
                <label for="graine' . $product['id_product'] . '">Graines</label><br />
                <input type="radio" name="graine_gramme' . $product['id_product'] . '" id="gramme' . $product['id_product'] . '" value="gramme" checked="checked" />
                <label for="gramme' . $product['id_product'] . '">Grammes</label>
              </td>
              <td align="left"><input placeholder="Quantit� appro" type="text" id="quantite' . $product['id_product'] . '" value="" /></td>
              <td align="left"><input placeholder="N&deg; lot LBG" type="text" id="lot_LBG' . $product['id_product'] . '" value="" /></td>
              <td></td>
              <td align="left"><span style="display:none" id="date_germ_span' . $product['id_product'] . '" ></span><input placeholder="" type="text" class="datepickerLot" id="date_germ' . $product['id_product'] . '" value="' . $date_courante . '" /></td>
              <td align="left"><span style="display:none"  id="pourcent_germ_span' . $product['id_product'] . '" ></span><input placeholder="Pourcentage germination" type="text" id="pourcent_germ' . $product['id_product'] . '" value="" /></td>
              <td colspan="3" align="left"><span style="display:none"  id="comm_span' . $product['id_product'] . '" ></span><textarea placeholder="Mettre un commentaire" rows="3" cols="80" id="comm' . $product['id_product'] . '"></textarea></td>
              <td><input type="button" id="button_' . $product['id_product'] . '" value="Ajouter ce lot" onclick="submit_form_lot(' . $product['id_product'] . ')"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" id="button_reset_' . $product['id_product'] . '" value="Remettre &agrave; z&eacute;ro" onclick="reset_lot_add(' . $product['id_product'] . ')"/></td>
            </tr>
          </tbody>
        </table>';


              
			  
                        $poids_theorique = 0;
                        $rangee_attrib = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product = "' . $product['id_product'] . '";');
                        $nb_graine = 0;
                        foreach ($rangee_attrib AS $attrib)
                        {
                          $aux_dec = array();
                          $qt_commandee = 0;
                          $stock_theorique = 0;
                          $rangee_comb = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute_combination WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '";');
                          $traiter = true;
                          foreach ($rangee_comb AS $comb)
                          {
                            $dec = Db::getInstance()->ExecuteS('SELECT name, id_attribute FROM ' . _DB_PREFIX_ . 'attribute_lang WHERE id_attribute = "' . $comb['id_attribute'] . '" AND id_lang = 1 LIMIT 0,1;');
							              if(count($dec)){
                              $aux_dec[] = $dec[0]['name'];
							              }
                            if ( $dec[0]['id_attribute'] == 10512 || $dec[0]['id_attribute'] == 10513 )
                            {
                              if ( $_GET['id_category'] != '248' && $_GET['id_category'] != '143' && $_GET['id_category'] != '145' && $_GET['id_category'] != '146' )
                              {
                              $traiter = false;
                              }
                            }
                          }
                          if ( $traiter == false )
                          {
                            continue;
                          }

                          sort($aux_dec);
                          $libelle_dec = implode(' - ', $aux_dec);

                          $inv = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'inventaire WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');
                          if (!empty($inv[0]['date']))
                          {
                            $jour_inv = substr($inv[0]['date'], 6, 2);
                            $mois_inv = substr($inv[0]['date'], 4, 2);
                            $annee_inv = substr($inv[0]['date'], 0, 4);
                            $heure_inv = substr($inv[0]['date'], 8, 2);
                            $minutes_inv = substr($inv[0]['date'], 10, 2);


                            $der_inv = '<td>' . $jour_inv . '/' . $mois_inv . '/' . $annee_inv . '</td><td></td>';
                            $str_inv =  $jour_inv . '/' . $mois_inv . '/' . $annee_inv . ';'.$inv[0]['valeur'];
                          }
                          else
                          {
                            $der_inv = '<td>&nbsp;</td><td>&nbsp;</td>';
                          }
                          if ($der_inv != '<td>&nbsp;</td><td>&nbsp;</td>')
                          {



                            $qtec = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "' . $attrib['id_product_attribute'] . '";');

                            $poids_theorique += $qtec[0]['quantity'] * $attrib['weight'];
                            $stock_theorique = $qtec[0]['quantity'];
                            //if ( $product['id_product'] == 969 ) error_log('quantity : '.$qtec[0]['quantity'].' // weight : '.$attrib['weight']);
                            //if ( $product['id_product'] == 969 ) error_log('poids_theorique : '.$poids_theorique);
                            //if ( $product['id_product'] == 969 ) error_log('stock_theorique2 : '.$stock_theorique);
                            //if ( $product['id_product'] == 969 ) error_log('//////');

                          }
                          $id_stock_presta = StockAvailable::getStockAvailableIdByProductId($product['id_product'], $attrib['id_product_attribute']);
                          $stockAvailable = new StockAvailable($id_stock_presta);

                        // On récupère les stocks des commandes dans les états suivants : 
						
						// 1-en attente de paiement par chèque
						// 2-Paiement accepté
						// 3-Préparation en cours
						// 9-en attente : produits indisponibles
						// 10-en attente de paiement par virement bancaire
						// 11-en attente de paiement par Paypal
						// 12-paiement par mandat administratif accepté
						// 15-en attente : message envoyé au client
						// 16-en attente : à expédier ultérieurement
						// 17-en attente de paiement par mandat administratif
						// 18-en attente : produits indisponibles
						// 29-nous partons arracher votre rosier !
						
						$stateList = "( 1, 2, 3, 9, 10, 11, 12, 15, 16, 17, 18, 29)";
						$stockEnAttente = '';
						
						$product_attribute_id = (int) $attrib['id_product_attribute'];
						$product_id = $product['id_product'];

						// Calcul du stock en attente
						$sql = 'SELECT SUM(od.product_quantity) as total FROM `'._DB_PREFIX_.'order_detail` od 
						LEFT JOIN `'._DB_PREFIX_.'orders` o ON od.id_order = o.id_order 
						WHERE od.product_attribute_id = '.$product_attribute_id.'
						AND od.product_id = ' . $product_id .'
						AND o.current_state IN ' . $stateList . '';
						
						$res = Db::getInstance()->ExecuteS($sql);
						if(is_array($res)){
							if(count($res)){
								$stockEnAttente = $res[0]['total'];
							}
						}											 
												 
                        $this->_html .= '<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;' . $libelle_dec . '</td>' . $der_inv . '<td style="display:none;">' . $stock_theorique . '</td><td><input type="text" name="' . $product['id_product'] . '#' . $attrib['id_product_attribute'] . '" style="width:50px;" /></td><td>'.$stockAvailable->quantity.'</td><td>'.$stockEnAttente.'</td><td>&nbsp;</td></tr>';



                          $str_export .= $product['name'].' - '.$libelle_dec.';'.$str_inv.';'.$stock_theorique.';'.$stockAvailable->quantity."\n\r";
                          $nb_graine_sach = substr($libelle_dec, 0, strpos($libelle_dec, ' '));
                          $nb_graine_prod = intval($nb_graine_sach) * intval($stock_theorique);
                          $nb_graine = $nb_graine_prod + $nb_graine;
                          if ( $product['id_product'] == 2545 )
                          {
                           /* echo 'nb_graine_sach : '.$nb_graine_sach.'<br />';
                              echo 'stock_theorique : '.$stock_theorique.'<br />';
                              echo 'quantity : '.($qtec[0]['quantity']);
                              echo 'weight : '.($attrib['weight']);*/
                          }
                        }
                        $qt_reassort = 0;
                        $stock_theorique_tamp = 0;
                        $inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'inventaire WHERE id_product_attribute = "0" AND id_product = "' . $product['id_product'] . '" ORDER BY date DESC LIMIT 0,1;');



                        $total_site_tamp = $inv_tamp[0]['valeur'];
                        if (!empty($inv_tamp[0]['date']))
                        {
                          $jour_inv_tamp = substr($inv_tamp[0]['date'], 6, 2);
                          $mois_inv_tamp = substr($inv_tamp[0]['date'], 4, 2);
                          $annee_inv_tamp = substr($inv_tamp[0]['date'], 0, 4);
                          $heure_inv_tamp = substr($inv_tamp[0]['date'], 8, 2);
                          $minutes_inv_tamp = substr($inv_tamp[0]['date'], 10, 2);


                          $der_inv_tamp = '<td>' . $jour_inv_tamp . '/' . $mois_inv_tamp . '/' . $annee_inv_tamp . '</td><td></td>';
                          $str_der_inv_tamp = $jour_inv_tamp . '/' . $mois_inv_tamp . '/' . $annee_inv_tamp . ';' . $inv_tamp[0]['valeur'];
                        }
                        else
                        {
                          $der_inv_tamp = '<td>&nbsp;</td><td>&nbsp;</td>';
                        }
                        if ($der_inv_tamp != '<td>&nbsp;</td><td>&nbsp;</td>')
                        {
                    // Somme des quantit�s command�es depuis de dernier inventaire
                          $reassorts = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'reassort WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "0" AND date > "' . $annee_inv_tamp . $mois_inv_tamp . $jour_inv_tamp . $heure_inv_tamp . $minutes_inv_tamp . '";');
                          foreach ($reassorts AS $reassort)
                          {
                            $qt_reassort += $reassort['valeur'];
                          }
                          $stock_theorique_tamp = $inv_tamp[0]['valeur'] + $qt_reassort;
                        }

                        $this->_html .= '<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;Stock tampon</td>' . $der_inv_tamp .'<td><input type="text" name="' . $product['id_product'] . '#tampon" style="width:50px;" /></td><td style="display:none"></td><td>'.$stock_theorique_tamp.'</td></tr>';
                        $str_export .= $product['name'].' - Stock TAMPON ;'.$str_der_inv_tamp.';'.$stock_theorique_tamp.';'."\n\r";
                        if ($graine_gramme == "graine") {
                          $stock_total = $stock_theorique_tamp + $nb_graine;
                        }
                        else{
                          $stock_total =1000* $poids_theorique + $stock_theorique_tamp;
                        }
						
						// Plants de fraisiers à 0 (demande de Karine le 11 mars 2024)
						if($id_category == 378 || $id_category == 149 ){
							$stock_total = 0;
						}
						
                        $this->_html .= '<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;Stock Total</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>' . $stock_total .'</td><td>&nbsp;</td>';

                        $this->_html .= '<td>&nbsp;</td></tr>';
                        $product_complet_use = new Product($product['id_product']);
                        $prix_achat = floatval($product_complet_use->wholesale_price) / 1000;
                        if ($graine_gramme == "graine") {
                          $prix_stock_theorique = Tools::ps_round(floatval($prix_achat)*($stock_theorique + $stock_theorique_tamp + $nb_graine));
                        }
                        else{
                          $prix_stock_theorique = Tools::ps_round(floatval($prix_achat) * floatval(($poids_theorique * 1000) + $stock_theorique_tamp), 2);
                        }
                        if ($prix_stock_theorique < 0)
                        {
                          $prix_stock_theorique = 0;
                        }
                        $total_theorique_valeur += $prix_stock_theorique;
                        $this->_html .= '<tr class="border_bottom_inventaire"><td>&nbsp;</td><td colspan=3>&nbsp;&nbsp;&nbsp;&nbsp;Valeur th&eacute;orique produit</td><td>&nbsp;</td><td>' . $prix_stock_theorique . ' &euro;</td></tr>';
                      }
                      $this->_html .= '<tr><td colspan="1"><input type="submit" value="Valider" /></td><td colspan=2 style="text-align:right">Valeur th&eacute;orique Totale : </td><td>' . $total_theorique_valeur . ' &euro;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
                      $this->_html .= '</tbody></table><input type="hidden" name="maj" value="ok" /><input type="hidden" name="id_category" value="' . $id_category . '" /></form></div>';
                    }

                    $this->_html .= '</fieldset><br />';


        if(Tools::isSubmit('export_stock') && Tools::isSubmit('id_category'))
        {
         header('Content-Type: application/octet-stream');
         header('Content-disposition: attachment; filename=exportstock.csv');
         header('Pragma: no-cache');
         header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
         header('Expires: 0');
         print $str_export;
         exit();
       }
       return $this->_html;
     }

     public function setOption($option, $layers = 1)
     {
      list($this->_option, $this->_id_product) = explode('-', $option);
      $dateBetween = $this->getDate();
      switch ($this->_option)
      {
        case 1:
        $this->_query = '
        SELECT o.`date_add`, SUM(od.`product_quantity`) AS total
        FROM `' . _DB_PREFIX_ . 'order_detail` od
        LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON o.`id_order` = od.`id_order`
        WHERE od.`product_id` = ' . intval($this->_id_product) . '
        AND o.valid = 1
        AND o.`date_add` BETWEEN ' . $dateBetween . '
        GROUP BY o.`date_add`';
        $this->_titles['main'] = $this->l('Number of purchases');
        break;
        case 2:
        $this->_query = '
        SELECT dr.`time_start` AS date_add, SUM(pv.`counter`) AS total
        FROM `' . _DB_PREFIX_ . 'page_viewed` pv
        LEFT JOIN `' . _DB_PREFIX_ . 'date_range` dr ON pv.`id_date_range` = dr.`id_date_range`
        LEFT JOIN `' . _DB_PREFIX_ . 'page` p ON pv.`id_page` = p.`id_page`
        LEFT JOIN `' . _DB_PREFIX_ . 'page_type` pt ON pt.`id_page_type` = p.`id_page_type`
        WHERE pt.`name` = \'product.php\'
        AND p.`id_object` = ' . intval($this->_id_product) . '
        AND dr.`time_start` BETWEEN ' . $dateBetween . '
        AND dr.`time_end` BETWEEN ' . $dateBetween . '
        GROUP BY dr.`time_start`';
        $this->_titles['main'] = $this->l('Number of visits');
        break;
        case 3:
        $this->_query = '
        SELECT product_attribute_id, SUM(od.`product_quantity`) AS total
        FROM `' . _DB_PREFIX_ . 'orders` o
        LEFT JOIN `' . _DB_PREFIX_ . 'order_detail` od ON o.`id_order` = od.`id_order`
        WHERE od.`product_id` = ' . intval($this->_id_product) . '
        AND o.valid = 1
        AND o.`date_add` BETWEEN ' . $dateBetween . '
        GROUP BY od.`product_attribute_id`';
        $this->_titles['main'] = $this->l('Attributes');
        break;
      }
    }

    protected function getData($layers)
    {
      if ($this->_option != 3)
        $this->setDateGraph($layers, true);
      else
      {
        $product = new Product($this->_id_product, false, intval($this->getLang()));

        $combArray = array();
        $assocNames = array();
        $combinaisons = $product->getAttributeCombinaisons(intval($this->getLang()));
        foreach ($combinaisons AS $k => $combinaison)
          $combArray[$combinaison['id_product_attribute']][] = array('group' => $combinaison['group_name'], 'attr' => $combinaison['attribute_name']);
        foreach ($combArray AS $id_product_attribute => $product_attribute)
        {
          $list = '';
          foreach ($product_attribute AS $attribute)
            $list .= trim($attribute['group']) . ' - ' . trim($attribute['attr']) . ', ';
          $list = rtrim($list, ', ');
          $assocNames[$id_product_attribute] = $list;
        }

        $result = Db::getInstance()->ExecuteS($this->_query);
        foreach ($result as $row)
        {
          $this->_values[] = $row['total'];
          $this->_legend[] = $assocNames[$row['product_attribute_id']];
        }
      }
    }

    protected function setYearValues($layers)
    {
      $result = Db::getInstance()->ExecuteS($this->_query);
      foreach ($result AS $row)
        $this->_values[intval(substr($row['date_add'], 5, 2))] += $row['total'];
    }

    protected function setMonthValues($layers)
    {
      $result = Db::getInstance()->ExecuteS($this->_query);
      foreach ($result AS $row)
        $this->_values[intval(substr($row['date_add'], 8, 2))] += $row['total'];
    }

    protected function setDayValues($layers)
    {
      $result = Db::getInstance()->ExecuteS($this->_query);
      foreach ($result AS $row)
        $this->_values[intval(substr($row['date_add'], 11, 2))] += $row['total'];
    }
	
	protected function displayCatOption($children, $depth = '') {
		foreach ($children as $child) {
			$selected = Tools::getValue('id_category') == $child['id'] ? ' selected="selected"' : '';
			$this->_html .= '<option value="'.$child['id'].'"'.$selected.'>'.$depth.$child['name'].'</option>';
			$this->displayCatOption($child['children'],$depth.'    ');
		}
    }
}
?>
