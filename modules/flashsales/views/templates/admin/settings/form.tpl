{*
* 2022 - Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author    Keyrnel
* @copyright 2022 - Keyrnel
* @license   commercial
* International Registered Trademark & Property of Keyrnel
*}

<script type="text/javascript">
var default_language = {$default_language|intval};
</script>

<form id="flashsales_form" class="form-horizontal" action="{$currentIndex|escape:'html':'UTF-8'}" method="post" autocomplete="off" enctype="multipart/form-data">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='General' mod='flashsales'}
        </div>
        {include file="$tpl_general"}
        <div class="panel-footer">
            <button type="submit" name="submitFlashSales" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='flashsales'}</button>
        </div>
    </div>
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-cogs"></i> {l s='Display' mod='flashsales'}
        </div>
        {include file="$tpl_display"}
        <div class="panel-footer">
            <button type="submit" name="submitFlashSales" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save' mod='flashsales'}</button>
        </div>
    </div>
</form>
