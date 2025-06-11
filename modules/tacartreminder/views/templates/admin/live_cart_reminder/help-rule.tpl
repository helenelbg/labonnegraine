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
 * It is page help for rule setting, so user cannot read all documentation
 * user can just show the help for current page
 *}
<div class="ta-to-bootstrap bootstrap" style="max-width:640px;">

<div class="row" style="font-family: 'Roboto', sans-serif;font-weight: 500;
font-size: 13px;width:100%">
		<div class="col-lg-12" style="padding:10px">
		<h2>{l s='Rule' mod='tacartreminder'}</h2>
		<!--img src="../modules/tacartreminder/views/img/helps/steps_rule.png" /-->
		<p>{l s='This page allows you to define your cart reminder rules.' mod='tacartreminder'}</p>
		<h3 class="link-tab-content" data-target="tab-content-column"><span class="flaticon-minus87 toggle"></span> &nbsp;{l s='Columns present in this list' mod='tacartreminder'}</h3>
		<div id="tab-content-column" >
		<table style="width:100%;background:#fff;border:1px solid #CECECE;width:100%;color:#555">
		<tr style="border: 1px solid #ececec;">
			<td style="padding:5px">{l s='ID' mod='tacartreminder'}</td>
			<td style="padding:5px"><p>{l s='Rule identifier' mod='tacartreminder'}</p></td>
		</tr>
		<tr style="border: 1px solid #ececec;">
			<td style="padding:5px">{l s='Position' mod='tacartreminder'}</td>
			<td style="padding:5px"><p>{l s='The rule\'s position. You can manage the priority.' mod='tacartreminder'}<br/>{l s='Eg: You can rank the rule at the top if you want to prioritize it.' mod='tacartreminder'}</p></td>
		</tr>
		<tr style="border: 1px solid #ececec;">
			<td style="width:120px;padding:5px;">{l s='Date From' mod='tacartreminder'}</td>
			<td style="padding:5px;"><p>{l s='Start date for the rule validity.' mod='tacartreminder'}</p></td>
		</tr>
		<tr style="border: 1px solid #ececec;">
			<td style="padding:5px">{l s='Date To' mod='tacartreminder'}</td>
			<td style="padding:5px">{l s='End date of the rule validity.' mod='tacartreminder'}</td>
		</tr>
		<tr style="border: 1px solid #ececec;">
			<td style="padding:5px">{l s='Discount' mod='tacartreminder'}</td>
			<td style="padding:5px">{l s='Check if rule generates a coupon' mod='tacartreminder'}</td>
		</tr>
		<tr style="border: 1px solid #ececec;">
			<td style="padding:5px">{l s='Status' mod='tacartreminder'}</td>
			<td style="padding:5px">{l s='Allows you to enable or disable a rule' mod='tacartreminder'}</td>
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