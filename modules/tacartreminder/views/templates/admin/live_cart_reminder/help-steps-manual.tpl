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
 * It is page help for manual reminder, so user cannot read all documentation
 *}
<div class="ta-to-bootstrap bootstrap ta-help-window" style="max-width:640px;min-width:500px;">
<div class="row" style="text-align: center;">
	<img src="../modules/tacartreminder/views/img/helps/steps_man.png" width="95%" height="auto" style="margin-left:auto;margin-right:auto;"/>
</div>
<div class="row" style="font-family: 'Roboto', sans-serif;font-weight: 500;
font-size: 13px;width:100%">
		<div class="col-lg-12" style="padding:10px">
		<h2>{l s='Cart reminder to completed' mod='tacartreminder'}</h2>
		<h3 class="link-tab-content" data-target="tab-content-cart-checking-list"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Listed cart' mod='tacartreminder'}</h3>
		<div id="tab-content-cart-checking-list" style="display:none">
			{l s='A cart is present in the list if:' mod='tacartreminder'}<br/>
			<ul>
				<li>{l s='Cart containing reminder to complete.' mod='tacartreminder'}</li>
			</ul>
		</div>
		<h3 class="link-tab-content" data-target="tab-content-accomplish-how"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='How to complete a reminder?' mod='tacartreminder'}</h3>
		<div id="tab-content-accomplish-how" style="display:none">
			<table>
			<tr>
				<td style="padding:5px;">
					<span class="ta-badge">1</span>
				</td>
				<td style="padding:5px;">
					{l s='Click on the icon' mod='tacartreminder'} <i class="flaticon-support3" style="font-size:20px;cursor:pointer;"></i>
				</td>
			</tr>
			<tr>
				<td style="padding:5px;">
					<span class="ta-badge">2</span>
				</td>
				<td style="padding:5px;">
					{l s='Contact your customer' mod='tacartreminder'}
				</td>
			</tr>
			<tr>
				<td style="padding:5px;">
					<span class="ta-badge">3</span>
				</td>
				<td style="padding:5px;">
					{l s='Click on this button' mod='tacartreminder'} <a href="javascript:;" style="font-size: 12px;font-weight: bold;" class="btn btn-primary"><i class="flaticon-check33"></i>&nbsp;{l s='Flag as completed' mod='tacartreminder'}</a>
				</td>
			</tr>
			</table>			
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