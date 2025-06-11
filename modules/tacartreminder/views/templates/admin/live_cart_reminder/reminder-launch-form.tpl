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
 * Form display when user want launch reminder manualy
 *}
<span id="reminder_launch_form_loader" style="display:none"></span>
<form id="reminder_launch_form" action="#" class="taform">
	<h2 class="page-subheading">{l s='Perform reminder' mod='tacartreminder'}</h2>
	<input type="hidden" name="id_reminder" value="{$reminder.id_reminder|intval}"/>
	<input type="hidden" name="id_cart" value="{$cart->id|intval}"/>
	<input type="hidden" name="type_perform" value="DONE"/>
	<div id="launchreminder_form_error" class="error" style="display: none; padding: 15px 25px">
		<ul class="alert-list"></ul>
	</div>
	<p id="launchreminder_form_message">
		{l s='This action will perform a reminder.' mod='tacartreminder'}
	</p>
	<table class="table">
		<!--thead>
			<tr>
				<th>{l s='Label' mod='tacartreminder'}</th>
				<th>{l s='Value' mod='tacartreminder'}</th>
			</tr>
		</thead-->
		<tbody>
			<tr><td class="label">{l s='Reminder position' mod='tacartreminder'}</td><td ><span style="background-color:#00aff0;color:#fff;font-weight:bold;padding:8px;">{$reminder.position|intval}</span></td></tr>
			<tr class="odd"><td class="label">{l s='Customer email' mod='tacartreminder'}</td><td>{$customer->email|escape:'html':'UTF-8'}</td></tr>
			<tr><td class="label">{l s='Email template' mod='tacartreminder'}</td><td>{$mail_template->name|escape:'html':'UTF-8'}</td></tr>
			<tr class="odd"><td class="label">{l s='Email Language' mod='tacartreminder'}</td><td>{$language_mail->iso_code|escape:'html':'UTF-8'}</td></tr>
		</tbody>
	</table>
	<br/>
	<p>{l s='You can create a message that will be saved in the history.' mod='tacartreminder'}</p>
	<textarea id="message" name="message" style="width:95%;height: 80px;"></textarea>
	<div>
		<a href="javascript:;" class="ta-action-button" id="submit-launch-reminder" style="font-size:15px;text-transform:uppercase;float:right;"><i class="flaticon-email43"></i>&nbsp;{l s='Launch' mod='tacartreminder'}</a>
	</div>
	
</form>
