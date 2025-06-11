/**
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from SARL DREAM ME UP
* Use, copy, modification or distribution of this source file without written
* license agreement from the SARL DREAM ME UP is strictly forbidden.
*
*   .--.
*   |   |.--..-. .--, .--.--.   .--.--. .-.   .  . .,-.
*   |   ;|  (.-'(   | |  |  |   |  |  |(.-'   |  | |   )
*   '--' '   `--'`-'`-'  '  `-  '  '  `-`--'  `--`-|`-'
*        w w w . d r e a m - m e - u p . f r       '
*
*  @author    Dream me up <prestashop@dream-me-up.fr>
*  @copyright 2007 - 2023 Dream me up
*  @license   All Rights Reserved
*/

$(document).ready(function() {

	if ($('table.dmu_vues_commandes').length) // Module config
	{
		if (dlc_oldversion)
			$('table.dmu_vues_commandes tbody td.pointer').each(function() {
				if (!$(this).attr('onclick'))
					$(this).addClass('dragHandle');
			});

		$('table.dmu_vues_commandes tbody tr td.dragHandle').each(function() {
			position = parseInt($(this).text());
			$(this).html('<div class="dragGroup"><div class="positions">'+position+'</div></div>');
		});

		$('table.dmu_vues_commandes').tableDnD({
			dragHandle: 'dragHandle',
			onDragClass: 'myDragClass',
			onDrop: function(table, row) {
				$('table.dmu_vues_commandes').css('opacity', '.5');
				id_vue_list = [];
				if (dlc_oldversion)
					$('table.dmu_vues_commandes tbody tr').each(function() {
						id_vue_list.push(parseInt($(this).find('td.pointer').first().text()));
					});
				else
					$('table.dmu_vues_commandes tbody td.id_vue').each(function() {
						id_vue_list.push(parseInt($(this).text()));
					});
				$.ajax({
					type: 'POST',
					url: document.location,
					async: true,
					cache: false,
					dataType : 'json',
					data: 'ajax=1&action=movePositions&id_vue_list='+id_vue_list.join(','),
					success: function(json)
					{
						if (json.success)
						{
							position = 1;
							$('table.dmu_vues_commandes tbody td.dragHandle').each(function() {
								$(this).html('<div class="dragGroup"><div class="positions">'+(position++)+'</div></div>');
							});
							$('table.dmu_vues_commandes').css('opacity', '1');
						}
					}
				});
			}
		});
	}
	
	if ($('table.order').length) // Admin controller
	{
		// transformation des URLS vers AdminOrders
		if (_PS_VERSION_ < '1.7.7') {
			$('table.order tbody td').addClass('changeToOrderUrl');
			$('table.order tbody div.btn-group a.btn').addClass('changeToOrderUrl');
			$('#desc-order-new').addClass('changeToOrderUrl');
			$('.changeToOrderUrl').each(function() {
				href = $(this).attr('href');
				on_click = $(this).attr('onclick');

				if (href) {
					href = href.replace('AdminDmuListeCommandes', 'AdminOrders');
					href = href.replace(/token=[0-9a-zA-Z]*/, 'token='+adminOrders_token);
					$(this).attr('href', href);
				}
				if (on_click) {
					on_click = on_click.replace('AdminDmuListeCommandes', 'AdminOrders');
					on_click = on_click.replace(/token=[0-9a-zA-Z]*/, 'token='+adminOrders_token);
					$(this).attr('onclick', on_click);
				}
			});
			$('.changeToOrderUrl').removeClass('changeToOrderUrl');
		}
		

		// modification des fonctions Javascript admin de base
		$('#form-order div.bulk-actions li a').each(function() {
			on_click = $(this).attr('onclick');
			if (on_click) {
				on_click = on_click.replace(/sendBulkAction/, 'dlc_sendBulkAction');
				$(this).attr('onclick', on_click);
			}
		});
		
		// modification du clic "Transporteur"
		$('td.order_carrier').attr('onclick', '').click(function() {
			tracking_link = $(this).find('.tracking_link').attr('rel');
            if (tracking_link) {
                if (tracking_link != 'nourl')
                    window.open(tracking_link);
            } else {
                tracking_number = prompt(dlc_tracking_txt);
                if (tracking_number) {
                    id_order = $(this).find('.carrier_label').attr('rel');
                    $.ajax({
                        type: 'POST',
                        url: document.location,
                        async: true,
                        cache: false,
                        dataType : "json",
                        data: 'ajax=1&action=setTrackingNumber&id_order='+id_order+'&tracking_number='+tracking_number,
                        success: function(json)
                        {
                            if (json.success) {
                                $('.carrier_label_'+json.id_order).parent().html(json.html);
                            }
                        }
                    });
                }
            }
		});

		// modification du clic "Client"
		$('td.order_customer').attr('onclick', '').click(function() {
			customer_link = $(this).find('.customer_link').attr('rel');
            if (customer_link)
                window.open(customer_link);
		});
		
		$('.dlc_tooltop').on('click', function(event)
		{
			event.preventDefault();
			return false;
		});
        
        // submit automatique pour la selection Nouveau client
        $('select[name=orderFilter_new]').change(function() {
            if ($(this).val().length < 1)
                $('#orderFilter_customer').val(function() {
                    return $(this).val() + ' ';
                });
            $('#submitFilterButtonorder').trigger('click');
        });

		// KPI - Ajout de la Sélection de vue
		if ($('#kpi_listecommandes').length)
		{
			html = '<select onChange="document.location=\'index.php?controller=AdminDmuListeCommandes&id_vue=\'+this.value+\'&token=\'+adminDmuListeCommandes_token">';
			html += '<option value="0">-- '+dlc_views_list_txt+' --</option>';
			for (i = 0; i < dlc_views_list.length; i++)
			{
				detail = dlc_views_list[i].split('¤¤');
				html += '<option value="'+detail[0]+'">'+detail[1]+'</option>';
			}
			html += '</select>';
			$('#kpi_listecommandes span.value').html(html);
		}

		// Suppresion des Action Buttons (si demandé)
		if (!dlc_show_buttons)
		{
			$('table.order tr td:last-child').hide();
			$('table.order tr th:last-child').hide();
		}

		// colorisation des lignes entières (si demandé)
		if (dlc_status_on_line)
		{
			$('table.order tbody tr').each(function() {
				tr = $(this);
				background = tr.find('.color_field').css('background-color');
				color = tr.find('.color_field').css('color');
				tr.find('td').each(function() {
					$(this).css('background-color', background);
					$(this).css('color', color);
				});
			});
		}

		// Création d'un décalage en bas de page
		// pour éviter la superposition avec le Batch panel
		$('#dlc_bulk_panel_blank').height($('#dlc_bulk_panel').height() + 10);
		// Décalage automatique pour voir la fleche de retour en Haut
		$(window).scroll(function() {
			if ($('#footer #go-top').length)
				setTimeout(dlc_setBulkPanelPosition, 200);
		});
		// Animation de départ
		$('#dlc_bulk_panel').css('right', $(window).width() + 'px').animate({ right: '4px' });

		// Actions groupées
		$('#dlc_bulk_select_status').change(function() {
			status = $(this).val();
			if (status != 0)
			{
				nb_lines = $('table.order input:checked').length;
				if ((nb_lines>0) && (confirm(unescape(dlc_warning_txt+'%0A'+dlc_status_change_txt.replace('%d', nb_lines)))))
				{
					dlc_sendBulkAction(dlc_oldversion == true ? $('form.form') : ($('#form-order').length ? $('#form-order') : $('form#order')), 'submitBulkupdateOrderStatusorder&status='+status);
				}
			}
			$(this).val(0);
		});
		$('#dlc_bulk_select_printing').change(function() {
			printing = $(this).val();
			if (printing != 0)
				dlc_sendBulkAction(dlc_oldversion == true ? $('form.form') : ($('#form-order').length ? $('#form-order') : $('form#order')), 'submitBulkgenerate'+printing+'order');
			$(this).val(0);
		});
        
        // Décochage des lignes
        $('input.noborder').attr('checked', false);
        
        // Ajout du filtre style « range » pour les codes postaux
        html = '<div class="row date_range"><div class="input-group fixed-width-md center">'+
                '<input type="text" class="filter" name="orderFilter_postcode_from" value="'+dlc_postcode_from+'" placeholder="'+dlc_from_txt+'" />'+
            '</div><div class="input-group fixed-width-md center">'+
                '<input type="text" class="filter" name="orderFilter_postcode_to" value="'+dlc_postcode_to+'" placeholder="'+dlc_to_txt+'" />'+
            '</div></div>';
        $('input[name=orderFilter_postcode]').parent().html(html).find('input').css('border-radius', '3px').focus(function() {
            $(this).select();
        });;
	}

});

function dlc_setBulkPanelPosition()
{
	$('#dlc_bulk_panel').css('right', '4px');
	if (!$('#footer').hasClass('hide'))
		$('#dlc_bulk_panel').css('right', '50px');
}

function dlc_sendBulkAction(form, action)
{
	String.prototype.splice = function(index, remove, string) {
		return (this.slice(0, index) + string + this.slice(index + Math.abs(remove)));
	};

	var form_action = $(form).attr('action');

	if (!form_action || form_action.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ') == '')
		return false;

	if (form_action.indexOf('#') == -1)	$(form).attr('action', form_action + '&' + action);
	else								$(form).attr('action', form_action.splice(form_action.lastIndexOf('&'), 0, '&' + action));

	$(form).submit().attr('action', form_action);
}
