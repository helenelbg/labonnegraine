{*
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
*}
{extends file="helpers/form/form.tpl"}
{block name="legend"}
	<div class="panel-heading">
		{if isset($field.image) && isset($field.title)}<img src="{$field.image|escape:'html':'UTF-8'}" alt="{$field.title|escape:'html':'UTF-8'}" />{/if}
		{if isset($field.icon)}<i class="{$field.icon|escape:'html':'UTF-8'}"></i>{/if}
		{$field.title|escape:'html':'UTF-8'}
        {if (isset($add_new) && $add_new) || $submit_action=='savePaymentMethod'}
            <span class="panel-heading-action">
                {if $submit_action=='savePaymentMethod'}
                    <a class="list-toolbar-btn" href="{$link->getAdminLink('AdminPaymentFee',true)|escape:'html':'UTF-8'}">
                        <span data-placement="top" data-html="true" data-original-title="{l s='Set fee for other payment methods' mod='ets_payment_with_fee'}" class="label-tooltip" data-toggle="tooltip" title="{l s='Set fee for other payment methods' mod='ets_payment_with_fee'}">
            				<i class="icon icon-cogs"></i>
                        </span>
                    </a>  
                {/if}
                {if isset($add_new) && $add_new}
                    <a class="list-toolbar-btn" href="{$link->getAdminLink('AdminModules', true)|escape:'html':'UTF-8'}&configure=ets_payment_with_fee&tab_module=payments_gateways&module_name=ets_payment_with_fee&control=payment_method">
                        <span data-placement="top" data-html="true" data-original-title="{l s='Add custom payment method with fee' mod='ets_payment_with_fee'}" class="label-tooltip" data-toggle="tooltip" title="{l s='Add custom payment method with fee' mod='ets_payment_with_fee'}">
            				<i class="process-icon-new"></i>
                        </span>
                    </a>
                {/if}
            </span>
        {/if}
	</div>
{/block}
{block name="field"}
{$smarty.block.parent}
{if $input.type == 'file' && (!isset($input.imageType) || isset($input.imageType) && $input.imageType!='thumb')&&  isset($display_img) && $display_img}
    <label class="control-label col-lg-3 uploaded_image_label" style="font-style: italic;">{l s='Uploaded image: ' mod='ets_payment_with_fee'}</label>
    <div class="col-lg-9 uploaded_img_wrapper">
		<a  class="ets_fancy" href="{$display_img|escape:'html':'UTF-8'}"><img title="{l s='Click to see full size image' mod='ets_payment_with_fee'}" style="display: inline-block; max-width: 200px;" src="{$display_img|escape:'html':'UTF-8'}" /></a>
        {if isset($img_del_link) && $img_del_link && !(isset($input.required) && $input.required)}
            <a class="delete_url" style="display: inline-block; text-decoration: none!important;" href="{$img_del_link|escape:'html':'UTF-8'}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
        {/if}
    </div>
{elseif $input.type == 'file' && isset($input.imageType) && $input.imageType=='thumb' &&  isset($display_thumb) && $display_thumb}
    <label class="control-label col-lg-3 uploaded_image_label" style="font-style: italic;">{l s='Uploaded image: ' mod='ets_payment_with_fee'}</label>
    <div class="col-lg-9 uploaded_img_wrapper">
		<a  class="ets_fancy" href="{$display_thumb|escape:'html':'UTF-8'}"><img title="{l s='Click to see full size image' mod='ets_payment_with_fee'}" style="display: inline-block; max-width: 200px;" src="{$display_thumb|escape:'html':'UTF-8'}" /></a>
        {if isset($thumb_del_link) && $thumb_del_link && !(isset($input.required) && $input.required)}
            <a class="delete_url"  style="display: inline-block; text-decoration: none!important;" href="{$thumb_del_link|escape:'html':'UTF-8'}"><span style="color: #666"><i style="font-size: 20px;" class="process-icon-delete"></i></span></a>
        {/if}
    </div>
{/if}
{/block}

