<?php
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;

include('config/config.inc.php');
require 'init.php';

if ( (int)$context->cart->id > 0 )
{
    $req_delete = 'DELETE FROM ps_custom_delivery WHERE id_cart = ' . $context->cart->id . ';';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_delete);
   
    $req_optim = 'UPDATE ps_cart SET optim = 0 WHERE id_cart = ' . $context->cart->id . ';';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req_optim);

    $context->cart->getProducts(true);

    $cart_presenterEC = new CartPresenter();

    $context->smarty->assign(
        array(
            'cart' => $cart_presenterEC->present($context->cart),
        )
    );
    echo $context->smarty->fetch(_PS_THEME_DIR_.'templates/checkout/_partials/steps/colis.tpl');
}
else
{
    echo false;
}
?>