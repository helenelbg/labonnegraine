{*
 * Cart Reminder
 * 
 *    @category advertising_marketing
 *    @author    Timactive - Romain DE VERA
 *    @copyright Copyright (c) TIMACTIVE 2014 - Romain De Véra
 *    @version 1.0.0
 *    @license   Commercial license
 *
 *************************************
 **         CART REMINDER            *
 **          V 1.0.0                 *
 *************************************
 *  _____ _            ___       _   _           
 * |_   _(_)          / _ \     | | (_)          
 *   | |  _ _ __ ___ / /_\ \ ___| |_ ___   _____ 
 *   | | | | '_ ` _ \|  _  |/ __| __| \ \ / / _ \
 *   | | | | | | | | | | | | (__| |_| |\ V /  __/
 *   \_/ |_|_| |_| |_\_| |_/\___|\__|_| \_/ \___|
 *                                              
 * +
 * + Languages: EN, FR
 * + PS version: 1.5,1.6
 * Display on all tab present cart, manual remind, running reminder, closed reminder
 * @todo useJS
 *}
<script type="text/javascript">
	var ad = '{$ad|escape:'htmlall':'UTF-8'}';
	{if $ps_version < "1.6"}
		var ps15 = true;
	{else}
		var ps15 = false;
	{/if}
	var ad = '{$ad|escape:'htmlall':'UTF-8'}';
	var token = '{$token|escape:'htmlall':'UTF-8'}';
	var iso = '{$iso|escape:'htmlall':'UTF-8'}';
	var pathCSS = '{$smarty.const._THEME_CSS_DIR_|escape:'htmlall':'UTF-8'}';
	var admin_cart_url = '{$link->getAdminLink('AdminCarts')|escape:'quotes':'UTF-8'}';
	var admin_cart_token = '{getAdminToken tab='AdminCarts'}';
	var confirm_reminder_done = '{l s='Indicate that the reminder is completed. Activate the next reminder for this cart?' mod='tacartreminder' js=1}';
	var confirm_reminder_finish = '{l s='Finish reminders on this cart?' mod='tacartreminder' js=1}';
	var currency_format = {$currency->format|intval};
	var currency_sign = '{$currency->sign|escape:'quotes':'UTF-8'}';
	var currency_blank = {$currency->blank|intval};
	var priceDisplayPrecision = 0;
</script>
<input type="hidden" value="" id="voucher" />
{include file="../menu-top.tpl"}
{if $ta_cr_tab_select=='manual'}
	<script type="text/javascript">
		$( document ).ready(function() {
			$('.ta-reminders-openorclose').trigger('click');
		});
	</script>
{/if}