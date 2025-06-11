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

<div class="ets-am-ref-popup ets-am-ref-popup-voucher" style="display: block;" id="ets-am-ref-popup-voucher">
	<div class="ref-popup">
		<span class="ets-am-ref-popup-voucher-close"></span>
		<div class="popup-header">
			<div class="popup-title">
				{l s='Congratulation! You get our discount' mod='ets_affiliatemarketing'}
			</div>
		</div>
		<div class="popup-body">
			<div class="mb-10">
				{$voucher.message nofilter}
			</div>
			<div class="my-voucher">
				<span class="voucher-icon">{$_svg_scissors nofilter}</span> <div class="code-text eam-copy-clipboard" data-text="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'} <span class="eam-inner-copy-tooltip" data-copied="{l s='Copied' mod='ets_affiliatemarketing'}">{l s='Click to copy voucher code' mod='ets_affiliatemarketing'}</span></div>
			</div>
			<p class="text-center mt-20">{l s='The discount is only available from' mod='ets_affiliatemarketing'} {if $voucher.from == '00-00-0000' && $voucher.to == '00-00-0000'}{l s='unlimited' mod='ets_affiliatemarketing'}{else}<strong>{$voucher.from|escape:'html':'UTF-8'}</strong> {l s='to' mod='ets_affiliatemarketing'} <strong>{$voucher.to|escape:'html':'UTF-8'}</strong>{/if}</p>
		</div>
	</div>
</div>
<script type="text/javascript">
	var eam_show_popup_voucher_ref = "{$show_popup_voucher nofilter}";
	var eam_link_ajax_exec = "{$link_ajax nofilter}";
</script>
