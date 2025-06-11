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
		<i class="flaticon-stopwatch6"></i> {l s='Automated task' mod='tacartreminder'}
	</div>
	<div class="ta-alert alert-info">
		{l s='To run autonomously you need to add a cron task.' mod='tacartreminder'}<br/>
	</div>
	<h4>{l s='Crontab' mod='tacartreminder'}</h4>
	{l s='Add the following lines to the crontab of your server :' mod='tacartreminder'}
	<p style="padding:5px;margin-top:10px;border-radius:5px;border:1px solid #CECECE;">
		*/15 * * * * curl "{$cron_url|escape:'quotes':'UTF-8'}"
	</p>
	<p>
	{l s='In this example, the module will check for reminders to run every 15 minutes.' mod='tacartreminder'}
	</p>
	<br/>
	<h4>{l s='OR' mod='tacartreminder'} {l s='Manual Solution / browser URL' mod='tacartreminder'}</h4>
	<p>
		{l s='Copy link into your browser or click on the following link' mod='tacartreminder'}<br/>
		<a href="{$cron_url|escape:'quotes':'UTF-8'}">{$cron_url|escape:'quotes':'UTF-8'}</a>
	</p>
</div>