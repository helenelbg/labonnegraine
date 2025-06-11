<?php

include('config/config.inc.php');
require 'init.php';

if ( (int)$context->cart->id > 0 )
{
    $req = 'SELECT count(*) as nb FROM ps_custom_delivery WHERE id_cart = ' . $context->cart->id . ';';
    $rangee = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

    if ( isset($rangee[0]['nb']) && $rangee[0]['nb'] > 0 )
    {
        echo '1';
    }
    else
    {
        echo false;
    }
}
else
{
    echo false;
}
?>