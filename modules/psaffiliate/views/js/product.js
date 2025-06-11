/*
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code.
 *
 * @author    Active Design <office@activedesign.ro>
 * @copyright 2016-2018 Active Design
 * @license   LICENSE.txt
 */
$(function () {
    $(document).ready(function () {
        var clipboard = new Clipboard('.btn-copy');

        $('.btn-copy').popover({
            trigger: 'manual',
            placement: 'top'
        });
        function setTooltip(btn, message) {
            $(btn).attr('data-content', message)
                .popover('show');
        }

        function hideTooltip(btn) {
            setTimeout(function () {
                $(btn).popover('hide');
            }, 1000);
        }

        clipboard.on('success', function (e) {
            setTooltip(e.trigger, 'Copied!');
            hideTooltip(e.trigger);
        });

        // BS Tooltips
        $('[data-toggle="tooltip"]').tooltip({container: "body"});
    });
});