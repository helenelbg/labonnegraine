$(document).ready(function()
{
    if(!$('.aw_mailsmailjet_lang_id').val())
    {
        $('.aw_mailsmailjet_template_mailjet_section').hide();
        $('.aw_mailsmailjet_template_prestashop_section').hide();
    }
    else
    {
        getLinkedTemplateList();
    }
    
});

$(document).on('change', '.aw_mailsmailjet_lang_id', function()
{
    getLinkedTemplateList();
});

function aw_mailsmailjet_linked_template_lang_id()
{
    lang_id = $(".aw_mailsmailjet_linked_template_lang_id option:selected" ).val();
    $('.linked_template_list').empty();

    $('.linked_template_list_section').show();
    
    $.ajax({
		type: "POST",
		url: '/modules/aw_mailsmailjet/controllers/get_linked_template_list.php',
		data: {
            token : '5g49e84g68r4gegujherge564er8gujhghgiu',
			lang_id : lang_id
		},
		cache: false,
		success: function(response) {
			value = $.parseJSON(response);
            $('.linked_template_list').append('<table class="table_linked_template_list">'+
                                                '<thead>'+
                                                    '<tr>'+
                                                        '<th colspan="1">Selectionner</th>'+
                                                        '<th colspan="1">Mailjet</th>'+
                                                        '<th colspan="1">&</th>'+
                                                        '<th colspan="1">Prestashop</th>'+
                                                    '</tr>'+
                                                '</thead>'+
                                                    '<tbody class="table_body_linked_template_list">');
                                                    for(var i=0; i<value.length; i++)
                                                    {
                                                        $('.aw_mailsmailjet_template_prestashop_id option[value="'+value[i]['aw_mails_mailjet_prestashop_template']+'"]').hide();
                                                    
                                                        $('.table_body_linked_template_list').append('<tr>'+
                                                                                                    '<td><input type="checkbox" id="template_'+value[i]['aw_mails_mailjet_id']+'" name="AW_MAILSMAILJET_DELETE_LINKED_MAILS_ID[]" value="'+value[i]['aw_mails_mailjet_id']+'"></td>'+
                                                                                                    '<td>'+value[i]['aw_mails_mailjet_template_name']+'</td>'+
                                                                                                    '<td><=></td>'+
                                                                                                    '<td>'+value[i]['aw_mails_mailjet_prestashop_template']+'</td>'+
                                                                                                '</tr>')
                                                    }
        },
		error: function (e) {
			console.log("ERROR : ", e);
        }
   });
}

$(document).on('change', '.aw_mailsmailjet_linked_template_lang_id', function()
{
    
    aw_mailsmailjet_linked_template_lang_id();
});

function getLinkedTemplateList()
{
    lang_id = $(".aw_mailsmailjet_lang_id option:selected" ).val();

    $('.aw_mailsmailjet_template_mailjet_section').show();
    $('.aw_mailsmailjet_template_prestashop_section').show();

    $(".aw_mailsmailjet_template_prestashop_id option").each(function(index)
    {
        $(this).show();
    });

    $.ajax({
		type: "POST",
		url: '/modules/aw_mailsmailjet/controllers/get_linked_template_list.php',
		data: {
            token : '5g49e84g68r4gegujherge564er8gujhghgiu',
			lang_id : lang_id
		},
		cache: false,
		success: function(response) {
			value = $.parseJSON(response);
            for(var i=0; i<value.length; i++)
            {
                $('.aw_mailsmailjet_template_prestashop_id option[value="'+value[i]['aw_mails_mailjet_prestashop_template']+'"]').hide();
            }
        },
		error: function (e) {
			console.log("ERROR : ", e);
        }
   });
}