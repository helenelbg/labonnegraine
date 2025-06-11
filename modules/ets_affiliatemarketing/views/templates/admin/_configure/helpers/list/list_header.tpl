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
{extends file="helpers/list/list_header.tpl"}
{block name="preTable"}
	{assign var='_svg_plus_circle' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 960v-128q0-26-19-45t-45-19h-256v-256q0-26-19-45t-45-19h-128q-26 0-45 19t-19 45v256h-256q-26 0-45 19t-19 45v128q0 26 19 45t45 19h256v256q0 26 19 45t45 19h128q26 0 45-19t19-45v-256h256q26 0 45-19t19-45zm320-64q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg></i>'}
	{assign var='_svg_search' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1216 832q0-185-131.5-316.5t-316.5-131.5-316.5 131.5-131.5 316.5 131.5 316.5 316.5 131.5 316.5-131.5 131.5-316.5zm512 832q0 52-38 90t-90 38q-54 0-90-38l-343-342q-179 124-399 124-143 0-273.5-55.5t-225-150-150-225-55.5-273.5 55.5-273.5 150-225 225-150 273.5-55.5 273.5 55.5 225 150 150 225 55.5 273.5q0 220-124 399l343 343q37 37 37 90z"/></svg></i>'}

	<script type="text/javascript">

		var aff_link_search_customer = '{$aff_link_search_customer nofilter}';
    </script>
    <div class="aff_popup_wapper aff_add_user">
		<div class="popup_table">
			<div class="popup_tablecell">
				<div class="aff_popup_content">
					<span class="aff_close_popup">{l s='Close' mod='ets_affiliatemarketing'}</span>
					<div class="form-group">
						<label class="control-label col-lg-3" for="aff_search_customer_user"> {l s='Search for customers' mod='ets_affiliatemarketing'} </label>
						<div class="col-lg-9">
							<div class="input-group ">
								<input type="hidden" id="aff_id_search_customer_user" value="" />
                                <input id="aff_search_customer_user" class="" name="aff_search_customer_user" value="" placeholder="{l s='Search for customer by ID, email or name' mod='ets_affiliatemarketing'}" type="text" />
								<span class="input-group-addon"> {$_svg_search nofilter}</span>
							</div>
						</div>
					</div>
					<div class="form-group">
					    <label class="control-label col-lg-3">{l s='Join program' mod='ets_affiliatemarketing'}</label>
                        <div class="col-lg-9">
                            <div class="checkbox_group">
                                <label for="aff_customer_loyalty"><input type="checkbox" id="aff_customer_loyalty" name="aff_customer_loyalty" /> {l s='Loyalty program' mod='ets_affiliatemarketing'}</label><br />
                                <label for="aff_customer_referral"><input type="checkbox" id="aff_customer_referral" name="aff_customer_referral" /> {l s='Referral / Sponsorship program' mod='ets_affiliatemarketing'}</label><br />
                                <label for="aff_customer_affiliate"><input type="checkbox" id="aff_customer_affiliate" name="aff_customer_affiliate" /> {l s='Affiliate program' mod='ets_affiliatemarketing'}</label><br />
                            </div>
                        </div>   
					</div>
					<div class="form-group">
						<div class="col-lg-3"></div>
						<div class="col-lg-9">
							<button class="btn btn-default full-right" name="submitAddUserReward">{$_svg_plus_circle nofilter} {l s='Add' mod='ets_affiliatemarketing'}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
	<button class="btn btn-default full-right" name="btnAddNewUserReward">{$_svg_plus_circle nofilter} {l s='Add user' mod='ets_affiliatemarketing'}</button>
{/block}