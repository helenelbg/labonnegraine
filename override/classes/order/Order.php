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

	public function getProductsDetail()
    {
        // The `od.ecotax` is a newly added at end as ecotax is used in multiples columns but it's the ecotax value we need
        $sql = 'SELECT p.*, ps.*, od.*';
        $sql .= ' FROM `%sorder_detail` od';
        $sql .= ' LEFT JOIN `%sproduct` p ON (p.id_product = od.product_id)';
        $sql .= ' LEFT JOIN `%sproduct_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)';
        $sql .= ' WHERE od.`id_order` = %d ORDER BY p.reference ASC';
        $sql = sprintf($sql, _DB_PREFIX_, _DB_PREFIX_, _DB_PREFIX_, (int) $this->id);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

	public static function getOrderIdsByStatusByZone($id_order_state, $limit = 0, $zone = 0, $id_order = 0)
    {
        Tools::displayAsDeprecated();

		$liste_zone = array(
			1 => array(
				'0-', '1-', '2-', '3-', '4-'
			),
			2 => array(
				'5-'
			),
			3 => array(
				'6-', '7-', '8-'
			),
			4 => array(
				'9-', '10-', '11-', '13-'
			),
			5 => array(
				'14-', '15-', 'M-'
			)
		);

		if ( $zone > 0 && isset($liste_zone[$zone]) ) // Zones 1 à 5
		{
			$close = '';
			if ( $limit > 0 )
			{
				$close = ' LIMIT 0, '.$limit.';';
			}

			$close_zone = '';
			foreach($liste_zone as $num_zone => $zone_detail)
			{
				if ( $num_zone != $zone )
				{
					foreach($zone_detail as $ref_af)
					{
						if ( !empty($close_zone) )
						{
							$close_zone .= ' OR ';
						}
						$close_zone .= '(od.product_reference LIKE "'.$ref_af.'%")';
					}
				}
			}
			if ( !empty($close_zone) )
			{
				$close_zone .= ' OR ';
			}
			$close_zone .= '(od.product_id = 1858)';
			$close_zone .= ' OR (od.product_id IN (SELECT id_product FROM ps_category_product WHERE id_category IN (129,135,132,131,133,134,213,299,338)))';

			if ( $id_order_state == 0 && $id_order > 0 )
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON (o.id_carrier = c.id_carrier) 
					WHERE o.`id_order` = ' . (int) $id_order . ' AND c.id_reference <> 342
					AND o.id_order NOT IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND ('.$close_zone.')
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 WHERE od2.id_order = ' . (int) $id_order . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}
			else 
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON (o.id_carrier = c.id_carrier) 
					WHERE o.`current_state` = ' . (int) $id_order_state . ' AND c.id_reference <> 342
					AND o.id_order NOT IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone.')
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 LEFT JOIN ' . _DB_PREFIX_ . 'orders o3 ON (od2.id_order = o3.id_order) WHERE o3.`current_state` = ' . (int) $id_order_state . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}

			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

			$orders = [];
			foreach ($result as $order) {
				$orders[] = (int) $order['id_order'];
			}
		}
		elseif ( $zone == -4 ) // Rosiers mixte (ne conserve que la zone 1)
		{
			$close = '';
			if ( $limit > 0 )
			{
				$close = ' LIMIT 0, '.$limit.';';
			}
			
			$close_zone_rosiers = '(od.product_id IN (SELECT id_product FROM ps_category_product WHERE id_category IN (129,135,132,131,133,134,213,299,338)))';

			if ( $id_order_state == 0 && $id_order > 0 )
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON (o.id_carrier = c.id_carrier) 
					WHERE o.`id_order` = ' . (int) $id_order . '
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND ('.$close_zone_rosiers.')
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 WHERE od2.id_order = ' . (int) $id_order . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}
			else 
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON (o.id_carrier = c.id_carrier) 
					WHERE o.`current_state` = ' . (int) $id_order_state . '
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone_rosiers.')
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 LEFT JOIN ' . _DB_PREFIX_ . 'orders o3 ON (od2.id_order = o3.id_order) WHERE o3.`current_state` = ' . (int) $id_order_state . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}

					$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

			$orders = [];
			foreach ($result as $order) {
				$orders[] = (int) $order['id_order'];
			}
		}
		elseif ( $zone == -2 ) // Lettre verte (ne conserve que la zone 1)
		{
			$close = '';
			if ( $limit > 0 )
			{
				$close = ' LIMIT 0, '.$limit.';';
			}

			$close_zone = '';
			foreach($liste_zone as $num_zone => $zone_detail)
			{
				if ( $num_zone != 1 )
				{
					foreach($zone_detail as $ref_af)
					{
						if ( !empty($close_zone) )
						{
							$close_zone .= ' OR ';
						}
						$close_zone .= '(od.product_reference LIKE "'.$ref_af.'%")';
					}
				}
			}
			if ( $id_order_state == 0 && $id_order > 0 )
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON (o.id_carrier = c.id_carrier) 
					WHERE o.`id_order` = ' . (int) $id_order . ' AND c.id_reference = 342
					AND o.id_order NOT IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND ('.$close_zone.')
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 WHERE od2.id_order = ' . (int) $id_order . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}
			else 
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON (o.id_carrier = c.id_carrier) 
					WHERE o.`current_state` = ' . (int) $id_order_state . ' AND c.id_reference = 342
					AND o.id_order NOT IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone.')
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 LEFT JOIN ' . _DB_PREFIX_ . 'orders o3 ON (od2.id_order = o3.id_order) WHERE o3.`current_state` = ' . (int) $id_order_state . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}

			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

			$orders = [];
			foreach ($result as $order) {
				$orders[] = (int) $order['id_order'];
			}
		}
		elseif ( $zone == -3 ) // Box (ne conserve que le produit 1858)
		{
			$close = '';
			if ( $limit > 0 )
			{
				$close = ' LIMIT 0, '.$limit.';';
			}

			if ( $id_order_state == 0 && $id_order > 0 )
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o
					WHERE o.`id_order` = ' . (int) $id_order . '
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND (od.product_id = 1858)
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 WHERE od2.id_order = ' . (int) $id_order . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}
			else 
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o
					WHERE o.`current_state` = ' . (int) $id_order_state . '
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND (od.product_id = 1858)
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 LEFT JOIN ' . _DB_PREFIX_ . 'orders o3 ON (od2.id_order = o3.id_order) WHERE o3.`current_state` = ' . (int) $id_order_state . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}

			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

			$orders = [];
			foreach ($result as $order) {
				$orders[] = (int) $order['id_order'];
			}
		}
		elseif ( $zone == -1 ) // Zones Mixte
		{
			$close = '';
			if ( $limit > 0 )
			{
				$close = ' LIMIT 0, '.$limit.';';
			}

			for ($czt = 1; $czt <= 5; $czt++)
			{
				${'close_zone'.$czt} = '';
				foreach($liste_zone as $num_zone => $zone_detail)
				{
					if ( $num_zone != $czt )
					{
						foreach($zone_detail as $ref_af)
						{
							if ( !empty(${'close_zone'.$czt}) )
							{
								${'close_zone'.$czt} .= ' OR ';
							}
							${'close_zone'.$czt} .= '(od.product_reference LIKE "'.$ref_af.'%")';
						}
					}
				}
			}

			$close_zone_rosier = '(od.product_id IN (SELECT id_product FROM ps_category_product WHERE id_category IN (129,135,132,131,133,134,213,299,338)))';
			
			if ( $id_order_state == 0 && $id_order > 0 )
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o
					WHERE o.`id_order` = ' . (int) $id_order . '
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND ('.$close_zone1.')
					)
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND ('.$close_zone2.')
					)
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND ('.$close_zone3.')
					)
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND ('.$close_zone4.')
					)
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND ('.$close_zone5.')
					)
					AND o.id_order NOT IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`id_order` = ' . (int) $id_order . ' AND ('.$close_zone_rosier.')
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 WHERE od2.id_order = ' . (int) $id_order . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}
			else 
			{
				$sql = 'SELECT id_order
					FROM ' . _DB_PREFIX_ . 'orders o
					WHERE o.`current_state` = ' . (int) $id_order_state . '
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone1.')
					)
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone2.')
					)
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone3.')
					)
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone4.')
					)
					AND o.id_order IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone5.')
					)
					AND o.id_order NOT IN (
						SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone_rosier.')
					)
					AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 LEFT JOIN ' . _DB_PREFIX_ . 'orders o3 ON (od2.id_order = o3.id_order) WHERE o3.`current_state` = ' . (int) $id_order_state . ' AND od2.id_warehouse IN (0, '.date('W').'))
					' . Shop::addSqlRestriction(false, 'o') . '
					ORDER BY invoice_date ASC'.$close;
			}
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

			$orders = [];
			foreach ($result as $order) {
				$orders[] = (int) $order['id_order'];
			}
		}
		else 
		{
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
		}

        return $orders;
    }

	public static function getOrderIdsByStatusByDeuxZones($id_order_state, $limit = 0, $zone1 = 0, $zone2 = 0)
    {
        Tools::displayAsDeprecated();

		$liste_zone = array(
			1 => array(
				'0-', '1-', '2-', '3-', '4-'
			),
			2 => array(
				'5-'
			),
			3 => array(
				'6-', '7-', '8-'
			),
			4 => array(
				'9-', '10-', '11-', '13-'
			),
			5 => array(
				'14-', '15-', 'M-'
			)
		);

		$close = '';
		if ( $limit > 0 )
		{
			$close = ' LIMIT 0, '.$limit.';';
		}

		$close_zone = '';
		$close_zone1 = '';
		$close_zone2 = '';
		foreach($liste_zone as $num_zone => $zone_detail)
		{
			if ( $num_zone != $zone1 && $num_zone != $zone2 )
			{
				foreach($zone_detail as $ref_af)
				{
					if ( !empty($close_zone) )
					{
						$close_zone .= ' OR ';
					}
					$close_zone .= '(od.product_reference LIKE "'.$ref_af.'%")';
				}
			}
		}
		foreach($liste_zone as $num_zone => $zone_detail)
		{
			if ( $num_zone == $zone1 )
			{
				foreach($zone_detail as $ref_af)
				{
					if ( !empty($close_zone1) )
					{
						$close_zone1 .= ' OR ';
					}
					$close_zone1 .= '(od.product_reference LIKE "'.$ref_af.'%")';
				}
			}
		}
		foreach($liste_zone as $num_zone => $zone_detail)
		{
			if ( $num_zone == $zone2 )
			{
				foreach($zone_detail as $ref_af)
				{
					if ( !empty($close_zone2) )
					{
						$close_zone2 .= ' OR ';
					}
					$close_zone2 .= '(od.product_reference LIKE "'.$ref_af.'%")';
				}
			}
		}
		if ( !empty($close_zone) )
		{
			$close_zone .= ' OR ';
		}
		$close_zone .= '(od.product_id = 1858)';
		$close_zone .= ' OR (od.product_id IN (SELECT id_product FROM ps_category_product WHERE id_category IN (129,135,132,131,133,134,213,299,338)))';

		$sql = 'SELECT id_order
			FROM ' . _DB_PREFIX_ . 'orders o LEFT JOIN ' . _DB_PREFIX_ . 'carrier c ON (o.id_carrier = c.id_carrier) 
			WHERE o.`current_state` = ' . (int) $id_order_state . ' AND c.id_reference <> 342
			AND o.id_order NOT IN (
				SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone.')
			)
			AND o.id_order IN (
				SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone1.')
			)
			AND o.id_order IN (
				SELECT distinct od.id_order FROM ' . _DB_PREFIX_ . 'order_detail od LEFT JOIN ' . _DB_PREFIX_ . 'orders o2 ON (od.id_order = o2.id_order) WHERE o2.`current_state` = ' . (int) $id_order_state . ' AND ('.$close_zone2.')
			)
			AND o.id_order IN (SELECT distinct od2.id_order FROM ' . _DB_PREFIX_ . 'order_detail od2 LEFT JOIN ' . _DB_PREFIX_ . 'orders o3 ON (od2.id_order = o3.id_order) WHERE o3.`current_state` = ' . (int) $id_order_state . ' AND od2.id_warehouse IN (0, '.date('W').', '.(date('W')-1).', '.(date('W')-2).', '.(date('W')-3).'))
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
