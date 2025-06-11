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
{if $reward_history.results}
	{foreach $reward_history.results as $result}
		<tr>
			<td>{$result.id_ets_am_reward|escape:'html':'UTF-8'}</td>
			<td>{$result.program|escape:'html':'UTF-8'}</td>
			<td>{$result.amount|escape:'html':'UTF-8'}</td>
			<td>
                {if $result.type=='usage'}
                    {if $result.status==0}
                        <label class="label label-refunded">{l s='Refunded' mod='ets_affiliatemarketing'}</label>
                    {else}
                         <label class="label label-deducted">{l s='Deducted' mod='ets_affiliatemarketing'}</label>
                    {/if}
                {else}
                    {if $result.status == -2}
                    <label class="label label-danger">{l s='Expired' mod='ets_affiliatemarketing'}</label>
                    {elseif $result.status == -1}
                        <label class="label label-default">{l s='Canceled' mod='ets_affiliatemarketing'}</label>
                    {elseif $result.status == 0}
                        <label class="label label-warning">{l s='Pending' mod='ets_affiliatemarketing'}</label>
                    {else}
                        <label class="label label-success">{l s='Approved' mod='ets_affiliatemarketing'}</label>
                    {/if}
                {/if}
            </td>
			<td>{$result.datetime_added|escape:'html':'UTF-8'}</td>
			<td>{$result.note nofilter}</td>
            <td>{$result.product_name nofilter}</td>
			<td class="text-right">
				{if count($result.actions) > 1}
                <div class="btn-group">
                  <button type="button" class="btn btn-default {$result.actions[0].class|escape:'html':'UTF-8'}" data-id="{$result.actions[0].id|escape:'html':'UTF-8'}" {if isset($result.actions[0].action)}data-action="{$result.actions[0].action|escape:'html':'UTF-8'}"{/if}><i class="fa fa-{$result.actions[0].icon|escape:'html':'UTF-8'}"></i> {$result.actions[0].label|escape:'html':'UTF-8'}</button>
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu">
                    {foreach $result.actions as $k=>$v}
                        {if $k > 0}
                        <li><a href="javascript:void(0)" data-id="{$v.id|escape:'html':'UTF-8'}" class="{$v.class|escape:'html':'UTF-8'}" {if isset($v.action)}data-action="{$v.action|escape:'html':'UTF-8'}"{/if}><i class="fa fa-{$v.icon|escape:'html':'UTF-8'}"></i> {$v.label|escape:'html':'UTF-8'}</a></li>
                        {/if}
                    {/foreach}
                  </ul>
                </div>
            {elseif count($result.actions) == 1}
                <button href="javascript:void(0)" class="btn btn-default {$result.actions[0].class|escape:'html':'UTF-8'}" {if isset($result.actions[0].action|escape:'html':'UTF-8')}data-action="{$result.actions[0].action|escape:'html':'UTF-8'}"{/if} data-id="{$result.actions[0].id|escape:'html':'UTF-8'}"><i class="fa fa-{$result.actions[0].icon|escape:'html':'UTF-8'}"></i> {$result.actions[0].label|escape:'html':'UTF-8'}</button>
            {/if}
			</td>
		</tr>
	{/foreach}
{/if}