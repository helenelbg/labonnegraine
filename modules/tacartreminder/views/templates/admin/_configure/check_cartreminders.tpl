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
 * check_cartreminders.tpl display for ajax content
 * file display when reminder is update or delete and it is already use for remind cart
 * user can validate to update reminder if true all remind cart use this reminder is closed
 *}
<div class="taform">
<div class="alert alert-info">
	{l s='You remove the reminder and it has already been executed for one or more carts.' mod='tacartreminder'}<br/> 
	{l s='To minimize the impact of the removal, after updating rule, the module to tag \'closed\' all reminders for each cart (s) listed (s) below:' mod='tacartreminder'}
</div>

<h3>{l s='If deleted, no other reminders will be sent to these carts:' mod='tacartreminder'}</h3>
<table class="tareminder_table" style="text-align: center;font-size: 15px;">
	<thead>
	<tr>
			<th>{l s='Cart' mod='tacartreminder'}</th>
			<th>{l s='Cart date added' mod='tacartreminder'}</th>
			<th>{l s='Customer' mod='tacartreminder'}</th>
			<th>{l s='Date launched' mod='tacartreminder'}</th>
	</tr>
	</thead>
	<tbody>
		{foreach from=$info_jreminders item='info_jreminder'}
			<tr>
				<td>{$info_jreminder.id_cart|intval}</td>
				<td>{$info_jreminder.date_add_cart|escape:'htmlall':'UTF-8'}</td>
				<td>{$info_jreminder.firstname|escape:'htmlall':'UTF-8'}&nbsp;{$info_jreminder.lastname|escape:'htmlall':'UTF-8'}</td>
				<td>{$info_jreminder.date_launched|escape:'htmlall':'UTF-8'}</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<br/>
<div>
		<a href="javascript:;" class="ta-action-button" style="font-size:15px;text-transform:uppercase;float:right;" id="reminder_remove_{$id_reminder|intval}">
			<i class="flaticon-check33"></i>&nbsp;{l s='Validate' mod='tacartreminder'}
		</a>
</div>
</div>