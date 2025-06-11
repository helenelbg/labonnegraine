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
 * It is page help for customer unsubsribed, so user cannot read all documentation
 *}
<div class="ta-to-bootstrap bootstrap ta-help-window" style="max-width:640px;min-width:500px;">
<div class="row" style="font-family: 'Roboto', sans-serif;font-weight: 500;
font-size: 13px;width:100%">
		<div class="col-lg-12" style="padding:10px">
		<h2>{l s='Unsubscribed customers' mod='tacartreminder'}</h2>
		<p>{l s='List of customers who have clicked on the unsubscribe link.' mod='tacartreminder'}</p>
		
		<h3 class="link-tab-content" data-target="tab-content-action"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Action(s) available on this page' mod='tacartreminder'}</h3>
		<div id="tab-content-action" style="display:none">
			<table>
			<tr>
				<td style="padding: 5px;"><b>{l s='Delete' mod='tacartreminder'}</b></td><td>{l s='After removal, the customer can receive cart reminders.' mod='tacartreminder'}</td>
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