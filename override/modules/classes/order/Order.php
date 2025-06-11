<?php

class Order extends OrderCore
{
	public static function printPDFIcons($id_order)
	{
		$order = new Order($id_order);
		$orderState = OrderHistory::getLastOrderState($id_order);
		
		if (!Validate::isLoadedObject($orderState) OR !Validate::isLoadedObject($order))
			die(Tools::displayError('Invalid objects'));
			
		echo '<span style="margin-right:5px;" class="btn btn-default">';
		if (($orderState->invoice AND $order->invoice_number))
			echo '<a href="/admin123/index.php/sell/orders/'.(int)($order->id).'/generate-invoice-pdf?_token='.Tools::getAdminToken('AdminModules').'"><i class="material-icons">receipt</i></a>';
		else
			echo '&nbsp;';
		echo '</span>';
		
		echo '<span style="margin-right:5px" class="btn btn-default">';
		if ($orderState->delivery AND $order->delivery_number)
			echo '<a href="pdf.php?id_delivery='.(int)($order->delivery_number).'"><i class="material-icons">local_shipping</i></a>';
		else echo '&nbsp;';
		echo '</span>';
	}

	public static function getOrderIdsByStatus($id_order_state, $limit = 0)
    {
        Tools::displayAsDeprecated();

		$close = '';
		if ( $limit > 0 )
		{
			$close = ' LIMIT 0, '.$limit.';';
		}

        $sql = 'SELECT id_order
                FROM ' . _DB_PREFIX_ . 'orders o
                WHERE o.`current_state` = ' . (int) $id_order_state . '
                ' . Shop::addSqlRestriction(false, 'o') . '
                ORDER BY invoice_date ASC'.$close;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        $orders = [];
        foreach ($result as $order) {
            $orders[] = (int) $order['id_order'];
        }

        return $orders;
    }
}
