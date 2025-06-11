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
<p class="payment_module">
	<a class="bankwire" href="{$link->getModuleLink('ets_affiliatemarketing', 'validation')|escape:'html':'UTF-8'}" title="{l s='Pay by reward' mod='ets_affiliatemarketing'}">
		{l s='Pay by reward' mod='ets_affiliatemarketing'} <span>(
        {if $show_point}                
    		{l s='You have' mod='ets_affiliatemarketing'} {$eam_reward_total_balance|escape:'html':'UTF-8'} ({$eam_reward_point|escape:'html':'UTF-8'}) {l s='in your reward balance that can be used to pay for this order.' mod='ets_affiliatemarketing'}
    	{else}
        	{l s='You have' mod='ets_affiliatemarketing'} {$eam_reward_total_balance|escape:'html':'UTF-8'} {l s='in your reward balance that can be used to pay for this order.' mod='ets_affiliatemarketing'}
        {/if})</span>
	</a>
</p>