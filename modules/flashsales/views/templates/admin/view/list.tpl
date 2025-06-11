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
<div role="tabpanel" class="tab-pane {if $id=='active'}active{/if}" id="{$id|escape:'html':'UTF-8'}">
    <div class="row">
        <div class="col-lg-12">
        {if isset($flash_sale.content) && $flash_sale.content}
            {$flash_sale.content}{* HTML, cannot escape *}
        {/if}
        </div>
    </div>
</div>
