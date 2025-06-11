<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class CheckoutDeliveryStep extends CheckoutDeliveryStepCore
{
    public function render(array $extraParams = [])
    {
		$delivery_options = $this->getCheckoutSession()->getDeliveryOptions();
		$aw_erreur = $this->PlantsCorse();
		if($aw_erreur){
			$delivery_options = [];
		}
		
        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams,
            [
                'hookDisplayBeforeCarrier' => Hook::exec('displayBeforeCarrier', ['cart' => $this->getCheckoutSession()->getCart()]),
                'hookDisplayAfterCarrier' => Hook::exec('displayAfterCarrier', ['cart' => $this->getCheckoutSession()->getCart()]),
                'id_address' => $this->getCheckoutSession()->getIdAddressDelivery(),
                'delivery_options' => $delivery_options,
                'delivery_option' => $this->getCheckoutSession()->getSelectedDeliveryOption(),
                'recyclable' => $this->getCheckoutSession()->isRecyclable(),
                'recyclablePackAllowed' => $this->isRecyclablePackAllowed(),
                'delivery_message' => $this->getCheckoutSession()->getMessage(),
                'gift' => [
                    'allowed' => $this->isGiftAllowed(),
                    'isGift' => $this->getCheckoutSession()->getGift()['isGift'],
                    'label' => $this->getTranslator()->trans(
                        'I would like my order to be gift wrapped %cost%',
                        ['%cost%' => $this->getGiftCostForLabel()],
                        'Shop.Theme.Checkout'
                    ),
                    'message' => $this->getCheckoutSession()->getGift()['message'],
                ],
				'aw_erreur' => $aw_erreur
            ]
        );
    }
	
	public function PlantsCorse()
    {
		// Exclure tous les plants aromatiques/potagers et greffés ainsi que les petits fruits et fraisiers en godets de la livraison en Corse
		// Aucun plant ne peut être envoyé en Corse pour des raisons sanitaires
		
		if(!isset($this->context->cart)){
			return false;
		}
		
		$cart = $this->context->cart;
				
		$address = new Address($cart->id_address_delivery);

		$id_france = 8;
		$id_departement = 20;
		$departement = substr($address->postcode, 0, 2); 
	
		if($address->id_country != $id_france){
			return false;
		}
		
		if($departement != $id_departement){
			return false;
		}
		
		//echo 'Corse !';
		
		$safe_products = [3072,3071,3070,3068,2577,3066,3064,3062,2192,2187,2191,2188,2190,2189,2361,2921,2920,2573,2913,1962,1961,1247,1970,2914,960,957,956,1114,1113,1115,959,1126,958];
		$excl_categories = [248,344,345,346,347,348];
		
		$products = $cart->getProducts();
		$plants = [];
		foreach($products as $product){
			$id_product = $product['id_product'];
			$id_product_attribute = $product['id_product_attribute'];
			$name = $product['name'];
			
			if(in_array($id_product,$safe_products)){
				continue;
			}
			
			if(Product::isPlantEnPrecommandeByProductId($id_product)){
				// Cas : plants en précommande
				if(Product::isPlantEnPrecommandeById($id_product_attribute)){
					$plants[] = ' - '.$name . ' ' . $product['attributes'];
				}
			}
			else{
				// Cas : autres plants
				$product_obj = new Product($id_product);
				$categories = $product_obj->getCategories();
				if(array_intersect($categories,$excl_categories)){
					$plants[] = ' - '.$name . ' ' . $product['attributes'];
				}
			}
			
		}
		
		if(count($plants)){
			$str = "";
			$str .= "Malheureusement, vous ne pouvez pas commander les produits suivants :<br>";
			$str .= implode("<br>",$plants);
			$str .= "<br><br>Nous sommes désolés de ce désagrément, mais aucun plant ne peut être envoyé en Corse pour des raisons sanitaires. Afin de finaliser votre commande, vous devez supprimer l'ensemble des plants en godet de votre panier. Nous vous remercions pour votre compréhension.<br>";
			//echo $str;
			return $str;
		}
		
		return false;
	}
}
