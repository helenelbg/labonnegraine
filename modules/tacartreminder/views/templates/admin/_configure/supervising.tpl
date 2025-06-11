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
 * Tab configure in setting tab
 *}
<div class="panel" id="fieldset_0">
	<div class="panel-heading">
		<i class="flaticon-stopwatch6"></i> {l s='Supervising & Optimization' mod='tacartreminder'}
	</div>
	<h4>{l s='Check optimization database' mod='tacartreminder'}</h4>
	<p class="ta-alert alert-info">
	{l s='The module also ensures that you have solutions for monitoring your data and provides quick actions to set up to optimize the data.
These actions are in the scope of the module and do not affect any other data other than the module.' mod='tacartreminder'}
	</p>
	<table>
	<tr>
		<td>{l s='Journal Cart expirate Messages Systems' mod='tacartreminder'}</td>
		<td>{$total_journal_system_message_expirate|intval}</td>
		<td>
			<a href="{$admin_module_url|escape:'quotes':'UTF-8'}&module_name=cronjobs" target="_blank" class="btn btn-default">
				{l s='Delete Messages Systems' mod='tacartreminder'}
		</a>
		</td>
	</tr>
	</table>
	<p class="ta-alert ta-alert-info"></p>
</div>