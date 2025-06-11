{*
*
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
{assign var='_svg_close_icon' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"/></svg></i>'}
{assign var='_svg_check_icon' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"/></svg></i>'}
{assign var='_svg_trash' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M704 736v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></svg></i>'}
<div class="eam-view-application">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel" style="padding: 0;">
					<div class="info-box">
						<div class="row">
							<div class="col-lg-6 col-sm-6 eam-col-rp">
								<div class="form-horizontal">
									<div class="row">
										<label class="control-label col-lg-6 col-sm-6 col-xs-6"><em>{l s='Full name:' mod='ets_affiliatemarketing'}</em></label>
										<div class="col-lg-6 col-xs-6 col-sm-6">
											<p class="form-control-static"><a href="{$user_link|escape:'html':'UTF-8'}&id_customer={$app.id_customer|escape:'html':'UTF-8'}&viewreward_users" title="{l s='View customer' mod='ets_affiliatemarketing'}" target="_blank">
												{if $app.email}
												{$app.firstname|escape:'html':'UTF-8'} {$app.lastname|escape:'html':'UTF-8'}
												{else}
												<span class="warning-deleted">{l s='User deleted' mod='ets_affiliatemarketing'}
												{/if}
										</a></p>
										</div>
									</div>
									<div class="row">
										<label class="control-label col-lg-6 col-xs-6"><em>{l s='Email:' mod='ets_affiliatemarketing'}</em></label>
										<div class="col-lg-6 col-sm-6 col-xs-6">
											<p class="form-control-static">{$app.email|escape:'html':'UTF-8'}</p>
										</div>
									</div>
									{if $app.intro}
									<div class="row">
										<label class="control-label col-lg-6 col-sm-6"><em>{l s='Introduction about customer:' mod='ets_affiliatemarketing'}</em></label>
										<div class="col-lg-6 col-sm-6 col-xs-6">
											<p class="form-control-static">{$app.intro|escape:'html':'UTF-8'}</p>
										</div>
									</div>
									{/if}
									<div class="row">
										<label class="control-label col-lg-6 col-sm-6 col-xs-6"><em>{l s='Register date:' mod='ets_affiliatemarketing'}</em></label>
										<div class="col-lg-6 col-sm-6 col-xs-6">
											<p class="form-control-static">{if $app.date_add && $app.date_add !== '0000-00-00'}{dateFormat date=$app.date_add full=0}{/if}</p>
										</div>
									</div>
									<div class="row">
										<label class="control-label col-lg-6 col-sm-6 col-xs-6"><em>{l s='Status:' mod='ets_affiliatemarketing'}</em></label>
										<div class="col-lg-6 col-sm-6 col-xs-6">
											<p class="form-control-static js-eam-app-status">
												{if $app.status == 1}
												<span class="label label-success">{l s='Approved' mod='ets_affiliatemarketing'}</span>
												{elseif $app.status == 0}
												<span class="label label-warning">{l s='Pending' mod='ets_affiliatemarketing'}</span>
												{elseif $app.status == -1}
												<span class="label label-default">{l s='Declined' mod='ets_affiliatemarketing'}</span>
												{/if}
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6 col-sm-6 eam-col-rp">
							<div class="row">
								<div class="col-lg-6 col-sm-6 col-lg-offset-6 list-btn-actions-app">
									<form action="" method="get" accept-charset="utf-8" class="js-eam-app-btns">
    									{if $app.status == 0 }
    									<button type="button" class="btn btn-default js-btn-action-app"  data-id="{$app.id|escape:'html':'UTF-8'}" data-action="approve">{$_svg_check_icon nofilter} {l s='Approve' mod='ets_affiliatemarketing'}</button>
    									{/if}
    									{if $app.status == 0}
											<button type="button" class="btn btn-default js-btn-action-app"  data-id="{$app.id|escape:'html':'UTF-8'}" data-action="decline">{$_svg_close_icon nofilter} {l s='Decline' mod='ets_affiliatemarketing'}</button>
										{/if}
    									<button type="button" class="btn btn-default js-btn-action-app"  data-id="{$app.id|escape:'html':'UTF-8'}" data-action="delete">{$_svg_trash nofilter} {l s='Delete' mod='ets_affiliatemarketing'}</button>
    								</form>
								</div>
							</div>
						</div>	
					</div>	
				<div class="divider-horizontal"></div>
				<div class="row">
					<div class="col-lg-12">
						<a href="{$link_app nofilter}" title="" class="btn btn-default"><span class="eam-icon-back">
								{$_svg_close_icon nofilter}
							</span> {l s='Back' mod='ets_affiliatemarketing'}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalReasonDeclineApp" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{l s='Send an message to customer via email?' mod='ets_affiliatemarketing'}</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <textarea name="reason" class="form-control" rows="5" placeholder="{l s='Messages' mod='ets_affiliatemarketing'}"></textarea>
        </div>
        <p><em>{l s='Give customer a reason why their application is declined. Leave this blank if you just want to decline the application without giving any reason' mod='ets_affiliatemarketing'}</em></p>
        <div class="form-group">
        	<button type="button" id="submit_reason_decline" class="btn btn-default">{l s='Decline' mod='ets_affiliatemarketing'}</button>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade" id="modalReasonAproveApp" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">{l s='Send an message to customer via email?' mod='ets_affiliatemarketing'}</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            <textarea name="reason" class="form-control" rows="5" placeholder="{l s='Messages' mod='ets_affiliatemarketing'}"></textarea>
        </div>
        <p><em>{l s='Give customer a reason why their application is approved. Leave this blank if you just want to approve the application without giving any reason' mod='ets_affiliatemarketing'}</em></p>
        <div class="form-group">
        	<button type="button" id="submit_reason_approve" class="btn btn-default">{l s='Approve' mod='ets_affiliatemarketing'}</button>
        </div>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->