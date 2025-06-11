<?php

class Order extends OrderCore {
    public function getProductsDetail()
    {
        error_log('LA');
        // The `od.ecotax` is a newly added at end as ecotax is used in multiples columns but it's the ecotax value we need
        $sql = 'SELECT p.*, ps.*, od.*';
        $sql .= ' FROM `%sorder_detail` od';
        $sql .= ' LEFT JOIN `%sproduct` p ON (p.id_product = od.product_id)';
        $sql .= ' LEFT JOIN `%sproduct_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)';
        $sql .= ' WHERE od.`id_order` = %d ORDER BY p.reference ASC';
        $sql = sprintf($sql, _DB_PREFIX_, _DB_PREFIX_, _DB_PREFIX_, (int) $this->id);

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }
}
?>