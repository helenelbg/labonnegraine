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
 * Display in all page
 *}
{if isset($saverule_sucess) && $saverule_sucess}
	<div class="ta-alert alert-success">{l s='Operation successful' mod='tacartreminder'}</div>
{/if}
<div class="row">
	<div class="navigation col-md-2">
		<nav class="list-group">
			<a class="list-group-item {if $tab_configure == 'mail'}active{/if}" href="{$link->getAdminLink('AdminModules')|escape:'quotes':'UTF-8'}&configure=tacartreminder&tab_select=settings&tab_configure=mail">{l s='Email templates' mod='tacartreminder'}</a>
			<a class="list-group-item {if $tab_configure == 'rule'}active{/if}" href="{$link->getAdminLink('AdminModules')|escape:'quotes':'UTF-8'}&configure=tacartreminder&tab_select=settings&tab_configure=rule">{l s='Rules' mod='tacartreminder'}</a>
			<a class="list-group-item {if $tab_configure == 'configuration'}active{/if}" href="{$link->getAdminLink('AdminModules')|escape:'quotes':'UTF-8'}&configure=tacartreminder&tab_select=settings&tab_configure=configuration">{l s='Configuration' mod='tacartreminder'}</a>
			<a class="list-group-item {if $tab_configure == 'cronjob'}active{/if}" href="{$link->getAdminLink('AdminModules')|escape:'quotes':'UTF-8'}&configure=tacartreminder&tab_select=settings&tab_configure=cronjob">{l s='Automated task' mod='tacartreminder'}</a>
			<!-- a class="list-group-item {if $tab_configure == 'supervising'}active{/if}" href="{$link->getAdminLink('AdminModules')|escape:'quotes':'UTF-8'}&configure=tacartreminder&tab_select=settings&tab_configure=supervising">{l s='Supervising' mod='tacartreminder'}</a-->
		</nav>
	</div>
	<div class="col-md-10">