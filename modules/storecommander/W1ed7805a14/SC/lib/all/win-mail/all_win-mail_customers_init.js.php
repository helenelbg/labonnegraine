<?php echo '<script type="text/javascript">'; ?>
	// param
	<?php require_once __DIR__.'/all_win-mail_init_translations.js.php'; ?>
	var employeeMail = "<?php echo $sc_agent->email; ?>";
	var selectedIds = "<?php echo Tools::getValue('selectedIds'); ?>";
	const fieldWidth = 680;
	const formId = new Date().getTime();

	const customersFormConfig = [
		{
			type: "settings",
			position: 'label-top',
		},
		{
			type: "block", list: [
				{
					type: "block", width: fieldWidth, list: [
						{
							type: "input",
							width: fieldWidth,
							name: "from",
							value: employeeMail,
							readonly: true,
							required: false,
							label: senderLabel,
							offsetTop: 10
						},
						{
							type: "combo",
							width: fieldWidth-16,
							name: 'search_customer',
							label: '<span class="fa fa-search"></span>',
							position: 'label-left',
							className: "customer-autocomplete"
						},
						{
							type: "template",
							width: fieldWidth - 10,
							name: "recipients",
							value: customersRecipientsPlaceholder,
							label: recipientsLabel+' (0)',
							position: 'label-top',
							className: 'recipients',
							offsetTop: 10,
							format: defaultCustomerList
						},
						{
							type: "hidden",
							name: "customers_ids",
						},
						{
							type: "input",
							required: true,
							width: fieldWidth,
							name: "subject",
							label: objectLabel,
							offsetTop: 10
						},
						{
							type: "input",
							required: true,
							width: fieldWidth,
							name: "message",
							rows: 8,
							label: messageLabel,
							offsetTop: 10
						},
						{
							type: "upload",
							width: fieldWidth,
							required: false,
							name: "files",
							label: attachmentsLabel,
							offsetTop: 10
						},
						{
							type: "button",
							width: fieldWidth,
							name: "submit_mails",
							value: submitLabel
						}
					]
				}
			]
		}
	];

	mailForm = sendMailTabBar.tabs('customers').attachForm(customersFormConfig);
	// destinataires
	var recipients = mailForm.getInput('recipients');
	// pieces jointes
	var attachments = mailForm.getUploader('files');
	attachments.setURL("index.php?ajax=1&act=all_upload&obj=mail_attachment&formId=" + formId);
	attachments.setTitleText(attachmentsMessage);
	// combo autocomplete
	var combo = mailForm.getCombo('search_customer');
	combo.enableFilteringMode(true, "index.php?ajax=1&act=all_win-mail_get", false);
	combo.setPlaceholder(searchCustomerPlaceHolder);
	// liste des destinataires (champ caché)
	var customerIds = mailForm.getInput('customers_ids');

	if (selectedIds != null) {
		customerIds.value = selectedIds;
	}

	// liste "visuelle" des destinataires
	getCustomerInfos();

	/*************************************/
	// EVENTS
	/*************************************/

	mailForm.attachEvent("onUploadFail", function (realName, response) {
		dhtmlx.message({
			text: realName + ': ' + response.message,
			type: "error"
		});
	});

	mailForm.attachEvent("onValidateError", function (name, value, result) {
		var label = mailForm.getItemLabel(name).toLowerCase();
		dhtmlx.message({
			text: requiredMessage.replace('%s', label),
			type: "error"
		});


	});

	wSendMail.attachEvent('onClose', function () {
		removeAttachments();
		$('.recipient_list').empty();
		return true;
	})

	combo.attachEvent("onChange", function (value, text) {
		if (value != null) {
			let ids = customerIds.value.split(',');
			ids = ids.filter(Boolean);
			if (ids.includes(value) === false) {
				ids.push(value);
			}
			customerIds.value = ids.join(',');
		}
		getCustomerInfos();
		combo.clearAll(); // suppression liste autocomplete
		combo.getInput().value = ''; // on vide l'input combo
		mailForm.setItemFocus('search_customer'); // declenche event change sur textarea
		mailForm.resetValidateCss('recipients');
	});

	// suppression d'un destinataire
	handleCustomerRemove();

	// soumissions formulaire
	mailForm.attachEvent("onButtonClick", function (id) {
		if (id === 'submit_mails') {
			uploadAttachments()
				.then(sendMails)
				.then(displayMailSuccessMessage)
				.catch(error => dhtmlx.message({
					text: error,
					type: "error"
				}))
			;
		}
	});


	/*************************************/
	// ASYNC
	/*************************************/

	/**
	 * upload des fichiers
	 **/
	async function uploadAttachments() {
		return new Promise((resolve, reject) => {
			if (attachments.p_files.childNodes.length == 0) {
				resolve('No Attachments, continue');
			}
            if(mailForm.getUploaderStatus('files')){
                resolve('Attachments already uploaded');
            }
			attachments.upload();
			mailForm.attachEvent('onUploadComplete', function () {
				resolve('Attachments successfully uploaded');
			})
		});
	}

	/**
	 * sending form via ajax
	 * */
	async function sendMails() {
		return new Promise((resolve, reject) => {
			if (mailForm.validate() == false) {
				reject(invalidFormMessage);
			} else {
				var url = document.getElementById('mailForm').getAttribute('action');
				var xhr = new XMLHttpRequest();
				xhr.open("post", url, true);
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				let datas = mailForm.getFormData();
				datas.type = "customers";
				datas.formId = formId;
				let params = new URLSearchParams(datas);
				xhr.onreadystatechange = function () {
					if (xhr.readyState === 4) {
						var response = JSON.parse(xhr.responseText);
						if (xhr.status === 200) {
							resolve('mails sent !');
						} else {
							reject(errorSendingMailMessage);
						}
					}
				}
				xhr.send(params.toString());
			}
		});
	}

	async function removeAttachments() {
		return new Promise((resolve, reject) => {
			fetch("index.php?ajax=1&act=all_upload_remove&obj=mail_attachment&formId=" + formId)
				.then(function (response) {
					resolve('attachments removed');
				});
		});
	}


	async function displayMailSuccessMessage() {
		return new Promise((resolve, reject) => {
			wSendMail.close();
			dhtmlx.message({
				type: "alert-success",
				text: successSendingMailMessage,
			});
			resolve('messages sent');
		});
	}


	/*************************************/
	// TOOLS
	/*************************************/

	/**
	 *
	 * @param ids
	 * @returns {boolean}
	 */
	function getCustomerInfos() {
		if ( customerIds.value === '' || (selecteIds = null && combo.getInput().value == '')) {
			return true;
		} else {
			let params = new URLSearchParams({selectedIds: String( customerIds.value)});
			let list = $('.recipient_list');
			list.html(customersRecipientsPlaceholder);
			$.get("index.php?ajax=1&act=all_win-mail_get&" + params.toString(), function (data) {
				if (data == null) {
					dhtmlx.message({
						text: unableTOFindCustomerSelectionTxt,
						type: "error"
					});
					return false;
				} else {
					if (data.length > 0) {
						list.empty();
						data.forEach(function (customer) {
							list.append('<li data-customer_id="' + customer.id_customer + '"><span class="' + userIcon + '"></span>&nbsp;'
								+ customer.firstname + ' ' + customer.lastname + '<span class="email">' + customer
									.email + '</span><span class="remove fa fa-times-circle"></span></li>');
						});
						handleCustomerRemove();
						displayCountRecipients();
					}
					return true;
				}
			});
		}
	}

	function defaultCustomerList(name, value) {
		return '<ul class="recipient_list"><li class="empty">' + value + '</li></ul>';
	}

	function handleCustomerRemove(){
		$('.recipient_list .remove').on('click', function(e) {
			let idToRemove = e.target.parentNode.getAttribute('data-customer_id');
			let ids = customerIds.value.split(',');
			ids = ids.filter(Boolean);
			e.target.parentNode.remove();
			let index = ids.indexOf(idToRemove);
			if (index > -1) {
				ids.splice(index, 1);
			}
			customerIds.value = ids.join(',');
			displayCountRecipients();
		});
	}

	function displayCountRecipients(){
		let ids = customerIds.value.split(',');
		// suppression du null initial
		ids = ids.filter(Boolean);
		let index = ids.indexOf('null');
		if (index > -1) {
			ids.splice(index, 1);
		}
		$('.recipients label').text($('.recipients label').text().replace(/\d/i, ids.length));
	}

<?php echo '</script>'; ?>
