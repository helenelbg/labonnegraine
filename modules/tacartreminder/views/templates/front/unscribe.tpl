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
 * Front template when user click at unsuscribe link
 * Developer can be override this file with the template such
 * theme/[your_theme]/modules/tacartreminder/views/templates/front/unscribe.tpl
 * Please not update directly this
 *}
{if $success_unscriber}
	<p class="alert alert-success success">
		{l s='You have successfully unsubscribed.' mod='tacartreminder'}
	</p>
{else}
	<p class="alert alert-warning warning">
		{$message_err|escape:'html':'UTF-8'}
	</p>
{/if}