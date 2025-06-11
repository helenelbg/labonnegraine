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
 * It is page help for abandonned cart, so user cannot read all documentation
 * user can just show the help for current page
 *}
<div class="ta-to-bootstrap bootstrap ta-help-window" style="max-width:640px;min-width:500px;">
<div class="row" style="text-align: center;">
	<img src="../modules/tacartreminder/views/img/helps/steps_cart.png?version4" width="95%" height="auto" style="margin-left:auto;margin-right:auto;"/>
</div>
<div class="row" style="font-family: 'Roboto', sans-serif;font-weight: 500;
font-size: 13px;width:100%">
		<div class="col-lg-12" style="padding:10px">
		<h2>{l s='Cart checking' mod='tacartreminder'}</h2>
		<h3 class="link-tab-content" data-target="tab-content-cart-checking-list"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Listed cart' mod='tacartreminder'}</h3>
		<div id="tab-content-cart-checking-list" style="display:none">
			{l s='A cart is present in the list if:' mod='tacartreminder'}<br/>
			<ul>
				<li>{l s='Not ordered' mod='tacartreminder'}</li>
				<li>{l s='"Not hooked" cart has no pending or completed reminder' mod='tacartreminder'}</li>
				<li>{l s='The cart has been updated there under' mod='tacartreminder'} {$stopreminder_nbhour|intval} {l s='hours' mod='tacartreminder'}({l s='This information can be modified in the Settings tab' mod='tacartreminder'})</li>
			</ul>
		</div>
		<h3 class="link-tab-content" data-target="tab-content-cart-crocheted-how"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='How a cart is "hooked" (will be reminded)?' mod='tacartreminder'}</h3>
		<div id="tab-content-cart-crocheted-how" style="display:none">
			{l s='A cart is "hooked" if :' mod='tacartreminder'}<br/>
			<ul>
				<li>{l s='One of the rules you created applies' mod='tacartreminder'}</li>
				<li><u>{l s='Automatic' mod='tacartreminder'}</u> : {l s='The time for the first reminder has been exceeded' mod='tacartreminder'} {l s='Eg:' mod='tacartreminder'}
					<table>
					<tr>
						<td style="padding:5px;"><span class="position_reminder_line pendinglaunch" style="background-color:orange">1</span></td>
						<td>
							<span class="flip-clock-wrapper" data-id-reminder="13" data-nb-second="3599">
							<span class="flip-clock-divider days"></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><span class="flip-clock-divider hours"><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><span class="flip-clock-divider minutes"><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">4</div></div><div class="down"><div class="shadow"></div><div class="inn">4</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">8</div></div><div class="down"><div class="shadow"></div><div class="inn">8</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><span class="flip-clock-divider seconds"><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span><ul class="flip  play"><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">3</div></div><div class="down"><div class="shadow"></div><div class="inn">3</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip  play"><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">3</div></div><div class="down"><div class="shadow"></div><div class="inn">3</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul></span>
						</td>
					</tr>
					</table>
					<u>{l s='Or one of these actions done by an employee' mod='tacartreminder'}</u>:
					<ul><li><i class="flaticon-mail29" style="font-size:20px;"></i>&nbsp;{l s='The reminder was performed manually by an employee' mod='tacartreminder'}</li>
					<li><a href="javascript:;" style="font-size: 12px;font-weight: bold;" class="btn btn-primary"><i class="flaticon-check33"></i>&nbsp;{l s='Flag as completed' mod='tacartreminder'}</a></li>
					<ul>
				</li>
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