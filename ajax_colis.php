<?php
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;

include('config/config.inc.php');
require 'init.php';

$id_category = $_POST['id_category'];
$semaine = $_POST['semaine'];

if ( (int)$semaine >= 0 )
{
    $req_delete = 'DELETE FROM ps_custom_delivery WHERE id_cart = ' . $context->cart->id . ' AND id_category = '.(int) $id_category.';';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_delete);

    //if ( $semaine != date('W') )
    //{
        $req_insert = 'INSERT INTO ps_custom_delivery SET id_cart = ' . $context->cart->id . ', id_category = '.(int) $id_category.', semaine = '.(int) $semaine.';';
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_insert);
    //}
    $context->cart->getProducts(true);

    $cart_presenterEC = new CartPresenter();

    $context->smarty->assign(
        array(
            'cart' => $cart_presenterEC->present($context->cart),
        )
    );
    echo $context->smarty->fetch(_PS_THEME_DIR_.'templates/checkout/_partials/steps/colis.tpl');
    //echo true;
}
else
{
    echo false;
}
?>