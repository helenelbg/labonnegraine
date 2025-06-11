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
{if $cronjob_last}
    <div class="alert alert-info cronjob">
        {l s='Last time cronjob run: ' mod='ets_affiliatemarketing'}{$cronjob_last|escape:'html':'UTF-8'}
    </div>
{/if}
{if !$run_cronjob}
    <div class="alert alert-warning cronjob">{l s='Cronjob didn\'t run in last 12 hours. Please check again cronjob configuration to make sure Cronjob run correctly' mod='ets_affiliatemarketing'}</div>
{/if}
