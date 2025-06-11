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
{assign var='_svg_compress' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 960v448q0 26-19 45t-45 19-45-19l-144-144-332 332q-10 10-23 10t-23-10l-114-114q-10-10-10-23t10-23l332-332-144-144q-19-19-19-45t19-45 45-19h448q26 0 45 19t19 45zm755-672q0 13-10 23l-332 332 144 144q19 19 19 45t-19 45-45 19h-448q-26 0-45-19t-19-45v-448q0-26 19-45t45-19 45 19l144 144 332-332q10-10 23-10t23 10l114 114q10 10 10 23z"/></svg></i>'}
{assign var='_svg_download' value='<i class="ets_svg"><svg width="16" height="16" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1344 1344q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm256 0q0-26-19-45t-45-19-45 19-19 45 19 45 45 19 45-19 19-45zm128-224v320q0 40-28 68t-68 28h-1472q-40 0-68-28t-28-68v-320q0-40 28-68t68-28h465l135 136q58 56 136 56t136-56l136-136h464q40 0 68 28t28 68zm-325-569q17 41-14 70l-448 448q-18 19-45 19t-45-19l-448-448q-31-29-14-70 17-39 59-39h256v-448q0-26 19-45t45-19h256q26 0 45 19t19 45v448h256q42 0 59 39z"/></svg></i>'}

<div class="import-export-form">
	<form action="" method="POST" accept-charset="utf-8" enctype="multipart/form-data" id="eamFormImportExport">
		<div class="box-import-export clearfix">
			<div class="box-item">
				<div class="eam-panel">
					<div class="eam-panel-heading">
						<h3 class="eam-panel-title">{l s='Create backup' mod='ets_affiliatemarketing'}</h3>
					</div>
					<div class="eam-panel-body">
						<p class="text-gray">{l s='Export all reward data and module\'s configurations for backup purpose. If your website is multishop, It will export data from all shops.' mod='ets_affiliatemarketing'}</p>
						<button type="submit" name="exportAllData" value="1" class="eam-btn-flat mt-15">{$_svg_download nofilter} {l s='Create backup' mod='ets_affiliatemarketing'}</button>
					</div>
				</div>
			</div>
			<div class="box-divider"></div>
			<div class="box-item">
				<div class="eam-panel">
					<div class="eam-panel-heading">
						<h3 class="eam-panel-title">{l s='Restore backup' mod='ets_affiliatemarketing'}</h3>
					</div>
					<div class="eam-panel-body">
						<p class="text-gray">{l s='Import reward data and module\'s configurations for restoration. If your website is multishop, It will restore data of all shops.' mod='ets_affiliatemarketing'}</p>
						<div class="form-group mt-15">
							<label for="import_source">{l s='Backup file:' mod='ets_affiliatemarketing'}</label>
							<input type="file" id="import_source" name="import_source" value="" class="eam-input-inline">
						</div>
						<p><strong>{l s='Restoring options:' mod='ets_affiliatemarketing'}</strong></p>
						
						<div class="form-group form-group-thin">
							<div class="checkbox">
								<label for="restore_config" class="eam-label-thin">
									<input type="checkbox" id="restore_config" name="restore_config" value="1" checked="checked" />{l s='Restore configuration' mod='ets_affiliatemarketing'}
								</label>
							</div>
						</div>
						<div class="form-group form-group-thin">
							<div class="checkbox">
								<label for="restore_reward" class="eam-label-thin">
									<input type="checkbox" id="restore_reward" name="restore_reward" value="1" {if isset($restore_reward)}{if $restore_reward}checked="checked"{/if}{else}checked="checked"{/if} />{l s='Restore reward data' mod='ets_affiliatemarketing'}
								</label>
							</div>
						</div>
						<div class="form-group form-group-thin">
							<div class="checkbox">
								<label for="delete_reward" class="eam-label-thin">
									<input type="checkbox" id="delete_reward" name="delete_reward" value="1" {if isset($delete_reward)}{if $delete_reward}checked="checked"{/if}{else}checked="checked"{/if} />{l s='Delete reward before restoring' mod='ets_affiliatemarketing'}
								</label>
							</div>
						</div>
						<div class="form-group">
							<button type="submit" name="importAllData" value="1" class="eam-btn-flat mt-15">{$_svg_compress nofilter} {l s='Restore' mod='ets_affiliatemarketing'}</button>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</form>
</div>