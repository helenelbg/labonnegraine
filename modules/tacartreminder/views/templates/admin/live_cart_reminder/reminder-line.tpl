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
 * Reminder line present in list of reminder performed
 *}
<tr class="reminder-action-line reminder-line-{$id_cart|intval} {if ($journal_reminder && $journal_reminder.manual_process) || (!$journal_reminder && $reminder.manual_process)}manual-process{/if}" style="display:none">
	<td style="padding:7px;">
		<span class="position_reminder_line {if $to_launch}pendinglaunch{/if}{if $to_accomplish}pendingaccomplish{/if}" style="background-color:{if $journal_reminder && !$to_accomplish}#79bd3c{elseif $to_launch}orange{elseif $to_accomplish}#FF0066{else}#999{/if}">
			{if isset($journal_reminder_position)}{$journal_reminder_position|intval}{elseif isset($reminder)}{$reminder.position|intval}{else}?{/if}
		</span>
	</td>
	<td>
		{if $journal_reminder}
			{if !$journal_reminder.manual_process}
				{if $journal_reminder.isopen}
					<i class="flaticon flaticon-eye46" style="color:#79bd3c;font-size:16px;" title="{l s='The email was opened' mod='tacartreminder'}"></i>
				{/if}
				{if $journal_reminder.isclick}
					<i class="flaticon flaticon-left27" style="color:#79bd3c;font-size:22px;" title="{l s='The email was clicked' mod='tacartreminder'}"></i>
				{/if}
				<br/>
			{/if}
			{if $journal_reminder.manual_process}
				{if $journal_reminder.performed}
					<span style="font-size:10px">{l s='Completed on ' mod='tacartreminder'} {dateFormat date=$journal_reminder.date_performed full=1} 
					{if $journal_reminder.id_employee|intval}{l s='by' mod='tacartreminder'} {$journal_reminder.e_firstname|escape:'htmlall':'UTF-8'} {$journal_reminder.e_lastname|escape:'htmlall':'UTF-8'}{/if}</span>
				{elseif !$to_accomplish}
					<span style="font-size:10px">{l s='Not Completed' mod='tacartreminder'}</span>
				{/if}
			{else}
				<span style="font-size:10px">{l s='Sent on ' mod='tacartreminder'} {dateFormat date=$journal_reminder.date_performed full=1}</span>
			{/if}
		{/if}
		{if !$journal_reminder && $to_launch && $cart_reminder_to_close}
			{l s='The rule no longer applies. The reminder will be canceled' mod='tacartreminder'}
		{elseif $to_accomplish}
			{l s='To do' mod='tacartreminder'}
		{elseif !$journal_reminder && $to_launch && $nbsecond > 0}
			<span class="flip-clock-wrapper" data-id-reminder="{$reminder.id_reminder|intval}" data-nb-second="{$nbsecond|intval}"></span>
		{elseif !$journal_reminder && !$to_launch}
			{l s='Pending previous reminder' mod='tacartreminder'}
		{elseif !$journal_reminder && $to_launch}
			{l s='Pending action' mod='tacartreminder'}
		{/if}
	</td>
	<td style="padding:0">
		{if ($journal_reminder && $journal_reminder.manual_process && !$to_accomplish) || (isset($reminder) && ($journal_reminder || ($to_launch && !$cart_reminder_to_close))) }
			{if ($journal_reminder && $journal_reminder.manual_process) || (!$journal_reminder && $reminder.manual_process)}
				<a href="javascript:;" data-id-reminder="{$reminder.id_reminder|intval}" data-id-cart="{$id_cart|intval}" class="launch-reminder-manual" ><i class="flaticon-support3" style="font-size:20px;cursor:pointer;"></i></a>
			{else}
				<a href="javascript:;" data-id-reminder="{$reminder.id_reminder|intval}" data-id-cart="{$id_cart|intval}" class="launch-reminder"><i class="flaticon-mail29" style="font-size:20px;cursor:pointer;"></i></a>
			{/if}
		{/if}
	</td>
</tr>
{if ($journal_reminder && !$journal_reminder.manual_process) || (!$journal_reminder && !$reminder.manual_process && !$cart_reminder_to_close)}
<tr class="reminder-mail-line reminder-line-{$id_cart|intval}" style="display:none">
	<td colspan="3" style="text-align:center"><i >{if !$journal_reminder}{$reminder.mail_template_name|escape:'htmlall':'UTF-8'}{else}{$journal_reminder.mail_name|escape:'htmlall':'UTF-8'}{/if}</i></td>
</tr>
{/if}