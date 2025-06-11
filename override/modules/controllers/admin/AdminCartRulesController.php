<?php
class AdminCartRulesController extends AdminCartRulesControllerCore
{
	
	public function ajaxProcessShowOrders()
    {
		$html = '';
		$cart_rule_id = (int) Tools::getValue('cart_rule_id');
		
		if(!$cart_rule_id){
			$response = array('success' => false, 'message' => '');
			die(Tools::jsonEncode($response));
		}
		
		$sql = 'SELECT ocr.id_order, o.total_paid, o.date_add, ocr.value, o.reference, c.firstname, c.lastname
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
}
