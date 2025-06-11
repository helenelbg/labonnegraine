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
 * All action client for rule form
 */

var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating; //flag to prevent quick multi-click glitches

$( document ).ready(function() {
	$( "tr[data-id-group-condition]" ).each(function(i)
	{
		var id_group_condition = parseInt($(this).data('id-group-condition'));
		condition_counters[id_group_condition]=parseInt($(this).data('count-condition'));
	});
	$('#main').addClass('rule');
	$('body').addClass('rule');
	$('.condition_toselect option').each(function(i) {
		$(this).attr('selected', true);
	});
	triggerDisplayForceReminder();
	
	 toggle_discount();
	 $("input[name$='create_cart_rule']").click(function() {
		toggle_discount();
	 });
	 $("input[name$='submitRule']").click(function() {
		 $('.condition_toselect option').each(function(i) {
				$(this).attr('selected', true);
			});
		 $('#rule_wizard .action-button').addClass('disabled');
		 $.ajax({
			type:"POST",
			url : $('#rule_step_general_form').attr("action"),
			async: false,
			dataType: 'json',
			data : $('#rule_wizard form').serialize() + '&submitEditRule=1&action=finish_step&ajax=1&step_number='+($("#progressbar li.active").last().index()+1),
			success : function(data) {
				$('#rule_wizard .action-button').removeClass('disabled');
				if (data.has_error)
					displayError(data.errors);
				else
					window.location.href = tacartreminder_configure_url + '&saverule_sucess&tab_configure=rule';
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				alert(XMLHttpRequest.responseText);
				$('#rule_wizard .action-button').removeClass('disabled');
				jAlert("TECHNICAL ERROR: \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	 });
	 function displayError(errors)
	 {
		$('#rule_wizard .action-button').removeClass('disabled');
	 	$('.wizard_error').remove();
	 	str_error = '<div class="error wizard_error" style="display:none"><ul>';
	 	for (var error in errors)
	 	{
	 		str_error += '<li class="alert alert-danger">'+errors[error]+'</li>';
	 	}
	 	$('#progressbar').after(str_error+'</ul></div>');
	 	$('.wizard_error').fadeIn('fast');
	 }
     $('#cart_rule_filter')
		.autocomplete(
			tamodule_url+'&ajax_action=cart_rule_filter', {
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
			$('#id_cart_rule').val(data.id_cart_rule);
			$('#cart_rule_filter').val(data.id_cart_rule + ' ' + data.name);
	});
     $("#progressbar li").click(function(){
    	 $(".ta-step").eq($("#progressbar li.active").last().index()).hide();
    	 $("#progressbar li").removeClass("active");
    	 i = 0;
    	 for (i = 0; i <= $(this).index(); i++) 
    	 { 
    		 $("#progressbar li").eq(i).addClass("active");
    	 } 
    	 $(".ta-step").eq($(this).index()).fadeIn();
     });
     $("#rule_wizard .next").click(function(){
    		
    		current_fs = $(this).closest(".ta-step");
    		next_fs = current_fs.next();
    		
    		//activate next step on progressbar using the index of next_fs
    		$("#progressbar li").eq($(".ta-step").index(next_fs)).addClass("active");
    		
    		//show the next fieldset
    		current_fs.hide();
    		 //$(this).hide('slide',{direction:'right'},1000);
    		next_fs.fadeIn();
    	});

	$("#rule_wizard .previous").click(function(){
		
		current_fs = $(this).closest(".ta-step");
		previous_fs = current_fs.prev();
		
		//de-activate current step on progressbar
		$("#progressbar li").eq($(".ta-step").index(current_fs)).removeClass("active");
		
		//show the previous fieldset
		current_fs.hide();
		previous_fs.fadeIn(); 
		//hide the current fieldset with style
	});

	$("#rule_wizard .submit").click(function(){
		return false;
	})
	}
);



function toggle_discount()
{
	 var show = $("input[name$='create_cart_rule']:checked").val();
	 if(show == 1)
	 {
	 	$('#block_select_discount').show('slide');
	 }
	 else
	 {
	 	$('#block_select_discount').hide();
	 }
}
function triggerDisplayForceReminder()
{
	if($('#reminder_table tr').length <= 1)
	{
		$('#force_reminder_on').closest("div.form-group").hide();
	}
	else
	{
		$('#force_reminder_on').closest("div.form-group").fadeIn();
	}
}
function addConditionGroup()
{
	$('#condition_group_table').show();
	condition_groups_counter += 1;
	condition_counters[condition_groups_counter] = 0;
	$.get(
		tamodule_url+'&ajax_action=add_condition_group',
		{newGroupCondition:1,condition_group_id:condition_groups_counter},
		function(content) {
			if (content != "")
				$('#condition_group_table').append(content);
		}
	);
}
function addReminder()
{
	$('.reminder_delete').remove();
	$('#reminder_table').show();
	reminders_counter += 1;
	$.get(
		tamodule_url+'&ajax_action=add_reminder',
		{newReminder:1,reminder_position:reminders_counter},
		function(content) {
			if (content != "")
				$('#reminder_table').append(content);
			triggerDisplayForceReminder();
		}
	);
	
}
$(document).on('click','.radio-manual-process',function(){
	var pos = $(this).data('posReminder');
	if($(this).filter(':checked').val()==1)
	{
		$('#reminder_'+pos+'_id_mail_template').hide();
		$('#reminder_'+pos+'_admin_mails').show();
	}
	else
	{
		$('#reminder_'+pos+'_admin_mails').hide();
		$('#reminder_'+pos+'_id_mail_template').show();
	}
});
$(document).on('click','.unselected-search-button',function(){
	var url = $(this).data('action-url');
	var condition_type = $(this).data('condition-type');
	var search = $(this).parent().parent().find('.unselected-search-text').val();
	var unselected = $(this).parents('.ta-condition-list:first').find('.unselected-list');
	var selected = $(this).parents('.ta-condition-list:first').find('.condition_toselect');
	unselected.find('option').remove();
	$.ajax({
	      type: 'POST',
	      url:url,
	      cache: false,
	      data: 'condition_type='+condition_type+'&search='+search,
	      dataType: 'json',
	      success: function (data) {
	    	  $.each(data, function(i, item) {
	    		  var present = false;
	    		  selected.find("option").each(function()
	    		  {
	    			  if ($(this).val() == data[i].id)
	    			  {
	    				  present=true;
	    			  }
	    		  });
	    		  if (!present)
	    		  {
	    			  unselected.append('<option value="'+data[i].id+'">'+data[i].id+' '+ data[i].name + ' ' + data[i].reference + '</option>');
	    		  }
	    	  });
	    	  
	      }
	  });
});
$(document).on('click','.check_reminder_to_delete',function(){
	$form = $('#ta_cartreminder_rule_form');
	var id_reminder = $(this).data('id-reminder');
	var pos_reminder = $(this).data('pos-reminder');
	$.ajax({
	      type: "POST",
	      url:$form.attr('action'),
	      cache: false,
	      data: 'check_reminder_befor_delete=1' + '&id_reminder=' + id_reminder,
	      dataType: 'json',
	      success: function (data) {
	    	  if(data.has_error)
	    	  {
	    		  $.fancybox.open({
		    	        content   : data.page_content,
		    	        autoSize: true, // 
		    	        closeClick: false,
		    	        openEffect: 'elastic',
		    	        closeEffect: 'fade',
		    	      });
		    	  $('#reminder_remove_'+id_reminder).click(function(){
		    		  removeReminder(pos_reminder);
		    		  $.fancybox.close()
		    	  });
	    	  }
	    	  else
	    		  removeReminder(pos_reminder);
	      }
	  });
});
$(".ta-modal-tree input[name^=condition_select_]").on( "click", function() {
 		updateConditionShortDescriptionForTree($(this));
});
	


function removeReminder(pos)
{
	$('#reminder_' + pos + '_tr').remove();
	if(pos > 1)
	{
		lastpos = pos - 1;
		var id_reminder = $('#reminder_' + lastpos + '_tr').data('id-reminder');
		htmladelete = '<a class="btn btn-default check_reminder_to_delete" href="javascript:;" data-pos-reminder="'+lastpos+'" data-id-reminder="'+id_reminder+'" >'+
				'<i class="flaticon-cancel6"></i>'+
				'</a>';
		$('#reminder_' + (pos - 1) + '_tr td.action_delete').html(htmladelete);
	}
	reminders_counter -= 1;
	triggerDisplayForceReminder();
	
}
function removeConditionGroup(id)
{
	$('#condition_group_' + id + '_tr').remove();
}

function addCondition(condition_group_id)
{
	condition_counters[condition_group_id] += 1;
	if ($('#condition_type_' + condition_group_id).val() != 0)
		$.get(
			tamodule_url+'&ajax_action=add_condition',
			{newCondition:1,condition_type:$('#condition_type_' + condition_group_id).val(),condition_group_id:condition_group_id,condition_id:condition_counters[condition_group_id]},
			function(content) {
				if (content != "")
					$('#condition_table_' + condition_group_id).append(content);
			}
		);
}

function removeCondition(condition_group_id, condition_id)
{
	$('#condition_' + condition_group_id + '_' + condition_id + '_tr').remove();
}


function updateConditionShortDescription(item)
{
	/******* For IE: put a product in condition on cart rules *******/
	if(typeof String.prototype.trim !== 'function') {
	  String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, ''); 
	  }
	}

	var id1 = $(item).attr('id').replace('_add', '').replace('_remove', '');
	var id2 = id1.replace('_select', '');
	var length = $('#' + id1 + '_2 option').length;
	if (length == 1)
		$('#' + id2 + '_match').val($('#' + id1 + '_2 option').first().text().trim());
	else
		$('#' + id2 + '_match').val(length);
}
function updateConditionShortDescriptionForTree(item)
{
	/******* For IE: put a product in condition on cart rules *******/
	if(typeof String.prototype.trim !== 'function') {
	  String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, ''); 
	  }
	}
	var match_id = $(item).attr('name').replace('_select', '').replace('[]', '') + '_match';
	var nb = $("input[name^="+$(item).attr("name").replace('[]', '')+"]:checked").length;
	$('#' + match_id).val(nb);
}

function removeConditionOption(item)
{
	var id = $(item).attr('id').replace('_remove', '');
	$('#' + id + '_2 option:selected').remove().appendTo('#' + id + '_1');
}

function addConditionOption(item)
{
	var id = $(item).attr('id').replace('_add', '');
	$('#' + id + '_1 option:selected').remove().appendTo('#' + id + '_2');
}