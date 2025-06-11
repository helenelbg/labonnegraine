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

<div class="form-group">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='Enable this option to automatically override existing specific prices with flash sales custom prices' mod='flashsales'}">
            {l s='Override specific prices' mod='flashsales'}
        </span>
    </label>
    <div class="col-lg-4">
        <select id="flashsale_del_specificprice" name="flashsale_del_specificprice">
            <option value="0" {if Configuration::get('FLASHSALE_DEL_SPECIFICPRICE') == 0}selected="selected"{/if}>{l s='Disabled' mod='flashsales'}</option>
            <option value="1" {if Configuration::get('FLASHSALE_DEL_SPECIFICPRICE') == 1}selected="selected"{/if}>{l s='Soft mode' mod='flashsales'}</option>
            <option value="2" {if Configuration::get('FLASHSALE_DEL_SPECIFICPRICE') == 2}selected="selected"{/if}>{l s='Hard mode' mod='flashsales'}</option>
        </select>
    </div>
</div>

{if $topmenu}
<div class="form-group">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='Enable this option to display a top menu quick link to flashale page' mod='flashsales'}">
            {l s='Top menu quick link' mod='flashsales'}
        </span>
    </label>
    <div class="col-lg-4">
        <span class="switch prestashop-switch fixed-width-lg">
        <input id="flashsale_display_topmenu_on" type="radio" value="1" name="flashsale_display_topmenu" {if Configuration::get('FLASHSALE_DISPLAY_TOPMENU')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_display_topmenu_on">{l s='Enabled' mod='flashsales'}</label>
        <input id="flashsale_display_topmenu_off" type="radio" value="0" name="flashsale_display_topmenu" {if !Configuration::get('FLASHSALE_DISPLAY_TOPMENU')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_display_topmenu_off">{l s='Disabled' mod='flashsales'}</label>
        <a class="slide-button btn"></a>
    </div>
</div>
{/if}

<div class="form-group">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='Enable this option to use the template provides with the module for displaying products in slider' mod='flashsales'}">
            {l s='Use module template for products in slider' mod='flashsales'}
        </span>
    </label>
    <div class="col-lg-4">
        <span class="switch prestashop-switch fixed-width-lg">
        <input id="flashsale_product_list_on" type="radio" value="1" name="flashsale_product_list" {if Configuration::get('FLASHSALE_PRODUCT_LIST')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_product_list_on">{l s='Yes' mod='flashsales'}</label>
        <input id="flashsale_product_list_off" type="radio" value="0" name="flashsale_product_list" {if !Configuration::get('FLASHSALE_PRODUCT_LIST')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_product_list_off">{l s='No' mod='flashsales'}</label>
        <a class="slide-button btn"></a>
    </div>
</div>

<input type="hidden" name="css_file" class="css_file" value="{if isset($css_file)}{$css_file}{* HTML, cannot escape *}{/if}">
