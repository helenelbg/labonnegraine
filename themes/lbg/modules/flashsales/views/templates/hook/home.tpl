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

{if isset($flashsales.flash_sales) && count($flashsales.flash_sales)}
    <div class="flashsale flashsale-home">
      {foreach from=$flashsales.flash_sales item=flash_sale}
          {if Configuration::get('FLASHSALE_BANNER_HOME_PAGE') || Configuration::get('FLASHSALE_CAROUSEL_HOME_PAGE')}
              <h3 class="flashsale-section-title">{$flash_sale.name|escape:'html':'UTF-8'}</h3>

              {if $flash_sale.banner && Configuration::get('FLASHSALE_BANNER_HOME_PAGE')}
                  <section class="banner">
                      <a href="{$link->getModuleLink('flashsales', 'page', ['id_flash_sale' => {$flash_sale.id_flash_sale|intval}])|escape:'quotes':'UTF-8'}">
                          <img class="img-responsive" src="{$flash_sale.banner|escape:'quotes':'UTF-8'}" alt="{$flash_sale.name|escape:'html':'UTF-8'}"/>
                      </a>
                  </section>
              {/if}
			  
			<div class="flashsale-reduction">
				{if $flash_sale.reduction_type == "percentage"}
					<span class="flashsale-reduction-a">{$flash_sale.reduction|string_format:"%.0f"}</span>
					<span class="flashsale-reduction-b"> %</span>
				{else}
					<span class="flashsale-reduction-a">{$flash_sale.reduction|string_format:"%.2f"}</span>
					<span class="flashsale-reduction-b"> €</span>
				{/if}
			</div>

			
			<div class="flashsale-end">
				<div class="flashsale flashsale-countdown-box">
					<div class="row">
						<div class="col-lg-12">
							<div class="content">
								<span class="countdown" {if $flash_sale.to}data-to="{$flash_sale.to|strtotime}"{/if}>
									<span class="timerDay"><span class="timer TimerDay"></span><span class="timerTypeM">{l s='jour(s)' mod='flashsales'}</span></span>
									<span class="timerHour"><span class="timer TimerHour"></span><span class="timerTypeM">{l s='heure(s)' mod='flashsales'}</span></span>
									<span class="timerMin"><span class="timer TimerMin"></span><span class="timerTypeM">{l s='min.' mod='flashsales'}</span></span>
									<span class="timerSec"><span class="timer TimerSec"></span><span class="timerTypeM">{l s='sec.' mod='flashsales'}</span></span>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>

          {/if}
      {/foreach}
  </div>
{/if}
