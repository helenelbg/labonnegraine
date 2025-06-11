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
{assign var='_svg_plus' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1600 736v192q0 40-28 68t-68 28h-416v416q0 40-28 68t-68 28h-192q-40 0-68-28t-28-68v-416h-416q-40 0-68-28t-28-68v-192q0-40 28-68t68-28h416v-416q0-40 28-68t68-28h192q40 0 68 28t28 68v416h416q40 0 68 28t28 68z"/></svg></i>'}
{assign var='_svg_pencil' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M491 1536l91-91-235-235-91 91v107h128v128h107zm523-928q0-22-22-22-10 0-17 7l-542 542q-7 7-7 17 0 22 22 22 10 0 17-7l542-542q7-7 7-17zm-54-192l416 416-832 832h-416v-416zm683 96q0 53-37 90l-166 166-416-416 166-165q36-38 90-38 53 0 91 38l235 234q37 39 37 91z"/></svg></i>'}
{assign var='_svg_trash' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></i>'}

<div class="payment-setting eam-ox-auto">
	<div class="eam-minwidth-900">
		<div class="panel-action">
			<a href="{$link_pm nofilter}&create_pm=1" class="btn btn-default">{$_svg_plus nofilter} {l s='Add new method' mod='ets_affiliatemarketing'}</a>
		</div>
		<table class="table table-bordered eam-datatables">
			<thead>
				<tr>
					<th>{l s='ID' mod='ets_affiliatemarketing'}</th>
					<th>{l s='Title' mod='ets_affiliatemarketing'}</th>
					<th>{l s='Fee type' mod='ets_affiliatemarketing'}</th>
					<th>{l s='Fee amount' mod='ets_affiliatemarketing'}</th>
					<th>{l s='Status' mod='ets_affiliatemarketing'}</th>
					<th class="text-center">{l s='Sort order' mod='ets_affiliatemarketing'}</th>
					<th style="width: 150px;">{l s='Action' mod='ets_affiliatemarketing'}</th>
				</tr>
			</thead>
			<tbody class="list-pm">
				{if $payment_methods}
					{foreach $payment_methods as $p}
					<tr data-id="{$p.id_ets_am_payment_method|escape:'html':'UTF-8'}">
						<td>{$p.id_ets_am_payment_method|escape:'html':'UTF-8'}</td>
						<td>{$p.title|escape:'html':'UTF-8'}</td>
						<td>
							{if $p.fee_type == 'PERCENT'}
								{l s='Percentage' mod='ets_affiliatemarketing'}
							{elseif $p.fee_type == 'FIXED'}
								{l s='Fixed' mod='ets_affiliatemarketing'}
							{else}
								{l s='No fee' mod='ets_affiliatemarketing'}
							{/if}
						</td>
						<td>
							{if $p.fee_type == 'PERCENT'}
								{$p.fee_percent|escape:'html':'UTF-8'} %
							{elseif $p.fee_type == 'FIXED'}
								{$p.fee_fixed|escape:'html':'UTF-8'}
							{/if}
						</td>
						<td>
							{if $p.enable == 1}
								<span class="label label-success">{l s='Enabled' mod='ets_affiliatemarketing'}</span>
							{else}
								<span class="label label-default">{l s='Disabled' mod='ets_affiliatemarketing'}</span>
							{/if}
						</td>
						<td class="eam-active-sortable"><div class="box-drag"><i class="fa fa-arrows"></i><span class="sort-order">{$p.sort|escape:'html':'UTF-8'}</span></div></td>
						<td>
							<!-- Split button -->
							<div class="btn-group">
							  <a href="{$link_pm nofilter}&payment_method={$p.id_ets_am_payment_method|escape:'html':'UTF-8'}&edit_pm=1" class="btn btn-default" style="text-transform: inherit;">
								  {$_svg_pencil nofilter} {l s='Edit' mod='ets_affiliatemarketing'}
								</a>
							  <button type="button" class="btn btn-default dropdown-toggle dropdown-has-form" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							    <span class="caret"></span>
							    <span class="sr-only">Toggle Dropdown</span>
							  </button>
							  <ul class="dropdown-menu">
							    <li>
							    	<a href="javascript:void(0)">
							    		<form style="display: inline-block;" action="{$link_pm|escape:'html':'UTF-8'}&payment_method={$p.id_ets_am_payment_method|escape:'html':'UTF-8'}&delete_pm=1" method="POST" onsubmit="return eamConfirmDelete()">
											<button type="submit" name="delete_payment_method" class="btn btn-link btn-link-dropdown">{$_svg_trash nofilter} {l s='Delete' mod='ets_affiliatemarketing'}</button>
										</form>
							    	</a>
							    </li>
							  </ul>
							</div>
							
							
							
						</td>
					</tr>
					{/foreach}
				{else}
				<tr>
	                <td colspan="100%" style="text-align: center;">
	                    {l s='No data found' mod='ets_affiliatemarketing'}
	                </td>
	            </tr>
				{/if}
			</tbody>
		</table>
	</div>
</div>