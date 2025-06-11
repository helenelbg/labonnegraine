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
<link href="https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700" rel="stylesheet">
<script type="text/javascript">
    var eam_cookie_filter = {$cookie_filter|json_encode};
    var eam_submit_error = {$submit_errors nofilter};
    var idRewardUser = "{if $idRewardUser}{$idRewardUser|escape:'html':'UTF-8'}{else}{/if}";
</script>
<style type="text/css">
.bootstrap > .module_confirmation.alert-success {
    display: none;
}
</style>

<div class="ets-sn-admin clearfix">
    {$admin_left_content nofilter}
        <div class="ets-sn-admin__body" {if $activetab == 'dashboard'}style="padding-top: 25px;"{/if}>
            {$html nofilter}
        </div>
    </div> {*close admin left*}
</div>