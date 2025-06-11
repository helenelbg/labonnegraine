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
<div class="eam-cronjob">
	<form action="{$post_url|escape:'html':'UTF-8'}" method="post">
        <div class="form-wrapper">
            <div class="form-group">										            
                <label class="control-label col-lg-3" style="text-align: right;">
                	{l s='Save cronjob log' mod='ets_affiliatemarketing'}
                </label>
                <div class="col-lg-3">
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input name="ETS_AM_SAVE_LOG" id="ETS_AM_SAVE_LOG_on" value="1"{if $ETS_AM_SAVE_LOG} checked="checked"{/if} type="radio"/>
                        <label for="ETS_AM_SAVE_LOG_on">{l s='Yes' mod='ets_affiliatemarketing'}</label>
                        <input name="ETS_AM_SAVE_LOG" id="ETS_AM_SAVE_LOG_off" value="0"{if !$ETS_AM_SAVE_LOG} checked="checked"{/if} type="radio" />
                        <label for="ETS_AM_SAVE_LOG_off">{l s='No' mod='ets_affiliatemarketing'}</label>
                        <a class="slide-button btn"></a>
                    </span>				
                    <p class="help-block">{l s='Only recommended for debug purpose' mod='ets_affiliatemarketing'}</p>
                </div>
            </div>
    		<textarea readonly class="ets_conjob_log">{$log|escape:'html':'UTF-8'}</textarea>
    		<input type="submit" class="btn btn-default ets-am-clear-log" value="{l s='Clear log' mod='ets_affiliatemarketing'}" />
        </div>
	</form>
    <br />
    {$info_cronjob nofilter}
</div>