<?php
/**
* NOTICE OF LICENSE
*
* This product is licensed for one customer to use on one installation (test stores and multishop included).
* Site developer has the right to modify this module to suit their needs, but can not redistribute the module in
* whole or in part. Any other use of this module constitues a violation of the user agreement.
*
* DISCLAIMER
*
* NO WARRANTIES OF DATA SAFETY OR MODULE SECURITY
* ARE EXPRESSED OR IMPLIED. USE THIS MODULE IN ACCORDANCE
* WITH YOUR MERCHANT AGREEMENT, KNOWING THAT VIOLATIONS OF
* PCI COMPLIANCY OR A DATA BREACH CAN COST THOUSANDS OF DOLLARS
* IN FINES AND DAMAGE A STORES REPUTATION. USE AT YOUR OWN RISK.
*
*  @author    idnovate.com <info@idnovate.com>
*  @copyright 2022 idnovate.com
*  @license   See above
*/
class AdminCartRulesController extends AdminCartRulesControllerCore
{
    /*
    * module: quantitydiscountpro
    * date: 2023-06-21 17:26:03
    * version: 2.1.43
    */
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        if (Module::isEnabled('quantitydiscountpro')) {
            $this->_where = 'AND a.id_cart_rule NOT IN (SELECT id_cart_rule FROM `'._DB_PREFIX_.'quantity_discount_rule_cart`)';
        }
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
    }
	
	public function ajaxProcessShowOrders()
    {
		$html = '';
		$cart_rule_id = (int) Tools::getValue('cart_rule_id');
		
		if(!$cart_rule_id){
			$response = array('success' => false, 'message' => '');
			die(Tools::jsonEncode($response));
		}
		
		$sql = 'SELECT ocr.id_order, o.total_paid, o.date_add, ocr.value, o.reference, c.firstname, c.lastname, c.email
				FROM '._DB_PREFIX_.'order_cart_rule ocr
				LEFT JOIN '._DB_PREFIX_.'orders o ON o.id_order = ocr.id_order
				LEFT JOIN '._DB_PREFIX_.'customer c ON c.id_customer = o.id_customer
				WHERE ocr.id_cart_rule = '.$cart_rule_id;
		$res = Db::getInstance()->executeS($sql);
		
		if(!is_array($res)){
			$html .= '<div class="modal-aw-no-orders">Pas de commande</div>';
			$response = array('success' => true, 'message' => $html);
			die(Tools::jsonEncode($response));
		}
		
		if(!count($res)){
			$html .= '<div class="modal-aw-no-orders">Pas de commande</div>';
			$response = array('success' => true, 'message' => $html);
			die(Tools::jsonEncode($response));
		}
		
		function group_by($array, $key) {
			$return = array();
			foreach($array as $val) {
				$return[$val[$key]][] = $val;
			}
			return $return;
		}
		
		$orders = group_by($res, 'id_order');

		if(is_array($orders)){
			$html .= '<table><thead><tr>
				<th>ID commande</th>
				<th>Référence</th>
				<th>Client</th>
				<th>Email</th>
				<th>Date</th>
				<th>Valeur</th>
				<th>Montant de la commande</th>
			</tr></thead><tbody>';
			foreach($orders as $id_order => $order){
				$prix = 0;
				$montant = number_format($order[0]['total_paid'], 2, ',', ""). ' €'; 
				foreach($order as $o){
					$prix += $o['value'];
				}
				$prix = Tools::displayPrice($prix);
				$html .= '<tr>';
				$html .= '<td><a href="/admin123/index.php/sell/orders/'.$id_order.'/view">'.$id_order .'</a></td>';
				$html .= '<td>'.$order[0]['reference'] .'</td>';
				$html .= '<td>'.$order[0]['firstname'] .' ' . $order[0]['lastname'] .'</td>';
				$html .= '<td>'.$order[0]['email'] .'</td>';
				$html .= '<td>'.$order[0]['date_add'] .'</td>';
				$html .= '<td>'.$prix.'</td>';
				$html .= '<td>'.$montant.'</td>';
				$html .= '</tr>';
			}
			$html .= '</tbody></table>';
		}

        $response = array('success' => true, 'message' => $html);
        die(Tools::jsonEncode($response));
    }

	public function postProcess()
    {
        if (Tools::isSubmit('submitAddcart_rule') || Tools::isSubmit('submitAddcart_ruleAndStay')) {
            // If the reduction is associated to a specific product, then it must be part of the product restrictions
            if ((int) Tools::getValue('reduction_product') && Tools::getValue('apply_discount_to') == 'specific' && Tools::getValue('apply_discount') != 'off') {
                $reduction_product = (int) Tools::getValue('reduction_product');

                // First, check if it is not already part of the restrictions
                $already_restricted = false;
                if (is_array($rule_group_array = Tools::getValue('product_rule_group')) && count($rule_group_array) && Tools::getValue('product_restriction')) {
                    foreach ($rule_group_array as $rule_group_id) {
                        if (is_array($rule_array = Tools::getValue('product_rule_' . $rule_group_id)) && count($rule_array)) {
                            foreach ($rule_array as $rule_id) {
                                if (Tools::getValue('product_rule_' . $rule_group_id . '_' . $rule_id . '_type') == 'products'
                                    && in_array($reduction_product, Tools::getValue('product_rule_select_' . $rule_group_id . '_' . $rule_id))) {
                                    $already_restricted = true;

                                    break 2;
                                }
                            }
                        }
                    }
                }
                if ($already_restricted == false) {
                    // Check the product restriction
                    $_POST['product_restriction'] = 1;

                    // Add a new rule group
                    $rule_group_id = 1;
                    if (is_array($rule_group_array)) {
                        // Find the first rule_group_id that is not available in the array
                        while (in_array($rule_group_id, $rule_group_array)) {
                            ++$rule_group_id;
                        }
                        $_POST['product_rule_group'][] = $rule_group_id;
                    } else {
                        $_POST['product_rule_group'] = [$rule_group_id];
                    }

                    // Set a quantity of 1 for this new rule group
                    $_POST['product_rule_group_' . $rule_group_id . '_quantity'] = 1;
                    // Add one rule to the new rule group
                    $_POST['product_rule_' . $rule_group_id] = [1];
                    // Set a type 'product' for this 1 rule
                    $_POST['product_rule_' . $rule_group_id . '_1_type'] = 'products';
                    // Add the product in the selected products
                    $_POST['product_rule_select_' . $rule_group_id . '_1'] = [$reduction_product];
                }
            }

            // These are checkboxes (which aren't sent through POST when they are not checked), so they are forced to 0
            foreach (['country', 'carrier', 'group', 'cart_rule', 'product', 'shop'] as $type) {
                if (!Tools::getValue($type . '_restriction')) {
                    $_POST[$type . '_restriction'] = 0;
                }
            }

            // If the restriction is checked, but no item is selected, raise an error
            foreach (['country', 'carrier', 'group', 'shop'] as $type) {
                if (Tools::getValue($type . '_restriction') && empty(Tools::getValue($type . '_select'))) {
                    switch ($type) {
                        case 'country':
                            $restriction_name = $this->trans('Country selection', [], 'Admin.Catalog.Feature');
                            break;
                        case 'carrier':
                            $restriction_name = $this->trans('Carrier selection', [], 'Admin.Catalog.Feature');
                            break;
                        case 'group':
                            $restriction_name = $this->trans('Customer group selection', [], 'Admin.Catalog.Feature');
                            break;
                        case 'shop':
                        default:
                            $restriction_name = $this->trans('Store selection', [], 'Admin.Catalog.Feature');
                            break;
                    }
                    $this->errors[] = $this->trans('The "%s" restriction is checked, but no item is selected.', [$restriction_name], 'Admin.Catalog.Notification');
                }
            }

            // Remove the gift if the radio button is set to "no"
            if (!(int) Tools::getValue('free_gift')) {
                $_POST['gift_product'] = 0;
            }

            // Retrieve the product attribute id of the gift (if available)
            if ($id_product = (int) Tools::getValue('gift_product')) {
                $_POST['gift_product_attribute'] = (int) Tools::getValue('ipa_' . $id_product);
            }

            // Idiot-proof control
            if (strtotime(Tools::getValue('date_from')) > strtotime(Tools::getValue('date_to'))) {
                $this->errors[] = $this->trans('The voucher cannot end before it begins.', [], 'Admin.Catalog.Notification');
            }
            if ((int) Tools::getValue('minimum_amount') < 0) {
                $this->errors[] = $this->trans('The minimum amount cannot be lower than zero.', [], 'Admin.Catalog.Notification');
            }
            if ((float) Tools::getValue('reduction_percent') < 0 || (float) Tools::getValue('reduction_percent') > 100) {
                $this->errors[] = $this->trans('Reduction percentage must be between 0% and 100%', [], 'Admin.Catalog.Notification');
            }
            if ((int) Tools::getValue('reduction_amount') < 0) {
                $this->errors[] = $this->trans('Reduction amount cannot be lower than zero.', [], 'Admin.Catalog.Notification');
            }
            if (Tools::getValue('code') && ($same_code = (int) CartRule::getIdByCode(Tools::getValue('code'))) && $same_code != Tools::getValue('id_cart_rule')) {
                $this->errors[] = $this->trans('This cart rule code is already used (conflict with cart rule %rulename%)', ['%rulename%' => $same_code], 'Admin.Catalog.Notification');
            }
            if (Tools::getValue('apply_discount') == 'off' && !Tools::getValue('free_shipping') && !Tools::getValue('free_gift')) {
                //$this->errors[] = $this->trans('An action is required for this cart rule.', [], 'Admin.Catalog.Notification');
            }
        }

        return AdminController::postProcess();
    }
}
