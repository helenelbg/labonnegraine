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
    <label class="control-label col-lg-3 required">
        <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='Invalid characters:' mod='flashsales'} &lt;&gt;;=#{}">
            {l s='Heading title' mod='flashsales'}
        </span>
    </label>
    <div class="col-lg-9">
        {foreach from=$languages item=language}
        {if $languages|count > 1}
        <div class="row">
            <div class="translatable-field lang-{$language.id_lang|intval}" {if $language.id_lang != $default_language}style="display:none"{/if}>
                <div class="col-lg-9">
        {/if}
                    <input type="text" id="flashsale_title_flashsale_page_{$language.id_lang|intval}" name="flashsale_title_flashsale_page_{$language.id_lang|intval}" value="{Configuration::get('FLASHSALE_TITLE_FLASHSALE_PAGE', $language.id_lang)|escape:'html':'UTF-8'}">
        {if $languages|count > 1}
                </div>
                <div class="col-lg-2">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        {$language.iso_code|escape:'html':'UTF-8'}
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        {foreach from=$languages item=language}
                        <li><a href="javascript:hideOtherLanguage({$language.id_lang|intval});" tabindex="-1">{$language.name|escape:'html':'UTF-8'}</a></li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        </div>
        {/if}
        {/foreach}
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='Number of products to display in the flash sale page. Default value : 4' mod='flashsales'}">
            {l s='Products to displayed in page' mod='flashsales'}
        </span>
    </label>
    <div class="col-sm-6 col-md-4 col-lg-2">
        <div class="input-group">
            <input type="text" name="flashsale_products_nb_flashsale_page" id="flashsale_products_nb_flashsale_page" value="{Configuration::get('FLASHSALE_PRODUCTS_NB_FLASHSALE_PAGE')|intval}"/>
            <span class="input-group-addon">{l s='products' mod='flashsales'}</span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='Display flash sale description' mod='flashsales'}">
            {l s='Display description' mod='flashsales'}
        </span>
    </label>
    <div class="col-lg-4">
        <span class="switch prestashop-switch fixed-width-lg">
        <input id="flashsale_description_flashsale_page_on" type="radio" value="1" name="flashsale_description_flashsale_page" {if Configuration::get('FLASHSALE_DESCRIPTION_FLASHSALE_PAGE')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_description_flashsale_page_on">{l s='Yes' mod='flashsales'}</label>
        <input id="flashsale_description_flashsale_page_off" type="radio" value="0" name="flashsale_description_flashsale_page" {if !Configuration::get('FLASHSALE_DESCRIPTION_FLASHSALE_PAGE')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_description_flashsale_page_off">{l s='No' mod='flashsales'}</label>
        <a class="slide-button btn"></a>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='Display banner image and countdown' mod='flashsales'}">
            {l s='Display banner' mod='flashsales'}
        </span>
    </label>
    <div class="col-lg-4">
        <span class="switch prestashop-switch fixed-width-lg">
        <input id="flashsale_banner_flashsale_page_on" type="radio" value="1" name="flashsale_banner_flashsale_page" {if Configuration::get('FLASHSALE_BANNER_FLASHSALE_PAGE')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_banner_flashsale_page_on">{l s='Yes' mod='flashsales'}</label>
        <input id="flashsale_banner_flashsale_page_off" type="radio" value="0" name="flashsale_banner_flashsale_page" {if !Configuration::get('FLASHSALE_BANNER_FLASHSALE_PAGE')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_banner_flashsale_page_off">{l s='No' mod='flashsales'}</label>
        <a class="slide-button btn"></a>
    </div>
</div>

<div class="form-group">
    <label class="control-label col-lg-3">
        <span class="label-tooltip" data-toggle="tooltip"
        title="{l s='Display flash sale products into carousel' mod='flashsales'}">
            {l s='Display carousel' mod='flashsales'}
        </span>
    </label>
    <div class="col-lg-4">
        <span class="switch prestashop-switch fixed-width-lg">
        <input id="flashsale_carousel_flashsale_page_on" type="radio" value="1" name="flashsale_carousel_flashsale_page" {if Configuration::get('FLASHSALE_CAROUSEL_FLASHSALE_PAGE')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_carousel_flashsale_page_on">{l s='Yes' mod='flashsales'}</label>
        <input id="flashsale_carousel_flashsale_page_off" type="radio" value="0" name="flashsale_carousel_flashsale_page" {if !Configuration::get('FLASHSALE_CAROUSEL_FLASHSALE_PAGE')|intval}checked="checked"{/if}>
        <label class="radioCheck" for="flashsale_carousel_flashsale_page_off">{l s='No' mod='flashsales'}</label>
        <a class="slide-button btn"></a>
    </div>
</div>
