{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author     PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{literal}
<script type="text/javascript">
    var genericErrorMessage = "{/literal}{l s='An error occurred while displaying order details.' mod='colissimo'}{literal}";
    var genericEmailErrorMessage = "{/literal}{l s='An error occurred while sending mail.' mod='colissimo'}{literal}";
    var genericDeleteErrorMessage = "{/literal}{l s='An error occurred while deleting label.' mod='colissimo'}{literal}";
    var channel = '{/literal}{if isset($colissimo_channel)}{$colissimo_channel|escape:'htmlall':'UTF-8'}{/if}{literal}';
    var tokenDashboard = '{/literal}{getAdminToken tab='AdminColissimoDashboard'}{literal}';
    var tokenLabel = '{/literal}{getAdminToken tab='AdminColissimoLabel'}{literal}';
    var tokenAffranchissement = '{/literal}{getAdminToken tab='AdminColissimoAffranchissement'}{literal}';
    var colissimoBlock = $('#view_colissimo_block, #form-colissimo_dashboard');

    colissimoBlock
        .off('click', '.js-colissimo-show-hidden-order')
        .on('click', '.js-colissimo-show-hidden-order', function (e) {
            e.preventDefault();

            var idColissimoOrder = $(this).attr('data-colissimo-order-id');
            var btn = $(this);

            btn.find('i').attr('class', 'icon icon-spin icon-spinner');
            btn.addClass('disabled');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: baseAdminDir + 'index.php',
                data: {
                    controller: 'AdminColissimoDashboard',
                    ajax: 1,
                    token: tokenDashboard,
                    action: 'updatePostageVisibility',
                    id_colissimo_order: parseInt(idColissimoOrder),
                    channel: channel
                }
            }).fail(function (jqXHR, textStatus) {
                showErrorMessage(genericErrorMessage);
            }).done(function (data) {
                btn.remove();
                if (data.errors) {
                    showErrorMessage(data.message);
                } else {
                    showSuccessMessage(data.message);
                }
            });
        });

    colissimoBlock
        .off('click', '.colissimo-update-order-tracking button')
        .on('click', '.colissimo-update-order-tracking button', function (e) {
            e.preventDefault();

            var idColissimoOrder = $(this).attr('data-colissimo-order-id');
            var btn = $(this);
            var shipmentsTable = $('.colissimo-shipments-' + parseInt(idColissimoOrder));

            btn.find('i').attr('class', 'icon icon-spin icon-spinner');
            btn.addClass('disabled');
            shipmentsTable.fadeTo('fast', 0.4);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: baseAdminDir + 'index.php',
                data: {
                    controller: 'AdminColissimoDashboard',
                    ajax: 1,
                    token: tokenDashboard,
                    action: 'updateOrderTracking',
                    newTheme: newTheme,
                    id_colissimo_order: parseInt(idColissimoOrder),
                    channel: channel
                }
            }).fail(function (jqXHR, textStatus) {
                showErrorMessage(genericErrorMessage);
            }).done(function (data) {
                if (data.errors.length) {
                    for (var i = 0; i < data.errors.length; i++) {
                        showErrorMessage(data.errors[i]);
                    }
                }
                if (data.success.length) {
                    for (i = 0; i < data.success.length; i++) {
                        showSuccessMessage(data.success[i]);
                    }
                }
                shipmentsTable.html(data.html);
            }).always(function (data) {
                btn.find('i').attr('class', 'icon icon-refresh');
                btn.removeClass('disabled');
                shipmentsTable.fadeTo('fast', 1);
            });
        });

    colissimoBlock
        .off('click', 'a.colissimo-delete-label')
        .on('click', 'a.colissimo-delete-label', function (e) {
            e.preventDefault();

            var idColissimoLabel = $(this).attr('data-colissimo-label-id');
            var idColissimoOrder = $(this).closest('.colissimo-hook-admin-order').find('.colissimo-update-order-tracking button').attr('data-colissimo-order-id');
            var className = '.colissimo-modal-delete-label-' + idColissimoOrder;
            $(className + ' #colissimo-shipment-number').html($(this).attr('data-colissimo-label-number'));
            $(className + ' input[name="colissimo-modal-label-id"]').val(idColissimoLabel);
            $(className + '.colissimo-modal-delete-label').modal('show');
        });

    colissimoBlock
        .off('click', '.colissimo-modal-confirm-mail-label')
        .on('click', '.colissimo-modal-confirm-mail-label', function (e) {
            e.preventDefault();
            var sendMail = 1;
            var idColissimoOrder = $(this).closest('.colissimo-modal-mail-label').find('input[name="colissimo-modal-order-id"]').val();
            var idColissimoLabel = $(this).closest('.colissimo-modal-mail-label').find('input[name="colissimo-modal-label-id"]').val();
            generateReturnLabel(sendMail, idColissimoOrder, idColissimoLabel);
        });

    colissimoBlock
        .off('click', '.colissimo-modal-no-confirm-mail-label')
        .on('click', '.colissimo-modal-no-confirm-mail-label', function (e) {
            e.preventDefault();
            var sendMail = 0;
            var idColissimoOrder = $(this).closest('.colissimo-modal-mail-label').find('input[name="colissimo-modal-order-id"]').val();
            var idColissimoLabel = $(this).closest('.colissimo-modal-mail-label').find('input[name="colissimo-modal-label-id"]').val();
            generateReturnLabel(sendMail, idColissimoOrder, idColissimoLabel);
        });

    colissimoBlock
        .off('click', '.colissimo-mail-return-label')
        .on('click', '.colissimo-mail-return-label', function (e) {
            e.preventDefault();

            var idColissimoReturnLabel = $(this).attr('data-colissimo-return-label-id');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: baseAdminDir + 'index.php',
                data: {
                    controller: 'AdminColissimoLabel',
                    ajax: 1,
                    token: tokenLabel,
                    action: 'mailReturnLabel',
                    id_colissimo_return_label: parseInt(idColissimoReturnLabel),
                    channel: channel
                }
            }).fail(function (jqXHR, textStatus) {
                showErrorMessage(genericEmailErrorMessage);
            }).done(function (data) {
                if (!data.error) {
                    showSuccessMessage(data.message);
                } else {
                    showErrorMessage(data.message);
                }
            });
        });

    colissimoBlock
        .off('click', '.colissimo-generate-return-label')
        .on('click', '.colissimo-generate-return-label', function (e) {
            e.preventDefault();

            var idColissimoLabel = parseInt($(this).attr('data-colissimo-label-id'));
            var idColissimoOrder = $(this).closest('.colissimo-hook-admin-order').find('.colissimo-update-order-tracking button').attr('data-colissimo-order-id');
            var className = '.colissimo-modal-mail-label-' + idColissimoOrder;
            $(className + ' input[name="colissimo-modal-label-id"]').val(idColissimoLabel);
            $(className + '.colissimo-modal-mail-label').modal('show');
        });

    colissimoBlock
        .off('click', '.colissimo-modal-confirm-delete-label')
        .on('click', '.colissimo-modal-confirm-delete-label', function (e) {
            e.preventDefault();

            var idColissimoOrder = $(this).closest('.colissimo-modal-delete-label').find('input[name="colissimo-modal-order-id"]').val();
            var idColissimoLabel = $(this).closest('.colissimo-modal-delete-label').find('input[name="colissimo-modal-label-id"]').val();
            var shipmentsTable = $('.colissimo-shipments-' + parseInt(idColissimoOrder));


            $('.colissimo-modal-delete-label').modal('hide');
            shipmentsTable.fadeTo('fast', 0.4);
            shipmentsTable.css('pointer-events', 'none');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: baseAdminDir + 'index.php',
                data: {
                    controller: 'AdminColissimoLabel',
                    ajax: 1,
                    token: tokenLabel,
                    action: 'deleteLabel',
                    newTheme: newTheme,
                    id_colissimo_label: parseInt(idColissimoLabel),
                    channel: channel
                }
            }).fail(function (jqXHR, textStatus) {
                showErrorMessage(genericDeleteErrorMessage);
            }).done(function (data) {
                if (data === null) {
                    showErrorMessage(genericDeleteErrorMessage);
                    return;
                }
                if (!data.error) {
                    showSuccessMessage(data.message);
                } else {
                    showErrorMessage(data.message);
                }
                if (data.html) {
                    shipmentsTable.html(data.html);
                }
            }).always(function (data) {
                shipmentsTable.fadeTo('fast', 1);
                shipmentsTable.css('pointer-events', 'auto');
            });
        });

    colissimoBlock
        .off('click', '.colissimo-service-selection')
        .on('click', '.colissimo-service-selection', function (e) {
            e.preventDefault();

            var idOrder = $(this).attr('data-id-order');

            $(this).find('i').toggleClass('icon-spin icon-spinner');
            $(this).toggleClass('disabled');
            loadColissimoServiceModalUpdate(idOrder);
        });

    function generateReturnLabel(sendMail, idColissimoOrder, idColissimoLabel) {
        var btn = $('.colissimo-generate-return-label[data-colissimo-label-id=' + idColissimoLabel + ']');
        var shipmentsTable = $('.colissimo-shipments-' + idColissimoOrder);
        var tokenLabel = '{/literal}{getAdminToken tab='AdminColissimoLabel'}{literal}';

        $('.colissimo-modal-mail-label-' + parseInt(idColissimoOrder)).modal('hide');
        btn.find('i').attr('class', 'icon icon-spin icon-spinner');
        btn.addClass('disabled');
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: baseAdminDir + 'index.php',
            data: {
                controller: 'AdminColissimoLabel',
                ajax: 1,
                token: tokenLabel,
                action: 'generateReturn',
                newTheme: newTheme,
                id_colissimo_label: parseInt(idColissimoLabel),
                send_mail: sendMail,
                channel: channel
            }
        }).fail(function (jqXHR, textStatus) {
            showErrorMessage(genericErrorMessage);
        }).done(function (data) {
            if (!data.error) {
                showSuccessMessage(data.message);
            } else {
                showErrorMessage(data.message);
            }
            if (data.warning_message !== undefined && data.warning_message != false) {
                showNoticeMessage(data.warning_message);
            }
            shipmentsTable.html(data.html);
        }).always(function (data) {
            btn.find('i').attr('class', 'icon icon-refresh');
            btn.removeClass('disabled');
        });
    }
</script>
{/literal}
