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
 *}
<div class="message-item" data-filter-info="{if !$message['is_system']|intval}employee{else}notification{/if}">
	<div class="message-avatar">
		<div class="avatar-md">
			<i class="{if $message['is_system']|intval}flaticon-bell31{else}flaticon-man457{/if} icon-2x"></i>
		</div>
	</div>
	<div class="message-body">
		<span class="message-date">&nbsp;<i class="flaticon-calendar146"></i>
			{dateFormat date=$message['date_add'] full=1}&nbsp;
		</span>
		<h4 class="message-item-heading">
		{if ($message['elastname']|escape:'html':'UTF-8')}
			{$message['elastname']|escape:'html':'UTF-8'}&nbsp;{$message['efirstname']|escape:'html':'UTF-8'}
		{/if}
		</h4>
		<p class="message-item-text">
			{$message['message']|escape:'html':'UTF-8'|nl2br}
		</p>
	</div>
</div>
