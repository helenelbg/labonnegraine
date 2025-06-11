<?php

//if (!defined('STORE_COMMANDER'))
//{
//    exit;
//}

use Sc\service\Process\Process;
use Sc\service\Process\ProcessCollection;
use Sc\Service\Shippingbo\Process\ShippingboCollect;
use Sc\Service\Shippingbo\Process\ShippingboImport;
use Sc\Service\Shippingbo\Process\ShippingboMatch;
use Sc\Service\Shippingbo\ShippingboService;
session_write_close();


header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header("Access-Control-Allow-Origin: *");
header('X-Accel-Buffering: no'); // dsactive le buffer nginx

ignore_user_abort(false); // Stops PHP from checking for user disconnect

try
{
    $shippingBoService = ShippingboService::getInstance();

    // last collect date
    $lastCollectDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $shippingBoService->getConfigValue('lastSyncedAt'), new DateTimeZone(SCI::getConfigurationValue('PS_TIMEZONE')));

    /* ----------------------- */
    // Quess SBO Product number
    /* ----------------------- */
//    $collectProductTotal = new ShippingboCollect($shippingBoService);

    /* ----------------------- */
    // FETCH SBO DATA
    /* ----------------------- */
    /* products */
    $collectProducts = new ShippingboCollect($shippingBoService);
    $fetchProducts = new Process($collectProducts);
    $fetchProducts->setMethod('get')
//                  ->setTotal($collectProductTotal->getTotalProducts())
                  ->setMethodArguments(['products', $lastCollectDate])
    ;

    /* packs */
    $fetchPacks = new Process(new ShippingboCollect($shippingBoService));
    $fetchPacks->setMethod('get')
    ->setMethodArguments(['packs', $lastCollectDate]);

    /* AdditionalReferences */
    $fetchPackComponents = new Process(new ShippingboCollect($shippingBoService));
    $fetchPackComponents->setMethod('get')
    ->setMethodArguments(['pack_components', $lastCollectDate]);

    /* pack_components */
    $fetchAdditionalRefs = new Process(new ShippingboCollect($shippingBoService));
    $fetchAdditionalRefs->setMethod('get')
    ->setMethodArguments(['additional_references', $lastCollectDate]);

    /* ----------------------- */
    // updates from SBO ?
    /* ----------------------- */
    /* products */
    $importProducts = new ShippingboImport($shippingBoService);
    $importProducts->setFieldsToImport((array) json_decode($shippingBoService->getConfigValue('defaultDataImport')));
    $updateProducts = new Process($importProducts);
    $updateProducts->setMethod('updatePS')
    ->setMethodArguments(['products'])
;
    /* packs */
    $importPacks = new ShippingboImport($shippingBoService);
    $importProducts->setFieldsToImport((array) json_decode($shippingBoService->getConfigValue('defaultDataImport')));
    $updatePacks = new Process($importPacks);
    $updatePacks->setMethod('updatePS')
    ->setMethodArguments(['packs'])
;

    /* batches */
    $importBatches = new ShippingboImport($shippingBoService);
    $importProducts->setFieldsToImport((array) json_decode($shippingBoService->getConfigValue('defaultDataImport')));
    $updateBatches = new Process($importBatches);
    $updateBatches->setMethod('updatePS')
    ->setMethodArguments(['batches'])
;

    /* ----------------------- */
    // matching reference between PS and SBO and save id_sbo to relation table
    /* ----------------------- */
    /* remove unwanted relation due to suppression in PS */
    $removeRelations = new Process(new ShippingboMatch($shippingBoService));
    $removeRelations->setMethod('removeRelations');
    /* add missing relations in case of ref added in PS */
    $addMissingRelations = new Process(new ShippingboMatch($shippingBoService));
    $addMissingRelations->setMethod('addMissingRelations');
    /* try to match SBO <-> PS */
    $matchProducts = new Process(new ShippingboMatch($shippingBoService));
    $matchProducts->setMethod('matchProducts');

    /* ----------------------- */
    /* RUN */
    /* ----------------------- */

    $processCollection = new ProcessCollection(Tools::getValue('start_process'), Tools::getValue('start_iteration'));
    $processCollection
    ->setLogger($shippingBoService->getLogger())
//    ->setLastRunAt($lastCollectDate)
//    ->setDelayBetweenProcesses(400000)
    ->onComplete($shippingBoService, 'onSyncComplete')
    ->add($fetchProducts)
    ->add($fetchPacks)
    ->add($fetchPackComponents)
    ->add($fetchAdditionalRefs)
    ->add($updateProducts)
    ->add($updateBatches)
    ->add($updatePacks)
    ->add($removeRelations)
    ->add($addMissingRelations)
    ->add($matchProducts)
    ->run();

    $shippingBoService->setConfig(['lastSyncedAt' => date('Y-m-d H:i:s')]);
}
catch (Exception $e)
{
    echo $e->getMessage();
}
