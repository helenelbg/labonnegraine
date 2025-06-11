<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

if (!defined('_ETS_CROSSSELL_CACHE_DIR_')) 
    define('_ETS_CROSSSELL_CACHE_DIR_',_PS_CACHE_DIR_.'ets_crosssell_cache/');
require_once(dirname(__FILE__) . '/Ets_crosssell_db.php');
require_once(dirname(__FILE__) . '/classes/EtsCrossSellTools.php');
class Ets_crosssell extends Module
{ 
    public $_config_types;
    public $_configs;
    public $_sidebars;
    public $is17 = false;
    public $_sort_options;
    public function __construct()
	{
        $this->name = 'ets_crosssell';
		$this->tab = 'merchandizing';
		$this->version = '2.4.0';
		$this->author = 'PrestaHero';
		$this->need_instance = 0;
		$this->bootstrap = true;
        if(version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;
        $this->module_key = '0d2ff6d8b136b0e02a7c5c446415d6df';
		parent::__construct();
        $this->ps_versions_compliancy = array('min' => '1.6.0.0', 'max' => _PS_VERSION_);
        $this->displayName =$this->l('Cross Selling Pro - Upsell - Shopping cart and all pages');
        $this->description = $this->l('Automated product suggestions based on customer’s interest to display on the shopping cart, product page, order page, etc. Cross-selling Pro (upsell) helps increase the visibility of all products and encourage customers to buy more!');

	}
    public function _defines()
    {
        $this->context->smarty->assign('link',$this->context->link);
        $this->_sort_options=array(
            array(
                'id_option' => 'cp.position asc',
                'name' => $this->l('Popularity')
            ),
            array(
                'id_option' => 'pl.name asc',
                'name' => $this->l('Product name: A-Z')
            ),
            array(
                'id_option' => 'pl.name desc',
                'name' => $this->l('Product name: Z-A')
            ),
            array(
                'id_option' => 'price asc',
                'name' => $this->l('Price: Lowest first')
            ),
            array(
                'id_option' => 'price desc',
                'name' => $this->l('Price: Highest first')
            ),
            array(
                'id_option' => 'p.id_product desc',
                'name' => $this->l('Newest items first')
            ),
        );
        $this->_config_types = array(
            'purchasedtogether' =>array(
                'title' => $this->l('Frequently purchased together'),
                'default'=>1,
                'desc' => $this->l('Display the products that were often purchased in the same cart as the product currently viewed'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'popularproducts' =>array(
                'title'=> $this->l('Popular products'),
                'default'=>1,
                'desc' => $this->l('Popular products of a product category'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'id_category',
                        'type'=>'text',
                        'required' => true,
                        'validate' => 'isunsignedInt',  
                        'default' => Configuration::get('HOME_FEATURED_CAT'),
                        'label' => $this->l('Category whose products will be selected to display'),
                        'desc' => $this->l('Choose the category ID of the products that you would like to display on store front (default: 2 for "Home").'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'mostviewedproducts' => array(
                'title' => $this->l('Most viewed products'),
                'default' => 1,
                'desc' => $this->l('Products which are viewed most by visitors/customers in your store'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'trendingproducts' => array(
                'title' => $this->l('Trending products'),
                'default' => 1,
                'desc' => $this->l('Products which get most sales in a period of time are considered as trending'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name' => 'day',
                        'type' =>'text',
                        'label' => $this->l('Most purchased in (days)'),
                        'required' => true,
                        'default' => 30,
                        'validate' => 'isunsignedInt',
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'topratedproducts' =>array(
                'title' => $this->l('Top rated products'),
                'default'=>1,
                'desc' => $this->l('Products with the highest rating by customers in your store'),
                'warning' => (Module::isInstalled('productcomments') && Module::isEnabled('productcomments')) || (Module::isInstalled('ets_productcomments') && Module::isEnabled('ets_productcomments')) || (Module::isInstalled('ets_reviews') && Module::isEnabled('ets_reviews')) ? false : $this->l('module is not installed on your site. This module is made by PrestaShop and it\'s free. Please install that module to display top rated products to customers'),
                'module_name' => $this->l('Product Comments'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                        
                    )
                )
            ),
            'featuredproducts' =>array(
                'title' => $this->l('Featured products'),
                'default'=>1,
                'desc' => $this->l('Featured products of a category'),
                'setting' => array(
                    array(
                            'name' => 'title',
                            'type' =>'text',
                            'label' => $this->l('Custom title'),
                            'validate'=>'isCleanHtml',
                            'lang'=>true,
                            'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'id_category',
                        'label' => $this->l('Category'),
                        'type' => 'categories',
                        'required' => true,
                        'validate' => 'isunsignedInt',
                        'default' => Configuration::get('HOME_FEATURED_CAT'),
                        'tree' => array(
                            'id'=>Configuration::get('PS_ROOT_CATEGORY'),
                        )
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'youmightalsolike' =>array(
                'title' => $this->l('You might also like'),
                'default'=>1,
                'desc' => $this->l('Suggest products that are related to the product customers are viewing or the products which are put into their shopping cart. Products are selected from a list of related products configured by the store administrator on the product detail pages in the back office.'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                        
                    )
                )
            ),
            'productinthesamecategories' =>array(
                'title' => $this->l('Products in the same category'),
                'default'=>1,
                'desc' => $this->l('Products which are in the same category with the ones customers currently viewing'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'viewedproducts' =>array(
                'title' => $this->l('Viewed products'),
                'default'=>1,
                'desc' => $this->l('Products which visitors/customers recently viewed'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'bestselling' =>array(
                'title'=>$this->l('Best selling'),
                'default'=>1,
                'desc' => $this->l('The top products based on sales'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'newproducts' =>array(
                'title' => $this->l('New products'),
                'default'=>1,
                'desc' => $this->l('The newest products within your online store'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'lang'=>true,
                        'validate'=>'isCleanHtml',
                        'label' => $this->l('Custom title'),
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'specialproducts' =>array(
                'title' => $this->l('Special products'),
                'default'=>1,
                'desc' => $this->l('Products which are discounted on the current time'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'lang'=>true,
                        'validate'=>'isCleanHtml',
                        'label' => $this->l('Custom title'),
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'productinthesamemanufacture' =>array(
                'title'=> $this->l('Product in the same brand'),
                'default' =>1,
                'desc' => $this->l('Products which come from the same manufacturer'),
                'info' => Manufacturer::getManufacturers() ? false : $this->display(__FILE__,'brand.tpl'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name'=>'sort_by_default',
                        'type'=>'select',
                        'label'=> $this->l('Default product sort option'),
                        'options' => array(
                            'query' => $this->_sort_options,
                            'id' => 'id_option',
                            'name' => 'name'
                        ),
                        'default' => 'cp.position asc',
                    ),
                    array(
                        'name'=>'enable_sort_by',
                        'type'=>'switch',
                        'label' => $this->l('Display sort options'),
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    ),
                    array(
                        'name'=>'display_sub_category',
                        'type'=>'switch',
                        'label' => $this->l('Display subcategories filter'),
                        'default'=>1,
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)
                    )
                )
            ),
            'specificproducts' => array(
                'title'=> $this->l('Specific products'),
                'default' =>0,
                'desc' => $this->l('Specific products'),
                'setting' => array(
                    array(
                        'name' => 'title',
                        'type' =>'text',
                        'label' => $this->l('Custom title'),
                        'validate'=>'isCleanHtml',
                        'lang'=>true,
                        'desc' => $this->l('Leave blank to use default title'),
                    ),
                    array(
                        'name' => 'id_products',
                        'type' =>'search',
                        'label' => $this->l('Products'),
                        'placeholder' => $this->l('Search product by ID, name or reference'),
                        'validate'=>'isCleanHtml',
                    ),
                )
            ),
        );
        $id_root_category = Context::getContext()->shop->getCategory();
        $sub_categories_default=array();
        $categories = Category::getChildren($id_root_category,$this->context->language->id,1,$this->context->shop->id);
        if($categories)
        {
            foreach($categories as $category)
                $sub_categories_default[]= $category['id_category'].',';
        }
        $this->_configs = array(
            'home_page' => array( 
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'category_page' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'product_page' => array(
                'purchasedtogether' =>$this->l('Frequently purchased together'),
                'trendingproducts' => $this->l('Trending products'),
                'productinthesamecategories' =>$this->l('Products from the same category'),
                'productinthesamemanufacture' =>$this->l('Products from the same brand'),
                'youmightalsolike' =>$this->l('You might also like'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'viewedproducts' =>$this->l('Viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'quick_view_page' => array(
                'purchasedtogether' =>$this->l('Frequently purchased together'),
                'trendingproducts' => $this->l('Trending products'),
                'productinthesamecategories' =>$this->l('Products from the same category'),
                'productinthesamemanufacture' =>$this->l('Products from the same brand'),
                'youmightalsolike' =>$this->l('You might also like'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'viewedproducts' =>$this->l('Viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'added_popup_page' => array(
                'purchasedtogether' =>$this->l('Frequently purchased together'),
                'trendingproducts' => $this->l('Trending products'),
                'productinthesamecategories' =>$this->l('Products from the same category'),
                'productinthesamemanufacture' =>$this->l('Products from the same brand'),
                'youmightalsolike' =>$this->l('You might also like'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'viewedproducts' =>$this->l('Viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'cart_page' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'order_conf' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'cms_page' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'custom_page' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'contact_page' => array(
                'trendingproducts' => $this->l('Trending products'),
                'mostviewedproducts' => $this->l('Most viewed products'),
                'topratedproducts' =>$this->l('Top rated products'),
                'youmightalsolike' =>$this->l('You might also like'),
                'viewedproducts' =>$this->l('Viewed products'),
                'featuredproducts' =>$this->l('Featured products'),
                'popularproducts' =>$this->l('Popular products'),
                'bestselling' =>$this->l('Best selling'),
                'newproducts' =>$this->l('New products'),
                'specialproducts' =>$this->l('Special products'),
                'specificproducts' => $this->l('Specific products'),
            ),
            'settings'=>array(
                array(
                    'name'=>'ETS_CS_CATEGORY_SUB',
                    'label' => $this->l('Sub categories to filter'),
                    'type' => 'categories',
                    'default' => $sub_categories_default,
                    'use_checkbox'=>true,
                    'tree' => array(
                        'id'=>Configuration::get('PS_ROOT_CATEGORY'),
                        'use_checkbox'=>true,
                        'selected_categories'=> explode(',',Configuration::get('ETS_CS_CATEGORY_SUB')),
                    ),
                    'desc' => $this->l('Customers can filter products by categories'),
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Enable cache'),
                    'name' => 'ETS_CS_ENABLE_CACHE',
                    'default'=>1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			)
                ),
                array(
                    'type' =>'text',
                    'label' => $this->l('Cache lifetime'),
                    'name' => 'ETS_CS_CACHE_LIFETIME',
                    'default'=>24,
                    'suffix' => $this->l('hour(s)'),
                    'col' => '6',
                    'required' => true,
                    'validate' => 'isUnsignedFloat',
                    'form_group_class' => 'ets_cs_cahe_lifetime',
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Display "Out of stock" products'),
                    'name' => 'ETS_CS_OUT_OF_STOCK',
                    'default'=>1,
                    'values' => array(
        				array(
        					'id' => 'active_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'active_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			)
                ),
                array(
                    'type' =>'switch',
                    'label' => $this->l('Exclude free products'),
                    'name' => 'ETS_CS_EXCL_FREE_PRODUCT',
                    'default'=>0,
                    'values' => array(
        				array(
        					'id' => 'ETS_CS_EXCL_FREE_PRODUCT_on',
        					'value' => 1,
        					'label' => $this->l('Yes')
        				),
        				array(
        					'id' => 'ETS_CS_EXCL_FREE_PRODUCT_off',
        					'value' => 0,
        					'label' => $this->l('No')
        				)
        			)
                )
            ),
        ); 
        $this->_sidebars = array(
            'home_page' => $this->l('Homepage'),
            'category_page' => $this->l('Product category page'),
            'product_page' => $this->l('Product details page'),
            'quick_view_page' => $this->l('Product quick view popup'),
            'added_popup_page' => $this->l('Added product popup'),
            'cart_page' => $this->l('Shopping cart page'),
            'order_conf' => $this->l('Order confirmation page'),
            'cms_page' => $this->l('CMS page'),
            'contact_page' => $this->l('Contact page'),
            'custom_page' =>$this->l('Custom page'),
            'settings' => $this->l('General settings'),
                        
        );
    }
    
    /**
	 * @see Module::install()
	 */
    public function install()
	{
	    return parent::install()
        && $this->registerHook('displayBackOfficeHeader') 
        && $this->registerHook('displayHome') 
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayContentWrapperBottom')
        && $this->registerHook('displayProductAdditionalInfo')
        && $this->registerHook('displayRightColumnProduct')
        && $this->registerHook('displayProductPopupAdded')
        && $this->registerHook('displayShoppingCartFooter')
        && $this->registerHook('actionProductAdd')
        && $this->registerHook('actionProductUpdate')
        && $this->registerHook('actionProductDelete')
        && $this->registerHook('actionOrderStatusPostUpdate')
        && $this->registerHook('displayOrderConfirmation')
        && $this->registerHook('displayOrderConfirmation2')
        && $this->registerHook('actionPageCacheAjax')
        && $this->registerHook('displayFooterProduct')
        && $this->registerHook('displayCustomProduct')
        && $this->_registerCustomHooks()
        && Ets_crosssell_db::installDb()
        && $this->installDbDefault();
    }

    public function installDbDefault()
    {
        if(!$this->_sidebars)
            $this->_defines();
        foreach($this->_sidebars as $control=> $sidebar)
        {
            $this->_saveConfig($control,true);
            unset($sidebar);
        }
        Configuration::updateGlobalValue('ETS_CS_HOME_PAGE_LAYOUT','tabgrid');
        Configuration::updateGlobalValue('ETS_CS_HOME_PAGE_TRENDINGPRODUCTS',1);
        Configuration::updateGlobalValue('ETS_CS_HOME_PAGE_MOSTVIEWEDPRODUCTS',1);
        Configuration::updateGlobalValue('ETS_CS_HOME_PAGE_TOPRATEDPRODUCTS',1);
        Configuration::updateGlobalValue('ETS_CS_CATEGORY_PAGE_LAYOUT','tabslide');
        Configuration::updateGlobalValue('ETS_CS_PRODUCT_PAGE_LAYOUT','listslide');
        Configuration::updateGlobalValue('ETS_CS_PRODUCT_PAGE_PURCHASEDTOGETHER',1);
        Configuration::updateGlobalValue('ETS_CS_PRODUCT_PAGE_PRODUCTINTHESAMECATEGORIES',1);
        Configuration::updateGlobalValue('ETS_CS_QUICK_VIEW_PAGE_LAYOUT','tabslide');
        Configuration::updateGlobalValue('ETS_CS_QUICK_VIEW_PAGE_YOUMIGHTALSOLIKE',1);
        Configuration::updateGlobalValue('ETS_CS_ADDED_POPUP_PAGE_LAYOUT','tabslide');
        Configuration::updateGlobalValue('ETS_CS_ADDED_POPUP_PAGE_YOUMIGHTALSOLIKE',1);
        Configuration::updateGlobalValue('ETS_CS_CART_PAGE_LAYOUT','listslide');
        Configuration::updateGlobalValue('ETS_CS_CART_PAGE_YOUMIGHTALSOLIKE',1);
        Configuration::updateGlobalValue('ETS_CS_ORDER_CONF_LAYOUT','listslide');
        Configuration::updateGlobalValue('ETS_CS_ORDER_CONF_YOUMIGHTALSOLIKE',1);
        Configuration::updateGlobalValue('ETS_CS_CMS_PAGE_LAYOUT','listgrid');
        Configuration::updateGlobalValue('ETS_CS_CONTACT_PAGE_LAYOUT','tabgrid');
        Configuration::updateGlobalValue('ETS_CS_CONTACT_PAGE_TRENDINGPRODUCTS',1);
        Configuration::updateGlobalValue('ETS_SP_CLEAR_CACHE_CRS',1);
        if($pages= array_keys($this->_sidebars))
        {
            foreach($pages as $page)
            {
                if($page!='settings' && $page!='custom_page')
                {
                    Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($page).'_ROW_DESKTOP');
                    Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($page).'_ROW_TABLET');
                    Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($page).'_ROW_MOBILE');                
                   if($page=='category_page'|| $page=='contact_page') 
                    {
                        Configuration::updateGlobalValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_DESKTOP',3);
                        Configuration::updateGlobalValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_TABLET',2);
                        Configuration::updateGlobalValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_MOBILE',1);
                    }elseif($page=='quick_view_page' || $page=='added_popup_page' ){
                        Configuration::updateGlobalValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_DESKTOP',4);
                        Configuration::updateGlobalValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_TABLET',2);
                        Configuration::updateGlobalValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_MOBILE',1);
                    }
                    else
                    {
                        Configuration::updateGlobalValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_DESKTOP',4);
                        Configuration::updateGlobalValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_TABLET',3);
                        Configuration::updateGlobalValue('ETS_CS_'.Tools::strtoupper($page).'_ROW_MOBILE',1);
                    }
                }
            }
        }
        Configuration::updateGlobalValue('ETS_CS_HOME_PAGE_NEWPRODUCTS_SORT_BY_DEFAULT','p.id_product desc');
        $this->clearCache();
        return true;
    }
    public function _registerCustomHooks(){
        $hooks = $this->getCustomHook();
        foreach($hooks as $hook)
            $this->registerHook($hook);
        return true;
    }
    public function _unRegisterHooksCustom(){
        $hooks = $this->getCustomHook();
        foreach($hooks as $hook)
            $this->unregisterHook($hook);
        return true;
    }
    public function _unregisterHooks()
    {
        if(!$this->_config_types)
            $this->_defines();
        foreach($this->_config_types as $key=>$config_type)
        {
            $this->unregisterHook('display'.$key);
            unset($config_type);
        }
        return true;
    }
    /**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
        $this->clearCache();
        return parent::uninstall()
        && $this->unregisterHook('displayBackOfficeHeader') 
        && $this->unregisterHook('displayHome') 
        && $this->unregisterHook('displayHeader')
        && $this->unregisterHook('displayContentWrapperBottom')
        && $this->unregisterHook('displayProductAdditionalInfo')
        && $this->unregisterHook('displayRightColumnProduct')
        && $this->unregisterHook('displayProductPopupAdded')
        && $this->unregisterHook('displayShoppingCartFooter')
        && $this->unregisterHook('actionProductAdd')
        && $this->unregisterHook('actionProductUpdate')
        && $this->unregisterHook('actionProductDelete')
        && $this->unregisterHook('actionOrderStatusPostUpdate')
        && $this->unregisterHook('displayOrderConfirmation')
        && $this->unregisterHook('displayOrderConfirmation2')
        && $this->unregisterHook('actionPageCacheAjax')
        && $this->unregisterHook('displayFooterProduct')
        && $this->_unRegisterHooksCustom()
        && $this->uninstallDbDefault() && Ets_crosssell_db::unInstallDb();
    }
    public function uninstallDbDefault()
    {
        if(!$this->_sidebars)
            $this->_defines();
        foreach($this->_sidebars as $control=> $sidebar)
        {
            $configs = $this->_configs[$control];
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key));
                    if(isset($this->_config_types[$key]['setting']) && ($settings = $this->_config_types[$key]['setting']) )
                    {
                        if($control=='custom_page')
                            $settings = array_merge($settings,$this->getCustomSettings());
                        foreach($settings as $setting)
                        {
                            Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key).'_'.Tools::strtoupper($setting['name']));
                        }
                    }
                    unset($config);
                }
             }
            if($control!='custom_page')
            {
                Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT');
                Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT');
                Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS');
                Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP');
                Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET');
                Configuration::deleteByName('ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE');
            }
            unset($sidebar);
        }
        Configuration::deleteByName('ETS_CS_CATEGORY_SUB');
        Configuration::deleteByName('ETS_CS_ENABLE_CACHE');
        Configuration::deleteByName('ETS_CS_CACHE_LIFETIME');
        return true;
    }
    public function hookDisplayBackOfficeHeader()
    {
        if((Tools::getValue('controller')=='AdminModules' && Tools::getValue('configure')==$this->name))
        {
            $this->context->controller->addCSS($this->_path.'views/css/admin.css');
        }
    }
    public function logProductsViewed(){
        $tabs = ['home_page','category_page','product_page','quick_view_page','added_popup_page','cart_page','order_conf','cms_page','contact_page','custom_page'];
        $ok = false;
        foreach($tabs as $tab)
        {
            if(Configuration::get('ETS_CS_'.Tools::strtoupper($tab).'_VIEWEDPRODUCTS'))
            {
                $ok = true;
                break;
            }
        }
        if($ok)
        {
            $id_product = (int)Tools::getValue('id_product');
            $productsViewed = (isset($this->context->cookie->viewed) && !empty($this->context->cookie->viewed)) ? explode(',', $this->context->cookie->viewed) : array();
            if(count($productsViewed) >30)
            {
                $productsViewed = array_reverse($productsViewed);
                $productsViewed = array_reverse(array_splice($productsViewed, count($productsViewed)-30));
                $this->context->cookie->viewed = implode(',',$productsViewed);
            }
            if(Tools::getValue('controller')=='product' && $id_product && !in_array($id_product, $productsViewed))
            {
                $product = new Product((int)$id_product);
                if ($product->checkAccess((int)$this->context->customer->id))
                {
                    if(!in_array($id_product, $productsViewed))
                    {
                        if (isset($this->context->cookie->viewed) && !empty($this->context->cookie->viewed))
                            $this->context->cookie->viewed .= ','.(int)$id_product;
                        else
                            $this->context->cookie->viewed = (int)$id_product;
                        $this->context->cookie->write();
                    }
                }
            }
        }
    }
    public function logProductsMostViewed(){
        $tabs = ['home_page','category_page','product_page','quick_view_page','added_popup_page','cart_page','order_conf','cms_page','contact_page','custom_page'];
        $ok = false;
        foreach($tabs as $tab)
        {
            if(Configuration::get('ETS_CS_'.Tools::strtoupper($tab).'_MOSTVIEWEDPRODUCTS'))
            {
                $ok = true;
                break;
            }
        }
        if($ok)
        {
            $id_product = (int)Tools::getValue('id_product');
            $productMostViewed = (isset($this->context->cookie->mostViewed) && !empty($this->context->cookie->mostViewed)) ? explode(',', $this->context->cookie->mostViewed) : array();
            if(count($productMostViewed) >30)
            {
                $productMostViewed = array_reverse($productMostViewed);
                $productMostViewed =  array_reverse(array_splice($productMostViewed, count($productMostViewed)-30));
                $this->context->cookie->mostViewed = implode(',',$productMostViewed);
            }
            if(Tools::getValue('controller')=='product' && $id_product &&  !in_array($id_product,$productMostViewed))
            {
                $product = new Product((int)$id_product);
                if ($product->checkAccess((int)$this->context->customer->id))
                {
                    if(!in_array($id_product,$productMostViewed))
                    {
                        Ets_crosssell_db::addProductViewed($id_product);
                        if (isset($this->context->cookie->mostViewed) && !empty($this->context->cookie->mostViewed))
                            $this->context->cookie->mostViewed .= ','.(int)$id_product;
                        else
                            $this->context->cookie->mostViewed = (int)$id_product;
                        $this->context->cookie->write();
                    }
                }
            }
        }
    }
    public function hookActionPageCacheAjax()
    {
        $this->logProductsViewed();
        $this->logProductsMostViewed();
        if(!Module::isInstalled('ets_homecategories') || !Module::isEnabled('ets_homecategories'))
        {
            $this->context->cookie->ets_homecat_order_seed = rand(1, 10000);
            $this->context->cookie->write();
        }
    }
    public function hookActionProductAdd()
    {
        $this->clearCache(false);
    }
    public function hookActionProductUpdate()
    {
        $this->clearCache(false);
    }
    public function hookActionProductDelete()
    {
        $this->clearCache(false);
    }
    public function hookActionOrderStatusPostUpdate($params)
    {
        $this->clearCache(false);
    }
    public static function registerPlugins(){
        if(version_compare(_PS_VERSION_, '8.0.4', '>='))
        {
            $smarty = Context::getContext()->smarty->_getSmartyObj();
            if(!isset($smarty->registered_plugins[ 'modifier' ][ 'implode' ]))
                Context::getContext()->smarty->registerPlugin('modifier', 'implode', 'implode');
            if(!isset($smarty->registered_plugins[ 'modifier' ][ 'strpos' ]))
                Context::getContext()->smarty->registerPlugin('modifier', 'strpos', 'strpos');
        }
    }
    public function getContent()
	{
	    $this->registerHook('displayPopularProducts');
	    if(!$this->active)
	        return $this->displayWarning(sprintf($this->l('You must enable "%s" module to configure its features'),$this->displayName));
	    self::registerPlugins();
	   if(Tools::isSubmit('add_specific_product'))
            $this->addSpecificProduct();
	   if(Tools::isSubmit('search_product'))
           Ets_crosssell_db::getInstance()->searchProduct();
        if(!$this->_sidebars)
            $this->_defines();
        $control = Tools::getValue('control','home_page');
        if(!in_array($control,array('home_page','category_page','product_page','quick_view_page','added_popup_page','cart_page','order_conf','cms_page','contact_page','custom_page','settings')))
            $control= 'home_page';
        if($control && in_array($control,array('settings','home_page','category_page','product_page','cms_page','contact_page','custom_page')))
        {
            if($control=='home_page')
                $clear_cache_page = 'index';
            elseif($control=='settings')
                $clear_cache_page ='all';
            else
                $clear_cache_page =str_replace('_page','',$control);
        }
        $this->context->controller->addJqueryUI('ui.sortable');
        if(Tools::getValue('action')=='clearCache')
        {
            $this->clearCache();
            die(
                json_encode(
                    array(
                        'success' => $this->l('Clear cache successfully'),
                    )
                )

            );
        }
        if(Tools::getValue('action')=='updateBlock')
        {
            $field = Tools::getValue('field');
            $value_filed = Tools::getValue('value_filed');
            Configuration::updateValue($field,$value_filed);
            if(isset($clear_cache_page) && $clear_cache_page && Configuration::get('ETS_SP_CLEAR_CACHE_CRS'))
            {
                Hook::exec('actionDeleteAllCache',array('page'=>$clear_cache_page,'module_name'=>$this->name));
            }
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                    )
                )
            );
        }
        if(Tools::getValue('action')=='updateFieldOrdering')
        {
            $field_positions= Tools::getValue('field_positions');
            Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS',implode(',',$field_positions));
            if(isset($clear_cache_page) && $clear_cache_page && Configuration::get('ETS_SP_CLEAR_CACHE_CRS'))
            {
                Hook::exec('actionDeleteAllCache',array('page'=>$clear_cache_page,'module_name'=>$this->name));
            }
            die(
                json_encode(
                    array(
                        'success' => $this->l('Updated successfully'),
                    )
                )
            );
        }
        if(Tools::isSubmit('saveConfig'))
        {
            if($this->_checkValidatePost($control))
            {
                $this->_saveConfig($control);
                $this->clearCache(false);
                if(isset($clear_cache_page) && $clear_cache_page && Configuration::get('ETS_SP_CLEAR_CACHE_CRS'))
                {
                    if($clear_cache_page=='all')
                        Hook::exec('actionDeleteAllCache',array('module_name'=>$this->name,'all'=>true));
                    else
                        Hook::exec('actionDeleteAllCache',array('page'=>$clear_cache_page,'module_name'=>$this->name));
                }
                if(Tools::isSubmit('ajax'))
                {
                    die(
                        json_encode(
                            array(
                                'success' => $this->l('Updated successfully'),
                            )
                        )
                    );
                }
                else
                    Tools::redirect($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&control='.$control.'&conf=4');
            }
            
        }
        $this->smarty->assign(array(
            'ets_crossell_sidebar' => $this->renderSidebar($control),
            'ets_crossell_body_html' => $this->renderAdminBodyHtml($control),
            'control' => $control,
            'ets_cs_module_dir' => $this->_path,
        ));
        return $this->display(__FILE__, 'admin.tpl');           
    }
    public function renderSidebar($control)
    {
        $this->context->smarty->assign(
            array(
                'sidebars' => $this->_sidebars,
                'control' => $control,
                'cs_link_module' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name,
                'ets_cs_link_search_product' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&search_product=1',
            )
        );
        return $this->display(__FILE__,'sidebar.tpl');
    }
    public static function getBaseModLink()
    {
        $context = Context::getContext();
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$context->shop->domain.$context->shop->getBaseURI();
    }
    public function getCustomSettings(){
	    return array(
	        array(
                'name' =>  'COUNT_PRODUCT',
                'type'=>'text',
                'required' => true,
                'validate' => 'isUnsignedInt',
                'label' => $this->l('Product count'),
                'desc' => $this->l('The number of products will be displayed per Ajax load'),
                'default' =>8,
            ),
            array(
                'name' =>  'LAYOUT',
                'type'=>'radio',
                'label' => $this->l('Layout type'),
                'global_field' => true,
                'default' => 'grid',
                'values' => array(
                    array(
                        'id' => '',
                        'value'=>'grid',
                        'label' => $this->l('Grid view')
                    ),
                    array(
                        'id'=>'',
                        'value'=>'slide',
                        'label' => $this->l('Carousel slider')
                    ),
                ),
            ),
            array(
                'name' =>  'ROW_DESKTOP',
                'type'=>'select',
                'label' => $this->l('Number of displayed products per row on desktop'),
                'default'=>3,
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' =>1,
                            'name' =>1,
                        ),
                        array(
                            'id_option' =>2,
                            'name' =>2,
                        ),
                        array(
                            'id_option' =>3,
                            'name' =>3,
                        ),
                        array(
                            'id_option' =>4,
                            'name' =>4,
                        ),
                        array(
                            'id_option' =>5,
                            'name' =>5,
                        ),
                        array(
                            'id_option' =>6,
                            'name' =>6,
                        )
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'name' =>  'ROW_TABLET',
                'type'=>'select',
                'label' => $this->l('Number of displayed products per row on tablet'),
                'default'=>3,
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' =>1,
                            'name' =>1,
                        ),
                        array(
                            'id_option' =>2,
                            'name' =>2,
                        ),
                        array(
                            'id_option' =>3,
                            'name' =>3,
                        ),
                        array(
                            'id_option' =>4,
                            'name' =>4,
                        ),
                        array(
                            'id_option' =>5,
                            'name' =>5,
                        ),
                        array(
                            'id_option' =>6,
                            'name' =>6,
                        )
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            ),
            array(
                'name' =>'ROW_MOBILE',
                'type'=>'select',
                'default'=>1,
                'label' => $this->l('Number of displayed products per row on mobile'),
                'options' => array(
                    'query' => array(
                        array(
                            'id_option' =>1,
                            'name' =>1,
                        ),
                        array(
                            'id_option' =>2,
                            'name' =>2,
                        ),
                    ),
                    'id' => 'id_option',
                    'name' => 'name'
                ),
            )
        );
    }
    public function getCustomHook($key=false){
	    $hooks = array(
	        'trendingproducts' => 'displayTrendingProducts',
            'mostviewedproducts' => 'displayMostViewedProducts',
            'topratedproducts' => 'displayTopRatedProducts',
            'youmightalsolike' => 'displayYouMightAlsoLike',
            'viewedproducts' => 'displayViewedProducts',
            'featuredproducts' => 'displayFeaturedProducts',
            'bestselling' => 'displayBestSelling',
            'newproducts' => 'displayNewProducts',
            'specialproducts' => 'displaySpecialProducts',
            'popularproducts' => 'displayPopularProducts',
            'specificproducts' => 'displaySpecificProducts',
        );
        if($key) {
           if(isset($hooks[$key]))
               return $hooks[$key];
           else
               return 'display'.$key;
        }
        else
            return $hooks;
    }
    public function renderAdminBodyHtml($control)
    {
        $languages = Language::getLanguages(false);
        $fields_form = array(
    		'form' => array(
    			'legend' => array(
    				'title' => ($control!='settings' ? $this->l('Product blocks').': ' : '').$this->_sidebars[$control],
    				'icon' => 'fa fa-list-ul'
    			),
    			'input' => array(),
                'submit' => array(
    				'title' => $this->l('Save'),
    			)
            ),
    	);
        $configs = $this->_configs[$control];
        $fields = array();
        $custom_hooks = $this->getCustomHook();
        if($control!='settings')
        {
            if($configs)
            {
                $first_field=true;
                foreach($configs as $key => $config){
                    $arg = array(
                        'type' =>'switch',
                        'label' => $config,
                        'first_field' => $first_field ? true : false,
                        'name' => 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key),
                        'form_group_class' => 'ets-cs-form-group-field',
                        'values' => array(
    						array(
    							'id' => 'active_on',
    							'value' => 1,
    							'label' => $this->l('Yes')
    						),
    						array(
    							'id' => 'active_off',
    							'value' => 0,
    							'label' => $this->l('No')
    						)
    					)	
                    );
                    
                    $fields['ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key)] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key));
                    $fields_form['form']['input'][] = $arg;
                    if(isset($this->_config_types[$key]['setting']) && ($settings = $this->_config_types[$key]['setting']))
                    {
                        if($control=='custom_page')
                        {
                            $settings = array_merge($settings,$this->getCustomSettings());
                        }
                        foreach($settings as $index=> $setting)
                        {
                            if($setting['name']=='COUNT_PRODUCT' && $key=='specificproducts')
                                continue;
                            $name = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key).'_'.Tools::strtoupper($setting['name']);
                            $arg = array(
                                'type' =>$setting['type'],
                                'label' => $setting['label'],
                                'begin_group' => $index==0 ? true:false,
                                'hook_name' => ($index==0 && $control=='custom_page') ? $custom_hooks[$key] :'',
                                'title_group' => $this->_config_types[$key]['title'],
                                'end_group' => $index==count($settings)-1 ? true:false,
                                'module_name' => isset($this->_config_types[$key]['module_name']) ? $this->_config_types[$key]['module_name']:false,
                                'warning' => isset($this->_config_types[$key]['warning']) ? $this->_config_types[$key]['warning']: false,
                                'info' => isset($this->_config_types[$key]['info']) ? $this->_config_types[$key]['info']: false,
                                'name' => $name,
                                'lang'=> isset($setting['lang']) ? $setting['lang']:false,
                                'desc' => isset($setting['desc']) ? $setting['desc'] :'',
                                'form_group_class' => (isset($setting['form_group_class'] ) ? $setting['form_group_class'].' ':'').$key,
                                'tree' => isset($setting['tree']) ? $setting['tree']:array(),
                                'required' => isset($setting['required']) ? $setting['required'] : false,
                                'values' => isset($setting['values']) ? $setting['values']:'',	
                                'options' => isset($setting['options']) ? $setting['options']:false,
                                'placeholder' => isset($setting['placeholder']) ? $setting['placeholder']:false,
                                
                            );
                            if(isset($setting['tree']))
                            {
                                $tree = $setting['tree'];
                                $tree['selected_categories'] = array(Configuration::get($name));
                                $arg['tree']= $tree;
                            }
                            if(isset($setting['lang'])  && $setting['lang'])
                            {
                                foreach($languages as $lang)
                                {
                                    $fields[$name][$lang['id_lang']] = Configuration::get($name,$lang['id_lang']);
                                }
                            }
                            else
                                $fields[$name] = Configuration::get($name);
                            $fields_form['form']['input'][] = $arg;
                        }
                    }
                    $first_field=false;
                }
            }
            $layoutLabels = [
                'listgrid'  => $this->l('Product list with grid view'),
                'listslide' => $this->l('Product list with slider'),
                'tabgrid'   => $this->l('Product tabs with grid view'),
                'tabslide'  => $this->l('Product tabs with slider')
            ];
            if($control!='custom_page')
            {
                $fields_form['form']['input'][] = [
                    'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_LAYOUT',
                    'type'=>'radio',
                    'label' => $this->l('Product layout'),
                    'global_field' => true,
                    'values' => array(
                        array(
                            'id' => 'ETS_CS_'.Tools::strtoupper($control).'_LAYOUT_LISTGRID',
                            'value'=>'listgrid',
                            'label' => $this->renderImgPreviewLayout('listgrid', $layoutLabels['listgrid']) . EtsCrossSellTools::html([
                                    'tag' => 'span',
                                    'class' => 'radio-img-text',
                                    'content' => $layoutLabels['listgrid']
                                ])
                        ),
                        array(
                            'id' => 'ETS_CS_'.Tools::strtoupper($control).'_LAYOUT_LISTSLIDE',
                            'value'=>'listslide',
                            'label' => $this->renderImgPreviewLayout('listslide', $layoutLabels['listslide']) . EtsCrossSellTools::html([
                                    'tag' => 'span',
                                    'class' => 'radio-img-text',
                                    'content' => $layoutLabels['listslide']
                                ])
                        ),
                        array(
                            'id' => 'ETS_CS_'.Tools::strtoupper($control).'_LAYOUT_TABGRID',
                            'value'=>'tabgrid',
                            'label' => $this->renderImgPreviewLayout('tabgrid', $layoutLabels['tabgrid']) . EtsCrossSellTools::html([
                                    'tag' => 'span',
                                    'class' => 'radio-img-text',
                                    'content' => $layoutLabels['tabgrid']
                                ])
                        ),
                        array(
                            'id' => 'ETS_CS_'.Tools::strtoupper($control).'_LAYOUT_TABSLIDE',
                            'value'=>'tabslide',
                            'label' => $this->renderImgPreviewLayout('tabslide', $layoutLabels['tabslide']) . EtsCrossSellTools::html([
                                    'tag' => 'span',
                                    'class' => 'radio-img-text',
                                    'content' => $layoutLabels['tabslide']
                                ])
                        ),
                    ),
                    'form_group_class' => 'etscs-radio-img-with-preview'
                ];
                $fields_form['form']['input'][] = array(
                    'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT',
                    'type'=>'text',
                    'required' => true,
                    'label' => $this->l('Product count'),
                    'desc' => $this->l('The number of products will be displayed per Ajax load'),
                );
                $fields_form['form']['input'][] = array(
                    'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP',
                    'type'=>'select',
                    'label' => $this->l('Number of displayed products per row on desktop'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' =>1,
                                'name' =>1,
                            ),
                            array(
                                'id_option' =>2,
                                'name' =>2,
                            ),
                            array(
                                'id_option' =>3,
                                'name' =>3,
                            ),
                            array(
                                'id_option' =>4,
                                'name' =>4,
                            ),
                            array(
                                'id_option' =>5,
                                'name' =>5,
                            ),
                            array(
                                'id_option' =>6,
                                'name' =>6,
                            )
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    ),
                );
                $fields_form['form']['input'][] = array(
                    'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET',
                    'type'=>'select',
                    'label' => $this->l('Number of displayed products per row on tablet'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' =>1,
                                'name' =>1,
                            ),
                            array(
                                'id_option' =>2,
                                'name' =>2,
                            ),
                            array(
                                'id_option' =>3,
                                'name' =>3,
                            ),
                            array(
                                'id_option' =>4,
                                'name' =>4,
                            ),
                            array(
                                'id_option' =>5,
                                'name' =>5,
                            ),
                            array(
                                'id_option' =>6,
                                'name' =>6,
                            )
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    ),
                );
                $fields_form['form']['input'][] = array(
                    'name' =>  'ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE',
                    'type'=>'select',
                    'label' => $this->l('Number of displayed products per row on mobile'),
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' =>1,
                                'name' =>1,
                            ),
                            array(
                                'id_option' =>2,
                                'name' =>2,
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    ),
                );
                $fields['ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP');
                $fields['ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET');
                $fields['ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE');
                $fields['ETS_CS_'.Tools::strtoupper($control).'_LAYOUT'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT');
                $fields['ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT'] = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT');
            }
        }
        else
        {
            foreach($configs as $config)
            {
                $fields_form['form']['input'][] = $config;
                $fields[$config['name']] = Configuration::get($config['name'],Tools::getValue($config['name']));
            }
        }
        $helper = new HelperForm();
    	$helper->show_toolbar = false;
    	$helper->table = $this->table;
    	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    	$helper->default_form_language = $lang->id;
    	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    	$helper->module = $this;
    	$helper->identifier = $this->identifier;
    	$helper->submit_action = 'saveConfig';
    	$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.$control;
    	$helper->token = Tools::getAdminTokenLite('AdminModules');
    	$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));            
        $helper->override_folder = '/';
        $fields_position = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS') ? explode(',',Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS')) :array_keys($this->_configs[$control]);
        $fields_postion_value = array();
        if($fields_position)
        {
            foreach($fields_position as &$field_position)
            {
                $fields_postion_value[] = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($field_position);
            }
        }
        $helper->tpl_vars = array(
    		'base_url' => $this->context->shop->getBaseURL(),
    		'language' => array(
    			'id_lang' => $language->id,
    			'iso_code' => $language->iso_code
    		),
    		'fields_value' => $fields,
            'fields_position' => $fields_position,
            'fields_postion_value' =>$fields_postion_value,
            '_config_types' => $this->_config_types,
            'control' => Tools::strtoupper($control),
    		'languages' => $this->context->controller->getLanguages(),
    		'id_language' => $this->context->language->id,
            'isConfigForm' => true,
            'image_baseurl' => $this->_path.'views/img/',
            'page_title' => $this->_sidebars[$control],
            'tab' => $control,
            'custom_hooks' => $custom_hooks,
        );
        return $helper->generateForm(array($fields_form));	
    }
    public function _saveConfig($control,$default=false)
    {
        $languages = Language::getLanguages(false);
        if(!$this->_configs)
            $this->_defines();
        $configs = $this->_configs[$control];
        if($control!='settings')
        {
            if($configs)
            {
                foreach($configs as $key => $config)
                {
                    $name = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key);
                    if($default)
                    {
                        $value=0;
                    }else
                    {
                        $value = Tools::getValue($name);
                    }
                    if(!$default || !Configuration::hasKey($name))
                        Configuration::updateValue($name,$value);
                    if(isset($this->_config_types[$key]['setting']) && ($settings = $this->_config_types[$key]['setting']) )
                    {
                        if($control =='custom_page')
                            $settings = array_merge($settings,$this->getCustomSettings());
                        foreach($settings as $setting)
                        {
                            if($setting['name']=='COUNT_PRODUCT' && $key=='specificproducts')
                                continue;
                            $name = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key).'_'.Tools::strtoupper($setting['name']);
                            if($default && Configuration::hasKey($name))
                                continue;
                            if(isset($setting['lang']) && $setting['lang'])
                            {
                                $valules = array();
                                foreach($languages as $lang)
                                {
                                    $valules[$lang['id_lang']] = trim(Tools::getValue($name.'_'.$lang['id_lang'])) ? trim(Tools::getValue($name.'_'.$lang['id_lang'])) : '';
                                }
                                Configuration::updateValue($name,$valules);
                            }
                            else
                            {
                                if($default)
                                {
                                    if(isset($setting['default']))
                                        $value= $setting['default'];
                                    else
                                        $value=0;
                                }
                                else
                                    $value = Tools::getValue($name);
                                Configuration::updateValue($name,$value);
                            }
                        }
                    }
                }
            }
            if($control!='custom_page')
            {
                if(!$default || !Configuration::hasKey('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT'))
                    Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT','tabgrid'));
                if(!$default || !Configuration::hasKey('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT'))
                    Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT',8));
                if(!$default || !Configuration::hasKey('ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP'))
                    Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_DESKTOP'));
                if(!$default || !Configuration::hasKey('ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET'))
                    Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_TABLET'));
                if(!$default || !Configuration::hasKey('ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE'))
                    Configuration::updateValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE',Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_ROW_MOBILE'));
            }
        }
        else
        {
            foreach($configs as $config)
            {
                if($default && Configuration::hasKey($config['name']))
                    continue;
                if($config['type']!='categories')
                    Configuration::updateValue($config['name'],Tools::getValue($config['name'],(isset($config['default']) ? $config['default']:'') ));
                else
                {
                    Configuration::updateValue($config['name'],implode(',',Tools::getValue($config['name'],(isset($config['default']) && !Tools::isSubmit('saveConfig') ? $config['default']:array()))));
                }
            }
        }
    }
    public function _checkValidatePost($control)
    {
        $errors = array();
        $languages = Language::getLanguages(false);
        $configs = $this->_configs[$control];
        if($configs)
        {
            if($control!='settings' && $control!='custom_page')
            {
                if(!Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT'))
                    $errors[] = $this->l('Product count is required');
                elseif(!Validate::isUnsignedInt(Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_COUNT_PRODUCT')))
                    $errors[]= $this->l('Product count is not valid');
            }
            foreach($configs as $key => $config)
            {
                if($control!='settings')
                {
                    if(isset($this->_config_types[$key]['setting']) && ($settings = $this->_config_types[$key]['setting']) && Tools::getValue('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key)))
                    {
                        if($control=='custom_page')
                            $settings = array_merge($settings,$this->getCustomSettings());
                        foreach($settings as $setting)
                        {
                            if($setting['name']=='COUNT_PRODUCT' && $key=='specificproducts')
                                continue;
                            $name = 'ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($key).'_'.Tools::strtoupper($setting['name']);
                            if((isset($setting['required']) && $setting['required']) ||  (isset($setting['validate']) && $setting['validate'] && method_exists('Validate',$setting['validate'])))
                            {
                                $validate = $setting['validate'];
                                if(isset($setting['lang']) && $setting['lang'])
                                { 
                                    foreach($languages as $lang)
                                    {
                                        if(isset($setting['required']) && $setting['required']  && !Tools::getValue($name.'_'.$lang['id_lang']))
                                            $errors[] = $setting['label'].' ('.$this->_config_types[$key]['title'].')'.' '.$this->l('is required ');
                                        elseif((isset($setting['validate']) && $setting['validate'] && method_exists('Validate',$setting['validate'])) && !Validate::$validate(trim(Tools::getValue($name.'_'.$lang['id_lang']))))
                                            $errors[] =  $setting['label'].' ('.$this->_config_types[$key]['title'].')'.' '.$this->l('is not valid in ').$lang['iso_code'];
                                    }
                                }
                                else
                                {
                                    if(isset($setting['required']) && $setting['required'] && !Tools::getValue($name))
                                        $errors[] = $setting['label'].' ('.$this->_config_types[$key]['title'].')'.' '. $this->l('is required');
                                    elseif(isset($setting['validate']) && $setting['validate'] && !Validate::$validate(trim(Tools::getValue($name))))
                                        $errors[] = $setting['label'].' ('.$this->_config_types[$key]['title'].')'.' '. $this->l('is not valid');
                                }
                                unset($validate);
                            }
                        }
                    }
                }
                else
                {
                    $validate = isset($config['validate']) ? $config['validate']:'';
                    $name = $config['name'];
                    if(isset($config['lang']) && $config['lang'])
                    { 
                        foreach($languages as $lang)
                        {
                            if(isset($config['required']) && $config['required']  && !Tools::getValue($name.'_'.$lang['id_lang']))
                                $errors[] = $config['label'].' '.$this->l('is required ');
                            elseif((isset($config['validate']) && $config['validate'] && method_exists('Validate',$config['validate'])) && !Validate::$validate(trim(Tools::getValue($name.'_'.$lang['id_lang']))))
                                $errors[] =  $config['label'].' '.$this->l('is not valid in ').$lang['iso_code'];
                        }
                    }
                    else
                    {
                        if(isset($config['required']) && $config['required'] && !Tools::getValue($name))
                            $errors[] = $config['label'].' '. $this->l('is required');
                        elseif($validate &&  !Validate::$validate(trim(Tools::getValue($name))))
                            $errors[] = $config['label'].' '. $this->l('is not valid');
                    }
                    unset($validate);
                }
            }
         }
         if(!$errors)
            return true;
         else
         {
            die(
                json_encode(
                    array(
                        'errors' => $this->displayError($errors),
                    )
                )
            );
         }       
    }
    public function hookDisplayHeader()
    {
        $this->hookActionPageCacheAjax();
        if($this->isCrosssell())
        {
            if(!$this->is17 && Tools::getValue('controller')!='index' && Tools::getValue('controller')!='category')
                $this->context->controller->addCSS($this->_path . 'views/css/product_list16.css', 'all');
            if(Tools::isSubmit('getCrosssellContent') && ($tab = Tools::getValue('tab')) && isset($this->_config_types[$tab]) && ($name_page = Tools::getValue('page_name')) && isset($this->_configs[$name_page]))
            {
                $id_product = (int)Tools::getValue('id_product');
                $func = 'hookdisplay'.$tab;
                die(
                    json_encode(
                        array(
                            'product_list' => $this->{$func}(array('name_page'=>$name_page,'id_product'=>$id_product)),
                        )
                    )
                );
            }
            if(Tools::isSubmit('sortProductsCrosssellContent') && ($tab = Tools::getValue('tab')) && isset($this->_config_types[$tab]) && ($name_page = Tools::getValue('page_name')) && isset($this->_configs[$name_page]) )
            {
                $sort_by = ($sort_by = Tools::getValue('sort_by')) && in_array($sort_by,self::getSortOptions()) ? $sort_by: '';
                if(!in_array($sort_by,self::getSortOptions()))
                    $sort_by ='';
                $id_product = (int)Tools::getValue('id_product');
                $func = 'hookdisplay'.$tab;
                die(
                    json_encode(
                        array(
                            'product_list' =>$this->{$func}(array('name_page'=>$name_page,'id_product'=>$id_product,'order_by'=>$sort_by)),
                        )
                    )
                );
            }
            if(Tools::getValue('getProductPopupAdded'))
            {
                die(
                    json_encode(
                        array(
                            'product_lists' => Hook::exec('displayProductPopupAdded',array('name_page'=>'added_popup_page','id_product'=>Tools::getValue('id_product')),$this->id),
                        )
                    )
                );
            }
            if(Tools::getValue('getProductExtraPage') && !$this->is17)
            {
                die(
                    json_encode(
                        array(
                            'product_lists' => Hook::exec('displayContentWrapperBottom',array(),$this->id),
                        )
                    )
                );
            }
            if($this->isSlideCrosssell())
            {
                $this->context->controller->addCSS($this->_path . 'views/css/slick.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/slick.min.js');
            }
            $this->context->controller->addCSS($this->_path . 'views/css/front.css', 'all');
            $this->context->controller->addJS($this->_path . 'views/js/front.js');
            if(!$this->is17)
                $this->context->controller->addCSS($this->_path . 'views/css/front16.css', 'all');
            $this->context->smarty->assign(
                array(
                    'ets_crosssell_16' => !$this->is17,
                )
            );
            return $this->display(__FILE__,'header.tpl');
        }
    }
    public function hookDisplayOrderConfirmation()
    {
        if(!$this->is17)
            return $this->_execHook('order_conf');
    }
    public function hookDisplayOrderConfirmation2()
    {
        if($this->is17)
            return $this->_execHook('order_conf');
    }
    public function hookDisplayHome()
    {
        return $this->_execHook('home_page');
    }
    public function hookDisplayFooterProduct($params)
    {
        return $this->_execHook('product_page',array('id_product'=>$params['product']->id));
    }
    public function hookDisplayCustomProduct($params)
    {
        return $this->hookDisplayFooterProduct($params);
    }
    public function hookDisplayContentWrapperBottom()
    {
        if(Tools::getValue('controller')=='category' && !Tools::isSubmit('module'))
        {
            return $this->_execHook('category_page');
        }   
        if(Tools::getValue('controller')=='contact')
        {
            return $this->_execHook('contact_page');
        }
        if(Tools::getValue('controller')=='cms')
            return $this->_execHook('cms_page');
    }
    public function hookDisplayProductAdditionalInfo()
    {
        if((Tools::getValue('action')=='quickview' || (Tools::isSubmit('quickview') && Tools::isSubmit('ajax'))  || Tools::isSubmit('cetoken')) && $this->is17)
        {
            return $this->_execHook('quick_view_page',array('id_product'=>Tools::getValue('id_product')));
        }
    }
    public function hookDisplayRightColumnProduct()
    {
        if(Tools::isSubmit('content_only') && !$this->is17)
            return $this->_execHook('quick_view_page',array('id_product'=>Tools::getValue('id_product')));
    }
    public function hookDisplayProductPopupAdded($params)
    {
        return $this->_execHook('added_popup_page',array('id_product'=>isset($params['id_product'])? $params['id_product']:0 ));
    }
    public function hookDisplayShoppingCartFooter()
    {
        return $this->_execHook('cart_page');
    }
    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60)
    {
        if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
            $stream_context = stream_context_create(array(
                "http" => array(
                    "timeout" => $curl_timeout,
                    "max_redirects" => 101,
                    "header" => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
                ),
                "ssl"=>array(
                    "allow_self_signed"=>true,
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            ));
        }
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => html_entity_decode($url),
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => $curl_timeout,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_FOLLOWLOCATION => true,
            ));
            $content = curl_exec($curl);
            curl_close($curl);
            return $content;
        } elseif (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
            return Tools::file_get_contents($url, $use_include_path, $stream_context);
        } else {
            return false;
        }
    }
    public function excuteHookDisplay($hook_name, $name_page,$id_product =0)
    {
        $func = 'hook'.$hook_name;
        return $this->{$func}(array('name_page'=>$name_page,'id_product'=>$id_product));
    }
    public function isCrosssell()
    {
        if($this->_checkHasCrosssell('custom_page') || $this->_checkHasCrosssell('quick_view_page') || $this->_checkHasCrosssell('added_popup_page'))
            return true;
        else
        {
            $controller = Tools::strtolower(Tools::getValue('controller'));
            if(Validate::isControllerName($controller))
            {
                if($controller=='orderconfirmation')
                    return $this->_checkHasCrosssell('order_conf');
                elseif($controller=='index')
                    return $this->_checkHasCrosssell('home_page');
                elseif($controller=='product')
                    return $this->_checkSlideCrosssell('product_page');
                elseif($controller=='category')
                    return $this->_checkHasCrosssell('category_page');
                elseif($controller=='contact')
                    return $this->_checkHasCrosssell('contact_page');
                elseif($controller=='cms')
                    return $this->_checkHasCrosssell('cms_page');
                elseif($controller=='cart' || $controller=='order')
                    return $this->_checkHasCrosssell('cart_page');
            }
        }
        return false;
    }
    public function _checkCustomPageSlideCrosssell(){
	    $hooks = $this->getCustomHook();
	    $positions = array_keys($hooks);
	    foreach($positions as $position){
	        if(Configuration::get('ETS_CS_CUSTOM_PAGE_'.Tools::strtoupper($position)) && Configuration::get('ETS_CS_CUSTOM_PAGE_'.Tools::strtoupper($position).'_LAYOUT')=='slide'){
	            return true;
            }
        }
    }
    public function isSlideCrosssell()
    {
        if($this->_checkCustomPageSlideCrosssell() || $this->_checkSlideCrosssell('quick_view_page') || $this->_checkSlideCrosssell('added_popup_page'))
            return true;
        else
        {
            $controller = Tools::strtolower(Tools::getValue('controller'));
            if(Validate::isControllerName($controller))
            {
                if($controller=='orderconfirmation')
                    return $this->_checkSlideCrosssell('order_conf');
                elseif($controller=='index')
                    return $this->_checkSlideCrosssell('home_page');
                elseif($controller=='product')
                    return $this->_checkSlideCrosssell('product_page');
                elseif($controller=='category')
                    return $this->_checkSlideCrosssell('category_page');
                elseif($controller=='contact')
                    return $this->_checkSlideCrosssell('contact_page');
                elseif($controller=='cms')
                    return $this->_checkSlideCrosssell('cms_page');
                elseif($controller=='cart' || $controller=='order')
                    return $this->_checkSlideCrosssell('cart_page');
            }
        }
        return false;
    }
    public function _checkHasCrosssell($control)
    {
        if(!$this->_configs)
            $this->_defines();
        $fields_position = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS') ? explode(',',Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS')) :array_keys($this->_configs[$control]);
        if($fields_position) {
            foreach ($fields_position as $filed_position) {
                if (Configuration::get('ETS_CS_' . Tools::strtoupper($control) . '_' . Tools::strtoupper($filed_position))) {
                    return true;
                }
            }
        }
        return false;
    }
    public function _checkSlideCrosssell($control)
    {
        if ($this->_checkHasCrosssell($control)) {
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($control) . '_LAYOUT');
            if ($layout == 'listslide' || $layout == 'tabslide') {
                return true;
            }
        }
        return false;
    }
    public function _execHook($control,$params=array())
    {
        if(!$this->_configs)
            $this->_defines();
        $layout = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_LAYOUT');
        $configs = $this->_configs[$control];
        $sc_configs = array();
        $fields_position = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS') ? explode(',',Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_POSITIONS')) :array_keys($this->_configs[$control]);
        if($fields_position)
        {
            foreach($fields_position as $filed_position)
            {
                if(Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($filed_position)) && ($filed_position!='viewedproducts' ||  $this->hookDisplayViewedProducts(array('check' => true,'name_page'=>$control,'id_product'=>isset($params['id_product']) ? $params['id_product']:0))))
                {
                    $title = Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($filed_position).'_TITLE',$this->context->language->id);
                    if($id_categories = Configuration::get('ETS_CS_CATEGORY_SUB'))
                    {
                          $id_categories = explode(',',$id_categories);
                          $sub_categories = Ets_crosssell_db::getCategoriesByIDs($id_categories);
                    }
                    else
                        $sub_categories=array();
                    $sc_configs[] = array(
                        'tab_name' => $title ? $title : $configs[$filed_position],
                        'hook' => 'display'.$filed_position,
                        'tab' => $filed_position,
                        'sub_categories' => Configuration::get('ETS_CS_'.Tools::strtoupper($control).'_'.Tools::strtoupper($filed_position).'_DISPLAY_SUB_CATEGORY') ? $sub_categories : array()
                    );
                }
            }
        }
        if ($layout == 'tabgrid' || $layout == 'tabslide') {
            $array = array();
            if ($sc_configs) {
                foreach ($sc_configs as $sc_config) {
                    $func = 'hook' . $sc_config['hook'];
                    if ($this->{$func}(array('name_page' => $control, 'id_product' => isset($params['id_product']) ? $params['id_product'] : 0, 'check' => true))) {
                        $array[] = $sc_config;
                    }
                }
            }
            $sc_configs = $array;
        }
        $this->smarty->assign(
            array(
                'sc_configs' => $sc_configs,
                'name_page' => $control,
                'id_product' => isset($params['id_product']) ? $params['id_product']:0,
                'layout_mode' => $layout == 'listgrid' || $layout == 'tabgrid' ? 'grid' : 'slide',
            )   
        );
        if ($layout=='tabgrid' || $layout == 'tabslide') {
            return $this->display(__FILE__, 'layout_tab.tpl');
        } else {
            return $this->display(__FILE__, 'layout_list.tpl');
        }
    }
    public static function productsForTemplate($products, Context $context = null)
    {
        if (!$products || !is_array($products))
            return array();
        if (!$context)
            $context = Context::getContext();
        $assembler = new ProductAssembler($context);
        $presenterFactory = new ProductPresenterFactory($context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
            new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
                $context->link
            ),
            $context->link,
            new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
            new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
            $context->getTranslator()
        );

        $products_for_template = array();

        foreach ($products as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $context->language
            );
        }
        return $products_for_template;
    }

    protected function getBestSellers($nProducts)
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }
		if (!($result = Ets_crosssell_db::getBestSalesLight((int)$this->context->language->id, 0, (int)$nProducts)))
			return  array();
        if($this->is17)
            return Ets_crosssell::productsForTemplate($result);                    
		return $result;
    }
    protected function getNewProducts($nbProducts=8,$order_sort = 'cp.position asc')
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }
        if($order_sort)
        {
            $order_sort = explode(' ',$order_sort);
            $order_by = $order_sort[0];
            if(isset($order_sort[1]))
                $order_way = $order_sort[1];
            else
                $order_way = null;
        }
        else
        {
            $order_way = null;
            $order_by = null;
        }
		$newProducts = Ets_crosssell_db::getListNewProducts((int) $this->context->language->id, 0, (int)$nbProducts,false,$order_by,$order_way);
        if($this->is17)
            return Ets_crosssell::productsForTemplate($newProducts);
		return $newProducts;
    }
    private function getSpecialProducts($nbProducts,$order_sort = 'cp.position asc')
    {
        if($order_sort)
        {
            $order_sort = explode(' ',$order_sort);
            $order_by = $order_sort[0];
            if(isset($order_sort[1]))
                $order_way = $order_sort[1];
            else
                $order_way = null;
        }
        else
        {
            $order_way = null;
            $order_by = null;
        }
        if($order_by=='rand')
        {
            $order_way = null;
            $order_by = null;
        }
        $products = Ets_crosssell_db::getPricesDrop(
            (int)Context::getContext()->language->id,
            0,
            (int)$nbProducts,false,$order_by,$order_way
        );
        if($this->is17)
        {
            return Ets_crosssell::productsForTemplate($products);
        }
        else
            return $products;
    }
    public function createCache($html,$params)
    {
        if(!Configuration::get('ETS_CS_ENABLE_CACHE'))
            return false;
        if(!is_dir(_ETS_CROSSSELL_CACHE_DIR_))
        {
            @mkdir(_ETS_CROSSSELL_CACHE_DIR_,0777,true);
            if ( @file_exists(dirname(__file__).'/index.php')){
                @copy(dirname(__file__).'/index.php', _ETS_CROSSSELL_CACHE_DIR_.'index.php');
            }
        }

        $str = '';
        if($params)
        {
            foreach($params as $key=>$value)
            {
                if(!is_array($value))
                    $str .='&'.$key.'='.$value;
            }
        }
        $str .= '&id_lang='.$this->context->language->id;
        $str .= '&ets_currency='.($this->context->cookie->id_currency ? $this->context->cookie->id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
        $id_customer = (isset($this->context->customer->id)) ? (int)($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        } 
        $str .= '&ets_group='.(int)$id_group; 
        $id_country =isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && Validate::isLanguageIsoCode($this->context->cookie->iso_code_country) ?
                    (int) Country::getByIso(Tools::strtoupper($this->context->cookie->iso_code_country)) : (int) Tools::getCountry();
        $str .='&ets_country='.($id_country ? $id_country : (int)$this->context->country->id);
        if(isset($this->context->cookie->id_cart) && $this->context->cookie->id_cart)
            $str .='&hascart=1';
        $str .='&id_category='.(int)Tools::getValue('id_ets_css_sub_category');
        file_put_contents(_ETS_CROSSSELL_CACHE_DIR_.md5($str).'.'.time(),$html);    
    }
    public function clearCache($clear_all = true)
    {
        if(is_dir(_ETS_CROSSSELL_CACHE_DIR_) && ($files = glob(_ETS_CROSSSELL_CACHE_DIR_.'*')))
        {
            foreach ($files as $filename) {
                if(file_exists($filename) && $filename!=_ETS_CROSSSELL_CACHE_DIR_.'index.php')
                    @unlink($filename);
                }
        }
        if($clear_all)
        {
            if($clear_all)
            {
                if(Configuration::get('ETS_SP_CLEAR_CACHE_CRS'))
                {
                    Hook::exec('actionDeleteAllCache',array('module_name'=>$this->name));
                }

            }
        }
        return true;
    }
    public function getCache($params){
	    if(!Configuration::get('ETS_CS_ENABLE_CACHE'))
            return false;
        if ( !$params )
            return false;
        $str = '';
        if($params)
        {
            foreach($params as $key=>$value)
            {
                if(!is_array($value))
                    $str .='&'.$key.'='.$value;
            }
        }
        $str .= '&id_lang='.$this->context->language->id;
        $str .= '&ets_currency='.($this->context->cookie->id_currency ? $this->context->cookie->id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));
        $id_customer = (isset($this->context->customer->id)) ? (int)($this->context->customer->id) : 0;
        $id_group = null;
        if ($id_customer) {
            $id_group = Customer::getDefaultGroupId((int)$id_customer);
        }
        if (!$id_group) {
            $id_group = (int)Group::getCurrent()->id;
        } 
        $str .= '&ets_group='.(int)$id_group; 
        $id_country =isset($this->context->cookie->iso_code_country) && $this->context->cookie->iso_code_country && Validate::isLanguageIsoCode($this->context->cookie->iso_code_country) ?
                    (int) Country::getByIso(Tools::strtoupper($this->context->cookie->iso_code_country)) : (int) Tools::getCountry();
        $str .='&ets_country='.($id_country ? $id_country : (int)$this->context->country->id);
        if(isset($this->context->cookie->id_cart) && $this->context->cookie->id_cart)
            $str .='&hascart=1';
        $str .='&id_category='.(int)Tools::getValue('id_ets_css_sub_category');
        $url_file = _ETS_CROSSSELL_CACHE_DIR_.md5($str);
        $cacheLifeTime = (float)Configuration::get('ETS_CS_CACHE_LIFETIME');
        if($files = @glob($url_file.'.*'))
            foreach ($files as $file) {
                if(file_exists($file)){
                    $file_extends = Tools::substr(strrchr($file, '.'), 1);
                    if ( is_numeric( $file_extends )){
                        if ( (time() - (int)$file_extends <= $cacheLifeTime*60*60) || !$cacheLifeTime){
                            return Tools::file_get_contents($file);
                        }else{
                            unlink($file);
                        }
                    }
                }
            }
        return false;
    }
    public function displayProductList($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page']:'custom_page';
        $tab = isset($params['tab']) ? $params['tab']:'';
        $display_sub_categories = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_'.Tools::strtoupper($tab).'_DISPLAY_SUB_CATEGORY');
        if($display_sub_categories && ($id_categories = Configuration::get('ETS_CS_CATEGORY_SUB')))
        {
              $id_categories = explode(',',$id_categories);
              $sub_categories = Ets_crosssell_db::getCategoriesByIDs($id_categories);
        }
        else
            $sub_categories=array();
        if($name_page=='custom_page')
            $row_desktop = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_'.Tools::strtoupper($tab).'_ROW_DESKTOP');
        else
            $row_desktop = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_ROW_DESKTOP');
        if($name_page=='custom_page')
            $row_tablet = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_'.Tools::strtoupper($tab).'_ROW_TABLET');
        else
            $row_tablet = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_ROW_TABLET');
        if($name_page=='custom_page')
            $row_mobile = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_'.Tools::strtoupper($tab).'_ROW_MOBILE');
        else
            $row_mobile = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_ROW_MOBILE');
        $id_sub_category = (int)Tools::getValue('id_ets_css_sub_category');
        $id_product = (int)Tools::getValue('id_product');
        $this->smarty->assign(array(
            'products' => isset($params['products']) ? $params['products'] :[],
            'tab' => $tab,
            'name_page' => $name_page,
            'ets_per_row_desktop' => $row_desktop,
            'ets_per_row_tablet' => $row_tablet,
            'ets_per_row_mobile' => $row_mobile,
            'layout_mode' => isset($params['layout_mode']) ? $params['layout_mode']:'slide',
            'sub_categories' =>$display_sub_categories ? $sub_categories:array(),
            'id_ets_css_sub_category' => $id_sub_category,
            'id_product_page' => $id_product,
            'sort_by' => isset($params['sort_by']) && $params['sort_by'] ? $params['sort_by']:'',
            'sort_options' =>isset($params['sort_options']) && $params['sort_options'] ? $params['sort_options'] :false,
        ));
        if($this->is17)
        {
            $controller = Tools::getValue('controller');
            if(Validate::isControllerName($controller))
                $this->smarty->assign('page_name',$controller);
        }
        $list_products = $this->display(__FILE__, 'product_list' . ($this->is17 ? '_17' : '') . '.tpl');
        if(Tools::isSubmit('ajax') || $name_page!='custom_page')
            return $list_products;
        else
        {
            $this->smarty->assign(
                array(
                    'crosssell_list_products' =>$list_products,
                    'custom_name_page' => $name_page,
                    'custom_tab'=> $tab,
                    'custom_page_title' => isset($params['page_title']) ? $params['page_title']:'',
                )
            );
            return $this->display(__FILE__,'custom_page.tpl');
        }
    }
    public function getRandomSeed()
    {
        if ((int)Tools::getValue('ets_homecat_order_seed') > 0 && (int)Tools::getValue('ets_homecat_order_seed') <= 10000)
            return (int)Tools::getValue('ets_homecat_order_seed');
        elseif ((int)$this->context->cookie->ets_homecat_order_seed > 0 && (int)$this->context->cookie->ets_homecat_order_seed <= 10000)
            return (int)$this->context->cookie->ets_homecat_order_seed;
        else
            return 1;
    }
    protected static $viewed_products = array();
    public function hookDisplayViewedProducts($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page'] :'custom_page';
        if(isset($this->_configs[$name_page]) && Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_VIEWEDPRODUCTS'))
        {
            if($name_page=='custom_page')
                $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_VIEWEDPRODUCTS_COUNT_PRODUCT') ? :8;
            else
                $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? :8;
            $productsViewed = (isset($this->context->cookie->viewed) && !empty($this->context->cookie->viewed)) ? array_slice(array_reverse(explode(',', $this->context->cookie->viewed)), 0) : array();
            if($productsViewed || Tools::isSubmit('ajax'))
            {
                $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_VIEWEDPRODUCTS_ENABLE_SORT_BY');
                $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_VIEWEDPRODUCTS_SORT_BY_DEFAULT');
                if(!isset(self::$viewed_products[$name_page]))
                    self::$viewed_products[$name_page] = Ets_crosssell_db::getProducts(false,1,$count_product,isset($params['order_by']) && $params['order_by'] ? $params['order_by']: ($sort_by_default ? $sort_by_default :'cp.position') ,$productsViewed);
                $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
                if(isset($params['check']) && $params['check'])
                {
                    if(self::$viewed_products[$name_page])
                        return true;
                    else
                        return false;
                }
                if($name_page =='custom_page')
                    $layout = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_VIEWEDPRODUCTS_LAYOUT');
                else
                    $layout = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_LAYOUT');
                $params = array(
                    'products' => self::$viewed_products[$name_page],
                    'tab' => 'viewedproducts',
                    'name_page' => $name_page, 
                    'sort_by' => $sort_by,
                    'sort_options' =>$enable_sort_by ? $this->_sort_options:false,
                    'layout_mode' => Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide',
                    'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_VIEWEDPRODUCTS_TITLE',$this->context->language->id) ? :$this->l('Viewed products'),
                );
                return $this->displayProductList($params);
            }
            else
            {
                return false;
            }
        }
        
    }
    public function hookDisplayFeaturedProducts($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page'] :'custom_page';
        if(!Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS'))
            return '';
        $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS_SORT_BY_DEFAULT');        
        if($name_page=='custom_page')
            $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS_COUNT_PRODUCT') ? :8;
        else
            $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? :8;
        $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
        if($name_page=='custom_page')
            $layout = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS_LAYOUT');
        else
            $layout = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_LAYOUT');
        $cacheparams = array(
            'tab' => 'featuredproducts',
            'name_page' => $name_page, 
            'sort_by' => $sort_by,
            'ajax' => Tools::isSubmit('ajax'),
            'sort_options' =>$enable_sort_by ? $this->_sort_options:false,
            'layout_mode' => Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide',
        );
        if($html = $this->getCache($cacheparams))
            return $html;
        if(isset($this->_configs[$name_page]))
        {
            if($id_category = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS_ID_CATEGORY'))
            {
                $cache_key ='Crosssell:hookDisplayFeaturedProducts_'.$name_page;
                if(!Cache::isStored($cache_key))
                {
                    $products = Ets_crosssell_db::getProducts($id_category,1,$count_product,isset($params['order_by']) && $params['order_by'] ? $params['order_by']: ($sort_by_default? $sort_by_default: 'cp.position'));
                    Cache::store($cache_key,$products);
                }
                else
                    $products = Cache::retrieve($cache_key);
                if( isset($params['check']) && $params['check'])
                {
                    if($products)
                        return true;
                    else
                        return false;
                }
                $params = array(
                    'products' => $products,
                    'tab' => 'featuredproducts',
                    'name_page' => $name_page, 
                    'sort_by' => $sort_by,
                    'sort_options' =>$enable_sort_by ? $this->_sort_options:false,
                    'layout_mode' => Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide',
                    'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_FEATUREDPRODUCTS_TITLE',$this->context->language->id) ? :$this->l('Featured products'),
                );
                $html = $this->displayProductList($params);
                $this->createCache($html,$cacheparams);
                return $html;
            }
            else{

                if(isset($params['check']) && $params['check'])
                    return false;    
                $this->smarty->assign(
                    array(
                        'tab' => 'featuredproducts',
                        'name_page' => $name_page, 
                        'layout_mode' => Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide'
                    )
                );
                $html = $this->display(__FILE__,'no_product.tpl');
                $this->createCache($html,$cacheparams);
                return $html;
            }
        }
    }
    public function hookDisplayPopularProducts($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page']:'custom_page';

        if(!Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_POPULARPRODUCTS'))
            return '';
        if($name_page=='custom_page')
            $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_POPULARPRODUCTS_COUNT_PRODUCT') ? : 8;
        else
            $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') ? : 8;
        $id_category = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_POPULARPRODUCTS_ID_CATEGORY') ? (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_POPULARPRODUCTS_ID_CATEGORY') : 2;
        $enable_sort_by = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_POPULARPRODUCTS_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_POPULARPRODUCTS_SORT_BY_DEFAULT');
        $sort_by = ($sort_by = Tools::getValue('sort_by', $sort_by_default)) && in_array($sort_by, self::getSortOptions()) ? $sort_by : '';
        if($name_page=='custom_page')
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_POPULARPRODUCTS_LAYOUT');
        else
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
        $cacheparams = array(
            'tab' => 'popularproducts',
            'name_page' => $name_page,
            'sort_by' => $sort_by,
            'ajax' => Tools::isSubmit('ajax'),
            'sort_options' => $enable_sort_by ? $this->_sort_options : false,
            'layout_mode' => Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide'
        );

        if ($html = $this->getCache($cacheparams))
        {
            return $html;
        }
        if (isset($this->_configs[$name_page])) {
            $cache_key = 'Crosssell:hookDisplayPopularProducts_'.$name_page;
            if (!Cache::isStored($cache_key)) {
                $products = Ets_crosssell_db::getProducts($id_category, 1, $count_product, isset($params['order_by']) && $params['order_by'] ? $params['order_by'] : ($sort_by_default ? $sort_by_default : 'cp.position'));
                Cache::store($cache_key, $products);
            } else
                $products = Cache::retrieve($cache_key);
            if (isset($params['check']) && $params['check']) {
                if ($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'popularproducts',
                'name_page' => $name_page,
                'sort_by' => $sort_by,
                'sort_options' => $enable_sort_by ? $this->_sort_options : false,
                'layout_mode' => Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide',
                'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_POPULARPRODUCTS_TITLE',$this->context->language->id) ? :$this->l('Popular products'),
            );
            $html = $this->displayProductList($params);
            $this->createCache($html, $cacheparams);
            return $html;
        }
    }
    public function hookDisplayMostViewedProducts($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page']:'custom_page';
        if (isset($this->_configs[$name_page]) && Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MOSTVIEWEDPRODUCTS')) {
            $cache_key = 'Crosssell::hookDisplayMostViewedProducts_'.$name_page;
            if($name_page=='custom_page')
                $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_MOSTVIEWEDPRODUCTS_COUNT_PRODUCT') ? : 8;
            else
                $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') ? : 8;
            if($name_page=='custom_page')
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_MOSTVIEWEDPRODUCTS_LAYOUT');
            else
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
            if (!Cache::isStored($cache_key)) {
                $products = Ets_crosssell_db::getMostViewedProducts($count_product);
                Cache::store($cache_key, $products);
            } else
                $products = Cache::retrieve($cache_key);
            if (isset($params['check']) && $params['check']) {
                if ($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'mostviewedproducts',
                'name_page' => $name_page,
                'layout_mode' =>Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide',
                'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_MOSTVIEWEDPRODUCTS_TITLE',$this->context->language->id) ? : $this->l('Most viewed products'),
            );
            return $this->displayProductList($params);
        }
    }
    public function hookDisplayYouMightAlsoLike($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page']:'custom_page';
        if(!Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_YOUMIGHTALSOLIKE'))
            return '';
        if($name_page=='custom_page')
            $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_YOUMIGHTALSOLIKE_COUNT_PRODUCT') ? : 8;
        else
            $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') ? : 8;
        if($name_page=='custom_page')
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_YOUMIGHTALSOLIKE_LAYOUT');
        else
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
        if (Tools::getValue('id_product'))
            $id_product = (int)Tools::getValue('id_product');
        else
            $id_product = 0;
        $sort_by = ($sort_by = Tools::getValue('sort_by')) && in_array($sort_by, self::getSortOptions()) ? $sort_by : '';
        if ($id_product) {
            $cacheparams = array(
                'id_product' => $id_product,
                'tab' => 'youmightalsolike',
                'name_page' => $name_page,
                'sort_by' =>  $sort_by,
                'ajax' => Tools::isSubmit('ajax'),
                'sort_options' => false,
                'layout_mode' => $layout == 'listgrid' || $layout == 'tabgrid' ? 'grid' : 'slide'
            );
            if ($html = $this->getCache($cacheparams))
                return $html;
        }
        if (isset($this->_configs[$name_page])) {
            $cache_key = 'Crosssell::hookDisplayYouMightAlsoLike_'.$name_page;
            if (!Cache::isStored($cache_key)) {
                $products = Ets_crosssell_db::getProductYouMightAlsoLike($id_product, $count_product, isset($params['order_by']) && $params['order_by'] ? $params['order_by'] : 'total_product desc');
                Cache::store($cache_key, $products);
            } else
                $products = Cache::retrieve($cache_key);
            if (isset($params['check']) && $params['check']) {
                if ($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'youmightalsolike',
                'name_page' => $name_page,
                'sort_by' => $sort_by,
                'sort_options' => false,
                'layout_mode' => Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide',
                'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_YOUMIGHTALSOLIKE_TITLE',$this->context->language->id) ? : $this->l('You might also like'),
            );
            $html = $this->displayProductList($params);
            if ($id_product)
                $this->createCache($html, $cacheparams);
            return $html;
        }
    }
    public function hookDisplayBestSelling($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page'] :'custom_page';
        if(!Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_BESTSELLING'))
            return '';
        if (isset($this->_configs[$name_page])) {
            $cache_key = 'Crosssell:hookDisplayBestSelling_'.$name_page;
            if($name_page=='custom_page')
                $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_BESTSELLING_COUNT_PRODUCT') ? : 8;
            else
                $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') ? : 8;
            if($name_page=='custom_page')
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_BESTSELLING_LAYOUT');
            else
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
            if (!Cache::isStored($cache_key)) {
                $products = $this->getBestSellers($count_product);
                Cache::store($cache_key, $products);
            } else
                $products = Cache::retrieve($cache_key);
            if (isset($params['check']) && $params['check']) {
                if ($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'bestselling',
                'name_page' => $name_page,
                'layout_mode' => Tools::strpos($layout,'grid')!==false ? 'grid' : 'slide',
                'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_BESTSELLING_TITLE',$this->context->language->id) ? : $this->l('Best selling'),
            );
            return $this->displayProductList($params);
        }
        
    }
    public function hookDisplayTrendingProducts($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page']:'custom_page';
        if(isset($this->_configs[$name_page]) && Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_TRENDINGPRODUCTS'))
        {
            if($name_page=='custom_page')
                $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_TRENDINGPRODUCTS_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_TRENDINGPRODUCTS_COUNT_PRODUCT') :8;
            else
                $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
            $day = (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_TRENDINGPRODUCTS_DAY');
            $products = Ets_crosssell_db::getTrendingProducts($count_product,$day,isset($params['check']) && $params['check'] ? false : true);
            if($name_page=='custom_page')
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_TRENDINGPRODUCTS_LAYOUT');
            else
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
            if(isset($params['check']) && $params['check'])
            {
                if($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'trendingproducts',
                'name_page' => $name_page, 
                'layout_mode' => Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide',
                'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_TRENDINGPRODUCTS_TITLE',$this->context->language->id) ? :$this->l('Trending products'),
            );
            return $this->displayProductList($params);
        }
    }
    public function hookDisplayNewProducts($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page'] :'custom_page';
        if(!Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_NEWPRODUCTS'))
            return '';
        if($name_page=='custom_page')
            $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_NEWPRODUCTS_COUNT_PRODUCT') ? : 8;
        else
            $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') ? : 8;
        $enable_sort_by = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_NEWPRODUCTS_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_NEWPRODUCTS_SORT_BY_DEFAULT');
        $sort_by = ($sort_by = Tools::getValue('sort_by', $sort_by_default)) && in_array($sort_by, self::getSortOptions()) ? $sort_by : '';
        if($name_page=='custom_page')
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_TRENDINGPRODUCTS_LAYOUT');
        else
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
        $cacheparams = array(
            'tab' => 'newproducts',
            'name_page' => $name_page,
            'sort_by' => $sort_by,
            'ajax' => Tools::isSubmit('ajax'),
            'sort_options' => $enable_sort_by ? $this->_sort_options : false,
            'layout_mode' => Tools::strpos($layout,'grid')!==false ? 'grid' : 'slide'
        );
        if ($html = $this->getCache($cacheparams))
            return $html;
        if (isset($this->_configs[$name_page])) {
            $cache_key = 'Crosssell::newProducts_'.$name_page;
            if (!Cache::isStored($cache_key)) {
                $products = $this->getNewProducts($count_product, isset($params['order_by']) && $params['order_by'] ? $params['order_by'] : ($sort_by_default ? $sort_by_default : 'cp.position'));
                Cache::store($cache_key, $products);
            } else
                $products = Cache::retrieve($cache_key);

            if (isset($params['check']) && $params['check']) {
                if ($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'newproducts',
                'name_page' => $name_page,
                'sort_by' => $sort_by,
                'sort_options' => $enable_sort_by ? $this->_sort_options : false,
                'layout_mode' => Tools::strpos($layout,'grid')!==false ? 'grid' : 'slide',
                'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_NEWPRODUCTS_TITLE',$this->context->language->id) ? :$this->l('New products'),
            );
            $html = $this->displayProductList($params);
            $this->createCache($html, $cacheparams);
            return $html;
        }
    }
    public function hookDisplaySpecialProducts($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page']:'custom_page';
        if(!Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_SPECIALPRODUCTS'))
            return '';
        if($name_page=='custom_page')
            $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_SPECIALPRODUCTS_COUNT_PRODUCT') ? : 8;
        else
            $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') ? : 8;
        $enable_sort_by = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_SPECIALPRODUCTS_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_SPECIALPRODUCTS_SORT_BY_DEFAULT');
        $sort_by = ($sort_by = Tools::getValue('sort_by', $sort_by_default)) && in_array($sort_by, self::getSortOptions()) ? $sort_by : '';
        if($name_page=='custom_page')
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_SPECIALPRODUCTS_LAYOUT');
        else
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
        $cacheparams = array(
            'tab' => 'specialproducts',
            'name_page' => $name_page,
            'sort_by' => $sort_by,
            'ajax' => Tools::isSubmit('ajax'),
            'sort_options' => $enable_sort_by ?  $this->_sort_options : false,
            'layout_mode' =>Tools::strpos($layout,'grid')!==false ? 'grid' : 'slide'
        );
        if ($html = $this->getCache($cacheparams))
            return $html;
        if (isset($this->_configs[$name_page])) {
            $cache_key = 'Crosssell:hookDisplaySpecialProducts_'.$name_page;
            if (!Cache::isStored($cache_key)) {
                $products = $this->getSpecialProducts($count_product, isset($params['order_by']) && $params['order_by'] ? $params['order_by'] : ($sort_by_default ? $sort_by_default : 'cp.position'));
                Cache::store($cache_key, $products);
            } else
                $products = Cache::retrieve($cache_key);
            if (isset($params['check']) && $params['check']) {
                if ($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'specialproducts',
                'name_page' => $name_page,
                'sort_by' => $sort_by,
                'sort_options' => $enable_sort_by ?  $this->_sort_options : false,
                'layout_mode' =>Tools::strpos($layout,'grid')!==false ? 'grid' : 'slide',
                'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_SPECIALPRODUCTS_TITLE',$this->context->language->id) ? :$this->l('Special products'),
            );
            $html = $this->displayProductList($params);
            $this->createCache($html, $cacheparams);
            return $html;
        }
    }
    public function hookDisplayTopratedProducts($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page']:'custom_page';
        if (isset($this->_configs[$name_page]) && Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_TOPRATEDPRODUCTS') ) {
            $key_cache = 'Crosssell:hookDisplayTopratedProducts_'.$name_page;
            if($name_page=='custom_page')
                $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_TOPRATEDPRODUCTS_COUNT_PRODUCT') ? : 8;
            else
                $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') ? : 8;
            if($name_page=='custom_page')
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_TOPRATEDPRODUCTS_LAYOUT');
            else
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
            if (!Cache::isStored($key_cache)) {
                $products = Ets_crosssell_db::getTopRatedProducts($count_product);
                Cache::store($key_cache, $products);
            } else
                $products = Cache::retrieve($key_cache);
            if (isset($params['check']) && $params['check']) {
                if ($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'topratedproducts',
                'name_page' => $name_page,
                'layout_mode' => Tools::strpos($layout,'grid') !==false ? 'grid' : 'slide',
                'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_TOPRATEDPRODUCTS_TITLE',$this->context->language->id) ? :$this->l('Top rated products'),
            );
            return $this->displayProductList($params);
        }
    }
    public function hookDisplayPurchasedTogether($params)
    {
        $name_page = $params['name_page'];
        if (isset($this->_configs[$name_page])) {
            $id_product = isset($params['id_product']) ? (int)$params['id_product'] : 0;
            $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') : 8;
            $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
            $key_cache = 'Crosssell:hookDisplayPurchasedTogether';
            if (!Cache::isStored($key_cache)) {
                $products = Ets_crosssell_db::getProductPurchasedTogether($id_product, $count_product);
                Cache::store($key_cache, $products);
            } else
                $products = Cache::retrieve($key_cache);
            if (isset($params['check']) && $params['check']) {
                if ($products)
                    return true;
                else
                    return false;
            }
            $params = array(
                'products' => $products,
                'tab' => 'purchasedtogether',
                'name_page' => $name_page,
                'layout_mode' => $layout == 'listgrid' || $layout == 'tabgrid' ? 'grid' : 'slide'
            );
            return $this->displayProductList($params);
        }
    }
    public function hookDisplayProductInTheSameCategories($params)
    {
        $name_page = $params['name_page'];
        $count_product = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_COUNT_PRODUCT') : 8;
        $enable_sort_by = (int)Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_PRODUCTINTHESAMECATEGORIES_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_PRODUCTINTHESAMECATEGORIES_SORT_BY_DEFAULT');
        $id_product = isset($params['id_product']) ? (int)$params['id_product'] : 0;
        $sort_by = ($sort_by = Tools::getValue('sort_by', $sort_by_default)) && in_array($sort_by, self::getSortOptions()) ? $sort_by : '';
        $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
        if (!$id_product)
            return false;
        $cacheparams = array(
            'id_product' => $id_product,
            'tab' => 'productinthesamecategories',
            'name_page' => $name_page,
            'sort_by' => $sort_by,
            'ajax' => Tools::isSubmit('ajax'),
            'sort_options' => $enable_sort_by ? $this->_sort_options : false,
            'layout_mode' => $layout == 'listgrid' || $layout == 'tabgrid' ? 'grid' : 'slide'
        );
        if ($html = $this->getCache($cacheparams))
            return $html;
        if (isset($this->_configs[$name_page])) {
            if ($id_product) {
                $product = new Product($id_product);
                $id_category = $product->id_category_default;
                $cache_key = 'Crosssell:hookDisplayProductInTheSameCategories';
                if (!Cache::isStored($cache_key)) {
                    $products = Ets_crosssell_db::getProducts($id_category, 0, $count_product, isset($params['order_by']) ? $params['order_by'] : ($sort_by_default ? $sort_by_default : 'cp.position'), false, array($id_product));
                    Cache::store($cache_key, $products);
                } else
                    $products = Cache::retrieve($cache_key);
                if (isset($params['check']) && $params['check']) {
                    if ($products)
                        return true;
                    else
                        return false;
                }
                $params = array(
                    'products' => $products,
                    'tab' => 'productinthesamecategories',
                    'name_page' => $name_page,
                    'sort_by' => $sort_by,
                    'sort_options' => $enable_sort_by ? $this->_sort_options : false,
                    'layout_mode' => $layout == 'listgrid' || $layout == 'tabgrid' ? 'grid' : 'slide'
                );
                $html = $this->displayProductList($params);
                $this->createCache($html, $cacheparams);
                return $html;
            }
        }
    }
    public function hookDisplayProductInTheSameManufacture($params)
    {
        $name_page = $params['name_page'];
        $id_product= isset($params['id_product']) ? (int)$params['id_product'] :0;
        if(!$id_product)
            return false;
        $count_product= (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') ? (int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_COUNT_PRODUCT') :8;
        $enable_sort_by =(int)Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_PRODUCTINTHESAMEMANUFACTURE_ENABLE_SORT_BY');
        $sort_by_default = Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_PRODUCTINTHESAMEMANUFACTURE_SORT_BY_DEFAULT');
        $sort_by = ($sort_by = Tools::getValue('sort_by',$sort_by_default)) && in_array($sort_by,self::getSortOptions()) ? $sort_by:'';
        $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
        $cacheparams = array(
            'id_product' => $id_product,
            'tab' => 'productinthesamemanufacture',
            'name_page' => $name_page, 
            'sort_by' => $sort_by,
            'ajax' => Tools::isSubmit('ajax'),
            'sort_options' => $enable_sort_by ? $this->_sort_options:false,
            'layout_mode' => $layout == 'listgrid' || $layout == 'tabgrid' ? 'grid' : 'slide'
        );
        if($html = $this->getCache($cacheparams))
            return $html;
        if(isset($this->_configs[$name_page]))
        {
            if($id_product)
            {
                $product = new Product($id_product);
                $id_manufacturer = $product->id_manufacturer;
                $cache_key = 'Crosssell:hookDisplayProductInTheSameManufacture';
                if(!Cache::isStored($cache_key))
                {
                    $products = Ets_crosssell_db::getProducts(0,0,$count_product,isset($params['order_by']) ? $params['order_by']:($sort_by_default ? $sort_by_default : 'cp.position'),false,array($id_product),false,false,$id_manufacturer);
                    Cache::store($cache_key,$products);
                }
                else
                    $products = Cache::retrieve($cache_key);
                if(isset($params['check']) && $params['check'])
                {
                    if($products)
                        return true;
                    else
                        return false;
                }
                $params = array(
                    'products' => $products,
                    'tab' => 'productinthesamemanufacture',
                    'name_page' => $name_page, 
                    'sort_by' => $sort_by,
                    'sort_options' => $enable_sort_by ? $this->_sort_options:false,
                    'layout_mode' => $layout == 'listgrid' || $layout == 'tabgrid' ? 'grid' : 'slide'
                );
                $html = $this->displayProductList($params);
                $this->createCache($html,$cacheparams);
                return $html;
            }
        }
    }
    public function hookdisplayspecificproducts($params)
    {
        $name_page = isset($params['name_page']) && $params['name_page'] ? $params['name_page'] : 'custom_page';
        if(!Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_SPECIFICPRODUCTS') )
            return '';
        if ($name_page && ($productIds = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_SPECIFICPRODUCTS_ID_PRODUCTS'))) {
            if($name_page=='custom_page')
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_SPECIFICPRODUCTS_LAYOUT');
            else
                $layout = Configuration::get('ETS_CS_' . Tools::strtoupper($name_page) . '_LAYOUT');
            $cacheparams = array(
                'id_products' => $productIds,
                'tab' => 'specificproducts',
                'ajax' => Tools::isSubmit('ajax'),
                'layout_mode' => Tools::strpos($layout,'grid')!==false ? 'grid' : 'slide'
            );
            if ($html = $this->getCache($cacheparams))
                return $html;
            $IDs = explode(',', $productIds);
            $products = array();
            foreach ($IDs as $ID) {
                if ($ID && ($tmpIDs = explode('-', $ID)) && isset($tmpIDs[0]) && $tmpIDs[0] && ($product = new Product($tmpIDs[0])) && Validate::isLoadedObject($product) && $product->active) {
                    $products[] = array(
                        'id_product' => $tmpIDs[0],
                        'id_product_attribute' => !empty($tmpIDs[1]) ? $tmpIDs[1] : 0,
                    );
                }
            }
            if (isset($params['check']) && $params['check']) {
                if (!$products)
                    return false;
                else
                    return true;
            }

            if ($products) {
                $products = Ets_crosssell_db::getInstance()->getBlockProducts($products, true);
                $params = array(
                    'products' => $products,
                    'tab' => 'specificproducts',
                    'name_page' => $name_page,
                    'sort_by' => false,
                    'sort_options' => false,
                    'layout_mode' => Tools::strpos($layout,'grid')!==false ? 'grid' : 'slide',
                    'page_title' => Configuration::get('ETS_CS_'.Tools::strtoupper($name_page).'_SPECIFICPRODUCTS_TITLE',$this->context->language->id) ? :$this->l('Specific products'),
                );
                $html = $this->displayProductList($params);
                $this->createCache($html, $cacheparams);
                return $html;
            }
        }
    }
    public function displaySearchProductList($productIds)
    {
        if ($productIds)
        {
            $IDs = explode(',', $productIds);
            $products = array();
            foreach ($IDs as $ID) {
                if ($ID &&($tmpIDs = explode('-', $ID))) {
                    $products[] = array(
                        'id_product' => $tmpIDs[0],
                        'id_product_attribute' => !empty($tmpIDs[1])? $tmpIDs[1] : 0,
                    );
                }
            }
            if ($products) {
                $products = Ets_crosssell_db::getInstance()->getBlockProducts($products);
            }
            $this->smarty->assign('products', $products);
            return $this->display(__FILE__, 'block-product-item.tpl');
        }
    }
    public function addSpecificProduct()
    {
        if (($IDs = Tools::getValue('ids', false)) && self::validateArray($IDs))
        {
            die(
                json_encode(
                    array(
                        'html' => $this->displaySearchProductList($IDs),
                    )
                )
            );
        }
    }
    public static function validateArray($array,$validate='isCleanHtml')
    {
        if(!is_array($array))
        {
            if(method_exists('Validate',$validate))
            {
                return Validate::$validate($array);
            }
            else
                return true;
        }
        if(method_exists('Validate',$validate))
        {
            if($array && is_array($array))
            {
                $ok= true;
                foreach($array as $val)
                {
                    if(!is_array($val))
                    {
                        if($val && !Validate::$validate($val))
                        {
                            $ok= false;
                            break;
                        }
                    }
                    else
                        $ok = self::validateArray($val,$validate);
                }
                return $ok;
            }
        }
        return true;
    }
    public static function getSortOptions()
    {
        return array('cp.position desc','cp.position asc','pl.name asc','pl.name desc','price asc','price desc','p.id_product desc','p.id_product asc');
    }

    /**
     * @param string $layoutName
     * @return string
     */
    private function renderImgPreviewLayout($layoutName = 'layout_1', $label = '')
    {
        $fileName = 'product-layout-' . str_replace('_', '', $layoutName);
        $this->context->smarty->assign([
            'previewFileName' => $fileName,
            'layoutName' => $layoutName,
            'label' => $label
        ]);
        return $this->display(__FILE__, 'admin_preview_layout.tpl');
    }
}