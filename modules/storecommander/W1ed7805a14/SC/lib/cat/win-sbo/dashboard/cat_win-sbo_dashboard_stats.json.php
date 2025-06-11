<?php

if (!defined('STORE_COMMANDER'))
{
    exit;
}
use Sc\Service\Shippingbo\ShippingboService;

$shippingBoService = ShippingboService::getInstance();

$extra = [];
try
{
    $stats = $shippingBoService->getStatsRepository()->getOverview(
        $shippingBoService->getStatsRepository()->getAll()
    );
    $extra = $stats;
}
catch (Exception $e)
{
    $shippingBoService->addError($e);
}
finally
{
    exit($shippingBoService->sendResponse('success', $extra));
}
