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

{if isset($eam_display_aff_promo_code) && $eam_display_aff_promo_code}
    <div class="ets-am-ref-popup ets-am-ref-popup-voucher" style="display: block;" id="ets_am_aff_modal_promo_code">
        <div class="ref-popup">
            <span class="ets-am-ref-popup-voucher-close"></span>
            <div class="popup-header">
                <div class="popup-title">
                    {l s='Congratulation! You get our discount' mod='ets_affiliatemarketing'}
                </div>
            </div>
            <div class="popup-body">
                <div class="mb-10">
                    {$eam_aff_promo_code_message nofilter}
                </div>
                
            </div>
            <div class="popup-footer">
                <button class="btn btn-primary pull-right shop_now_aff">{l s='Shop now' mod='ets_affiliatemarketing'}</button>
            </div>
        </div>
    </div>
{/if}
