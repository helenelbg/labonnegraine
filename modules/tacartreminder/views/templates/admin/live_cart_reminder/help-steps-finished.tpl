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
 * It is page help for journal closed, so user cannot read all documentation
 * user can just show the help for current page
 *}
<div class="ta-to-bootstrap bootstrap ta-help-window" style="max-width:640px;min-width:500px;">
<div class="row" style="text-align: center;">
	<img src="../modules/tacartreminder/views/img/helps/steps_finished.png" width="95%" height="auto" style="margin-left:auto;margin-right:auto;"/>
</div>
<div class="row" style="font-family: 'Roboto', sans-serif;font-weight: 500;
font-size: 13px;width:100%">
		<div class="col-lg-12" style="padding:10px">
		<h2>{l s='Cart Reminder finished' mod='tacartreminder'}</h2>
		<h3 class="link-tab-content" data-target="tab-content-cart-checking-list"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Listed cart' mod='tacartreminder'}</h3>
		<div id="tab-content-cart-checking-list" style="display:none">
			{l s='A cart is present in the list if:' mod='tacartreminder'}<br/>
			<ul>
				<li>{l s='Cart is purchased' mod='tacartreminder'}</li>
				<li>{l s='All reminders were performed' mod='tacartreminder'}</li>
				<li>{l s='The cart reminder was canceled' mod='tacartreminder'}</li>
			</ul>
		</div>
		<h3 class="link-tab-content" data-target="tab-content-order-legend"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Order legend' mod='tacartreminder'}</h3>
		<div id="tab-content-order-legend" style="display:none">
			<table>
			<tr>
				<td style="padding: 5px;"><span class="ta-badge">{l s='No order #cart' mod='tacartreminder'}35</span></td><td>{l s='No order associated with this reminder' mod='tacartreminder'}&nbsp;({l s='35 is the cart ID' mod='tacartreminder'})</td>
			</tr>
			<tr>
				<td style="padding: 5px;"><span class="ta-badge ta-badge-success" style="font-size: 14px;"><i class="flaticon-check33"></i> #18</span></td><td>{l s='There is an order associated with this cart' mod='tacartreminder'}&nbsp;({l s='18 is the order ID' mod='tacartreminder'})</td>
			</tr>
			<tr>
				<td style="padding: 5px;"><span class="ta-badge ta-badge-success" style="font-size: 14px;">20,00</span></td><td>{l s='Product total including tax' mod='tacartreminder'}</td>
			</tr>
			</table>
		</div>
		<h3 class="link-tab-content" data-target="tab-content-canceled-reason"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Cart reminder cancelation reasons' mod='tacartreminder'}</h3>
		<div id="tab-content-canceled-reason" style="display:none">
			<ul>
				<li><b>{l s='Shelf life' mod='tacartreminder'}</b> : {l s='Cart reminder shelf life is exceeded.' mod='tacartreminder'}</li>
				<li><b>{l s='Rule is no longer applicable' mod='tacartreminder'}</b> : {l s='The cart changed and the rule no longer applies (The option "Force launch reminder" is set to "No" in the rule setting).' mod='tacartreminder'}</li>
				<li><b>{l s='Rule settings' mod='tacartreminder'}</b> : {l s='The rule is deleted, disabled or one of the reminders included in the rule has been updated.' mod='tacartreminder'}</li>
			</ul>
		</div>
		{include file="./help-steps-include-actions.tpl" tab_select=$tab_select}
		{include file="./help-steps-include-reminders.tpl" tab_select=$tab_select}
		<h3 class="link-tab-content" data-target="tab-content-information"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Information' mod='tacartreminder'}</h3>
		<div id="tab-content-information" style="display:none">
		<h4>{l s='Customer and carts' mod='tacartreminder'}</h4>
		<p>
		{l s='If your customer has multiple carts, only the most recently-updated cart is used.' mod='tacartreminder'}
		</p>
		</div>
		</div>
</div>
</div>
<script type="text/javascript">
$(function() {
	$('.link-tab-content').click(function(){
		var id_content_target = $(this).data('target');
		if($('#'+id_content_target).css('display') == 'none')
		{
			$(this).find('.toggle').removeClass('flaticon-add133');
			$('#'+id_content_target).slideDown();
			$(this).find('.toggle').addClass('flaticon-minus87');
		}
		else
		{
			$(this).find('.toggle').removeClass('flaticon-minus87');
			$('#'+id_content_target).slideUp();
			$(this).find('.toggle').addClass('flaticon-add133');
		}
	});
});
</script>