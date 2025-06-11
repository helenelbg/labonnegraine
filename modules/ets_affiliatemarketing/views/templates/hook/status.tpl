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
{if $loy_status==0}
    <span class="loy_status pending">{l s='Pending' mod='ets_affiliatemarketing'}</span>
{/if}
{if $loy_status==1}
    <span class="loy_status approved">{l s='Approved' mod='ets_affiliatemarketing'}</span>
{/if}
{if $loy_status==-1}
    <span class="loy_status canceled">{l s='Canceled' mod='ets_affiliatemarketing'}</span>
{/if}
{if $loy_status==-2}
    <span class="loy_status expired">{l s='Expired' mod='ets_affiliatemarketing'}</span>
{/if}