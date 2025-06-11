{*
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
*}

<ul class="nav nav-tabs nav-justified" role="tablist">
    <li role="presentation" class="active"><a href="#home_page" aria-controls="home_page" role="tab" data-toggle="tab">{l s='Home page' mod='flashsales'}</a></li>
    <li role="presentation"><a href="#flashsale_page" aria-controls="flashsale_page" role="tab" data-toggle="tab">{l s='Flashsale page' mod='flashsales'}</a></li>
    <li role="presentation"><a href="#product_page" aria-controls="product_page" role="tab" data-toggle="tab">{l s='Product page' mod='flashsales'}</a></li>
    <li role="presentation"><a href="#product_list" aria-controls="product_list" role="tab" data-toggle="tab">{l s='Product list' mod='flashsales'}</a></li>
    <li role="presentation"><a href="#column" aria-controls="column" role="tab" data-toggle="tab">{l s='Column' mod='flashsales'}</a></li>
</ul>

<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home_page">{include file="$tpl_tab_home_page"}</div>
    <div role="tabpanel" class="tab-pane" id="flashsale_page">{include file="$tpl_tab_flashsale_page"}</div>
    <div role="tabpanel" class="tab-pane" id="product_page">{include file="$tpl_tab_product_page"}</div>
    <div role="tabpanel" class="tab-pane" id="product_list">{include file="$tpl_tab_product_list"}</div>
    <div role="tabpanel" class="tab-pane" id="column">{include file="$tpl_tab_column"}</div>
</div>
