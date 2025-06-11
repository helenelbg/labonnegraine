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
{assign var='_svg_copy' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1696 384q40 0 68 28t28 68v1216q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-288h-544q-40 0-68-28t-28-68v-672q0-40 20-88t48-76l408-408q28-28 76-48t88-20h416q40 0 68 28t28 68v328q68-40 128-40h416zm-544 213l-299 299h299v-299zm-640-384l-299 299h299v-299zm196 647l316-316v-416h-384v416q0 40-28 68t-68 28h-416v640h512v-256q0-40 20-88t48-76zm956 804v-1152h-384v416q0 40-28 68t-68 28h-416v640h896z"/></svg></i>'}
{if $is_aff}
{$message|replace:'[commission_value]':$commission|replace:'[affiliate_link]':'' nofilter}
<div class="input-group input-group-sm eam-form-group mt-10">
	<input type="text" class="eam-input-link form-control" class="eam-input-link disabled eam-tooltip" value="{$link nofilter}" aria-describedby="eam-affiliate-link-add-on">
	<span class="input-group-addon eam-tooltip" data-eam-tooltip="{l s='Click to copy affiliate link' mod='ets_affiliatemarketing'}" data-eam-copy="{l s='Copied to clipboard' mod='ets_affiliatemarketing'}" id="eam-affiliate-link-add-on">{$_svg_copy nofilter}</span>
</div>
{else}
{$message|replace:'[commission_value]':$commission|replace:'[join_button]':'' nofilter}
<div class="btn-group-join-aff">
	<a href="{$link nofilter}" class="btn btn-info eam-button">{l s='Join Affiliate Program' mod='ets_affiliatemarketing'}</a>
</div>
{/if}