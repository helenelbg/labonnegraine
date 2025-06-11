/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from Common-Services Co., Ltd.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SARL SMC is strictly forbidden.
 * In order to obtain a license, please contact us: contact@common-services.com
 * ...........................................................................
 * INFORMATION SUR LA LICENCE D'UTILISATION
 *
 * L'utilisation de ce fichier source est soumise a une licence commerciale
 * concedee par la societe Common-Services Co., Ltd.
 * Toute utilisation, reproduction, modification ou distribution du present
 * fichier source sans contrat de licence ecrit de la part de la Common-Services Co. Ltd. est
 * expressement interdite.
 * Pour obtenir une licence, veuillez contacter Common-Services Co., Ltd. a l'adresse: contact@common-services.com
 * ...........................................................................
 *
 * @package   SoNice_Etiquetage
 * @author    Alexandre D. <alexandre@common-services.com>
 * @copyright Copyright (c) 2011-2015 Common Services Co Ltd - 90/25 Sukhumvit 81 - 10260 Bangkok - Thailand
 * @license   Commercial license
 * Support by mail  :  support.sonice@common-services.com
 */

/**
 * Plug, the plugins library.
 *
 * Easily create HTML advanced elements like dialog box, slider, ...
 *
 * @author debuss-a
 * @version 1.0.0
 */
var plug = (function () {

        /**
         * @lends plug
         */
        var p = {

            fn: {

                version: '1.0.0',

                overlay: function () {
                    var overlay = document.createElement('div');
                    overlay.style.backgroundColor = 'black';
                    overlay.style.opacity = '0.75';
                    overlay.style.mozOpacity = '0.75';
                    overlay.style.filter = 'alpha(opacity=75)';
                    overlay.style.position = 'fixed';
                    overlay.style.top = 0;
                    overlay.style.bottom = 0;
                    overlay.style.left = 0;
                    overlay.style.right = 0;
                    overlay.style.zIndex = 6000;
                    document.body.appendChild(overlay);
                    return overlay;
                },

                alert_modal: function () {
                    var alert_modal = document.createElement('div');
                    alert_modal.style.width = '50%';
                    alert_modal.style.position = 'absolute';
                    alert_modal.style.left = '50%';
                    alert_modal.style.top = '100px';
                    alert_modal.style.transform = 'translate(-50%)';
                    alert_modal.style.zIndex = 6001;
                    alert_modal.style.border = 'solid 1px #d3d8db';
                    alert_modal.style.backgroundColor = '#fff';
                    alert_modal.style.borderRadius = '5px';
                    alert_modal.className += ' bootstrap';
                    document.body.appendChild(alert_modal);
                    return alert_modal;
                }

            },

            /**
             * Display an alert dialog box.
             *
             * @param {string} message The message to display in the dialog box
             */
            alert: function (message) {
                message = message || '';

                var overlay = this.fn.overlay();
                var alert_modal = this.fn.alert_modal();
                var alert_modal_content = document.createElement('div');
                var alert_modal_footer = document.createElement('div');
                var alert_modal_footer_ok = document.createElement('button');
                var alert_modal_footer_clearfix = document.createElement('div');

                // Overlay
                overlay.addEventListener('click', function () {
                    this.parentNode.removeChild(this);
                    alert_modal.parentNode.removeChild(alert_modal);
                });

                // Modal content
                alert_modal_content.style.padding = '20px';
                alert_modal_content.style.borderBottom = '1px solid gainsboro';
                alert_modal_content.innerHTML = message;
                alert_modal.appendChild(alert_modal_content);

                // Modal footer
                alert_modal_footer.style.padding = '20px';
                alert_modal.appendChild(alert_modal_footer);

                // Modal footer button
                alert_modal_footer_ok.className += ' button btn btn-primary';
                alert_modal_footer_ok.style.float = 'right';
                alert_modal_footer_ok.innerText = 'OK';
                alert_modal_footer_clearfix.className += ' clearfix';
                alert_modal_footer_clearfix.innerHTML = '&nbsp;';
                alert_modal_footer.appendChild(alert_modal_footer_ok);
                alert_modal_footer.appendChild(alert_modal_footer_clearfix);
                alert_modal.appendChild(alert_modal_footer);

                // Event
                alert_modal_footer_ok.addEventListener('click', function () {
                    overlay.parentNode.removeChild(overlay);
                    alert_modal.parentNode.removeChild(alert_modal);
                });
                document.addEventListener('keydown', function(e) {
                    e.keyCode === 27 && (
                        this.removeEventListener('keydown', arguments.callee),
                        overlay.parentNode.removeChild(overlay),
                        alert_modal.parentNode.removeChild(alert_modal)
                    );
                });


                alert_modal_footer_ok.focus();
            },

            /**
             * Display a confirm dialog box.
             *
             * @param {string} title Title of the dialog box, default is "Attention" (Can be HTML code)
             * @param {string} content Content of the dialog box (Can be HTML code)
             * @param {function} on_accept Callback if user accepts
             * @param {function} on_cancel Callback if user denies
             */
            confirm: function (title, content, on_accept, on_cancel) {
                title = title || 'Attention !';
                content = content || '';

                var overlay = this.fn.overlay();
                var alert_modal = this.fn.alert_modal();
                var alert_modal_header = document.createElement('div');
                var alert_modal_content = document.createElement('div');
                var alert_modal_footer = document.createElement('div');
                var alert_modal_footer_cancel = document.createElement('button');
                var alert_modal_footer_ok = document.createElement('button');

                // Overlay
                overlay.addEventListener('click', function () {
                    this.parentNode.removeChild(overlay);
                    alert_modal.parentNode.removeChild(alert_modal);
                });

                // Modal title
                alert_modal_header.style.padding = '20px';
                alert_modal_header.style.fontSize = '16px';
                alert_modal_header.style.fontWeight = 'bold';
                alert_modal_header.style.borderBottom = '1px solid gainsboro';
                alert_modal_header.innerHTML = title;
                alert_modal.appendChild(alert_modal_header);

                // Modal content
                alert_modal_content.style.padding = '20px';
                alert_modal_content.style.borderBottom = '1px solid gainsboro';
                alert_modal_content.innerHTML = content;
                alert_modal.appendChild(alert_modal_content);

                // Modal footer
                alert_modal_footer.style.padding = '20px';
                alert_modal.appendChild(alert_modal_footer);

                // Modal footer button
                alert_modal_footer_cancel.className += ' button btn btn-danger';
                alert_modal_footer_cancel.innerText = 'Cancel';
                alert_modal_footer_ok.className += ' button btn btn-primary';
                alert_modal_footer_ok.style.float = 'right';
                alert_modal_footer_ok.innerText = 'OK';
                alert_modal_footer.appendChild(alert_modal_footer_cancel);
                alert_modal_footer.appendChild(alert_modal_footer_ok);
                alert_modal.appendChild(alert_modal_footer);

                // Events
                alert_modal_footer_cancel.addEventListener('click', function () {
                    overlay.parentNode.removeChild(overlay);
                    alert_modal.parentNode.removeChild(alert_modal);
                    typeof(on_cancel) === 'function' && on_cancel();
                });
                alert_modal_footer_ok.addEventListener('click', function () {
                    overlay.parentNode.removeChild(overlay);
                    alert_modal.parentNode.removeChild(alert_modal);
                    typeof(on_accept) === 'function' && on_accept();
                });
                document.addEventListener('keydown', function(e) {
                    e.keyCode === 27 && (
                        this.removeEventListener('keydown', arguments.callee),
                        overlay.parentNode.removeChild(overlay),
                        alert_modal.parentNode.removeChild(alert_modal),
                        typeof(on_cancel) === 'function' && on_cancel()
                    );
                });

                alert_modal_footer_ok.focus();
            },

            /**
             * Display a prompt dialog box.
             *
             * @param {string} message The message to display in the dialog box
             * @param {string} default_value The default value
             * @param {function} on_accept Callback if user accepts
             * @param {function} on_cancel Callback if user denies
             */
            prompt: function(message, default_value, on_accept, on_cancel) {
                message = message || '';
                default_value = default_value || '';

                var overlay = this.fn.overlay();
                var alert_modal = this.fn.alert_modal();
                var alert_modal_content = document.createElement('div');
                var alert_modal_content_input = document.createElement('input');
                var alert_modal_footer = document.createElement('div');
                var alert_modal_footer_cancel = document.createElement('button');
                var alert_modal_footer_ok = document.createElement('button');

                // Overlay
                overlay.addEventListener('click', function () {
                    this.parentNode.removeChild(overlay);
                    alert_modal.parentNode.removeChild(alert_modal);
                });

                // Modal content
                alert_modal_content.style.padding = '20px';
                alert_modal_content.style.borderBottom = '1px solid gainsboro';
                alert_modal_content.innerHTML = message + '<br>';
                alert_modal_content_input.style.width = '100%';
                alert_modal_content_input.style.padding = '5px';
                alert_modal_content_input.value = default_value;
                alert_modal_content.appendChild(alert_modal_content_input);
                alert_modal.appendChild(alert_modal_content);

                // Modal footer
                alert_modal_footer.style.padding = '20px';
                alert_modal.appendChild(alert_modal_footer);

                // Modal footer button
                alert_modal_footer_cancel.className += ' button btn btn-danger';
                alert_modal_footer_cancel.innerText = 'Cancel';
                alert_modal_footer_ok.className += ' button btn btn-primary';
                alert_modal_footer_ok.style.float = 'right';
                alert_modal_footer_ok.innerText = 'OK';
                alert_modal_footer.appendChild(alert_modal_footer_cancel);
                alert_modal_footer.appendChild(alert_modal_footer_ok);
                alert_modal.appendChild(alert_modal_footer);

                // Events
                alert_modal_footer_cancel.addEventListener('click', function () {
                    overlay.parentNode.removeChild(overlay);
                    alert_modal.parentNode.removeChild(alert_modal);
                    typeof(on_cancel) === 'function' && on_cancel();
                });
                alert_modal_footer_ok.addEventListener('click', function () {
                    var entered_value = alert_modal.querySelector('input').value;
                    overlay.parentNode.removeChild(overlay);
                    alert_modal.parentNode.removeChild(alert_modal);
                    typeof(on_accept) === 'function' && on_accept(entered_value);
                });
                document.addEventListener('keydown', function(e) {
                    e.keyCode === 27 && (
                        this.removeEventListener('keydown', arguments.callee),
                            overlay.parentNode.removeChild(overlay),
                            alert_modal.parentNode.removeChild(alert_modal),
                        typeof(on_cancel) === 'function' && on_cancel()
                    );
                });

                alert_modal_footer_ok.focus();
            },

            switch: function(elements) {
                elements.each(function(ind) {
                    var label_yes = document.createElement('label');
                    var label_no = document.createElement('label');
                    var input_yes = document.createElement('input');
                    var input_no = document.createElement('input');
                    var a_slider = document.createElement('a');
                    var element_id_yes = elements[ind].querySelector('input[type="radio"]').getAttribute('name').replace(/\W+/g, '_') + '_on';
                    var element_id_no = elements[ind].querySelector('input[type="radio"]').getAttribute('name').replace(/\W+/g, '_') + '_off';
                    var yes_selected = elements[ind].querySelector('input[type="radio"][value="1"]').checked;
                    var no_selected = elements[ind].querySelector('input[type="radio"][value="0"]').checked;

                    elements[ind].querySelector('input[type="radio"][value="1"]').id = element_id_yes;
                    elements[ind].querySelector('input[type="radio"][value="0"]').id = element_id_no;
                    label_yes.setAttribute('for', element_id_yes);
                    label_yes.innerText = 'Oui';
                    label_no.setAttribute('for', element_id_no);
                    label_no.innerText = 'Non';
                    input_yes.id = element_id_yes;
                    input_yes.type = 'radio';
                    input_yes.setAttribute('name', elements[ind].querySelector('input[type="radio"]').getAttribute('name'));
                    input_yes.value = 1;
                    input_yes.checked = (yes_selected || (!yes_selected && !no_selected)) ? true : false;
                    input_no.id = element_id_no;
                    input_no.type = 'radio';
                    input_no.setAttribute('name', elements[ind].querySelector('input[type="radio"]').getAttribute('name'));
                    input_no.value = 0;
                    input_no.checked = no_selected;
                    a_slider.className = 'slide-button btn';

                    elements[ind].innerHTML = '';
                    elements[ind].className += ' switch prestashop-switch fixed-width-lg';
                    elements[ind].appendChild(input_yes);
                    elements[ind].appendChild(label_yes);
                    elements[ind].appendChild(input_no);
                    elements[ind].appendChild(label_no);
                    elements[ind].appendChild(a_slider);
                });
            }

        };

        return p;

    }());