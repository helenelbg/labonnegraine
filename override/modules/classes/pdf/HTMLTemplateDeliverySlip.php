<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*/
class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore
{
    public function getContent()
    {
        $delivery_address = new Address((int) $this->order->id_address_delivery);
        $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, [], '<br />', ' ');
        $formatted_invoice_address = '';
        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $invoice_address = new Address((int) $this->order->id_address_invoice);
            $formatted_invoice_address = AddressFormat::generateAddress($invoice_address, [], '<br />', ' ');
        }
        $carrier = new Carrier($this->order->id_carrier);
        $carrier->name = ($carrier->name == '0' ? Configuration::get('PS_SHOP_NAME') : $carrier->name);
        $order_details = $this->order_invoice->getProducts();
		
		// Sur le BL trier les réfs en numérique car les accessoires en 14-001 s'intercalent entre les refs 1-000 et 2-000
		usort($order_details, function ($a, $b) {
			$refa = $a['product_reference'];
			$refb = $b['product_reference'];
			$refa = str_replace('-','',$refa);
			$refb = str_replace('-','',$refb);
			$refa = (int) $refa;
			$refb = (int) $refb;
			return $refa <=> $refb;
		});

        if ($order_details) {
            foreach ($order_details as &$order_detail) {
				$order_detail['plant_en_precommande'] = false;
                if (Module::isInstalled('wkbundleproduct') && Module::isEnabled('wkbundleproduct')) {
                    include_once _PS_MODULE_DIR_ . 'wkbundleproduct/classes/WkBundleProductRequiredClasses.php';
                    $objBundle = new WkBundle();
                    if ($objBundle->isBundleProduct($order_detail['product_id'])) {
                        $order_detail['product_name'] = Hook::exec(
                            'displayAddBpProductName',
                            [
                                'id_order' => $order_detail['id_order'],
                                'product_id' => $order_detail['product_id'],
                                'product_name' => $order_detail['product_name'],
                                'id_customization' => $order_detail['id_customization'],
                            ]
                        );
                    } else {
                        $order_detail['product_name'] = $order_detail['product_name'];
                    }
                }
				// Ajout plants en précommande
				$categories = Product::getProductCategories($order_detail['product_id']);
				if ( Product::isPlantEnPrecommande($order_detail['product_name'],$categories) ){
					$str = " - La livraison est prévue entre le 20/04 et le 10/05.";
					$order_detail['product_name'] .= $str;
					$order_detail['plant_en_precommande'] = true;
				}
				
				// Patates douces
				if (in_array(248,$categories)){
					$str = " - La livraison est prévue mi-mai.";
					$order_detail['product_name'] .= $str;
				}
				
				// Plants gréffés
				if (in_array(346,$categories)){
					$str = " - La livraison est prévue mi-avril.";
					$order_detail['product_name'] .= $str;
				}
            }
        }
		
		// Sur le BL trier par plant en precommande
		usort($order_details, function ($a, $b) {
			$refa = $a['plant_en_precommande'];
			$refb = $b['plant_en_precommande'];
			return $refa <=> $refb;
		});
		
        if (Configuration::get('PS_PDF_IMG_DELIVERY')) {
            foreach ($order_details as &$order_detail) {
                if ($order_detail['image'] != null) {
                    $name = 'product_mini_' . (int) $order_detail['product_id'] .
                    (isset($order_detail['product_attribute_id']) ? '_' .
                    (int) $order_detail['product_attribute_id'] : '') . '.jpg';
                    $path = _PS_PROD_IMG_DIR_ . $order_detail['image']->getExistingImgPath() . '.jpg';
                    $order_detail['image_tag'] = preg_replace(
                        '/\.*' . preg_quote(__PS_BASE_URI__, '/') . '/',
                        _PS_ROOT_DIR_ . DIRECTORY_SEPARATOR,
                        ImageManager::thumbnail($path, $name, 45, 'jpg', false),
                        1
                    );
                    if (file_exists(_PS_TMP_IMG_DIR_ . $name)) {
                        $order_detail['image_size'] = getimagesize(_PS_TMP_IMG_DIR_ . $name);
                    } else {
                        $order_detail['image_size'] = false;
                    }
                }
            }
        }
        $this->smarty->assign([
            'order' => $this->order,
            'order_details' => $order_details,
            'delivery_address' => $formatted_delivery_address,
            'invoice_address' => $formatted_invoice_address,
            'order_invoice' => $this->order_invoice,
            'carrier' => $carrier,
            'display_product_images' => Configuration::get('PS_PDF_IMG_DELIVERY'),
        ]);
        $tpls = [
            'style_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.style-tab')),
            'addresses_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.addresses-tab')),
            'summary_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.summary-tab')),
            'product_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.product-tab')),
            'payment_tab' => $this->smarty->fetch($this->getTemplate('delivery-slip.payment-tab')),
        ];
        $this->smarty->assign($tpls);

        return $this->smarty->fetch($this->getTemplate('delivery-slip'));
    }
    
    public function getFilename()
    {
        if ($this->order_invoice->delivery_number == 0) $this->order_invoice->delivery_number = rand();
        return Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop) . sprintf('%06d', $this->order_invoice->delivery_number) . '.pdf';
    }
	
	public function getHeader()
    {
        $this->assignCommonHeaderData();
        $this->smarty->assign(['header' => Context::getContext()->getTranslator()->trans('Delivery', [], 'Shop.Pdf')]);
		
		$discounts = $this->order->getCartRules();
		foreach($discounts as $key => $discount){
			if(!$discount['id_cart_rule']){
				unset($discounts[$key]);
				continue;
			}
			$cartRule = new CartRule($discount['id_cart_rule']);
			if(!$cartRule){
				unset($discounts[$key]);
				continue;
			}
			if(!$cartRule->code){
				unset($discounts[$key]);
				continue;
			}
			$code = $cartRule->code;
			if(strpos($code, "BP") !== 0){ // pour tous les codes ne commençant pas par "BP" 
				 unset($discounts[$key]);
			} 
		}
		
		$this->smarty->assign([
            'order' => $this->order,
			'discounts' => $discounts,
            //'order_details' => $order_details,
            //'delivery_address' => $formatted_delivery_address,
            //'invoice_address' => $formatted_invoice_address,
            'order_invoice' => $this->order_invoice,
            //'carrier' => $carrier,
            'display_product_images' => Configuration::get('PS_PDF_IMG_DELIVERY'),
        ]);
		
        return $this->smarty->fetch($this->getTemplate('delivery-slip-header'));
    }
	
	/**
     * Returns the template's HTML footer.
     *
     * @return string HTML footer
     */
    public function getFooter()
    {
        $shop_address = $this->getShopAddress();

        $id_shop = (int) $this->shop->id;

        $this->smarty->assign([
            'available_in_your_account' => $this->available_in_your_account,
            'shop_address' => $shop_address,
            'shop_fax' => Configuration::get('PS_SHOP_FAX', null, null, $id_shop),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE', null, null, $id_shop),
            'shop_email' => Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop),
            'free_text' => Configuration::get('PS_INVOICE_FREE_TEXT', (int) Context::getContext()->language->id, null, $id_shop),
        ]);

        return $this->smarty->fetch($this->getTemplate('delivery-slip-footer'));
    }
	

}
