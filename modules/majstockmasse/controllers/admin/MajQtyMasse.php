<?php
class MajQtyMasseController extends ModuleAdminController {
    public $name = 'majstockmasse';
    public $displayName = 'Mise &agrave; jour de quantit&eacute;s en masse';

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->bootstrap = true;
    }

    public function initContent()
    {
        parent::initContent();
        $this->loadModuleContent();
        $this->setTemplate('MajQtyMasse.tpl');
    }
    private function getProducts($id_lang, $tous_les_id_sous_categ = array())
    {
//        return Db::getInstance()->ExecuteS('
//		SELECT p.`id_product`, p.reference, pl.`name`, (p.quantity + IFNULL((SELECT SUM(pa.quantity) FROM ' . _DB_PREFIX_ . 'product_attribute pa WHERE pa.id_product = p.id_product GROUP BY pa.id_product), 0)) as quantity
//		FROM `' . _DB_PREFIX_ . 'product` p
//		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`
//		' . (Tools::getValue('id_category') ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`' : '') . '
//		WHERE pl.`id_lang` = ' . intval($id_lang) . '
//		' . (!empty($tous_les_id_sous_categ) ? 'AND cp.id_category IN ('.implode(',', $tous_les_id_sous_categ) .')' : '') . '
//		AND p.active=1 AND p.id_product NOT IN (SELECT id_pack FROM ' . _DB_PREFIX_ . 'pm_advancedpack_products) ORDER BY pl.`name`');

        return Db::getInstance()->ExecuteS('
		SELECT DISTINCT p.`id_product`, p.reference, pl.`name`, (SELECT SUM(psa.quantity) FROM ' . _DB_PREFIX_ . 'stock_available psa WHERE psa.id_product = p.id_product GROUP BY psa.id_product) as quantity
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`
		LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` ps ON p.`id_product` = ps.`id_product`
		' . (Tools::getValue('id_category') ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`' : '') . '
		WHERE pl.`id_lang` = ' . intval($id_lang) . '
		' . (!empty($tous_les_id_sous_categ) ? 'AND cp.id_category IN ('.implode(',', $tous_les_id_sous_categ) .')' : '') . '
		AND ps.active=1 ORDER BY p.`reference`');
    }


    public function loadModuleContent($token = NULL)
    {
        global $cookie, $currentIndex;
        $message = "";

        require_once(dirname(__FILE__)."/../../../ps_emailalerts/MailAlert.php");

        if (isset($_POST['maj']) && $_POST['maj'] == 'ok')
        {
            ////// Je traite la modification de quantité
            $tableau_decoupe = array();
            $tableau_qte_declinaisons = array();
            $qte_produit = 0;

            foreach ($_POST as $key => $value)
            {
                if (strstr($key, '#') && !strstr($key, 'hidden_'))
                {
                    $tableau_decoupe = explode('#', $key);

                    // Je recupere l'ancienne quantité
                    $ancienne_qte_dec = Db::getInstance()->ExecuteS('SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product_attribute = "' . $tableau_decoupe[1] . '";');


                     if((trim($value)=="") || !is_numeric(trim($value)))
                     {
                          $value_actuelle_hidden = $_POST['hidden_'.$key];
                          if (!isset($tableau_qte_declinaisons[$tableau_decoupe[0]]))
                          {
                                $tableau_qte_declinaisons[$tableau_decoupe[0]] = 0;
                          }
                        $tableau_qte_declinaisons[$tableau_decoupe[0]] += intval($value_actuelle_hidden);
//                          $req_up_product_attributes = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_attribute SET quantity = ' . intval($value_actuelle_hidden) . ' WHERE id_product_attribute = "' . $tableau_decoupe[1] . '";');
                         $req_up_product_attributes2 = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'stock_available SET quantity = ' . intval($value_actuelle_hidden) . ' WHERE id_product_attribute = "' . $tableau_decoupe[1] . '";');

                     }
                     else
                     {
                        //Addition de la qte de chaque declinaison
                        if(!isset($tableau_qte_declinaisons[$tableau_decoupe[0]]))
                        {
                            $tableau_qte_declinaisons[$tableau_decoupe[0]] = 0;
                        }
                        $tableau_qte_declinaisons[$tableau_decoupe[0]] += intval($value);
//                        $req_up_product_attributes = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_attribute SET quantity = ' . intval($value) . ' WHERE id_product_attribute = "' . $tableau_decoupe[1] . '";');
                        $req_up_product_attributes2 = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'stock_available SET quantity = ' . intval($value) . ' WHERE id_product_attribute = "' . $tableau_decoupe[1] . '";');
                     }

                    // Si la checkbox alerte mails est coch�e
                    if (isset($_POST['checkbox_alerte_mails']))
                    {
                        if((intval($ancienne_qte_dec[0]['quantity'])<=0)&&(intval($value)>0))
                        {
                            MailAlert::sendCustomerAlert($tableau_decoupe[0], $tableau_decoupe[1]);
                        }
                    }
                }
            }

            foreach ($tableau_qte_declinaisons as $key => $value)
            {
                // Je recupere l'ancienne quantité
                $ancienne_qte = Db::getInstance()->ExecuteS('SELECT quantity FROM ' . _DB_PREFIX_ . 'product WHERE id_product = "' . $key . '";');
                // j'ajoute le nombre de produits dans la BDD
//                $req_up_product = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product SET quantity = "' . intval($value) . '" WHERE id_product = "' . $key . '";');
                $req_up_product2 = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'stock_available SET quantity = "' . intval($value) . '" WHERE id_product = "' . $key . '" AND id_product_attribute = 0;');

                // Si la checkbox alerte mails est cochée
                if (isset($_POST['checkbox_alerte_mails']))
                {
                    if((intval($ancienne_qte[0]['quantity'])<=0)&&(intval($value)>0))
                    {
                        MailAlert::sendCustomerAlert($key, 0);
                    }
                }
            }

            $message = "Les quantit&eacute;s ont bien &eacute;t&eacute; mises &agrave; jour.";
        }
        $id_category = intval(Tools::getValue('id_category'));

        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        $this->_html = '<fieldset><legend><img src="../modules/'.$this->name.'/logo.gif" /> ' . $this->displayName . '</legend>';

        $categories = Category::getCategories(intval($cookie->id_lang), false);

        $this->_html .= '
         <script type="text/javascript" src="/modules/' . $this->name . '/assets/js/modulemajstockmasse.js"></script>

        <label>Choisir une catégorie</label>
        <div class="margin-form">
            <form action="" method="post" id="categoriesForm" name="categoriesForm">
                <select name="id_category" id="id_category" onchange="submit_form_select_category();">
                    <option value="">Choisir...</option>';
//                    '<option value="0">Toutes</option>';
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
        </div>';

        $this->_html .= '<div class="clear space"></div>';

        if (isset($_POST['id_category']) && $_POST['id_category'] != '')
        {
            $id_current_category = $_POST['id_category'];
        }
        elseif (isset($_GET['id_category']) && $_GET['id_category'] != '')
        {
            $id_current_category = $_GET['id_category'];
        }

        if ((isset($_POST['id_category']) && $_POST['id_category'] != '') || (isset($_GET['id_category']) && $_GET['id_category'] != ''))
        {
            $this->_html .= '<div>
          <script type="text/javascript">
            $(function() {
                     $(".datepickerLot").datepicker({
                      dateFormat:"yy-mm-dd",
                      prevText:"",
                      nextText:""});
             });
            </script>

                    <div class="form_qte_generale" style="text-align:center; padding-bottom:15px;">
                        Quantit&eacute; g&eacute;n&eacute;rale  &nbsp &nbsp
                        <input type="text" name="input_qte_generale" id="input_qte_generale" style="width:50px;" />
                         &nbsp &nbsp
                        <input type="button" value="Modifier la quantit&eacute; g&eacute;n&eacute;rale" onclick="modifier_qte_generale()" />
                    </div>


        <form name="formInventaire" action="" method="post">
                    <input type="hidden" name="maj" value="ok" />
        <table class="table" border="0" cellspacing="0" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Nom</th>
                                    <th  style="text-align:center">Quantit&eacute actuelle</th>
                <th  style="text-align:center">Nouvelle Quantit&eacute;</th>
            </tr>
        </thead><tbody>';





            // on recupere l'id de toutes les sous categories qu'il y a dans $array_categories qui elle meme liste les produits des sous categories
            function get_id_products($liste_sous_categ)
            {
                $tableau_id = array();
                foreach($liste_sous_categ as $sous_categ_recupere)
                {
                    $tableau_id[] = $sous_categ_recupere['id'];
                    $tableau_id = array_merge($tableau_id, get_id_products($sous_categ_recupere['children']));
                }

                //print_r($tableau_id);
                return $tableau_id;
            }


            $array_categories = array();
            // Il n'y a pas d'objet catégorie en cours dans cette page donc on la créé
            $current_category = new Category($id_current_category);
            $array_categories = $current_category->recurseLiteCategTree(10);

            $tous_les_id_sous_categ = array();
            $tous_les_id_sous_categ = get_id_products($array_categories['children']);
            $tous_les_id_sous_categ[] = $id_current_category;



            foreach ($this->getProducts($cookie->id_lang, $tous_les_id_sous_categ) AS $product)
            {

                $this->_html .= '<tr><td>' . $product['reference'] . '</td><td colspan=2 style="border-top: 3px solid #DEDEDE; height:30px;"><font style="color: #000000">' . $product['name'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </font></td><td style="border-top: 3px solid #DEDEDE;"></td></tr>';

                // Nom des declinaisons
                $rangee_attrib = Db::getInstance()->ExecuteS('SELECT sa.* FROM ' . _DB_PREFIX_ . 'product_attribute pa LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa ON (sa.id_product_attribute = pa.id_product_attribute) WHERE sa.id_product = "' . $product['id_product'] . '" AND sa.id_product_attribute <> 0 AND sa.id_shop = 1;');

                foreach ($rangee_attrib AS $attrib)
                {
                    $aux_dec = array();
                    $rangee_comb = Db::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . 'product_attribute_combination WHERE id_product_attribute = "' . $attrib['id_product_attribute'] . '";');
                    foreach ($rangee_comb AS $comb)
                    {
                        $dec = Db::getInstance()->ExecuteS('SELECT name FROM ' . _DB_PREFIX_ . 'attribute_lang WHERE id_attribute = "' . $comb['id_attribute'] . '" AND id_lang = 1 LIMIT 0,1;');
                        $aux_dec[] = $dec[0]['name'];
                    }
                    sort($aux_dec);
                    $libelle_dec = implode(' - ', $aux_dec);


                    $this->_html .= '<tr><td></td><td style="height:30px;">&nbsp;&nbsp;&nbsp;&nbsp;' . $libelle_dec . '</td><td style="text-align:center"><input type="hidden" name="hidden_' . $product['id_product'] . '#' . $attrib['id_product_attribute'] . '" value="' . $attrib['quantity'] . '" />' . $attrib['quantity'] . '</td><td  style="text-align:center"><input class="input_qte_declinaisons" type="text" name="' . $product['id_product'] . '#' . $attrib['id_product_attribute'] . '" style="width:50px;" /></td></tr>';
                }
            }

            $this->_html .= '</tbody></table><input type="hidden" name="maj" value="ok" /><input type="hidden" name="id_category" value="' . $id_category . '" />';
            $this->_html .= ' <input type="submit" value="Modifier le stock" /> &nbsp;&nbsp; <input type="checkbox" name="checkbox_alerte_mails" checked="checked">  Envoyer les alertes de stock par mail.</form>';
        }
        $this->_html .= '</fieldset><br />';
        
        $this->context->smarty->assign([
            'content' => $this->_html,
            'message' => $message
        ]);
    }
}
?>
