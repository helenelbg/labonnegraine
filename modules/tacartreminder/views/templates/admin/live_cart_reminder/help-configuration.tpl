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
 * It is page help for configuration, so user cannot read all documentation
 * user can just show the help for current page
 *}
<div class="ta-to-bootstrap bootstrap" style="max-width:640px;">

<div class="row" style="font-family: 'Roboto', sans-serif;font-weight: 500;
font-size: 13px;width:100%">
		<div class="col-lg-12" style="padding:10px">
		<h2>{l s='Configuration' mod='tacartreminder'}</h2>
		
		<h3 class="link-tab-content" data-target="tab-content-settings"><span class="flaticon-minus87 toggle"></span> &nbsp;{l s='Settings' mod='tacartreminder'}</h3>
		<div id="tab-content-settings" >
		<table style="width:100%">
		<tr>
			<td style="text-align:center;padding:5px;background-color:#D81848;color:#fff;font-weight:bold">{l s='Time-lapse for abandoned cart' mod='tacartreminder'}</td>
		</tr>
		<tr>
			<td style="text-align:center;padding:5px;"><img src="../modules/tacartreminder/views/img/helps/steps_setting_abandonned.png" /></td>
		</tr>
		<tr>
		<td>
		<p>{l s='Time in hours: after this time the cart will be considered abandoned.' mod='tacartreminder'}</p>
		<p><i>{l s='The time is based on the date of the last cart update' mod='tacartreminder'}</i></p>
		</td>
		</tr>
		<tr>
			<td style="text-align:center;padding:5px;background-color:#D81848;color:#fff;font-weight:bold">{l s='Time-lapse for do not remind or stop reminders' mod='tacartreminder'}</td>
		</tr>
		<tr>
			<td style="text-align:center;padding:5px;"><img src="../modules/tacartreminder/views/img/helps/steps_setting_stop.png" /></td>
		</tr>
		<tr>
		<td>
		<p>
		{l s='Time in hours' mod='tacartreminder'}<br/>
		<span class="ta-badge" style="background-color: #D81848;">1</span> {l s='Do not remind a cart if the last cart update exceeds this time' mod='tacartreminder'}<br/>
		{l s='Or' mod='tacartreminder'}<br/>
		<span class="ta-badge" style="background-color: #D81848;">2</span> {l s='If reminders exist and have not yet been executed for a cart, after that time it will be canceled.' mod='tacartreminder'}<br/>
		<i>{l s='The time is based on the date of the last cart update' mod='tacartreminder'}</i></p>
		</td>
		</tr>
		<tr>
			<td style="text-align:center;padding:5px;background-color:#D81848;color:#fff;font-weight:bold">{l s='Time for accept new cart reminder for a same customer' mod='tacartreminder'}</td>
		</tr>
		<tr>
			<td style="text-align:center;padding:5px;"><img src="../modules/tacartreminder/views/img/helps/steps_setting_afterreminder.png" /></td>
		</tr>
		<tr>
		<td>
		<p>{l s='If a customer has already been reminded on an old cart. After this time, a new rule can be applied to another abandonned cart.' mod='tacartreminder'}</p>
		</td>
		</tr>
		<tr>
			<td style="text-align:center;padding:5px;background-color:#D81848;color:#fff;font-weight:bold">{l s='Time before clean coupon (cart rule)' mod='tacartreminder'}</td>
		</tr>
		<tr>
			<td style="text-align:center;padding:5px;"><img src="../modules/tacartreminder/views/img/helps/steps_setting_deletecartrule.png" /></td>
		</tr>
		<tr>
		<td>
		<p>{l s='Time before deleting an expired coupon. This applies only to coupons created by the module.' mod='tacartreminder'}</p>
		<p><i>{l s='The time is based on the coupon\'s expiry date.' mod='tacartreminder'}</i></p>
		</td>
		</tr>
		</table>
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