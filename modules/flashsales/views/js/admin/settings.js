/*
* 2022 Keyrnel
*
* NOTICE OF LICENSE
*
* The source code of this module is under a commercial license.
* Each license is unique and can be installed and used on only one shop.
* Any reproduction or representation total or partial of the module, one or more of its components,
* by any means whatsoever, without express permission from us is prohibited.
* If you have not received this module from us, thank you for contacting us.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future.
*
* @author Keyrnel
* @copyright  2022 - Keyrnel
* @license commercial
* International Registered Trademark & Property of Keyrnel
*/

$(document).ready(function() {

	Css = {
		field: $('.css_form'),
		fieldAction: $('.css_file'),

		init: function() {
			$(Css.field).find('input').on('change', function() {
				var className = $(this).attr('data-class');
				var property = $(this).attr('data-property');
				var val = $(this).val();

				if ($(this).is('input[type="radio"]:checked')) {
					val = val == 1 ? (property == "border-style" ? 'solid' : 'inline-block') : 'none';
				}

				if (property == "width") {
                    val = val+'%';
                } else if (property == "font-size"
                    || property == "border-width"
                    || property == "padding-top"
                    || property == "padding-bottom"
                    || property == "padding-left"
                    || property == "padding-right"
                    || property == "margin-top"
                    || property == "margin-bottom"
                ) {
                    val = val+'px';
                }

				Css.replaceStr(className, property, val);
			});
		},

		replaceStr: function (className, property, val) {
			var pos = Css.fieldAction.val().indexOf(className, 0);
			pos = Css.fieldAction.val().indexOf(property, pos);
			pos = Css.fieldAction.val().indexOf(':', pos);

			var start = pos + 1;
			var end = Css.fieldAction.val().indexOf(';', start);
			var len = Css.fieldAction.val().length;
			var html = Css.fieldAction.val().substring(0, start) + ' '+val + Css.fieldAction.val().substring(end, len);

			Css.fieldAction.val(html);
		},
	};

	initAsset();
    Css.init();
});

function initAsset()
{
	hideOtherLanguage(default_language);
	$(".textarea-autosize").autosize();
    $(document).find('input[type="color"]').mColorPicker();
}
