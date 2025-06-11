{*
* 2022 Keyrnel
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
* @author Keyrnel
* @copyright  2015 - Keyrnel
* @license commercial
* International Registered Trademark & Property of Keyrnel
*}
{if isset($flashsales.flash_sales) && $flashsales.flash_sales}
{include file="$tpl_dir./product-list.tpl" products=$flashsales.flash_sales class='flashsales tab-pane' id='flashsales'}
{else}
<ul id="flashsales" class="tab-pane">
	<li class="alert alert-info">{l s='No flash sales at this time.' mod='flashsales'}</li>
</ul>
{/if}
