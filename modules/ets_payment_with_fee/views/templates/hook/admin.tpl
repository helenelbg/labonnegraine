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
{if $ets_custom_payment_error_message}
    {$ets_custom_payment_error_message nofilter}
{/if}
<script type="text/javascript" src="{$ets_custom_payment_module_dir|escape:'html':'UTF-8'}views/js/admin.js"></script>
<div class="bootstrap">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="ets_custom_payment blog_center_content col-lg-12">
                    {$ets_custom_payment_body_html nofilter}
                </div>
            </div>
        </div>
    </div>
</div>