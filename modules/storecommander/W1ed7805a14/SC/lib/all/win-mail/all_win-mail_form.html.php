<style type="text/css">@import url(<?php echo SC_PLUPLOAD; ?> js/vault/vault.min.css);</style>
<script type="text/javascript" src="<?php echo SC_JQUERY; ?>"></script>
<script type="text/javascript" src="lib/js/jquery.cokie.js"></script>
<script type="text/javascript" src="<?php echo SC_JSFUNCTIONS; ?>"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD; ?>js/vault/vault.min.js"></script>
<style type="text/css">
	input,
	textarea,
	.dhxcombo_material {
		background: #f5f5f5 !important;
	}

	input[readonly] {
		background: #FFF !important;
		color: #999 !important;
	}

	.recipient_list {
		height: 187px !important;
		list-style: none outside none;
		overflow-y: scroll;
	}

	.recipient_list,
	.recipient_list li {
		margin: 0 !important;
		padding: 5px !important;
		width: 100% !important;
		position:relative;
	}

	.customer-autocomplete .dhxform_label {
		background: #f5f5f5 !important;
		padding: 5px 1px !important;
		border-bottom: 1px solid #dfdfdf !important;
		position: relative !important;
		margin-top: 0 !important;
		color:#999 !important;
	}
	.customer-autocomplete .dhxform_control {
		margin-left:0 !important;
	}
	
	.recipient_list {
		border: 1px dashed #999;
		overflow-y: auto;
	}

	.recipient_list li:not(.empty) {
		width: 49% !important;
		margin: 0 0 5px 0 !important;
		background: #f5f5f5;
	}

	.recipient_list li.empty {
		color: #999;
		padding: 0 !important;
	}

	.recipient_list li > span {
		margin-right: 3px;
		color: #39c;
	}

	.recipient_list li .email {
		color: #999;
		font-size: 12px;
		display: block;
		margin-left: 18px;
	}

	.dhxform_btn_filler {
		cursor: pointer !important;
	}

	.dhxform_obj_material {
		margin-bottom: 10px !important;
	}

	.help {
		font-size: 11px;
		color: #999;
		margin-left: 27px;
	}

	.bcc {
		float: left;
		width: 50%;
	}
	
	.remove {
		position:absolute;
		right:5px;
		top:5px;
		cursor:pointer;
	}

</style>
<form id="mailForm" method="post" action="index.php?ajax=1&act=all_win-mail_send" enctype="multipart/form-data">
	<div id="dhxForm">
	</div>
</form>
