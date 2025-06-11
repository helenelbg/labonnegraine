<?php

include("../config/config.inc.php");

$fiche_pratique_nom = pSQL($_POST['fiche_pratique_nom']);
$fiche_pratique_description = pSQL($_POST['fiche_pratique_description']);
$fiche_pratique_pdf = $_FILES['fiche_pratique_pdf'];
$fiche_pratique_picto = $_FILES['fiche_pratique_picto'];
$type = pSQL($fiche_pratique_pdf['type']);
$size = pSQL($fiche_pratique_pdf['size']);
$file_name = pSQL($fiche_pratique_pdf['name']);

// Ajustement pour lier un picto à un attachment.
// Par Dorian, BERRY-WEB, mars 2023.
$uniqidPicto = '';
if (isset($_FILES['fiche_pratique_picto']))
{
	$array = explode('.', $_FILES['fiche_pratique_picto']['name']);
	$extension = end($array);
	do $uniqidPicto = sha1(microtime()).'.'.$extension;
	while (file_exists(_PS_UPLOAD_DIR_.$uniqidPicto));
	if (!copy($_FILES['fiche_pratique_picto']['tmp_name'], _PS_UPLOAD_DIR_.$uniqidPicto))
		$_FILES['fiche_pratique_picto']['error'][] = $this->l('File copy failed');
	@unlink($_FILES['fiche_pratique_picto']['tmp_name']);
}

$uniqidPdf = '';
if (isset($_FILES['fiche_pratique_pdf']))
{
	do $uniqidPdf = sha1(microtime());
	while (file_exists(_PS_DOWNLOAD_DIR_.$uniqidPdf));
	if (!copy($_FILES['fiche_pratique_pdf']['tmp_name'], _PS_DOWNLOAD_DIR_.$uniqidPdf))
		$_FILES['fiche_pratique_pdf']['error'][] = $this->l('File copy failed');
	@unlink($_FILES['fiche_pratique_pdf']['tmp_name']);
}

$sql = 'INSERT INTO `'._DB_PREFIX_.'attachment` (file, file_name, file_size, mime, picto) VALUES("'.$uniqidPdf.'","'.$file_name.'","'.$size.'","'.$type.'","'.$uniqidPicto.'");';
$res = Db::getInstance()->execute($sql);
$last_id = (int)Db::getInstance()->Insert_ID();

$sql = 'INSERT INTO `'._DB_PREFIX_.'attachment_lang` (id_attachment, id_lang, name, description) VALUES("'.$last_id.'","1","'.$fiche_pratique_nom.'","'.$fiche_pratique_description.'");';
$res = Db::getInstance()->execute($sql);

echo 'ok';
