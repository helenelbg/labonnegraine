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
{if isset($message) && $message}
    <div id="ets_affiliatemarketing_cart_message">
        <div class="alert alert-info">
            {$message nofilter}
        </div>
        <i class="icon icon-loading"></i>
    </div>
{/if}
{if isset($total_balance) && $total_balance >0 && $convert_message}
    <div class="alert alert-info">{$convert_message|escape:'html':'UTF-8'|replace:'[available_reward_to_convert]':$total_balance|Replace:'[Convert_now]':$convert_now_button nofilter}</div>
{/if}