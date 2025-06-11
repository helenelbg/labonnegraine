<?php

class OrderHistory extends OrderHistoryCore
{
    public static function getLastOrderState($id_order)
	{
		Tools::displayAsDeprecated();
		$id_order_state = Db::getInstance()->getValue('
		SELECT `id_order_state`
		FROM `'._DB_PREFIX_.'order_history`
		WHERE `id_order` = '.(int)$id_order.'
		ORDER BY `date_add` DESC, `id_order_history` DESC');

		// returns false if there is no state
		if (!$id_order_state)
			return false;

		// else, returns an OrderState object
		return new OrderState($id_order_state, Configuration::get('PS_LANG_DEFAULT'));
	}
}