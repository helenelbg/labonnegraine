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
 * It is reminder line use in rule setting form
 *}
<tr id="reminder_{$position|intval}_tr" data-id-reminder="{$id_reminder|intval}">
	<td width="10px">
		<span class="ta-badge ta-badge-info">{$position|intval}</span>
		<input type="hidden" name="reminder_{$position|intval}_id" value="{$id_reminder|intval}" />
	</td>
	<td>
		<div >
				<label class="control-label">
					<span class="label-tooltip" 
						title="{l s='For example, if you want to call your customer directly. If other reminders exist, they will be executed only if you clicked completed.'  mod='tacartreminder' }">
						{l s='Manual' mod='tacartreminder'}
					</span>
				</label><br/><br/>
				<div >
					<span class="switch prestashop-switch" >
						<input type="radio" name="reminder_{$position|intval}_manual_process" class="radio-manual-process" data-pos-reminder="{$position|intval}" data-id-reminder="{$id_reminder|intval}" id="reminder_{$position|intval}_manual_process_on" value="1" {if $manual_process} checked="checked"{/if}/>
						<label for="reminder_{$position|intval}_manual_process_on">{l s='Yes' mod='tacartreminder'}</label>
						<input type="radio" name="reminder_{$position|intval}_manual_process" class="radio-manual-process" data-pos-reminder="{$position|intval}" data-id-reminder="{$id_reminder|intval}" id="reminder_{$position|intval}_manual_process_off" value="0" {if !$manual_process} checked="checked"{/if} />
						<label for="reminder_{$position|intval}_manual_process_off">{l s='No' mod='tacartreminder'}</label>
						<a class="slide-button btn"></a>
					</span>
				</div>
		</div>
	</td>
	<td>
		<div  id="reminder_{$position|intval}_id_mail_template" style="{if $manual_process}display:none{/if}">
		<label class="control-label">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='Select an email template' mod='tacartreminder'}">
						{l s='Email template' mod='tacartreminder' }
					</span>
				</label><br/><br/>
		<select name="reminder_{$position|intval}_id_mail_template" >
			<option value="">{l s='-- Choose --' mod='tacartreminder'}</option>
			{if isset($mail_templates) && $mail_templates|@count}
				{foreach from=$mail_templates item='mail_template'}
					<option value="{$mail_template.id_mail_template|intval}" {if $id_mail_template==$mail_template.id_mail_template}selected{/if}>
						{$mail_template.name|escape:'htmlall':'UTF-8'}
					</option>
				{/foreach}
			{/if}
		</select>
		</div>
		<div   id="reminder_{$position|intval}_admin_mails" style="{if !$manual_process}display:none{/if}">
			<label class="control-label">
					<span class="label-tooltip" data-toggle="tooltip"
					title="{l s='This field permit to specify the email addresses to be notified. 
								 A message with the customer information and the contents of the cart will be sent.
								 If you want multiple addresses, you must be separated by commas eg : email1@example.com, email2@example.com'  mod='tacartreminder' }">
						{l s='Admin Mails' mod='tacartreminder' }
					</span>
				</label><br/><br/>
				<input name="reminder_{$position|intval}_admin_mails"  type="text" placeholder="{l s='eg : admin@myshop.com' mod='tacartreminder'}" value="{$admin_mails|escape:'htmlall':'UTF-8'}"/>
		</div>
	</td>
	<td width="150px">
		{if $position==1}
		<label class="control-label">
			<span class="label-tooltip" data-toggle="tooltip"
				  title="{l s='Number of hours after the cart is considered abandoned'  mod='tacartreminder' }">
						{l s='After abandonned' mod='tacartreminder' }
			</span>
		</label>
		{elseif $position > 1}
		<label class="control-label">
			<span class="label-tooltip" data-toggle="tooltip"
				  title="{l s='Number of hours after the reminder %s is executed'  mod='tacartreminder' sprintf=($position-1)}">
						{l s='After reminder %s' mod='tacartreminder' sprintf=($position-1)}
			</span>
		</label>
		{/if}
		
		<br/><br/>
		<div class="input-group">
			
			<span class="input-group-addon">{l s='hour' mod='tacartreminder'}</span>
			<input type="text" name="reminder_{$position|intval}_nb_hour" class="input-mini" value="{$nb_hour|floatval}"></input>
		</div>
	</td>
	<td class="action_delete" data-position="{$position|intval}">
		{if $delete_avalable}
		<a class="btn btn-default check_reminder_to_delete" data-pos-reminder="{$position|intval}" data-id-reminder="{$id_reminder|intval}" href="javascript:;" >
			<i class="flaticon-cancel6"></i>
		</a>
		{/if}
	</td>
</tr>