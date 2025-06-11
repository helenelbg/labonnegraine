<?php

class OrderControl extends ObjectModel
{

    public $id_order_control;
    public $id_order;
    public $id_employee;
    public $date_add;
    public $date_validation; 
    public $state;
    public static $definition = array(
        'table' => 'order_control',
        'primary' => 'id_order_control',
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_employee' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'validate' => array('type' => self::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_validation' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    public static function getByIdOrder($id_order = 0)
    {
        if (intval($id_order) != 0)
        {
            $id_order_control = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_order_control`
			FROM `' . _DB_PREFIX_ . 'order_control` 
			WHERE `id_order` = ' . intval($id_order));

            return new OrderControl($id_order_control);
        }
        else
        {
            return false;
        }
    }

    public static function getIdOrder($id_order_control)
    {
        $control = new OrderControl($id_order_control);
        return $control->id_order;
    }

    public function isValid()
    {
        $order_products_control = $this->getProductsControl();

        if (empty($order_products_control))
        {
            return false;
        }
        $total_prepared = 0;
        foreach ($order_products_control as $product_control)
        {
            $total_prepared += $product_control['quantity_prepared'];
            if ($product_control['validate'] != 1)
            {
                return false;
            }
        }
        if ($total_prepared != $this->getTotalProductOrder())
        {
            return false;
        }
        return true;
    }

    public function delete()
    {
        $order_products_control = $this->getProductsControl();

        foreach ($order_products_control as $product_control)
        {
            $OrderProductControl = new OrderProductsControl($product_control['id_order_product_control']);
            $OrderProductControl->delete();
        }
        return parent::delete();
    }

    public function getProductValidationDetails($id_product, $id_product_attribute)
    {
        $sql = 'SELECT *
            FROM `' . _DB_PREFIX_ . 'order_products_control` 
            WHERE `id_order_control` = ' . intval($this->id_order_control) . '
            AND `id_product` = ' . intval($id_product) . '
            AND `id_product_attribute` = ' . intval($id_product_attribute);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
    }

    public function getProductsControl()
    {
        $sql = 'SELECT *
            FROM `' . _DB_PREFIX_ . 'order_products_control` 
            WHERE `id_order_control` = ' . intval($this->id_order_control);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getTotalProductOrder()
    {
        $sql = 'SELECT SUM(product_quantity)
            FROM `' . _DB_PREFIX_ . 'order_detail` 
            WHERE product_id <> 3063 AND `id_order` = ' . intval($this->id_order);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

}
