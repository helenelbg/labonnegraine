<?php

class MajmsgMasseController extends ModuleAdminController
{
    public $name = 'majmessagesmasse';
    public $displayName = 'Mise &agrave; jour de messages en masse';


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
        $this->setTemplate('MajmsgMasse.tpl');
    }

    private function getProducts($id_lang, $tous_les_id_sous_categ = array())
    {
        return Db::getInstance()->ExecuteS('
		SELECT p.`id_product`, p.reference, pl.`name`, (SELECT SUM(psa.quantity) FROM ' . _DB_PREFIX_ . 'stock_available psa WHERE psa.id_product = p.id_product GROUP BY psa.id_product) as quantity
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON p.`id_product` = pl.`id_product`
		' . (Tools::getValue('id_category') ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON p.`id_product` = cp.`id_product`' : '') . '
		WHERE pl.`id_lang` = ' . intval($id_lang) . '
		' . (!empty($tous_les_id_sous_categ) ? 'AND cp.id_category IN (' . implode(',', $tous_les_id_sous_categ) . ')' : '') . '
		AND p.active=1 ORDER BY p.`reference`');
    }


    private function loadModuleContent()
    {
        global $cookie, $currentIndex;
        $message = "";

        if (isset($_POST['maj']) && $_POST['maj'] == 'ok') {
            ////// Je traite la modification des messages

            foreach ($_POST['input_form_en_stock'] as $key => $value) {

                // Mise a jour des messages en stock
                $value_actuelle_en_stock = $_POST['input_form_en_stock'][$key];

                $req_maj_message_en_stock = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_lang SET available_now="' . $value_actuelle_en_stock . '" WHERE id_product="' . $key . '" AND id_lang='.$cookie->id_lang.';');
            }

            foreach ($_POST['input_form_hors_stock'] as $key => $value) {
                // Mise a jour des messages hors stock
                $value_actuelle_hors_stock = $_POST['input_form_hors_stock'][$key];

                $req_maj_message_hors_stock = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_lang SET not_available_message="' . $value_actuelle_hors_stock . '" WHERE id_product="' . $key . '" AND id_lang='.$cookie->id_lang.';');
            }

            foreach ($_POST['input_form_reappro'] as $key => $value) {
                // Mise a jour des messages hors stock
                $value_actuelle_reappro = $_POST['input_form_reappro'][$key];

                $req_maj_message_reappro = Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_lang SET available_later="' . $value_actuelle_reappro . '" WHERE id_product="' . $key . '" AND id_lang='.$cookie->id_lang.';');
            }

            $message = "Les messages ont bien &eacute;t&eacute;s mis &agrave; jour.";
        }
        $id_category = intval(Tools::getValue('id_category'));

        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
        $categories = Category::getCategories(intval($cookie->id_lang), false);

        $this->_html = '
			 <script type="text/javascript" src="/modules/' . $this->name . '/assets/js/moduleMajmessagesMasse.js"></script>

			<label>Choisir une catégorie</label>
			<div class="margin-form">
				<form action="" method="post" id="categoriesForm" name="categoriesForm">
					<select name="id_category" id="id_category" onchange="submit_form_select_category();">
						<option value="">Choisir...</option>';
//                        '<option value="0">Toutes</option>';
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

        if (isset($_POST['id_category']) && $_POST['id_category'] != '') {
            $id_current_category = $_POST['id_category'];
        } elseif (isset($_GET['id_category']) && $_GET['id_category'] != '') {
            $id_current_category = $_GET['id_category'];
        }

        if ((isset($_POST['id_category']) && $_POST['id_category'] != '') || (isset($_GET['id_category']) && $_GET['id_category'] != '')) {
            $this->_html .= '<div>
              <script type="text/javascript">
				$(function() {
						 $(".datepickerLot").datepicker({
						  dateFormat:"yy-mm-dd",
						  prevText:"",
						  nextText:""});
	             });
	            </script>

                       <div class="form_msg_generale" style="margin-top: 30px">
                                <div class="block_modif_en_stock">
                                    <input style="margin-left:0px; margin-bottom:5px; width: calc(100% - 300px); float: left" type="text" name="modifier_tous_en_stock" id="modifier_tous_en_stock" />
                                    <input style="height: 38px; width: 300px; float: right" type="button" value="Modifier les messages en stock" onclick="modifier_qte_generale_en_stock()" />
                                </div>
                                <br /><br />
                                <div class="block_modif_hors_stock">
                                    <input style="margin-left:0px; margin-bottom:5px; width: calc(100% - 300px); float: left" type="text" name="modifier_tous_hors_stock" id="modifier_tous_hors_stock" />
                                    <input style="height: 38px; width: 300px; float: right" type="button" value="Modifier les messages hors stock" onclick="modifier_qte_generale_hors_stock()" />
                                </div>
                                <br /><br />

                                <div class="block_modif_reappro">
                                    <input style="margin-left:0px; margin-bottom:5px; width: calc(100% - 300px); float: left" type="text" name="modifier_tous_en_stock" id="modifier_tous_reappro" />
                                    <input style="height: 38px; width: 300px; float: right" type="button" value="Modifier les messages de r&eacute;appro" onclick="modifier_qte_generale_reappro()" />
                                </div>
                        </div>

                    <div class="tableau_liste_produits">
			<form name="formInventaire" action="" method="post">
                        <input type="hidden" name="maj" value="ok" />
			<table class="table" border="0" cellspacing="0" cellspacing="0" width="100%">
			<thead>
				<tr>
                    <th style="width: 100px;">Reference</th>
					<th style="width: 250px;">Nom</th>
                                        <th style="text-align:center;width:150px;">Message en stock</th>
					<th style="text-align:center;width:150px;">Message hors stock</th>
                                        <th style="text-align:center;width:150px;">Message r&eacute;aprovisionnement</th>
				</tr>
			</thead><tbody>';


            // on recupere l'id de toutes les sous categories qu'il y a dans $array_categories qui elle meme liste les produits des sous categories
            function get_id_products($liste_sous_categ)
            {
                $tableau_id = array();
                foreach ($liste_sous_categ as $sous_categ_recupere) {
                    $tableau_id[] = $sous_categ_recupere['id'];
                    $tableau_id = array_merge($tableau_id, get_id_products($sous_categ_recupere['children']));
                }

                //print_r($tableau_id);
                return $tableau_id;
            }


            $array_categories = array();
            // Il n'y a pas d'objet cat�gorie en cours dans cette page donc on la cr��
            $current_category = new Category($id_current_category);
            $array_categories = $current_category->recurseLiteCategTree(10);

            $tous_les_id_sous_categ = array();
            $tous_les_id_sous_categ = get_id_products($array_categories['children']);
            $tous_les_id_sous_categ[] = $id_current_category;


            foreach ($this->getProducts($cookie->id_lang, $tous_les_id_sous_categ) as $product) {
                $infos_messages_stock = Db::getInstance()->executeS('SELECT available_now, available_later, not_available_message, id_lang FROM ' . _DB_PREFIX_ . 'product_lang WHERE id_product = "' . $product['id_product'] . '" AND id_lang='.$cookie->id_lang.';');

                $this->_html .= '<tr><td>' . $product['reference'] . '<td style="height:30px;color:#000000">' . $product['name'] . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td style="text-align:center"><input style="width:95%;" class="input_form_en_stock" type="text" value="' . $infos_messages_stock[0]['available_now'] . '" name="input_form_en_stock[' . $product['id_product'] . ']"/></td>
                                    <td style="text-align:center"><input style="width:95%;" class="input_form_hors_stock" type="text" value="' . $infos_messages_stock[0]['not_available_message'] . '" name="input_form_hors_stock[' . $product['id_product'] . ']"/></td>
                                    <td style="text-align:center"><input style="width:95%;" class="input_form_reappro" type="text" value="' . $infos_messages_stock[0]['available_later'] . '" name="input_form_reappro[' . $product['id_product'] . ']"/></td>
                                    </tr>';

            }

            $this->_html .= '</tbody></table><input type="hidden" name="maj" value="ok" /><input type="hidden" name="id_category" value="' . $id_category . '" />';
            $this->_html .= ' <input type="submit" value="Modifier les messages" /></form> </div>';

        }
        $this->_html .= '</fieldset><br />';

        $this->context->smarty->assign([
            'content' => $this->_html,
            'message' => $message
        ]);
    }
}