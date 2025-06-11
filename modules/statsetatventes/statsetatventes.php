<?php

class StatsEtatventes extends ModuleGraph
{

    private $_html = '';
    private $_query = '';
    private $_option = 0;
    private $_id_product = 0;

    function __construct()
    {
        $this->name = 'statsetatventes';
        $this->tab = 'Stats';
        $this->version = 1.0;

        parent::__construct();

        $this->displayName = 'Etat Ventes';
        $this->description = '';
    }

    public function install()
    {
        return (parent::install() AND $this->registerHook('AdminStatsModules'));
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
		
		$sql ='SELECT p.`id_product`, p.reference, pl.`name`, (p.quantity + IFNULL((SELECT SUM(pa.quantity) FROM ' . _DB_PREFIX_ . 'stock_available pa WHERE pa.id_product = p.id_product GROUP BY pa.id_product), 0)) as quantity
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`
		' . (Tools::getValue('id_category') ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`' : '') . '
		WHERE pl.`id_lang` = ' . intval($id_lang) . '
		' . (Tools::getValue('id_category') ? 'AND cp.id_category = ' . intval(Tools::getValue('id_category')) : '') . ' AND p.active=1
		ORDER BY pl.`name`';
		
		//$sql = preg_replace('/\s+/', ' ', $sql);
		//error_log($sql);

        return Db::getInstance()->ExecuteS($sql);
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

    public function hookAdminStatsModules($params)
    {
        global $cookie, $currentIndex;

        //mail('guillaume@anjouweb.com', 'datelbg', ModuleGraph::getDateBetween());

        if (isset($_POST['maj']) && $_POST['maj'] == 'ok')
        {
            reset($_POST);
            while (list($cle, $valeur) = each($_POST))
            {
                $exp = explode('#', $cle);
                if (!empty($exp[1]) && ($exp[1] == 'reassort') && !empty($valeur))
                {
                    $req_ins = Db::getInstance()->Execute('INSERT INTO ps_reassort (date, id_product, id_product_attribute, valeur) VALUES ("' . date('YmdHi') . '", "' . $exp[0] . '", "0", "' . $valeur . '");');
                }
                if (!empty($exp[1]) && ($exp[1] == 'achat') && !empty($valeur))
                {
                    $req_up = Db::getInstance()->Execute('UPDATE ps_product SET wholesale_price = "' . $valeur . '" WHERE id_product = "' . $exp[0] . '";');
                    $req_up2 = Db::getInstance()->Execute('UPDATE ps_product_shop SET wholesale_price = "' . $valeur . '" WHERE id_product = "' . $exp[0] . '";');
                }

                if (!empty($exp[1]) && ($exp[1] == 'alerte') && !empty($valeur))
                {
                    //On regarde s'il y a dÈja une alerte
                    $req_exist = Db::getInstance()->ExecuteS('SELECT count(*) as cpt FROM ps_alerte WHERE id_product=' . $exp[0] . ';');
                    //mail('aurelien@anjouweb.com', 'result_exist', print_r($req_exist, true));
                    if ($req_exist[0]['cpt'] == 0)
                    {
                        $req_ins = Db::getInstance()->Execute('INSERT INTO ps_alerte (id_product, valeur) VALUES ("' . $exp[0] . '", "' . $valeur . '");');
                    }
                    else
                    {
                        $req_ins = Db::getInstance()->Execute('UPDATE ps_alerte SET valeur="' . $valeur . '" WHERE id_product="' . $exp[0] . '";');
                    }
                }
            }
        }

        $id_category = intval(Tools::getValue('id_category'));
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        $this->_html = '<fieldset class="width3" style="width:650px;"><legend><img src="../modules/' . $this->name . '/logo.gif" /> ' . $this->displayName . '</legend>';

        $categories = Category::getCategories(intval($cookie->id_lang), false);

        $this->_html .= '
			<script type="text/javascript" src="'._MODULE_DIR_. $this->name . '/modulestatsetatventes.js"></script>
                        <style>.detail_reassort{display:none}</style>
			<label>Choisir une cat√©gorie</label>
			<div class="margin-form">
				<form action="" method="post" id="categoriesForm" name="categoriesForm">
					<select name="id_category" id="id_category" onchange="submit_form_select_category();">
						<option value="">Choisir...</option>
                        <option value="0">Toutes</option>';
        ob_start();
        $id_categorie_root = intval(Configuration::get('PS_ROOT_CATEGORY'));
        Category::recurseCategory($categories, $categories[0][$id_categorie_root], 1, $id_category);
        $buffer = ob_get_contents();
        ob_clean();
        ob_end_flush();
        $this->_html .= $buffer;
        $this->_html .= '
					</select>
				</form>
			</div>
			<div class="clear space"></div>
			<div class="clear space"></div>';
        if ((isset($_POST['id_category']) && $_POST['id_category'] != '') || (isset($_GET['id_category']) && $_GET['id_category'] != ''))
        {
            $this->_html .= '
			<div>

			<form name="formInventaire" action="" method="post">
			<table class="table" border="0" cellspacing="0" cellspacing="0" width="100%">
			<thead>
				<tr style="text-align:right;">
                    <th>Reference</th>
					<th>Nom</th>
					<th>Date dernier inventaire</th>
					<th>Date dernier r√©assort</th>
					<th>Stock Initial</th>
					<th>Stock Vendu</th>
					<th>Stock R√©assort (en g.)</th>
					<th>Stock Restant</th><th>P.A. <br />(en kg)</th>';
            /* <th>R√©assort (en g.)</th>

              <th>Alerte (en g.)</th> */
            $this->_html .= '</tr>
			</thead><tbody>';

            foreach ($this->getProducts($cookie->id_lang) AS $product)
            {
                /////////////////////////////////////////////////////////
                //$this->_html .= '<tr><td colspan="5"><font style="color: #000000">'.$product['name'].'</font></td></tr>';
                /////////////////////////////////////////////////////////
                $poids_commande = 0;
                $poids_theorique = 0;
                $stock_initial = 0;
				$jour_d = 0;
                $mois_d = 0;
                $annee_d = 0;
                $heure_d = 0;
                $minutes_d = 0;
                $rangee_attrib = Db::getInstance()->ExecuteS('SELECT * FROM ps_product_attribute WHERE id_product = "' . $product['id_product'] . '";');
                foreach ($rangee_attrib AS $attrib)
                {
                    $aux_dec = array();
                    $qt_commandee = 0;
                    $qt_commandee_init = 0;
                    $stock_initial_attr = 0;
                    $stock_theorique = 0;
                    $poids_commande_attr = 0;
                    $rangee_comb = Db::getInstance()->ExecuteS('SELECT * FROM ps_product_attribute_combination WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '";');
                    foreach ($rangee_comb AS $comb)
                    {
                        $dec = Db::getInstance()->ExecuteS('SELECT name FROM ps_attribute_lang WHERE id_attribute = "' . $comb['id_attribute'] . '" AND id_lang = 2 LIMIT 0,1;');
                        $aux_dec[] = @$dec[0]['name'];
                    }
                    sort($aux_dec);
                    $libelle_dec = implode(' - ', $aux_dec);

                    $explode_date = explode("' AND '", ModuleGraph::getDateBetween());
                    $jour_d = substr($explode_date[0], 10, 2);
                    $mois_d = substr($explode_date[0], 7, 2);
                    $annee_d = substr($explode_date[0], 2, 4);
                    $heure_d = substr($explode_date[0], 13, 2);
                    $minutes_d = substr($explode_date[0], 16, 2);

                    $jour_f = substr($explode_date[1], 8, 2);
                    $mois_f = substr($explode_date[1], 5, 2);
                    $annee_f = substr($explode_date[1], 0, 4);
                    $heure_f = substr($explode_date[1], 11, 2);
                    $minutes_f = substr($explode_date[1], 14, 2);

                    //echo $annee_d;

                    $inv = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '" AND id_product = "' . $product['id_product'] . '" AND date > "' . $annee_d . $mois_d . $jour_d . $heure_d . $minutes_d . '" ORDER BY date DESC LIMIT 0,1;');
                    /* mail('aurelien@anjouweb.com', 'awlbg', 'SELECT * FROM ps_inventaire WHERE id_product_attribute = "'.$attrib['id_product_attribute'].'" AND id_product = "'.$product['id_product'].'" AND date < "'.$annee_d.$mois_d.$jour_d.$heure_d.$minutes_d.'" ORDER BY date DESC LIMIT 0,1;'); */
                    /* if ( !empty($inv[0]['date']) )
                      {
                      $jour_inv = substr($inv[0]['date'], 6, 2);
                      $mois_inv = substr($inv[0]['date'], 4, 2);
                      $annee_inv = substr($inv[0]['date'], 0, 4);
                      $heure_inv = substr($inv[0]['date'], 8, 2);
                      $minutes_inv = substr($inv[0]['date'], 10, 2);
                      $der_inv = '<td>'.$jour_inv.'/'.$mois_inv.'/'.$annee_inv.'</td><td>'.$inv[0]['valeur'].'</td>';
                      }
                      else
                      {
                      $der_inv = '<td>&nbsp;</td><td>&nbsp;</td>';
                      } */
                    if (!empty($inv[0]['id']))
                    {
                        // Somme des quantitÈs commandÈes depuis de dernier inventaire
                        //$commandes = Db::getInstance()->ExecuteS('SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "'.$product['id_product'].'" AND pod.product_attribute_id = "'.$attrib['id_product_attribute'].'" AND po.date_add > "'.$annee_inv.'-'.$mois_inv.'-'.$jour_inv.' '.$heure_inv.':'.$minutes_inv.'";');
                        $jour_inv = substr($inv[0]['date'], 6, 2);
                        $mois_inv = substr($inv[0]['date'], 4, 2);
                        $annee_inv = substr($inv[0]['date'], 0, 4);
                        $heure_inv = substr($inv[0]['date'], 8, 2);
                        $minutes_inv = substr($inv[0]['date'], 10, 2);
                        //echo 'SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "'.$product['id_product'].'" AND pod.product_attribute_id = "'.$attrib['id_product_attribute'].'" AND po.date_add BETWEEN "'.$annee_inv.'-'.$mois_inv.'-'.$jour_inv.' '.$heure_inv.':'.$minutes_inv.':00" AND "'.$annee_d.'-'.$mois_d.'-'.$jour_d.' '.$heure_d.':'.$minutes_d.':00";<br /><br />';
                        $commandes_init = Db::getInstance()->ExecuteS('SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "' . $product['id_product'] . '" AND pod.product_attribute_id = "' . $attrib['id_product_attribute'] . '" AND po.date_add BETWEEN "' . $annee_inv . '-' . $mois_inv . '-' . $jour_inv . ' ' . $heure_inv . ':' . $minutes_inv . ':00" AND "' . $annee_d . '-' . $mois_d . '-' . $jour_d . ' ' . $heure_d . ':' . $minutes_d . ':00";');
                        foreach ($commandes_init AS $commande_init)
                        {
                            $qt_commandee_init += $commande_init['product_quantity'];
                        }
                        $stock_initial_attr = $inv[0]['valeur'] - $qt_commandee_init;
                        $stock_initial += ($stock_initial_attr * $attrib['weight']) * 1000;

                        $commandes = Db::getInstance()->ExecuteS('SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "' . $product['id_product'] . '" AND pod.product_attribute_id = "' . $attrib['id_product_attribute'] . '" AND po.date_add BETWEEN ' . ModuleGraph::getDateBetween() . ';');
                        //echo 'SELECT * FROM ps_order_detail pod, ps_orders po WHERE po.id_order = pod.id_order AND pod.product_id = "'.$product['id_product'].'" AND pod.product_attribute_id = "'.$attrib['id_product_attribute'].'" AND po.date_add BETWEEN '.ModuleGraph::getDateBetween().';<br /><br />';
                        foreach ($commandes AS $commande)
                        {
                            $qt_commandee += $commande['product_quantity'];
                        }
                        $stock_theorique = $stock_initial_attr - $qt_commandee;
                        $poids_theorique += $stock_theorique * $attrib['weight'];
                        $poids_commande_attr = ($qt_commandee * $attrib['weight']) * 1000;
                        $poids_commande += $poids_commande_attr;
                    }
                    /////////////////////////////////////////////////////////
                    //$this->_html .= '<tr style="text-align:right;"><td>&nbsp;&nbsp;&nbsp;&nbsp;'.$libelle_dec.'</td><td>'.$stock_initial_attr.'</td><td>'.$poids_commande_attr.'</td><td>&nbsp;</td><td>'.$stock_theorique.'</td><td><input type="text" name="'.$product['id_product'].'#'.$attrib['id_product_attribute'].'" style="width:50px;" /></td></tr>';
                    /////////////////////////////////////////////////////////
                }
                $qt_reassort = 0;
                $qt_reassort_init = 0;
                $stock_theorique_tamp = 0;
                $date_dernier_inventaire = "";
                $inv_tamp = Db::getInstance()->ExecuteS('SELECT * FROM ps_inventaire WHERE id_product_attribute = "0" AND id_product = "' . $product['id_product'] . '" AND date > "' . $annee_d . $mois_d . $jour_d . $heure_d . $minutes_d . '" ORDER BY date DESC LIMIT 0,1;');
                if (!empty($inv_tamp[0]['date']))
                {
                    $jour_inv_tamp = substr($inv_tamp[0]['date'], 6, 2);
                    $mois_inv_tamp = substr($inv_tamp[0]['date'], 4, 2);
                    $annee_inv_tamp = substr($inv_tamp[0]['date'], 0, 4);
                    $heure_inv_tamp = substr($inv_tamp[0]['date'], 8, 2);
                    $minutes_inv_tamp = substr($inv_tamp[0]['date'], 10, 2);
                    $der_inv_tamp = '<td>' . $inv_tamp[0]['valeur'] . ' g.</td>';
                    $stock_initial += $inv_tamp[0]['valeur'];
                    $date_dernier_inventaire = $jour_inv_tamp . "/" . $mois_inv_tamp . "/" . $annee_inv_tamp;
                }
                else
                {
                    $der_inv_tamp = '<td>&nbsp;</td>';
                }
                if ($der_inv_tamp != '<td>&nbsp;</td>')
                {
                    $reassorts_init = Db::getInstance()->ExecuteS('SELECT * FROM ps_reassort WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "0" AND date BETWEEN "' . $annee_inv . $mois_inv . $jour_inv . $heure_inv . $minutes_inv . '" AND "' . $annee_d . $mois_d . $jour_d . $heure_d . $minutes_d . '";');
                    foreach ($reassorts_init AS $reassort_init)
                    {
                        $qt_reassort_init += $reassort_init['valeur'];
                    }
                    $stock_initial += $qt_reassort_init;

                    $reassorts = Db::getInstance()->ExecuteS('SELECT * FROM ps_reassort WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "0" AND date >= "' . $annee_d . $mois_d . $jour_d . $heure_d . $minutes_d . '" AND date <= "' . $annee_f . $mois_f . $jour_f . $heure_f . $minutes_f . '";');
                    // mail('aurelien@anjouweb.com', 'date dernier reassort LBG','SELECT * FROM ps_reassort WHERE id_product = "'.$product['id_product'].'" AND id_product_attribute = "0" AND date >= "'.$annee_d.$mois_d.$jour_d.$heure_d.$minutes_d.'" AND date <= "'.$annee_f.$mois_f.$jour_f.$heure_f.$minutes_f.'";');
                    //echo 'SELECT * FROM ps_reassort WHERE id_product = "'.$product['id_product'].'" AND id_product_attribute = "0" AND date >= "'.$annee_d.$mois_d.$jour_d.$heure_d.$minutes_d.'" AND date <= "'.$annee_f.$mois_f.$jour_f.$heure_f.$minutes_f.'";<br />';
                    foreach ($reassorts AS $reassort)
                    {
                        $qt_reassort += $reassort['valeur'];
                    }
                    $stock_theorique_tamp = $inv_tamp[0]['valeur'] + $qt_reassort + $qt_reassort_init;
                }
                /////////////////////////////////////////////////////////
                //$this->_html .= '<tr style="text-align:right;"><td>&nbsp;&nbsp;&nbsp;&nbsp;Stock tampon</td>'.$der_inv_tamp.'<td>&nbsp;</td><td>'.$qt_reassort.' g.</td><td>'.$stock_theorique_tamp.' g.</td><td><input type="text" name="'.$product['id_product'].'#tampon" style="width:50px;" /></td></tr>';
                /////////////////////////////////////////////////////////
                $date_dern_reassort = Db::getInstance()->ExecuteS('SELECT date FROM ps_reassort WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "0" ORDER BY date DESC LIMIT 0,1;');
                
                if (isset($date_dern_reassort[0]['date']) && $date_dern_reassort[0]['date'] != "")
                {
                    $jour_dern_reassort = substr($date_dern_reassort[0]['date'], 6, 2);
                    $mois_dern_reassort = substr($date_dern_reassort[0]['date'], 4, 2);
                    $annee_dern_reassort = substr($date_dern_reassort[0]['date'], 0, 4);
                    $date_dernier_reassort = $jour_dern_reassort . "/" . $mois_dern_reassort . "/" . $annee_dern_reassort;
                }
                else
                {
                    $date_dernier_reassort = "";
                }

                $ach = Db::getInstance()->ExecuteS('SELECT wholesale_price FROM ps_product WHERE id_product = "' . $product['id_product'] . '" LIMIT 0,1;');
                $ale = Db::getInstance()->ExecuteS('SELECT valeur FROM ps_alerte WHERE id_product = "' . $product['id_product'] . '" ORDER BY id DESC LIMIT 0,1;');

                $this->_html .= '<tr style="text-align:right;"><td>'.$product['reference'].'</td><td><a href="javascript:afficher_details_reassort(' . $product['id_product'] . ')"><font style="color: #000000">' . $product['name'] . '</font></a></td><td>' . $date_dernier_inventaire . '</td><td>' . $date_dernier_reassort . '</td><td>' . $stock_initial . ' g.</td><td>' . $poids_commande . ' g.</td><td>' . $qt_reassort . ' g.</td><td>' . (($poids_theorique * 1000) + $stock_theorique_tamp) . ' g.</td>';
                /* <td><input type="text" name="'.$product['id_product'].'#reassort" style="width:50px;" /></td>
                  <td><input type="text" name="'.$product['id_product'].'#alerte" style="width:50px;" value="'.$ale[0]['valeur'].'" /></td>
                 */

                $this->_html .= '<td><input type="text" name="' . $product['id_product'] . '#achat" style="width:50px;" value="' . $ach[0]['wholesale_price'] . '" /></td></tr>';
                $liste_derniers_reassorts = Db::getInstance()->ExecuteS('SELECT date, valeur FROM ps_reassort WHERE id_product = "' . $product['id_product'] . '" AND id_product_attribute = "0" ORDER BY date DESC LIMIT 0,3;');
                $liste_reassort = "";
                foreach ($liste_derniers_reassorts as $reassort_der)
                {
                    $jour_reas = substr($reassort_der['date'], 6, 2);
                    $mois_reas = substr($reassort_der['date'], 4, 2);
                    $annee_reas = substr($reassort_der['date'], 0, 4);
                    $reassort_der['date'] = $jour_reas . "/" . $mois_reas . "/" . $annee_reas;
                    $liste_reassort .= '<tr><td>' . $reassort_der['date'] . '</td><td>' . $reassort_der['valeur'] . '</td></tr>';
                }
                if ($liste_reassort != "")
                {
                    $header_reassort = "<table class='table_detail_reassort'><tr><th>Date r√©assort</th><th>Quantit√©</th></tr>";
                    $liste_reassort = $header_reassort . $liste_reassort . "</table>";
                }
                else
                {
                    $liste_reassort = utf8_encode("Aucun rÈassort !");
                }


                $this->_html .= '<tr id="reassort_product_' . $product['id_product'] . '" class="detail_reassort"><td colspan = "10">' . $liste_reassort . '</td></tr>';
            }
            $this->_html .= '<tr><td colspan="2"><input type="submit" value="Valider" /></td></tr>';
            $this->_html .= '</tbody></table><input type="hidden" name="maj" value="ok" /><input type="hidden" name="id_category" value="' . $id_category . '" /></form></div>';
        }

        $this->_html .= '</fieldset><br />';

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

}

?>
