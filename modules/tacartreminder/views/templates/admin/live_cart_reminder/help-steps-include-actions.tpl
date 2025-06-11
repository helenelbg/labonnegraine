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
 * Included in page help
 *}
<h3 class="link-tab-content" data-target="tab-content-actions"><span class="flaticon-add133 toggle"></span> &nbsp;{l s='Actions available for this page' mod='tacartreminder'}</h3>
<div id="tab-content-actions" style="display:none">
	<table>
		<tr>
			<td style="padding: 5px;"><i class="flaticon-seo5" style="font-size:17px;"></i></td><td>{l s='View cart and details of the selected rule' mod='tacartreminder'}</td>
		</tr>
		<tr>
			<td style="padding: 5px;"><i class="flaticon-add133" style="font-size:17px;"></i></td><td>{l s='Open to view all reminders for this cart' mod='tacartreminder'}</td>
		</tr>
		{if $tab_select == 'running' || $tab_select == 'finished'}
		<tr>
			<td style="padding: 5px;"><i class="flaticon-comment3" style="font-size:17px;"></i></td><td>{l s='View employee messages and systems notifications' mod='tacartreminder'}</td>
		</tr>
		{/if}
		{if $tab_select == 'running' || $tab_select == 'cart'}
		<tr>
			<td style="padding: 5px;"><i class="flaticon-mail29" style="font-size:20px;"></i></td><td>{l s='Open the auto reminder form to manually start or resume sending the email' mod='tacartreminder'}</td>
		</tr>
		<tr>
			<td style="padding: 5px;"><i class="flaticon-support3" style="font-size:20px;"></i></td><td>{l s='Open the manual reminder form to access customer contact information' mod='tacartreminder'}</td>
		</tr>
		{/if}
	</table>
</div>