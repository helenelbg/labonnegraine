<?php
require_once __DIR__.'/all_win-mail_form.html.php';
?>
<?php echo '<script type="text/javascript">'; ?>
	<?php require_once __DIR__.'/all_win-mail_init_translations.js.php'; ?>
	var selectedIds = '<?php echo Tools::getValue('selectedIds', null); ?>';


	// LAYOUI
	var wSendMailLayout = wSendMail.attachLayout("1C");
	wSendMailLayout.cells('a').hideHeader();


	// TABS
	const tabWidth = 384;
	var sendMailTabBar = wSendMailLayout.cells('a').attachTabbar({
		tabs: [
			{
				id: "customers",
				text: customersTabTxt,
				width: tabWidth,
				index: 1,
				active: true,
				enabled: true,
				close: false
			},
			{
				id: "unrestricted",
				text: unrestrcitedTabTxt,
				width: tabWidth,
				index: 2,
				active: false,
				enabled: true,
				close: false
			},

		]             // tabs and other config
	});

	var params = {
		id_shop: shopselection,
		id_lang: SC_ID_LANG,
		selectedIds: selectedIds
	};

	var queryString = new URLSearchParams(params);
	sendMailTabBar.tabs('customers').attachURL('index.php?ajax=1&act=all_win-mail_customers_init&' + queryString
		.toString(), true);
	sendMailTabBar.tabs("unrestricted").unloadView("unrestricted");
	sendMailTabBar.attachEvent('onTabClick', function (name) {
		var state = sendMailTabBar.tabs("customers").showView(name);
		if (state == true) {
			if (name == "unrestricted") {
				sendMailTabBar.tabs("customers").unloadView("customers");
				params = {
					id_shop: shopselection,
					id_lang: SC_ID_LANG
				};
				sendMailTabBar.tabs('unrestricted').attachURL('index.php?ajax=1&act=all_win-mail_unrestricted_init&' + queryString
					.toString(), true);
			} else {
				sendMailTabBar.tabs("unrestricted").unloadView("unrestricted");
				params.selectedIds = selectedIds;
				sendMailTabBar.tabs('customers').attachURL('index.php?ajax=1&act=all_win-mail_customers_init&' + queryString
					.toString(), true);
			}
		}
	})
<?php echo '</script>'; ?>
