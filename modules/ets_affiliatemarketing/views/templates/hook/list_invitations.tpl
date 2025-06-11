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
{assign var='_svg_clock_o' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1024 544v448q0 14-9 23t-23 9h-320q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h224v-352q0-14 9-23t23-9h64q14 0 23 9t9 23zm416 352q0-148-73-273t-198-198-273-73-273 73-198 198-73 273 73 273 198 198 273 73 273-73 198-198 73-273zm224 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>'}
{assign var='_svg_check_icon' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg></i>'}
{if $invitations.result}
	{foreach $invitations.result as $result}
		<tr>
			<td class="text-left">{$result.email nofilter}</td>
			<td class="text-left">{$result.username nofilter}</td>
			<td class="text-left">{dateFormat date=$result.datetime_sent full=0}</td>
			<td  class="text-center">
				{if $result.status}
					<span class="eam-text-green eam-mr-8">
						{$_svg_check_icon nofilter}
					</span> {l s='Registered' mod='ets_affiliatemarketing'}
				{else}
					<span class="eam-text-orange  eam-mr-8">
						{$_svg_clock_o nofilter}
					</span> {l s='Pending' mod='ets_affiliatemarketing'}
				{/if}
			</td>
		</tr>
	{/foreach}
    {if $invitations.total_page > $invitations.current_page}
        <tr class="refer-friends">
            <td colspan="100%" style="text-align: center;"><a class="button-refer-friends" href="{$link->getModuleLink('ets_affiliatemarketing','refer_friends',['page'=>$invitations.current_page|intval+1,'load_more'=>true,'ajax'=>true])}">{l s='Load more' mod='ets_affiliatemarketing'}</a></td>
        </tr>
    {/if}
{/if}