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
 * Footer module display documentation link with help associated at current page
 *}
<div class="row" id="ta-footer-module">
<h3>{l s='Read before using' mod='tacartreminder'}</h3>
<div class="col-lg-12">
	<div class="col-xs-5 col-lg-2 col-sm-2 block-logo">
		<img src="../modules/tacartreminder/views/img/logo57x57.png"/>
	</div>
	<div class="col-xs-7 col-lg-10 col-sm-10">
		{if isset($tab_select) && $tab_select == 'running'}
			<a href="#" class="btn btn-default ta-help-fancy ta-help-page"  href="javascript:;"data-fancybox-href="{$link->getAdminLink('AdminLiveCartReminder')|escape:'htmlall':'UTF-8'}&helppage=steps-running&submitAction=showHelp" title="{l s='Help for this page' mod='tacartreminder'}"><img src="../modules/tacartreminder/views/img/helppage.png"/></a>
		{/if}
		{if isset($tab_select) && $tab_select == 'cart'}
			<a href="#" class="btn btn-default ta-help-fancy ta-help-page"  href="javascript:;"data-fancybox-href="{$link->getAdminLink('AdminLiveCartReminder')|escape:'htmlall':'UTF-8'}&helppage=steps-cart&submitAction=showHelp&tab_select=running" title="{l s='Help for this page' mod='tacartreminder'}"><img src="../modules/tacartreminder/views/img/helppage.png"/></a>
		{/if}
		{if isset($tab_select) &&  $tab_select == 'finished'}
			<a href="#" class="btn btn-default ta-help-fancy ta-help-page"  href="javascript:;"data-fancybox-href="{$link->getAdminLink('AdminLiveCartReminder')|escape:'htmlall':'UTF-8'}&helppage=steps-finished&tab_select=finished&submitAction=showHelp" title="{l s='Help for this page' mod='tacartreminder'}"><img src="../modules/tacartreminder/views/img/helppage.png"/></a>
		{/if}
		{if isset($tab_select) && $tab_select == 'manual'}
			<a href="#" class="btn btn-default ta-help-fancy ta-help-page"  href="javascript:;"data-fancybox-href="{$link->getAdminLink('AdminLiveCartReminder')|escape:'htmlall':'UTF-8'}&helppage=steps-manual&tab_select=manual&submitAction=showHelp" title="{l s='Help for this page' mod='tacartreminder'}"><img src="../modules/tacartreminder/views/img/helppage.png"/></a>
		{/if}
		{if isset($tab_select) && $tab_select == 'unsubscribes'}
			<a href="#" class="btn btn-default ta-help-fancy ta-help-page"  href="javascript:;"data-fancybox-href="{$link->getAdminLink('AdminLiveCartReminder')|escape:'htmlall':'UTF-8'}&helppage=unsubscribes&tab_select=unsubscribes&submitAction=showHelp" title="{l s='Help for this page' mod='tacartreminder'}"><img src="../modules/tacartreminder/views/img/helppage.png"/></a>
		{/if}
		{if isset($tab_configure) && $tab_configure == 'configuration'}
			<a href="#" class="btn btn-default ta-help-fancy ta-help-page"  href="javascript:;"data-fancybox-href="{$link->getAdminLink('AdminLiveCartReminder')|escape:'htmlall':'UTF-8'}&helppage=configuration&tab_select=configuration&submitAction=showHelp" title="{l s='Help for this page' mod='tacartreminder'}"><img src="../modules/tacartreminder/views/img/helppage.png"/></a>
		{/if}
		{if isset($tab_configure) && $tab_configure == 'rule'}
			<a href="#" class="btn btn-default ta-help-fancy ta-help-page"  href="javascript:;"data-fancybox-href="{$link->getAdminLink('AdminLiveCartReminder')|escape:'htmlall':'UTF-8'}&helppage=rule&tab_select=rule&submitAction=showHelp" title="{l s='Page help' mod='tacartreminder'}"><img src="../modules/tacartreminder/views/img/helppage.png"/></a>
		{/if}
		<a href="../modules/tacartreminder/readme_en.pdf" class="btn btn-default"><img src="../modules/tacartreminder/views/img/doc_en.png"/></a>
		<a href="../modules/tacartreminder/readme_fr.pdf"  class="btn btn-default"><img src="../modules/tacartreminder/views/img/doc_fr.png"/></a>
		<a href="../modules/tacartreminder/readme_es.pdf" class="btn btn-default"><img src="../modules/tacartreminder/views/img/doc_es.png"/></a>
	</div>
</div>
</div>
<script type="text/javascript">
$('.ta-help-fancy').click(function() {
		var url = $(this).data('fancybox-href');
		$.fancybox({
	        autoSize: true,
	        autoDimensions: true,
	        href: url,
	        beforeShow: function(){
	        	  /*$(".fancybox-skin").css("backgroundColor","#700227");*/
	        },
	        type: 'ajax'
	    });
	});
</script>