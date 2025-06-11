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
 * It is page help for running cart remind, so user cannot read all documentation
 *}
<div class="ta-to-bootstrap bootstrap ta-help-window" style="max-width:640px;min-width:500px;">
<div class="row" style="text-align: center;">
	<img src="../modules/tacartreminder/views/img/helps/steps_running.png?version4" width="95%" height="auto" style="margin-left:auto;margin-right:auto;"/>
</div>
<div class="row" style="font-family: 'Roboto', sans-serif;font-weight: 500;
font-size: 13px;width:100%">
		<div class="col-lg-12" style="padding:10px">
		<h2>{l s='Cart reminder pending' mod='tacartreminder'}</h2>
		<p>{l s='Cart containing one or more reminders that have not been executed.' mod='tacartreminder'}<br/>
		{l s='This page allows you to view and interact with all of the pending reminders.' mod='tacartreminder'}
		</p>
		{include file="./help-steps-include-actions.tpl" tab_select=$tab_select}
		{include file="./help-steps-include-reminders.tpl" tab_select=$tab_select}
		<h3 class="link-tab-content" data-target="tab-content-information"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Information' mod='tacartreminder'}</h3>
		<div id="tab-content-information" style="display:none">
		<h4>{l s='Transition to the finished state' mod='tacartreminder'}</h4>
		<p>
		{l s='Reminders be considered completed if an order has been made or the reminder\'s shelf life has expired.' mod='tacartreminder'}<br/>
		{l s='Shelf life is set at : ' mod='tacartreminder'} {$stopreminder_nbhour|intval} {l s='hours' mod='tacartreminder'}.
		({l s='You can modify this information in the Settings tab' mod='tacartreminder'})
		</p><br/>
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