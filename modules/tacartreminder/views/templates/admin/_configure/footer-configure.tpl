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
 * Display in all footer controller and configuration content
 *}
{* end of col-md-9 in header-configure.tpl*}
</div>
{* end of row in header-configure.tpl*}
</div>

<script type="text/javascript">
$(document).ready(function(){
	var tab_configure = '{$tab_configure|escape:'html':'UTF-8'}';
	if(tab_configure=='mail')
		$('#ta_cartreminder_mail_template').show();
	if(tab_configure=='rule')
		$('#ta_cartreminder_rule').show();
	if(tab_configure=='configuration')
		$('#tacartreminder_form').show();
});
</script>