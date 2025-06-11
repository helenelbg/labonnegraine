<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Pour que le module soit complètement fonctionnel, il faut :
 * - Mettre la liaison du TPL de ce module dans le layout-both-column du theme
 * - Mettre en place la surcharge au niveau du add-to-cart
**/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Aw_googlesuite extends Module{
    public function __construct(){
        $this->name = 'aw_googlesuite';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Andy - Anjou Web';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Suite Google By AW');
        $this->description = $this->l('Gestion de GTM / Google Ads et GA4');

        $this->confirmUninstall = $this->l('Êtes-vous certain de vouloir supprimer ce module ?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }
    public function install(){
        return parent::install();
    }

    public function uninstall(){
        return parent::uninstall();
    }

    public function hookDisplayHeader(){
        if(!empty(Context::getContext()) && !empty(Context::getContext()->controller) && !empty(Context::getContext()->controller->php_self)){
            switch (Context::getContext()->controller->php_self){
                case "category" :
                    $this->view_item_list();
                    break;
                case "product" :
                    $this->view_item();
                    break;
                case "cart" :
                    $this->view_cart();
                    break;
                case "order" :
                    $this->begin_checkout();
                    break;
                default:
                    break;
            }
        }
    }

    private function view_item_list(){
        $id_lang = 1;
        $pageNum = !empty($_GET["page"]) ? $_GET["page"] : 1;
        $pageQte = 48;

        $categ = Context::getContext()->controller->getCategory();
        $products = $categ->getproducts($id_lang, $pageNum, $pageQte);
        
        $categList[] = $categ->link_rewrite;
        $categ2 = $categ;

        while($categ2->id_parent > 2){
            $categ2 = new Category($categ2->id_parent);
            $categList[] = $categ2->link_rewrite[1];
        }

        rsort($categList);

        $datalayer["event"] = "view_item_list";
        $datalayer["ecommerce"]["items"] = [];

        foreach ($products as $product){
            $datalayer["ecommerce"]["items"][] = [
                "item_id" => $product["id_product"],
                "item_name" => $product["name"],
                "price" => $product["price"],
                "currency" => "EUR",
                "item_list_id" => $categ->link_rewrite,
                "item_list_name" => $categ->name
            ];
        }

        foreach ($datalayer["ecommerce"]["items"] as &$item){
            $i = 1;
            foreach ($categList as $categL){
                $index = ($i == 1) ? "item_category" : "item_category".$i;
                $item[$index] = $categL;
                $i++;
            }
        }

        $this->context->smarty->assign([
            'datalayer' => json_encode($datalayer)
        ]);
    }

    private function view_item(){
        $product = Context::getContext()->controller->getProduct();

        $datalayer["event"] = "view_item";
        $datalayer["ecommerce"]["currency"] = "EUR";
        $datalayer["ecommerce"]["value"] = "";
        $datalayer["ecommerce"]["items"] = [
            "item_id" => $product->id,
            "item_name" => $product->name
        ];

        $this->context->smarty->assign([
            'datalayer' => json_encode($datalayer)
        ]);
    }

    private function view_cart(){
         $cart = Context::getContext()->cart;
         $total = 0;

         $datalayer["event"] = "view_cart";
         $datalayer["ecommerce"]["currency"] = "EUR";
         $datalayer["ecommerce"]["items"] = [];

         foreach ($cart->getProducts() as $product){
             $datalayer["ecommerce"]["items"][] = [
                 "item_id" => $product["id_product"],
                 "quantity" => $product["cart_quantity"],
                 "item_name" => $product["name"],
                 "price" => $product["total_wt"]
             ];

             $total += $product["total_wt"];
         }

        $datalayer["ecommerce"]["value"] = $total;

         $this->context->smarty->assign([
             'datalayer' => json_encode($datalayer)
         ]);
    }

    private function begin_checkout(){
        $cart = Context::getContext()->cart;
        $total = 0;

        $datalayer["event"] = "begin_checkout";
        $datalayer["ecommerce"]["currency"] = "EUR";
        $datalayer["ecommerce"]["items"] = [];

        foreach ($cart->getProducts() as $product){
            $datalayer["ecommerce"]["items"][] = [
                "item_id" => $product["id_product"],
                "quantity" => $product["cart_quantity"],
                "item_name" => $product["name"],
                "price" => $product["total_wt"]
            ];

            $total += $product["total_wt"];
        }

        $datalayer["ecommerce"]["value"] = $total;

        $this->context->smarty->assign([
            'datalayer' => json_encode($datalayer)
        ]);
    }
}
