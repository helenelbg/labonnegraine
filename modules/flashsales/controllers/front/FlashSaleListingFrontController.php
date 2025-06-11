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

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class FlashSaleListingFrontController extends ProductListingFrontController
{
    public $controller;

    public function __construct($controller)
    {
        parent::__construct();
        $this->controller = $controller;
    }

    /**
     * Initializes controller.
     *
     * @see FrontController::init()
     *
     * @throws PrestaShopException
     */
    public function init()
    {
        $this->doProductSearch('module:flashsales/views/templates/front/1.7/page.tpl');
    }

    protected function doProductSearch($template, $params = [], $locale = null)
    {
        if ($this->controller->ajax) {
            ob_end_clean();
            header('Content-Type: application/json');
            $this->ajaxDie(json_encode($this->getAjaxProductSearchVariables()));
        } else {
            $variables = $this->getProductSearchVariables();
            $this->context->smarty->assign([
                'listing' => $variables,
            ]);
            $this->controller->setTemplate($template, $params, $locale);
        }
    }

    protected function prepareProductArrayForAjaxReturn(array $products)
    {
        return $products;
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setQueryType('flash-sales')
            ->setSortOrder(new SortOrder('product', 'name', 'asc'))
        ;

        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new FlashSaleProductSearchProvider(
            $this->getTranslator(),
            new FlashSale((int) $this->controller->id_flash_sale)
        );
    }

    public function getListingLabel()
    {
        return Configuration::get('FLASHSALE_TITLE_FLASHSALE_PAGE', (int) $this->context->language->id);
    }
}
