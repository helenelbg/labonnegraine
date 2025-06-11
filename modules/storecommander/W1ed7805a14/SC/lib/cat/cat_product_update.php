<?php


if(Tools::getValue('action', false) === 'stocks' && version_compare(_PS_VERSION_, '1.7.2.0', '>=')){
    $stock_manager = new PrestaShop\PrestaShop\Adapter\StockManager();
    $stock_manager->updatePhysicalProductQuantity(
        SCI::getSelectedShop(),
        (int) Configuration::get('PS_OS_ERROR'),
        (int) Configuration::get('PS_OS_CANCELED')
    );
}
