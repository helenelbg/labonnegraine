<?php

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;

if (!defined('STORE_COMMANDER')) {
    exit;
}

$pdo = Db::getInstance()->getLink();
if(Tools::getIsset('DEBUG')){
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
$shippingBoService= \Sc\Service\Shippingbo\ShippingboService::getInstance();
$productIdsToTransform = Tools::getValue('product_ids', '');
$productsQuery = new DbQuery();
$productsQuery
    ->select('p.id_product')
    ->from(Product::$definition['table'],'p')
    ->where('p.id_product IN('.pInSQL($productIdsToTransform).')')
    ->leftJoin('product_lang', 'pl', 'pl.id_product = p.id_product')
    ->where('pl.id_shop IN('.pInSQL($shippingBoService->getConfigShopsForPdo()).')')
;
$productsStatement = $pdo->prepare($productsQuery);
$productsStatement->execute(array(
    ':id_shop' => SCI::getSelectedShop(),
));


$productsToTransform = array();
foreach($productsStatement->fetchAll(PDO::FETCH_ASSOC) as $productToTransform){
    $productsToTransform[$productToTransform['id_product']] = new Product($productToTransform['id_product']);
}

// forbidden product types
$forbiddenProducts = array();
foreach ($productsToTransform as $key => $productToTransform) {
    $allowed = false;
    if (version_compare(_PS_VERSION_, '1.7.8.0', '>=')) {
        $allowed = $productToTransform->getProductType() === ProductType::TYPE_STANDARD;
    } else {
        $allowed = $productToTransform->cache_is_pack == 0;
    }
    if (!$allowed) {
        $forbiddenProducts[]  = $productToTransform;
        unset($productsToTransform[$key]);
    }
}

?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
</head>
<body>
<div class="service html_content">


    <?php if (!empty($forbiddenProducts)) { ?>
        <div class="message warning">
            <?php echo _l("These products won't be converted because the type associated is not supported for this operation");?> :
            <ul>
                <?php foreach ($forbiddenProducts as $forbiddenProduct) { ?>
                    <li><?php echo $forbiddenProduct->name[$sc_agent->getIdLang()]; ?> [<?php echo version_compare(_PS_VERSION_, '1.7.8.0', '>=')?$forbiddenProduct->getProductType():'pack'; ?>]</li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <div>
        <?php echo _l('The new product will be used to link new combinations, for instance a product named "T-shirt" will have combinations like \'Size:XL/Color:Blue\',\'Size:XL/Color:Red\',etc. '); ?>
    </div>

    <div id="convert_products_to_combination_form_container" style="width:100%;min-height:90px;"></div>

    <div id="products_to_convert">
        <h2><?php echo ucfirst(_l('combinations to create')); ?></h2>

        <table id="combinations_list" style="min-height:80px;" class="no-rowselected-color">
            <thead>
            <tr>
                <th id="header_product_name" width="250"><?php echo _l('Product name'); ?></th>
                <th id="header_product_ref" width="100"><?php echo ucfirst(_l('combination reference')); ?></th>
                <th id="header_is_default" width="100" type="ra"><?php echo ucfirst(_l('default combination')); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 0;
            foreach ($productsToTransform as $productToTransform) { ?>
                <tr id="<?php echo $productToTransform->id; ?>">
                    <td type="ro"><?php echo $productToTransform->name[$sc_agent->getIdLang()].' ('.$productToTransform->id.')'; ?></td>
                    <td type="ro"><?php echo $productToTransform->reference; ?></td>
                    <td><?php echo (int) $i === 0; ?></td>
                </tr>
            <?php
                $i++;
            } ?>
            </tbody>
        </table>
    </div>


</div>
<ul class="actions">
    <li class="save_btn">
        <button class="btn primary" id="create_combinations">
            <i class="far fa-play"></i>
            <?php echo ucfirst(_l('create combinations')); ?>
        </button>
    </li>
</ul>

</body>
</html>