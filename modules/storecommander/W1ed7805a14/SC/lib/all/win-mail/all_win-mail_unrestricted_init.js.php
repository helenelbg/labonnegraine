<?php echo '<script type="text/javascript">'; ?>
	<?php require_once __DIR__.'/all_win-mail_init_translations.js.php'; ?>
	var employeeMail = "<?php echo $sc_agent->email; ?>";
	const fieldWidth = 680;
	const formId = new Date().getTime();
	const maximumNumberOfRecipients = 10;

	let unrestrictedFormConfig = [
		{
			type: "settings",
			position: 'label-top',
		},
		{
			type: "block", list: [
				{
					type: "block", width: 700, list: [

						{
							type: "input",
							width: fieldWidth,
							name: "from",
							value: employeeMail,
							readonly: true,
							label: senderLabel,
							offsetTop: 10
						},
						{
							type: "input",
							width: fieldWidth,
							required: true,
							name: "recipients",
							label: recipientsLabel,
							position: 'label-top',
							rows: 10,
							offsetTop: 10
						},
						{
							type: "block", width: fieldWidth / 2, className: 'bcc', list: [
								{
									type: "radio",
									width: fieldWidth / 2,
									checked: true,
									name: "cc_type",
									label: blindCarbonCopyTxt,
									position: 'label-right',
									value: 'BCC'
								},
								{
									type: "template",
									className: 'help',
									name: "bccTxt",
									label: "",
									format: function () {
										return bccTxt;
									}
								},
							]
						},
						{
							type: "block", width: fieldWidth / 2, className: 'bcc', list: [
								{
									type: "radio",
									width: fieldWidth / 2,
									name: "cc_type",
									label: carbonCopyTxt,
									position: 'label-right',
									value: 'CC'
								},
								{
									type: "template",
									className: 'help',
									name: "ccTxt",
									label: "",
									format: function () {
										return ccTxt;
									}
								}
							]
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
							name: "files",
							label: attachmentsLabel,
							offsetTop: 10
						},
						{
							type: "button",
							width: fieldWidth,
							name: "submit_mails",
							value: submitLabel
						},
					]
				}
			]
		}
	];
	mailForm = sendMailTabBar.tabs('unrestricted').attachForm(unrestrictedFormConfig);
	// destinataires
	var recipients = mailForm.getInput('recipients');
	// pieces jointes
	var attachments = mailForm.getUploader('files');
	attachments.setURL("index.php?ajax=1&act=all_upload&obj=mail_attachment&formId=" + formId);
	attachments.setTitleText(attachmentsMessage);

	/*************************************/
	// EVENTS
	/*************************************/

	wSendMail.attachEvent('onClose', function () {
		removeAttachments();
		$('.recipient').empty();
		return true;
	})

	mailForm.attachEvent("onChange", function (name, value, result) {
		if (name == 'recipients') {
			cleanUpRecipients();
		}
	});

	mailForm.attachEvent("onButtonClick", function (id) {
		if (id == 'submit_mails') {
			cleanUpRecipients()
				.then(removeInvalidMails)
				.then(removeDuplicates)
				.then(checkRecipientsNumber)
				.then(uploadAttachments)
				.then(sendMails)
				.then(displayMailSuccessMessage)
				.catch(error => dhtmlx.message({
					text: error,
					type: "error"
				}))
			;
		}
	});

	mailForm.attachEvent("onUploadFail", function (realName, response) {
		dhtmlx.message({
			text: realName + ': ' + response.message,
			type: "error"
		});
	});


	mailForm.attachEvent("onValidateError", function (name, value, result) {
		var label = mailForm.getItemLabel(name).toLowerCase();
		var message = "<?php echo _l('Field %s is required'); ?>";
		dhtmlx.message({
			text: message.replace('%s', label),
			type: "error"
		});
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

	async function checkRecipientsNumber(){
		return new Promise((resolve, reject) => {
				if(recipients.value.split("\n").length >= maximumNumberOfRecipients) {
					mailForm.setValidateCss('recipients', false, 'validate_error');
					reject(maxEmailAddressesNumber.replace('%s', maximumNumberOfRecipients));
				}
				resolve();
		})
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
				datas.type = "unrestricted";
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

	/**
	 * remove attachemnts
	 */
	async function removeAttachments() {
		return new Promise((resolve, reject) => {
			fetch("index.php?ajax=1&act=all_upload_remove&obj=mail_attachment&formId=" + formId)
				.then(function (response) {
					resolve('attachments removed');
				});
		});
	}

	/**
	 * display message
	 */
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

	/**
	 * suppression des séparateurs et des retours de lignes non voulus dans le textarea
	 **/
	async function cleanUpRecipients() {
		return new Promise((resolve, reject) => {
			let recipientsEmails = getRecipients();
			recipients.value = recipientsEmails.join("\n").trim();
			resolve('recipients cleaned');
		});
	}

	/**
	 * suppression des adresses email invalides
	 **/

	async function removeInvalidMails() {
		return new Promise((resolve, reject) => {
			let invalidEmails = getInvalidMails();
			if (invalidEmails.length > 0) {
				dhtmlx.confirm({
					text: invalidEmails.join(', ') + removeInvalidMailsTxt,
					ok: yesLabel,
					cancel: noLabel,
					callback: function (result) {
						if (result == true) {
							recipients.value = getRecipients().filter(x => !invalidEmails.includes(x)).join("\n");
							mailForm.setItemFocus('recipients');
						}
						resolve('invalid emails processed');
					}
				});
			} else {
				resolve('invalid emails processed');
			}
		});
	}


	/**
	 * suppression des adresses email en doublon
	 **/
	async function removeDuplicates() {
		return new Promise((resolve, reject) => {
			var emails = getRecipients();
			var uniqueEmails = emails.filter(function (el, index, arr) {
				return index == arr.indexOf(el);
			});
			if (uniqueEmails.length != emails.length) {
				dhtmlx.confirm({
					text: removeDuplicateMailsTxt,
					ok: yesLabel,
					cancel: noLabel,
					callback: function (result) {
						if (result == true) {
							recipients.value = uniqueEmails.join("\n");
							mailForm.setItemFocus('recipients');
						}
						resolve('Duplicates processed');

					}
				});
			} else {
				resolve('Duplicates processed');
			}
		});
	}


	/*************************************/
	// UTILS
	/*************************************/
	/**
	 * recuperation de la liste des emails
	 * @return renvoie un tableau des adresses
	 **/
	function getRecipients() {
		const parseRecipientsExp = /[\r?\n|\,|\;]*(.[^\r?\n|\,|\;]*)[\r?\n|\,|\;]+/g;
		return recipients.value.split(parseRecipientsExp).filter((a) => a);
	}

	/**
	 * verification des adresses emails potentiellement mal formées
	 * @return renvoie un tableau des adresses mal formées
	 **/
	function getInvalidMails() {
		let recipientsEmails = getRecipients();
		var invalidEmails = [];
		for (var recipient of recipientsEmails) {
			const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/g;
			if (recipient.match(regex) == null) {
				invalidEmails.push(recipient);
			}
		}
		return invalidEmails;
	}
<?php echo '</script>'; ?>

