<?php

class OrderProductsControl extends ObjectModel
{

    public $id_order_product_control;
    public $id_order_control;
    public $id_product;
    public $id_product_attribute;
    public $quantity_prepared;
    public $validate;
    public static $definition = array(
        'table' => 'order_products_control',
        'primary' => 'id_order_product_control',
        'fields' => array(
            'id_order_control' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'quantity_prepared' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'validate' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
        ),
    );

    public function getProductInOrder($id_order)
    {
        if (intval($id_order) != 0 && intval($this->id_product) != 0)
        {
            $sql = '
                    SELECT `id_order_detail`
                    FROM `' . _DB_PREFIX_ . 'order_detail` 
                    WHERE `id_order` = ' . intval($id_order) . '
                    AND `product_id` = ' . intval($this->id_product) . '
                    AND `product_attribute_id` = ' . intval($this->id_product_attribute);

            $id_order_detail = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $orderDetail = new OrderDetail($id_order_detail);
            return $orderDetail;
        }
        else
        {
            return false;
        }
    }

    public static function getByProduct($id_order_control, $id_product, $id_product_attribute = 0)
    {
        if (intval($id_order_control) != 0 && intval($id_product) != 0)
        {
            $id_order_product_control = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_order_product_control`
			FROM `' . _DB_PREFIX_ . 'order_products_control` 
			WHERE `id_order_control` = ' . intval($id_order_control) . '
			AND `id_product` = ' . intval($id_product) . '
			AND `id_product_attribute` = ' . intval($id_product_attribute));

            return new OrderProductsControl($id_order_product_control);
        }
        else
        {
            return false;
        }
    }

}
