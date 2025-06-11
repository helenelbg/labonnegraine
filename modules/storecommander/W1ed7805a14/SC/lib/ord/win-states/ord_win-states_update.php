<?php

$idLang = (int) Tools::getValue('id_lang', 0);
$idState = (int) Tools::getValue('id_order_state', 0);
$value = (int) Tools::getValue('value', 0);
$action = Tools::getValue('action');
$idShop = (int) Tools::getValue('id_shop', SCI::getSelectedShop());

// recuperation des configs SC
$scHideOrderStatesConfig = unserialize(SCI::getConfigurationValue('SC_HIDE_ORDER_STATES', ''));

if (empty($scHideOrderStatesConfig))
{
    $scHideOrderStatesConfig = array();
}

if ($idShop === 0)
{
    $sql = 'SELECT id_shop FROM `'._DB_PREFIX_.'shop`';
    $shops = Db::getInstance()->executeS($sql);
    $selectedShops = array_map(function ($e)
    {
        return $e['id_shop'];
    }, $shops);
    $selectedShops[] = 0; //on enregistre quand meme le statut pour Toutes ls boutiques
}
else
{
    $selectedShops = (array) $idShop;
}
// affectation
if ($value === 1)
{
    foreach ($selectedShops as $shopId)
    {
        $scHideOrderStatesConfig[$idState]['shops'][] = $shopId;
    }
}
elseif (isset($scHideOrderStatesConfig[$idState]))
{
    foreach ($selectedShops as $shopId)
    {
        $index = array_search($shopId, $scHideOrderStatesConfig[$idState]['shops']);
        unset($scHideOrderStatesConfig[$idState]['shops'][$index]);
    }
}
$scHideOrderStatesConfig[$idState]['shops'] = array_unique($scHideOrderStatesConfig[$idState]['shops']);

SCI::updateConfigurationValue('SC_HIDE_ORDER_STATES', serialize($scHideOrderStatesConfig));
$scConfig = unserialize(SCI::getConfigurationValue('SC_HIDE_ORDER_STATES'));

if (stristr($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml'))
{
    header('Content-type: application/xhtml+xml');
}
else
{
    header('Content-type: text/xml');
}
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo '<data>';
echo "<action type='".$action."' sid='".$idState."' tid='".$idState."'/>";
echo isset($debug) && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>' : '';
echo '</data>';
