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
* @copyright  2022 - Keyrnel
* @license commercial
* International Registered Trademark & Property of Keyrnel
*}
<!-- MODULE Countdown Flash sale -->
<div
	class="flashsale flashsale-countdown-box {$flashsales.layout|escape:'html':'UTF-8'}"
	data-id-product="{$flashsales.id_product|intval}"
	{if isset($flashsales.combinations)}
		{if !$flashsales.to}style="display:none;"{/if}
		data-combinations="{$flashsales.combinations|escape:'html':'UTF-8'}"
		data-id-product-attribute="{$flashsales.id_product_attribute|intval}"
	{/if}
>
	<div class="row">
		<div class="col-lg-12">
			<div class="content">
				<i class="icon-clock-o"></i>
				<span class="title">{$flashsales.txt|escape:'html':'UTF-8'}</span>
				<span class="countdown" {if $flashsales.to}data-to="{$flashsales.to|intval}"{/if}>
					<span class="timerDay"><span class="timer TimerDay"></span><span class="timerTypeM">{l s='jour(s)' mod='flashsales'}</span></span>
					<span class="timerHour"><span class="timer TimerHour"></span><span class="timerTypeM">{l s='heure(s)' mod='flashsales'}</span></span>
					<span class="timerMin"><span class="timer TimerMin"></span><span class="timerTypeM">{l s='min.' mod='flashsales'}</span></span>
					<span class="timerSec"><span class="timer TimerSec"></span><span class="timerTypeM">{l s='sec.' mod='flashsales'}</span></span>
				</span>
			</div>
		</div>
	</div>
</div>
<!-- /MODULE Countdown Flash sale -->
