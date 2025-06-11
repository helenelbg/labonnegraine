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
class FlashsalesPageModuleFrontController extends ModuleFrontController
{
    /**
     *  @var flashsales
     */
    public $module;

    /**
     * @var FlashSaleListingFrontController
     */
    public $adapter;

    /**
     * @var int|null
     */
    public $id_flash_sale;

    public $filter;

    public function __construct()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->adapter = new FlashSaleListingFrontController($this);
        }

        parent::__construct();

        $this->filter = 'page';
        $this->id_flash_sale = Tools::getvalue('id_flash_sale') ? (int) Tools::getvalue('id_flash_sale') : null;
    }

    public function initFlashSaleDetails()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->adapter->init();
        } else {
            $this->productSort();

            $nbProducts = (int) FlashSale::getNbProducts($this->id_flash_sale);
            $this->pagination($nbProducts);

            $products = FlashSale::getAllProducts((int) $this->context->language->id, $this->id_flash_sale, $this->filter, false, $this->p - 1, $this->n, $this->orderBy, $this->orderWay);

            $this->addColorsToProductList($products);

            $this->context->smarty->assign([
                'products' => $products,
                'nbProducts' => $nbProducts,
                'request' => $this->context->link->getModuleLink('flashsales', 'page', ['id_flash_sale' => $this->id_flash_sale]),
                'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
                'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
                'comparator_max_item' => Configuration::get('PS_COMPARATOR_MAX_ITEM'),
            ]);
        }
    }

    public function initFlashSaleListing()
    {
        $flash_sales = FlashSale::getFlashSales((int) $this->context->language->id, $this->filter);

        if (is_array($flash_sales) && count($flash_sales)) {
            foreach ($flash_sales as $key => $flash_sale) {
                if (!($products = $this->module->getProducts($this->filter, (int) $flash_sale['id_flash_sale']))) {
                    unset($flash_sales[$key]);
                    continue;
                }

                $flash_sales[$key]['products'] = $products;
                $banner = _PS_MODULE_DIR_ . $this->module->name . '/views/img/banner/' . (int) $flash_sale['id_flash_sale'] . '.jpg';
                $flash_sales[$key]['banner'] = file_exists($banner)
                    ? $this->context->link->getMediaLink(_MODULE_DIR_ . $this->module->name . '/views/img/banner/' . (int) $flash_sale['id_flash_sale'] . '.jpg') . '?' . filemtime($banner)
                    : false;
            }
        } else {
            $flash_sales = [];
        }

        $this->context->smarty->assign([
            'flash_sales' => $flash_sales,
            'txt' => Configuration::get('FLASHSALE_COUNTDOWN_STRING_HOME_PAGE', (int) $this->context->language->id),
        ]);
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->id_flash_sale) {
            $this->initFlashSaleDetails();

            $flashSale = new FlashSale((int) $this->id_flash_sale);
            $banner_url = _PS_MODULE_DIR_ . $this->module->name . '/views/img/banner/' . (int) $flashSale->id . '.jpg';
            $banner = file_exists($banner_url)
                ? $this->context->link->getMediaLink(_MODULE_DIR_ . $this->module->name . '/views/img/banner/' . (int) $flashSale->id . '.jpg') . '?' . filemtime($banner_url)
                : false;

            $this->context->smarty->assign([
                'flashsale_name' => $flashSale->name[Context::getContext()->language->id],
                'flashsale_description' => $flashSale->description[Context::getContext()->language->id],
                'flashsale_banner' => $banner,
            ]);
        } else {
            $this->initFlashSaleListing();
        }

        $this->context->smarty->assign([
            'id_flash_sale' => $this->id_flash_sale,
            'layout' => 'page',
        ]);

        $template = version_compare(_PS_VERSION_, '1.7', '>=') ? 'module:flashsales/views/templates/front/1.7/page.tpl' : '1.6/page.tpl';
        $this->setTemplate($template);
    }

    public function setMedia()
    {
        parent::setMedia();

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            $this->addCSS(_THEME_CSS_DIR_ . 'category.css');
            $this->addCSS(_THEME_CSS_DIR_ . 'product_list.css');
            if (Configuration::get('PS_COMPARATOR_MAX_ITEM')) {
                $this->addJS(_THEME_JS_DIR_ . 'products-comparison.js');
            }
        }

        return true;
    }
}
