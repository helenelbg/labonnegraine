<?php
/**
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*/

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use Symfony\Component\Translation\TranslatorInterface;

class FlashSaleProductSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $flashsale;

    public function __construct(
        TranslatorInterface $translator,
        FlashSale $flashsale
    ) {
        $this->translator = $translator;
        $this->flashsale = $flashsale;
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        if (!$products = FlashSale::getAllProducts(
            $context->getIdLang(),
            $this->flashsale->id,
            'page',
            false,
            $query->getPage() - 1,
            $query->getResultsPerPage(),
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay()
        )) {
            $products = [];
        }

        $count = (int) FlashSale::getNbProducts($this->flashsale->id, 'page');

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            $result->setAvailableSortOrders(
                [
                    (new SortOrder('product', 'name', 'asc'))->setLabel(
                        $this->translator->trans('Name, A to Z', [], 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'name', 'desc'))->setLabel(
                        $this->translator->trans('Name, Z to A', [], 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'price', 'asc'))->setLabel(
                        $this->translator->trans('Price, low to high', [], 'Shop.Theme.Catalog')
                    ),
                    (new SortOrder('product', 'price', 'desc'))->setLabel(
                        $this->translator->trans('Price, high to low', [], 'Shop.Theme.Catalog')
                    ),
                ]
            );
        }

        return $result;
    }
}
