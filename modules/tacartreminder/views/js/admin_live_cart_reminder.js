/**
 * Cart Reminder
 * 
 *    @category advertising_marketing
 *    @author    Timactive - Romain DE VERA <support@timactive.com>
 *    @copyright Copyright (c) TIMACTIVE 2014 - Romain De Véra AI
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
 * All client action for the page admin
 */

var clocks = [];
function add_cart_rule(id_cart_rule)
{
	console.log(id_cart_rule);
}
function add_cart_rule_in_cart(id_cart_rule,id_cart, id_customer, id_reminder)
{
	$.ajax({
		type:"POST",
		url: admin_cart_url,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			token: admin_cart_token,
			tab: "AdminCarts",
			action: "addVoucher",
			id_cart_rule: id_cart_rule,
			id_cart: id_cart,
			id_customer: id_customer
			},
		success : function(res)
		{
			$('#cart_rule_filter').val('');
			var errors = '';
			if (res.errors.length > 0)
			{
				$.each(res.errors, function() {
					errors += this+'<br/>';
				});
				$('#vouchers_err').html(errors).show();
			}
			else
				launchReminderManual(id_cart,id_reminder);
		}
	});
}
function remove_cart_rule_in_cart(id_cart_rule,id_cart,id_customer,id_reminder)
{
	$.ajax({
		type:"POST",
		url: admin_cart_url,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			token: admin_cart_token,
			tab: "AdminCarts",
			action: "deleteVoucher",
			id_cart_rule: id_cart_rule,
			id_cart: id_cart,
			id_customer: id_customer
			},
		success : function(res)
		{
			launchReminderManual(id_cart,id_reminder);
		}
	});
}
function launchReminderManual(id_cart, id_reminder)
{
	var d = new Date();
	var url = 'index.php?controller=AdminLiveCartReminder&submitAction=showReminderManualProcess&id_cart='+id_cart+'&id_reminder='+id_reminder+'&token='+token+
	'&random='+d.getTime();
	var id_customer;
	$.fancybox({
	        href: url,
	        autoSize: false,
	        autoDimensions: false,
	        width: '90%',
			height: '90%',
	        type: 'ajax',
	        beforeLoad : function() {
	        	$element_fancy = $(this.element);
	        },
	        ajax: {
	            complete: function(jqXHR, textStatus) {
	            	$(document).trigger( "ta-filter-tab-load" );
	            	id_customer = $('#cart_rule_customer_id').val();
	            	if(ps15)
	            	{
	            		tinySetupPS15({
		    				editor_selector :"autoload_rte",
		    				relative_urls : false,
		    				paste_data_images: true,
		    				extended_valid_elements : "em[class|name|id],html,head"
		    			});
	            	}
	            	else
	            	{
	            		tinySetup({
							editor_selector :"autoload_rte",
							convert_urls : false,
							relative_urls : false,
							paste_data_images: true,
							remove_script_host : false,
							plugins : "colorpicker link image paste pagebreak table contextmenu filemanager table code media autoresize textcolor fullpage",
							extended_valid_elements : "em[class|name|id],html,head"
		            	});
	            	}
	            	var url_search_rules = 'index.php?controller=AdminLiveCartReminder&action=get_cart_rules&submitAction=getCartRules&token='+token;
	            	$('#cart_rule_filter').autocomplete(
	            		url_search_rules, {
	         			minChars: 2,
	         			max: 50,
	         			width: 500,
	         			selectFirst: false,
	         			scroll: false,
	         			dataType: 'json',
	         			formatItem: function(data, i, max, value, term) {
	         				return value;
	         			},
	         			parse: function(data) {
	         				var mytab = new Array();
	         				for (var i = 0; i < data.length; i++)
	         					mytab[mytab.length] = { data: data[i], value: data[i].id_cart_rule + ' ' + data[i].name };
	         				return mytab;
	         			},
	         			extraParams: {
	         				cart_rule_filter: 1
	         			}
	         			}
	         		)
	         		.result(function(event, data, formatted) {
	         			add_cart_rule_in_cart(data.id_cart_rule,id_cart,id_customer,id_reminder);
	         			$('#cart_rule_filter').val(data.id_cart_rule + ' ' + data.name);
	         		});
	            	}
	        },
	        afterLoad: function() {
	        	
	        }
	 });
}
$(document).on('ta-filter-tab-load',function() {
	$('.ta-filter-tab').each(function() {
		var id_element = $(this).data('filter-content-id');
		var $filter_content_element = $('#'+id_element);
		var filter_item_class = $(this).data('filter-item-class');
	$(this).find('li').each(function(){
	    var filter_target = $(this).data('filter-target');
	    var nb_element = 0;
	    if(filter_target=='')
	    	nb_element = $filter_content_element.find('.'+filter_item_class).length;
	    else
	    	nb_element = $filter_content_element.find('.'+filter_item_class).filter('[data-filter-info="'+filter_target+'"]').length;
	    $(this).find('.ta-count-element').html(nb_element);
	 });
	});
});
$( document ).ready(function() {
	$('.check_reminder_rules').click(function() {
		var d = new Date();
		var url = 'index.php?controller=AdminLiveCartReminder&submitAction=showCheckRules&id_cart='+$(this).data('id-cart')+'&token='+token+
		'&random='+d.getTime();
		$.fancybox({
	        autoSize: false,
	        autoDimensions: false,
	        'width':'90%',
	        'height':'90%',
	        href: url,
	        type: 'ajax'
	    });
	 });
	
	$(document).on('click','.launch-reminder-manual',function(){
		$(document).trigger('launchmanualprocess',[0,5]);
		launchReminderManual($(this).data('id-cart'),$(this).data('id-reminder'));
	});
	$(document).on('click','.cart_rule_fancy',function(){
		 var url= $(this).data("fancybox-href");
		 var id_cart = $(this).data('id-cart');
		 var id_reminder = $(this).data('id-reminder');
		 $.fancybox({
			 	'type': 'iframe',
				'width': '90%',
				'height': '90%',
				'autoDimensions': false,
		        'href': url,
		        afterClose: function() {
		        	launchReminderManual(id_cart,id_reminder);
				}
		    });
	});

	$('.messages-summary').click(function() {
		var d = new Date();
		var id_journal = $(this).data('id-journal');
		var url = 'index.php?controller=AdminLiveCartReminder&submitAction=showMessages&id_journal='+id_journal+'&token='+token+
		'&random='+d.getTime();
		var id_customer;
		$.fancybox({
		        href: url,
		        autoSize: false,
		        autoDimensions: false,
		        width: '90%',
				height: '90%',
		        type: 'ajax',
		        ajax: {
		            complete: function(jqXHR, textStatus) {
		            	$(document).trigger( "ta-filter-tab-load" );
		            }
		        },
		        afterLoad: function() {
		        	
		        }
		 });
	});
	$(document).on('click','.ta-filter-tab li',function(){
		
		var $filter_tab_ul = $(this).parent('.ta-filter-tab');
		$filter_tab_ul.find('li').removeClass('active');
		var id_element = $filter_tab_ul.data('filter-content-id');
		var $filter_content_element = $('#'+id_element);
		var filter_item_class = $filter_tab_ul.data('filter-item-class');
		 var filter_target = $(this).data('filter-target');
		if(filter_target=='')
			$filter_content_element.find('.'+filter_item_class).show();
	    else
	    {
	    	$filter_content_element.find('.'+filter_item_class).hide();
	    	$filter_content_element.find('.'+filter_item_class).filter('[data-filter-info="'+filter_target+'"]').show();
	    }
		$(this).addClass('active');
	});
	
	
	
	$('.launch-reminder').click(function(){
		var d = new Date();
		var url = 'index.php?controller=AdminLiveCartReminder&submitAction=showReminderLaunchForm&id_cart='+$(this).data('id-cart')+'&id_reminder='+$(this).data('id-reminder')+'&token='+token+
		'&random='+d.getTime();
		 $.fancybox({
		        autoSize: true,
		        autoDimensions: true,
		        href: url,
		        type: 'ajax'
		 });
	});
	$('.flip-clock-wrapper').each(function(){
		
		clocks.push($(this).FlipClock($(this).data('nb-second'),{
	        countdown:true,
	        clockFace: 'DailyCounter',
	        createDivider:function(label, css, excludeDots){
	        	if(typeof css == "boolean" || !css) {
					excludeDots = css;
					css = label;
				}

				var dots = [
					'<span class="'+this.factory.classes.dot+' top"></span>',
					'<span class="'+this.factory.classes.dot+' bottom"></span>'
				].join('');

				if(excludeDots) {
					dots = '';	
				}

		

				var html = [
					'<span class="'+this.factory.classes.divider+' '+(css ? css : '').toLowerCase()+'">',
						'',
						dots,
					'</span>'
				];	
		
		return $(html.join(''));

	        }
	    }));
	});
	
	/*Copy mail template*/
	$(document).on('click','.use-mail-template',function(){
		var url = 'index.php?controller=AdminLiveCartReminder';  
		$form = $(this).parents('form:first');
		$form.hide();
		$loader = $form.closest('.panel').find('.ta_form_loader:first');
		$loader.show();
		$.ajax({
		      type: "POST",
		      cache: false,
		      async: true,
			  cache: false,
			  url:	url,
			  dataType: 'json',
			  data: 'submitAction=getMail&action=get_mail'+
			  	'&'+$form.serialize() + 
			  	'&token=' + token +
			  	'&rand=' + new Date().getTime(),
		      success: function (data) {
		    	  $loader.hide(200);
				  $form.show(100);
				  $form.find('textarea[name="content_html"]').val('');
				  $form.find('input[name="subject"]').val('');
				  $form.find('.ta_form_error ul').html('');
				  if (!data.has_error)
				  {
					  if(data.content_html)
					  {
						  $form.find('textarea[name="content_html"]').val(data.content_html);
						  for (editorId in tinyMCE.editors) {
							  if (editorId.indexOf('content_html') !== -1 || editorId.indexOf('ta_mail_content') !== -1) {
								  var orig_element = $(tinyMCE.get(editorId).getElement());
								  var name = orig_element.attr('name');
								  if (name === 'content_html') {
									  tinyMCE.get(editorId).setContent(data.content_html);
								  }
							  }
						  }
					  }
					  if(data.subject)
					  {
						  $form.find('input[name="subject"]').val(data.subject);
					  }
				  }
				  else
				  {
					  $form.find('.ta_form_error ul').html('');
						$.each(data.errors, function(index, value) {
							$form.find('.ta_form_error ul').append('<li class="ta-alert alert-danger">'+value+'</li>');
						});
					 $form.find('.ta_form_error').slideDown('slow');
				  }
		      }
		  });
	  });
	$(document).on('click','.send-mail-customer',function(){
		var url = 'index.php?controller=AdminLiveCartReminder';  
		$form = $(this).parents('form:first');
		$form.hide();
		$loader = $form.closest('.panel').find('.ta_form_loader:first');
		$loader.show();
		tinyMCE.triggerSave();
		$.ajax({
		      type: "POST",
		      cache: false,
		      async: true,
			  cache: false,
			  url:	url,
			  dataType: 'json',
			  data: 'submitAction=sendMail&action=send_mail'+
			  	'&'+$form.serialize() + 
			  	'&token=' + token +
			  	'&rand=' + new Date().getTime(),
		      success: function (data) {
		    	  $loader.hide(200);
				  $form.show(100);
				  $form.find('.ta_form_error ul').html('');
				  if (!data.has_error)
				  {
					$form.find('textarea[name="content_html"]').val('');
					$form.find('input[name="subject"]').val('');
					
					  for (editorId in tinyMCE.editors) {
						  if (editorId.indexOf('content_html') !== -1) {
							  var orig_element = $(tinyMCE.get(editorId).getElement());
							  var name = orig_element.attr('name');
							  if (name === 'content_html') {
								  tinyMCE.get(parseInt(editorId)).setContent(data.content_html);
							  }
						  }
					  }
					$.each(data.success, function(index, value) {
							$form.find('.ta_form_error ul').append('<li class="ta-alert alert-success">'+value+'</li>');
					});
				  }
				  else
				  {
					$.each(data.errors, function(index, value) {
							$form.find('.ta_form_error ul').append('<li class="ta-alert alert-danger">'+value+'</li>');
					});
				  }
				  $form.find('.ta_form_error').slideDown('slow');
				  if(data.message)
				  {
					$('#messages-reminder').show();
					$('#messages-reminder').prepend(data.message);
					var nbmessage = $('#messages-reminder').find('.message-item').length;
					$('#messages-reminder').closest('.panel').find('.badge').html(nbmessage);
				  }
		      }
		  });
	  });
	$(document).on('click','.submit-message-reminder',function(){
		$formmessage = $(this).parents('form:first');
		$formmessage.hide();
		$loader = $formmessage.closest('.panel').find('.ta_form_loader:first');
		$loader.show();
		var url = 'index.php?controller=AdminLiveCartReminder';
		$.ajax({
				type: 'POST',
				async: true,
				cache: false,
				url:	url,
				dataType: 'json',
				data: 'submitAction=saveMessage'+
				      '&action=save_message' +
					  '&'+$formmessage.serialize() + 
					  '&token=' + token +
					  '&rand=' + new Date().getTime(),
				success: function(data)
				{
					$formmessage.find('.ta_form_error ul').html('');
					$loader.hide(200);
					$formmessage.show(100);
					$formmessage.find('textarea[name="message"]').val('');
					if (!data.has_error && data.message)
					{
						$formmessage.closest('.panel').find('#fa-content-messages').show();
						$('#messages-reminder').show();
						$formmessage.closest('.panel').find('#fa-content-messages').prepend(data.message);
						var nbmessage = $formmessage.closest('.panel').find('.message-item').length;
						$formmessage.closest('.panel').find('.badge').html(nbmessage);
						$(document).trigger( "ta-filter-tab-load" );
					}
					else
					{
						$.each(data.errors, function(index, value) {
							$formmessage.find('.ta_form_error ul').append('<li class="ta-alert alert-danger">'+value+'</li>');
						});
						$formmessage.find('.ta_form_error').slideDown('slow');
					}
				},
				error:function()
				{
					$loader.hide(200);
				}
			});


	});
	$(document).on('click','#submit-launch-reminder',function(){
		var url = 'index.php?controller=AdminLiveCartReminder';
		$('#reminder_launch_form').hide();
		$('#reminder_launch_form_loader').show();
		$.ajax({
				type: 'POST',
				url:	url,
				async: true,
				cache: false,
				dataType: 'json',
				data: 'submitAction=performReminder'+
				      '&action=perform_reminder' +
					  '&'+$('#reminder_launch_form').serialize() + 
					  '&token=' + token +
					  '&rand=' + new Date().getTime(),
				success: function(data)
				{
					$('#reminder_launch_form_loader').hide(200);
					$('#launchreminder_form_error ul').html('');
					if (!data.has_error)
					{
						/*$.each(data.success, function(index, value) {
							$('#launchreminder_form_error ul').append('<li class="ta-alert alert-success success">'+value+'</li>');
						});*/
						location.reload();
					}
					else
					{
						$('#reminder_launch_form').show(100);
						$.each(data.errors, function(index, value) {
							$('#launchreminder_form_error ul').append('<li class="ta-alert alert-danger">'+value+'</li>');
						});
						$('#launchreminder_form_error').slideDown('slow');
					}
				},
				error:function()
				{
					$('#reminder_launch_form_loader').hide(200);
				}
			});
	});
	$(document).on('click','.manual-submit-perform-reminder',function(){
		var url = 'index.php?controller=AdminLiveCartReminder';
		var id_reminder = $(this).data('id-reminder');
		var type_perform = $(this).data('type-perform');
		var id_cart = $(this).data('id-cart');
		$('.manual-submit-perform-reminder').hide();
		$loader = $('#ta_form_loader_manual_perform');
		$loader.show();
		$.ajax({
				type: 'POST',
				url:	url,
				async: true,
				cache: false,
				dataType: 'json',
				data: 'submitAction=performReminder'+
				      '&action=perform_reminder' +
					  '&id_reminder=' + id_reminder +
					  '&id_cart=' + id_cart +
					  '&type_perform=' + type_perform +
					  '&token=' + token +
					  '&rand=' + new Date().getTime(),
				success: function(data)
				{
					$loader.hide(200);
					if (!data.has_error)
					{
						$.each(data.success, function(index, value) {
							$('#ta_form_error_manual_perform ul').append('<li class="ta-alert alert-success success">'+value+'</li>');
						});
						$('#ta_form_error_manual_perform').slideDown('slow');
						location.reload();
						launchReminderManual(id_cart,id_reminder);
					}
					else
					{
						$('.manual-submit-perform-reminder').show(100);
						$('#ta_form_error_manual_perform ul').html('');
						$.each(data.errors, function(index, value) {
							$('#ta_form_error_manual_perform ul').append('<li class="ta-alert alert-danger">'+value+'</li>');
						});
						$('#ta_form_error_manual_perform').slideDown('slow');
					}
				},
				error:function()
				{
					$loader.hide(200);
				}
			});
	});
	$(document).on('click','.ta-panel-openorclose',function(){
		if($(this).hasClass('open'))
		{
			$(this).removeClass('open');
			$(this).removeClass('flaticon-minus87');
			$(this).addClass('flaticon-add133');
			$(this).parent().next('.panel-content').hide();
		}
		else
		{
			$(this).parent().next('.panel-content').show();
			$(this).addClass('open');
			$(this).removeClass('flaticon-add133');
			$(this).addClass('flaticon-minus87');
		}
	});
	$(document).on('click','.cg-rule-openorclose',function(){
		var id_rule = $(this).data('id-rule');
		if($(this).hasClass('open'))
		{
			$('div.groupcondition-'+id_rule).fadeIn();
			$(this).removeClass('open');
			$(this).removeClass('flaticon-add133');
			$(this).addClass('flaticon-minus87');
			
		}
		else
		{
			$(this).addClass('open');
			$(this).removeClass('flaticon-minus87');
			$(this).addClass('flaticon-add133');
			$('div.groupcondition-'+id_rule).fadeOut();
			
		}
	});
	$(document).on('click','.ta-reminders-openorclose',function(){
		var id_cart = $(this).data('id-cart');
		if($(this).hasClass('taopened'))
		{
			$('tr.reminder-line-'+id_cart).fadeIn();
			$(this).removeClass('taopened');
			$(this).removeClass('flaticon-add133');
			$(this).addClass('flaticon-minus87');
		}
		else
		{
			$(this).addClass('taopened');
			$(this).removeClass('flaticon-minus87');
			$(this).addClass('flaticon-add133');
			$('tr.reminder-line-'+id_cart).fadeOut();
			
		}
	});
	
});
