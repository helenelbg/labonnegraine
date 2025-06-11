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

<div class="ets-am-ref-popup" id="ets-am-ref-popup">
	<div class="ref-popup">
		
		<span class="ets-am-ref-popup-close"></span>
		{if $banner}
			<div class="box_banner">
				<img src="{$banner|escape:'html':'UTF-8'}" alt="baner">
			</div>
		{/if}
		<div class="popup-header">
			<h5>{$title|escape:'html':'UTF-8'}</h5>
		</div>
		<div class="popup-body">
			{$content nofilter}
			<div class="join-referral">
				<a href="{$link_ref|escape:'html':'UTF-8'}" class="btn-eam-join-ref btn-popup">{l s='Join our sale team' mod='ets_affiliatemarketing'}</a>
				<a href="javascript:void(0)" class="btn-popup btn-popup-close js-eam-close-popup-ref">{l s='No Thanks' mod='ets_affiliatemarketing'}</a>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var eam_show_popup_ref = "{$show_popup nofilter}";
	var eam_link_ajax_exec = "{$link_ajax nofilter}";
</script>
