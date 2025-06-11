<?php
class MyAssistantController extends FrontController
{
    public function initContent()
    {
        parent::initContent();

        $namedAssistant = Db::getInstance()->executeS('
            SELECT * FROM '._DB_PREFIX_.'feature_value_lang ps
            INNER JOIN assistant_url u ON u.id_assistant = ps.id_feature_value
            INNER JOIN assistant_client a ON a.idAssistant = ps.id_feature_value
            WHERE ps.id_feature_value IN (SELECT a.idAssistant FROM assistant_client a) && a.idClient = '.$this->context->cart->id_customer.' && ps.id_lang = 2 && a.etat <> 0');

        $adresseAssistant = Db::getInstance()->executeS('
            SELECT * FROM assistant_client a
            INNER JOIN Departements d ON d.numDepartement = a.numDepartement
            WHERE a.idClient = '.$this->context->cart->id_customer);

		$adresseLivraison = Db::getInstance()->executeS('SELECT * FROM assistant_client WHERE idClient = '.$this->context->cart->id_customer);

		if (!empty($adresseLivraison[0])) {
			$this->context->smarty->assign("adresseLivraison", $adresseLivraison[0]["numDepartement"]);
		}else{
			$adresseLivraison = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'address WHERE id_address = '.$this->context->cart->id_address_delivery);
			$this->context->smarty->assign("adresseLivraison", substr($adresseLivraison[0]["postcode"], 0, 2));
		}

		$lesDepartements = Db::getInstance()->executeS('SELECT * FROM Departements');

		$this->context->smarty->assign("lesDepartements", $lesDepartements);

		// LBG assistants sur les 5 dernières commandes / 18 derniers mois

		if($this->context->customer->email == "guillaume@anjouweb.com"){
			$commandes = Db::getInstance()->executeS('
			SELECT id_cart FROM '._DB_PREFIX_.'orders
			WHERE id_customer = '.$this->context->cart->id_customer.'
			AND DATE_SUB(CURDATE(),INTERVAL 18 MONTH) <= date_add
			AND current_state NOT IN (6, 7)
			ORDER BY `id_order` DESC LIMIT 5');
		}else{
			$commandes = Db::getInstance()->executeS('
			SELECT id_cart FROM '._DB_PREFIX_.'orders
			WHERE id_customer = '.$this->context->cart->id_customer.'
			AND DATE_SUB(CURDATE(),INTERVAL 18 MONTH) <= date_add
			ORDER BY `id_order` DESC LIMIT 5');
		}


		$resultProduitFinal = array();
		$array_ass = array();

		foreach ($commandes as $c){
			$id_cart = $c['id_cart'];
			$my_cart = new Cart($id_cart);
			$lesProduits = $my_cart->getProducts();

			$arrayProduit = "(";
			for ($i=0; $i < count($lesProduits); $i++) {
				if ($i == 0) {
					$arrayProduit .= $lesProduits[$i]["id_product"];
				}else{
					$arrayProduit .= ", ".$lesProduits[$i]["id_product"];
				}
			}
			$arrayProduit .= ")";

			$resultProduit = Db::getInstance()->executeS('
			(SELECT ps.*, psv.value, a.etat FROM '._DB_PREFIX_.'feature_product ps
			INNER JOIN '._DB_PREFIX_.'feature_value_lang psv ON ps.id_feature_value = psv.id_feature_value
			LEFT JOIN assistant_client a ON a.idAssistant = psv.id_feature_value
			WHERE psv.id_lang = 2 AND ps.id_feature = 34 AND a.idClient = '.$this->context->cart->id_customer.' AND ps.id_product IN'.$arrayProduit.')
			UNION
			(SELECT ps.*, psv.value, 0 FROM '._DB_PREFIX_.'feature_product ps
			INNER JOIN '._DB_PREFIX_.'feature_value_lang psv ON ps.id_feature_value = psv.id_feature_value
			WHERE psv.id_lang = 2 AND ps.id_feature = 34 AND ps.id_product IN'.$arrayProduit.' AND ps.id_feature_value NOT IN (SELECT idAssistant FROM assistant_client WHERE idclient = "'.$this->context->cart->id_customer.'"))');

			foreach ($resultProduit as $pr){
				// Evite les doublons
				if (!in_array($pr['id_feature_value'], $array_ass)){

					$pa = Db::getInstance()->executeS('
					SELECT * FROM '._DB_PREFIX_.'feature_value_lang ps
					INNER JOIN assistant_url u ON u.id_assistant = ps.id_feature_value
					INNER JOIN assistant_client a ON a.idAssistant = ps.id_feature_value
					WHERE ps.id_feature_value = '.$pr['id_feature_value'].' LIMIT 1 ');

					$pr['image'] = '';
					$pr['value'] = '';
					if(is_array($pa)){
						if(count($pa)){
							$pr['image'] = $pa[0]['image'];
							$pr['value'] = $pa[0]['value'];
						}
					}
					$resultProduitFinal[] = $pr;
					$array_ass[] = $pr['id_feature_value'];
				}
			}
		}

		$this->context->smarty->assign('resultProduit', $resultProduitFinal);
        $this->context->smarty->assign('adresseAssistant', $adresseAssistant);
        $this->context->smarty->assign('namedAssistant', $namedAssistant);
        $this->context->smarty->assign("idCustomer", $this->context->cart->id_customer);

        $this->setTemplate('my-assistant.tpl');
    }
}
